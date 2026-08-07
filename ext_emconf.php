<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Klaro Cookie Consent',
    'description' => 'Cookie consent solution following the GDPR by using the solution Klaro',
    'category' => 'frontend',
    'constraints' => [
        'depends' => [
            'typo3' => '13.0.0-14.4.99',
        ],
        'conflicts' => [
        ],
    ],
    'autoload' => [
        'psr-4' => [
            'StudioMitte\\Klaro\\' => 'Classes'
        ],
    ],
    'state' => 'stable',
    'clearCacheOnLoad' => true,
    'version' => '4.0.0',
];
