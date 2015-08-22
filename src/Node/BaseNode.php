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
use ContentModel;
use Netzmacht\Contao\ContentNode\NodeElement;
use Netzmacht\Contao\ContentNode\View\Breadcrumb;
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
     * The template name.
     *
     * @var string
     */
    protected $template = 'ctn_default';

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
     * @param TranslatorInterface $translator
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
     * @inheritDoc
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function buildBreadcrumb(Breadcrumb $breadcrumb)
    {
        $label = $this->translator->translate($this->getName(), 'CONTENT_NODES');

        $breadcrumb->addNode($label);

        return $this;
    }

    /**
     * Generate a child.
     *
     * @param ContentModel $model The model
     *
     * @return string
     */
    private function generateChild(ContentModel $model)
    {
        if (!isset ($GLOBALS['TL_DCA']['tl_content']['list']['sorting']['child_record_callback'])) {
            return '';
        }

        $callback = $GLOBALS['TL_DCA']['tl_content']['list']['sorting']['child_record_callback'];

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
     * @param NodeElement $element
     *
     * @return array
     */
    private function generateChildren(NodeElement $element)
    {
        $children = array();
        $elements = ContentModel::findBy(
            array('pid=?', 'ptable=?'),
            array($element->id, 'tl_content_node'),
            array('order' => 'sorting')
        );

        if ($elements) {
            /** @var ContentModel $child */
            foreach ($elements as $child) {
                $children[] = $this->generateChild($child);
            }
        }

        return $children;
    }

    /**
     * @inheritDoc
     */
    public function generateBackendView(NodeElement $element, $templateName = null)
    {
        $template = new BackendTemplate($templateName ?: $this->template);
        $template
            ->set('element', $element)
            ->set('children', $this->generateChildren($element));

        return $template->parse();
    }

    /**
     * @inheritDoc
     */
    public function generateHeaderFields(array $headerFields, ContentModel $model)
    {
        $headerFields['type'] = $this->translator->translate('CTE' . $model->type, 'MSC');

        return $headerFields;
    }

    /**
     * @inheritDoc
     */
    public function getChildrenTypes(array $contentElements)
    {
        if ($this->supportedChildren === null) {
            return $contentElements;
        }

        return array_intersect($this->supportedChildren, $this->supportedChildren);
    }
}
