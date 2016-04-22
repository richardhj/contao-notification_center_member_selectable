<?php
/**
 * Member selectable messages for the notification_center extension for Contao Open Source CMS
 *
 * Copyright (c) 2016 Richard Henkenjohann
 *
 * @package NotificationCenterMemberSelectable
 * @author  Richard Henkenjohann <richardhenkenjohann@googlemail.com>
 */

namespace NotificationCenter\Util;


use MatthiasMullie\Minify\Exception;
use NotificationCenter\Model\Gateway;
use NotificationCenter\Model\MemberMessages;
use NotificationCenter\Model\Message;
use NotificationCenter\Model\Notification;


/**
 * Class MemberCustomizableHelper
 * @package NotificationCenter\Util
 */
class MemberCustomizableHelper
{

	/**
	 * Get all notifications with member customizable messages
	 * @category options_callback
	 *
	 * @return array
	 */
	public function getMessages()
	{
		$arrOptions = array();

		$objNotifications = \Database::getInstance()->query(<<<SQL
SELECT n.id, n.title
FROM tl_nc_notification n
INNER JOIN tl_nc_message m
  ON n.id=m.pid
INNER JOIN tl_nc_gateway g
  ON g.id=m.gateway
WHERE m.member_customizable<>''
SQL
		);

		while ($objNotifications->next())
		{
			$arrOptions[$objNotifications->id] = $objNotifications->title;
		}

		return $arrOptions;
	}


	/**
	 * Check if the message can be member customizable
	 * @category save_callback
	 *
	 * @param mixed          $varValue
	 * @param \DataContainer $dc
	 *
	 * @return mixed
	 * @throws Exception
	 */
	public function checkMessageMemberCustomizable($varValue, $dc)
	{
		/** @noinspection PhpUndefinedMethodInspection */
		$objNotification = Notification::findByPk($dc->activeRecord->pid);
		$strNotificationType = Notification::findGroupForType($objNotification->type);

		// Check the allowed tokens corresponding for this notification type
		foreach ($GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE'][$strNotificationType][$objNotification->type] as $field => $tokens)
		{
			// We have to check whether the member id will be passed to the message as token
			if (in_array('member_id', $tokens) || in_array('member_*', $tokens))
			{
				return $varValue;
			}
		}

		throw new Exception($GLOBALS['TL_LANG']['ERR']['messageNotMemberCustomizable']);
	}


	/**
	 * Skip messages that are member customizable and were not selected by the member
	 *
	 * @param Message|\Model $objMessage
	 * @param array          $arrTokens
	 * @param string         $strLanguage
	 * @param Gateway|\Model $objGateway
	 *
	 * @return bool
	 */
	public function skipUnselectedMessages($objMessage, $arrTokens, $strLanguage, $objGateway)
	{
		$intMemberId = $arrTokens['member_id'];

		if (!$intMemberId)
		{
			return true;
		}

		// User did not customize their messages for this notification
		if (\Database::getInstance()->prepare(<<<SQL
		SELECT n.id
FROM tl_nc_member_messages mm
INNER JOIN tl_nc_message m
  ON mm.message_id = m.id
INNER JOIN tl_nc_notification n
  ON m.pid = n.id
WHERE mm.member_id=?
  AND m.member_customizable<>''
SQL
			)
				->execute($intMemberId)
				->numRows < 1
		)
		{
			return true;
		}

		// Message is member customizable but was not selected by the member
		if ($objMessage->member_customizable && !MemberMessages::memberHasSelected($intMemberId, $objMessage->id))
		{
			return false;
		}

		return true;
	}
}
