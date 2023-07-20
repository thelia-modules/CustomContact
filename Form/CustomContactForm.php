<?php

namespace CustomContact\Form;

use CustomContact\CustomContact;
use CustomContact\Model\CustomContactQuery;
use Propel\Runtime\ActiveQuery\Criteria;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Thelia\Core\Translation\Translator;
use Thelia\Form\BaseForm;

class CustomContactForm extends BaseForm
{

    protected function buildForm()
    {
        $this->formBuilder
            ->add(
                'id',
                HiddenType::class,
                options: [
                    'label' => Translator::getInstance()->trans('The form ID', [], CustomContact::DOMAIN_NAME)
                ]
            )
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
                    'constraints' => [
                        new Regex('/^[A-Za-z0-9_-]+$/', $this->translator->trans('Les caractères autorisés sont les lettres, les chiffres le signe - et le signe _', [], CustomContact::DOMAIN_NAME)),
                        new Callback([$this, 'checkUnicity'])
                    ],
                    'label' => Translator::getInstance()->trans('Code', [], CustomContact::DOMAIN_NAME),
                    'label_attr' => [
                        'help' => $this->translator->trans('Les caractères autorisés sont les lettres, les chiffres le signe - et le signe _', [], CustomContact::DOMAIN_NAME),
                    ],
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
                    'label' => Translator::getInstance()->trans('Receiver emails', [], CustomContact::DOMAIN_NAME),
                    'label_attr' => [
                        'help' => $this->translator->trans('Vous pouvez indiquer une ou plusieurs adresses email séparé&es par des virgules', [], CustomContact::DOMAIN_NAME),
                    ],
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
            ->add(
                'success_message',
                TextareaType::class,
                options: [
                    'required' => false,
                    'label' => Translator::getInstance()->trans('Success message', [], CustomContact::DOMAIN_NAME)
                ]
            )
            ;
    }

    public function checkUnicity($value, ExecutionContextInterface $context)
    {
        $formId = (int) $context->getRoot()->getData()['id'];

        $query = CustomContactQuery::create()
            ->filterByCode($value)
            ->filterById($formId, Criteria::NOT_EQUAL)
        ;

        if ($query->count() > 0) {
            $context->addViolation(
                Translator::getInstance()->trans(
                    "A form with code %code already exists, please une another code.",
                    [ "%code" => $value ],
                    CustomContact::DOMAIN_NAME
                )
            );
        }
    }
}
