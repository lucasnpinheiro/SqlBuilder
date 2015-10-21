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

use JBZoo\SqlBuilder\Exception;

/**
 * Class Into
 * @package JBZoo\SqlBuilder\Block
 */
class Into extends Block
{
    /**
     * @param array|string $elements
     * @param null         $extra
     * @throws Exception
     */
    public function append($elements, $extra = null)
    {
        if (!$elements) {
            throw new Exception('Table name is undefined');
        }

        parent::append($elements, $extra);
    }

    /**
     * Magic function to convert the query element to a string
     * @return string
     */
    public function __toString()
    {
        return 'INTO ' . implode(', ', $this->_getDriver()->quoteName($this->_conditions));
    }
}
