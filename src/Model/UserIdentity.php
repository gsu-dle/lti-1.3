<?php

declare(strict_types=1);

namespace GAState\Web\LTI\Model;

/**
 * Since any platform-originating message is an OpenID ID Token, user claims are defined in the OpenId Connect Standard
 * Claims. LTI messages usually expect the following claims; a platform may add any other standard claims.
 *
 * @see https://www.imsglobal.org/spec/lti/v1p3#user-identity-claims
 */
class UserIdentity
{
    /**
     * This is the only required user claim (except, see anonymous launch case following). When included, per OIDC
     * specifications, the sub (Subject) MUST be a stable locally unique to the iss (Issuer) identifier for the actual,
     * authenticated End-User that initiated the launch. It MUST NOT exceed 255 ASCII characters in length and is
     * case-sensitive.
     *
     * @var string $subject
     */
    public readonly string $subject;


    /**
     * Per OIDC specifcations, given name(s) or first name(s) of the End-User. Note that in some cultures, people can
     * have multiple given names; all can be present, with the names being separated by space characters.
     *
     * @var ?string $givenName
     */
    public readonly ?string $givenName;


    /**
     * Per OIDC specifcations, surname(s) or last name(s) of the End-User. Note that in some cultures, people can have
     * multiple family names or no family name; all can be present, with the names being separated by space characters.
     *
     * @var ?string $familyName
     */
    public readonly ?string $familyName;


    /**
     * Per OIDC specifcations, end-User's full name in displayable form including all name parts, possibly including
     * titles and suffixes, ordered according to the End-User's locale and preferences.
     *
     * @var ?string $name
     */
    public readonly ?string $name;


    /**
     * Per OIDC specifcations, end-User's preferred e-mail address
     *
     * @var ?string $email
     */
    public readonly ?string $email;


    /**
     * @param string $subject
     * @param ?string $givenName
     * @param ?string $familyName
     * @param ?string $name
     * @param ?string $email
     */
    public function __construct(
        string $subject,
        ?string $givenName = null,
        ?string $familyName = null,
        ?string $name = null,
        ?string $email = null
    ) {
        $this->subject = $subject;
        $this->givenName = $givenName;
        $this->familyName = $familyName;
        $this->name = $name;
        $this->email = $email;
    }
}
