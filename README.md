# MiniMax H3 PHP SDK for RunAPI

[![Packagist](https://img.shields.io/packagist/v/runapi-ai/minimax-h3)](https://packagist.org/packages/runapi-ai/minimax-h3)
[![License](https://img.shields.io/github/license/runapi-ai/minimax-h3-php)](https://github.com/runapi-ai/minimax-h3-php/blob/main/LICENSE)

The MiniMax H3 PHP SDK is the language-specific package for MiniMax H3
on RunAPI. Use this package when your application needs Composer installs,
associative-array request bodies, task status lookup, and consistent RunAPI
errors in PHP.

This README is the PHP package guide for the public `minimax-h3-php` split
repository. For model details, use https://runapi.ai/models/minimax-h3; for API
reference, use https://runapi.ai/docs/api/minimax-h3/text-to-video; for SDK docs, use
https://runapi.ai/docs/resources/sdks.

## Install

```bash
composer require runapi-ai/minimax-h3
```

## Quick start

```php
<?php

require __DIR__ . "/vendor/autoload.php";

use RunApi\MinimaxH3\MinimaxH3Client;

$client = new MinimaxH3Client(); // reads RUNAPI_API_KEY

$imageToVideoTask = $client->imageToVideo->create([
    'model' => 'minimax-h3',
    'duration_seconds' => 4,
    'first_frame_image_url' => 'https://cdn.runapi.ai/public/samples/image.jpg',
    'last_frame_image_url' => 'https://cdn.runapi.ai/public/samples/image.jpg',
    'output_resolution' => '768p',
    'prompt' => 'Make it golden hour',
]);

$task = $client->textToVideo->create([
    'model' => 'minimax-h3',
    'aspect_ratio' => 'adaptive',
    'duration_seconds' => 4,
    'output_resolution' => '768p',
    'prompt' => 'A precise product render on white marble',
    'reference_audio_urls' => ['sample'],
    'reference_image_urls' => ['https://cdn.runapi.ai/public/samples/image.jpg'],
    'reference_video_urls' => ['sample'],
]);

$status = $client->textToVideo->get($task->id);

$result = $client->textToVideo->run([
    'model' => 'minimax-h3',
    'aspect_ratio' => 'adaptive',
    'duration_seconds' => 4,
    'output_resolution' => '768p',
    'prompt' => 'A serene mountain lake at dawn',
    'reference_audio_urls' => ['sample'],
    'reference_image_urls' => ['https://cdn.runapi.ai/public/samples/image.jpg'],
    'reference_video_urls' => ['sample'],
]);

echo $result->videos[0]->url . PHP_EOL;
```

Use `create()` to submit a task and return quickly, `get()` to fetch the latest
task state, and `run()` when a script should create and poll until completion.
In web request handlers, prefer `create()` plus webhook or later `get()`
polling so a worker is not held open.


RunAPI-generated file URLs are temporary. Download and store generated files
in your own durable storage within the retention window; do not treat returned
URLs as long-term assets.

## Language notes

Pass request parameters as associative arrays with snake_case keys. The
available resources are `textToVideo`, `imageToVideo`. Keep `RUNAPI_API_KEY` in the environment
or your secret manager; never commit API keys or callback secrets.

## Links

- Model page: https://runapi.ai/models/minimax-h3
- SDK docs: https://runapi.ai/docs/resources/sdks
- Product docs: https://runapi.ai/docs/api/minimax-h3/text-to-video
- Pricing and rate limits: https://runapi.ai/models/minimax-h3
- Full catalog: https://runapi.ai/models
- GitHub repository: https://github.com/runapi-ai/minimax-h3-php
- Multi-language SDK repository: https://github.com/runapi-ai/minimax-h3-sdk

## License

Licensed under the Apache License, Version 2.0.
