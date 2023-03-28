<?php

namespace CustomContactForm\Controller;

use CustomContactForm\Event\CustomContactFormEvents;
use CustomContactForm\Model\CustomContactForm;
use CustomContactForm\Model\CustomContactFormQuery;
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

#[Route('/admin/module/CustomContactForm', name: 'custom_contact_form_')]
class CustomContactFormController extends AdminController
{
    #[Route('/create', name: 'create')]
    public function createCustomContactForm(EventDispatcherInterface $dispatcher, MailerFactory $mailer, ParserContext $parserContext)
    {
        if (null !== $response = $this->checkAuth(AdminResources::MODULE, 'CustomContactForm', AccessManager::UPDATE)) {
            return $response;
        }

        $form = $this->createForm('custom_contact_form_form');

        try {
            $data = $this->validateForm($form)->getData();

            $CustomContactForm = new CustomContactForm();

            $CustomContactForm
                ->setTitle($data["title"])
                ->setCode($data["code"])
                ->setFieldConfiguration($data["field_configuration"])
                ->setEmail($data["receiver_email"])
                ->save();

            $event = new CustomContactFormEvents();
            $dispatcher->dispatch($event, CustomContactFormEvents::CUSTOM_CONTACT_FORM_EVENT);

            $storeEmail = ConfigQuery::getStoreEmail();

            $mailer->sendEmailMessage(
                'mail_custom_contact_form',
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
    public function deleteCustomContactForm($id)
    {
        if (null !== $response = $this->checkAuth(AdminResources::MODULE, 'CustomContactForm', AccessManager::DELETE)) {
            return $response;
        }

        CustomContactFormQuery::create()->filterById($id)->delete();

        return $this->generateRedirect(URL::getInstance()->absoluteUrl('/admin/module/CustomContactForm'));
    }
}