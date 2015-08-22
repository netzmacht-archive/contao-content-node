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

use Netzmacht\Contao\ContentNode\Event\CreateNodeEvent;
use Netzmacht\Contao\ContentNode\Event\InitializeNodeEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;


class Factory
{
    /**
     * The event dispatcher.
     *
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * The configuration.
     *
     * @var
     */
    private $configs;

    /**
     * Create a node.
     *
     * @param string $type The node type.
     *
     * @return Node
     */
    public function create($type)
    {
        if (!isset($this->configs[$type])) {
            throw new \InvalidArgumentException(sprintf('Unknown node type "%s"', $type));
        }

        $event = new CreateNodeEvent($type, $this->configs[$type]);
        $this->dispatcher->dispatch($event::NAME, $event);

        if ($event->getFactory()) {
            $node = call_user_func($event->getFactory(), $event->getType(), $event->getConfig());
        } elseif ($event->getClassName()) {
            $className = $event->getClassName();
            $node      = $className($type, $event->getConfigValue('children'));
        } else {
            throw new \RuntimeException(sprintf('Could not create node "%s"', $type));
        }

        $event = new InitializeNodeEvent($node, $event->getConfig());
        $this->dispatcher->dispatch($event::NAME, $event);

        return $node;
    }

    /**
     * Get all supported node types.
     *
     * @return array
     */
    public function getNodeTypes()
    {
        return array_keys($this->configs);
    }

    /**
     * Check if a node type is supported.
     *
     * @param string $type The node type.
     *
     * @return bool
     */
    public function supportsNodeType($type)
    {
        return array_key_exists($type, $this->configs);
    }
}
