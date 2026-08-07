<?php

declare(strict_types=1);

namespace RunApi\MinimaxH3\Models;

use RunApi\Core\Models\TaskResponse;
use RunApi\Core\Support\Payload;

/** Async video task response with lifecycle status and output videos. */
readonly class VideoTaskResponse extends TaskResponse
{
    /**
     * @param list<Video> $videos Generated video files when the task has completed.

     * @param array<string, mixed> $raw Raw response payload preserved by `toArray()`.
     */
    public function __construct(?string $id, string $status, ?string $error = null, public array $videos = [], array $raw = [])
    {
        parent::__construct(id: $id, status: $status, error: $error, raw: $raw === [] ? ['id' => $id, 'status' => $status, 'error' => $error, 'videos' => array_map(static fn (Video $video): array => $video->toArray(), $videos)] : $raw);
    }

    /**
     * Hydrate a task status response from a RunAPI response object.
     *
     * @param array<string, mixed> $raw
     */
    public static function fromArray(array $raw): self
    {
        return new self(id: Payload::string($raw, 'id'), status: Payload::string($raw, 'status'), error: self::error($raw), videos: self::videos($raw), raw: $raw);
    }

    /**
     * @param array<string, mixed> $raw
     *
     * @return list<Video>
     */
    protected static function videos(array $raw, bool $required = false): array
    {
        return Payload::listOf($raw, 'videos', Video::fromArray(...), $required);
    }


}
