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

/**
 * Class Union
 * @package JBZoo\SqlBuilder
 */
class Union extends Query
{
    /**
     * Query scheme
     * @var array
     */
    protected $_blocks = array(
        'union' => null,
        'order' => null,
        'limit' => null,
    );

    /**
     * @param Select|array $select
     * @param string       $mode
     * @return $this
     */
    public function union($select, $mode = null)
    {
        return $this->_append('Union', $select, $mode);
    }

    /**
     * @param string $column
     * @param string $direction
     * @param bool   $quote
     * @return $this
     */
    public function order($column, $direction = 'ASC', $quote = true)
    {
        return $this->_append('Order', array($column, $direction), $quote);
    }

    /**
     * Set query limit
     * @param int $length
     * @param int $offset
     * @return $this
     */
    public function limit($length, $offset = 0)
    {
        return $this->_append('Limit', $length, $offset);
    }
}
