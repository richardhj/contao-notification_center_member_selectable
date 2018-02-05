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

namespace Richardhj\NotificationCenterMembersChoiceBundle\Util;


use NotificationCenter\Model\Gateway;
use NotificationCenter\Model\MemberMessages;
use NotificationCenter\Model\Message;
use NotificationCenter\Model\Notification;


/**
 * Class MemberCustomizableHelper
 *
 * @package NotificationCenter\Util
 */
class MemberCustomizableHelper
{

    /**
     * Get all notifications with member customizable messages
     *
     * @category options_callback
     *
     * @return array
     */
    public function getMessages()
    {
        $options = [];

        $notifications = \Database::getInstance()
            ->query(
                <<<SQL
                SELECT n.id, n.title
FROM tl_nc_notification n
INNER JOIN tl_nc_message m
  ON n.id=m.pid
INNER JOIN tl_nc_gateway g
  ON g.id=m.gateway
WHERE m.member_customizable<>''
SQL
            );

        while ($notifications->next()) {
            $options[$notifications->id] = $notifications->title;
        }

        return $options;
    }


    /**
     * Check if the message can be member customizable
     *
     * @category save_callback
     *
     * @param mixed          $value
     * @param \DataContainer $dc
     *
     * @return mixed
     * @throws \RuntimeException
     */
    public function checkMessageMemberCustomizable($value, $dc)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $notification     = Notification::findByPk($dc->activeRecord->pid);
        $notificationType = Notification::findGroupForType($notification->type);

        // Check the allowed tokens corresponding for this notification type
        foreach (
            (array)$GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE'][$notificationType][$notification->type]
            as $field => $tokens
        ) {
            // We have to check whether the member id will be passed to the message as token
            if (\in_array('member_id', $tokens, true) || \in_array('member_*', $tokens, true)) {
                return $value;
            }
        }

        throw new \RuntimeException($GLOBALS['TL_LANG']['ERR']['messageNotMemberCustomizable']);
    }
}
