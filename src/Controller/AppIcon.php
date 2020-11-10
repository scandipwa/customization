<?php
/**
 * @category ScandiPWA
 * @package ScandiPWA\Customization
 * @author Rihards Abolins <info@scandiweb.com>
 * @copyright Copyright (c) 2015 Scandiweb, Ltd (http://scandiweb.com)
 * @license http://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
 */

namespace ScandiPWA\Customization\Controller;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Image\AdapterFactory;

/**
 * Class AppIcon
 * @package ScandiPWA\Customization\Controller
 */
class AppIcon
{
    const REFERENCE_IMAGE_PATH = 'favicon/favicon.png';

    const STORAGE_PATH = 'favicon/icons/';

    const IMAGE_RESIZING_CONFIG = [
        'apple' => [
            'type' => 'ios',
            'sizes' => [120, 152, 167, 180, 1024]
        ],
        'apple_startup' => [
            'type' => 'ios_startup',
            'sizes' => [2048, 1668, 1536, 1125, 1242, 750, 640]
        ],
        'android' => [
            'type' => 'android',
            'sizes' => [36, 48, 72, 96, 144, 192, 512]
        ]
    ];

    /**
     * @var Filesystem
     */
    protected $fileSystem;

    /**
     * @var AdapterFactory
     */
    protected $imageFactory;

    /**
     * AppIcon constructor.
     * @param Filesystem $fileSystem
     * @param AdapterFactory $imageFactory
     */
    public function __construct(
        Filesystem $fileSystem,
        AdapterFactory $imageFactory
    )
    {
        $this->imageFactory = $imageFactory;
        $this->fileSystem = $fileSystem;
    }

    /**
     * @param string $name
     * @param int $width
     * @param int $height
     * @param string $absolutePath
     * @return bool
     */
    protected function saveImage ($name, $width, $height, $absolutePath)
    {
        $imageResizedDir = $this->fileSystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath(self::STORAGE_PATH) . $name . '.png';

        $imageResize = $this->imageFactory->create();
        $imageResize->open($absolutePath);
        $imageResize->constrainOnly(true);
        $imageResize->keepTransparency(true);
        $imageResize->keepFrame(false);
        $imageResize->keepAspectRatio(false);
        $imageResize->resize($width,$height);

        try {
            $imageResize->save($imageResizedDir);
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * @return array
     */
    public function getIconData ()
    {
        $output = [];
        foreach (self::IMAGE_RESIZING_CONFIG as $config) {
            foreach ($config['sizes'] as $size) {
                $width = is_array($size) ? $size[0] : $size;
                $height = is_array($size) ? $size[1] : $size;
                $name = 'icon_' . $config['type'] . '_' . $width . 'x' . $height;
                $src = $this->fileSystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath(self::STORAGE_PATH) . $name . '.png';
                $output[] = [
                    'src' => $src,
                    'type' => 'image/png',
                    'size' => $width . 'x' . $height
                ];
            }
        }
        return $output;
    }

    /**
     * @return array[]
     */
    public function getIconLinks ()
    {
        $output = [
            'icon' => [],
            'ios_startup' => []
        ];

        foreach (self::IMAGE_RESIZING_CONFIG as $type => $config) {
            $targetPath = $type === 'apple_startup' ? 'ios_startup' : 'icon';
            foreach (self::IMAGE_RESIZING_CONFIG[1]['sizes'] as $size) {
                $width = is_array($size) ? $size[0] : $size;
                $height = is_array($size) ? $size[1] : $size;
                $size = $width . 'x' . $height;
                $name = 'icon_' . $config['type'] . '_' . $width . 'x' . $height;
                $src = $this->fileSystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath(self::STORAGE_PATH) . $name . '.png';
                $output[$targetPath][$size] = [
                    'href' => $src,
                    'sizes' => $size
                ];
            }
        }

        return $output;
    }

    /**
     * @return bool
     */
    public function buildAppIcons () {
        $absolutePath = $this->fileSystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath(self::REFERENCE_IMAGE_PATH);

        if (!file_exists($absolutePath))
            return false;

        foreach (self::IMAGE_RESIZING_CONFIG as $config) {
            foreach ($config['sizes'] as $size) {
                $width = is_array($size) ? $size[0] : $size;
                $height = is_array($size) ? $size[1] : $size;
                $name = 'icon_' . $config['type'] . '_' . $width . 'x' . $height;
                $this->saveImage($name, $width, $height, $absolutePath);
            }
        }
    }
}
