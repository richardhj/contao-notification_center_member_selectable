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


use Contao\System;
use Doctrine\DBAL\Connection;
use NotificationCenter\Model\Notification;
use Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;


/**
 * Class MemberCustomizableHelper
 *
 * @package NotificationCenter\Util
 */
class MemberCustomizableHelper
{

    /**
     * @var Connection
     */
    private $connection;

    /**
     * MemberCustomizableHelper constructor.
     *
     * @throws ServiceNotFoundException
     * @throws ServiceCircularReferenceException
     */
    public function __construct()
    {
        $this->connection = System::getContainer()->get('database_connection');
    }

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

        $statement = $this->connection->createQueryBuilder()
            ->select('n.id', 'n.title')
            ->from('tl_nc_notification', 'n')
            ->innerJoin('n', 'tl_nc_message', 'm', 'n.id=m.pid')
            ->innerJoin('m', 'tl_nc_gateway', 'g', 'g.id=m.gateway')
            ->where("m.member_customizable<>''")
            ->execute();

        while ($row = $statement->fetch(\PDO::FETCH_OBJ)) {
            $options[$row->id] = $row->title;
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
        if (!$value) {
            return $value;
        }

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
