<?php

array_insert(
    $GLOBALS['TL_DCA']['tl_content']['list']['operations'],
    1,
    array(
        'nodes' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_content']['nodes'],
            'href'  => '&nodes=1'
        )
    )
);
