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
use Netzmacht\Contao\ContentNode\Model\ContentNodeModel;
use Netzmacht\Contao\ContentNode\Node\Registry;
use Netzmacht\Contao\Toolkit\Dca;
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
     * Construct.
     */
    public function __construct()
    {
        $this->registry = $this->getService('content-nodes.registry');
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
                $container     = new ContentNodeModel();
                $container->id = $dataContainer->id;
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

        if (!\Input::get('popup')) {
            $onClick = sprintf(
                'Backend.openModalIframe({\'width\':768,\'title\':\'%s\',\'url\':this.href});return false',
                specialchars(str_replace("'", "\\'", sprintf('TL_CONTENT')))
            );

            $attributes .= sprintf(' onclick="%s"', $onClick);
        }

        return sprintf(
            '<a href="%s" title="%s"%s>%s</a> ',
            \Backend::addToUrl($href . '&amp;id=' . $row['id']),
            $title,
            $attributes,
            \Image::getHtml($icon, $label)
        );
    }
}
