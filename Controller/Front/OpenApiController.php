<?php

/*
 * This file is part of the Thelia package.
 * http://www.thelia.net
 *
 * (c) OpenStudio <info@thelia.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CustomContact\Controller\Front;

use CustomContact\Model\CustomContact;
use CustomContact\Model\CustomContactQuery;
use OpenApi\Controller\Front\BaseFrontOpenApiController;
use OpenApi\Model\Api\ModelFactory;
use OpenApi\Service\OpenApiService;
use Thelia\Core\HttpFoundation\JsonResponse;
use Thelia\Core\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;

/**
 * @Route("/open_api/custom_contact", name="custom_contact")
 */
class OpenApiController extends BaseFrontOpenApiController
{
    /**
     * @Route("", name="_forms", methods="GET")
     *
     * @OA\Get(
     *     path="/custom_contact",
     *     tags={"Custom contact"},
     *     summary="Get forms",
     *     @OA\Parameter(
     *          name="id",
     *          in="query",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *     ),
     *     @OA\Parameter(
     *          name="code",
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *     ),
     *     @OA\Parameter(
     *          name="locale",
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *     ),
     *     @OA\Response(
     *          response="200",
     *          description="Success",
     *          @OA\JsonContent(ref="#/components/schemas/CustomContact")
     *     ),
     *     @OA\Response(
     *          response="400",
     *          description="Bad request",
     *          @OA\JsonContent(ref="#/components/schemas/Error")
     *     )
     * )
     */
    public function getForms(
        Request $request,
        ModelFactory $modelFactory,
    ) {
        $locale = $this->findLocale($request);

        $formQuery = CustomContactQuery::create();

        if (null !== $id = $request->get('id')) {
            $formQuery->filterById($id);
        }

        if (null !== $code = $request->get('code')) {
            $formQuery->filterByCode($code);
        }

        $arr = [];

        /**
         * @var CustomContact $form
         */
        foreach ($formQuery->find() as $form) {
            /** @var \CustomContact\Model\Api\CustomContact $objet */
            $objet = $modelFactory->buildModel('CustomContact', $form, $locale);

            $arr[] = [
                "id" => $objet->getId(),
                "code" => $objet->getCode(),
                "title" => $objet->getTitle(),
                "email" => $objet->getEmail(),
                "fieldConfiguration" => $objet->getFieldConfiguration(),
                "returnUrl" => $objet->getReturnUrl(),
            ];
        }

        return OpenApiService::jsonResponse($arr);
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
