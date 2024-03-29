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

    const CONTACT_LIST = [
        "question" => "christine.chalat@billaudot.com",
        "note" => "emmanuel.gaultier@billaudot.com",
        "assistance" => "christine.chalat@billaudot.com",
        "delete" => "lucie.barthod@billaudot.com",
        "audio" => "christine.chalat@billaudot.com",
        "other" => "contact@billaudot.com"
    ];

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

        $fields = json_decode($event->getCustomContact()->getFieldConfiguration(), true, 512, JSON_THROW_ON_ERROR);

        // check if the custom contact form has a select contact field
        $hasSelectListContact = false;
        $selectedContact = false;

        // we verify the select contact exists
        foreach ($fields as $field) {
            if ($field['type'] === 'select-contact') {
                $hasSelectListContact = true;
                break;
            }
        }

        // and we verify the user selected a value in this select
        foreach ($event->getFields() as $field) {
            if (array_key_exists($field, self::CONTACT_LIST)) {
                $selectedContact = self::CONTACT_LIST[$field];
                break;
            }
        }

        if ($hasSelectListContact !== false && $selectedContact !== false) {
            $this->sendEmail($selectedContact, $customContact->getTitle(), $event);
            return;
        }

        if (empty($destinationEmails)) {
            $destinationEmails = [ConfigQuery::getStoreEmail()];
        }

        foreach ($destinationEmails as $emailAddress) {
            $this->sendEmail($emailAddress, $customContact->getTitle(), $event);
        }
    }


    protected function sendEmail(string $email, string $title, CustomContactEvent $event) {
        $emailService = $this->mailer->createEmailMessage(
            CustomContact::MAIL_CUSTOM_CONTACT,
            [ConfigQuery::getStoreEmail() => $event->getCustomContact()->getTitle()],
            [$email => ConfigQuery::getStoreName()],
            [
                'form_title' => $title,
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
        $emailService->subject($title);

        $this->mailer->send($emailService);
    }
}
