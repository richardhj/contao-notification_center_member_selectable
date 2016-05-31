<?php
/**
 * Member selectable messages for the notification_center extension for Contao Open Source CMS
 *
 * Copyright (c) 2016 Richard Henkenjohann
 *
 * @package NotificationCenterMemberSelectable
 * @author  Richard Henkenjohann <richardhenkenjohann@googlemail.com>
 */

namespace NotificationCenter\Module;

use Haste\Form\Form;
use NotificationCenter\Gateway\Base;
use NotificationCenter\Gateway\GatewayInterface;
use NotificationCenter\MessageDraft\MessageDraftCheckSendInterface;
use NotificationCenter\MessageDraft\MessageDraftFactoryInterface;
use NotificationCenter\Model\MemberMessages;
use NotificationCenter\Model\Message;
use NotificationCenter\Model\Notification;


/**
 * Class MemberCustomizeMessages
 * @package NotificationCenter\Module
 *
 * @property mixed  $nc_member_customizable_notifications
 * @property string $nc_member_customizable_label
 * @property string $nc_member_customizable_inputType
 * @property string $nc_member_customizable_mandatory
 */
class MemberCustomizeMessages extends \Module
{

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'mod_nc_member_customize';


	/**
	 * Display a wildcard in the back end
	 *
	 * @return string
	 */
	public function generate()
	{
		if (TL_MODE == 'BE')
		{
			/** @var \BackendTemplate|object $objTemplate */
			$objTemplate = new \BackendTemplate('be_wildcard');

			$objTemplate->wildcard = '### ' . utf8_strtoupper($GLOBALS['TL_LANG']['FMD']['newsmenu'][0]) . ' ###';
			$objTemplate->title = $this->headline;
			$objTemplate->id = $this->id;
			$objTemplate->link = $this->name;
			$objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

			return $objTemplate->parse();
		}

		$this->nc_member_customizable_notifications = deserialize($this->nc_member_customizable_notifications);

		if (!FE_USER_LOGGED_IN || empty($this->nc_member_customizable_notifications))
		{
			return '';
		}

		return parent::generate();
	}


	/**
	 * Generate the module
	 */
	protected function compile()
	{
		/** @var Message|\Model\Collection $objMessages */
		/** @noinspection PhpUndefinedMethodInspection */
		$objMessages = Message::findBy(array('pid IN (' . implode(',', $this->nc_member_customizable_notifications) . ') AND member_customizable<>\'\''), array());
		$arrOptions = array();
		$arrSelected = array();

		while ($objMessages->next())
		{
			if (MemberMessages::memberHasSelected(\FrontendUser::getInstance()->id, $objMessages->id))
			{
				$arrSelected[$objMessages->pid][] = $objMessages->id;
			}

			// Fetch tokens for parsing the option labels
			$objNotification = $objMessages->getRelated('pid');
			$objGateway = $objMessages->getRelated('gateway');

			$arrTokens = array_merge
			(
			// Add message tokens with corresponding prefix
				array_combine
				(
					array_map(function ($key)
					{
						return 'message_' . $key;
					}, array_keys($objMessages->row())),
					$objMessages->row()
				),
				// Add notification tokens with corresponding prefix
				array_combine
				(
					array_map(function ($key)
					{
						return 'notification_' . $key;
					}, array_keys($objNotification->row())),
					$objNotification->row()
				),
				// Add gateway tokens with corresponding prefix
				array_combine
				(
					array_map(function ($key)
					{
						return 'gateway_' . $key;
					}, array_keys($objGateway->row())),
					$objGateway->row()
				)
			);

			$arrOptions[$objMessages->pid][$objMessages->id] = \StringUtil::parseSimpleTokens($this->nc_member_customizable_label ?: '##message_title## (##gateway_title##)', $arrTokens);
		}

		$objForm = new Form('tl_select_notifications', 'POST', function ($objHaste)
		{
			/** @noinspection PhpUndefinedMethodInspection */
			return \Input::post('FORM_SUBMIT') === $objHaste->getFormId();
		});

		foreach ($arrOptions as $k => $options)
		{
			/** @noinspection PhpUndefinedMethodInspection */
			$objForm->addFormField('notification_' . $k, array(
				'label'     => Notification::findByPk($objMessages->pid)->title,
				'inputType' => $this->nc_member_customizable_inputType,
				'options'   => $options,
				'eval'      => array('mandatory' => $this->nc_member_customizable_mandatory),
				'value'     => (!empty($arrSelected[$k])) ? $arrSelected[$k] : array()
			));

			// Add a validator
			// We check whether it is possible to send the message to the recipient by means of the gateway
			// E.g. a sms message requires a phone number set by the member which is not default
			$objForm->addValidator('notification_' . $k, function ($varValue, $objWidget, $objForm) use ($k, $arrOptions)
			{
				if (empty($varValue))
				{
					return $varValue;
				}

				foreach ($varValue as $msg)
				{
					/** @noinspection PhpUndefinedMethodInspection */
					/** @var Message|\Model $objMessage */
					$objMessage = Message::findByPk($msg);

					/** @noinspection PhpUndefinedMethodInspection */
					/** @var GatewayInterface|MessageDraftCheckSendInterface $objGateway */
					$objGateway = $objMessage->getRelated('gateway')->getGateway();

					if (!$objGateway instanceof MessageDraftCheckSendInterface)
					{
						continue;
					}

					// Throw the error message as exception if the method has not yet
					if (!$objGateway->canSendDraft($objMessage))
					{
						throw new \Exception(sprintf($GLOBALS['TL_LANG']['ERR']['messageNotSelectable'], $arrOptions[$k][$msg]));
					}
				}

				return $varValue;
			});
		}

		$objForm->addSubmitFormField('submit', $GLOBALS['TL_LANG']['MSC']['saveSettings']);

		// Process form submit
		if ($objForm->validate())
		{
			$arrData = $objForm->fetchAll();

			foreach ($arrData as $field => $notification)
			{
				if (strpos($field, 'notification_') !== 0)
				{
					continue;
				}

				list(, $notificationId) = trimsplit('_', $field);

				// Delete
				foreach (array_diff((array)$arrSelected[$notificationId], (array)$notification) as $msg)
				{
					/** @noinspection PhpUndefinedMethodInspection */
					MemberMessages::findByMemberAndMessage(\FrontendUser::getInstance()->id, $msg)->delete();
				}

				// Create
				foreach (array_diff((array)$notification, (array)$arrSelected[$notificationId]) as $msg)
				{
					/** @var MemberMessages|\Model $objMemberMessage */
					$objMemberMessage = new MemberMessages();
					$objMemberMessage->member_id = \FrontendUser::getInstance()->id;
					$objMemberMessage->message_id = $msg;
					$objMemberMessage->save();
				}
			}
		}

		$this->Template->form = $objForm->generate();
	}
}
