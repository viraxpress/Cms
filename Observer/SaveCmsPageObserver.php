<?php
/**
 * ViraXpress - https://www.viraxpress.com
 *
 * LICENSE AGREEMENT
 *
 * This file is part of the ViraXpress package and is licensed under the ViraXpress license agreement.
 * You can view the full license at:
 * https://www.viraxpress.com/license
 *
 * By utilizing this file, you agree to comply with the terms outlined in the ViraXpress license.
 *
 * DISCLAIMER
 *
 * Modifications to this file are discouraged to ensure seamless upgrades and compatibility with future releases.
 *
 * @category    ViraXpress
 * @package     ViraXpress_Cms
 * @author      ViraXpress
 * @copyright   © 2024 ViraXpress (https://www.viraxpress.com/)
 * @license     https://www.viraxpress.com/license
 */

namespace ViraXpress\Cms\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Cms\Model\PageFactory;
use Magento\Cms\Model\BlockFactory;
use Magento\Framework\Filesystem;
use ViraXpress\Cms\Model\NodeVersionFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Shell;
use Magento\Framework\View\DesignInterface;
use ViraXpress\Configuration\Helper\Data;

class SaveCmsPageObserver implements ObserverInterface
{
    /**
     * @var PageFactory
     */
    protected $pageFactory;

    /**
     * @var BlockFactory
     */
    protected $blockFactory;

    /**
     * @var DirectoryList
     */
    protected $directory;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var NodeVersionFactory
     */
    protected $nodeVersionFactory;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var File
     */
    protected $file;

    /**
     * @var Shell
     */
    protected $shell;

    /**
     * @var Data
     */
    protected $dataHelper;

    /**
     * @param PageFactory $pageFactory
     * @param DirectoryList $directory
     * @param BlockFactory $blockFactory
     * @param Filesystem $filesystem
     * @param NodeVersionFactory $nodeVersionFactory
     * @param ScopeConfigInterface $scopeConfig
     * @param File $file
     * @param Shell $shell
     * @param Data $dataHelper
     */
    public function __construct(
        PageFactory $pageFactory,
        DirectoryList $directory,
        BlockFactory $blockFactory,
        Filesystem $filesystem,
        NodeVersionFactory $nodeVersionFactory,
        ScopeConfigInterface $scopeConfig,
        File $file,
        Shell $shell,
        Data $dataHelper
    ) {
        $this->directory = $directory;
        $this->pageFactory = $pageFactory;
        $this->blockFactory = $blockFactory;
        $this->filesystem = $filesystem;
        $this->scopeConfig = $scopeConfig;
        $this->nodeVersionFactory = $nodeVersionFactory;
        $this->file = $file;
        $this->shell = $shell;
        $this->dataHelper = $dataHelper;
    }

    /**
     * Execute method for the observer.
     *
     * This method is called when the observer is triggered.
     * It handles the saving of HTML content to a file and executes a shell script if configured.
     *
     * @param \Magento\Framework\Event\Observer $observer The event observer object.
     * @return void
     * @throws LocalizedException
     */
    public function execute(Observer $observer)
    {
        $object = $observer->getObject();
        if ($object instanceof \Magento\Cms\Model\Page || $object instanceof \Magento\Cms\Model\Block) {
            $htmlContent = $object->getContent();
            if ($htmlContent) {
                $pageId = $object->getPageId();
                $type = ($object instanceof \Magento\Cms\Model\Page) ? 'page' : 'block';
                $directoryPath = $this->directory->getPath('media') . DIRECTORY_SEPARATOR . 'cms' . DIRECTORY_SEPARATOR . $type . DIRECTORY_SEPARATOR;
                
                if (!$this->file->checkAndCreateFolder($directoryPath)) {
                    throw new LocalizedException(__('Could not create directory: %1', $directoryPath));
                }

                if (!$pageId) {
                    $pageId = 'block-' . $object->getBlockId();
                } else {
                    $pageId = 'page-' . $object->getPageId();
                }

                $filePath = $directoryPath . $pageId . '.html';
                if (!$this->file->write($filePath, $htmlContent)) {
                    throw new LocalizedException(__('Could not write to file: %1', $filePath));
                }

                $nodePath = $this->scopeConfig->getValue('viraxpress_config/general/server_npm_node_path');
                if (!empty($nodePath)) {
                    $storeIds = $object->getStoreId();
                    foreach ($storeIds as $storeId) {
                        $themeCode = $this->dataHelper->checkThemePathByStoreId($storeId);
                        if ($themeCode) {
                            $newEnvPath = $this->getCurrentEnvPath() . ":$nodePath";
                            $npmCommand = "sh " . $this->directory->getRoot() . "/pub/vx/{$themeCode}/web/tailwind/run_script.sh";
                            putenv('PATH=' . getenv('PATH') . ':' . $nodePath);
                            $result = $this->shell->execute($npmCommand, [], ['PATH' => $newEnvPath]);
                        }
                    }

                    $nodeVersion = $this->nodeVersionFactory->create();
                    $nodeVersion->setData('version', date('YmdHis'));
                    $nodeVersion->save();
                }
            }
        }
    }

    /**
     * Get current environment PATH
     *
     * @return string
     */
    private function getCurrentEnvPath(): string
    {
        return getenv('PATH') ?: '/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin:/snap/bin';
    }
}
