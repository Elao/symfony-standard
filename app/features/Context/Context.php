<?php

namespace Context;

use Behat\MinkExtension\Context\MinkContext;

use Behat\Symfony2Extension\Context\KernelAwareInterface;
use Symfony\Component\HttpKernel\KernelInterface;

use SensioLabs\Behat\PageObjectExtension\Context\PageObjectAwareInterface;
use SensioLabs\Behat\PageObjectExtension\Context\PageFactory;

/**
 * Context
 */
class Context extends MinkContext implements KernelAwareInterface, PageObjectAwareInterface
{
    /**
     * @var KernelInterface $kernel
     */
    private $kernel;

    /**
     * @var PageFactory $pageFactory
     */
    private $pageFactory = null;

    /**
     * @var Page $currentPage
     */
    private $currentPage;

    /**
     * Constructor
     */
    public function __construct(array $parameters)
    {
        // add sub contexts here
    }

    /**
     * Sets Kernel instance.
     *
     * @param KernelInterface $kernel HttpKernel instance
     */
    public function setKernel(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * Returns Container instance.
     *
     * @return ContainerInterface
     */
    private function getContainer()
    {
        return $this->kernel->getContainer();
    }

    /**
     * @param string $name
     *
     * @return Page
     */
    public function getPage($name)
    {
        if (null === $this->pageFactory) {
            throw new \RuntimeException('To create pages you need to pass a factory with setPageFactory()');
        }

        return $this->pageFactory->createPage($name);
    }

    /**
     * @param PageFactory $pageFactory
     */
    public function setPageFactory(PageFactory $pageFactory)
    {
        $this->pageFactory = $pageFactory;
    }

    /**
     * Returns the current Page
     *
     * @return Page The current page
     */
    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    /**
     * Open the page and set it as the current page
     *
     * @param  Page  $page   The page
     * @param  array $params The get params for the page
     *
     * @return Page
     */
    public function openPage($page, $params = array())
    {
        $this->currentPage = $this->getPage($page);

        $this->getCurrentPage()->open($params);
    }
}
