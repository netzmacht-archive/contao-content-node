<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

use Netzmacht\Contao\ContentNode\Node\Factory;
use Netzmacht\Contao\ContentNode\Node\Registry;

global $container;

$container['content-nodes.registry'] = $container->share(
    function ($container) {
        return new Registry(
            new Factory($container['event-dispatcher'], $GLOBALS['TL_CONTENT_NODE'])
        );
    }
);
