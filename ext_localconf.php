<?php
$boot = static function () {
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_pagerenderer.php']['render-preProcess']['klaro']
        = \StudioMitte\Klaro\Hooks\PageRendererHook::class . '->preProcess';

    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_pagerenderer.php']['render-postProcess']['klaro']
        = \StudioMitte\Klaro\Hooks\PageRendererHook::class . '->postProcess';
};

$boot();
unset($boot);
