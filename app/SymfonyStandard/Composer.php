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
    public static function hookCreateProject(CommandEvent $event)
    {
        $files = [
            'Vagrantfile',
            'app/config/parameters.yml.dist',
            'app/config/config.yml',
            'package.json',
            'ansible/group_vars/all.yml',
            'behat.yml.dist'
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

        $validator = function ($value) {
            if (!preg_match('/^([-A-Z0-9])*$/i', $value)) {
                throw new \InvalidArgumentException('The name should only contains alphanumeric characters (and hyphens)');
            }

            return $value;
        };

        $vendor = $event->getIO()
            ->askAndValidate(
                '<info>Vendor name</info>: ',
                $validator,
                1,
                null
            );

        $app = $event->getIO()
            ->askAndValidate(
                '<info>Application name</info> [<comment>app</comment>]: ',
                $validator,
                1,
                'app'
            );

        $appDatabase = ($vendor ? $vendor . '_' : '') . $app;

        $appHost = $app . ($vendor ? '.' . $vendor : '');

        $vars = [
            '{{ vendor }}'       => strtolower($vendor),
            '{{ app }}'          => strtolower($app),
            '{{ app_database }}' => strtolower($appDatabase),
            '{{ app_host }}'     => strtolower($appHost),
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
        $content = strtr($content, ['{{ app_name }}' => ucwords($appName)]);

        file_put_contents('README.md', $content);
        unlink('README.app.md');
    }
}
