<?php

namespace CustomContact\Controller;

use CustomContact\Event\CustomContactEvent;
use CustomContact\Model\CustomContactQuery;
use OpenApi\Model\Api\ModelFactory;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Thelia\Controller\Front\BaseFrontController;

#[Route('/custom_contact/{code}', name: 'front_custom_contact_')]
class CustomContactController extends BaseFrontController
{
    #[Route('', name:'view', methods: 'GET')]
    public function viewAction(RequestStack $requestStack, $code): Response
    {
        $request = $requestStack->getCurrentRequest();

        $locale = $this->findLocale($request);

        $customContact = CustomContactQuery::create()
            ->filterByCode($code)
            ->useCustomContactI18nQuery()
            ->filterByLocale($locale)
            ->endUse()
            ->find();

        return $this->render('custom_contact',
            [
                'custom_contact' => $customContact,
                'code' => $code
            ]);
    }

    #[Route('', name:'send', methods: 'POST')]
    public function sendCustomContact(
        EventDispatcherInterface $dispatcher,
        RequestStack $requestStack,
        $code
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
        return new RedirectResponse("/default_success");
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