<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

/*
 * Modules and elements
 */
$GLOBALS['BE_MOD']['content']['article']['tables'][] = 'tl_content_node';

$GLOBALS['TL_CTE']['nodes']['node'] = 'Netzmacht\Contao\ContentNode\NodeElement';

$GLOBALS['TL_WRAPPERS']['single'][] = 'node';

/*
 * Models
 */
$GLOBALS['TL_MODELS']['tl_content_node'] = 'Netzmacht\Contao\ContentNode\Model\ContentNodeModel';


/*
 * Hooks
 */
$GLOBALS['TL_HOOKS']['parseBackendTemplate'][] = array('Netzmacht\Contao\ContentNode\Dca\Helper', 'injectBreadcrumb');


/*
 * Content node definition
 */
if (!isset($GLOBALS['TL_CONTENT_NODE'])) {
    $GLOBALS['TL_CONTENT_NODE'] = array();
}

$GLOBALS['TL_CONTENT_NODE']['node']   = array();
