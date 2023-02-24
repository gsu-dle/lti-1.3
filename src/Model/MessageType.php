<?php

declare(strict_types=1);

namespace GAState\Web\LTI\Model;

enum MessageType: string
{
    case LtiDeepLinkingRequest = "LtiDeepLinkingRequest";
    case LtiResourceLinkRequest = "LtiResourceLinkRequest";
    case LtiSubmissionReviewRequest = "LtiSubmissionReviewRequest";
}
