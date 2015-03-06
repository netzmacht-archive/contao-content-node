<?php

$GLOBALS['TL_CONTENT_NODE']   = array();
$GLOBALS['TL_CONTENT_NODE'][] = 'node';

$GLOBALS['BE_MOD']['content']['article']['tables'][] = 'tl_content_node';

$GLOBALS['TL_CTE']['nodes']['node'] = 'Netzmacht\Contao\NestedContent\NodeElement';

$GLOBALS['TL_MODELS']['tl_content_node'] = 'Netzmacht\Contao\NestedContent\Model\NestedContentModel';

$GLOBALS['TL_WRAPPERS']['start'][] = 'node';
$GLOBALS['TL_WRAPPERS']['stop'][] = 'node';
