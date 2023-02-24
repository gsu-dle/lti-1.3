<?php

declare(strict_types=1);

namespace GAState\Web\LTI\Model;

/**
 * LTI uses the term context where you might expect to see the word "course". A context is roughly equivalent to a
 * course, project, or other collection of resources with a common set of users and roles. LTI uses the word "context"
 * instead of "course" because a course is only one kind of context (another type could be "group" or "section").
 *
 * @see https://www.imsglobal.org/spec/lti/v1p3#context-claim
 */
class Context
{
    /**
     * Stable identifier that uniquely identifies the context from which the LTI message initiates. The context id MUST
     * be locally unique to the deployment_id. It is recommended to also be locally unique to iss (Issuer). The value of
     * id MUST NOT exceed 255 ASCII characters in length and is case-sensitive.
     *
     * @var string $id
     */
    public readonly string $id;


    /**
     * An array of URI values for context types. If present, the array MUST include at least one context type from the
     * context type vocabulary described in context type vocabulary. If the sender of the message wants to include a
     * context type from another vocabulary namespace, by best practice it should use a fully-qualified URI. By best
     * practice, systems should not use context types from another role vocabulary, as this may limit interoperability.
     *
     * @var array<string>|null $type
     */
    public readonly ?array $type;


    /**
     * Short descriptive name for the context. This often carries the "course code" for a course offering or course
     * section context.
     *
     * @var ?string $label
     */
    public readonly ?string $label;


    /**
     * Full descriptive name for the context. This often carries the "course title" or "course name" for a course
     * offering context.
     *
     * @var ?string $title
     */
    public readonly ?string $title;


    /**
     * @param string $id
     * @param array<string>|null $type
     * @param ?string $label
     * @param ?string $title
     */
    public function __construct(
        string $id,
        ?array $type = null,
        ?string $label = null,
        ?string $title = null
    ) {
        $this->id = $id;
        $this->type = $type;
        $this->label = $label;
        $this->title = $title;
    }
}
