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
     * Kernel
     *
     * @var KernelInterface $kernel
     */
    protected $kernel;

    /**
     * Page factory
     *
     * @var PageFactory $pageFactory
     */
    protected $pageFactory;

    /**
     * Current page
     *
     * @var Page $currentPage
     */
    protected $currentPage;

    /**
     * Constructor
     *
     * @param array $parameters
     */
    public function __construct(array $parameters)
    {
        // add sub contexts here
    }

    /**
     * Set Kernel instance.
     *
     * @param KernelInterface $kernel HttpKernel instance
     */
    public function setKernel(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * Return Container instance.
     *
     * @return ContainerInterface
     */
    public function getContainer()
    {
        if (null === $this->kernel) {
            throw new \RuntimeException('Kernel is not set');
        }

        return $this->kernel->getContainer();
    }

    /**
     * Set page factory
     *
     * @param PageFactory $pageFactory
     */
    public function setPageFactory(PageFactory $pageFactory)
    {
        $this->pageFactory = $pageFactory;
    }

    /**
     * Get page
     *
     * @param string $name
     *
     * @return Page
     */
    public function getPage($name)
    {
        if (null === $this->pageFactory) {
            throw new \RuntimeException('Page factory is not set');
        }

        return $this->pageFactory->createPage($name);
    }

    /**
     * Return the current Page
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
     * @param Page  $page   The page
     * @param array $params The get params for the page
     *
     * @return Page
     */
    public function openPage($page, $params = array())
    {
        $this->currentPage = $this->getPage($page);

        $this->getCurrentPage()->open($params);
    }

    /**
     * Get current url path of the page.
     *
     * @return string
     */
    public function getCurrentUrlPath()
    {
        return preg_replace(
            '/^\/[^\.\/]+\.php/',
            '',
            parse_url($this->getSession()->getCurrentUrl(), PHP_URL_PATH)
        );
    }
}
