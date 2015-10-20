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

use JBZoo\SqlBuilder\Block\Element;
use JBZoo\SqlBuilder\SqlBuilder;

/**
 * Class Query
 * @package JBZoo\SqlBuilder
 */
abstract class Query
{

    protected $_blocks = array();

    /**
     * @return \JBZoo\SqlBuilder\Driver\Driver
     */
    public function getDriver()
    {
        return SqlBuilder::get();
    }

    /**
     * @param string $blockName
     * @param string $name
     * @param array  $conditions
     * @param string $glue
     * @param mixed  $extra
     * @return $this
     */
    protected function _append($blockName, $name, $conditions = null, $glue = ', ', $extra = null)
    {
        $blockName = strtolower($blockName);

        if (array_key_exists($blockName, $this->_blocks)) {
            $className = '\\JBZoo\\SqlBuilder\\Block\\' . ucfirst(strtolower($blockName));

            if (null === $this->_blocks[$blockName]) {
                if (class_exists($className)) {
                    $this->_blocks[$blockName] = new $className($name, $conditions, $glue);
                } else {
                    $this->_blocks[$blockName] = new Element($name, $conditions, $glue);
                }
            }

            /** @var Element $elem */
            $elem = $this->_blocks[$blockName];
            $elem->append($name, $conditions, $extra);
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

        /** @var Element $element */
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
