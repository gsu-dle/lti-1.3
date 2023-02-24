<?php

declare(strict_types=1);

namespace GAState\Web\LTI\Model;

use stdClass;

/**
 * This message type encapsulates the fundamental workflow of a user clicking a link in the presented user experience of
 * a context hosted by the platform and thereby launching out to an external tool that will provide a related, but
 * separate, user experience. With this workflow, the platform sends this message, and the tool receives the message.
 *
 * @see https://www.imsglobal.org/spec/lti/v1p3#resource-link-launch-request-message
 */
class Message
{
    /**
     * @var array<string, mixed> $rawValues
     */
    public readonly array $rawValues;


    /**
     * @var string $launchID
     */
    public readonly string $launchID;


    /**
     * Issuer identifier for the issuer of the token. The value is a case sensitive URL using the https scheme that
     * contains scheme, host, and optionally, port number and path components and no query or fragment components.
     *
     * @var string $issuer
     */
    public readonly string $issuer;


    /**
     * Audience(s) that this message is intended for. It MUST contain the OAuth 2.0 client_id of the tool provider. It
     * MAY also contain identifiers for other audiences. In the general case, the value is an array of case-sensitive
     * strings. In the common special case when there is one audience, the value MAY be a single case-sensitive string.
     *
     * NOTE: In this implementation if multiple values are returned, the first is used and the rest are discarded.
     *
     * @var string $clientID
     */
    public readonly string $clientID;


    /**
     * Used to associate a client session with a message, and to mitigate replay attacks.
     *
     * @var string $nonce
     */
    public readonly string $nonce;


    /**
     * @var string $state
     */
    public readonly string $state;


    /**
     * Contains a string that indicates the type of the sender's LTI message.
     *
     * @see https://www.imsglobal.org/spec/lti/v1p3#message-type-claim
     *
     * @var MessageType $type
     */
    public readonly MessageType $type;


    /**
     * Contains a string that indicates the version of LTI to which the message conforms. For conformance with this
     * specification, the claim must have the value 1.3.0.
     *
     * @see https://www.imsglobal.org/spec/lti/v1p3#lti-version-claim
     *
     * @var string $version
     */
    public readonly string $version;


    /**
     * Contains a case sensitive string that identifies the platform-tool integration governing the message. It MUST NOT
     * exceed 255 ASCII characters in length.
     *
     * The deployment_id is a stable locally unique identifier within the iss (Issuer).
     *
     * The deployment_id is an essential attribute for tools to associate to an account
     *
     * @see https://www.imsglobal.org/spec/lti/v1p3#lti-deployment-id-claim
     *
     * @var string $deploymentID
     */
    public readonly string $deploymentID;


    /**
     * The actual endpoint for the LTI resource to display; for example, the url in Deep Linking ltiResourceLink items,
     * or the launch_url in 1EdTech Common Cartridges, or any launch URL defined in the tool configuration.
     *
     * A Tool should rely on this claim rather than the initial target_link_uri to do the final redirection, since the
     * login initiation request is unsigned.
     *
     * @see https://www.imsglobal.org/spec/lti/v1p3#target-link-uri
     *
     * @var string $targetLinkURI
     */
    public readonly string $targetLinkURI;


    /**
     * Composes properties for the resource link from which the launch message occurs.
     *
     * @see https://www.imsglobal.org/spec/lti/v1p3#resource-link-claim
     *
     * @var ResourceLink $resourceLink
     */
    public readonly ResourceLink $resourceLink;


    /**
     * Since any platform-originating message is an OpenID ID Token, user claims are defined in the OpenId Connect
     * Standard Claims. LTI messages usually expect the following claims; a platform may add any other standard claims.
     *
     * @see https://www.imsglobal.org/spec/lti/v1p3#user-identity-claims
     *
     * @var UserIdentity $user
     */
    public readonly UserIdentity $user;


    /**
     * Contains a (possibly empty) array of URI values for roles that the user has within the message's associated
     * context.
     *
     * If the sender of the message wants to include a role from another vocabulary namespace, by best practice it
     * should use a fully-qualified URI to identify the role. By best practice, systems should not use roles from
     * another role vocabulary, as this may limit interoperability.
     *
     * @see https://www.imsglobal.org/spec/lti/v1p3#roles-claim
     *
     * @var array<string> $roles
     */
    public readonly array $roles;


    /**
     * LTI uses the term context where you might expect to see the word "course". A context is roughly equivalent to a
     * course, project, or other collection of resources with a common set of users and roles. LTI uses the word
     * "context" instead of "course" because a course is only one kind of context (another type could be "group" or
     * "section").
     *
     * @see https://www.imsglobal.org/spec/lti/v1p3#context-claim
     *
     * @var Context $context
     */
    public readonly Context $context;


