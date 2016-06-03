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
 * Fields
 */
$GLOBALS['TL_LANG']['tl_module']['nc_member_customizable_notifications'][0] = 'Notifications';
$GLOBALS['TL_LANG']['tl_module']['nc_member_customizable_notifications'][1] = 'Choose the notifications that can be customized by the user via this module.';
$GLOBALS['TL_LANG']['tl_module']['nc_member_customizable_label'][0] = 'Option label';
$GLOBALS['TL_LANG']['tl_module']['nc_member_customizable_label'][1] = 'Enter the option\'s label. All fields of the message (prefix "message_"), of the notification (prefix "notification_") and of the gateway (prefix "gateway_") can be used to parse the label. E.g. <em>"use ##gateway_title##"</em> will be parsed as <em>"use e mail"</em> and so on.';
$GLOBALS['TL_LANG']['tl_module']['nc_member_customizable_inputType'][0] = 'Option type';
$GLOBALS['TL_LANG']['tl_module']['nc_member_customizable_inputType'][1] = 'Choose <em>checkbox</em> if the user should have the possibility to select multiple messages. Choose <em>radio</em> if the user has to select only one message.';
$GLOBALS['TL_LANG']['tl_module']['nc_member_customizable_mandatory'][0] = 'Mandatory';
$GLOBALS['TL_LANG']['tl_module']['nc_member_customizable_mandatory'][1] = 'Choose whether the user has to select at least one message (mandatory) or whether he can turn off all messages.';
