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
use Netzmacht\Contao\ContentNode\Node\Node;
use Netzmacht\Contao\ContentNode\Node\Registry;
use Netzmacht\Contao\Toolkit\ServiceContainerTrait;

/**
 * Basic node content element. Can be used as base for own implementations.
 *
 * @package Netzmacht\Contao\ContentNode
 */
class NodeElement extends \ContentElement
{
    use ServiceContainerTrait;

    /**
     * The node template.
     *
     * @var string
     */
    protected $strTemplate = 'ce_node';

    /**
     * Backend view template. Override it if you want to use a custom backend view template.
     *
     * @var string|null
     */
    protected $backendViewTemplate = null;

    /**
     * The Node type.
     *
     * @var Node
     */
    private $node;

    /**
     * {@inheritDoc}
     */
    public function __construct($objElement, $strColumn = 'main')
    {
        parent::__construct($objElement, $strColumn);

        try {
            /** @var Registry $registry */
            $registry   = $this->getServiceContainer()->getService('content-nodes.registry');
            $this->node = $registry->getNode($this->type);
        } catch (\Exception $e) {
            $this->log(sprintf($e->getMessage(), $this->type), __METHOD__, TL_ERROR);
        }
    }

    /**
     * Get the node element.
     *
     * @return Node
     */
    protected function getNode()
    {
        return $this->node;
    }

    /**
     * Generate the element.
     *
     * @return string
     */
    public function generate()
    {
        // No node type configured. Break here.
        if (!$this->getNode()) {
            return '';
        }

        if (TL_MODE === 'BE') {
            return $this->getNode()->generateBackendView($this, $this->backendViewTemplate);
        }

        return parent::generate();
    }

    /**
     * Compile the content element.
     *
     * @return void
     */
    protected function compile()
    {
        $children = array();

        foreach ($this->getNode()->findChildren($this->id) as $element) {
            $children[$element->id] = $this->getContentElement($element, $this->strColumn);
        }

        $this->Template->children = $children;
    }
}
