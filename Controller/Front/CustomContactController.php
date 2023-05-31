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
        $customContact = CustomContactQuery::create()
            ->filterByCode($code)
            ->useCustomContactI18nQuery()
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
        $customContactForm = CustomContactQuery::create()
            ->filterByCode($code)
            ->findOne();

        if (!$customContactForm){
            throw new NotFoundHttpException();
        }

        $dispatcher
            ->dispatch(
                new CustomContactEvent(
                    $customContactForm,
                    array_combine(
                        array_map(fn($k) => str_replace('_', ' ', $k), array_keys($requestStack->getCurrentRequest()->request->all())),
                        $requestStack->getCurrentRequest()->request->all()
                    ),
                    $requestStack->getCurrentRequest()->files->all()
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
        $customContactForm = CustomContactQuery::create()
            ->filterByCode($code)
            ->findOne();

        return $this->render('custom_contact/success',
            [
                'code' => $code,
                'successMessage' => $customContactForm->getSuccessMessage()
            ]
        );
    }
}