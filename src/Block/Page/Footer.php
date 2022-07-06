<?php
/**
 * @category ScandiPWA
 * @package ScandiPWA\Customization
 * @author Aleksandrs Kondratjevs <info@scandiweb.com>
 * @copyright Copyright (c) 2021 Scandiweb, Ltd (http://scandiweb.com)
 * @license http://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
 */

namespace ScandiPWA\Customization\Block\Page;

use Magento\Backend\Block\Page\Footer as CoreFooter;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\Component\ComponentRegistrarInterface;
use Magento\Framework\View\Design\Theme\ListInterface;

class Footer extends CoreFooter
{
    const PACKAGE_JSON_FILE = 'package.json';

    /**
     * ScandiPWA registration theme component name
     */
    const SCANDIPWA_COMPONENT_NAME = 'frontend/scandipwa';

    /**
     * @var ListInterface
     */
    protected $themeList;

    /**
     * @var ComponentRegistrarInterface
     */
    protected $componentRegistrar;

    /**
     * @var string|false
     */
    public $scandiPWAPackgeVersion;

    /**
     * Footer constructor.
     * @param Context $context
     * @param ProductMetadataInterface $productMetadata
     * @param ListInterface $themeList
     * @param ComponentRegistrarInterface $componentRegistrar
     * @param array $data
     */
    public function __construct(
        Context $context,
        ProductMetadataInterface $productMetadata,
        ListInterface $themeList,
        ComponentRegistrarInterface $componentRegistrar,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $productMetadata,
            $data
        );

        $this->themeList = $themeList;
        $this->componentRegistrar = $componentRegistrar;
    }

    /**
     * Gets package.json file from ScandiPWA theme directory and sets in class properties its version
     * In case if no version provided or theme directory doesn't exists, sets as false
     */
    public function getPackageJsonData() {
        $pathToTheme = $this->getScandiPWADirectoryPath();

        if (!$pathToTheme) {
           $this->scandiPWAPackgeJsonData = false;
           return;
        }

        // Since theme registration files are located in separate folder 'magento' we need to fallback one step
        // where is located package.json file
        $pathToTheme = substr($pathToTheme, 0, -7) . self::PACKAGE_JSON_FILE;

        if (file_exists($pathToTheme)) {
            $packageData = json_decode(file_get_contents($pathToTheme), true);
            $this->scandiPWAPackgeVersion = $this->getScandiPWAFromPackageData($packageData);
        } else {
            $this->scandiPWAPackgeVersion = 'n/a';
        }
    }

    /**
     * Checks is ScandiPWA theme presents in theme list
     * and gets it path to directory where it is located
     *
     * @return string|null
     */
    public function getScandiPWADirectoryPath() {
        $themeDirectoryPath = null;

        foreach ($this->themeList as $theme) {
            if (str_contains($theme->getFullPath(), self::SCANDIPWA_COMPONENT_NAME)) {
                $themeDirectoryPath = $this->componentRegistrar->getPath(
                    ComponentRegistrar::THEME,
                    $theme->getFullPath()
                );

                break;
            }
        }

        return $themeDirectoryPath;
    }

    /**
     * @param $data
     * @return string|false
     */
    public function getScandiPWAFromPackageData($data) {
        if (isset($data['dependencies']['@scandipwa/scandipwa'])) {
            return $data['dependencies']['@scandipwa/scandipwa'];
        }

        return $data['version'] ?? false;
    }

    /**
     * @return string|false
     */
    public function getScandiPWAVersion() {
        $this->getPackageJsonData();

        return $this->scandiPWAPackgeVersion;
    }
}
