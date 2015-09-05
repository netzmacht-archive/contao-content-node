<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\ContentNode\Util;

use RecursiveArrayIterator;
use RecursiveIteratorIterator;

/**
 * ArrayUtil.
 *
 * @package Netzmacht\Contao\ContentNode\Util
 */
class ArrayUtil
{
    /**
     * Flatten an array.
     *
     * @param array $array The input array.
     *
     * @return array
     */
    public static function flatten(array $array)
    {
        $flatten = array();

        foreach (new RecursiveIteratorIterator(new RecursiveArrayIterator($array)) as $value) {
            $flatten[] = $value;
        }

        return $flatten;
    }
}
