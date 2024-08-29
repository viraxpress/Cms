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

namespace ViraXpress\Cms\Model;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\DataObject\IdentityInterface;

class NodeVersion extends AbstractModel implements IdentityInterface
{
    public const CACHE_TAG = 'node_npm_version';

    /**
     * @var CacheTag
     */
    protected $_cacheTag = 'node_npm_version';

    /**
     * @var EventPrefix
     */
    protected $_eventPrefix = 'node_npm_version';

    /**
     * Initialize the resource model.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\ViraXpress\Cms\Model\ResourceModel\NodeVersion::class);
    }

    /**
     * Retrieve cache tags associated with the model.
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Retrieve default attribute values for the model.
     *
     * @return array
     */
    public function getDefaultValues()
    {
        $values = [];
        return $values;
    }
}
