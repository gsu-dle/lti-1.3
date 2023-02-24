<?php

declare(strict_types=1);

namespace GAState\Web\LTI\Model;

use Exception                                as Exception;
use GAState\Web\LTI\Model\AGS                as AGS;
use GAState\Web\LTI\Model\Brightspace        as Brightspace;
use GAState\Web\LTI\Model\Context            as Context;
use GAState\Web\LTI\Model\LaunchPresentation as LaunchPresentation;
use GAState\Web\LTI\Model\LIS                as LIS;
use GAState\Web\LTI\Model\Message            as Message;
use GAState\Web\LTI\Model\MessageType        as MessageType;
use GAState\Web\LTI\Model\NRPS               as NRPS;
use GAState\Web\LTI\Model\ResourceLink       as ResourceLink;
use GAState\Web\LTI\Model\ToolPlatform       as ToolPlatform;
use GAState\Web\LTI\Model\UserIdentity       as UserIdentity;
use stdClass                                 as stdClass;

class MessageFactory implements MessageFactoryInterface
{
    protected const LAUNCH_ID           = "launch_id";
    protected const ISSUER              = "iss";
    protected const CLIENT_ID           = "aud";
    protected const STATE               = "state";
    protected const NONCE               = "nonce";
    protected const DEPLOYMENT_ID       = "https://purl.imsglobal.org/spec/lti/claim/deployment_id";
    protected const MESSAGE_TYPE        = "https://purl.imsglobal.org/spec/lti/claim/message_type";
    protected const MESSAGE_VERSION     = "https://purl.imsglobal.org/spec/lti/claim/version";
    protected const TARGET_LINK_URI     = "https://purl.imsglobal.org/spec/lti/claim/target_link_uri";
    protected const USER_SUBJECT        = "sub";
    protected const GIVEN_NAME          = "given_name";
    protected const FAMILY_NAME         = "family_name";
    protected const NAME                = "name";
    protected const EMAIL               = "email";
    protected const ROLES               = "https://purl.imsglobal.org/spec/lti/claim/roles";
    protected const RESOURCE_LINK       = "https://purl.imsglobal.org/spec/lti/claim/resource_link";
    protected const CONTEXT             = "https://purl.imsglobal.org/spec/lti/claim/context";
    protected const LIS                 = "https://purl.imsglobal.org/spec/lti/claim/lis";
    protected const LAUNCH_PRESENTATION = "https://purl.imsglobal.org/spec/lti/claim/launch_presentation";
    protected const TOOL_PLATFORM       = "https://purl.imsglobal.org/spec/lti/claim/tool_platform";
    protected const BRIGHTSPACE         = "http://www.brightspace.com";
    protected const AGS                 = "https://purl.imsglobal.org/spec/lti-ags/claim/endpoint";
    protected const NRPS                = "https://purl.imsglobal.org/spec/lti-nrps/claim/namesroleservice";


    /**
     * @param mixed $values
     *
     * @return Message
     */
    public function createMessage(mixed $values): Message
    {
        if (is_object($values)) {
            $values = get_object_vars($values);
        }
        if (!is_array($values)) {
            throw new Exception(); // TODO: add specific error
        }

        /**
         * The values for `UserIdentity` are on the root, so we're just going to recast the `$values` array as an
         * `stdClass` object
         *
         * @var stdClass $user
         **/
        $user = (object) $values;

        return new Message(
            rawValues: $values,
            launchID: $this->getString($values[self::LAUNCH_ID] ?? null),
            issuer: $this->getString($values[self::ISSUER] ?? null),
            clientID: $this->getString($values[self::CLIENT_ID] ?? null),
            state: $this->getString($values[self::STATE] ?? null),
            nonce: $this->getString($values[self::NONCE] ?? null),
            type: MessageType::from($this->getString($values[self::MESSAGE_TYPE] ?? null)),
            version: $this->getString($values[self::MESSAGE_VERSION] ?? null),
            deploymentID: $this->getString($values[self::DEPLOYMENT_ID] ?? null),
            targetLinkURI: $this->getString($values[self::TARGET_LINK_URI] ?? null),
            roles: $this->getArray($values[self::ROLES] ?? null),
            roleScopeMentor: [], // TODO: locate xpath
            resourceLink: $this->createResourceLink($values[self::RESOURCE_LINK] ?? null),
            user: $this->createUserIdentity($user),
            context: $this->createContext($values[self::CONTEXT] ?? null),
            toolPlatform: $this->createToolPlatform($values[self::TOOL_PLATFORM] ?? null),
            launchPresentation: $this->createLaunchPresentation(
                $values[self::LAUNCH_PRESENTATION] ?? null
            ),
            lis: $this->createLIS($values[self::LIS] ?? null),
            brightspace: $this->createBrightspace($values[self::BRIGHTSPACE] ?? null),
            ags: $this->createAGS($values[self::AGS] ?? null),
            nrps: $this->createNRPS($values[self::NRPS] ?? null),
        );
    }


    /**
     * @param mixed $values
     *
     * @return ResourceLink
     */
    public function createResourceLink(mixed $values): ResourceLink
    {
        $values = $this->getObject($values);
        return new ResourceLink(
            id: $this->getString($values->id ?? null),
            description: $this->getStringNull($values->description ?? null),
            title: $this->getStringNull($values->title ?? null),
        );
    }


    /**
     * @param mixed $values
     *
     * @return UserIdentity
     */
    public function createUserIdentity(mixed $values): UserIdentity
    {
        $values = $this->getObject($values);
        return new UserIdentity(
            subject: $this->getString($values->sub ?? null),
            givenName: $this->getStringNull($values->given_name ?? null),
            familyName: $this->getStringNull($values->family_name ?? null),
            name: $this->getStringNull($values->name ?? null),
            email: $this->getStringNull($values->email ?? null),
        );
    }


