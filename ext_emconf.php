<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Klaro Cookie Consent',
    'description' => 'Cookie consent solution following the GDPR by using the solution Klaro',
    'category' => 'frontend',
    'constraints' => [
        'depends' => [
            'typo3' => '9.5.0-12.9.99',
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
    'version' => '3.2.0',
];
