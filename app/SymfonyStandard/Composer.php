<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SymfonyStandard;

use Composer\Script\CommandEvent;

class Composer
{
    const TAG_START = '<#';
    const TAG_END = '#>';

    /**
     * Create the project.
     *
     * @param CommandEvent $event
     */
    static public function hookCreateProject(CommandEvent $event)
    {
        $files = [
            'README.app.md',
            'Vagrantfile',
            'behat.yml.dist',
            '.php_cs'
        ];

        $event->getIO()->write([
            '<info>Configure application</info>',
            '<comment>The following files will be updated</comment>:',
            '- composer.json',
            '- README.md'
        ]);

        foreach ($files as $file) {
            $event->getIO()->write('- ' . $file);
        }

        $confirmation = $event->getIO()
            ->askConfirmation(
                '<info>Do you want to continue?</info> [<comment>Y,n</comment>]',
                true
            );

        if (!$confirmation) {
            return;
        }

        $event->getIO()->write([
            '<info>You are going to define your project\'s vendor and app name, it will look something like this : app.vendor</info>',
        ]);

        $projectInfos = self::askAppVendorName($event);

        $vendor = $projectInfos['vendor'];
        $app = $projectInfos['appName'];

        $appName = $app . ($vendor ? '.' . $vendor : '');
        $appLabel = ucwords(str_replace('.', ' - ', str_replace('-', ' ', $appName)));

        $vendorLabel = $vendor ? ucwords(str_replace('-', ' ', $vendor))  : $appLabel;
        $appComposerName = str_replace('.', '/', $appName);

        $vars = [
            self::TAG_START . ' app.name ' . self::TAG_END      => strtolower($appName),
            self::TAG_START . ' app.label ' . self::TAG_END      => $appLabel,
            self::TAG_START . ' vendor.label ' . self::TAG_END   => $vendorLabel,
            self::TAG_START . ' app.composer.label ' . self::TAG_END => strtolower($appComposerName)
        ];

        foreach ($files as $file) {
            if (file_exists($file)) {

                $content = file_get_contents($file);
                $content = strtr($content, $vars);
                file_put_contents($file, $content);
            }
        }

        // App composer name
        $appComposerName = ($vendor ? $vendor : $app) . '/' . $app;

        // App name
        $appName = ($vendor ? str_replace('-', ' ', $vendor) . ' - ' : '') . str_replace('-', ' ', $app);

        $content = file_get_contents('composer.json');
        $content = strtr($content, ['elao/symfony-standard'             => strtolower($appComposerName)]);
        $content = strtr($content, ['The elao/symfony-standard project' => strtolower($appName)]);

        file_put_contents('composer.json', $content);

        $content = file_get_contents('README.app.md');

        file_put_contents('README.md', $content);
        unlink('README.app.md');

        self::installDependencies($event);
    }

    /**
     * Ask for the vendor and the app name, calls itself back if not confirmed.
     *
     * @param CommandEvent $event
     *
     * @return array
     */
    static private function askAppVendorName(CommandEvent $event)
    {
        $projectInfos = [];

        $projectInfos['vendor'] = self::askVendor($event);
        $projectInfos['appName'] = self::askAppName($event);

        $confirmation = $event->getIO()
            ->askConfirmation(
                '<info>Your project’s vendor will be "'.$projectInfos['vendor']. '" and your app name will be "'.$projectInfos['appName'].
                '". Do you want to continue?</info> [<comment>Y,n</comment>]',
                true
            );

        if (!$confirmation) {
            self::askAppVendorName($event);
        }

        return $projectInfos;
    }

    /**
     * Ask for the vendor.
     *
     * @param CommandEvent $event
     *
     * @return string
     */
    static private function askVendor(CommandEvent $event)
    {
        return $event->getIO()
            ->askAndValidate(
                '<info>Vendor name (optional)</info>: ',
                function ($value) {
                    if (!preg_match('/^([-A-Z0-9])*$/i', $value)) {
                        throw new \InvalidArgumentException('The name should only contains alphanumeric characters (and hyphens)');
                    }
                    return $value;
                },
                5,
                null
            );
    }

    /**
     * Ask for the App name.
     *
     * @param CommandEvent $event
     *
     * @return string
     */
    static private function askAppName(CommandEvent $event)
    {
        return $event->getIO()
            ->askAndValidate(
                '<info>Application name</info> [<comment>app</comment>]: ',
                function ($value) {
                    if (!preg_match('/^([-A-Z0-9])*$/i', $value)) {
                        throw new \InvalidArgumentException('The name should only contains alphanumeric characters (and hyphens)');
                    }
                    return $value;
                },
                5,
                'app'
            );
    }

    /**
     * Install the dependencies (roles) listed in the ansible/group_vars/app.yml config file.
     *
     * @param CommandEvent $event
     */
    static private function installDependencies(CommandEvent $event)
    {
        $file = 'ansible/group_vars/app.yml';

        $content = file_get_contents($file);
        $dependencies = self::getDependenciesList($content);
        $versionList = self::getDependenciesVersionList($content);
        $vars = [];

        self::handleDependencyVersion($event, 'php', $content, $vars);
        self::handleDependencyVersion($event, 'nodejs', $content, $vars);

        foreach ($dependencies as $dependency)
        {
            self::handleDependency($event, $dependency, $content, $versionList, $vars);
        }

        $content = strtr($content, $vars);
        file_put_contents($file, $content);
    }

