<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Klaro',
    'description' => '',
    'category' => 'frontend',
    'constraints' => [
        'depends' => [
            'typo3' => '9.5.0-9.5.99',
        ],
        'conflicts' => [
        ],
    ],
    'autoload' => [
        'psr-4' => [
            'StudioMitte\\Klaro\\' => 'Classes'
        ],
    ],
    'state' => 'beta',
    'uploadfolder' => 0,
    'clearCacheOnLoad' => 1,
    'version' => '1.0.0',
];
