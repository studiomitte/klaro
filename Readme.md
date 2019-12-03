# TYPO3 Extension `klaro`

Cookie consent solution using [klaro](https://klaro.kiprotect.com/)

## Usage

1. Install Extension, e.g. using `composer require studiomitte/klaro`
2. Setup configuration in sites module
3. Adopt configuration to your needs

### Configuration

See https://klaro.kiprotect.com/#getting-started, more later

#### Inline code

```
    <!-- Google Tag Manager -->
    <script type="opt-in" data-type="application/javascript" data-name="googleAnalytics">(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','###CODE###');</script>
    <!-- End Google Tag Manager -->
```

### Privacy Page 

Use the following pseudo-links

- Consent Link: `https://KLARO_CONSENT.com`
- Reset Link: `https://KLARO_RESET.com`

