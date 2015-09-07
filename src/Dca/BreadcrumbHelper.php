<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\ContentNode\Dca;

use Contao\ContentModel;
use Netzmacht\Contao\ContentNode\Node\Registry;
use Netzmacht\Contao\ContentNode\View\Breadcrumb;
use Netzmacht\Contao\Toolkit\ServiceContainerTrait;

/**
 * Data container helper to inject the breadcrumb.
 *
 * @package Netzmacht\Contao\ContentNode\Dca
 */
class BreadcrumbHelper
{
    use ServiceContainerTrait;

    /**
     * The node type registry.
     *
     * @var Registry
     */
    private $registry;

    /**
     * Construct.
     */
    public function __construct()
    {
        $this->registry = $this->getService('content-nodes.registry');
    }

    /**
     * Check if breadcrumb is required.
     *
     * @param string $template The template name.
     *
     * @return bool
     */
    private function isBreadcrumbRequired($template)
    {
        return $template === 'be_main'
        && \Input::get('table') === 'tl_content'
        && !\Input::get('act')
        && \Input::get('nodes');
    }

    /**
     * Add the parent to the breadcrumb.
     *
     * @param ContentModel $model      The content model.
     * @param Breadcrumb   $breadcrumb The breadcrumb.
     *
     * @return void
     */
    private function addParentToBreadcrumb(ContentModel $model, Breadcrumb $breadcrumb)
    {
        if ($model) {
            switch ($model->ptable) {
                case 'tl_article':
                case '':
                    $article = \ArticleModel::findByPk($model->pid);
                    $breadcrumb->addNode($article->id, $article->title, 'article', false);
                    break;

                // TODO: News and Events
                // TODO: Event for others.

                default:
                    // Do Nothing.
            }
        }
    }

    /**
     * Inject the breadcrumb.
     *
     * @param string $buffer   The generated output.
     * @param string $template The template name.
     *
     * @return string
     */
    public function injectBreadcrumb($buffer, $template)
    {
        if (!$this->isBreadcrumbRequired($template)) {
            return $buffer;
        }

        $items = array();
        $model = \ContentModel::findByPk(CURRENT_ID);
        $current = $model;

        while ($model && $this->registry->hasNodeType($model->type)) {
            array_unshift($items, $model);

            if ($model->ptable !== 'tl_content_node') {
                break;
            }

            $model = \ContentModel::findByPk($model->pid);
        }

        if ($items) {
            $breadcrumb = new Breadcrumb();
            $this->addParentToBreadcrumb($model, $breadcrumb);

            foreach ($items as $item) {
                $this->registry->getNode($item->type)->buildBreadcrumb($breadcrumb, $item);
            }

            $replacement = $breadcrumb->generate() . '<div class="tl_listing_container node-' . $current->type;
            $buffer      = str_replace('<div class="tl_listing_container', $replacement, $buffer);
        }

        return $buffer;
    }
}
