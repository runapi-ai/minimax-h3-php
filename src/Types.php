<?php

declare(strict_types=1);

namespace RunApi\MinimaxH3;

final class Types
{
    /**
     * Allowed model slugs for text to video requests.
     *
     * @var list<string>
     */
    public const TEXT_TO_VIDEO_MODELS = ['minimax-h3'];

    /**
     * Allowed model slugs for image to video requests.
     *
     * @var list<string>
     */
    public const IMAGE_TO_VIDEO_MODELS = ['minimax-h3'];

    private function __construct()
    {
    }
}
