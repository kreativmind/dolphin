<?php defined('BX_DOL') or die('hack attempt');
/**
 * Copyright (c) BoonEx Pty Limited - http://www.boonex.com/
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 *
 * @defgroup    Groups Groups
 * @ingroup     TridentModules
 *
 * @{
 */

class BxGroupsAlertsResponse extends BxBaseModGroupsAlertsResponse
{
    public function __construct()
    {
    	$this->MODULE = 'bx_groups';
        parent::__construct();
    }
}

/** @} */
