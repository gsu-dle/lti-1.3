<?php

declare(strict_types=1);

namespace GAState\Web\LTI\Model;

/**
 * The claim to include Names and Role Provisioning Service parameter in LTI 1.3 messages
 *
 * @see https://www.imsglobal.org/spec/lti-nrps/v2p0#lti-1-3-integration
 */
class NRPS
{
    /**
     * Service URL is always fully resolved, and matches the context of the launch
     *
     * @var string $contextMembershipsURL
     */
    public readonly string $contextMembershipsURL;


    /**
     * Specifies the versions of the service that are supported on the service URL end point
     *
     * @var array<string> $serviceVersions
     */
    public readonly array $serviceVersions;


    /**
     * @param string $contextMembershipsURL
     * @param array<string> $serviceVersions
     */
    public function __construct(
        string $contextMembershipsURL,
        array $serviceVersions
    ) {
        $this->contextMembershipsURL = $contextMembershipsURL;
        $this->serviceVersions = $serviceVersions;
    }
}
