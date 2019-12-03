<?php

declare(strict_types=1);

namespace StudioMitte\Klaro\Hooks;

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Localization\LocalizationFactory;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\Entity\SiteInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

class PageRendererHook
{

    /** @var PageRenderer */
    protected $pageRenderer;

    public function run(array $params, PageRenderer $pageRenderer)
    {
        $site = $this->getCurrentSite();
        if ($site && $tsfe = $this->getTypoScriptFrontendController()) {
            $siteConfiguration = $site->getConfiguration();
            if ($siteConfiguration['klaro_enable']) {
                $this->pageRenderer = $pageRenderer;
                if ($siteConfiguration['klaro_configuration_file']) {
                    $path = 'EXT:klaro/Resources/Public/Klaro/';

                    $this->createTranslations($siteConfiguration);
                    $pageRenderer->addJsFooterLibrary('klaro - config', $siteConfiguration['klaro_configuration_file'], 'application/javascript', false, false, '', false, '|', false, '', true);
                    $pageRenderer->addJsFooterLibrary('klaro - klaro', $path . 'klaro.js', 'application/javascript', false, false, '', false, '|', false, '', true);
                }
            }
        }
    }

    protected function createTranslations(array $siteConfiguration): void
    {
        $currentLanguage = $this->pageRenderer->getLanguage() === 'default' ? 'en' : $this->pageRenderer->getLanguage();

        $privacyLinkConfiguration = '';
        $privacyPageId = $siteConfiguration['klaro_privacy_page'] ?? 0;
        if ($privacyPageId) {
            $privacyLink = $this->getTypoScriptFrontendController()->cObj->typoLink_URL([
                'parameter' => $privacyPageId,
                'forceAbsoluteUrl' => true,
            ]);
            $privacyLinkConfiguration = LF . ',privacyPolicy: ' . GeneralUtility::quoteJSvalue($privacyLink);
        }

        $languageFile = $siteConfiguration['klaro_language_file'] ?? 'EXT:klaro/Resources/Private/Language/klaro.xlf';
        $this->pageRenderer->addJsInlineCode('klaro-trans',
            'var klaroConfigTranslations = {
                lang: ' . GeneralUtility::quoteJSvalue($currentLanguage) . ',
                translations: {
                    ' . $currentLanguage . ': ' . $this->includeLanguageFileForInline($languageFile) . '
                }' . $privacyLinkConfiguration . '
             };
        ', true);
    }

    protected function includeLanguageFileForInline($fileRef)
    {
        if (!$this->pageRenderer->getLanguage() || !$this->pageRenderer->getCharSet()) {
            throw new \RuntimeException('Language and character encoding are not set . ', 1575359889);
        }
        $labelsFromFile = [];
        $allLabels = $this->readLLfile($fileRef);
        if ($allLabels !== false) {
            // Merge language specific translations:
            if ($this->pageRenderer->getLanguage() !== 'default' && isset($allLabels[$this->pageRenderer->getLanguage()])) {
                $labels = array_merge($allLabels['default'], $allLabels[$this->pageRenderer->getLanguage()]);
            } else {
                $labels = $allLabels['default'];
            }
            // Iterate through all locallang labels:
            foreach ($labels as $label => $value) {
                // If $selectionPrefix is set, only respect labels that start with $selectionPrefix
                $labelsFromFile[$label] = $value;
            }
        }

        $final = [];
        foreach ($labelsFromFile as $key => $entry) {
            $this->setArray($final, $key, $entry[0]['target']);
        }
        return json_encode($final);
    }

    function setArray(array &$array, $keys, $value)
    {
        $keys = explode('.', $keys);
        $current = &$array;
        foreach ($keys as $key) {
            $current = &$current[$key];
        }
        $current = $value;
    }

    protected function readLLfile($fileRef)
    {
        $lang = $this->pageRenderer->getLanguage();
        /** @var LocalizationFactory $languageFactory */
        $languageFactory = GeneralUtility::makeInstance(LocalizationFactory::class);

        if ($this->pageRenderer->getLanguage() !== 'default') {
//            $languages = array_reverse($this->languageDependencies);
            $languages = [$lang];
            // At least we need to have English
            if (empty($languages)) {
                $languages[] = 'default';
            }
        } else {
            $languages = ['default'];
        }

        $localLanguage = [];
        foreach ($languages as $language) {
            $tempLL = $languageFactory->getParsedData($fileRef, $language);

            $localLanguage['default'] = $tempLL['default'];
            if (!isset($localLanguage[$lang])) {
                $localLanguage[$lang] = $localLanguage['default'];
            }
            if ($lang !== 'default' && isset($tempLL[$language])) {
                // Merge current language labels onto labels from previous language
                // This way we have a labels with fall back applied
                \TYPO3\CMS\Core\Utility\ArrayUtility::mergeRecursiveWithOverrule($localLanguage[$lang], $tempLL[$language], true, false);
            }
        }

        return $localLanguage;
    }

    /**
     * Returns the currently configured site if a site is configured (= resolved) in the current request.
     */
    protected function getCurrentSite(): ?SiteInterface
    {
        if ($GLOBALS['TYPO3_REQUEST'] instanceof ServerRequestInterface
            && $GLOBALS['TYPO3_REQUEST']->getAttribute('site') instanceof Site) {
            return $GLOBALS['TYPO3_REQUEST']->getAttribute('site');
        }
        return null;
    }

    /**
     * @return TypoScriptFrontendController
     */
    protected function getTypoScriptFrontendController(): ?TypoScriptFrontendController
    {
        return $GLOBALS['TSFE'];
    }

}
