<?php
/**
 * JBZoo SqlBuilder
 *
 * This file is part of the JBZoo CCK package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package   SqlBuilder
 * @license   MIT
 * @copyright Copyright (C) JBZoo.com,  All rights reserved.
 * @link      https://github.com/JBZoo/SqlBuilder
 * @author    Denis Smetannikov <denis@jbzoo.com>
 */

namespace JBZoo\SqlBuilder\Query;

use JBZoo\SqlBuilder\Block\Block;
use JBZoo\SqlBuilder\Exception;
use JBZoo\SqlBuilder\SqlBuilder;

/**
 * Class Query
 * @package JBZoo\SqlBuilder
 */
abstract class Query
{
    /**
     * @var array
     */
    protected $_blocks = array();

    /**
     * @return \JBZoo\SqlBuilder\Driver\Driver
     */
    public function getDriver()
    {
        return SqlBuilder::get();
    }

    /**
     * @param string $blockClassName
     * @param null   $conditions
     * @param null   $extra
     * @return $this|Block
     * @throws Exception
     */
    protected function _append($blockClassName, $conditions = null, $extra = null)
    {
        if (strpos($blockClassName, '_') === false) {
            $key     = strtolower($blockClassName);
            $type    = ucfirst($key);
            $subType = '';
        } else {
            list($key, $subType) = explode('_', $blockClassName, 2);
            $key     = strtolower($key);
            $type    = ucfirst($key);
            $subType = ucfirst(strtolower($subType));
        }

        if (array_key_exists($key, $this->_blocks)) {
            $className = '\\JBZoo\\SqlBuilder\\Block\\' . $type . $subType;

            if (null === $this->_blocks[$key]) {
                if (class_exists($className) && !$subType) {
                    $this->_blocks[$key] = new $className($conditions, $extra);
                }
            }

            /** @var Block $block */
            $block = $this->_blocks[$key];

            if ($subType && class_exists($className)) {
                $block = new $className($conditions, $extra);
                $block->append($conditions, $extra);
                return $block;

            } elseif ($block instanceof $className) {
                $block->append($conditions, $extra);
            }
        }

        return $this;
    }

    /**
     * @param string $text
     * @return string
     */
    public function escape($text)
    {
        return $this->getDriver()->escape($text);
    }

    /**
     * @param string $text
     * @param bool   $escape
     * @return string
     */
    public function quote($text, $escape = true)
    {
        return $this->getDriver()->quote($text, $escape);
    }

    /**
     * @param string $text
     * @param string $as
     * @return string
     */
    public function quoteName($text, $as = null)
    {
        return $this->getDriver()->quoteName($text, $as);
    }

    /**
     * @param string $condition
     * @param string $value
     * @return string
     */
    public function clean($condition, $value = null)
    {
        return $this->getDriver()->clean($condition, $value);
    }

    /**
     * @param $blockName
     * @return $this
     */
    public function cleanBlock($blockName)
    {
        $blockName = strtolower($blockName);

        if (isset($this->_blocks[$blockName])) {
            $this->_blocks[$blockName] = null;
        }

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $sql = array();

        /** @var Query $element */
        foreach ($this->_blocks as $element) {
            $sql[] = $element ? $element->__toString() : null;
        }

        $sql = array_filter($sql);
        $sql = trim(implode(' ', $sql));

        if ($prefix = $this->getDriver()->getTablePrfix()) {
            $sql = str_replace('#__', $prefix, $sql);
        }

        return $sql;
    }
}
