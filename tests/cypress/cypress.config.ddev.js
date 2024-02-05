const { defineConfig } = require("cypress");

//const getCompareSnapshotsPlugin = require('cypress-visual-regression/dist/plugin');
const getCompareSnapshotsPlugin = require('cypress-image-diff-js/plugin');

module.exports = defineConfig({

  component: {

    fixturesFolder: "fixtures",

    integrationFolder: "integration",

    pluginsFile: "plugins/index.js",

    screenshotsFolder: "screenshots",

    supportFile: "support/e2e.js",

    videosFolder: "videos",

    viewportWidth: 1440,

    viewportHeight: 900,

  },

  e2e: {

    setupNodeEvents(on, config) {
      getCompareSnapshotsPlugin(on, config);
    },

    baseUrl: "https://essex-intranet.ddev.site/",

    specPattern: "e2e-ddev/**/*.{js,jsx,ts,tsx}",

    supportFile: "support/e2e.js",

    fixturesFolder: "fixtures",

    screenshotsFolder: "screenshots",

    videosFolder: "videos",

    trashAssetsBeforeRuns: true,

  },

});
