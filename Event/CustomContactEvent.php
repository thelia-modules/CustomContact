<?php

namespace CustomContact\Event;

use CustomContact\Model\CustomContact;

class CustomContactEvent
{
    const SUBMIT = 'custom_contact_event_submit_form';

    public function __construct(
        private CustomContact $customContact,
        private array $fields
    ) {
    }

    /**
     * @return CustomContact
     */
    public function getCustomContact(): CustomContact
    {
        return $this->customContact;
    }

    /**
     * @param CustomContact $customContact
     * @return CustomContactEvent
     */
    public function setCustomContact(CustomContact $customContact): CustomContactEvent
    {
        $this->customContact = $customContact;
        return $this;
    }

    /**
     * @return array
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * @param array $fields
     * @return CustomContactEvent
     */
    public function setFields(array $fields): CustomContactEvent
    {
        $this->fields = $fields;
        return $this;
    }
}