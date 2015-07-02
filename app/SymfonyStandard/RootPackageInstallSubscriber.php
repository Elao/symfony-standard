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

use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\Script\ScriptEvents;
use Composer\Script\CommandEvent;
use Sensio\Bundle\DistributionBundle\Composer\ScriptHandler;

class RootPackageInstallSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            ScriptEvents::PRE_INSTALL_CMD => array(
                array('configureApp', 512),
            ),
        );
    }

    public static function configureApp(CommandEvent $event)
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
            ' - composer.json'
        ]);

        foreach ($files as $file) {
            $event->getIO()->write(' - ' . $file);
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

        $appHost = $app . ($vendor ? '.' . $vendor : '') . '.dev';

        $vars = [
            '{{ vendor }}'     => strtolower($vendor),
            '{{ app }}'        => strtolower($app),
            '{{ vendor_app }}' => ($vendor ? strtolower($vendor) . '_' : '') . strtolower($app),
            '{{ app_host }}'   => strtolower($appHost),
        ];

        foreach ($files as $file) {
            if (file_exists($file)) {
                $content = file_get_contents($file);

                $content = strtr($content, $vars);

                file_put_contents($file, $content);
            }
        }

        // Change the application name in composer
        $composerName = $vendor ? $vendor . '/' . strtolower($app) : strtolower($app);

        $content = file_get_contents('composer.json');

        $content = strtr($content, ['elao/symfony-standard' => $composerName]);

        file_put_contents('composer.json', $content);
    }
}
