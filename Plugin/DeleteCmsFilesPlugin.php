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

namespace ViraXpress\Cms\Plugin;

use Magento\Cms\Model\Page;
use Magento\Cms\Model\Block;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\DirectoryList as DirectoryLists;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Shell;
use Magento\Framework\View\DesignInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\View\Design\Theme\ThemeProviderInterface;

class DeleteCmsFilesPlugin
{
    /**
     * @var DirectoryLists
     */
    protected $directory;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var Shell
     */
    protected $shell;

    /**
     * @var ThemeProviderInterface
     */
    protected $themeProvider;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param Filesystem $filesystem
     * @param DirectoryLists $directory
     * @param ScopeConfigInterface $scopeConfig
     * @param Shell $shell
     * @param StoreManagerInterface $storeManager
     * @param ThemeProviderInterface $themeProvider
     */
    public function __construct(
        Filesystem $filesystem,
        DirectoryLists $directory,
        ScopeConfigInterface $scopeConfig,
        Shell $shell,
        StoreManagerInterface $storeManager,
        ThemeProviderInterface $themeProvider
    ) {
        $this->directory = $directory;
        $this->filesystem = $filesystem;
        $this->scopeConfig = $scopeConfig;
        $this->shell = $shell;
        $this->storeManager = $storeManager;
        $this->themeProvider = $themeProvider;
    }

    /**
     * Executes after deleting a CMS object.
     *
     * This method is executed after a CMS object (e.g., CMS block or CMS page) is deleted.
     * It performs additional cleanup tasks, such as deleting associated files.
     *
     * @param \Magento\Cms\Model\AbstractModel $cmsObject The CMS object that was deleted
     * @param bool $result The result of the delete operation
     * @return bool The result of the delete operation
     */
    public function afterDelete($cmsObject, $result)
    {
        $this->deleteAssociatedFile($cmsObject);
        return $result;
    }

    /**
     * Deletes the associated file for the CMS object.
     *
     * This method is responsible for deleting the associated file (HTML file) for the CMS object
     * (e.g., CMS page or CMS block) after it is deleted from the database.
     * If the CMS object is a page, the file name will be 'page-{page_id}.html'.
     * If the CMS object is a block, the file name will be 'block-{block_id}.html'.
     * Additionally, it may perform additional cleanup tasks, such as running a script.
     *
     * @param \Magento\Cms\Model\AbstractModel $cmsObject The CMS object for which the associated file will be deleted
     * @return void
     */
    protected function deleteAssociatedFile($cmsObject)
    {
        $themeId = $this->scopeConfig->getValue(
            DesignInterface::XML_PATH_THEME_ID,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $this->storeManager->getStore()->getId()
        );
        $theme = $this->themeProvider->getThemeById($themeId);
        $themeCode = $theme->getCode();
        $mediaDirectory = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        if ($cmsObject->getPageId()) {
            $type = 'page';
            $cmsId = 'page-'.$cmsObject->getPageId();
        } else {
            $type = 'block';
            $cmsId = 'block-'.$cmsObject->getBlockId();
        }
        $directoryPath = $this->directory->getPath('media') . DIRECTORY_SEPARATOR . 'cms' . DIRECTORY_SEPARATOR . $type . DIRECTORY_SEPARATOR;
        $filePath = $directoryPath . $cmsId . '.html';
        if ($mediaDirectory->isExist($filePath)) {
            $mediaDirectory->delete($filePath);
            $nodePath = $this->scopeConfig->getValue('viraxpress_config/general/server_npm_node_path');
            if (!empty($nodePath)) {
                $newEnvPath = $this->getCurrentEnvPath() . ":$nodePath";
                $npmCommand = "sh " . $this->directory->getRoot() . "/pub/vx/{$themeCode}/web/tailwind/run_script.sh";
                putenv('PATH=' . getenv('PATH') . ':' . $nodePath);
                $result = $this->shell->execute($npmCommand, [], ['PATH' => $newEnvPath]);
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
