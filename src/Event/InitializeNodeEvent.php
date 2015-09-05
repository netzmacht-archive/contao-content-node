<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\ContentNode\Event;

use Netzmacht\Contao\ContentNode\Node\Node;
use Symfony\Component\EventDispatcher\Event;

/**
 * Initialize a already created node.
 *
 * @package Netzmacht\Contao\ContentNode\Event
 */
class InitializeNodeEvent extends Event
{
    const NAME = 'content-nodes.initialize-node';

    /**
     * The node.
     *
     * @var Node
     */
    private $node;

    /**
     * The node configuration.
     *
     * @var array
     */
    private $config;

    /**
     * InitializeNodeEvent constructor.
     *
     * @param Node  $node   The node.
     * @param array $config The node config.
     */
    public function __construct($node, array $config)
    {
        $this->node   = $node;
        $this->config = $config;
    }

    /**
     * Get node.
     *
     * @return Node
     */
    public function getNode()
    {
        return $this->node;
    }
}
