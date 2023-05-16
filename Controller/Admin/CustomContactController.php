<?php

namespace CustomContact\Controller\Admin;

use CustomContact\Form\CustomContactForm;
use CustomContact\Model\CustomContact;
use CustomContact\Model\CustomContactQuery;
use Symfony\Component\Routing\Annotation\Route;
use Thelia\Controller\Admin\BaseAdminController;
use Thelia\Core\HttpFoundation\Response;
use Thelia\Core\Security\AccessManager;
use Thelia\Core\Security\Resource\AdminResources;
use Thelia\Core\Template\ParserContext;
use Thelia\Core\Translation\Translator;
use Thelia\Form\Exception\FormValidationException;
use Thelia\Tools\URL;

#[Route('/admin/custom_contact', name: 'custom_contact')]
class CustomContactController extends BaseAdminController
{
    public function __construct(
        private ParserContext $parserContext
    )
    {
    }

    #[Route('', name: 'create_view', methods: ['GET'])]
    public function createView()
    {
        return $this->render(
            'custom_contact/form',
            [
                'pageTitle' => Translator::getInstance()->trans("Add a new form", [], \CustomContact\CustomContact::DOMAIN_NAME),
                'customContact' => (new CustomContact()),
            ]
        );
    }

    #[Route('/{id}', name: 'update_view', methods: ['GET'])]
    public function updateView(string $id)
    {
        $customContact = CustomContactQuery::create()
            ->filterById($id)
            ->findOne();

        if (null === $customContact) {
            return new Response(status: Response::HTTP_NOT_FOUND);
        }

        $customContact->setLocale($this->getCurrentEditionLocale());

        return $this->render(
            'custom_contact/form',
            [
                'pageTitle' => Translator::getInstance()->trans("Update ", [], \CustomContact\CustomContact::DOMAIN_NAME)." \"".$customContact->getTitle()."\"",
                'customContact' => $customContact
            ]
        );
    }

    #[Route('', name: 'create_action', methods: ['POST'])]
    public function createAction()
    {
        return $this->saveCustomContact();
    }

    #[Route('/{id}', name: 'update_action', methods: ['POST'])]
    public function updateAction($id)
    {
        $customContact = CustomContactQuery::create()
            ->filterById($id)
            ->findOne();

        if (null === $customContact) {
            return new Response(status: Response::HTTP_NOT_FOUND);
        }

        return $this->saveCustomContact($customContact);
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

    private function saveCustomContact(CustomContact $customContact = null)
    {
        if (null === $customContact) {
            $customContact = new CustomContact();
        }

        $form = $this->createForm(CustomContactForm::getName());

        try {
            $data = $this->validateForm($form)->getData();

            $customContact
                ->setLocale($this->getCurrentEditionLocale())
                ->setTitle($data["title"])
                ->setCode($data["code"])
                ->setFieldConfiguration($data["field_configuration"])
                ->setEmail($data["receiver_email"])
                ->setReturnUrl($data["return_url"])
                ->setSuccessMessage($data["success_message"])
                ->save();

            return $this->generateSuccessRedirect($form);
        } catch (FormValidationException $e) {
            $errorMessage = $this->createStandardFormValidationErrorMessage($e);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
        }

        $form->setErrorMessage($errorMessage);

        $this->parserContext
            ->addForm($form)
            ->setGeneralError($errorMessage);

        return $this->generateErrorRedirect($form);
    }
}