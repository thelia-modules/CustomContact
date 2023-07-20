<?php

namespace CustomContact\EventListener;

use CustomContact\CustomContact;
use CustomContact\Event\CustomContactEvent;
use CustomContact\Model\CustomContactQuery;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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
        $customContact = $event->getCustomContact();

        $destinationEmails = array_map('trim', explode(',', $customContact->getEmail()));

        if (empty($destinationEmails)) {
            $destinationEmails = [ConfigQuery::getStoreEmail()];
        }

        foreach ($destinationEmails as $emailAddress) {
            $email = $this->mailer->createEmailMessage(
                CustomContact::MAIL_CUSTOM_CONTACT,
                [ConfigQuery::getStoreEmail() => $event->getCustomContact()->getTitle()],
                [$emailAddress => ConfigQuery::getStoreName()],
                [
                    'form_title' => $customContact->getTitle(),
                    'store_name' => ConfigQuery::getStoreName(),
                    'fields' => $event->getFields()
                ]
            );

            foreach ($event->getFileFields() as $fileField) {
                /** @var UploadedFile $file */
                foreach ($fileField as $file) {
                    $email->attach($file->getContent(), $file->getClientOriginalName());
                }
            }

            // Override subject
            $email->subject($customContact->getTitle());

            $this->mailer->send($email);
        }
    }
}
