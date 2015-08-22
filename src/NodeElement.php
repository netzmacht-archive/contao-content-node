<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\ContentNode;

use ContentElement;

class NodeElement extends \ContentElement
{
    protected $strTemplate = 'ce_node';

    public function generate()
    {
        if (TL_MODE === 'BE') {
            return $this->generateBackendView();
        }

        return parent::generate();
    }

    protected function compile()
    {
        $content  = '';
        $elements = \ContentModel::findBy(
            array('pid=?', 'ptable=?'),
            array($this->id, 'tl_content_node'),
            array('order' => 'sorting')
        );

        if ($elements) {
            foreach ($elements as $element) {
                $content .= $this->getContentElement($element, $this->column);
            }
        }

        $this->Template->content = $content;
    }

    private function generateBackendView()
    {
        $content  = '';
        $callback = $GLOBALS['TL_DCA']['tl_content']['list']['sorting']['child_record_callback'];

        if ($callback) {
            $renderer = new $callback[0];
            $elements = \ContentModel::findBy(
                array('pid=?', 'ptable=?'),
                array($this->id, 'tl_content_node')
            );

            if ($elements) {
                foreach ($elements as $element) {
                    $rendered = $renderer->{$callback[1]}($element->row());

                    if ($rendered) {
                        $content .= sprintf(
                            '<div style="background: #fff;border-bottom: 4px solid #f3f3f3; padding: 6px; border-left: 6px solid #f3f3f3; border-right: 6px solid #f3f3f3;">%s</div>',
                            $rendered
                        );
                    }
                }
            }

            return $content;
        }
    }
}
