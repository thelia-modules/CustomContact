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
                    'label' => Translator::getInstance()->trans('title', [], CustomContact::DOMAIN_NAME)
                ]
            )
            ->add(
                'code',
                TextType::class,
                options: [
                    'required' => true,
                    'label' => Translator::getInstance()->trans('code', [], CustomContact::DOMAIN_NAME)
                ]
            )
            ->add(
                'field_configuration',
                TextareaType::class,
                options: [
                    'required' => true,
                    'label' => Translator::getInstance()->trans('field_configuration', [], CustomContact::DOMAIN_NAME)
                ]
            )
            ->add(
                'receiver_email',
                TextType::class,
                options: [
                    'required' => true,
                    'label' => Translator::getInstance()->trans('receiver_email', [], CustomContact::DOMAIN_NAME)
                ]
            )
            ->add(
                'return_url',
                TextType::class,
                options: [
                    'required' => false,
                    'label' => Translator::getInstance()->trans('return_url', [], CustomContact::DOMAIN_NAME)
                ]
            )
            ;
    }
}