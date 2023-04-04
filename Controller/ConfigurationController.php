<?php

namespace CustomContact\Controller;

use CustomContact\Form\CustomContactForm;
use CustomContact\Model\CustomContact;
use CustomContact\Model\CustomContactQuery;
use Symfony\Component\Routing\Annotation\Route;
use Thelia\Controller\Admin\BaseAdminController;
use Thelia\Core\Security\AccessManager;
use Thelia\Core\Security\Resource\AdminResources;
use Thelia\Core\Template\ParserContext;
use Thelia\Form\Exception\FormValidationException;
use Thelia\Tools\URL;

#[Route('/admin/module/CustomContact', name: 'configuration_custom_contact_')]
class ConfigurationController extends BaseAdminController
{
    #[Route('/create', name: 'create')]
    public function createCustomContact(ParserContext $parserContext)
    {
        if (null !== $response = $this->checkAuth(AdminResources::MODULE, 'CustomContact', AccessManager::CREATE)) {
            return $response;
        }

        $form = $this->createForm(CustomContactForm::getName());

        try {
            $data = $this->validateForm($form)->getData();

            $customContact = new CustomContact();

            $customContact
                ->setLocale($this->getCurrentEditionLocale())
                ->setTitle($data["title"])
                ->setCode($data["code"])
                ->setFieldConfiguration($data["field_configuration"])
                ->setEmail($data["receiver_email"])
                ->save();

            return $this->generateSuccessRedirect($form);
        } catch (FormValidationException $e) {
            $errorMessage = $this->createStandardFormValidationErrorMessage($e);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
        }

        $form->setErrorMessage($errorMessage);

        $parserContext
            ->addForm($form)
            ->setGeneralError($errorMessage);

        return $this->generateErrorRedirect($form);
    }

    #[Route('/update/{id}', name: 'update', methods: 'POST')]
    public function updateCustomerContact(ParserContext $parserContext, $id)
    {
        if (null !== $response = $this->checkAuth(AdminResources::MODULE, 'CustomContact', AccessManager::UPDATE)) {
            return $response;
        }

        $form = $this->createForm(CustomContactForm::getName());

        try {
            $data = $this->validateForm($form)->getData();

            $customContact = CustomContactQuery::create()->findOneById($id);

            $customContact
                ->setLocale($this->getCurrentEditionLocale())
                ->setTitle($data["title"])
                ->setCode($data["code"])
                ->setFieldConfiguration($data["field_configuration"])
                ->setEmail($data["receiver_email"])
                ->save();


            return $this->generateSuccessRedirect($form);
        } catch (FormValidationException $e) {
            $errorMessage = $this->createStandardFormValidationErrorMessage($e);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
        }

        $form->setErrorMessage($errorMessage);

        $parserContext
            ->addForm($form)
            ->setGeneralError($errorMessage);

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