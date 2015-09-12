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

use DataContainer;
use Netzmacht\Contao\ContentNode\Exception\AccessDeniedException;
use Netzmacht\Contao\ContentNode\Model\ContentNodeModel;
use Netzmacht\Contao\ContentNode\Node\Registry;
use Netzmacht\Contao\ContentNode\View\BackendRenderer;
use Netzmacht\Contao\Toolkit\Dca;
use Netzmacht\Contao\Toolkit\Dca\Definition;
use Netzmacht\Contao\Toolkit\ServiceContainerTrait;

/**
 * Content backend view helper.
 *
 * @package Netzmacht\Contao\ContentNode\Dca
 */
class Helper
{
    use ServiceContainerTrait;

    /**
     * The node type registry.
     *
     * @var Registry
     */
    private $registry;

    /**
     * The dca definition for tl_content.
     *
     * @var Definition
     */
    private $definition;

    /**
     * Construct.
     */
    public function __construct()
    {
        $this->registry   = $this->getService('content-nodes.registry');
        $this->definition = $this->getServiceContainer()->getDcaManager()->get('tl_content');
    }

    /**
     * Create a callback definition for the given name.
     *
     * @param string $name The callback method name.
     *
     * @return array
     */
    public static function callback($name)
    {
        return array (get_called_class(), $name);
    }

    /**
     * Initialize the backend view.
     *
     * @param DataContainer $dataContainer The data container.
     *
     * @return void
     */
    public function initialize($dataContainer)
    {
        if (TL_MODE !== 'BE') {
            return;
        }

        $this->getServiceContainer()
            ->getAssetsManager()
            ->addStylesheet('system/modules/content-node/assets/css/backend.css');

        $callback = $this->definition->get('list/sorting/child_record_callback');
        if (is_array($callback)) {
            $callback[0] = \System::importStatic($callback[0]);
        }

        $renderer = new BackendRenderer($this->registry, $callback);
        Dca::set('tl_content', 'list/sorting/child_record_callback', $renderer);

        $parentType = null;
        if ($dataContainer->parentTable === 'tl_content_node') {
            $parent = \ContentModel::findByPk(CURRENT_ID);

            if ($parent && $this->registry->hasNodeType($parent->tye)) {
                $parentType = $this->registry->getNode($parent->type);
            }
        }

        try {
            $restriction = new ContentElementAccess(
                $this->definition,
                $this->registry,
                $this->getService('database.connection'),
                $this->getService('session'),
                $this->getService('input')
            );

            $restriction->restrict($dataContainer->id, $parentType);

        } catch (AccessDeniedException $e) {
            \Controller::log($e->getMessage(), 'ContentElementAccess::resitrct', TL_ACCESS);
            \Controller::redirect(\Environment::get('script') . '?act=error');
        }
    }

    /**
     * Create the node container.
     *
     * @param mixed         $value         The type value.
     * @param DataContainer $dataContainer The data container.
     *
     * @return mixed
     */
    public function createNodeContainer($value, $dataContainer)
    {
        if ($this->registry->hasNodeType($value)) {
            $container = ContentNodeModel::findOneBy('id', $dataContainer->id);

            if (!$container) {
                $container      = new ContentNodeModel();
                $container->id  = $dataContainer->id;
                $container->pid = $dataContainer->pid;

                $container->save();
            }
        }

        return $value;
    }

    /**
     * Generate the node type button.
     *
     * @param array  $row        The data row.
     * @param string $href       The button href.
     * @param string $title      The button title.
     * @param string $label      The button label.
     * @param string $icon       The icon.
     * @param string $attributes Html attributes.
     *
     * @return string
     */
    public function generateButton($row, $href, $title, $label, $icon, $attributes)
    {
        if (!$this->registry->hasNodeType($row['type'])) {
            return '';
        }

        return sprintf(
            '<a href="%s" title="%s"%s>%s</a> ',
            \Backend::addToUrl($href . '&amp;id=' . $row['id'], true, array('act', 'mode')),
            $title,
            $attributes,
            \Image::getHtml($icon, $label)
        );
    }

    /**
     * Generate the header fields.
     *
     * @param array $fields The header fields.
     *
     * @return array
     */
    public function generateHeaderFields($fields)
    {
        $model = \ContentModel::findByPk(CURRENT_ID);

        if ($model instanceof \ContentModel && $this->registry->hasNodeType($model->type)) {
            return $this->registry->getNode($model->type)->generateHeaderFields($fields, $model);
        }

        return $fields;
    }
}
