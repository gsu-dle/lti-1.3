<?php

declare(strict_types=1);

namespace GAState\Web\LTI\Model;

interface MessageFactoryInterface
{
    public function createMessage(mixed $values): Message;
    public function createResourceLink(mixed $values): ResourceLink;
    public function createUserIdentity(mixed $values): UserIdentity;
    public function createContext(mixed $values): Context;
    public function createToolPlatform(mixed $values): ToolPlatform;
    public function createLaunchPresentation(mixed $values): LaunchPresentation;
    public function createLIS(mixed $values): LIS;
    public function createBrightspace(mixed $values): Brightspace;
    public function createAGS(mixed $values): AGS;
    public function createNRPS(mixed $values): NRPS;
}