    /**
     * @param mixed $values
     *
     * @return Context
     */
    public function createContext(mixed $values): Context
    {
        $values = $this->getObject($values);
        return new Context(
            id: $this->getString($values->id ?? null),
            label: $this->getStringNull($values->label ?? null),
            title: $this->getStringNull($values->title ?? null),
            type: $this->getArrayNull($values->type ?? null),
        );
    }


    /**
     * @param mixed $values
     *
     * @return ToolPlatform
     */
    public function createToolPlatform(mixed $values): ToolPlatform
    {
        $values = $this->getObject($values);
        return new ToolPlatform(
            guid: $this->getString($values->guid ?? null),
            contactEmail: $this->getStringNull($values->contact_email ?? null),
            description: $this->getStringNull($values->description ?? null),
            name: $this->getStringNull($values->name ?? null),
            url: $this->getStringNull($values->url ?? null),
            productFamilyCode: $this->getStringNull($values->product_family_code ?? null),
            version: $this->getStringNull($values->version ?? null),
        );
    }


    /**
     * @param mixed $values
     *
     * @return LaunchPresentation
     */
    public function createLaunchPresentation(mixed $values): LaunchPresentation
    {
        $values = $this->getObject($values);
        return new LaunchPresentation(
            documentTarget: $this->getStringNull($values->documentTarget ?? null),
            height: $this->getIntNull($values->height ?? null),
            width: $this->getIntNull($values->width ?? null),
            returnURL: $this->getStringNull($values->returnURL ?? null),
            locale: $this->getStringNull($values->locale ?? null),
        );
    }


    /**
     * @param mixed $values
     *
     * @return LIS
     */
    public function createLIS(mixed $values): LIS
    {
        $values = $this->getObject($values);
        return new LIS(
            outcomeServiceURL: $this->getStringNull($values->outcome_service_url ?? null),
            personSourcedID: $this->getStringNull($values->person_sourced_id ?? null),
            resultSourcedID: $this->getStringNull($values->result_sourced_id ?? null),
            courseOfferingSourceID: $this->getStringNull($values->course_offering_sourcedid ?? null),
            courseSectionSourceID: $this->getStringNull($values->course_section_sourcedid ?? null),
        );
    }


    /**
     * @param mixed $values
     *
     * @return Brightspace
     */
    public function createBrightspace(mixed $values): Brightspace
    {
        $values = $this->getObject($values);
        return new Brightspace(
            tenantID: $this->getStringNull($values->tenant_id ?? null),
            orgDefinedID: $this->getStringNull($values->org_defined_id ?? null),
            userID: $this->getInt($values->user_id ?? null),
            username: $this->getStringNull($values->username ?? null),
            contextIDHistory: $this->getStringNull($values->context_id_history ?? null),
            resourceLinkIDHistory: $this->getIntNull($values->content_topic_id ?? null),
            contentTopicID: $this->getIntNull($values->content_topic_id ?? null),
        );
    }


    /**
     * @param mixed $values
     *
     * @return AGS
     */
    public function createAGS(mixed $values): AGS
    {
        $values = $this->getObject($values);
        return new AGS(
            scope: $this->getArray($values->scope ?? null),
            lineitem: $this->getStringNull($values->lineitem ?? null),
            lineitems: $this->getStringNull($values->lineitems ?? null),
        );
    }


    /**
     * @param mixed $values
     *
     * @return NRPS
     */
    public function createNRPS(mixed $values): NRPS
    {
        $values = $this->getObject($values);
        return new NRPS(
            contextMembershipsURL: $this->getString($values->context_memberships_url ?? null),
            serviceVersions: $this->getArray($values->service_versions ?? null),
        );
    }


    /**
     * @param mixed $val
     *
     * @return int
     */
    protected function getInt(mixed $val): int
    {
        return is_integer($val) ? $val : intval($val);
    }


    /**
     * @param mixed $val
     *
     * @return int|null
     */
    protected function getIntNull(mixed $val): int|null
    {
        return is_integer($val) ? $val : null;
    }


    /**
     * @param mixed $val
     *
     * @return string
     */
    protected function getString(mixed $val): string
    {
        return is_string($val) ? $val : strval($val);
    }


    /**
     * @param mixed $val
     *
     * @return string|null
     */
    protected function getStringNull(mixed $val): string|null
    {
        return is_string($val) ? $val : null;
    }


    /**
     * @param mixed $val
     *
     * @return array<int|string,string>
     */
    protected function getArray(mixed $val): array
    {
        if (is_object($val)) {
            $val = get_object_vars($val);
        }

        $out = [];
        if (is_array($val)) {
            foreach ($val as $k => $v) {
                $out[$k] = $this->getString($v);
            }
        }

        /** @var array<int|string,string> $out */
        return $out;
    }


    /**
     * @param mixed $val
     *
     * @return array<int|string,string>|null
     */
    protected function getArrayNull(mixed $val): array|null
    {
        return (is_object($val) || is_array($val)) ? $this->getArray($val) : null;
    }


    /**
     * @param mixed $val
     *
     * @return stdClass
     */
    protected function getObject(mixed $val): stdClass
    {
        if (is_object($val)) {
            $val = get_object_vars($val);
        }

        if (is_array($val)) {
            $val = (object) $val;
            /** @var stdClass $val */
            return $val;
        }

        return new stdClass();
    }


    /**
     * @param mixed $val
     *
     * @return ?stdClass
     */
    protected function getObjectNull(mixed $val): ?stdClass
    {
        if (is_object($val)) {
            $val = get_object_vars($val);
        }

        if (is_array($val)) {
            $val = (object) $val;
            /** @var stdClass $val */
            return $val;
        }

        return null;
    }
}
