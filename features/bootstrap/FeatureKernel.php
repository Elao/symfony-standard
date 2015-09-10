<?php

include __DIR__ . '/../../app/AppKernel.php';

/**
 * See: https://github.com/Behat/Symfony2Extension/issues/69
 */
class FeatureKernel extends AppKernel
{
    /**
     * {@inheritdoc}
     */
    public function getRootDir()
    {
        if (null === $this->rootDir) {
            $r = new \ReflectionObject($this);
            $this->rootDir = str_replace(
                ['\\', 'features/bootstrap'],
                ['/'],
                dirname($r->getFileName())
            ) . 'app';
        }

        return $this->rootDir;
    }

    /**
     * {@inheritdoc}
     */
    public function getLogDir()
    {
        return $this->rootDir.'/../var/logs/'.$this->environment;
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheDir()
    {
        return $this->rootDir.'/../var/cache/'.$this->environment;
    }
}
