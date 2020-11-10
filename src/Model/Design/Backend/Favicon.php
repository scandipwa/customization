<?php
/**
 * @category ScandiPWA
 * @package ScandiPWA\Customization
 * @author Rihards Abolins <info@scandiweb.com>
 * @copyright Copyright (c) 2015 Scandiweb, Ltd (http://scandiweb.com)
 * @license http://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
 */

namespace ScandiPWA\Customization\Model\Design\Backend;

use  ScandiPWA\Customization\Controller\AppIcon;
use Magento\Config\Model\Config\Backend\File\RequestData\RequestDataInterface;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\UrlInterface;
use Magento\MediaStorage\Helper\File\Storage\Database;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Theme\Model\Design\Backend\Favicon as SourceFavicon;

/**
 * Class Favicon
 * @package ScandiPWA\Customization\Model\Design\Backend
 */
class Favicon extends SourceFavicon
{
    /**
     * @var AppIcon
     */
    protected $appIcon;

    /**
     * @var Database
     */
    protected $databaseHelper;

    /**
     * Favicon constructor.
     * @param Context $context
     * @param Registry $registry
     * @param ScopeConfigInterface $config
     * @param TypeListInterface $cacheTypeList
     * @param UploaderFactory $uploaderFactory
     * @param RequestDataInterface $requestData
     * @param Filesystem $filesystem
     * @param UrlInterface $urlBuilder
     * @param AppIcon $appIcon
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     * @param Database|null $databaseHelper
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ScopeConfigInterface $config,
        TypeListInterface $cacheTypeList,
        UploaderFactory $uploaderFactory,
        RequestDataInterface $requestData,
        Filesystem $filesystem,
        UrlInterface $urlBuilder,
        AppIcon $appIcon,
        Database $databaseHelper,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ){
        $this->appIcon = $appIcon;
        $this->databaseHelper = $databaseHelper;
        parent::__construct($context, $registry, $config, $cacheTypeList, $uploaderFactory, $requestData, $filesystem, $urlBuilder, $resource, $resourceCollection, $data, $databaseHelper);
    }

    /**
     * @return string
     */
    protected function _getUploadDir() {
        return $this->_mediaDirectory->getRelativePath(self::UPLOAD_DIR);
    }

    /**
     * Generates webmanifest icons
     * 
     * @return Favicon
     */
    public function afterSave() {
        $result = parent::afterSave();
        $this->appIcon->buildAppIcons();
        return $result;
    }

    /**
     * Save uploaded file and remote temporary file before saving config value
     *
     * @return $this
     * @throws LocalizedException
     */
    public function beforeSave() {
        $values = $this->getValue();
        $value = reset($values) ?: [];

        // Need to check name when it is uploaded in the media gallery
        $file = $value['file'] ?? $value['name'] ?? null;
        if (!isset($file)) {
            throw new LocalizedException(
                __('%1 does not contain field \'file\'', $this->getData('field_config/field'))
            );
        }
        if (isset($value['exists'])) {
            $this->setValue($file);
            return $this;
        }

        //phpcs:ignore Magento2.Functions.DiscouragedFunction
        $this->updateMediaDirectory(basename($file), $value['url']);

        return $this;
    }

    /**
     * Move file to the correct media directory
     *
     * @param string $filename
     * @param string $url
     * @throws LocalizedException
     */
    private function updateMediaDirectory(string $filename, string $url) {
        $relativeMediaPath = $this->getRelativeMediaPath($url);
        $tmpMediaPath = $this->getTmpMediaPath($filename);
        $mediaPath = $this->_mediaDirectory->isFile($relativeMediaPath) ? $relativeMediaPath : $tmpMediaPath;
        $destinationMediaPath = $this->_getUploadDir() . '/favicon.png';

        $result = $mediaPath === $destinationMediaPath;
        if (!$result) {

            $result = $this->_mediaDirectory->copyFile(
                $mediaPath,
                $destinationMediaPath
            );
            $this->databaseHelper->renameFile(
                $mediaPath,
                $destinationMediaPath
            );
        }
        if ($result) {
            if ($mediaPath === $tmpMediaPath) {
                $this->_mediaDirectory->delete($mediaPath);
            }
            if ($this->_addWhetherScopeInfo()) {
                $filename = $this->_prependScopeInfo($filename);
            }

            $this->setValue($filename);
        } else {
            $this->unsValue();
        }
    }

    /**
     * Getter for allowed extensions of uploaded files.
     *
     * @return string[]
     */
    public function getAllowedExtensions()
    {
        return ['png'];
    }
}
