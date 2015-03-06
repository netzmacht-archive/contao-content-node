<?php

$GLOBALS['TL_DCA']['tl_content_node'] = array
(
    'config' => array(
        'dataContainer'    => 'Table',
        'ctable'           => array('tl_content'),
        'sql'              => array
        (
            'keys' => array
            (
                'id'      => 'primary',
            )
        )
    ),

    'fields' => array
    (
        'id'           => array
        (
            'sql' => "int(10) unsigned NOT NULL"
        ),
        'tstamp'       => array
        (
            'sql' => "int(10) unsigned NOT NULL default '0'"
        ),
    )
);
