// @ts-check
const { defineConfig } = require('@playwright/test');

module.exports = defineConfig({
  testDir: './tests/playwright',
  timeout: 30000,
  use: {
    baseURL: 'http://127.0.0.1:8000'
  },
  projects: [
    {
      name: 'api'
    }
  ],
  webServer: {
    command: 'php scripts/start-billing-test-server.php',
    url: 'http://127.0.0.1:8000/healthz',
    reuseExistingServer: true,
    timeout: 90000
  }
});
