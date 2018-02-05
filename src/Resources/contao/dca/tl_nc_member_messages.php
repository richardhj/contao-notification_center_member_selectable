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
            'sql' => 'int(10) unsigned NOT NULL auto_increment',
        ],
        'member_id'  => [
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        'message_id' => [
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        'send'       => [
            'sql' => "char(1) NOT NULL default ''",
        ],
    ],
];
