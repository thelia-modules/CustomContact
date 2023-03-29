<?php

namespace CustomContact\Controller;

use CustomContact\Event\CustomContactEvents;
use CustomContact\Model\CustomContact;
use CustomContact\Model\CustomContactQuery;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Thelia\Controller\Admin\AdminController;
use Thelia\Core\Security\AccessManager;
use Thelia\Core\Security\Resource\AdminResources;
use Thelia\Core\Template\ParserContext;
use Thelia\Form\Exception\FormValidationException;
use Thelia\Mailer\MailerFactory;
use Thelia\Model\ConfigQuery;
use Thelia\Tools\URL;

#[Route('/admin/module/CustomContact', name: 'custom_contact_')]
class CustomContactController extends AdminController
{
    #[Route('/create', name: 'create')]
    public function createCustomContact(EventDispatcherInterface $dispatcher, MailerFactory $mailer, ParserContext $parserContext)
    {
        if (null !== $response = $this->checkAuth(AdminResources::MODULE, 'CustomContact', AccessManager::UPDATE)) {
            return $response;
        }

        $form = $this->createForm('custom_contact_form');

        try {
            $data = $this->validateForm($form)->getData();

            $customContact = new CustomContact();

            $customContact
                ->setTitle($data["title"])
                ->setCode($data["code"])
                ->setFieldConfiguration($data["field_configuration"])
                ->setEmail($data["receiver_email"])
                ->save();

            $event = new CustomContactEvents();
            $dispatcher->dispatch($event, CustomContactEvents::CUSTOM_CONTACT_EVENT);

            $storeEmail = ConfigQuery::getStoreEmail();

            $mailer->sendEmailMessage(
                'mail_custom_contact',
                [$storeEmail => ConfigQuery::getStoreName()],
                [$data["receiver_email"] => "new customer"],
                [
                    'receiver_email' => $data["receiver_email"],
                    'store_name' => ConfigQuery::getStoreName(),
                    'field_configuration' => $data["field_configuration"]
                ]
            );

            return $this->generateSuccessRedirect($form);
        } catch (FormValidationException $e) {
            $error_message = $this->createStandardFormValidationErrorMessage($e);
        } catch (\Exception $e) {
            $error_message = $e->getMessage();
        }

        $form->setErrorMessage($error_message);

        $parserContext
            ->addForm($form)
            ->setGeneralError($error_message);

        return $this->generateErrorRedirect($form);
    }

    #[Route('/delete/{id}', name: 'delete')]
    public function deleteCustomContact($id)
    {
        if (null !== $response = $this->checkAuth(AdminResources::MODULE, 'CustomContact', AccessManager::DELETE)) {
            return $response;
        }

        CustomContactQuery::create()->filterById($id)->delete();

        return $this->generateRedirect(URL::getInstance()->absoluteUrl('/admin/module/CustomContact'));
    }
}