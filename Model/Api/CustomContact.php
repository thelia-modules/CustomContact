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

namespace CustomContact\Model\Api;

use OpenApi\Model\Api\BaseApiModel;

/**
 * Class CustomContact.
 *
 * @OA\Schema(
 *     schema="CustomContact",
 *     title="CustomContact",
 * )
 */
class CustomContact extends BaseApiModel
{
    /**
     * @OA\Property(
     *    type="integer",
     * )
     * @Constraint\NotBlank(groups={"read"})
     */
    protected $id;

    /**
     * @OA\Property(
     *     type="string",
     * )
     */
    protected $code;

    /**
     * @OA\Property(
     *     type="string",
     * )
     */
    protected $title;

    /**
     * @OA\Property(
     *     type="string",
     * )
     */
    protected $returnUrl;

    /**
     * @OA\Property(
     *     type="string",
     * )
     */
    protected $fieldConfiguration;

    /**
     * @OA\Property(
     *     type="string",
     * )
     */
    protected $email;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    public function getReturnUrl()
    {
        return $this->returnUrl;
    }

    public function setReturnUrl($returnUrl)
    {
        $this->returnUrl = $returnUrl;
        return $this;
    }

    public function getFieldConfiguration()
    {
        return json_decode($this->fieldConfiguration);
    }

    public function setFieldConfiguration($fieldConfiguration)
    {
        $this->fieldConfiguration = $fieldConfiguration;
        return $this;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }
}
