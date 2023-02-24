<?php

declare(strict_types=1);

namespace GAState\Web\LTI\Model;

/**
 * Composes properties that describe aspects of how the message sender expects to host the presentation of the message
 * receiver's user experience (for example, the height and width of the viewport the message sender gives over to the
 * message receiver).
 *
 * @see https://www.imsglobal.org/spec/lti/v1p3#launch-presentation-claim
 */
class LaunchPresentation
{
    /**
     * The kind of browser window or frame from which the user launched inside the message sender's system. The value
     * for this property MUST be one of: frame, iframe, or window.
     *
     * @var ?string $documentTarget
     */
    public readonly ?string $documentTarget;


    /**
     * Height of the window or frame where the content from the message receiver will be displayed to the user.
     *
     * @var ?int $height
     */
    public readonly ?int $height;


    /**
     * Width of the window or frame where the content from the message receiver will be displayed to the user.
     *
     * @var ?int $width
     */
    public readonly ?int $width;


    /**
     * Fully-qualified HTTPS URL within the message sender's user experience to where the message receiver can redirect
     * the user back. The message receiver can redirect to this URL after the user has finished activity, or if the
     * receiver cannot start because of some technical difficulty.
     *
     * The message receiver may want to send back a message to the message sender. If the message sender includes a
     * return_url in its launch_presentation, it MUST support these four query parameters that MAY be parameterize the
     * redirection to the return URL:
     * - lti_errormsg, lti_msg. Use these query parameters to carry a user-targeted message for unsuccessful or
     *   successful (respectively) activity completion. These are intended for showing to the user.
     * - lti_errorlog, lti_log. Use these query parameters to carry a log-targeted message for unsuccessful or
     *   successful (respectively) activity completion. These are intended for writing to logs.
     *
     * @var ?string $returnURL
     */
    public readonly ?string $returnURL;


    /**
     * Language, country, and variant as represented using the IETF Best Practices for Tags for Identifying Languages
     *
     * @var ?string $locale
     */
    public readonly ?string $locale;


    /**
     * @param ?string $documentTarget
     * @param ?int $height
     * @param ?int $width
     * @param ?string $returnURL
     * @param ?string $locale
     */
    public function __construct(
        ?string $documentTarget = null,
        ?int $height = null,
        ?int $width = null,
        ?string $returnURL = null,
        ?string $locale = null
    ) {
        $this->documentTarget = $documentTarget;
        $this->height = $height;
        $this->width = $width;
        $this->returnURL = $returnURL;
        $this->locale = $locale;
    }
}
