<?php
/**
 * @category ScandiPWA
 * @package ScandiPWA\Customization
 * @copyright Copyright (c) 2015 Scandiweb, Ltd (http://scandiweb.com)
 * @license http://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
 */

namespace ScandiPWA\Customization\Model\LayoutDirection\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class LayoutDirection
 * @package ScandiPWA\Customization\Model\LayoutDirection\Source
 */
class LayoutDirection extends AbstractSource
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
            ['value' => 'ltr', 'label' => __('LTR')],
            ['value' => 'rtl', 'label' => __('RTL')]
        ];
    }
}
