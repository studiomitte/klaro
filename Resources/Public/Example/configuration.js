var klaroConfig = {
    disablePoweredBy: true,
    cookieExpiresAfterDays: 365,
    default: true,
    mustConsent: false,

    services: [
        {
            name: 'googleAnalytics',
            title: 'Google Analytics',
            purposes: ['analytics'],
            cookies: [/^ga/i],
            callback: function (consent, app) {
                if (consent !== false) {
                  window.dataLayer = window.dataLayer || [];
                  window.dataLayer.push({'event': 'loadgtm-analytics'})
                }
            },
        },
        {
            name: 'googleTagManager',
            title: 'Google Tag Manager',
            purposes: ['analytics'],
            required: true
        }
    ],
};
if (typeof klaroConfigTranslations !== 'undefined') {
    for (var attr in klaroConfigTranslations) {
        klaroConfig[attr] = klaroConfigTranslations[attr];
    }
}
