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

/**
 * Class Table
 * @package JBZoo\SqlBuilder\Block
 */
class Table extends From
{
    /**
     * Magic function to convert the query element to a string
     * @return string
     */
    public function __toString()
    {
        return implode(', ', $this->_conditions);
    }
}
