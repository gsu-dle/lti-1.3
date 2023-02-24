<?php

declare(strict_types=1);

namespace GAState\Web\LTI\Model;

/**
 * Composes properties for the resource link from which the launch message occurs.
 *
 * @see https://www.imsglobal.org/spec/lti/v1p3#resource-link-claim
 */
class ResourceLink
{
    /**
     * Opaque identifier for a placement of an LTI resource link within a context that MUST be a stable and locally
     * unique to the deployment_id. This value MUST change if the link is copied or exported from one system or context
     * and imported into another system or context. The value of id MUST NOT exceed 255 ASCII characters in length and
     * is case-sensitive.
     *
     * @var string $id
     */
    public readonly string $id;


    /**
     * Descriptive phrase for an LTI resource link placement.
     *
     * @var ?string $description
     */
    public readonly ?string $description;


    /**
     * Descriptive title for an LTI resource link placement.
     *
     * @var ?string $title
     */
    public readonly ?string $title;


    /**
     * @param string $id
     * @param ?string $description
     * @param ?string $title
     */
    public function __construct(
        string $id,
        ?string $description = null,
        ?string $title = null
    ) {
        $this->id = $id;
        $this->description = $description;
        $this->title = $title;
    }
}
