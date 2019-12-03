<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(
    function ($table) {
        $lll = 'LLL:EXT:klaro/Resources/Private/Language/Configuration.xlf:';
        $GLOBALS['SiteConfiguration'][$table]['columns']['klaro_enable'] = [
            'label' => $lll . 'site.configuration.enable',
            'config' => [
                'type' => 'check',
                'default' => 0,
            ],
        ];
        $GLOBALS['SiteConfiguration'][$table]['columns']['klaro_privacy_page'] = [
            'label' => $lll . 'site.configuration.privacy_page',
            'config' => [
                'type' => 'input',
                'eval' => 'int'
            ],
        ];
        $GLOBALS['SiteConfiguration'][$table]['columns']['klaro_configuration_file'] = [
            'label' => $lll . 'site.configuration.configuration_file',
            'config' => [
                'type' => 'input',
                'placeholder' => 'EXT:klaro/Resources/Public/Example/configuration.js',
            ],
        ];
        $GLOBALS['SiteConfiguration'][$table]['columns']['klaro_language_file'] = [
            'label' => $lll . 'site.configuration.language_file',
            'config' => [
                'type' => 'input',
                'placeholder' => 'EXT:klaro/Resources/Private/Language/klaro.xlf',
                'default' => 'EXT:klaro/Resources/Private/Language/klaro.xlf',
            ],
        ];

        $GLOBALS['SiteConfiguration'][$table]['types']['0']['showitem'] .= '
            ,--div--;' . $lll . 'site.configuration.tab, klaro_enable,klaro_privacy_page,klaro_configuration_file,klaro_language_file,
         ';
    },
    'site'
);
