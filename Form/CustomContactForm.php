<?php

namespace CustomContact\Form;

use CustomContact\CustomContact;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Thelia\Core\Translation\Translator;
use Thelia\Form\BaseForm;

class CustomContactForm extends BaseForm
{

    protected function buildForm()
    {
        $this->formBuilder
            ->add(
                'title',
                TextType::class,
                options: [
                    'required' => true,
                    'label' => Translator::getInstance()->trans('Title', [], CustomContact::DOMAIN_NAME)
                ]
            )
            ->add(
                'code',
                TextType::class,
                options: [
                    'required' => true,
                    'label' => Translator::getInstance()->trans('Code', [], CustomContact::DOMAIN_NAME)
                ]
            )
            ->add(
                'field_configuration',
                TextareaType::class,
                options: [
                    'required' => true,
                    'label' => Translator::getInstance()->trans('Field configuration', [], CustomContact::DOMAIN_NAME)
                ]
            )
            ->add(
                'receiver_email',
                TextType::class,
                options: [
                    'required' => true,
                    'label' => Translator::getInstance()->trans('Receiver email', [], CustomContact::DOMAIN_NAME)
                ]
            )
            ->add(
                'return_url',
                TextType::class,
                options: [
                    'required' => false,
                    'label' => Translator::getInstance()->trans('Return url', [], CustomContact::DOMAIN_NAME)
                ]
            )
            ;
    }
}