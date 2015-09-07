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

use ContaoCommunityAlliance\Translator\TranslatorInterface;
use ContentElement;
use ContentModel;
use Netzmacht\Contao\ContentNode\Util\Filter;
use Netzmacht\Contao\ContentNode\View\Breadcrumb;
use Netzmacht\Contao\ContentNode\View\Operations;
use Netzmacht\Contao\Toolkit\Dca;
use Netzmacht\Contao\Toolkit\View\BackendTemplate;

/**
 * Class BaseNode is the default implementation of the node interface and can be used by each node type.
 *
 * @package Netzmacht\Contao\ContentNode\Node
 */
class BaseNode implements Node, TranslatorAware
{
    /**
     * The type name.
     *
     * @var string
     */
    private $name;

    /**
     * The translator.
     *
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * The template name for the backend view.
     *
     * @var string
     */
    protected $template = 'be_node_base';

    /**
     * List of supported children types. If null all are supported.
     *
     * @var array|null
     */
    private $supportedChildren;

    /**
     * BaseNode constructor.
     *
     * @param string     $name              The name of the element.
     * @param array|null $supportedChildren List of supported children types. If null all are supported.
     */
    public function __construct($name, array $supportedChildren = null)
    {
        $this->name              = $name;
        $this->supportedChildren = $supportedChildren;
    }

    /**
     * Set the translator.
     *
     * @param TranslatorInterface $translator The translator.
     *
     * @return $this
     */
    public function setTranslator(TranslatorInterface $translator)
    {
        $this->translator = $translator;

        return $this;
    }

    /**
     * Get translator.
     *
     * @return TranslatorInterface
     */
    public function getTranslator()
    {
        return $this->translator;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritDoc}
     */
    public function buildBreadcrumb(Breadcrumb $breadcrumb, ContentModel $node)
    {
        $label = $this->translator->translate($this->getName() . '.0', 'CTE', ['id' => $node->id]);

        $breadcrumb->addNode($node->id, $label, 'node-' . $node->type);

        return $this;
    }

    /**
     * Generate a child.
     *
     * @param ContentModel $model The model.
     *
     * @return string
     */
    private function generateChild(ContentModel $model)
    {
        $dca = &Dca::load('tl_content');

        if (!isset($dca['list']['sorting']['child_record_callback'])) {
            return '';
        }

        $callback = $dca['list']['sorting']['child_record_callback'];

        if (is_array($callback)) {
            $callback[0] = new $callback[0];
        }

        if (is_callable($callback)) {
            return call_user_func($callback, $model->row());
        }

        // Invalid callback.
        return '';
    }

    /**
     * {@inheritDoc}
     */
    public function findChildren($nodeId)
    {
        $elements = \ContentModel::findBy(
            array('pid=?', 'ptable=?'),
            array($nodeId, 'tl_content_node'),
            array('order' => 'sorting')
        );

        if ($elements) {
            return $elements;
        }

        return array();
    }

    /**
     * {@inheritDoc}
     */
    public function generateBackendView(ContentElement $element, $templateName = null)
    {
        $collection = $this->findChildren($element->id);
        $children   = array();
        $generated  = array();

        if ($collection) {
            foreach ($collection as $child) {
                $children[$child->id]  = $child;
                $generated[$child->id] = $this->generateChild($child);
            }
        }

        $template = new BackendTemplate($templateName ?: $this->template);
        $template
            ->set('element', $element)
            ->set('operations', new Operations('tl_content', $this->translator))
            ->set('children', $children)
            ->set('elements', $generated);

        return $template->parse();
    }

    /**
     * {@inheritDoc}
     */
    public function generateChildInBackendView(array $child, $generated)
    {
        return $generated;
    }

    /**
     * {@inheritDoc}
     */
    public function generateHeaderFields(array $headerFields, ContentModel $model)
    {
        $label                = $this->translator->translate('id.0', 'MSC');
        $headerFields[$label] = $model->id;

        foreach (array('type', 'invisible', 'start', 'stop') as $field) {
            $label                = $this->translator->translate($field . '.0', 'tl_content');
            $headerFields[$label] = $this->translator->translate($model->$field, 'CTE');
        }

        return $headerFields;
    }

    /**
     * {@inheritDoc}
     */
    public function filterContentElements(Filter $filter, $parentType)
    {
        if ($this->supportedChildren === null || !$parentType || $parentType !== $this->getName()) {
            return $filter;
        }

        return $filter->in($this->supportedChildren);
    }
}
