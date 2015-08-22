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

use ContaoCommunityAlliance\Translator\TranslatorInterface as Translator;

/**
 * Interface TranslatorAware describes a node which uses the translator.
 *
 * @package Netzmacht\Contao\ContentNode\Node
 */
interface TranslatorAware
{
    /**
     * Set the translator instance.
     *
     * @param Translator $translator The translator.
     *
     * @return $this
     */
    public function setTranslator(Translator $translator);

    /**
     * Get the translator.
     *
     * @return Translator
     */
    public function getTranslator();
}