    /**
     * Composes properties associated with the platform instance initiating the launch.
     *
     * A typical usage is to identify the learning institution's online learning platform. In a multi-tenancy case, a
     * single platform (iss) will host multiple instances, but each LTI message is originating from a single instance
     * identified by its guid
     *
     * @see https://www.imsglobal.org/spec/lti/v1p3#platform-instance-claim
     *
     * @var ToolPlatform
     */
    public readonly ToolPlatform $toolPlatform;


    /**
     * Contains an array of the user ID values which the current, launching user can access as a mentor (for example,
     * the launching user may be a parent or auditor of a list of other users).
     *
     * Different systems may use this information in different ways, LTI generally expects that the message receiver
     * will provide the mentor with access to tracking and summary information for other users, but not necessarily
     * access to those users' personal data or content submissions.
     *
     * The sender of the message MUST NOT include a list of user ID values in this property unless they also provide
     * http://purl.imsglobal.orb/vocab/lis/v2/membership#Mentor as one of the values passed in the roles claim.
     *
     * @see https://www.imsglobal.org/spec/lti/v1p3#role-scope-mentor-claims
     *
     * @var array<string> $roleScopeMentor
     */
    public readonly array $roleScopeMentor;


    /**
     * Composes properties that describe aspects of how the message sender expects to host the presentation of the
     * message receiver's user experience (for example, the height and width of the viewport the message sender gives
     * over to the message receiver).
     *
     * @see https://www.imsglobal.org/spec/lti/v1p3#launch-presentation-claim
     *
     * @var LaunchPresentation $launchPresentation
     */
    public readonly LaunchPresentation $launchPresentation;


    /**
     * Composes properties about available Learning Information Services (LIS), usually originating from the Student
     * Information System. When the platform instance has access to these values it should, by best practice, provide
     * them in messages sent to tools.
     *
     * @see https://www.imsglobal.org/spec/lti/v1p3#learning-information-services-lis-claim
     * @see https://www.imsglobal.org/spec/lti/v1p3#lislti
     *
     * @var LIS $lis
     */
    public readonly LIS $lis;


    /**
     * Customer LTI properties for D2L Brightspace
     *
     * @var Brightspace $brightspace
     */
    public readonly Brightspace $brightspace;


    /**
     * Returns whether or not the current launch can use the assignments and grades service.
     *
     * @var AGS $ags
     */
    public readonly AGS $ags;


    /**
     * Names and Role Provisioning Services
     *
     * @var NRPS $nrps
     */
    public readonly NRPS $nrps;


    /**
     * @param array<string, mixed> $rawValues
     * @param string $launchID
     * @param string $issuer
     * @param string $clientID
     * @param string $state
     * @param string $nonce
     * @param MessageType $type
     * @param string $version
     * @param string $deploymentID
     * @param string $targetLinkURI
     * @param array<string> $roles
     * @param array<string> $roleScopeMentor
     * @param ResourceLink $resourceLink
     * @param UserIdentity $user
     * @param Context $context
     * @param ToolPlatform $toolPlatform
     * @param LaunchPresentation $launchPresentation
     * @param LIS $lis
     * @param Brightspace $brightspace
     * @param AGS $ags
     * @param NRPS $nrps
     */
    public function __construct(
        array $rawValues,
        string $launchID,
        string $issuer,
        string $clientID,
        string $state,
        string $nonce,
        MessageType $type,
        string $version,
        string $deploymentID,
        string $targetLinkURI,
        array $roles,
        array $roleScopeMentor,
        ResourceLink $resourceLink,
        UserIdentity $user,
        Context $context,
        ToolPlatform $toolPlatform,
        LaunchPresentation $launchPresentation,
        LIS $lis,
        Brightspace $brightspace,
        AGS $ags,
        NRPS $nrps
    ) {
        // Base values
        $this->rawValues = $rawValues;
        $this->launchID = $launchID;
        $this->issuer = $issuer;
        $this->clientID = $clientID;
        $this->state = $state;
        $this->nonce = $nonce;
        $this->type = $type;
        $this->version = $version;
        $this->deploymentID = $deploymentID;
        $this->targetLinkURI = $targetLinkURI;
        $this->roles = $roles;
        $this->roleScopeMentor = $roleScopeMentor;

        // Sub-component values
        $this->resourceLink = $resourceLink;
        $this->user = $user;
        $this->context = $context;
        $this->toolPlatform = $toolPlatform;
        $this->launchPresentation = $launchPresentation;
        $this->lis = $lis;
        $this->brightspace = $brightspace;
        $this->ags = $ags;
        $this->nrps = $nrps;
    }
}
