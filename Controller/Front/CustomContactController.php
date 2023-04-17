<?php

namespace CustomContact\Controller\Front;

use CustomContact\Event\CustomContactEvent;
use CustomContact\Model\CustomContactQuery;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Thelia\Controller\Front\BaseFrontController;

#[Route('/custom_contact/{code}', name: 'front_custom_contact_', requirements: ['code' => Requirement::ASCII_SLUG])]
class CustomContactController extends BaseFrontController
{
    #[Route('', name:'view', methods: 'GET')]
    public function viewAction(RequestStack $requestStack, string $code): Response
    {
        $request = $requestStack->getCurrentRequest();

        $locale = $this->findLocale($request);

        $customContact = CustomContactQuery::create()
            ->filterByCode($code)
            ->useCustomContactI18nQuery()
            ->filterByLocale($locale)
            ->endUse()
            ->find();

        return $this->render('custom_contact/form',
            [
                'custom_contact' => $customContact,
                'code' => $code
            ]);
    }

    #[Route('', name:'send', methods: 'POST')]
    public function sendCustomContact(
        EventDispatcherInterface $dispatcher,
        RequestStack $requestStack,
        string $code
    ) {
        $request = $requestStack->getCurrentRequest();

        $locale = $this->findLocale($request);

        $customContactForm = CustomContactQuery::create()
            ->filterByCode($code)
            ->findOne();

        if (!$customContactForm){
            throw new NotFoundHttpException();
        }

        $customContactForm->setLocale($locale);

        $dispatcher
            ->dispatch(
                new CustomContactEvent(
                    $customContactForm,
                    $requestStack->getCurrentRequest()->request->all()
                ),
                CustomContactEvent::SUBMIT
            );
        if ($customContactForm->getReturnUrl()){
            return new RedirectResponse($customContactForm->getReturnUrl());
        }
        return new RedirectResponse("/custom_contact/".$code."/success");
    }

    #[Route('/success', name:'success', methods: 'GET')]
    public function success(string $code): Response
    {
        return $this->render('custom_contact/success',
            [
                'code' => $code
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