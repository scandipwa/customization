<?php
/**
 * @category ScandiPWA
 * @package ScandiPWA\Customization
 * @author Rihards Abolins <info@scandiweb.com>
 * @copyright Copyright (c) 2015 Scandiweb, Ltd (http://scandiweb.com)
 * @license http://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
 */

namespace ScandiPWA\Customization\Controller;

use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Class Webmanifest
 * @package ScandiPWA\Customization\Controller
 */
class Webmanifest
{
    const WEBMANIFEST_CONFIG_PATH = 'webmanifest_customization/webmanifest/';

    const STORAGE_PATH = 'webmanifest/manifest.json';

    const ALLOWED_FIELDS = [
        'name',
        'short_name',
        'description',
        'background_color',
        'lang',
        'theme_color',
        'start_url',
        'orientation',
        'display',
        'categories',
        'dir',
        'iarc_rating_id',
        'icons',
        'prefer_related_applications',
        'related_applications',
        'scope',
        'screenshots',
        'serviceworker',
        'shortcuts'
    ];

    /**
     * @var WriterInterface
     */
    protected $writer;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var Filesystem
     */
    protected $fileSystem;

    /**
     * Webmanifest constructor.
     * @param WriterInterface $writer
     * @param ScopeConfigInterface $scopeConfig
     * @param Filesystem $fileSystem
     */
    public function __construct(
        WriterInterface $writer,
        ScopeConfigInterface $scopeConfig,
        Filesystem $fileSystem
    )
    {
        $this->writer = $writer;
        $this->scopeConfig = $scopeConfig;
        $this->fileSystem = $fileSystem;
    }

    /**
     * @param array $data
     * @return false|string
     */
    protected function getGeneratedJson(array $data)
    {
        $arrayKeys = array_keys($data);
        if (empty($arrayKeys))
            return false;

        $unSupportedKeys = array_filter($arrayKeys, function ($key) {
            return !in_array($key, self::ALLOWED_FIELDS);
        });

        foreach ($unSupportedKeys as $unSupportedKey) {
            unset($data[$unSupportedKey]);
        }

        return json_encode($data);
    }

    /**
     * @param array $data
     * @throws FileSystemException
     */
    public function saveJson(array $data)
    {
        $jsonData = $this->getGeneratedJson($data);

        if (!$jsonData || empty($data))
            return;

        $fileWriter = $this->fileSystem->getDirectoryWrite(DirectoryList::MEDIA);

        $fileWriter->writeFile(self::STORAGE_PATH, $jsonData);
    }

    /**
     * @return array
     */
    public function load()
    {
        $data = [];
        foreach (self::ALLOWED_FIELDS as $field) {
            $value = $this->scopeConfig->getValue(self::WEBMANIFEST_CONFIG_PATH . $field);
            if (!empty($value)) {
                if (in_array($field, ['background_color', 'theme_color'])) {
                    $value = '#' . $value;
                }
                $data[$field] = $value;
            }

        }
        return $data;
    }
}
