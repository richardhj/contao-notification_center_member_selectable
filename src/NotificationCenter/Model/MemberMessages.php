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
 *
 * @property int $member_id
 * @property int $message_id
 *
 * @package NotificationCenter\Model
 */
class MemberMessages extends \Model
{

    /**
     * Name of the current table
     *
     * @var string
     */
    protected static $strTable = 'tl_nc_member_messages';


    /**
     * Find by member
     *
     * @param integer $memberId
     *
     * @return static|null
     */
    public static function findByMember($memberId)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        return static::findBy('member_id', $memberId);
    }


    /**
     * Find by member and message
     *
     * @param integer $memberId
     * @param integer $messageId
     *
     * @return static|null
     */
    public static function findByMemberAndMessage($memberId, $messageId)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        return static::findBy(['member_id=? AND message_id=?'], [$memberId, $messageId]);
    }


    /**
     * Check if the member has selected the message
     *
     * @param integer $memberId
     * @param integer $messageId
     *
     * @return boolean
     */
    public static function memberHasSelected($memberId, $messageId)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        return static::countBy(
            ['member_id=? AND message_id=?'],
            [$memberId, $messageId]
        ) ? true : false;
    }
}
