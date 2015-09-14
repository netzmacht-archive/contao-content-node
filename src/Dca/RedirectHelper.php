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

use Contao\Input;
use Netzmacht\Contao\Toolkit\ServiceContainerTrait;

/**
 * Class RedirectHelper.
 *
 * @package Netzmacht\Contao\ContentNode\Dca
 */
class RedirectHelper
{
    use ServiceContainerTrait;

    /**
     * The input class.
     *
     * @var Input
     */
    private $input;

    /**
     * RedirectHelper constructor.
     */
    public function __construct()
    {
        $this->input = $this->getServiceContainer()->getInput();
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
     * Redirect to the content page when trying to access the content node.
     *
     * This fixes the edit links on the header.
     *
     * @return void
     */
    public function redirect()
    {
        if ($this->input->get('table') === 'tl_content_node') {
            $model = \ContentModel::findByPk($this->input->get('id'));

            if (!$model) {
                \Controller::log(sprintf('Content node "%s" not found', $this->input->get('id')), __METHOD__, TL_ERROR);
                \Controller::redirect('contao/main.php?act=error');
            }

            $nodes = ($model->ptable === 'tl_content_node') ? '1' : '';
            $url   = \Backend::addToUrl('table=tl_content&amp;nodes=' . $nodes);

            \Controller::redirect($url);
        }
    }
}
