<?php

declare(strict_types=1);

namespace RunApi\MinimaxH3;

use RunApi\Core\BaseClient;
use RunApi\Core\ClientOptions;
use RunApi\MinimaxH3\Resources\ImageToVideo;
use RunApi\MinimaxH3\Resources\TextToVideo;

/**
 * MiniMax H3 RunAPI PHP client.
 *
 * The client exposes typed model resources plus the universal `files` and
 * `account` resources.
 */
final class MinimaxH3Client extends BaseClient
{
    /** Text to video operations for MiniMax H3. */
    public readonly TextToVideo $textToVideo;
    /** Image to video operations for MiniMax H3. */
    public readonly ImageToVideo $imageToVideo;

    /** Create a MiniMax H3 client with optional API key, base URL, and transport overrides. */
    public function __construct(ClientOptions $options = new ClientOptions())
    {
        parent::__construct($options);
        $this->textToVideo = TextToVideo::fromHttp($this->http);
        $this->imageToVideo = ImageToVideo::fromHttp($this->http);
    }
}
