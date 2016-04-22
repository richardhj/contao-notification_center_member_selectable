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
$GLOBALS['TL_DCA']['tl_module']['palettes']['notification_selector'] = '{title_legend},name,headline,type;{config_legend},nc_member_customizable_notifications,nc_member_customizable_inputType,nc_member_customizable_label;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';


/**
 * Fields
 */
$GLOBALS['TL_DCA']['tl_module']['fields']['nc_member_customizable_notifications'] = array
(
	'label'            => &$GLOBALS['TL_LANG']['tl_module']['nc_member_customizable_notifications'],
	'exclude'          => true,
	'inputType'        => 'checkboxWizard',
	'options_callback' => array('\NotificationCenter\Util\MemberCustomizableHelper', 'getMessages'),
	'eval'             => array
	(
		'multiple'  => true,
		'mandatory' => true
	),
	'sql'              => "text NULL"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['nc_member_customizable_inputType'] = array
(
	'label'     => &$GLOBALS['TL_LANG']['tl_module']['nc_member_customizable_inputType'],
	'exclude'   => true,
	'inputType' => 'select',
	'default'   => 'checkbox',
	'options'   => array
	(
		'radio',
		'checkbox'
	),
	'eval'      => array
	(
		'mandatory' => true,
		'tl_class'  => 'w50'
	),
	'sql'       => "varchar(64) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['nc_member_customizable_label'] = array
(
	'label'     => &$GLOBALS['TL_LANG']['tl_module']['nc_member_customizable_label'],
	'exclude'   => true,
	'inputType' => 'text',
	'eval'      => array
	(
		'placeholder'    => '##message_title## (##gateway_title##)',
		'maxlength'      => 64,
		'decodeEntities' => true,
		'tl_class'       => 'w50'
	),
	'sql'       => "varchar(64) NOT NULL default ''"
);
