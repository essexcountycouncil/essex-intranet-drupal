module.exports = {
  "id": "essex_intranet",
  "viewports": [
    {
      "label": "phone",
      "width": 320,
      "height": 480
    },
    {
      "label": "tablet",
      "width": 768,
      "height": 800
    },
    {
      "label": "desktop",
      "width": 1200,
      "height": 960
    }
  ],
  "scenarios": [
    {
      "label": "homepage",
      "cookiePath": "backstop_data/engine_scripts/cookies.json",
      "url": `${process.env.BACKSTOP_BASE_URL}/`,
      "referenceUrl": "https://preprod.essex-intranet.nomensa.xyz/",
      "readyEvent": "",
      "readySelector": "",
      "delay": 0,
      "hideSelectors": ["video", "iframe"],
      "removeSelectors": [".eu-cookie-compliance-banner", ".localgov-alert-banner"],
      "hoverSelector": "",
      "clickSelector": "",
      "postInteractionWait": 0,
      "selectors": [],
      "selectorExpansion": true,
      "expect": 0,
      "misMatchThreshold" : 0.1,
      "requireSameDimensions": true
    },
    {
      "label": "guide_overview",
      "url": `${process.env.BACKSTOP_BASE_URL}/our-council`,
      "referenceUrl": "https://intranet.essex.gov.uk/our-council",
      "removeSelectors": [".eu-cookie-compliance-banner", ".localgov-alert-banner"],
    },
    {
      "label": "feedback_form",
      "url": `${process.env.BACKSTOP_BASE_URL}/form/feedback-form`,
      "referenceUrl": "https://intranet.essex.gov.uk/form/feedback-form",
      "removeSelectors": [".eu-cookie-compliance-banner", ".localgov-alert-banner"],
    },
    {
      "label": "feedback_form",
      "url": `${process.env.BACKSTOP_BASE_URL}/form/feedback-form`,
      "referenceUrl": "https://intranet.essex.gov.uk/form/feedback-form",
      "removeSelectors": [".eu-cookie-compliance-banner", ".localgov-alert-banner"],
    },
    {
      "label": "page_404",
      "url": `${process.env.BACKSTOP_BASE_URL}/dfghj`,
      "referenceUrl": "https://intranet.essex.gov.uk/dfghj",
      "removeSelectors": [".eu-cookie-compliance-banner", ".localgov-alert-banner"],
    }
  ],
  "paths": {
    "bitmaps_reference": "tests/backstop/backstop_data/bitmaps_reference",
    "bitmaps_test": "tests/backstop/backstop_data/bitmaps_test",
    "engine_scripts": "tests/backstop/backstop_data/engine_scripts",
    "html_report": "tests/backstop/backstop_data/html_report",
    "ci_report": "tests/backstop/backstop_data/ci_report"
  },
  "report": ["browser", "CI", "json"],
  "ci": {
    "format" :  "junit" ,
    "testReportFileName": "essex-intranet-xunit", // in case if you want to override the default filename (xunit.xml)
    "testSuiteName" :  "backstopJS"
  },
  "engine": "puppeteer",
  "engineOptions": {
    "args": ["--no-sandbox"]
  },
  "asyncCaptureLimit": 5,
  "asyncCompareLimit": 50,
  "debug": false,
  "debugWindow": false
}
