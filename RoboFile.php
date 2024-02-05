<?php

// @codingStandardsIgnoreStart

/**
 * Base tasks for setting up a module to test within a full Drupal environment.
 *
 * @class RoboFile
 * @codeCoverageIgnore
 */
class RoboFile extends \Robo\Tasks {

  /**
   * Command to build the environment
   *
   * @return \Robo\Result
   *   The result of the collection of tasks.
   */
  public function jobBuild() {
    $collection = $this->collectionBuilder();
    $collection->addTaskList($this->copyConfigurationFiles());
    $collection->addTaskList($this->runComposer());
    return $collection->run();
  }

  /**
   * Command to run unit tests.
   *
   * @return \Robo\Result
   *   The result of the collection of tasks.
   */
  public function jobUnitTests() {
    $collection = $this->collectionBuilder();
    $collection->addTask($this->installDrupal());
    $collection->addTaskList($this->runUnitTests());
    return $collection->run();
  }

  /**
   * Command to generate a coverage report.
   *
   * @return \Robo\Result
   *   The result of the collection of tasks.
   */
  public function jobCoverageReport() {
    $collection = $this->collectionBuilder();
    $collection->addTask($this->installDrupal());
    $collection->addTaskList($this->runCoverageReport());
    return $collection->run();
  }

  /**
   * Command to check for Drupal's Coding Standards.
   *
   * @return \Robo\Result
   *   The result of the collection of tasks.
   */
  public function jobCodingStandards() {
    $collection = $this->collectionBuilder();
    $collection->addTaskList($this->runCodeSniffer());
    return $collection->run();
  }

  /**
   * Command to run behat tests.
   *
   * @return \Robo\Result
   *   The result tof the collection of tasks.
   */
  public function jobBehatTests()
  {
    $collection = $this->collectionBuilder();
    $collection->addTaskList($this->runBehatTests());
    return $collection->run();
  }

  /**
   * Command to run Cypress tests on Drupal-CI.
   *
   * @return \Robo\Result
   *   The result tof the collection of tasks.
   */
  public function jobCypressTestsDrupalCi()
  {
    $collection = $this->collectionBuilder();
    $collection->addTaskList($this->runCypressTests('drupal-ci'));
    return $collection->run();
  }

  /**
   * Command to run Cypress tests on DDEV.
   *
   * @return \Robo\Result
   *   The result tof the collection of tasks.
   */
  public function jobCypressTestsDdev()
  {
    $collection = $this->collectionBuilder();
    $collection->addTaskList($this->runCypressTests('ddev'));
    return $collection->run();
  }

  /**
   * Command to run Cypress tests on Dev.
   *
   * @return \Robo\Result
   *   The result tof the collection of tasks.
   */
  public function jobCypressTestsDev()
  {
    $collection = $this->collectionBuilder();
    $collection->addTaskList($this->runCypressTests('dev'));
    return $collection->run();
  }

  /**
   * Command to run Cypress tests on Dev.
   *
   * @return \Robo\Result
   *   The result tof the collection of tasks.
   */
  public function jobBackstopTestsDdev()
  {
    $collection = $this->collectionBuilder();
    $collection->addTaskList($this->runBackstopTests('ddev'));
    return $collection->run();
  }

  /**
   * Command to run Cypress tests on Dev.
   *
   * @return \Robo\Result
   *   The result tof the collection of tasks.
   */
  public function jobBackstopTestsDev()
  {
    $collection = $this->collectionBuilder();
    $collection->addTaskList($this->runBackstopTests('dev'));
    return $collection->run();
  }

  /**
   * Serve Drupal.
   *
   * @return \Robo\Result
   *   The result tof the collection of tasks.
   */
  public function jobServeDrupal()
  {
    $collection = $this->collectionBuilder();
    $collection->addTaskList($this->importDatabase());
    $collection->addTaskList($this->runUpdateDatabase());
    $collection->addTaskList($this->runServeDrupal());
    return $collection->run();
  }

  /**
   * Serve Drupal.
   *
   * @return \Robo\Result
   *   The result tof the collection of tasks.
   */
  public function jobServeDrupalInstall()
  {
    $collection = $this->collectionBuilder();
    $collection->addTask($this->taskFilesystemStack()
      ->copy('.drupal-ci/settings.local.php', 'web/sites/default/settings.local.php', TRUE));
    $collection->addTask($this->installDrupal());
    $collection->addTaskList($this->runServeDrupal());
    return $collection->run();
  }

  /**
   * Updates the database.
   *
   * @return \Robo\Task\Base\Exec[]
   *   An array of tasks.
   */
  protected function runUpdateDatabase() {
    $tasks = [];
    $tasks[] = $this->drush()
      ->args('updatedb')
      ->option('yes')
      ->option('verbose');
    $tasks[] = $this->drush()
      ->args('config:import')
      ->option('yes')
      ->option('verbose');
    $tasks[] = $this->drush()->args('cache:rebuild')->option('verbose');
    $tasks[] = $this->drush()->args('st');
    return $tasks;
  }

