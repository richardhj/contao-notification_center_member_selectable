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
     * @throws \Exception
     */
    public function checkMessageMemberCustomizable($value, $dc)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $notification     = Notification::findByPk($dc->activeRecord->pid);
        $notificationType = Notification::findGroupForType($notification->type);

        // Check the allowed tokens corresponding for this notification type
        foreach (
            $GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE'][$notificationType][$notification->type]
            as $field => $tokens
        ) {
            // We have to check whether the member id will be passed to the message as token
            if (in_array('member_id', $tokens) || in_array('member_*', $tokens)) {
                return $value;
            }
        }

        throw new \Exception($GLOBALS['TL_LANG']['ERR']['messageNotMemberCustomizable']);
    }


    /**
     * Skip messages that are member customizable and were not selected by the member
     *
     * @param Message|\Model $message
     * @param array          $tokens
     *
     * @return bool
     *
     * @internal param string $language
     * @internal param \Model|Gateway $gateway
     */
    public function skipUnselectedMessages($message, $tokens)
    {
        $memberId = $tokens['member_id'];

        if (!$memberId) {
            return true;
        }

        // User did not customize their messages for this notification
        if (\Database::getInstance()
                ->prepare(
                    <<<SQL
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
                ->execute($memberId)
                ->numRows < 1
        ) {
            return true;
        }

        // Message is member customizable but was not selected by the member
        if ($message->member_customizable && !MemberMessages::memberHasSelected($memberId, $message->id)) {
            return false;
        }

        return true;
    }
}
