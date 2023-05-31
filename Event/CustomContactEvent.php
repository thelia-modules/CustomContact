<?php

namespace CustomContact\Event;

use CustomContact\Model\CustomContact;

class CustomContactEvent
{
    const SUBMIT = 'custom_contact_event_submit_form';

    public function __construct(
        private CustomContact $customContact,
        private array $fields,
        private array $fileFields
    ) {
    }

    public function getCustomContact(): CustomContact
    {
        return $this->customContact;
    }

    public function setCustomContact(CustomContact $customContact): CustomContactEvent
    {
        $this->customContact = $customContact;
        return $this;
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    public function setFields(array $fields): CustomContactEvent
    {
        $this->fields = $fields;
        return $this;
    }

    public function getFileFields(): array
    {
        return $this->fileFields;
    }

    public function setFileFields(array $fileFields): CustomContactEvent
    {
        $this->fileFields = $fileFields;
        return $this;
    }
}