  /**
   * Run unit tests.
   *
   * @return \Robo\Task\Base\Exec[]
   *   An array of tasks.
   */
  protected function runUnitTests() {
    $force = TRUE;
    $tasks = [];
    $tasks[] = $this->taskFilesystemStack()
      ->copy('.assets-tests-drupal-ci/phpunit.xml', 'web/core/phpunit.xml', $force);
    $tasks[] = $this->taskExecStack()
      ->dir('web')
      ->exec('../bin/phpunit -c core --debug --coverage-clover ../build/logs/clover.xml --verbose modules/custom');
    return $tasks;
  }

  /**
   * Generates a code coverage report.
   *
   * @return \Robo\Task\Base\Exec[]
   *   An array of tasks.
   */
  protected function runCoverageReport() {
    $tasks = [];
    $tasks[] = $this->taskFilesystemStack()
      ->copy('.drupal-ci/phpunit.xml', 'web/core/phpunit.xml', $force);
    $tasks[] = $this->taskExecStack()
      ->dir('web')
      ->exec('../bin/phpunit -c core --debug --verbose --coverage-html ../coverage modules/custom');
    return $tasks;
  }

  /**
   * Sets up and runs code sniffer.
   *
   * @return \Robo\Task\Base\Exec[]
   *   An array of tasks.
   */
  protected function runCodeSniffer() {
    $tasks = [];
    $tasks[] = $this->taskFilesystemStack()
      ->mkdir('artifacts/phpcs');
    $tasks[] = $this->taskExecStack()
      ->exec('bin/phpcs --standard=Drupal --report=junit --report-junit=artifacts/phpcs/phpcs.xml web/modules/custom')
      ->exec('bin/phpcs --standard=DrupalPractice --report=junit --report-junit=artifacts/phpcs/phpcs.xml web/modules/custom');
    return $tasks;
  }

  /**
   * Serves Drupal.
   *
   * @return \Robo\Task\Base\Exec[]
   *   An array of tasks.
   */
  function runServeDrupal()
  {
    $tasks = [];
    $tasks[] = $this->taskExec('chown -R www-data:www-data ' . getenv('CI_PROJECT_DIR'));
    $tasks[] = $this->taskExec('ln -sf ' . getenv('CI_PROJECT_DIR') . '/web /var/www/html');
    $tasks[] = $this->taskExec('echo "\nServerName localhost" >> /etc/apache2/apache2.conf');
    $tasks[] = $this->taskExec('service apache2 start');
    return $tasks;
  }

  /**
   * Runs Behat tests.
   *
   * @return \Robo\Task\Base\Exec[]
   *   An array of tasks.
   */
  protected function runBehatTests()
  {
    $force = TRUE;
    $tasks = [];
    $tasks[] = $this->taskFilesystemStack()
      ->copy('.drupal-ci/behat.yml', 'tests/behat.yml', $force);
    $tasks[] = $this->taskExec('sleep 10s');
    $tasks[] = $this->taskExec('bin/behat --verbose -c tests/behat.yml');
    return $tasks;
  }

  /**
   * Runs Cypress tests.
   *
   * @return \Robo\Task\Base\Exec[]
   *   An array of tasks.
   */
  protected function runCypressTests($env)
  {
    $tasks = [];
    $command = match($env) {
      'drupal-ci' => 'npx cypress run --config-file "tests/cypress/cypress.config.drupal-ci.js" --browser chrome',
      'ddev' => 'npx cypress run --config-file "tests/cypress/cypress.config.ddev.js" --browser chrome',
      'dev' => 'npx cypress run --config-file "tests/cypress/cypress.config.dev.js" --browser chrome',
    };
    if ($command) {
      $tasks[] = $this->taskExec('npm install cypress cypress-image-diff-js --save-dev');
      $tasks[] = $this->taskExec($command);
    }
    return $tasks;
  }

  /**
   * Runs Backstop tests.
   *
   * @return \Robo\Task\Base\Exec[]
   *   An array of tasks.
   */
  protected function runBackstopTests($env)
  {
    $tasks = [];
    switch ($env) {
      case 'ddev':
        $commands = [
          'npx backstop --config=tests/backstop/backstop.config.js reference',
          'npx backstop --config=tests/backstop/backstop.config.js test',
        ];
        $env = [
          'BACKSTOP_BASE_URL' => 'https://essex-intranet.ddev.site',
          'BACKSTOP_REFERENCE_URL' => 'https://intranet.essex.gov.uk',
        ];
        break;

      case 'dev':
        $commands = [
          'npx backstop --config=tests/backstop/backstop.config.js reference',
          'npx backstop --config=tests/backstop/backstop.config.js test',
        ];
        $env = [
          'BACKSTOP_BASE_URL' => 'https://dev.essex-intranet.nomensa.xyz/',
          'BACKSTOP_REFERENCE_URL' => 'https://intranet.essex.gov.uk',
        ];
        break;

    }
    if ($commands && $env) {
      $tasks[] = $this->taskExec('npm install backstopjs@6');
      foreach ($commands as $command) {
        $tasks[] = $this->taskExec($command)->envVars($env);
      }
    }
    return $tasks;
  }

