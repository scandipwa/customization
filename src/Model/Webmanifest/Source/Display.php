<?php
/**
 * @category ScandiPWA
 * @package ScandiPWA\Customization
 * @author Rihards Abolins <info@scandiweb.com>
 * @copyright Copyright (c) 2015 Scandiweb, Ltd (http://scandiweb.com)
 * @license http://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
 */

namespace ScandiPWA\Customization\Model\Webmanifest\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class Display
 * @package ScandiPWA\Customization\Model\Webmanifest\Source
 */
class Display extends AbstractSource
{
    /**
     * Retrieve All options
     *
     * @return array
     * @throws NoSuchEntityException
     */
    public function getAllOptions()
    {
        return [
            ['value' => 'fullscreen', 'label' => __('Fullscreen')],
            ['value' => 'standalone', 'label' => __('Standalone')],
            ['value' => 'minimal-ui', 'label' => __('Minimal UI')],
            ['value' => 'browser', 'label' => __('Browser')],
        ];
    }
}
