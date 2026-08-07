<?php

declare(strict_types=1);

namespace RunApi\MinimaxH3\Tests\Unit;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use RunApi\Core\ClientOptions;
use RunApi\Core\Errors\ValidationException;
use RunApi\Core\Resources\Pricing;
use RunApi\Core\Tests\Fixtures\QueueHttpClient;
use RunApi\MinimaxH3\MinimaxH3Client;
use RunApi\MinimaxH3\Models\CompletedVideoTaskResponse;
use RunApi\MinimaxH3\Resources\ImageToVideo;
use RunApi\MinimaxH3\Resources\TextToVideo;

final class MinimaxH3ClientTest extends TestCase
{
    public function testRejectsAudioOnlyReferences(): void
    {
        $client = new MinimaxH3Client(new ClientOptions(apiKey: 'k', httpClient: new QueueHttpClient([]), maxRetries: 0));

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('one of reference_image_urls, reference_video_urls is required');

        $client->textToVideo->create([
            'model' => 'minimax-h3',
            'prompt' => 'Continue the performance',
            'reference_audio_urls' => ['https://cdn.runapi.ai/public/samples/voice.mp3'],
        ]);
    }

    public function testRejectsAdaptiveAspectRatioWithoutReferenceMedia(): void
    {
        $client = new MinimaxH3Client(new ClientOptions(apiKey: 'k', httpClient: new QueueHttpClient([]), maxRetries: 0));

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('aspect_ratio must be one of:');

        $client->textToVideo->create([
            'model' => 'minimax-h3',
            'prompt' => 'A cinematic landscape',
            'aspect_ratio' => 'adaptive',
        ]);
    }

    public function testRequiresAspectRatioWithoutReferenceMedia(): void
    {
        $client = new MinimaxH3Client(new ClientOptions(apiKey: 'k', httpClient: new QueueHttpClient([]), maxRetries: 0));

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('aspect_ratio is required when reference_image_urls is absent and reference_video_urls is absent');

        $client->textToVideo->create([
            'model' => 'minimax-h3',
            'prompt' => 'A cinematic landscape',
        ]);
    }

    public function testRequiresFirstOrLastFrame(): void
    {
        $client = new MinimaxH3Client(new ClientOptions(apiKey: 'k', httpClient: new QueueHttpClient([]), maxRetries: 0));

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('one of first_frame_image_url, last_frame_image_url is required');

        $client->imageToVideo->create([
            'model' => 'minimax-h3',
            'prompt' => 'Animate the scene',
        ]);
    }

    public function testExposesTypedResources(): void
    {
        $client = new MinimaxH3Client(new ClientOptions(apiKey: 'k', httpClient: new QueueHttpClient([]), maxRetries: 0));

        self::assertInstanceOf(TextToVideo::class, $client->textToVideo);
        self::assertInstanceOf(ImageToVideo::class, $client->imageToVideo);
        self::assertInstanceOf(Pricing::class, $client->pricing);
    }

    public function testCreatePostsCompactedBodyToCorrectPath(): void
    {
        $transport = new QueueHttpClient([
            new Response(200, [], '{"id":"task_1"}'),
        ]);
        $client = new MinimaxH3Client(new ClientOptions(apiKey: 'k', httpClient: $transport, maxRetries: 0));

        $task = $client->textToVideo->create([
            'model' => 'minimax-h3',
            'prompt' => 'A product render',
            'duration_seconds' => 4,
            'output_resolution' => '768p',
            'aspect_ratio' => 'adaptive',
            'reference_image_urls' => ['https://cdn.runapi.ai/public/samples/image.jpg'],
            'reference_video_urls' => ['https://cdn.runapi.ai/public/samples/video.mp4'],
            'reference_audio_urls' => ['https://cdn.runapi.ai/public/samples/voice.mp3'],
            'callback_url' => '',
            'seed' => null,
        ]);

        $body = json_decode((string) $transport->requests[0]->getBody(), true, flags: JSON_THROW_ON_ERROR);

        self::assertSame('task_1', $task->id);
        self::assertSame('/api/v1/minimax_h3/text_to_video', $transport->requests[0]->getUri()->getPath());
        self::assertSame('minimax-h3', $body['model']);
        self::assertArrayNotHasKey('callback_url', $body);
        self::assertArrayNotHasKey('seed', $body);
    }

