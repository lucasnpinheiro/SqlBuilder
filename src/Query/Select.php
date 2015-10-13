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

use JBZoo\SqlBuilder\Element;
use JBZoo\SqlBuilder\Exception;

/**
 * Class QuerySelect
 * @package JBZoo\SqlBuilder
 */
class Select extends Query
{
    /**
     * @var Element
     */
    protected $select = null;

    /**
     * @var Element
     */
    protected $from = null;

    /**
     * @var Element
     */
    protected $where = null;

    /**
     * Constructor
     * @param null $tableName
     */
    public function __construct($tableName = null)
    {
        if ($tableName) {
            if (is_array($tableName)) {
                $this->from($tableName[0], $tableName[1]);
            } else {
                $this->from($tableName);
            }
        }
    }

    /**
     * Add a table to the FROM clause of the query.
     *
     * @param  string $table A string or array of table names.
     * @param  string $alias Alias table name
     * @return $this
     */
    public function from($table, $alias = null)
    {
        $table = $this->getDriver()->quoteName($table);

        if ($alias) {
            $table .= ' AS ' . $alias;
        }

        $this->from = new Element('from', $table);

        return $this;
    }

    /**
     * Where conditions
     *
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

        $condition = $this->getDriver()->clean($condition, $value);

        if ($this->where) {
            $this->where->append($logic . ' ' . $condition);
        } else {
            $this->where = new Element('where', $condition, ' ');
        }

        return $this;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function __toString()
    {
        $sql = '';

        if (!$this->select) {
            $this->select = new Element('select', '*');
        }

        $sql[] = $this->select;
        if ($this->from) {
            $sql[] = $this->from;
        } else {
            return '';
        }

        if ($this->where) {
            $sql[] = $this->where;
        }

        //$query = str_replace('#__', $this->db->getPrefix(), $query);

        return trim(implode('', $sql));
    }


}
