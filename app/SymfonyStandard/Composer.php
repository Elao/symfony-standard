<?php

/*
 * This file is part of the Elao/symfony-standard package.
 *
 * Copyright © Elao
 *
 * @author Maximilien Bernard <maximilien.bernard@elao.com>
 */

namespace SymfonyStandard;

use Composer\Script\CommandEvent;

/**
 * Class Composer
 * @package SymfonyStandard
 */
class Composer
{
    /**
     * @const ANSIBLE_FILE : path to the ansible app.yml file
     */
    const ANSIBLE_FILE = 'ansible/group_vars/app.yml';

    /**
     * @var array
     */
    public static $fileMap = [
        'README.app.md' => [
            'label' => '/App/',
            'name' => '/()app(\.dev)/',
        ],
        'Vagrantfile' => [
            'name' => '/(:name.+\')app(\')/',
        ],
        'behat.yml.dist' => [
            'name' => '/()app(\.dev)/',
        ],
        '.php_cs' => [
            'label' => '/App/',
            'vendor' => '/Vendor/',
        ],
        'composer.json' => [
            'composerVendor' => '/elao\/symfony-standard/',
            'composerName' => '/The elao\/symfony-standard project/',
        ],
    ];

    /**
     * Create the project.
     *
     * @param CommandEvent $event
     */
    public static function hookCreateProject(CommandEvent $event)
    {
        $files = array_keys(self::$fileMap);

        $event->getIO()->write([
            '<info>Configure application</info>',
            '<comment>The following files will be updated</comment>:',
            '- composer.json',
            '- README.md',
        ]);

        foreach ($files as $filename) {
            $event->getIO()->write('- ' . $filename);
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

        $appComposerName = str_replace('.', '/', $appName);

        $userValues = [
            'label' => $appLabel,
            'name' => '$1' . $appName . '$2',
            'vendor' => $vendor ? ucwords(str_replace('-', ' ', $vendor))  : $appLabel,
            'composerVendor' => ($vendor ? $vendor : $app) . '/' . $app,
            'composerName' => ($vendor ? str_replace('-', ' ', $vendor) . ' - ' : '') . str_replace('-', ' ', $app),
        ];

        foreach (self::$fileMap as $filename => $values) {
            if (file_exists($filename)) {
                $patterns = array_values($values);
                $replacements = array_intersect_key($userValues, $values);

                self::replaceValueInFile($filename, $patterns, $replacements);
            }
        }

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
    private static function askAppVendorName(CommandEvent $event)
    {
        $projectInfos = [];

        $projectInfos['vendor'] = self::askVendor($event);
        $projectInfos['appName'] = self::askAppName($event);

        $confirmation = $event->getIO()
            ->askConfirmation(
                '<info>Your project’s vendor will be "' . $projectInfos['vendor'] . '" and your app name will be "' . $projectInfos['appName'] .
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
    private static function askVendor(CommandEvent $event)
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
    private static function askAppName(CommandEvent $event)
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
    private static function installDependencies(CommandEvent $event)
    {
        $content = file_get_contents(self::ANSIBLE_FILE);
        $dependencies = self::getDependenciesList($content);
        $versionList = self::getDependenciesVersionList($content);

        self::handleDependencyVersion($event, 'php', $content);
        self::handleDependencyVersion($event, 'nodejs', $content);

        foreach ($dependencies as $dependency) {
            self::handleDependency($event, $dependency, $content, $versionList);
        }
    }

    /**
     * Handle a single dependency.
     *
     * @param CommandEvent $event
     * @param string       $dependency
     * @param string       $content
     * @param string       $versionList
     */
    private static function handleDependency(CommandEvent $event, $dependency, $content, $versionList)
    {
        preg_match('/#' . $dependency . '.*false/', $content, $activationMatches);
        preg_match('/#' . $dependency . '_version.*\r?\n/', $content, $versionMatches);

        $dependencyValue = $event->getIO()
            ->askAndValidate(
                '<info>Do you wish to install ' . $dependency . ' ?</info> [<comment>y/N</comment>]: ',
                function ($value) {
                    if (!preg_match('/^(?:Y|N|^$)$/i', $value)) {
                        throw new \InvalidArgumentException('Invalid input');
                    }

                    return $value;
                },
                5,
                null
            );

        preg_match('/^Y/i', $dependencyValue) ?
            self::replaceValueInFile(self::ANSIBLE_FILE, '/#(' . $dependency . ':.+)false/', '$1 ' . 'true')
        :
            self::replaceValueInFile(self::ANSIBLE_FILE, '/#' . $dependency . ':.+false\r?\n/', '');

        if (preg_match('/^Y/i', $dependencyValue) && 'postgresql' === $dependency) {
            $confirmation = $event->getIO()
                ->askConfirmation(
                    '<info>Do you want to use postgresql as your default connection for Doctrine ?</info> [<comment>y,N</comment>]',
                    false
                );

            if ($confirmation) {
                self::replaceValueInFile('app/config/config.yml', '/(doctrine:\s+dbal:\s+driver: +).+(\S)/', '$1pdo_pgsql');
            }
        }

        if (preg_match('/^Y/i', $dependencyValue) && in_array($dependency . '_version', $versionList)) {
            self::handleDependencyVersion($event, $dependency, $content);
        } elseif (in_array($dependency . '_version', $versionList)) {
            self::replaceValueInFile(self::ANSIBLE_FILE,  '/.*#' . $dependency . '_version:.+\r?\n/', '');
        }
    }

    /**
     * Handle the dependency version.
     *
     * @param CommandEvent $event
     * @param string       $dependency
     * @param string       $content
     */
    private static function handleDependencyVersion(CommandEvent $event, $dependency, $content)
    {
        $versions = self::getAvailableDependencyVersions($content, $dependency . '_version');
        $defaultVersion = self::getDefaultVersion($content, $dependency);
        preg_match('/#' . $dependency . '_version.*\r?\n/', $content, $versionMatches);

        $versionValidator = function ($value) use ($versions) {
            if (!in_array($value, $versions)) {
                throw new \InvalidArgumentException('This version is not valid');
            }

            return $value;
        };

        $chosenVersion = $event->getIO()
            ->askAndValidate(
                '<info>' . $dependency . ' version (' . implode(', ', $versions) . ')</info> [<comment>' . $defaultVersion . '</comment>] : ',
                $versionValidator,
                5,
                $defaultVersion
            );

        if ('php' === $dependency) {
            $boxVersion = '7.0' === $chosenVersion ? '3.0.0' : '2.0.0';
            self::replaceValueInFile('Vagrantfile',  '/(box_version\s+=>\s+)\'.+(\',)/', '$1\'~> ' . $boxVersion . '$2');
        }

        self::replaceValueInFile(self::ANSIBLE_FILE,  '/#(' . $dependency . '_version:.+)\'.+(\').+(\S)/', '$1 \'' . $chosenVersion . '$2');
    }

    /**
     * Get the dependency default version.
     *
     * @param string $content
     * @param string $dependency
     *
     * @throws \InvalidArgumentException
     * @return array
     */
    private static function getDefaultVersion($content, $dependency)
    {
        preg_match('/' . $dependency . '.*\'(.*)\'/', $content, $matches);

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
     * @return array
     */
    private static function getDependenciesVersionList($content)
    {
        $content = substr($content, 0, strpos($content, 'app_patterns'));
        preg_match_all('/#(.*_version):/', $content, $matches);

        return $matches[1];
    }

    /**
     * Get the list of available dependencies.
     *
     * @param string $content
     *
     * @return array
     */
    private static function getDependenciesList($content)
    {
        $content = substr($content, 0, strpos($content, 'app_patterns'));
        preg_match_all('/#(\w*(?<!_version)):/', $content, $matches);

        return $matches[1];
    }

    /**
     * Get the available versions for a given dependency.
     *
     * @param string $content
     * @param string $dependency
     *
     * @throws \InvalidArgumentException
     * @return array
     *
     */
    private static function getAvailableDependencyVersions($content, $dependency)
    {
        preg_match('/' . $dependency . '.*#(.*)\r?\n/', $content, $matches);

        if (!isset($matches[1]) || empty($matches[1])) {
            throw new \InvalidArgumentException(sprintf('A valid list of versions for %s is missing in your ansible/groupe_vars/app.yml file', $dependency));
        }

        return explode('|', $matches[1]);
    }

    /**
     * Uses a regular expression to find and replace $replacementPattern by $value in $file
     *
     * @param string $file
     * @param mixed  $replacementPattern
     * @param mixed  $value
     */
    private static function replaceValueInFile($file, $replacementPattern, $value)
    {
        $content = file_get_contents($file);
        $replacement = preg_replace($replacementPattern, $value, $content);

        file_put_contents($file, $replacement);
    }
}
