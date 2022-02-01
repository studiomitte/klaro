var klaroConfig = {
    cookieExpiresAfterDays: 365,
    // Put a link to your privacy policy here (relative or absolute).
    privacyPolicy: '/#privacy',
    default: true,
    mustConsent: false,
    lang: 'de',

    // You can overwrite existing translations and add translations for your
    // app descriptions and purposes. See `src/translations/` for a full
    // list of translations that can be overwritten:
    // https://github.com/KIProtect/klaro/tree/master/src/translations

    // Example config that shows how to overwrite translations:
    // https://github.com/KIProtect/klaro/blob/master/src/configs/i18n.js
    translations: {
        // If you erase the "consentModal" translations, Klaro will use the
        // bundled translations.
        de: {
            consentModal: {
                title: 'Dies ist der Titel des Zustimmungs-Dialogs',
                description:
                    'Hier können Sie einsehen und anpassen, welche Information wir über Sie sammeln. Einträge die als "Beispiel" gekennzeichnet sind dienen lediglich zu Demonstrationszwecken und werden nicht wirklich verwendet.',
            },
            privacyPolicy: {
                text:
                    'Dies ist der Text mit einem Link zu Ihrer {privacyPolicy}.',
                name: 'Datenschutzerklärung (Name)',
            },
            poweredBy: '',
            ok: "Los geht's!",
            close: "Speichern",
            inlineTracker: {
                description: 'Beispiel für ein Inline-Tracking Skript',
            },
            externalTracker: {
                description: 'Beispiel für ein externes Tracking Skript',
            },
            adsense: {
                description: 'Anzeigen von Werbeanzeigen (Beispiel)',
            },
            matomo: {
                description: 'Sammeln von Besucherstatistiken',
            },
            gtm: {
                description: 'Sammeln von Besucherstatistiken',
            },
            cloudflare: {
                description: 'Schutz gegen DDoS-Angriffe',
            },
            googleFonts: {
                description: 'Web-Schriftarten von Google gehostet',
            },
            purposes: {
                analytics: 'Besucher-Statistiken',
                security: 'Sicherheit',
                livechat: 'Live Chat',
                advertising: 'Anzeigen von Werbung',
                styling: 'Styling',
            },
        }
    },

    // This is a list of third-party apps that Klaro will manage for you.
    services: [
        {
            name: 'matomo',
            title: 'Matomo/Piwik',
            purposes: ['analytics'],
            cookies: [
                [/^_pk_.*$/, '/', 'klaro.kiprotect.com'], //for the production version
                [/^_pk_.*$/, '/', 'localhost'], //for the local version
                'piwik_ignore',
            ]
        },
        {
            name: 'gtm',
            title: 'Google Tag manager',
            purposes: ['analytics'],
            cookies: ['gtm'],
        },
        {
            name: 'inlineTracker',
            title: 'Inline Tracker',
            purposes: ['analytics'],
            cookies: ['inline-tracker'],
        },
        {
            name: 'externalTracker',
            title: 'External Tracker',
            purposes: ['analytics', 'security'],
            cookies: ['external-tracker'],
        },
        {
            name: 'googleFonts',
            title: 'Google Fonts',
            purposes: ['styling'],
        },
        {
            name: 'cloudflare',
            title: 'Cloudflare',
            purposes: ['security'],
            required: true,
        },
    ],
};

if (typeof klaroConfigTranslations !== 'undefined') {
    for (var attr in klaroConfigTranslations) {
        klaroConfig[attr] = klaroConfigTranslations[attr];
    }
}
