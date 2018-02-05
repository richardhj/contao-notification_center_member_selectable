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

namespace Richardhj\NotificationCenterMembersChoiceBundle\Model;

use Contao\Model;
use NotificationCenter\Model\Message;


/**
 * Class MemberMessages
 *
 * @property int   $member_id
 * @property int   $message_id
 * @property mixed $send
 *
 * @package NotificationCenter\Model
 */
class MemberMessages extends Model
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
        return static::findOneBy(['member_id=? AND message_id=?'], [$memberId, $messageId]);
    }

    /**
     * Check if the member chose to receive this message
     *
     * @param integer $memberId
     * @param integer $messageId
     *
     * @return boolean
     */
    public static function shouldSendMessage($memberId, $messageId)
    {
        $model = static::findByMemberAndMessage($memberId, $messageId);
        if (null !== $model) {
            return (bool)$model->send;
        }

        /** @noinspection PhpUndefinedMethodInspection */
        $message = Message::findByPk($messageId);
        if (null === $message) {
            return null;
        }

        return 'opt-out' === $message->member_customizable_default_behavior;
    }
}
