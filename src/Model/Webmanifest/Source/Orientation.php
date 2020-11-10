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

/**
 * Class Orientation
 * @package ScandiPWA\Customization\Model\Webmanifest\Source
 */
class Orientation extends AbstractSource
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
            ['value' => 'any', 'label' => __('Any')],
            ['value' => 'natural', 'label' => __('Natural')],
            ['value' => 'landscape', 'label' => __('Landscape')],
            ['value' => 'landscape-primary', 'label' => __('Landscape Primary')],
            ['value' => 'landscape-secondary', 'label' => __('Landscape Secondary')],
            ['value' => 'portrait', 'label' => __('Portrait')],
            ['value' => 'portrait-primary', 'label' => __('Portrait Primary')],
            ['value' => 'portrait-secondary', 'label' => __('Portrait Secondary')],
        ];
    }
}
