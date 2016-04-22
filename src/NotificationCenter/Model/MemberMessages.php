<?php
/**
 * Member selectable messages for the notification_center extension for Contao Open Source CMS
 *
 * Copyright (c) 2016 Richard Henkenjohann
 *
 * @package NotificationCenterMemberSelectable
 * @author  Richard Henkenjohann <richardhenkenjohann@googlemail.com>
 */

namespace NotificationCenter\Model;


/**
 * Class MemberMessages
 * @package NotificationCenter\Model
 */
class MemberMessages extends \Model
{

	/**
	 * Name of the current table
	 * @var string
	 */
	protected static $strTable = 'tl_nc_member_messages';


	/**
	 * Find by member
	 * 
	 * @param integer $intMemberId
	 *
	 * @return static|null
	 */
	public static function findByMember($intMemberId)
	{
		/** @noinspection PhpUndefinedMethodInspection */
		return static::findBy('member_id', $intMemberId);
	}


	/**
	 * Find by member and message
	 * 
	 * @param integer $intMemberId
	 * @param integer $intMessageId
	 *
	 * @return static|null
	 */
	public static function findByMemberAndMessage($intMemberId, $intMessageId)
	{
		/** @noinspection PhpUndefinedMethodInspection */
		return static::findBy(array('member_id=? AND message_id=?'), array($intMemberId, $intMessageId));
	}


	/**
	 * Check if the member has selected the message
	 * 
	 * @param integer $intMemberId
	 * @param integer $intMessageId
	 *
	 * @return boolean
	 */
	public static function memberHasSelected($intMemberId, $intMessageId)
	{
		/** @noinspection PhpUndefinedMethodInspection */
		return static::countBy(array('member_id=? AND message_id=?'), array($intMemberId, $intMessageId)) ? true : false;
	}
}
