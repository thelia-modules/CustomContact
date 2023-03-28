<?php

namespace CustomContactForm\Form;

use CustomContactForm\CustomContactForm;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Thelia\Core\Translation\Translator;
use Thelia\Form\BaseForm;

class CustomContactFormForm extends BaseForm
{

    protected function buildForm()
    {
        $this->formBuilder
            ->add(
                'title',
                TextType::class,
                options: [
                    'required' => true,
                    'label' => Translator::getInstance()->trans('title', [], CustomContactForm::DOMAIN_NAME)
                ]
            )
            ->add(
                'code',
                TextType::class,
                options: [
                    'required' => true,
                    'label' => Translator::getInstance()->trans('code', [], CustomContactForm::DOMAIN_NAME)
                ]
            )
            ->add(
                'field_configuration',
                TextareaType::class,
                options: [
                    'required' => true,
                    'label' => Translator::getInstance()->trans('field_configuration', [], CustomContactForm::DOMAIN_NAME)
                ]
            )
            ->add(
                'receiver_email',
                TextType::class,
                options: [
                    'required' => true,
                    'label' => Translator::getInstance()->trans('receiver_email', [], CustomContactForm::DOMAIN_NAME)
                ]
            )
            ;
    }

    public static function getName()
    {
        return 'custom_contact_form_form';
    }
}