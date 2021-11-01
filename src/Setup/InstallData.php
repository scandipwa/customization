<?php
namespace ScandiPWA\Customization\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use ScandiPWA\Customization\Controller\AppIcon;
use ScandiPWA\Customization\Controller\Webmanifest as WebmanifestController;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{
    /**
     * @var WebmanifestController
     */
    protected $webmanifestController;

    /**
     * @var AppIcon
     */
    protected $appIcon;

    public function __construct(
        WebmanifestController $webmanifestController,
        AppIcon $appIcon
    )
    {
        $this->webmanifestController = $webmanifestController;
        $this->appIcon = $appIcon;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $data = $this->webmanifestController->load();
        $data['icons'] = $this->appIcon->getIconData();
        $this->webmanifestController->saveJson($data);
    }
}
