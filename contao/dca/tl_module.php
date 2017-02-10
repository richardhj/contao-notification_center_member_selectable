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
$GLOBALS['TL_DCA']['tl_module']['palettes']['nc_manage_messages'] =
    '{title_legend},name,headline,type;{config_legend},nc_member_customizable_notifications,nc_member_customizable_inputType,nc_member_customizable_label,nc_member_customizable_mandatory;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';


/**
 * Fields
 */
$GLOBALS['TL_DCA']['tl_module']['fields']['nc_member_customizable_notifications'] = [
    'label'            => &$GLOBALS['TL_LANG']['tl_module']['nc_member_customizable_notifications'],
    'exclude'          => true,
    'inputType'        => 'checkboxWizard',
    'options_callback' => ['\NotificationCenter\Util\MemberCustomizableHelper', 'getMessages'],
    'eval'             => [
        'multiple'  => true,
        'mandatory' => true,
    ],
    'sql'              => "text NULL",
];

$GLOBALS['TL_DCA']['tl_module']['fields']['nc_member_customizable_inputType'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_module']['nc_member_customizable_inputType'],
    'exclude'   => true,
    'inputType' => 'select',
    'default'   => 'checkbox',
    'options'   => [
        'radio',
        'checkbox',
    ],
    'eval'      => [
        'mandatory' => true,
        'tl_class'  => 'w50',
    ],
    'sql'       => "varchar(64) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_module']['fields']['nc_member_customizable_label'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_module']['nc_member_customizable_label'],
    'exclude'   => true,
    'inputType' => 'text',
    'eval'      => [
        'placeholder'    => '##message_title## (##gateway_title##)',
        'maxlength'      => 64,
        'decodeEntities' => true,
        'tl_class'       => 'w50',
    ],
    'sql'       => "varchar(64) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_module']['fields']['nc_member_customizable_mandatory'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_module']['nc_member_customizable_mandatory'],
    'exclude'   => true,
    'inputType' => 'checkbox',
    'eval'      => [
        'tl_class' => 'w50 m12',
    ],
    'sql'       => "char(1) NOT NULL default ''",
];
