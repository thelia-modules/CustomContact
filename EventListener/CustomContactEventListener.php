<?php

namespace CustomContact\EventListener;

use CustomContact\CustomContact;
use CustomContact\Event\CustomContactEvent;
use CustomContact\Model\CustomContactQuery;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Thelia\Core\Event\ActionEvent;
use Thelia\Mailer\MailerFactory;
use Thelia\Model\ConfigQuery;

class CustomContactEventListener implements EventSubscriberInterface
{
    const SUBMIT = 'custom_contact_event_submit_form';

    public function __construct(
        protected MailerFactory $mailer
    ) {
    }

    public static function getSubscribedEvents()
    {
        return [
            CustomContactEvent::SUBMIT => ['sendCustomerContact', 128]
        ];
    }

    public function sendCustomerContact(CustomContactEvent $event)
    {
        $storeEmail = ConfigQuery::getStoreEmail();

        $this->mailer->sendEmailMessage(
            CustomContact::MAIL_CUSTOM_CONTACT,
            [$storeEmail => ConfigQuery::getStoreName()],
            [$event->getCustomContact()->getEmail() => ConfigQuery::getStoreName()],
            [
                'store_name' => ConfigQuery::getStoreName(),
                'fields' => $event->getFields()
            ]
        );
    }
}