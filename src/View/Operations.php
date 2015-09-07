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

use ContaoCommunityAlliance\Translator\TranslatorInterface;
use Netzmacht\Contao\Toolkit\Dca;

/**
 * Class operations generate the operations of a content node.
 *
 * @package Netzmacht\Contao\ContentNode\View
 */
class Operations
{
    /**
     * The table name.
     *
     * @var string
     */
    private $table;

    /**
     * The translator.
     *
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * Construct.
     *
     * @param string              $tableName  The table name.
     * @param TranslatorInterface $translator The translator.
     */
    public function __construct($tableName, TranslatorInterface $translator)
    {
        $this->table       = $tableName;
        $this->translator = $translator;
    }

    /**
     * Call the button callback.
     *
     * @param array  $button     The button definition.
     * @param array  $row        The current row.
     * @param string $label      The label.
     * @param string $title      The title.
     * @param string $attributes The attributes.
     *
     * @return string
     */
    private function callButtonCallback($button, $row, $label, $title, $attributes)
    {
        if (is_array($button['button_callback'])) {
            $button['button_callback'][0] = \System::importStatic($button['button_callback'][0]);
        }

        if (is_callable($button['button_callback'])) {
            return call_user_func(
                $button['button_callback'],
                $row,
                $button['href'] .= '&nodes=1',
                $label,
                $title,
                $button['icon'],
                $attributes,
                $this->table,
                array(), // Root ids
                null,
                false,
                null, // Previous
                null // Next
            );
        }

        return '';
    }

    /**
     * Generate the list buttons.
     *
     * @param array $row The data row.
     *
     * @return string
     */
    private function generateListOperations($row)
    {
        $dca    = (array) Dca::get($this->table, 'list/operations');
        $return = '';

        foreach ($dca as $name => $button) {
            // Cut mode would create empty child list. Disable so far.
            if ($name === 'cut') {
                continue;
            }

            $button = is_array($button) ? $button : array($button);
            $id     = specialchars(rawurldecode($row['id']));

            $label      = $button['label'][0] ?: $name;
            $title      = sprintf($button['label'][1] ?: $name, $id);
            $attributes = ($button['attributes'] != '') ? ' ' . ltrim(sprintf($button['attributes'], $id, $id)) : '';

            // Add the key as CSS class
            if (strpos($attributes, 'class="') !== false) {
                $attributes = str_replace('class="', 'class="' . $name . ' ', $attributes);
            } else {
                $attributes = ' class="' . $name . '"' . $attributes;
            }

            // Call a custom function instead of using the default button
            if ($button['button_callback']) {
                $return .= $this->callButtonCallback($button, $row, $label, $title, $attributes);
                continue;
            }

            if ($name == 'show') {
                $attributes .= sprintf(
                    ' onclick="Backend.openModalIframe({\'width\':768,\'title\':\'%s\',\'url\':this.href});return false"',
                    specialchars(
                        str_replace("'", "\\'", $this->translator->translate('show', $this->table, array($row['id'])))
                    )
                );
            }

            $return .= sprintf(
                '<a href="%s" title="%s" %s>%s</a> ',
                \Backend::addToUrl($button['href'] .'&amp;nodes=1&amp;id=' . $row['id'] . ($name == 'show' ? '&amp;popup=1' : '')),
                specialchars($title),
                $attributes,
                \Image::getHtml($button['icon'], $label)
            );
        }

        return $return;
    }

    /**
     * Check if the node is a child of the parent id.
     *
     * @param array $row      The current row.
     * @param int   $parentId The id.
     *
     * @return bool
     */
    private function isChildOf($row, $parentId)
    {
        if ($row['pid'] == $parentId) {
            return true;
        }

        if ($row['ptable'] != 'tl_content_node') {
            return false;
        }

        $child = \ContentModel::findByPk($row['pid']);
        if ($child) {
            return $this->isChildOf($child->row(), $parentId);
        }

        return false;
    }

    /**
     * Generate the move button.
     *
     * @param array $row The current row.
     *
     * @return string
     */
    private function generateMoveButton($row)
    {
        $clipboard   = \Session::getInstance()->get('CLIPBOARD');
        $isClipboard = !empty($clipboard[$this->table]);

        // Paste buttons
        if ($isClipboard) {
            $clipboard = $clipboard[$this->table];

            if (\Input::get('mode') == 'cut' && $this->isChildOf($row, $clipboard['id'])) {
                return \Image::getHtml('pasteafter_.gif', $this->translator->translate('pasteafter.0', $this->table));
            }

            $url = \Backend::addToUrl(
                'act=' .$clipboard['mode'] . '&amp;mode=1&amp;pid=' . $row['id'] .'&amp;id=' . $clipboard['id']
            );

            return sprintf(
               ' <a href="%s" title="%s" onclick="Backend.getScrollOffset()">%s</a>',
               $url,
               specialchars(sprintf($this->translator->translate('pasteafter.1', $this->table), $row['id'])),
               \Image::getHtml('pasteafter.gif', $this->translator->translate('pasteafter.0', $this->table))
           );
        }

        return '';
    }

    /**
     * Generate the buttons.
     *
     * @param array $row The current row.
     *
     * @return string
     */
    public function generate($row)
    {
        $return  = $this->generateListOperations($row);
        $return .= $this->generateMoveButton($row);

        return $return;
    }
}