    public function testRunReturnsTypedCompletedResponseAndPreservesUnknownFields(): void
    {
        $transport = new QueueHttpClient([
            new Response(200, [], '{"id":"task_1"}'),
            new Response(200, [], '{"id":"task_1","status":"completed","videos":[{"url":"https://file.runapi.ai/result"}],"generation_stage":"all_audios_ready","extra_field":"kept"}'),
        ]);
        $client = new MinimaxH3Client(new ClientOptions(apiKey: 'k', httpClient: $transport, maxRetries: 0));

        $result = $client->textToVideo->run([
            'model' => 'minimax-h3',
            'prompt' => 'A product render',
            'duration_seconds' => 4,
            'output_resolution' => '768p',
            'aspect_ratio' => 'adaptive',
            'reference_image_urls' => ['https://cdn.runapi.ai/public/samples/image.jpg'],
            'reference_video_urls' => ['https://cdn.runapi.ai/public/samples/video.mp4'],
            'reference_audio_urls' => ['https://cdn.runapi.ai/public/samples/voice.mp3'],
        ]);

        self::assertInstanceOf(CompletedVideoTaskResponse::class, $result);
        self::assertSame('https://file.runapi.ai/result', $result->videos[0]->url);
        self::assertSame('kept', $result->toArray()['extra_field']);
        self::assertSame('/api/v1/minimax_h3/text_to_video/task_1', $transport->requests[1]->getUri()->getPath());
    }

    public function testCompletedResponseRequiresResultFiles(): void
    {
        $transport = new QueueHttpClient([
            new Response(200, [], '{"id":"task_1"}'),
            new Response(200, [], '{"id":"task_1","status":"completed"}'),
        ]);
        $client = new MinimaxH3Client(new ClientOptions(apiKey: 'k', httpClient: $transport, maxRetries: 0));

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('videos is required');

        $client->textToVideo->run([
            'model' => 'minimax-h3',
            'prompt' => 'A product render',
            'duration_seconds' => 4,
            'output_resolution' => '768p',
            'aspect_ratio' => 'adaptive',
            'reference_image_urls' => ['https://cdn.runapi.ai/public/samples/image.jpg'],
            'reference_video_urls' => ['https://cdn.runapi.ai/public/samples/video.mp4'],
            'reference_audio_urls' => ['https://cdn.runapi.ai/public/samples/voice.mp3'],
        ]);
    }

    public function testRejectsInvalidContractEnum(): void
    {
        $client = new MinimaxH3Client(new ClientOptions(apiKey: 'k', httpClient: new QueueHttpClient([]), maxRetries: 0));

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('duration_seconds must be one of the allowed values');

        $client->textToVideo->create([
        'model' => 'minimax-h3',
        'prompt' => 'A product render',
        'output_resolution' => '768p',
        'aspect_ratio' => 'adaptive',
        'reference_image_urls' => ['https://cdn.runapi.ai/public/samples/image.jpg'],
        'reference_video_urls' => ['https://cdn.runapi.ai/public/samples/video.mp4'],
        'reference_audio_urls' => ['https://cdn.runapi.ai/public/samples/voice.mp3'],
        'duration_seconds' => 1,
        ]);
    }


    public function testSecondaryResourceUsesItsOwnPath(): void
    {
        $transport = new QueueHttpClient([
            new Response(200, [], '{"id":"task_2"}'),
        ]);
        $client = new MinimaxH3Client(new ClientOptions(apiKey: 'k', httpClient: $transport, maxRetries: 0));

        $client->imageToVideo->create([
            'model' => 'minimax-h3',
            'prompt' => 'A product render',
            'first_frame_image_url' => 'https://cdn.runapi.ai/public/samples/image.jpg',
            'last_frame_image_url' => 'https://cdn.runapi.ai/public/samples/image.jpg',
            'duration_seconds' => 4,
            'output_resolution' => '768p',
        ]);

        self::assertSame('/api/v1/minimax_h3/image_to_video', $transport->requests[0]->getUri()->getPath());
    }
}
