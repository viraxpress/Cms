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

namespace ViraXpress\Cms\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;
use Magento\Cms\Model\BlockFactory;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Cms\Model\PageFactory;
use Magento\Cms\Api\BlockRepositoryInterface;
use Magento\Cms\Api\PageRepositoryInterface;
use Magento\Framework\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\FileSystemException;

class PopulateCmsData implements DataPatchInterface, PatchRevertableInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var BlockFactory
     */
    private $blockFactory;

    /**
     * @var WriterInterface
     */
    protected $configWriter;

    /**
     * @var PageFactory
     */
    protected $pageFactory;

    /**
     * @var BlockRepositoryInterface
     */
    protected $blockRepository;

    /**
     * @var PageRepositoryInterface
     */
    protected $pageRepository;

    /**
     * @var DirectoryList
     */
    protected $directory;

    /**
     * @var File
     */
    protected $file;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param BlockFactory $blockFactory
     * @param WriterInterface $configWriter
     * @param PageFactory $pageFactory
     * @param BlockRepositoryInterface $blockRepository
     * @param PageRepositoryInterface $pageRepository
     * @param DirectoryList $directory
     * @param File $file
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        BlockFactory $blockFactory,
        WriterInterface $configWriter,
        PageFactory $pageFactory,
        BlockRepositoryInterface $blockRepository,
        PageRepositoryInterface $pageRepository,
        DirectoryList $directory,
        File $file
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->blockFactory = $blockFactory;
        $this->configWriter = $configWriter;
        $this->pageFactory = $pageFactory;
        $this->blockRepository = $blockRepository;
        $this->pageRepository = $pageRepository;
        $this->directory = $directory;
        $this->file = $file;
    }

    /**
     * Apply patch
     */
    public function apply()
    {
        $this->moduleDataSetup->startSetup();
        $filePath = BP . "/vendor/viraxpress/frontend/cms/home.html";

        //Create header static block
        $cmsBlockData = [
            'title' => 'Announcements',
            'identifier' => 'viraxpress_cms_announcement',
            'stores' => ['0'],
            'is_active' => 1,
            'content' => '<p><span style="font-size: 16px; color: #ffffff;">Exciting Announcement: Introducing ViraXpress!</span></p>'
        ];
        $this->blockFactory->create()->setData($cmsBlockData)->save();
        $path = 'viraxpress_config/header/block_id';
        $value = 'viraxpress_cms_announcement';
        $this->configWriter->save($path, $value, $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $scopeId = 0);

        // Create footer static extra block
        $cmsBlockData = [
            'title' => 'Footer App Download',
            'identifier' => 'viraxpress_app_download',
            'stores' => ['0'],
            'is_active' => 1,
            'content' => 
            '
            <div class="block">
                <p class="font-semibold leading-6 lg:text-right text-gray-800">Download Now</p>
                <ul class="footer links mt-5 lg:text-right">
                    <li>
                        <a target="_blank" title="Android Store"
                        class="inline-block leading-6 lg:p-[0.2em] text-gray-700 hover:text-gray-800 hover:underline"
                        href="#">
                            <picture>
                                <source srcset="{{media url=wysiwyg/gplay-store-360.webp}}" media="(max-width: 600px)">
                                <source srcset="{{media url=wysiwyg/gplay-store-360.webp}}" media="(min-width: 601px) and (max-width: 1200px)">
                                <source srcset="{{media url=wysiwyg/gplay-store-360.webp}}" media="(min-width: 1200px) and (max-width: 1366px)">
                                <source srcset="{{media url=wysiwyg/gplay-store-360.webp}}" media="(min-width: 1440px) and (max-width: 1800px)">
                                <source srcset="{{media url=wysiwyg/gplay-store-360.webp}}" media="(min-width: 1801px)">
                                <img loading="lazy" width="130" height="40" src="{{media url=wysiwyg/gplay-store-360.webp}}" alt="Responsive Image">
                            </picture>
                        </a>
                    </li>
                    <li>
                        <a target="_blank" title="App Store"
                        class="inline-block leading-6 lg:p-[0.2em] text-gray-700 hover:text-gray-800 hover:underline"
                        href="#">
                        <picture>
                            <source srcset="{{media url=wysiwyg/app-store-360.webp}}" media="(max-width: 600px)">
                            <source srcset="{{media url=wysiwyg/app-store-360.webp}}" media="(min-width: 601px) and (max-width: 1200px)">
                            <source srcset="{{media url=wysiwyg/app-store-360.webp}}" media="(min-width: 1200px) and (max-width: 1366px)">
                            <source srcset="{{media url=wysiwyg/app-store-360.webp}}" media="(min-width: 1440px) and (max-width: 1800px)">
                            <source srcset="{{media url=wysiwyg/app-store-360.webp}}" media="(min-width: 1801px)">
                            <img loading="lazy" width="130" height="40" src="{{media url=wysiwyg/app-store-360.webp}}" alt="Responsive Image">
                        </picture>
                        </a>
                    </li>
                </ul>
            </div>
            '
        ];
        $this->blockFactory->create()->setData($cmsBlockData)->save();
        $path = 'viraxpress_config/footer/extra_block_id';
        $value = 'viraxpress_app_download';
        $this->configWriter->save($path, $value, $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $scopeId = 0);

        // Create footer static link block
        $cmsBlockData = [
            'title' => 'Footer Links Block',
            'identifier' => 'viraxpress_footer_links_block',
            'stores' => ['0'],
            'is_active' => 1,
            'content' =>
            '
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <p class="font-semibold leading-6 text-gray-800">Company</p>
                    <ul class="footer links mt-5 lg:space-y-3">
                        <li>
                            <a class="inline-block leading-6 p-[0.8em] lg:p-[0.2em] text-gray-700 hover:text-gray-800 hover:underline"
                                href="{{store url="about-us"}}">
                                About us
                            </a>
                        </li>
                        <li>
                            <a class="inline-block leading-6 p-[0.8em] lg:p-[0.2em] text-gray-700 hover:text-gray-800 hover:underline"
                                href="{{store url="customer-service"}}">
                                Customer Service
                            </a>
                        </li>
                        <li>
                            <a class="inline-block leading-6 p-[0.8em] lg:p-[0.2em] text-gray-700 hover:text-gray-800 hover:underline"
                                href="{{store url="#"}}">
                                FAQ
                            </a>
                        </li>
                        <li>
                            <a class="inline-block leading-6 p-[0.8em] lg:p-[0.2em] text-gray-700 hover:text-gray-800 hover:underline"
                                href="{{store url="#"}}">
                                Blog
                            </a>
                        </li>
                        <li>
                            <a class="inline-block leading-6 p-[0.8em] lg:p-[0.2em] text-gray-700 hover:text-gray-800 hover:underline"
                                href="{{store url="#"}}">
                                News
                            </a>
                        </li>
                    </ul>
                </div>
                <div>
                    <p class="font-semibold leading-6 text-gray-800">Legal</p>
                    <ul class="footer links mt-5 lg:space-y-3">
                        <li>
                            <a class="inline-block leading-6 p-[0.8em] lg:p-[0.2em] text-gray-700 hover:text-gray-800 hover:underline"
                                href="{{store url="#"}}">
                                Terms and Conditions
                            </a>
                        </li>
                        <li>
                            <a class="inline-block leading-6 p-[0.8em] lg:p-[0.2em] text-gray-700 hover:text-gray-800 hover:underline"
                                href="{{store url="#"}}">
                                Refund Policy
                            </a>
                        </li>
                        <li>
                            <a class="inline-block leading-6 p-[0.8em] lg:p-[0.2em] text-gray-700 hover:text-gray-800 hover:underline"
                                href="{{store url="#"}}">
                                License
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            '
        ];
        $this->blockFactory->create()->setData($cmsBlockData)->save();
        $path = 'viraxpress_config/footer/block_id';
        $value = 'viraxpress_footer_links_block';
        $this->configWriter->save($path, $value, $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $scopeId = 0);

        $cmsBlockData = [
            'title' => 'viraxpress blogs',
            'identifier' => 'viraxpress_blogs',
            'stores' => ['0'],
            'is_active' => 1,
            'content' =>
            '
            <div class="splide products-slider blogs-slider">
                <div class="splide__track">
                    <div class="splide__list">
                        <div class="splide__slide">
                            <div class="rounded-md overflow-hidden"> <img src="{{media url=wysiwyg/blog-1.webp}}" alt="" />
                                <div class="p-4">
                                    <h3 class="font-medium text-gray-900 leading-normal text-left mb-5">Anim sint Lorem excepteur commodo</h3>
                                    <p class="text-sm m-0 text-gray-600">Oct 12, 2022</p>
                                </div>
                            </div>
                        </div>
                        <div class="splide__slide">
                            <div class="rounded-md overflow-hidden"> <img src="{{media url=wysiwyg/blog-2.webp}}" alt="" />
                                <div class="p-4">
                                    <h3 class="font-medium text-gray-900 leading-normal text-left mb-5">Anim sint Lorem excepteur commodo</h3>
                                    <p class="text-sm m-0 text-gray-600">Oct 12, 2022</p>
                                </div>
                            </div>
                        </div>
                        <div class="splide__slide">
                            <div class="rounded-md overflow-hidden"> <img src="{{media url=wysiwyg/blog-3.webp}}" alt="" />
                                <div class="p-4">
                                    <h3 class="font-medium text-gray-900 leading-normal text-left mb-5">Anim sint Lorem excepteur commodo</h3>
                                    <p class="text-sm m-0 text-gray-600">Oct 12, 2022</p>
                                </div>
                            </div>
                        </div>
                        <div class="splide__slide">
                            <div class="rounded-md overflow-hidden"> <img src="{{media url=wysiwyg/blog-1.webp}}" alt="" />
                                <div class="p-4">
                                    <h3 class="font-medium text-gray-900 leading-normal text-left mb-5">Anim sint Lorem excepteur commodo</h3>
                                    <p class="text-sm m-0 text-gray-600">Oct 12, 2022</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            '
        ];
        $newBlock = $this->blockFactory->create();
        $newBlock->setData($cmsBlockData);
        $newBlock->save();
        $content1 = $this->file->read($filePath);
        $modifiedContent1 = str_replace("viraxpress_blogs", $newBlock->getId(), $content1);
        $this->file->write($filePath, $modifiedContent1);

        $cmsBlockData = [
            'title' => 'ViraXpress - Event promotion 1',
            'identifier' => 'viraxpress-event-promotion-1',
            'stores' => ['0'],
            'is_active' => 1,
            'content' =>
            '
            <style>
                #html-body [data-pb-style=OBHBFOO]{justify-content:flex-start;display:flex;flex-direction:column;background-position:left top;background-size:cover;background-repeat:no-repeat;background-attachment:scroll}#html-body [data-pb-style=OGLG476]{border-style:none}#html-body [data-pb-style=P0OQO4D],#html-body [data-pb-style=R454LCG]{max-width:100%;height:auto}#html-body [data-pb-style=L4E3H19]{background-position:left top;background-size:cover;background-repeat:no-repeat;background-attachment:scroll;align-self:stretch}#html-body [data-pb-style=J1QL72T]{display:flex;width:100%}#html-body [data-pb-style=Q693CWT]{justify-content:flex-start;display:flex;flex-direction:column;background-position:left top;background-size:cover;background-repeat:no-repeat;background-attachment:scroll;width:100%;align-self:stretch}#html-body [data-pb-style=J4YLE37]{display:inline-block}#html-body [data-pb-style=VU5EF09]{text-align:center}@media only screen and (max-width: 768px) { #html-body [data-pb-style=OGLG476]{border-style:none} }
            </style>
            <div class="relative full-h-cover" data-content-type="row" data-appearance="full-bleed" data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true"
            data-video-lazy-load="true" data-video-fallback-src="" data-element="main" data-pb-style="OBHBFOO">
                <figure class="w-full" data-content-type="image" data-appearance="full-width" data-element="main" data-pb-style="OGLG476"><img class="pagebuilder-mobile-hidden" src="{{media url=wysiwyg/second_banner2.webp}}" alt="" title="" data-element="desktop_image" data-pb-style="P0OQO4D"><img class="pagebuilder-mobile-only" src="{{media url=wysiwyg/second_banner2.webp}}" alt=""
                    title="" data-element="mobile_image" data-pb-style="R454LCG"></figure>
                <div class="pagebuilder-column-group w-full absolute h-full inset-0 z-10 p-8 flex" data-background-images="{}" data-content-type="column-group" data-appearance="default" data-grid-size="12"
                data-element="main" data-pb-style="L4E3H19">
                    <div class="pagebuilder-column-line" data-content-type="column-line" data-element="main" data-pb-style="J1QL72T">
                        <div class="pagebuilder-column w-full" data-content-type="column" data-appearance="full-height" data-background-images="{}" data-element="main" data-pb-style="Q693CWT">
                            <h2 class="text-2xl text-black font-medium" data-content-type="heading" data-appearance="default" data-element="main">Smooth &amp; Bright Skin</h2>
                            <div data-content-type="text" data-appearance="default" data-element="main">
                                <p><span style="font-size: 14px;">A true jungle companion, this watch is fully waterproof with bonded leather and swiss movement.</span></p>
                            </div>
                            <div data-content-type="buttons" data-appearance="inline" data-same-width="false" data-element="main" class="mt-5">
                                <div data-content-type="button-item" data-appearance="default" data-element="main" data-pb-style="J4YLE37"><a class="pagebuilder-button-secondary" href="#" target="" data-link-type="product"
                                    data-element="link" data-pb-style="VU5EF09"><span data-element="link_text">Shop</span></a></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            '
        ];
        $newBlock = $this->blockFactory->create();
        $newBlock->setData($cmsBlockData);
        $newBlock->save();
        $content2 = $this->file->read($filePath);
        $modifiedContent2 = str_replace("viraxpress-event-promotion-1", $newBlock->getId(), $content2);
        $this->file->write($filePath, $modifiedContent2);

        $cmsBlockData = [
            'title' => 'ViraXpress - Event promotion 2',
            'identifier' => 'viraxpress-event-promotion-2',
            'stores' => ['0'],
            'is_active' => 1,
            'content' =>
            '
            <style>
                #html-body [data-pb-style=CB9S828]{justify-content:flex-start;display:flex;flex-direction:column;background-position:left top;background-size:cover;background-repeat:no-repeat;background-attachment:scroll}#html-body [data-pb-style=EI9KS5L]{border-style:none}#html-body [data-pb-style=HI6187H],#html-body [data-pb-style=LTM75WR]{max-width:100%;height:auto}#html-body [data-pb-style=F0RRCOX]{background-position:left top;background-size:cover;background-repeat:no-repeat;background-attachment:scroll;align-self:stretch}#html-body [data-pb-style=U867BRA]{display:flex;width:100%}#html-body [data-pb-style=T6OHP94]{justify-content:flex-start;display:flex;flex-direction:column;background-position:left top;background-size:cover;background-repeat:no-repeat;background-attachment:scroll;width:100%;align-self:stretch}#html-body [data-pb-style=PLT722K]{display:inline-block}#html-body [data-pb-style=IWFFHLA]{text-align:center}@media only screen and (max-width: 768px) { #html-body [data-pb-style=EI9KS5L]{border-style:none} }
            </style>
            <div class="relative promotion-left-2" data-content-type="row" data-appearance="full-bleed" data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true"
            data-video-lazy-load="true" data-video-fallback-src="" data-element="main" data-pb-style="CB9S828">
                <figure class="w-full" data-content-type="image" data-appearance="full-width" data-element="main" data-pb-style="EI9KS5L"><img class="pagebuilder-mobile-hidden" src="{{media url=wysiwyg/second_2banner.webp}}" alt="" title="" data-element="desktop_image" data-pb-style="HI6187H"><img class="pagebuilder-mobile-only" src="{{media url=wysiwyg/second_2banner.webp}}" alt=""
                    title="" data-element="mobile_image" data-pb-style="LTM75WR"></figure>
                <div class="pagebuilder-column-group w-full absolute h-full inset-0 z-10 p-8 flex" data-background-images="{}" data-content-type="column-group" data-appearance="default" data-grid-size="12"
                data-element="main" data-pb-style="F0RRCOX">
                    <div class="pagebuilder-column-line" data-content-type="column-line" data-element="main" data-pb-style="U867BRA">
                        <div class="pagebuilder-column w-full" data-content-type="column" data-appearance="full-height" data-background-images="{}" data-element="main" data-pb-style="T6OHP94">
                            <h2 class="text-2xl text-black font-medium" data-content-type="heading" data-appearance="default" data-element="main">Smooth &amp; Bright Skin</h2>
                            <div data-content-type="text" data-appearance="default" data-element="main">
                                <p><span style="font-size: 14px;">A true jungle companion, this watch is fully waterproof with bonded leather and swiss movement.</span></p>
                            </div>
                            <div data-content-type="buttons" data-appearance="inline" data-same-width="false" data-element="main" class="mt-5">
                                <div data-content-type="button-item" data-appearance="default" data-element="main" data-pb-style="PLT722K"><a class="pagebuilder-button-secondary" href="#" target="" data-link-type="category"
                                    data-element="link" data-pb-style="IWFFHLA"><span data-element="link_text">Shop</span></a></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            '
        ];
        $newBlock = $this->blockFactory->create();
        $newBlock->setData($cmsBlockData);
        $newBlock->save();
        $content3 = $this->file->read($filePath);
        $modifiedContent3 = str_replace("viraxpress-event-promotion-2", $newBlock->getId(), $content3);
        $this->file->write($filePath, $modifiedContent3);

        $cmsBlockData = [
            'title' => 'ViraXpress - Event promotion 3',
            'identifier' => 'viraxpress-event-promotion-3',
            'stores' => ['0'],
            'is_active' => 1,
            'content' =>
            '
            <style>
                #html-body [data-pb-style=DBOUCQM]{justify-content:flex-start;display:flex;flex-direction:column;background-position:left top;background-size:cover;background-repeat:no-repeat;background-attachment:scroll}#html-body [data-pb-style=M5X4SVV]{border-style:none}#html-body [data-pb-style=AOL1DA9],#html-body [data-pb-style=J9A2N9A]{max-width:100%;height:auto}#html-body [data-pb-style=C6K5K14]{background-position:left top;background-size:cover;background-repeat:no-repeat;background-attachment:scroll;align-self:stretch}#html-body [data-pb-style=M2D2RS5]{display:flex;width:100%}#html-body [data-pb-style=QIQ7Y9G]{justify-content:flex-start;display:flex;flex-direction:column;background-position:left top;background-size:cover;background-repeat:no-repeat;background-attachment:scroll;width:100%;align-self:stretch}#html-body [data-pb-style=ESF8DNI]{display:inline-block}#html-body [data-pb-style=UXK7S4S]{text-align:center}@media only screen and (max-width: 768px) { #html-body [data-pb-style=M5X4SVV]{border-style:none} }
            </style>
            <div class="relative promotion-left-1" data-content-type="row" data-appearance="full-bleed" data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image" data-video-loop="true" data-video-play-only-visible="true"
            data-video-lazy-load="true" data-video-fallback-src="" data-element="main" data-pb-style="DBOUCQM">
                <figure class="w-full" data-content-type="image" data-appearance="full-width" data-element="main" data-pb-style="M5X4SVV"><img class="pagebuilder-mobile-hidden" src="{{media url=wysiwyg/second_1banner.webp}}" alt="" title="" data-element="desktop_image" data-pb-style="J9A2N9A"><img class="pagebuilder-mobile-only" src="{{media url=wysiwyg/second_1banner.webp}}" alt=""
                    title="" data-element="mobile_image" data-pb-style="AOL1DA9"></figure>
                <div class="pagebuilder-column-group w-full absolute h-full inset-0 z-10 p-8 flex" data-background-images="{}" data-content-type="column-group" data-appearance="default" data-grid-size="12"
                data-element="main" data-pb-style="C6K5K14">
                    <div class="pagebuilder-column-line" data-content-type="column-line" data-element="main" data-pb-style="M2D2RS5">
                        <div class="pagebuilder-column w-full" data-content-type="column" data-appearance="full-height" data-background-images="{}" data-element="main" data-pb-style="QIQ7Y9G">
                            <h2 class="text-2xl text-black font-medium" data-content-type="heading" data-appearance="default" data-element="main">Smooth &amp; Bright Skin</h2>
                            <div data-content-type="text" data-appearance="default" data-element="main">
                                <p><span style="font-size: 14px;">A true jungle companion, this watch is fully waterproof with bonded leather and swiss movement.</span></p>
                            </div>
                            <div data-content-type="buttons" data-appearance="inline" data-same-width="false" data-element="main" class="mt-5">
                                <div data-content-type="button-item" data-appearance="default" data-element="main" data-pb-style="ESF8DNI"><a class="pagebuilder-button-secondary" href="#" target="" data-link-type="category"
                                    data-element="link" data-pb-style="UXK7S4S"><span data-element="link_text">Shop</span></a></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            '
        ];
        $newBlock = $this->blockFactory->create();
        $newBlock->setData($cmsBlockData);
        $newBlock->save();
        $content4 = $this->file->read($filePath);
        $modifiedContent4 = str_replace("viraxpress-event-promotion-3", $newBlock->getId(), $content4);
        $this->file->write($filePath, $modifiedContent4);

        $cmsBlockData = [
            'title' => 'ViraXpress - Our Story Video',
            'identifier' => 'viraxpress-our-story-video',
            'stores' => ['0'],
            'is_active' => 1,
            'content' =>
            '
            <div data-content-type="html" data-appearance="default" data-element="main">&lt;div x-data="handleVideo()" x-on:keyup.escape="showModal=false"&gt; &lt;div class="absolute bottom-0 left-1/2 -translate-x-1/2 z-0 bg-black/30 rounded-md w-calc-100-minus-50 h-calc-100-minus-124"&gt;&lt;/div&gt; &lt;div class="absolute bottom-0 left-1/2
                -translate-x-1/2 z-10 w-calc-100-minus-50 h-calc-100-minus-124 flex justify-center items-center"&gt; &lt;button type="button" class="rounded text-white flex justify-start items-center gap-2 text-center" title="Watch A Video" x-on:click="showModal=true"&gt;
                &lt;span class="p-2 rounded-full flex justify-center items-center animation-pulse"&gt; &lt;svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="0.5" stroke="currentColor" class="size-24"&gt; &lt;path stroke-linecap="round"
                stroke-linejoin="round" d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /&gt; &lt;path stroke-linecap="round" stroke-linejoin="round" class="fill-white" d="M15.91 11.672a.375.375 0 0 1 0 .656l-5.603 3.113a.375.375 0 0 1-.557-.328V8.887c0-.286.307-.466.557-.327l5.603
                3.112Z" /&gt; &lt;/svg&gt; &lt;/span&gt; &lt;/button&gt; &lt;/div&gt; &lt;/div&gt; &lt;script&gt; function
                handleVideo() { return { showModal: false, } } &lt;/script&gt;
            </div>
            '
        ];
        $newBlock = $this->blockFactory->create();
        $newBlock->setData($cmsBlockData);
        $newBlock->save();
        $content5 = $this->file->read($filePath);
        $modifiedContent5 = str_replace("viraxpress-our-story-video", $newBlock->getId(), $content5);
        $this->file->write($filePath, $modifiedContent5);

        // Create static home page
        if (file_exists($filePath)) {
            $pageContent = file_get_contents($filePath);
            $pageData = [
                'title' => 'Home page',
                'identifier' => 'viraxpress-cms-page',
                'content_heading' => '',
                'content' => $pageContent,
                'is_active' => 1,
                'stores' => [0],
                'sort_order' => 0,
                'page_layout' => 'cms-full-width'
            ];

            $page = $this->pageFactory->create()->setData($pageData)->save();
        }
        $this->configWriter->save('web/default/cms_home_page', $page->getIdentifier());
        $type = 'block';
        $blockDirectoryPath = $this->directory->getPath('media') . DIRECTORY_SEPARATOR . 'cms' . DIRECTORY_SEPARATOR . $type . DIRECTORY_SEPARATOR;

        if (!$this->file->checkAndCreateFolder($blockDirectoryPath)) {
            throw new LocalizedException(__('Could not create directory: %1', $blockDirectoryPath));
        }

        $blockIdentifiers = [
            'viraxpress_footer_links_block',
            'viraxpress_footer_links_block',
            'viraxpress-event-promotion-1',
            'viraxpress-event-promotion-2',
            'viraxpress-event-promotion-3',
            'viraxpress-our-story-video'
        ];

        foreach ($blockIdentifiers as $identifier) {
            $block = $this->blockRepository->getById($identifier);
            $blockId = $block->getBlockId();
            $filePath = $blockDirectoryPath . $blockId . '.html';
            if (!$this->file->write($filePath, $block->getContent())) {
                throw new LocalizedException(__('Could not write to file: %1', $filePath));
            }
        }

        $page = $this->pageRepository->getById('viraxpress-cms-page');
        $pageId = $page->getId();
        $type = 'page';
        $pageDirectoryPath = $this->directory->getPath('media') . DIRECTORY_SEPARATOR . 'cms' . DIRECTORY_SEPARATOR . $type . DIRECTORY_SEPARATOR;
        if (!$this->file->checkAndCreateFolder($pageDirectoryPath)) {
            throw new LocalizedException(__('Could not create directory: %1', $pageDirectoryPath));
        }
        $filePath = $pageDirectoryPath . $pageId . '.html';
        if (!$this->file->write($filePath, $page->getContent())) {
            throw new LocalizedException(__('Could not write to file: %1', $filePath));
        }

        $this->moduleDataSetup->endSetup();
    }

    /**
     * Revert patch
     *
     * @return array
     */
    public function revert()
    {
        return [];
    }

    /**
     * Get dependencies for the patch
     *
     * @return array
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * Get aliases for the patch
     *
     * @return array
     */
    public function getAliases()
    {
        return [];
    }
}
