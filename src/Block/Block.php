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

namespace JBZoo\SqlBuilder\Block;

use JBZoo\SqlBuilder\SqlBuilder;

/**
 * Class Block
 * @package JBZoo\SqlBuilder\Block
 */
class Block
{
    /**
     * @var array An array of conditions
     */
    protected $_conditions = array();

    /**
     * Appends element parts to the internal list.
     * @param string|array $elements
     * @param mixed        $extra
     * @return void
     */
    public function append($elements, $extra = null)
    {
        $this->_conditions = array_merge($this->_conditions, (array)$elements);
    }

    /**
     * @return array
     */
    public function getConditions()
    {
        return $this->_conditions;
    }

    /**
     * @return \JBZoo\SqlBuilder\Driver\Driver|null
     */
    protected function _getDriver()
    {
        return SqlBuilder::get();
    }
}
