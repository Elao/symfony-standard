Todo
----

 * Fixtures / Alice
 * Behat 3 (/app/features, /app/behat.yml, /composer.json)
 * PHPSpec
 * Bundle migration (/composer.json, /app/AppKernel.php)
 * Use composer global instead of pear/composer.json (/composer.json)
 * Vagrant / Docker (/vagrant, /Vagrantfile, /app/AppKernel.php)
 * Gulp
 * Build tools (/build.xml, /app/jenkins.xml, /app/phpdoc.xml, /app/phpmd.xml, /app/phpunit.xml, /app/phpunit.xml.dist; /.gitignore)
 * Cleanup .gitignore
 * Update readme
 * check.php / SymfonyRequirements.php (/.gitignore)
 * Favicon / Robots
 * **Session name** should only use alphanumeric characters (recommended by PHP):
   * Update the default session name in `/app/parameters.yml.dist`
   * Update the recommendation in `README.md`, under the section **Symfony
     config files**
 * Hook composer pre-install
 * Use Ip filtering in app_dev.php/app_test.php (instead of commenting code)
 * Common gitignore entries in user configuration (not in repo) (except bower & npm)
 * Move .sass-cache in /var
 * Polemic around views in app/Resources :)
 * Translation key convention
 * Sass classe names convention
