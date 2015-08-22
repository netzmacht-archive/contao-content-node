<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\ContentNode\Subscriber;

use Netzmacht\Contao\ContentNode\Event\CreateNodeEvent;
use Netzmacht\Contao\ContentNode\Event\InitializeNodeEvent;
use Netzmacht\Contao\ContentNode\Node\TranslatorAware;
use Netzmacht\Contao\Toolkit\ServiceContainerTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class Subscriber
 *
 * @package Netzmacht\Contao\ContentNode\Subscriber
 */
class Subscriber implements EventSubscriberInterface
{
    use ServiceContainerTrait;

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return array(
            CreateNodeEvent::NAME     => 'setClassName',
            InitializeNodeEvent::NAME => 'setTranslator'
        );
    }

    /**
     * Set the class name for the factory.
     *
     * @param CreateNodeEvent $event The event.
     *
     * @return void
     */
    public function setClassName(CreateNodeEvent $event)
    {
        if (!$event->getClassName()) {
            $class = $event->getConfigValue('class', 'Netzmacht\Contao\ContentNode\Node\BaseNode');
            $event->setClassName($class);
        }
    }

    /**
     * Set the translator if the node supports the translator aware interface.
     *
     * @param InitializeNodeEvent $event The event.
     *
     * @return void
     */
    public function setTranslator(InitializeNodeEvent $event)
    {
        $node = $event->getNode();

        if ($node instanceof TranslatorAware && !$node->getTranslator()) {
            $node->setTranslator($this->getService('translator'));
        }
    }
}
