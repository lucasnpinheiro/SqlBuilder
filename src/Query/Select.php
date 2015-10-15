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

use JBZoo\SqlBuilder\Element\Where;
use JBZoo\SqlBuilder\Exception;

/**
 * Class QuerySelect
 * @package JBZoo\SqlBuilder
 */
class Select extends Query
{
    /**
     * Query scheme
     * @var array
     */
    protected $elements = array(
        'select' => null,
        'option' => null,
        'from'   => null,
        'join'   => null,
        'where'  => null,
        'group'  => null,
        'having' => null,
        'order'  => null,
        'limit'  => null,
    );

    /**
     * Constructor
     * @param array|string $tableName
     * @throws Exception
     */
    public function __construct($tableName)
    {
        if (!$tableName) {
            throw new Exception('Table name is undefined');
        }

        if (is_array($tableName)) {
            if (count($tableName) == 2) {
                $this->from($tableName[0], $tableName[1]);
            } else {
                $this->from($tableName[0]);
            }
        } else {
            $this->from($tableName);
        }
    }

    /**
     * Select columns
     * @param string|array $columns
     * @return $this
     */
    public function select($columns)
    {
        if ($columns) {
            $columns = $this->quoteName($columns);
            $this->_append('select', 'select', $columns);
        }

        return $this;
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
        $tableName = $this->quoteName($tableName);
        if ($alias) {
            $tableName .= ' AS ' . $this->quoteName($alias);
        }

        $this->_append('from', 'FROM', $tableName);

        return $this;
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
        if (!$condition) {
            return $this;
        }

        $condition = $this->clean($condition, $value);
        $this->_append('where', 'WHERE', $condition, ', ', $logic);

        return $this;
    }

    /**
     * Where conditions group
     * @param array  $conditions
     * @param string $logic
     * @return $this
     */
    public function whereGroup($conditions, $logic = 'AND')
    {
        $conditions = (array)$conditions;

        $where = new Where('');

        foreach ($conditions as $condition) {

            $logicInner = 'AND';

            if (is_array($condition)) {

                $value      = isset($condition[1]) ? $condition[1] : null;
                $logicInner = isset($condition[2]) ? $condition[2] : 'AND';
                $condition  = $this->clean($condition[0], $value);

            } else {
                $condition = $this->clean($condition);
            }

            $where->append('', $condition, $logicInner);
        }

        $this->where('(' . $where . ')', null, $logic);

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
        $conditions = false;

        if ($offset) {
            $conditions = (int)$offset . ', ' . (int)$length;
        } else if ($length) {
            $conditions = (int)$length;
        }

        if ($conditions) {
            $this->cleanElement('limit');
            $this->_append('limit', 'LIMIT', $conditions);
        }

        return $this;
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
        $type  = strtoupper($type);
        $valid = array('LEFT', 'RIGHT', 'INNER', 'STRAIGHT_JOIN', 'NATURAL LEFT', 'NATURAL RIGHT');

        if (in_array($type, $valid, true)) {

            if (is_array($table)) {
                $table = $this->quoteName($table[0]) . ' AS ' . $this->quoteName($table[1]);
            } else {
                $table = $this->quoteName($table);
            }

            // cleanup and build conditions
            $condition = (array)$condition;
            $condition = array_filter(array_unique($condition));
            $condition = $table . ' ON (' . implode(' AND ', $condition) . ')';

            $this->_append('join', $type . ' JOIN', $condition);
        }

        return $this;
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
     * @return string
     * @throws Exception
     */
    public function __toString()
    {
        if (!isset($this->elements['select'])) {
            $this->select('*');
        }

        return parent::__toString();
    }


}
