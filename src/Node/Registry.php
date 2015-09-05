<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\ContentNode\Node;

use Netzmacht\Contao\ContentNode\Util\Filter;

/**
 * The registry provides all supported node instances.
 *
 * @package Netzmacht\Contao\ContentNode\Node
 */
class Registry
{
    /**
     * Already created node types.
     *
     * @var array
     */
    private $instances = array();

    /**
     * The node factory.
     *
     * @var Factory
     */
    private $factory;

    /**
     * Registry constructor.
     *
     * @param Factory $factory The factory.
     */
    public function __construct(Factory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * Get a node by a given type.
     *
     * @param string $type The node type.
     *
     * @return Node
     */
    public function getNode($type)
    {
        if (!isset($this->instances[$type])) {
            $this->instances[$type] = $this->factory->create($type);
        }

        return $this->instances[$type];
    }

    /**
     * Get the node types.
     *
     * @return array
     */
    public function getNodeTypes()
    {
        return $this->factory->getNodeTypes();
    }

    /**
     * Filter content elements.
     *
     * @param array  $elements   The content elements grouped in categories.
     * @param string $parentType The parent type as string.
     *
     * @return array
     */
    public function filterContentElements($elements, $parentType)
    {
        $filter = new Filter($elements);

        foreach ($this->getNodeTypes() as $type) {
            $filter = $this->getNode($type)->filterContentElements($filter, $parentType);
        }

        return $filter->getResult();
    }

    /**
     * Check if a node type is supported.
     *
     * @param string $type The type.
     *
     * @return bool
     */
    public function hasNodeType($type)
    {
        return $this->factory->supportsNodeType($type);
    }
}
