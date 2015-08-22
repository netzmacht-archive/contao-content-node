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


use Netzmacht\Contao\ContentNode\Model\ContentNodeModel;
use Netzmacht\Contao\Toolkit\Dca;

class Helper
{
    private $nodeTypes;

    public function __construct()
    {
        $this->nodeTypes = (array) $GLOBALS['TL_CONTENT_NODE'];
    }

    public function createNodeContainer($value, $dataContainer)
    {
        if (in_array($value, $this->nodeTypes)) {
            $container = ContentNodeModel::findOneBy('id', $dataContainer->id);

            if (!$container) {
                $container     = new ContentNodeModel();
                $container->id = $dataContainer->id;
                $container->save();
            }
        }

        return $value;
    }

    public function generateButton($row, $href, $title, $label, $icon, $attributes, $table)
    {
        if (!in_array($row['type'], $this->nodeTypes)) {
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
