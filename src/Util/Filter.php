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

/**
 * Class Filter.
 *
 * @package Netzmacht\Contao\ContentNode\Util
 */
class Filter
{
    /**
     * The content elements grouped in categories.
     *
     * @var array
     */
    private $elements;

    /**
     * Filter constructor.
     *
     * @param array $elements The content elements grouped in categories.
     */
    public function __construct(array $elements)
    {
        $this->elements = $elements;
    }

    /**
     * Get all elements but not the ones being mentioned in types.
     *
     * @param array|string $types The types being filtered.
     *
     * @return $this
     */
    public function not($types)
    {
        $types = array_flip((array) $types);

        foreach ($this->elements as $group => $config) {
            $this->elements[$group] = array_diff_key($config, $types);
        }

        return $this;
    }

    /**
     * Get all elements which are in a set of types.
     *
     * @param array|string $types The types being filtered.
     *
     * @return $this
     *
     * @SuppressWarnings(PHPMD.ShortMethodName)
     */
    public function in($types)
    {
        $types = array_flip((array) $types);

        foreach ($this->elements as $group => $config) {
            $this->elements[$group] = array_intersect_key($config, $types);
        }

        return $this;
    }

    /**
     * Filter by a callback. Each callback get the content type as argument.
     *
     * @param callable $callback The callback.
     *
     * @return $this
     */
    public function callback($callback)
    {
        foreach ($this->elements as $group => $config) {
            $this->elements[$group] = array_filter($config, $callback, ARRAY_FILTER_USE_KEY);
        }

        return $this;
    }

    /**
     * Get the filtered list of elements.
     *
     * @return array
     */
    public function getResult()
    {
        return array_filter($this->elements);
    }
}
