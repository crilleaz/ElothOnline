<?php

declare(strict_types=1);

namespace Game\Chat;

readonly class ChatMessage
{
    public \DateTimeInterface $sentAt;

    public function __construct(public string $sender, public string $content, int $timestamp, public bool $isFromAdmin = false)
    {
        $this->sentAt = (new \DateTimeImmutable())->setTimestamp($timestamp);
    }
}
