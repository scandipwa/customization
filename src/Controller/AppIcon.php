<?php
/**
 * @category ScandiPWA
 * @package ScandiPWA\Customization
 * @author Rihards Abolins <info@scandiweb.com>
 * @copyright Copyright (c) 2015 Scandiweb, Ltd (http://scandiweb.com)
 * @license http://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
 */

namespace ScandiPWA\Customization\Controller;

use Magento\Framework\App\Config\ScopeConfigInterface;
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

    const MASKABLE_IMAGE_SIZE = 192;

    const IMAGE_RESIZING_CONFIG = [
        'ios' => [
            'type' => 'ios',
            'sizes' => [120, 152, 167, 180, 1024]
        ],
        'ios_startup' => [
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
    protected function saveImage ($source, $name, $width = null, $height = null)
    {
        $path = $this->fileSystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath(self::STORAGE_PATH) . $name . '.png';
        $this->saveImageWithPath($source, $path, $width, $height);
    }

    /**
     * @param $path
     * @param $width
     * @param $height
     * @param $absolutePath
     * @return bool
     */
    protected function saveImageWithPath ($source, $targetPath, $width = null, $height = null)
    {
        if (!file_exists($source) || !is_file($source)) {
            return false;
        }

        $imageResize = $this->imageFactory->create();
        $imageResize->open($source);
        $imageResize->constrainOnly(true);
        $imageResize->keepTransparency(true);

        // Resizes image
        if ($width !== null && $height !== null) {
            $imageResize->keepFrame(false);
            $imageResize->keepAspectRatio(false);
            $imageResize->resize($width,$height);
        }

        try {
            $imageResize->save($targetPath);
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
                $src = '../' . self::STORAGE_PATH . $name . '.png';
                $purpose = $width === self::MASKABLE_IMAGE_SIZE ? 'any maskable' : 'any';
                $output[] = [
                    'src' => $src,
                    'type' => 'image/png',
                    'sizes' => $width . 'x' . $height,
                    'purpose' => $purpose
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
            'ios' => [],
            'ios_startup' => [],
            'android' => []
        ];

        foreach (self::IMAGE_RESIZING_CONFIG as $type => $config) {
            foreach ($config['sizes'] as $size) {
                $width = is_array($size) ? $size[0] : $size;
                $height = is_array($size) ? $size[1] : $size;
                $size = $width . 'x' . $height;
                $name = 'icon_' . $config['type'] . '_' . $width . 'x' . $height;
                $href = '/media/' . self::STORAGE_PATH . $name . '.png';
                $output[$type][$size] = [
                    'href' => $href,
                    'sizes' => $size
                ];
                $output['icon'][$size] = [
                    'href' => $href,
                    'sizes' => $size
                ];
            }
        }

        return $output;
    }

    /**
     * Creates main favicon icon.
     */
    private function buildFaviconImage($sourcePath)
    {
        $targetPath = $this->fileSystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath(self::REFERENCE_IMAGE_PATH);
        $this->saveImageWithPath($sourcePath, $targetPath);
    }

    /**
     * @return bool
     */
    public function buildAppIcons ($sourcePath)
    {
        if (!file_exists($sourcePath))
            return false;

        $this->buildFaviconImage($sourcePath);

        foreach (self::IMAGE_RESIZING_CONFIG as $config) {
            foreach ($config['sizes'] as $size) {
                $width = is_array($size) ? $size[0] : $size;
                $height = is_array($size) ? $size[1] : $size;
                $name = 'icon_' . $config['type'] . '_' . $width . 'x' . $height;
                $this->saveImage($sourcePath, $name, $width, $height);
            }
        }
    }
}
