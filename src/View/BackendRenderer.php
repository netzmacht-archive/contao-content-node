<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\ContentNode\View;


use Netzmacht\Contao\ContentNode\Node\Registry;

class BackendRenderer
{
    /**
     * @var Registry
     */
    private $registry;

    /**
     * The callback.
     * @var callable
     */
    private $callback;

    /**
     * Construct.
     *
     * @param Registry $registry The registry.
     * @param callable $callback The callback.
     */
    public function __construct(Registry $registry, $callback)
    {

        $this->registry = $registry;
        $this->callback = $callback;
    }

    /**
     * Invoke the rendering.
     *
     * @param array $row The child as array.
     *
     * @return mixed|string
     */
    public function __invoke($row)
    {
        $buffer = call_user_func($this->callback, $row);

        if ($row['ptable'] === 'tl_content_node') {
            $parent = \ContentModel::findByPk($row['pid']);

            if ($this->registry->hasNodeType($parent->type)) {
                $node = $this->registry->getNode($parent->type);

                return $node->generateChildInBackendView($row, $buffer);
            }
        }
        return $buffer;
    }
}
