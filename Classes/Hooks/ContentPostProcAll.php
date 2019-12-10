<?php

declare(strict_types=1);

namespace StudioMitte\Klaro\Hooks;

class ContentPostProcAll
{
    /** @var array */
    protected $simpleSearchReplacements = [
        'href="https://KLARO_CONSENT.com"' => 'href="#" onClick="return klaro.show(klaroConfig)"',
        'href="https://KLARO_RESET.com"' => 'href="#" onClick="klaro.getManager().resetConsent();location.reload()"',
    ];

    /**
     * @param array $parameters
     */
    public function run(array &$parameters): void
    {
        $parameters['pObj']->content = $this->simpleReplacements($parameters['pObj']->content);
    }

    /**
     * Simple string replacements
     *
     * @param string $searchText
     * @return string
     */
    protected function simpleReplacements(string $searchText): string
    {
        return str_replace(array_keys($this->simpleSearchReplacements), array_values($this->simpleSearchReplacements), $searchText);
    }
}
