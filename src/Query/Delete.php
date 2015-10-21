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
 * Class Delete
 * @package JBZoo\SqlBuilder
 */
class Delete extends Query
{
    /**
     * Query scheme
     * @var array
     */
    protected $_blocks = array(
        'options' => null,
        'from'    => null,
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
        $this->from($tableName, $alias);
    }

    /**
     * Add a table to the FROM clause of the query.
     *
     * @param  string $tableName A string or array of table names.
     * @param  string $alias     Alias table name
     * @return $this
     */
    public function from($tableName, $alias = null)
    {
        return $this->_append('From', $tableName, $alias);
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
     * @param string|array $optionName
     * @return $this
     */
    public function option($optionName)
    {
        return $this->_append('Options', $optionName, 'DELETE');
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
        return 'DELETE ' . parent::__toString();
    }
}
