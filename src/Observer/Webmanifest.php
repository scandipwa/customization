<?php
/**
 * @category ScandiPWA
 * @package ScandiPWA\Customization
 * @author Rihards Abolins <info@scandiweb.com>
 * @copyright Copyright (c) 2015 Scandiweb, Ltd (http://scandiweb.com)
 * @license http://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
 */

namespace ScandiPWA\Customization\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use ScandiPWA\Customization\Controller\Webmanifest as WebmanifestController;
use ScandiPWA\Customization\Controller\AppIcon;

/**
 * Class Webmanifest
 * @package ScandiPWA\Customization\Observer
 */
class Webmanifest implements ObserverInterface
{
    /**
     * @var WebmanifestController
     */
    protected $webmanifestController;

    protected $appIcon;

    public function __construct(
        WebmanifestController $webmanifestController,
        AppIcon $appIcon
    )
    {
        $this->webmanifestController = $webmanifestController;
        $this->appIcon = $appIcon;
    }

    /**
     * @param Observer $observer
     * @throws NoSuchEntityException
     */
    public function execute(Observer $observer)
    {
        $data = $this->webmanifestController->load();
        $data['icons'] = $this->appIcon->getIconData();
        $this->webmanifestController->saveJson($data);
    }
}
