<?php

declare(strict_types=1);

namespace GAState\Web\LTI\Model;

/**
 * Composes properties associated with the platform instance initiating the launch.
 *
 * A typical usage is to identify the learning institution's online learning platform. In a multi-tenancy case, a single
 * platform (iss) will host multiple instances, but each LTI message is originating from a single instance identified
 * by its guid.
 *
 * @see https://www.imsglobal.org/spec/lti/v1p3#platform-instance-claim
 */
class ToolPlatform
{
    /**
     * A stable locally unique to the iss identifier for an instance of the tool platform. The value of guid is a
     * case-sensitive string that MUST NOT exceed 255 ASCII characters in length. The use of Universally Unique
     * IDentifier (UUID) defined in [RFC4122] is recommended.
     *
     * @var string $guid
     */
    public readonly string $guid;


    /**
     * Administrative contact email for the platform instance.
     *
     * @var ?string $contactEmail
     */
    public readonly ?string $contactEmail;


    /**
     * Descriptive phrase for the platform instance
     *
     * @var ?string $description
     */
    public readonly ?string $description;


    /**
     * Name for the platform instance
     *
     * @var ?string $name
     */
    public readonly ?string $name;


    /**
     * Home HTTPS URL endpoint for the platform instance.
     *
     * @var ?string $url
     */
    public readonly ?string $url;


    /**
     * Vendor product family code for the type of platform.
     *
     * @var ?string $productFamilyCode
     */
    public readonly ?string $productFamilyCode;


    /**
     * Vendor product version for the platform.
     *
     * @var ?string $version
     */
    public readonly ?string $version;


    /**
     * @param string $guid
     * @param ?string $contactEmail
     * @param ?string $description
     * @param ?string $name
     * @param ?string $url
     * @param ?string $productFamilyCode
     * @param ?string $version
     */
    public function __construct(
        string $guid,
        ?string $contactEmail = null,
        ?string $description = null,
        ?string $name = null,
        ?string $url = null,
        ?string $productFamilyCode = null,
        ?string $version = null
    ) {
        $this->guid = $guid;
        $this->contactEmail = $contactEmail;
        $this->description = $description;
        $this->name = $name;
        $this->url = $url;
        $this->productFamilyCode = $productFamilyCode;
        $this->version = $version;
    }
}
