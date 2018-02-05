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

namespace Richardhj\NotificationCenterMembersChoiceBundle\Module;

use Contao\FrontendUser;
use Contao\Input;
use Haste\Form\Form;
use NotificationCenter\Gateway\GatewayInterface;
use NotificationCenter\Gateway\MessageDraftCheckSendInterface;
use NotificationCenter\Model\Message;
use NotificationCenter\Model\Notification;
use Richardhj\NotificationCenterMembersChoiceBundle\Model\MemberMessages;


/**
 * Class MemberCustomizeMessages
 *
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
     *
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
        if ('BE' === TL_MODE) {
            /** @var \BackendTemplate|object $objTemplate */
            $objTemplate = new \BackendTemplate('be_wildcard');

            $objTemplate->wildcard = '### '.utf8_strtoupper($GLOBALS['TL_LANG']['FMD']['newsmenu'][0]).' ###';
            $objTemplate->title    = $this->headline;
            $objTemplate->id       = $this->id;
            $objTemplate->link     = $this->name;
            $objTemplate->href     = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id='.$this->id;

            return $objTemplate->parse();
        }

        $this->nc_member_customizable_notifications = deserialize($this->nc_member_customizable_notifications);

        if (!FE_USER_LOGGED_IN || empty($this->nc_member_customizable_notifications)) {
            return '';
        }

        return parent::generate();
    }

    /**
     * Generate the module
     *
     * @throws \LogicException When a gateway occurs errors sending a message. Is thrown within the validator and will
     *                         be catched and shown as error message.
     * @throws \RuntimeException When message is not selectable. Is thrown within the validator and will be catched and
     *                           shown as error message.
     */
    protected function compile()
    {
        /** @var Message|\Model\Collection $messages */
        /** @noinspection PhpUndefinedMethodInspection */
        $messages = Message::findBy(
            ['pid IN ('.implode(',', $this->nc_member_customizable_notifications).') AND member_customizable<>\'\''],
            []
        );
        $memberId = FrontendUser::getInstance()->id;
        $options  = [];
        $selected = [];

        while ($messages->next()) {
            if (MemberMessages::shouldSendMessage($memberId, $messages->id)) {
                $selected[$messages->pid][] = $messages->id;
            }

            // Fetch tokens for parsing the option labels
            $notification = $messages->getRelated('pid');
            $gateway      = $messages->getRelated('gateway');

            $tokens = array_merge(
            // Add message tokens with corresponding prefix
                array_combine(
                    array_map(
                        function ($key) {
                            return 'message_'.$key;
                        },
                        array_keys($messages->row())
                    ),
                    $messages->row()
                ),
                // Add notification tokens with corresponding prefix
                array_combine(
                    array_map(
                        function ($key) {
                            return 'notification_'.$key;
                        },
                        array_keys($notification->row())
                    ),
                    $notification->row()
                ),
                // Add gateway tokens with corresponding prefix
                array_combine(
                    array_map(
                        function ($key) {
                            return 'gateway_'.$key;
                        },
                        array_keys($gateway->row())
                    ),
                    $gateway->row()
                )
            );

            try {
                $options[$messages->pid][$messages->id] = \StringUtil::parseSimpleTokens(
                    $this->nc_member_customizable_label ?: '##message_title## (##gateway_title##)',
                    $tokens
                );
            } catch (\Exception $e) {
                $options[$messages->pid][$messages->id] = $this->nc_member_customizable_label;
            }
        }

        $form = new Form(
            'tl_select_notifications', 'POST', function ($haste) {
            /** @noinspection PhpUndefinedMethodInspection */
            return $haste->getFormId() === Input::post('FORM_SUBMIT');
        }
        );

        foreach ($options as $nId => $messagesOptions) {
            /** @noinspection PhpUndefinedMethodInspection */
            $form->addFormField(
                'notification_'.$nId,
                [
                    'label'     => Notification::findByPk($nId)->title,
                    'inputType' => $this->nc_member_customizable_inputType,
                    'options'   => $messagesOptions,
                    'eval'      => [
                        'mandatory' => $this->nc_member_customizable_mandatory,
                    ],
                    'value'     => !empty($selected[$nId]) ? $selected[$nId] : [],
                ]
            );

            // Add a validator
            // We check whether it is possible to send the message to the recipient by means of the gateway
            // E.g. a sms message requires a phone number set by the member which is not default
            $form->addValidator(
                'notification_'.$nId,
                function ($value) use ($nId, $options) {
                    if (empty($value)) {
                        return $value;
                    }

                    foreach ((array)$value as $msg) {
                        /** @noinspection PhpUndefinedMethodInspection */
                        /** @var Message|\Model $message */
                        $message = Message::findByPk($msg);

                        /** @noinspection PhpUndefinedMethodInspection */
                        /** @var GatewayInterface|MessageDraftCheckSendInterface $gateway */
                        $gateway = $message->getRelated('gateway')->getGateway();

                        if (!$gateway instanceof MessageDraftCheckSendInterface) {
                            continue;
                        }

                        // Throw the error message as exception if the method has not yet
                        if (!$gateway->canSendDraft($message)) {
                            throw new \RuntimeException(
                                sprintf($GLOBALS['TL_LANG']['ERR']['messageNotSelectable'], $options[$nId][$msg])
                            );
                        }
                    }

                    return $value;
                }
            );
        }

        $form->addSubmitFormField('submit', $GLOBALS['TL_LANG']['MSC']['saveSettings']);

        // Process form submit
        if ($form->validate()) {
            $data = $form->fetchAll();

            foreach ($data as $field => $notificationMessages) {
                if (0 !== strpos($field, 'notification_')) {
                    continue;
                }

                list(, $notificationId) = trimsplit('_', $field);

                $allNotificationMessages = array_keys($options[$notificationId]);

                // Should send
                foreach ((array)$notificationMessages as $msg) {
                    $this->persistShouldSendMessage($memberId, $msg, true);
                }
                // Should not send
                foreach (array_diff((array)$allNotificationMessages, (array)$notificationMessages) as $msg) {
                    $this->persistShouldSendMessage($memberId, $msg, false);
                }
            }
        }

        $this->Template->form = $form->generate();
    }

    /**
     * @param int  $memberId
     * @param int  $messageId
     * @param bool $send
     */
    private function persistShouldSendMessage($memberId, $messageId, $send)
    {
        \Database::getInstance()
            ->prepare(
                'INSERT INTO tl_nc_member_messages (member_id, message_id, send) '.
                ' VALUES (?, ?, ?)'.
                ' ON DUPLICATE KEY UPDATE send=?'
            )
            ->execute($memberId, $messageId, $send, $send);
    }
}
