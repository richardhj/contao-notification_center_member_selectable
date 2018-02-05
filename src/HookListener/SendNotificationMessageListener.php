<?php

/**
 * This file is part of richardhj/contao-ferienpass.
 *
 * Copyright (c) 2015-2018 Richard Henkenjohann
 *
 * @package   richardhj/contao-ferienpass
 * @author    Richard Henkenjohann <richardhenkenjohann@googlemail.com>
 * @copyright 2015-2018 Richard Henkenjohann
 * @license   https://github.com/richardhj/contao-ferienpass/blob/master/LICENSE
 */

namespace Richardhj\NotificationCenterMembersChoiceBundle\HookListener;


use Contao\Model;
use NotificationCenter\Model\Message;
use Richardhj\NotificationCenterMembersChoiceBundle\Model\MemberMessages;

class SendNotificationMessageListener
{

    /**
     * @param Message|Model $message
     * @param array   $tokens
     *
     * @return bool
     */
    public function onSendNotificationMessage(Message $message, array $tokens)
    {
        $memberId = $tokens['member_id'];

        if (!$memberId) {
            return true;
        }

        // Message is member customizable but was not opt-in by the member
        if ($message->member_customizable && !MemberMessages::shouldSendMessage($memberId, $message->id)) {
            return false;
        }

        return true;
    }
}
