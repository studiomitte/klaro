<?php
defined('TYPO3_MODE') or die();

$boot = static function () {
    if (TYPO3_MODE === 'FE') {
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_pagerenderer.php']['render-preProcess']['klaro']
            = \StudioMitte\Klaro\Hooks\PageRendererHook::class . '->run';

        // Hook for changing output before showing it
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_fe.php']['contentPostProc-output']['klaro']
            = \StudioMitte\Klaro\Hooks\ContentPostProcAll::class . '->run';

    }
};

$boot();
unset($boot);
