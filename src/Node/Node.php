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

use ContentModel;
use DataContainer;
use Netzmacht\Contao\ContentNode\NodeElement;
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
     * @param Breadcrumb $breadcrumb The breadcrumb.
     *
     * @return $this
     */
    public function buildBreadcrumb(Breadcrumb $breadcrumb);

    /**
     * Generate the backend view of the nested element.
     *
     * @param NodeElement $element  The nested content element.
     * @param string|null $template The template name. Can be used to render by a custom template.
     *
     * @return string
     */
    public function generateBackendView(NodeElement $element, $template = null);

    /**
     * Generate the header fields for the content node type.
     *
     * This method is triggered when the header_field callback is called.
     *
     * @param array         $headerFields  The header fields.
     * @param ContentModel  $model         The content model.
     *
     * @return array
     */
    public function generateHeaderFields(array $headerFields, ContentModel $model);

    /**
     * Get supported child types. Useful to limit to a specific type of elements.
     *
     * @param array $contentElements All available elements.
     *
     * @return array
     */
    public function getChildrenTypes(array $contentElements);
}