  /**
   * Return drush with default arguments.
   *
   * @return \Robo\Task\Base\Exec
   *   A drush exec command.
   */
  protected function drush() {
    return $this->taskExec('bin/drush');
  }

  /**
   * Copies configuration files.
   *
   * @return \Robo\Task\Base\Exec[]
   *   An array of tasks.
   */
  protected function copyConfigurationFiles() {
    $force = TRUE;
    $tasks = [];
    $tasks[] = $this->taskFilesystemStack()
      ->copy('.drupal-ci/settings.local.php', 'web/sites/default/settings.local.php', $force)
      ->copy('.drupal-ci/.env', '.env', $force);
    return $tasks;
  }

  /**
   * Runs composer commands.
   *
   * @return \Robo\Task\Base\Exec[]
   *   An array of tasks.
   */
  protected function runComposer() {
    $tasks = [];
    $tasks[] = $this->taskComposerValidate()->noCheckPublish();
    $tasks[] = $this->taskComposerInstall()
      ->noInteraction()
      ->envVars(['COMPOSER_ALLOW_SUPERUSER' => 1, 'COMPOSER_DISCARD_CHANGES' => 1] + getenv())
      ->optimizeAutoloader();
    return $tasks;
  }

  /**
   * Install Drupal.
   *
   * @return \Robo\Task\Base\Exec
   *   A task to install Drupal.
   */
  protected function installDrupal()
  {
    $task = $this->drush()
      ->args('site-install')
      ->option('existing-config')
      ->option('verbose')
      ->option('yes');
    return $task;
  }

  /**
   * Imports and updates the database.
   *
   * This task assumes that there is an environment variable $DB_DUMP_URL
   * that contains a URL to a database dump. Ideally, you should set up drush
   * site aliases and then replace this task by a drush sql-sync one. See the
   * README at lullabot/drupal9ci for further details.
   *
   * @return \Robo\Task\Base\Exec[]
   *   An array of tasks.
   */
  protected function importDatabase()
  {
    $force = TRUE;
    $tasks = [];
    $tasks[] = $this->taskExec('mysql -u root -proot -h mariadb -e "create database if not exists drupal"');
    $tasks[] = $this->taskFilesystemStack()
      ->copy('.drupal-ci/settings.local.php', 'web/sites/default/settings.local.php', $force);
    $tasks[] = $this->taskExec('wget -O dump.sql "' . getenv('DB_DUMP_URL') . '"');
    $tasks[] = $this->drush()->rawArg('sql-cli < dump.sql');
    return $tasks;
  }


  /**
   * Command to run existing site tests.
   *
   * @return \Robo\Result
   *   The result tof the collection of tasks.
   */
  public function jobExistingSiteTests() {
    $collection = $this->collectionBuilder();
    $collection->addTaskList($this->runUpdateDatabase());
    $collection->addTaskList($this->runExistingSiteTests());
    return $collection->run();
  }

  /**
   * Command to run Chrome headless.
   *
   * @return \Robo\Result
   *   The result tof the task
   */
  public function runChromeHeadless() {
    return $this->taskExec('google-chrome-unstable --disable-gpu --headless --no-sandbox --remote-debugging-address=0.0.0.0 --remote-debugging-port=9222')->run();
  }

  /**
   * Runs existing site tests.
   *
   * @return \Robo\Task\Base\Exec[]
   *   An array of tasks.
   */
  protected function runExistingSiteTests() {
    $CI_PROJECT_DIR = getenv('CI_PROJECT_DIR');
    $tasks = [];
    $tasks[] = $this->taskExec('sed -ri -e \'s!/var/www/html/web!' . $CI_PROJECT_DIR . '/web!g\' /etc/apache2/sites-available/*.conf /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf');
    $tasks[] = $this->taskExec('service apache2 start');
    $tasks[] = $this->taskExec('bin/phpunit --debug --verbose --bootstrap=vendor/weitzman/drupal-test-traits/src/bootstrap-fast.php --testsuite=existing-site,existing-site-javascript');
    return $tasks;
  }

  public function jobCreateNode() {
    $collection = $this->collectionBuilder();
    $collection->addTaskList($this->runCreateNode());
    return $collection->run();
  }

  protected function runCreateNode() {
    $tasks = [];
    $tasks[] = $this->drush()
      ->args('genc')
      ->option('bundles', 'localgov_subsites_overview');
    return $tasks;
  }

}
