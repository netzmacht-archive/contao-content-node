<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\ContentNode\Dca;

use Contao\Database;
use Contao\Input;
use Contao\Session;
use Netzmacht\Contao\ContentNode\Exception\AccessDeniedException;
use Netzmacht\Contao\ContentNode\Node\Node;
use Netzmacht\Contao\ContentNode\Node\Registry;
use Netzmacht\Contao\ContentNode\Util\ArrayUtil;
use Netzmacht\Contao\Toolkit\Dca;
use Netzmacht\Contao\Toolkit\Dca\Definition;

/**
 * Class ContentElementAccess restricts the access to the available types.
 *
 * This class is taken from the gorgeous ce-access extension and refactored.
 *
 * @see    https://github.com/terminal42/contao-ce-access/blob/master/CeAccess.php
 * @author Andreas Schempp <andreas.schempp@terminal42.ch>
 *
 * @package Netzmacht\Contao\ContentNode\Dca
 */
class ContentElementAccess
{
    /**
     * The node registry.
     *
     * @var Registry
     */
    private $registry;

    /**
     * The database connection.
     *
     * @var Database
     */
    private $database;

    /**
     * The session.
     *
     * @var Session
     */
    private $session;

    /**
     * The input.
     *
     * @var Input
     */
    private $input;

    /**
     * The dca definition.
     *
     * @var Definition
     */
    private $definition;

    /**
     * ContentElementAccess constructor.
     *
     * @param Definition $definition The dca definition.
     * @param Registry   $registry   The node registry.
     * @param Database   $database   The database connection.
     * @param Session    $session    The session.
     * @param Input      $input      The input.
     */
    public function __construct(
        Definition $definition,
        Registry $registry,
        Database $database,
        Session $session,
        Input $input
    ) {
        $this->definition = $definition;
        $this->registry   = $registry;
        $this->database   = $database;
        $this->session    = $session;
        $this->input      = $input;
    }

    /**
     * Close the data container.
     *
     * @return void
     */
    private function closeDataContainer()
    {
        $config =& $this->definition->get('config');

        $config['closed']       = true;
        $config['notEditable']  = true;
        $config['notDeletable'] = true;

        $operations =& $this->definition->get('list/global_operations');
        unset($operations['all']);
    }

    /**
     * Set the default content type and it's palettes.
     *
     * @param array $allowedElements List of allowed types.
     *
     * @return void
     */
    private function setDefaults($allowedElements)
    {
        $default = key(reset($allowedElements));

        $this->definition->set('fiedls/type/default', $default);
        $this->definition->set('palettes/default', $this->definition->get('palettes/' . $default));
    }

    /**
     * Filter the allowed ids.
     *
     * @param array       $ids             Content elkement ids.
     * @param array       $allowedElements Flat list of allowed elements.
     * @param string|null $orderBy         Optional order statement.
     *
     * @return array
     */
    private function filterIds($ids, $allowedElements, $orderBy = null)
    {
        $query = sprintf(
            "SELECT id FROM tl_content WHERE id IN (%s) AND type IN ('%s')",
            implode(',', $ids),
            implode("','", $allowedElements)
        );

        if ($orderBy) {
            $query .= ' ORDER BY ' . $orderBy;
        }

        return $this->database->query($query)->fetchEach('id');
    }

    /**
     * Guard that the access is allowed.
     *
     * @param array $allowedElements The allowed elements.
     * @param int   $contentId       The content id.
     *
     * @return void
     * @throws AccessDeniedException When an invalid content element type is accessed.
     */
    private function guardAllowedAccess($allowedElements, $contentId)
    {
        $actions = array('show', 'create', 'paste', 'select', 'editAll');
        if (!in_array($this->input->get('act'), $actions) && $this->input->get('mode') == 'create') {
            $result = $this->database
                ->prepare('SELECT type FROM tl_content WHERE id=?')
                ->limit(1)
                ->execute($contentId);

            if ($result->numRows && !in_array($result->type, $allowedElements)) {
                $message = sprintf('Attempt to access restricted content element "%s"', $result->type);

                throw new AccessDeniedException($message);
            }
        }
    }

    /**
     * Restrict content element ids.
     *
     * @param array $allowedElements List of allowed elements.
     * @param int   $contentId       The id of the current content element.
     *
     * @return void
     * @throws AccessDeniedException When an invalid content element type is accessed.
     */
    private function restrictIds($allowedElements, $contentId)
    {
        $session         = $this->session->getData();
        $allowedElements = ArrayUtil::flatten($allowedElements);

        // Set allowed content element IDs (edit multiple)
        if (!empty($session['CURRENT']['IDS']) && is_array($session['CURRENT']['IDS'])) {
            $session['CURRENT']['IDS'] = $this->filterIds($session['CURRENT']['IDS'], $allowedElements);
        }

        // Set allowed clipboard IDs
        if (!empty($session['CLIPBOARD']['tl_content']['id'])) {
            $session['CLIPBOARD']['tl_content']['id'] = $this->filterIds(
                (array) $session['CLIPBOARD']['tl_content']['id'],
                $allowedElements,
                'sorting'
            );
        }

        // Overwrite session
        $this->session->setData($session);
        $this->guardAllowedAccess($allowedElements, $contentId);
    }

    /**
     * Restrict the content elements.
     *
     * @param int  $contentId The id of the current content element.
     * @param Node $node      The node type.
     *
     * @return void
     * @throws AccessDeniedException When an invalid content element type is accessed.
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function restrict($contentId, Node $node = null)
    {
        $nodeType        = $node ? $node->getName() : null;
        $allowedElements = $this->registry->filterContentElements($GLOBALS['TL_CTE'], $nodeType);

        if (empty($allowedElements)) {
            $this->closeDataContainer();
        } elseif (!in_array($this->definition->get('fields/type/default'), $allowedElements)) {
            $this->setDefaults($allowedElements);
        }

        if ($this->input->get('act') != '' && $this->input->get('act') !== 'select') {
            $GLOBALS['TL_CTE'] = $allowedElements;
            $this->restrictIds($allowedElements, $contentId);
        }
    }
}
