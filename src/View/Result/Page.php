<?php
/**
 * ScandiPWA - Progressive Web App for Magento
 *
 * Copyright © Scandiweb, Inc. All rights reserved.
 * See LICENSE for license details.
 *
 * @license OSL-3.0 (Open Software License ("OSL") v. 3.0)
 * @package scandipwa/customization
 * @link https://github.com/scandipwa/quote-graphql
 */

namespace ScandiPWA\Customization\View\Result;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Locale\Resolver;
use Magento\Framework\Translate\InlineInterface;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\View\EntitySpecificHandlesList;
use Magento\Framework\View\Layout\BuilderFactory;
use Magento\Framework\View\Layout\GeneratorPool;
use Magento\Framework\View\Layout\ReaderPool;
use Magento\Framework\View\LayoutFactory;
use Magento\Framework\View\Page\Config\RendererFactory;
use Magento\Framework\View\Page\Layout\Reader;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use ScandiPWA\Locale\View\Result\Page as LocalePage;
use ScandiPWA\Customization\Controller\AppIcon;
use Magento\Framework\Serialize\Serializer\Json;

/**
 * Class Page
 * @package ScandiPWA\Customization\View\Result
 */
class Page extends LocalePage
{
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var Json
     */
    protected $json;

    /**
     * @var AppIcon
     */
    protected $appIcon;

    /**
     * Page constructor.
     * @param StoreManagerInterface $storeManager
     * @param Resolver $localeResolver
     * @param Context $context
     * @param LayoutFactory $layoutFactory
     * @param ReaderPool $layoutReaderPool
     * @param InlineInterface $translateInline
     * @param BuilderFactory $layoutBuilderFactory
     * @param GeneratorPool $generatorPool
     * @param RendererFactory $pageConfigRendererFactory
     * @param Reader $pageLayoutReader
     * @param DirectoryList $directoryList
     * @param Json $json
     * @param string $template
     * @param AppIcon $appIcon,
     * @param bool $isIsolated
     * @param EntitySpecificHandlesList|null $entitySpecificHandlesList
     * @param null $action
     * @param array $rootTemplatePool
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        Resolver $localeResolver,
        Context $context,
        LayoutFactory $layoutFactory,
        ReaderPool $layoutReaderPool,
        InlineInterface $translateInline,
        BuilderFactory $layoutBuilderFactory,
        GeneratorPool $generatorPool,
        RendererFactory $pageConfigRendererFactory,
        Reader $pageLayoutReader,
        DirectoryList $directoryList,
        Json $json,
        string $template,
        AppIcon $appIcon,
        $isIsolated = false,
        EntitySpecificHandlesList $entitySpecificHandlesList = null,
        $action = null,
        $rootTemplatePool = []
    ) {
        $this->scopeConfig = $context->getScopeConfig();
        $this->storeManager = $storeManager;
        $this->json = $json;
        $this->appIcon = $appIcon;

        parent::__construct(
            $localeResolver,
            $context,
            $layoutFactory,
            $layoutReaderPool,
            $translateInline,
            $layoutBuilderFactory,
            $generatorPool,
            $pageConfigRendererFactory,
            $pageLayoutReader,
            $template,
            $directoryList,
            $isIsolated,
            $entitySpecificHandlesList,
            $action,
            $rootTemplatePool
        );
    }

    /**
     * Get config by section name
     * @param string $sectionName
     * @return array
     * @throws NoSuchEntityException
     */
    public function getThemeConfiguration(
        string $sectionName
    ) {
        return $this->scopeConfig->getValue(
            $sectionName,
            ScopeInterface::SCOPE_STORE,
            $this->storeManager->getStore()->getId()
        );
    }

    /**
     * Get store list json
     *
     * @return bool|false|string
     */
    public function getStoreListJson()
    {
        $result = [];
        $storeList = $this->storeManager->getStores();

        foreach ($storeList as $store) {
            $result[] = $store->getCode();
        }

        return $this->json->serialize($result);
    }

    /**
     * @return array[]
     */
    public function getAppIconData()
    {
        return $this->appIcon->getIconLinks();
    }

    /**
     * @return string
     */
    public function getWebsiteCode()
    {
        return $this->storeManager->getWebsite()->getCode();
    }
}
