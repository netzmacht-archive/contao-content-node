<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

use Netzmacht\Contao\ContentNode\Dca\Helper;
use Netzmacht\Contao\ContentNode\Dca\PermissionCallback;

/*
 * Config
 */
$GLOBALS['TL_DCA']['tl_content']['config']['onload_callback'][] = Helper::callback('initialize');

/*
 * Palettes
 */
$GLOBALS['TL_DCA']['tl_content']['metapalettes']['node'] = array(
    'type'      => array('type', 'headline'),
    'template'  => array(':hide', 'customTpl'),
    'protected' => array(':hide', 'protected'),
    'expert'    => array(':hide', 'guests', 'cssID', 'space'),
    'invisible' => array(':hide', 'invisible', 'start', 'stop'),
);

/*
 * List config
 */
array_insert(
    $GLOBALS['TL_DCA']['tl_content']['list']['operations'],
    0,
    array(
        'nodes' => array(
            'label'           => &$GLOBALS['TL_LANG']['tl_content']['nodes'],
            'href'            => 'nodes=1',
            'icon'            => 'system/modules/content-node/assets/img/nodes.png',
            'button_callback' => Helper::callback('generateButton'),
        )
    )
);

/*
 * Fields
 */
$GLOBALS['TL_DCA']['tl_content']['fields']['type']['save_callback'][] = Helper::callback('createNodeContainer');

/*
 * Customize config when being in the nodes mode.
 */
if (\Input::get('nodes')) {
    $GLOBALS['TL_DCA']['tl_content']['config']['ptable'] = 'tl_content_node';

    $mapping = array(
        'tl_article'         => 'tl_content',
        'tl_news'            => 'tl_content_news',
        'tl_calendar_events' => 'tl_content_calendar'
    );

    foreach ($mapping as $table => $callback) {
        $index = array_search(
            array($callback, 'checkPermission'),
            $GLOBALS['TL_DCA']['tl_content']['config']['onload_callback']
        );

        if ($index) {
            $GLOBALS['TL_DCA']['tl_content']['config']['onload_callback'][$index] = new PermissionCallback($table);
        }
    }

    $GLOBALS['TL_DCA']['tl_content']['list']['sorting']['headerFields']    = array();
    $GLOBALS['TL_DCA']['tl_content']['list']['sorting']['header_callback'] = Helper::callback('generateHeaderFields');
}
