<?php
/**
 * @package     Dolphin Core
 * @copyright   Copyright (c) BoonEx Pty Limited - http://www.boonex.com/
 * @license     CC-BY - http://creativecommons.org/licenses/by/3.0/
 */

require_once('./inc/header.inc.php');
require_once(BX_DIRECTORY_PATH_INC . "languages.inc.php");
require_once(BX_DIRECTORY_PATH_INC . "params.inc.php");
require_once(BX_DIRECTORY_PATH_INC . "design.inc.php");

check_logged();

$sSys = isset($_REQUEST['sys']) ? $_REQUEST['sys'] : '';
$sAction = isset($_REQUEST['action']) && preg_match ('/^[A-Za-z_-]+$/', $_REQUEST['action']) ? $_REQUEST['action'] : '';
$iId = (int)$_REQUEST['id'];

bx_import ('BxTemplCmtsView');
$aSystems = BxDolCmts::getSystems ();

if ($sSys && $sAction && $iId && ($oCmts = BxDolCmts::getObjectInstance($sSys, $iId, true))) {
    header('Content-Type: text/html; charset=utf-8');
    $sMethod = 'action' . $sAction;
    echo $oCmts->$sMethod();
}

