<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\ContentNode\View;

use Netzmacht\Contao\Toolkit\Assets;
use Netzmacht\Contao\Toolkit\View\BackendTemplate;

/**
 * Class Breadcrumb renders the backend breadcrumb view.
 *
 * @package Netzmacht\Contao\ContentNode\View
 */
class Breadcrumb
{
    /**
     * The template name.
     *
     * @var string
     */
    private $template = 'be_node_breadcrumb';

    /**
     * The nodes.
     *
     * @var array
     */
    private $nodes = array();

    /**
     * Add a node.
     *
     * @param int         $nodeId The node id.
     * @param string      $label  The label.
     * @param string|null $class  The class.
     * @param bool        $nodes  The nodes.
     *
     * @return $this
     */
    public function addNode($nodeId, $label, $class = null, $nodes = true)
    {
        $this->nodes[] = array(
            'id'   => $nodeId,
            'label' => $label,
            'class' => $class,
            'link'  => \Backend::addToUrl('id=' . $nodeId . '&amp;nodes=' . (int) $nodes)
        );

        return $this;
    }

    /**
     * Generate the breadcrumb.
     *
     * @return string
     */
    public function generate()
    {
        $template = new BackendTemplate($this->template);
        $template->set('nodes', $this->nodes);

        return $template->parse();
    }
}
