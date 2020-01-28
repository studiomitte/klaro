<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Klaro Cookie Consent',
    'description' => 'Cookie consent solution following the GDPR by using the solution Klaro',
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
    'clearCacheOnLoad' => true,
    'version' => '2.0.1',
];
