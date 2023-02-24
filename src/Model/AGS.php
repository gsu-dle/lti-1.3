<?php

declare(strict_types=1);

namespace GAState\Web\LTI\Model;

/**
 * This claim MUST be included in LTI messages if any of the Assignment and Grade Services are accessible by the tool in
 * the context of the LTI message.
 *
 * The platform MAY change end point URLs as it deems necessary; therefore, by best practice, the tool should check with
 * each message for the endpoint URL it should use with respect to the resource associated with the message. By best
 * practice, the platform should maintain the presence of endpoints communicated within a message for some length of
 * time, as tools may intend to perform asynchronous operations; for example, the tool may use the lineitem URL to
 * update scores quite some time after the student has actually completed its associated activity.
 *
 * @see https://www.imsglobal.org/spec/lti-ags/v2p0/#assignment-and-grade-service-claim
 */
class AGS
{
    /**
     * An array of scopes the tool may ask an access token for
     *
     * @var array<string> $scope
     */
    public readonly array $scope;


    /**
     * When an LTI message is launching a resource associated to one and only one lineitem, the claim must include the
     * endpoint URL for accessing the associated line item; in all other cases, this property must be either blank or
     * not included in the claim.
     *
     * @var ?string $lineitem
     */
    public readonly ?string $lineitem;


    /**
     * The endpoint URL for accessing the line item container for the current context. May be omitted if the tool has no
     * permissions to access this endpoint.
     *
     * @var string? $lineitems
     */
    public readonly ?string $lineitems;


    /**
     * @param array<string> $scope
     * @param ?string $lineitem
     * @param ?string $lineitems
     */
    public function __construct(
        array $scope,
        ?string $lineitem = null,
        ?string $lineitems = null
    ) {
        $this->scope = $scope;
        $this->lineitem = $lineitem;
        $this->lineitems = $lineitems;
    }
}
