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

use JBZoo\SqlBuilder\Block\Where;
use JBZoo\SqlBuilder\Block\WhereGroup;
use JBZoo\SqlBuilder\Exception;

/**
 * Class Select
 * @package JBZoo\SqlBuilder
 */
class Select extends Query
{
    /**
     * Query scheme
     * @var array
     */
    protected $_blocks = array(
        'options' => null,
        'select'  => null,
        'from'    => null,
        'join'    => null,
        'where'   => null,
        'group'   => null,
        'having'  => null,
        'order'   => null,
        'limit'   => null,
    );

    /**
     * @var string
     */
    protected $_explain = '';

    /**
     * @param array|string $tableName
     * @param null         $alias
     * @throws Exception
     */
    public function __construct($tableName, $alias = null)
    {
        $this->from($tableName, $alias);
    }

    /**
     * Select columns
     * @param string|array $columns
     * @param bool         $quote
     * @return $this
     */
    public function select($columns, $quote = true)
    {
        return $this->_append('Select', $columns, $quote);
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
     * Where conditions (alias)
     * @param string $condition
     * @param string $value
     * @return $this
     */
    public function whereOR($condition, $value = null)
    {
        return $this->where($condition, $value, 'OR');
    }

    /**
     * Having conditions
     * @param string $condition
     * @param string $value
     * @param string $logic
     * @return $this
     */
    public function having($condition, $value = null, $logic = 'AND')
    {
        return $this->_append('Having', array($condition, $value), $logic);
    }

    /**
     * Where conditions group
     * @param array  $conditions
     * @param string $logic
     * @return $this
     */
    public function whereGroup($conditions, $logic = 'AND')
    {
        /** @var WhereGroup $whereGroup */
        $whereGroup = $this->_append('Where_Group', $conditions, $logic);
        if ($group = $whereGroup->__toString()) {
            $this->where('(' . $whereGroup . ')', null, $logic);
        }

        return $this;
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
     * Add a JOIN clause to the query.
     * @param string $type      The type of join. This string is prepended to the JOIN keyword.
     * @param string $table     A table name
     * @param string $condition A string or array of conditions.
     * @return $this
     */
    public function join($type, $table, $condition)
    {
        return $this->_append('Join', array($table, $condition), $type);
    }

    /**
     * Add a LEFT JOIN clause to the query.
     * @param string $tableName
     * @param string $conditions
     * @return $this
     */
    public function leftJoin($tableName, $conditions)
    {
        return $this->join('LEFT', $tableName, $conditions);
    }

    /**
     * Add a RIGHT JOIN clause to the query.
     * @param string $tableName
     * @param string $conditions
     * @return $this
     */
    public function rightJoin($tableName, $conditions)
    {
        return $this->join('RIGHT', $tableName, $conditions);
    }

    /**
     * Add a INNER JOIN clause to the query.
     * @param string $tableName
     * @param string $conditions
     * @return $this
     */
    public function innerJoin($tableName, $conditions)
    {
        return $this->join('INNER', $tableName, $conditions);
    }

    /**
     * @param string $column
     * @param bool   $quote
     * @return $this
     */
    public function group($column, $quote = true)
    {
        return $this->_append('Group', $column, $quote);
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
        return $this->_append('Options', $optionName, 'SELECT');
    }

    /**
     * @param bool $isShow
     * @return $this
     */
    public function explain($isShow = true)
    {
        if ($isShow) {
            $this->_explain = 'EXPLAIN ';
        } else {
            $this->_explain = '';
        }

        return $this;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function __toString()
    {
        if (!isset($this->_blocks['select'])) {
            $this->select('*');
        }

        return $this->_explain . 'SELECT ' . parent::__toString();
    }
}
