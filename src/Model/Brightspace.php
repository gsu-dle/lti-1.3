<?php

declare(strict_types=1);

namespace GAState\Web\LTI\Model;

/**
 * @see https://documentation.brightspace.com/EN/integrations/ipsis/LTI%20Advantage/intro_to_LTI.htm
 */
class Brightspace
{
    public readonly int $userID;
    public readonly ?string $tenantID;
    public readonly ?string $orgDefinedID;
    public readonly ?string $username;
    public readonly ?string $contextIDHistory;
    public readonly ?int $resourceLinkIDHistory;
    public readonly ?int $contentTopicID;

    /**
     * @param int $userID
     * @param ?string $tenantID
     * @param ?string $orgDefinedID
     * @param ?string $username
     * @param ?string $contextIDHistory
     * @param ?int $resourceLinkIDHistory
     * @param ?int $contentTopicID
     */
    public function __construct(
        int $userID,
        ?string $tenantID = null,
        ?string $orgDefinedID = null,
        ?string $username = null,
        ?string $contextIDHistory = null,
        ?int $resourceLinkIDHistory = null,
        ?int $contentTopicID = null
    ) {
        $this->userID = $userID;
        $this->tenantID = $tenantID;
        $this->orgDefinedID = $orgDefinedID;
        $this->username = $username;
        $this->contextIDHistory = $contextIDHistory;
        $this->resourceLinkIDHistory = $resourceLinkIDHistory;
        $this->contentTopicID = $contentTopicID;
    }
}
