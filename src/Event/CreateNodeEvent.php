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

use Symfony\Component\EventDispatcher\Event;


/**
 *
 */
class CreateNodeEvent extends Event
{
    const NAME = 'content-nodes.create-node';

    /**
     * A custom factory.
     *
     * @var callable
     */
    private $factory;

    /**
     * The node name.
     *
     * @var string
     */
    private $type;

    /**
     * The node config.
     *
     * @var string
     */
    private $config;

    /**
     * The class name.
     *
     * @var string
     */
    private $className;

    /**
     * CreateNodeEvent constructor.
     *
     * @param $type
     * @param $config
     */
    public function __construct($type, array $config)
    {
        $this->type   = $type;
        $this->config = $config;
    }

    /**
     * Get type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Get config.
     *
     * @return string
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Get a config value.
     *
     * @param string $key     The config key.
     * @param mixed  $default The default value.
     *
     * @return mixed
     */
    public function getConfigValue($key, $default = null)
    {
        if (array_key_exists($key, $this->config)) {
            return $this->config[$key];
        }

        return $default;
    }

    /**
     * Get factory.
     *
     * @return callable|null
     */
    public function getFactory()
    {
        return $this->factory;
    }

    /**
     * Set factory.
     *
     * @param callable $factory Factory.
     *
     * @return $this
     */
    public function setFactory($factory)
    {
        $this->factory = $factory;

        return $this;
    }

    /**
     * Get class name.
     *
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * Set class name.
     *
     * @param string $className THe Class name.
     *
     * @return $this
     */
    public function setClassName($className)
    {
        $this->className = $className;

        return $this;
    }
}
