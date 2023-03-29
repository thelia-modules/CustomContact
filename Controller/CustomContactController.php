<?php

namespace CustomContact\Controller;

use CustomContact\Event\CustomContactEvents;
use CustomContact\Model\CustomContact;
use OpenApi\Annotations as OA;
use CustomContact\Model\CustomContactQuery;
use OpenApi\Model\Api\ModelFactory;
use OpenApi\Service\OpenApiService;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;
use Thelia\Controller\Admin\AdminController;
use Thelia\Mailer\MailerFactory;
use Thelia\Model\ConfigQuery;
use Thelia\Tools\URL;

#[Route('/open_api/custom_contact', name: 'front_custom_contact_')]
class CustomContactController extends AdminController
{
    #[Route('/view', name:'view')]
    /**
     * @OA\Get(
     *     path="/custom_contact/view",
     *     tags={"Custom Contact"},
     *     summary="View Custom Contact",
     *     @OA\Response(
     *          response="200",
     *          description="Success",
     *          @OA\JsonContent(ref="#")
     *     ),
     *     @OA\Response(
     *          response="400",
     *          description="Bad request",
     *          @OA\JsonContent(ref="#")
     *     )
     * )
     */
    public function viewAction(RequestStack $requestStack, ModelFactory $modelFactory)
    {
        $request = $requestStack->getCurrentRequest();

        $locale = $this->findLocale($request);

        $customContact = CustomContactQuery::create()
            ->find();

        /*
        if (null !== $code = $request->get('code')) {
            $customContactQuery->filterByCode($code);
        }

        $customContactQuery->useCustomContactI18nQuery()
            ->filterByLocale($locale)
            ->endUse();
        */

        return $this->render('custom_contact', ['custom_contact' => $customContact]);
    }

    #[Route('/send/{code}', name:'send')]
    /**
     * @OA\Get(
     *     path="/custom_contact/send/{code}",
     *     tags={"Custom Contact"},
     *     summary="Send Custom Contact",
     *     @OA\Parameter(
     *          name="code",
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *     ),
     *     @OA\Response(
     *          response="200",
     *          description="Success",
     *          @OA\JsonContent(ref="#")
     *     ),
     *     @OA\Response(
     *          response="400",
     *          description="Bad request",
     *          @OA\JsonContent(ref="#")
     *     )
     * )
     */
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
            [$customContactForm->getEmail() => "customer"],
            [
                'receiver_email' => $customContactForm->getEmail(),
                'store_name' => ConfigQuery::getStoreName(),
                'response' => $requestStack->getCurrentRequest()->request
            ]
        );

        return $this->generateRedirect(URL::getInstance()->absoluteUrl('/account'));
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