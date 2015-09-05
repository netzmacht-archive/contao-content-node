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
use DataContainer;
use Netzmacht\Contao\ContentNode\Exception\AccessDeniedException;
use Netzmacht\Contao\ContentNode\Model\ContentNodeModel;
use Netzmacht\Contao\ContentNode\Node\Registry;
use Netzmacht\Contao\ContentNode\View\Breadcrumb;
use Netzmacht\Contao\Toolkit\Assets;
use Netzmacht\Contao\Toolkit\Dca;
use Netzmacht\Contao\Toolkit\ServiceContainerTrait;

/**
 * Content backend view helper.
 *
 * @package Netzmacht\Contao\ContentNode\Dca
 */
class Helper
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
     * Create a callback definition for the given name.
     *
     * @param string $name The callback method name.
     *
     * @return array
     */
    public static function callback($name)
    {
        return array (get_called_class(), $name);
    }

    /**
     * Initialize the backend view.
     *
     * @param DataContainer $dataContainer The data container.
     *
     * @return void
     */
    public function initialize($dataContainer)
    {
        if (TL_MODE !== 'BE') {
            return;
        }

        Assets::addStylesheet('system/modules/content-node/assets/css/backend.css');

        $parentType = null;
        if ($dataContainer->parentTable === 'tl_content_node') {
            $parent = \ContentModel::findByPk(CURRENT_ID);

            if ($parent && $this->registry->hasNodeType($parent->tye)) {
                $parentType = $this->registry->getNode($parent->type);
            }
        }

        try {
            $restriction = new ContentElementAccess(
                $this->registry,
                $this->getService('database.connection'),
                $this->getService('session'),
                $this->getService('input')
            );

            $restriction->restrict($dataContainer->id, $parentType);

        } catch (AccessDeniedException $e) {
            \Controller::log($e->getMessage(), 'ContentElementAccess::resitrct', TL_ACCESS);
            \Controller::redirect(\Environment::get('script') . '?act=error');
        }
    }

    /**
     * Create the node container.
     *
     * @param mixed         $value         The type value.
     * @param DataContainer $dataContainer The data container.
     *
     * @return mixed
     */
    public function createNodeContainer($value, $dataContainer)
    {
        if ($this->registry->hasNodeType($value)) {
            $container = ContentNodeModel::findOneBy('id', $dataContainer->id);

            if (!$container) {
                $container     = new ContentNodeModel();
                $container->id = $dataContainer->id;
                $container->save();
            }
        }

        return $value;
    }

    /**
     * Generate the node type button.
     *
     * @param array  $row        The data row.
     * @param string $href       The button href.
     * @param string $title      The button title.
     * @param string $label      The button label.
     * @param string $icon       The icon.
     * @param string $attributes Html attributes.
     *
     * @return string
     */
    public function generateButton($row, $href, $title, $label, $icon, $attributes)
    {
        if (!$this->registry->hasNodeType($row['type'])) {
            return '';
        }

        return sprintf(
            '<a href="%s" title="%s"%s>%s</a> ',
            \Backend::addToUrl($href . '&amp;id=' . $row['id']),
            $title,
            $attributes,
            \Image::getHtml($icon, $label)
        );
    }

    /**
     * Generate the header fields.
     *
     * @param array         $fields        The header fields.
     * @param DataContainer $dataContainer The data container.
     *
     * @return array
     */
    public function generateHeaderFields($fields, $dataContainer)
    {
        $model = \ContentModel::findByPk($dataContainer->id);

        if ($model && $this->registry->hasNodeType($model->type)) {
            return $this->registry->getNode($model->type)->generateHeaderFields($fields, $model);
        }

        return $fields;
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

            $buffer = str_replace(
                array('<div class="tl_listing_container', 'table=tl_content_node'),
                array($breadcrumb->generate() . '<div class="tl_listing_container', 'table=tl_content'),
                $buffer
            );
        }

        return $buffer;
    }
}
