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

class CreateProjectSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            ScriptEvents::POST_CREATE_PROJECT_CMD => array(
                array('configureApp', 512),
            ),
        );
    }

    public static function configureApp(CommandEvent $event)
    {
        $files = [
            'composer.json',
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

        $appDatabase = ($vendor ? $vendor . '_' : '') . $app;

        $appComposer = ($vendor ? $vendor : $name) . '/' . $app;

        $appHost = $app . ($vendor ? '.' . $vendor : '') . '.dev';

        $vars = [
            '{{ vendor }}'       => strtolower($vendor),
            '{{ app }}'          => strtolower($app),
            '{{ app_database }}' => strtolower($appDatabase),
            '{{ app_composer }}' => strtolower($appComposer),
            '{{ app_host }}'     => strtolower($appHost),
        ];

        foreach ($files as $file) {
            if (file_exists($file)) {
                $content = file_get_contents($file);

                $content = strtr($content, $vars);

                file_put_contents($file, $content);
            }
        }
    }
}
