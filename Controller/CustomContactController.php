<?php

namespace CustomContact\Controller;

use CustomContact\Event\CustomContactEvents;
use CustomContact\Model\CustomContactQuery;
use OpenApi\Model\Api\ModelFactory;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;
use Thelia\Controller\Front\BaseFrontController;
use Thelia\Mailer\MailerFactory;
use Thelia\Model\ConfigQuery;
use Thelia\Tools\URL;

#[Route('/custom_contact/{code}', name: 'front_custom_contact_')]
class CustomContactController extends BaseFrontController
{
    #[Route('', name:'view', methods: 'GET')]
    public function viewAction(RequestStack $requestStack, ModelFactory $modelFactory, $code)
    {
        $request = $requestStack->getCurrentRequest();

        $locale = $this->findLocale($request);

        $customContact = CustomContactQuery::create()
            ->filterByCode($code)
            ->useCustomContactI18nQuery()
            ->filterByLocale($locale)
            ->endUse()
            ->find();

        return $this->render('custom_contact', ['custom_contact' => $customContact]);
    }

    #[Route('', name:'send', methods: 'POST')]
    public function sendCustomContact(
        EventDispatcherInterface $dispatcher,
        MailerFactory $mailer,
        RequestStack $requestStack,
        $code
    ) {
        $event = new CustomContactEvents();
        $dispatcher->dispatch($event, CustomContactEvents::CUSTOM_CONTACT_EVENT);

        $storeEmail = ConfigQuery::getStoreEmail();

        $customContactForm = CustomContactQuery::create()
            ->filterByCode($code)
            ->findOne();

        $mailer->sendEmailMessage(
            'mail_custom_contact',
            [$storeEmail => ConfigQuery::getStoreName()],
            [$customContactForm->getEmail() => "receiver_email"],
            [
                'receiver_email' => $customContactForm->getEmail(),
                'store_name' => ConfigQuery::getStoreName(),
                'response' => $requestStack->getCurrentRequest()->request->all()
            ]
        );
    }

    protected function findLocale(Request $request)
    {
        $locale = $request->get('locale');

        if (null == $locale) {
            $locale = $request->getSession()->getLang()->getLocale();
        }

        return $locale;
    }
}