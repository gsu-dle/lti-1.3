<?php

declare(strict_types=1);

namespace GAState\Web\LTI\Model;

/**
 * Composes properties about available Learning Information Services (LIS), usually originating from the Student
 * Information System. When the platform instance has access to these values it should, by best practice, provide them
 * in messages sent to tools.
 *
 * @see https://www.imsglobal.org/spec/lti/v1p3#learning-information-services-lis-claim
 * @see https://www.imsglobal.org/spec/lti/v1p3#lislti
 */
class LIS
{
    /**
     * URL endpoint for the LTI Basic Outcomes Service [LTI-BO-11]. By best practice, this URL should not change from
     * one resource link launch request message to the next; platforms should provide a single, unchanging endpoint URL
     * for each registered tool. This URL endpoint may support various operations/actions; by best practice, the
     * provider of an LTI Basic Outcome Service should respond with a response of unimplemented for actions it does not
     * support.
     *
     * This field MUST appear if the platform supports the LTI Basic Outcomes Service for receiving outcomes from any
     * resource link launch request messages sent to a particular tool.
     *
     * By best practice, an LTI Basic Outcome Service will only accept outcomes for launches from a user whose roles in
     * the context contains the Learner context role, and thus will only provide a services.lis.result_sourcedid value
     * in those resource link launch request messages. However, the platform should still send the
     * services.lis.outcome_service_url for all launching users in that context, regardless of whether or not it
     * provides a result_sourcedid value.
     *
     * @var ?string $outcomeServiceURL
     */
    public readonly ?string $outcomeServiceURL;


    /**
     * The LIS identifier for the user account that initiated the resource link launch request. The exact format of the
     * sourced ID may vary with the LIS integration; it is simply a unique identifier for the launching user.
     *
     * @var ?string $personSourcedID
     */
    public readonly ?string $personSourcedID;


    /**
     * An opaque identifier that indicates the LIS Result Identifier (if any) associated with the resource link launch
     * request (identifying a unique row and column within the service provider's gradebook).
     *
     * This field's value MUST be unique for every combination of context.id, resource_link.id, and user.id. The value
     * may change for a particular resource_link.id + user.id from one resource link launch request to the next, so the
     * tool should retain only the most recent value received for this field (for each context.id + resource_link.id +
     * user.id).
     *
     * @var ?string
     */
    public readonly ?string $resultSourcedID;


    /**
     * The LIS course offering identifiers applicable to the context of this basic LTI launch request message.
     *
     * @var ?string $courseOfferingSourceID
     */
    public readonly ?string $courseOfferingSourceID;


    /**
     * The LIS course section identifiers applicable to the context of this basic LTI launch request message.
     *
     * @var ?string $courseSectionSourceID
     */
    public readonly ?string $courseSectionSourceID;


    /**
     * @param ?string $outcomeServiceURL
     * @param ?string $personSourcedID
     * @param ?string $resultSourcedID
     * @param ?string $courseOfferingSourceID
     * @param ?string $courseSectionSourceID
     */
    public function __construct(
        ?string $outcomeServiceURL = null,
        ?string $personSourcedID = null,
        ?string $resultSourcedID = null,
        ?string $courseOfferingSourceID = null,
        ?string $courseSectionSourceID = null
    ) {
        $this->outcomeServiceURL = $outcomeServiceURL;
        $this->personSourcedID = $personSourcedID;
        $this->resultSourcedID = $resultSourcedID;
        $this->courseOfferingSourceID = $courseOfferingSourceID;
        $this->courseSectionSourceID = $courseSectionSourceID;
    }
}
