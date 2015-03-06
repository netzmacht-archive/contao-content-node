<?php

array_insert(
    $GLOBALS['TL_DCA']['tl_content']['list']['operations'],
    1,
    array(
        'nodes' => array(
            'label'           => &$GLOBALS['TL_LANG']['tl_content']['nodes'],
            'href'            => 'nodes=1&amp;popup=1',
            'icon'            => 'system/modules/content-node/assets/img/nodes.png',
            'button_callback' => array('Netzmacht\Contao\NestedContent\Dca\Helper', 'generateButton'),
        )
    )
);

$GLOBALS['TL_DCA']['tl_content']['fields']['type']['save_callback'][] = array(
    'Netzmacht\Contao\NestedContent\Dca\Helper',
    'createNodeContainer'
);

if (\Input::get('nodes')) {
    $GLOBALS['TL_DCA']['tl_content']['config']['ptable'] = 'tl_content_node';

    $index = array_search(
        array('tl_content', 'checkPermission'),
        $GLOBALS['TL_DCA']['tl_content']['config']['onload_callback']
    );

    if ($index) {
        unset($GLOBALS['TL_DCA']['tl_content']['config']['onload_callback'][$index]);
    }

    $GLOBALS['TL_DCA']['tl_content']['list']['sorting']['headerFields'] = array('title', 'pid');
}
