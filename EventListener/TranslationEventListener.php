<?php

namespace CustomContact\EventListener;

use Billaudot\Model\BillaudotAreaOfInterestQuery;
use CustomContact\CustomContact;
use CustomContact\Model\CustomContactQuery;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Core\Event\Translation\TranslationEvent;
use Thelia\Core\Translation\Translator;

class TranslationEventListener implements EventSubscriberInterface
{
    public function getTranslatableStrings(TranslationEvent $event): void
    {
        if ($event->getDomain() === CustomContact::DOMAIN_NAME) {
            $forms = CustomContactQuery::create()
                ->find();

            foreach ($forms as $form) {
                $fields = json_decode($form->getFieldConfiguration(), true);
                foreach ($fields as $field) {
                    $label = $field['label'];
                    $hash = md5($label);

                    if (isset($strings[$hash])) {
                        continue;
                    }

                    $strings[$hash] = [
                        'files' => ["Fields"],
                        'text' => $label,
                        'translation' => Translator::getInstance()->trans(
                            $label,
                            [],
                            $event->getDomain(),
                            $event->getLocale(),
                            false,
                            false
                        ),
                        'custom_fallback' => "",
                        'global_fallback' => "",
                        'dollar' => false,
                    ];
                }

                $formTitle = $form->getTitle();
                $titleHash = md5($formTitle);

                if (!isset($strings[$titleHash])) {
                    $strings[$titleHash] = [
                        'files' => ["Form titles"],
                        'text' => $formTitle,
                        'translation' => Translator::getInstance()->trans(
                            $formTitle,
                            [],
                            $event->getDomain(),
                            $event->getLocale(),
                            false,
                            false
                        ),
                        'custom_fallback' => "",
                        'global_fallback' => "",
                        'dollar' => false,
                    ];
                }

                $formSuccessMessage = $form->getSuccessMessage();
                $successMessageHash = md5($formSuccessMessage);

                if (!empty($formSuccessMessage) && !isset($strings[$successMessageHash])) {
                    $strings[$successMessageHash] = [
                        'files' => ["Form success message"],
                        'text' => $formSuccessMessage,
                        'translation' => Translator::getInstance()->trans(
                            $formSuccessMessage,
                            [],
                            $event->getDomain(),
                            $event->getLocale(),
                            false,
                            false
                        ),
                        'custom_fallback' => "",
                        'global_fallback' => "",
                        'dollar' => false,
                    ];
                }
            }

            $event->setTranslatableStrings($strings);
            $event->setTranslatableStringCount(count($strings));
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            TheliaEvents::TRANSLATION_GET_STRINGS => ['getTranslatableStrings', 0]
        ];
    }
}