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
$GLOBALS['TL_DCA']['tl_nc_message']['palettes']['__selector__'][] = 'member_customizable';


/**
 * SubPalettes
 */
$GLOBALS['TL_DCA']['tl_nc_message']['subpalettes']['member_customizable'] = 'member_customizable_default_behavior';


/**
 * Fields
 */
$GLOBALS['TL_DCA']['tl_nc_message']['fields']['member_customizable'] = [
    'label'         => &$GLOBALS['TL_LANG']['tl_nc_message']['member_customizable'],
    'exclude'       => true,
    'inputType'     => 'checkbox',
    'eval'          => [
        'submitOnChange' => true,
        'tl_class'       => 'w50 m12',
    ],
    'save_callback' => [
        ['\NotificationCenter\Util\MemberCustomizableHelper', 'checkMessageMemberCustomizable'],
    ],
    'sql'           => "char(1) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_nc_message']['fields']['member_customizable_default_behavior'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_nc_message']['member_customizable_default_behavior'],
    'exclude'   => true,
    'inputType' => 'select',
    'options'   => [
        'opt-out',
        'opt-in',
    ],
    'reference' => &$GLOBALS['TL_LANG']['tl_nc_message']['member_customizable_default_behavior_options'],
    'eval'      => [
        'mandatory' => true,
        'tl_class'  => 'w50',
    ],
    'sql'       => "varchar(64) NOT NULL default ''",
];
