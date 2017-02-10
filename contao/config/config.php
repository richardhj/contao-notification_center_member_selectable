<?php
/**
 * Member selectable messages for the notification_center extension for Contao Open Source CMS
 *
 * Copyright (c) 2016 Richard Henkenjohann
 *
 * @package NotificationCenterMemberSelectable
 * @author  Richard Henkenjohann <richardhenkenjohann@googlemail.com>
 */


/**
 * Front end modules
 */
$GLOBALS['FE_MOD']['user']['nc_manage_messages'] = '\NotificationCenter\Module\MemberCustomizeMessages';


/**
 * Hooks
 */
$GLOBALS['TL_HOOKS']['sendNotificationMessage'][] =
    ['\NotificationCenter\Util\MemberCustomizableHelper', 'skipUnselectedMessages'];


/**
 * Models
 */
$GLOBALS['TL_MODELS']['tl_nc_member_messages'] = 'NotificationCenter\Model\MemberMessages';
