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
 * Table tl_nc_member_messages
 */
$GLOBALS['TL_DCA']['tl_nc_member_messages'] = [

    // Config
    'config' => [
        'sql' => [
            'keys' => [
                'id'                   => 'primary',
                'member_id,message_id' => 'unique',
            ],
        ],
    ],

    // Fields
    'fields' => [
        'id'         => [
            'sql' => "int(10) unsigned NOT NULL auto_increment",
        ],
        'member_id'  => [
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        'message_id' => [
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
    ],
];
