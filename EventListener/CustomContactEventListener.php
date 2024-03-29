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

        $fields = json_decode($event->getCustomContact()->getFieldConfiguration(), true, 512, JSON_THROW_ON_ERROR);

        // check if the custom contact form has a select contact field
        $hasSelectListContact = false;
        $labelSelectListContact = '';
        $selectedContact = false;

        // we verify the select contact exists
        foreach ($fields as $field) {
            if ($field['type'] === 'select-contact') {
                $hasSelectListContact = true;
                $labelSelectListContact = $field['label'];
                break;
            }
        }

        // and we verify the user selected a value in this select
        if ($hasSelectListContact !== false) {
            foreach ($event->getFields() as $key => $field) {
                if ($labelSelectListContact === $key && filter_var($field, FILTER_VALIDATE_EMAIL)) {
                    $selectedContact = $field;
                    break;
                }
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
