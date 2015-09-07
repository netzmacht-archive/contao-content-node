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

use Netzmacht\Contao\Toolkit\Dca\Definition;

/**
 * Interface DefinitionAware.
 *
 * @package Netzmacht\Contao\ContentNode\Node
 */
interface DefinitionAware
{
    /**
     * Get the dca definition.
     *
     * @return Definition
     */
    public function getDefinition();

    /**
     * Set the dca definition.
     *
     * @param Definition $definition The data definition.
     *
     * @return $this
     */
    public function setDefinition(Definition $definition);
}
