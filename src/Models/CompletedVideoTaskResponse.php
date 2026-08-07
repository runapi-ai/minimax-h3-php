<?php

declare(strict_types=1);

namespace RunApi\MinimaxH3\Models;

use RunApi\Core\Support\Payload;

/** Completed video task response returned by `run()`; videos are guaranteed present. */
readonly class CompletedVideoTaskResponse extends VideoTaskResponse
{
    /**
     * Hydrate a completed task response and require generated videos.
     *
     * @param array<string, mixed> $raw
     */
    public static function fromArray(array $raw): self
    {
        return new self(id: Payload::string($raw, 'id'), status: Payload::string($raw, 'status'), error: self::error($raw), videos: self::videos($raw, required: true), raw: $raw);
    }

    /** Narrow a polled task response after completion has been confirmed. */
    public static function fromResponse(VideoTaskResponse $response): self
    {
        return self::fromArray($response->toArray());
    }
}
