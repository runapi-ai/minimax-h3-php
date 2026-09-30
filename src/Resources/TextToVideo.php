<?php

declare(strict_types=1);

namespace RunApi\MinimaxH3\Resources;

use RunApi\Core\Http\HttpClient;
use RunApi\Core\Models\TaskCreateResponse;
use RunApi\Core\RequestOptions;
use RunApi\Core\Resources\TypedConfiguredResource;
use RunApi\MinimaxH3\Models\CompletedVideoTaskResponse;
use RunApi\MinimaxH3\Models\VideoTaskResponse;

/** Text to video operations for MiniMax H3. */
readonly class TextToVideo extends TypedConfiguredResource
{
    /**
     * Create a text to video task and return immediately with a task id.
     *
     * @param array{
     *   model: string,
     *   prompt: string,
     *   aspect_ratio?: string,
     *   callback_url?: string,
     *   duration_seconds?: int,
     *   output_resolution?: string,
     *   reference_audio_urls?: list<string>,
     *   reference_image_urls?: list<string>,
     *   reference_video_urls?: list<string>
     * } $params
     */
    public function create(array $params, ?RequestOptions $options = null): TaskCreateResponse
    {
        return parent::create($params, $options);
    }

    /** Fetch the current status of a text to video task. */
    public function get(string $id, ?RequestOptions $options = null): VideoTaskResponse
    {
        $response = parent::get($id, $options);

        /** @var VideoTaskResponse $response */
        return $response;
    }

    /**
     * Create a text to video task and poll until it completes.
     *
     * @param array{
     *   model: string,
     *   prompt: string,
     *   aspect_ratio?: string,
     *   callback_url?: string,
     *   duration_seconds?: int,
     *   output_resolution?: string,
     *   reference_audio_urls?: list<string>,
     *   reference_image_urls?: list<string>,
     *   reference_video_urls?: list<string>
     * } $params
     */
    public function run(array $params, ?RequestOptions $options = null): CompletedVideoTaskResponse
    {
        $response = parent::run($params, $options);

        /** @var CompletedVideoTaskResponse $response */
        return $response;
    }

    /** Create the resource using the shared RunAPI HTTP transport. */
    public static function fromHttp(HttpClient $http): self
    {
        return new self(
            $http,
            '/api/v1/minimax_h3/text_to_video',
            VideoTaskResponse::class,
            CompletedVideoTaskResponse::class,
            'text-to-video',
            VideoTaskResponse::class,
            CompletedVideoTaskResponse::class,
        );
    }
}
