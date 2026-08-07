<?php

declare(strict_types=1);

namespace StudioMitte\Klaro\Hooks;

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Localization\LocalizationFactory;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Routing\PageArguments;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

class PageRendererHook
{

    protected PageRenderer $pageRenderer;

    private const FAKE_PREFIX = 'fake-klaro';
    private const CORE_FILE = 'EXT:klaro/Resources/Public/Klaro/klaro.js';

    /**
     * @param array<mixed> $params
     * @param PageRenderer $pageRenderer
     */
    public function preProcess(array $params, PageRenderer $pageRenderer): void
    {
        if (!$this->getCurrentRequest()?->getAttribute('frontend.page.information')) {
            return;
        }
        try {
            $site = $this->getCurrentSite();
            if ($site) {
                $siteConfiguration = $site->getConfiguration();
                if ($siteConfiguration['klaro_enable']) {
                    $this->pageRenderer = $pageRenderer;
                    if ($siteConfiguration['klaro_configuration_file']) {
                        $this->createTranslations($siteConfiguration);
                        $stylePrefix = (isset($siteConfiguration['klaro_style_prefix']) && !empty($siteConfiguration['klaro_style_prefix'])) ? self::FAKE_PREFIX : '';
                        $configFile = $siteConfiguration['klaro_core_js'] ?? self::CORE_FILE;

                        $pageRenderer->addJsFooterLibrary('klaro - config', $siteConfiguration['klaro_configuration_file'], 'application/javascript', false, false, '', false, '|', false, '', true);
                        $pageRenderer->addJsFooterLibrary('klaro - klaro', $configFile, 'application/javascript', false, false, '', false, '|', false, $stylePrefix, true);

                    }
                }
            }
        } catch (\RuntimeException $e) {
            // do nothing
        }
    }

    /**
     * @param array<mixed> $params
     * @param PageRenderer $pageRenderer
     */
    public function postProcess(array $params, PageRenderer $pageRenderer): void
    {
        if (!$this->getCurrentRequest()?->getAttribute('frontend.page.information')) {
            return;
        }
        $site = $this->getCurrentSite();
        if ($site) {
            $siteConfiguration = $site->getConfiguration();
            if ($siteConfiguration['klaro_enable'] && $siteConfiguration['klaro_configuration_file']) {
                $stylePrefix = $siteConfiguration['klaro_style_prefix'] ?? '';
                if ($stylePrefix) {
                    $params['jsFooterLibs'] = str_replace(' integrity="' . self::FAKE_PREFIX . '"', ' data-style-prefix="' . $stylePrefix . '"', $params['jsFooterLibs']);
                }
            }
        }
    }

    /**
     * @param array<mixed> $siteConfiguration
     * @throws \JsonException
     */
    protected function createTranslations(array $siteConfiguration): void
    {
        $currentLanguage = $this->pageRenderer->getLanguage() === 'default' ? 'en' : $this->pageRenderer->getLanguage();

        $privacyLinkConfiguration = '';
        $privacyPageId = $siteConfiguration['klaro_privacy_page'] ?? 0;
        $request = $this->getCurrentRequest();
        if ($privacyPageId && $request) {
            $cObj = GeneralUtility::makeInstance(ContentObjectRenderer::class);
            $cObj->setRequest($request);
            $privacyLink = $cObj->typoLink_URL([
                'parameter' => $privacyPageId,
                'forceAbsoluteUrl' => true,
            ]);
            $privacyLinkConfiguration = chr(10) . ',privacyPolicy: ' . GeneralUtility::quoteJSvalue($privacyLink);
        }

        $languageFile = $siteConfiguration['klaro_language_file'] ?? 'EXT:klaro/Resources/Private/Language/klaro.xlf';
        $this->pageRenderer->addJsInlineCode('klaro-trans',
            'var klaroConfigTranslations = {
                lang: ' . GeneralUtility::quoteJSvalue($currentLanguage) . ',
                ' . ((bool)($siteConfiguration['klaro_must_consent'] ?? false) ? 'mustConsent:true,' : '') . '
                translations: {
                    ' . $currentLanguage . ': ' . $this->includeLanguageFileForInline($languageFile) . '
                }' . $privacyLinkConfiguration . '
             };
        ', true);

        $hiddenOnPages = $siteConfiguration['klaro_hidden_on_pages'] ?? '';
        if ($hiddenOnPages && $pageId = $this->getCurrentPageId()) {
            if (in_array($pageId, GeneralUtility::intExplode(',', $hiddenOnPages), true)) {
                $this->pageRenderer->addJsInlineCode('hideKlaro', "
                    document.addEventListener('DOMContentLoaded', function() {
                        const klaroModal = document.querySelector('#klaro');
                        klaroModal.setAttribute('style', 'display:none;')

                    });
               ");
            }
        }
    }

    protected function includeLanguageFileForInline(string $fileRef): string
    {
        if (!$this->pageRenderer->getLanguage()) {
            throw new \RuntimeException('Language and character encoding are not set . ', 1575359889);
        }

        $final = [];
        foreach ($this->readLocallangFile($fileRef) as $key => $value) {
            $this->setArray($final, $key, (string)$value);
        }
        return json_encode($final, JSON_THROW_ON_ERROR);
    }

    /**
     * @param array<mixed> $array
     * @param string $keys
     * @param string $value
     */
    protected function setArray(array &$array, string $keys, string $value): void
    {
        $keys = explode('.', $keys);
        $current = &$array;
        foreach ($keys as $key) {
            $current = &$current[$key];
        }
        $current = $value;
    }

    /**
     * @param string $fileRef
     * @return array<string, mixed> Flat key => label map, fallback to English/default already applied.
     */
    protected function readLocallangFile(string $fileRef): array
    {
        $lang = $this->pageRenderer->getLanguage();
        $languageFactory = GeneralUtility::makeInstance(LocalizationFactory::class);
        $data = $languageFactory->getParsedData($fileRef, $lang);

        if ((new Typo3Version())->getMajorVersion() >= 14) {
            // TYPO3 v14+: LocalizationFactory is Symfony Translator based and returns
            // a flat key => label map for the requested language, fallback already applied.
            return $data;
        }

        // TYPO3 v13 and earlier: legacy LOCAL_LANG format, nested by language key,
        // each label wrapped as [0]['target'].
        $labels = $data['default'] ?? [];
        if ($lang !== 'default' && isset($data[$lang])) {
            // Merge current language labels onto labels from previous language
            // This way we have a labels with fall back applied
            ArrayUtility::mergeRecursiveWithOverrule($labels, $data[$lang], true, false);
        }

        $flat = [];
        foreach ($labels as $key => $entry) {
            $flat[$key] = $entry[0]['target'];
        }
        return $flat;
    }

    protected function getCurrentSite(): ?Site
    {
        $site = $this->getCurrentRequest()?->getAttribute('site');
        return $site instanceof Site ? $site : null;
    }

    protected function getCurrentPageId(): int
    {
        $pageId = 0;
        $pageArguments = $this->getCurrentRequest()?->getAttribute('routing');
        if ($pageArguments instanceof PageArguments) {
            $pageId = $pageArguments->getPageId();
        }
        return $pageId;
    }

    protected function getCurrentRequest(): ?ServerRequestInterface
    {
        return $GLOBALS['TYPO3_REQUEST'] instanceof ServerRequestInterface ? $GLOBALS['TYPO3_REQUEST'] : null;
    }

}
