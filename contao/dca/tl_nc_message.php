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
 * Palettes
 */
foreach ($GLOBALS['TL_DCA']['tl_nc_message']['palettes'] as $name => $palette) {
    if (in_array($name, ['__selector__', 'default'])) {
        continue;
    }

    $GLOBALS['TL_DCA']['tl_nc_message']['palettes'][$name] .= ',member_customizable';
}


/**
 * Fields
 */
$GLOBALS['TL_DCA']['tl_nc_message']['fields']['member_customizable'] = [
    'label'         => &$GLOBALS['TL_LANG']['tl_nc_message']['member_customizable'],
    'exclude'       => true,
    'inputType'     => 'checkbox',
    'eval'          => [
        'tl_class' => 'w50 m12',
    ],
    'save_callback' => [
        ['\NotificationCenter\Util\MemberCustomizableHelper', 'checkMessageMemberCustomizable'],
    ],
    'sql'           => "char(1) NOT NULL default ''",
];
