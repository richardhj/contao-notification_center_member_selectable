<?php

/**
 * This file is part of richardhj/contao-notification_center_member_selectable.
 *
 * Copyright (c) 2016-2018 Richard Henkenjohann
 *
 * @package   richardhj/contao-notification_center_member_selectable
 * @author    Richard Henkenjohann <richardhenkenjohann@googlemail.com>
 * @copyright 2016-2018 Richard Henkenjohann
 * @license   https://github.com/richardhj/contao-notification_center_member_selectable/blob/master/LICENSE LGPL-3.0
 */

use Richardhj\NotificationCenterMembersChoiceBundle\Model\MemberMessages;
use Richardhj\NotificationCenterMembersChoiceBundle\Module\MemberCustomizeMessages;


/**
 * Front end modules
 */
$GLOBALS['FE_MOD']['user']['nc_manage_messages'] = MemberCustomizeMessages::class;


/**
 * Hooks
 */
$GLOBALS['TL_HOOKS']['sendNotificationMessage'][] = [
    'richardhj.notification_center_members_choice.hook_listener.send_notification_message',
    'onSendNotificationMessage',
];


/**
 * Models
 */
$GLOBALS['TL_MODELS']['tl_nc_member_messages'] = MemberMessages::class;
