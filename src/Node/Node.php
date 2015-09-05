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

use ContentElement;
use ContentModel;
use Netzmacht\Contao\ContentNode\Util\Filter;
use Netzmacht\Contao\ContentNode\View\Breadcrumb;

/**
 * A Node provides relevant method to customize the backend view behaviour of a content element.
 *
 * @package Netzmacht\Contao\ContentNode
 */
interface Node
{
    /**
     * Get the name of the type.
     *
     * @return string
     */
    public function getName();

    /**
     * Add the node to the breadcrumb. The breadcrumb is used to visualize multiple nested nodes.
     *
     * @param Breadcrumb   $breadcrumb The breadcrumb.
     * @param ContentModel $node       The model for the node.
     *
     * @return $this
     */
    public function buildBreadcrumb(Breadcrumb $breadcrumb, ContentModel $node);

    /**
     * Generate the backend view of the nested element.
     *
     * @param ContentElement $element  The nested content element.
     * @param string|null    $template The template name. Can be used to render by a custom template.
     *
     * @return string
     */
    public function generateBackendView(ContentElement $element, $template = null);

    /**
     * Generate the child in the backend view.
     *
     * @param array  $child     The child model as array.
     * @param string $generated The generated element.
     *
     * @return string
     */
    public function generateChildInBackendView(array $child, $generated);

    /**
     * Generate the header fields for the content node type.
     *
     * This method is triggered when the header_field callback is called.
     *
     * @param array        $headerFields The header fields.
     * @param ContentModel $model        The content model.
     *
     * @return array
     */
    public function generateHeaderFields(array $headerFields, ContentModel $model);

    /**
     * Get supported child types. Useful to limit to a specific type of elements.
     *
     * @param Filter $contentElements All available elements grouped by category.
     * @param string $parentType      The parent type.
     *
     * @return Filter
     */
    public function filterContentElements(Filter $contentElements, $parentType);

    /**
     * Find children nodes.
     *
     * @param int $nodeId The node id.
     *
     * @return \Model\Collection|ContentModel[]
     */
    public function findChildren($nodeId);
}
