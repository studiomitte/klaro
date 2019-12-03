var klaroConfig = {
    cookieExpiresAfterDays: 365,
    default: true,
    mustConsent: false,

    apps: [
        {
            name: 'googleAnalytics',
            title: 'Google Tag Manager',
            purposes: ['analytics'],
            cookies: ['gtm'],
        }
    ],
};
if (typeof klaroConfigTranslations !== 'undefined') {
    for (var attr in klaroConfigTranslations) {
        klaroConfig[attr] = klaroConfigTranslations[attr];
    }
}