    /**
     * Handle a single dependency.
     *
     * @param CommandEvent $event
     *
     * @param string $dependency
     *
     * @param string $content
     *
     * @param string $versionList
     *
     * @param array $vars
     */
    private static function handleDependency($event, $dependency, $content, $versionList, &$vars)
    {
        preg_match('/#'. $dependency .'.*false/', $content, $activationMatches);
        preg_match('/#'. $dependency .'_version.*\r?\n/', $content, $versionMatches);

        $dependencyValue = $event->getIO()
            ->askAndValidate(
                '<info>Do you wish to install '.$dependency.' ?</info> [<comment>y/N</comment>]: ',
                function ($value) {
                    if (!preg_match('/^(?:Y|N|^$)$/i', $value)) {
                        throw new \InvalidArgumentException('Invalid input');
                    }
                    return $value;
                },
                5,
                null
            );

        $activationReplacement = preg_match('/^Y/i', $dependencyValue) ? preg_replace(['/#/', '/false/'], ['', 'true'] , $activationMatches[0]) : '';
        $vars[$activationMatches[0]] = $activationReplacement;

        if ('postgresql' === $dependency)
        {
            $confirmation = $event->getIO()
                ->askConfirmation(
                    '<info>Do you want to use postgresql as your default connection for Doctrine ?</info> [<comment>y,N</comment>]',
                    false
                );

            if ($confirmation) {
                self::replaceValueInFile('app/config/config.yml', '/driver\:.*\r?\n/', '/mysql/', 'pgsql');
            }

        }

        if (preg_match('/^Y/i', $dependencyValue) && in_array($dependency . '_version', $versionList))
        {
           self::handleDependencyVersion($event, $dependency, $content, $vars);
        }
        else if (in_array($dependency . '_version', $versionList))
        {
            preg_match('/#'. $dependency .'_version.*\r?\n/', $content, $versionMatches);
            $vars[$versionMatches[0]] = '';
        }
    }

    /**
     * Handle the dependency version.
     *
     * @param CommandEvent $event
     *
     * @param string $dependency
     *
     * @param string $content
     *
     * @param array $vars
     */
    private static function handleDependencyVersion($event, $dependency, $content, &$vars)
    {
        $versions = self::getAvailableDependencyVersions($content, $dependency . '_version');
        $defaultVersion = self::getDefaultVersion($content, $dependency);
        preg_match('/#'. $dependency .'_version.*\r?\n/', $content, $versionMatches);

        $versionValidator = function ($value) use ($versions) {
            if (!in_array($value, $versions)) {
                throw new \InvalidArgumentException('This version is not valid');
            }
            return $value;
        };

        $chosenVersion = $event->getIO()
            ->askAndValidate(
                '<info>'. $dependency .' version ('. implode(', ', $versions) .')</info> [<comment>' . $defaultVersion . '</comment>] : ',
                $versionValidator,
                5,
                $defaultVersion
            );

        if ('php' === $dependency)
        {
            $boxVersion = '7.0' === $chosenVersion ? '3.0.0' : '2.0.0';
            self::replaceValueInFile('Vagrantfile', '/\:box_version.*,/', '/\'.*\'/', '\'' . $boxVersion.'\'');
        }

        $versionReplacement = preg_replace(['/#/', '/\'\d.*\'.*(\r?\n)/'], ['', '\'' . $chosenVersion.'\'$1'] , $versionMatches[0]);
        $vars[$versionMatches[0]] = $versionReplacement;
    }

    /**
     * Get the dependency default version.
     *
     * @param string $content
     *
     * @param string $dependency
     *
     * @return mixed
     */
    private static function getDefaultVersion($content, $dependency)
    {
        preg_match('/'.$dependency.'.*\'(.*)\'/', $content, $matches);

        if (!isset($matches[1]) || empty($matches[1])) {
            throw new \InvalidArgumentException(sprintf('A default version of %s is missing in your ansible/groupe_vars/app.yml file', $dependency));
        }

        return $matches[1];
    }

    /**
     * Get the list of dependencies having versions defined.
     *
     * @param string $content
     *
     * @return mixed
     */
    private static function getDependenciesVersionList($content)
    {
        $content = substr($content, 0, strpos($content, "app_patterns"));
        preg_match_all('/#(.*_version):/', $content, $matches);

        return $matches[1];
    }

    /**
     * Get the list of available dependencies.
     *
     * @param string $content
     *
     * @return mixed
     */
    private static function getDependenciesList($content)
    {
        $content = substr($content, 0, strpos($content, "app_patterns"));
        preg_match_all('/#(\w*(?<!_version)):/', $content, $matches);

        return $matches[1];
    }

    /**
     * Get the available versions for a given dependency.
     *
     * @param string $content
     *
     * @param string $dependency
     *
     * @return array
     */
    private static function getAvailableDependencyVersions($content, $dependency)
    {
        preg_match('/'.$dependency.'.*#(.*)\r?\n/', $content, $matches);

        if (!isset($matches[1]) || empty($matches[1])) {
            throw new \InvalidArgumentException(sprintf('A valid list of versions for %s is missing in your ansible/groupe_vars/app.yml file', $dependency));
        }

        return explode("|", $matches[1]);
    }

    /**
     * @param string $file
     *
     * @param string $pattern
     *
     * @param string $replacementPattern
     *
     * @param string $value
     */
    private static function replaceValueInFile($file, $pattern, $replacementPattern, $value)
    {
        $content = file_get_contents($file);
        preg_match($pattern, $content, $matches);

        $replacement = preg_replace($replacementPattern, $value, $matches[0]);
        $content = strtr($content, [$matches[0] => $replacement]);
        file_put_contents($file, $content);
    }
}
