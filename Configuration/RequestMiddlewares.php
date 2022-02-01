<?php

declare(strict_types=1);

return [
    'frontend' => [
        'typo3/klaro/replace-content' => [
            'target' => \StudioMitte\Klaro\Middleware\ReplaceBeforeOutput::class,
            'before' => [
                'typo3/cms-frontend/output-compression',
                'typo3/cms-frontend/content-length-headers',
            ],
            'after' => [
                'typo3/cms-frontend/tsfe',
            ],
        ],
    ],
];
