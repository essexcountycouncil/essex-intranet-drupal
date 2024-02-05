const { defineConfig } = require("cypress");

module.exports = defineConfig({

  component: {

    fixturesFolder: "tests/cypress/fixtures",

    integrationFolder: "tests/cypress/integration",

    pluginsFile: "tests/cypress/plugins/index.js",

    screenshotsFolder: "tests/cypress/screenshots",

    supportFile: "tests/cypress/support/e2e.js",

    videosFolder: "tests/cypress/videos",

    viewportWidth: 1440,

    viewportHeight: 900,

  },

  e2e: {

    setupNodeEvents(on, config) {

      // implement node event listeners here

    },

    baseUrl: "https://dev.knowsley.nomensa.xyz/",

    specPattern: "tests/cypress/e2e-dev/**/*.{js,jsx,ts,tsx}",

    supportFile: "tests/cypress/support/e2e.js",

    fixturesFolder: "tests/cypress/fixtures",

    screenshotsFolder: "tests/cypress/screenshots",

    videosFolder: "tests/cypress/videos"

  },

});
