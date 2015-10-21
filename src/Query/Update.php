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

use JBZoo\SqlBuilder\Exception;

/**
 * Class Update
 * @package JBZoo\SqlBuilder
 */
class Update extends Query
{
    /**
     * Query scheme
     * @var array
     */
    protected $_blocks = array(
        'options' => null,
        'table'   => null,
        'set'     => null,
        'where'   => null,
        'order'   => null,
        'limit'   => null,
    );

    /**
     * Constructor
     * @param array|string $tableName
     * @param string       $alias
     * @throws Exception
     */
    public function __construct($tableName, $alias = null)
    {
        $this->table($tableName, $alias);
    }

    /**
     * @param string|array $optionName
     * @return $this
     */
    public function option($optionName)
    {
        $this->_append('Options', $optionName, 'UPDATE');
        return $this;
    }

    /**
     * @param string $tableName
     * @param string $alias
     * @return $this
     */
    public function table($tableName, $alias = null)
    {
        return $this->_append('Table', $tableName, $alias);
    }

    /**
     * @param array|string $data
     * @param string       $newValue
     * @return $this
     */
    public function set($data, $newValue = null)
    {
        return $this->_append('Set', $data, $newValue);
    }

    /**
     * Where conditions
     * @param string $condition
     * @param string $value
     * @param string $logic
     * @return $this
     */
    public function where($condition, $value = null, $logic = 'AND')
    {
        return $this->_append('Where', array($condition, $value), $logic);
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

    /**
     * @return string
     * @throws Exception
     */
    public function __toString()
    {
        return 'UPDATE ' . parent::__toString();
    }
}
