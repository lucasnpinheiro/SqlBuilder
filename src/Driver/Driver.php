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

namespace JBZoo\SqlBuilder\Driver;

/**
 * Class Driver
 * @package JBZoo\SqlBuilder\Driver
 */
abstract class Driver
{
    const NULL  = 'NULL';
    const TRUE  = 'TRUE';
    const FALSE = 'FALSE';

    /**
     * @var string
     */
    protected $_tablePrefix = '';

    /**
     * @var mixed|null
     */
    protected $_connection;

    /**
     * @param mixed  $connection
     * @param string $tablePrefix
     */
    public function __construct($connection, $tablePrefix = null)
    {
        $this->_connection  = $connection;
        $this->_tablePrefix = trim($tablePrefix);
    }

    /**
     * Quotes and optionally escapes a string to database requirements for use in database queries.
     *
     * @param   mixed   $text   A string or an array of strings to quote.
     * @param   boolean $escape True (default) to escape the string, false to leave it unchanged.
     * @return  string
     */
    public function quote($text, $escape = true)
    {
        if (is_array($text)) {
            foreach ($text as $key => $value) {
                $text[$key] = $this->quote($value, $escape);
            }

            return $text;
        }

        if ($text === null) {
            return self::NULL;

        } elseif ($text === true) {
            return self::TRUE;

        } elseif ($text === false) {
            return self::FALSE;
        }

        return '\'' . ($escape ? $this->escape($text) : $text) . '\'';
    }

    /**
     * Wrap an SQL statement identifier name such as column, table or database names in quotes to prevent injection
     * risks and reserved word conflicts.
     *
     * @param   mixed $name   The identifier name to wrap in quotes, or an array of identifier names to wrap in quotes.
     *                        Each type supports dot-notation name.
     * @param   mixed $as     The AS query part associated to $name. It can be string or array, in latter case it
     *                        has to be same length of $name;
     *                        if is null there will not be any AS part for string or array element.
     * @return  mixed
     */
    public function quoteName($name, $as = null)
    {
        if (is_string($name)) {
            $quotedName = $this->_quoteNameStr(explode('.', trim($name, '.')));

            $quotedAs = '';

            if (!is_null($as)) {
                settype($as, 'array');
                $quotedAs .= ' AS ' . $this->_quoteNameStr($as);
            }

            return $quotedName . $quotedAs;
        } else {
            $fin = array();

            if (is_null($as)) {
                foreach ($name as $str) {
                    $fin[] = $this->quoteName($str);
                }
            } elseif (is_array($name) && (count($name) === count($as))) {
                $count = count($name);

                for ($i = 0; $i < $count; $i++) {
                    $fin[] = $this->quoteName($name[$i], $as[$i]);
                }
            }

            return $fin;
        }
    }

    /**
     * Quote strings coming from quoteName call.
     *
     * @param   array $strArr Array of strings coming from quoteName dot-explosion.
     * @return  string
     */
    protected function _quoteNameStr($strArr)
    {
        $parts = array();

        foreach ($strArr as $part) {
            if (!$part) {
                continue;
            }

            if ($part === '*') {
                $parts[] = $part;
            } else {
                $parts[] = '`' . $part . '`';
            }
        }

        return implode('.', $parts);
    }

    /**
     * @param string $condition
     * @param string $value
     * @return string
     */
    public function clean($condition, $value = null)
    {
        if (is_array($condition)) {
            $condition = $this->quoteName($condition[0])
                . (isset($condition[1]) ? ' ' . $condition[1] : '')
                . (isset($condition[2]) ? ' ' . $condition[2] : '');
        }

        $condition = trim($condition);

        if (strpos($condition, '?e') !== false) { // SQL Entities
            $condition = $this->_cleanEntity($condition, $value);

        } elseif (strpos($condition, '?s') !== false) { // any string
            $condition = $this->_cleanString($condition, $value);

        } elseif (strpos($condition, '?i') !== false) { // integer
            $condition = $this->_cleanInteger($condition, $value);

        } elseif (strpos($condition, '?f') !== false) { // float
            $condition = $this->_cleanFloat($condition, $value);

        } elseif (strpos($condition, '?b') !== false) { // boolean
            $condition = $this->_cleanBool($condition, $value);

        } elseif (strpos($condition, '?n') !== false) { // null
            $condition = $this->_cleanNull($condition, $value);

        } elseif (strpos($condition, '?k') !== false) { // list of key=value
            $condition = $this->_cleanKeyValue($condition, $value);

        } elseif (strpos($condition, '?u') !== false) { // list for update (SET ...)
            $condition = $this->_cleanUpdate($condition, $value);

        } elseif (strpos($condition, '?a') !== false) { // array
            $condition = $this->_cleanArray($condition, $value);
        }

        return $condition;
    }

    /**
     * @param string $condition
     * @param string $value
     * @return string
     */
    protected function _cleanEntity($condition, $value)
    {
        $condition = str_replace('?e', $this->quoteName($value), $condition);
        return $condition;
    }

    /**
     * @param string $condition
     * @param string $value
     * @return string
     */
    protected function _cleanString($condition, $value)
    {
        $condition = str_replace('?s', $this->quote($value), $condition);
        return $condition;
    }

    /**
     * @param string $condition
     * @param string $value
     * @return string
     */
    protected function _cleanInteger($condition, $value)
    {
        $condition = str_replace('?i', (int)trim($value), $condition);
        return $condition;
    }

    /**
     * @param string $condition
     * @param string $value
     * @return string
     */
    protected function _cleanFloat($condition, $value)
    {
        $value     = (float)str_replace(',', '.', trim($value));
        $condition = str_replace('?f', $value, $condition);
        return $condition;
    }

    /**
     * @param string $condition
     * @param string $value
     * @return string
     */
    protected function _cleanBool($condition, $value)
    {
        $value     = (bool)$value ? self::TRUE : self::FALSE;
        $condition = str_replace('?b', $value, $condition);
        return $condition;
    }

    /**
     * @param string $condition
     * @param string $value
     * @return string
     */
    protected function _cleanNull($condition, $value)
    {
        $value     = (bool)$value ? 'IS NOT NULL' : 'IS NULL';
        $condition = str_replace('?n', $value, $condition);
        return $condition;
    }

    /**
     * @param string $condition
     * @param string $value
     * @return string
     */
    protected function _cleanKeyValue($condition, $value)
    {
        $query = array();

        foreach ((array)$value as $key => $value) {
            if (is_numeric($key)) {
                continue;
            }

            if (is_array($value)) {
                $value = $this->clean($value[0], $value[1]);
            }

            $query[] = $this->quoteName($key) . '=' . $value;
        }

        $condition = str_replace('?k', implode(', ', $query), $condition);

        return $condition;
    }

    /**
     * @param string $condition
     * @param string $value
     * @return string
     */
    protected function _cleanUpdate($condition, $value)
    {
        $query = array();

        foreach ((array)$value as $value) {
            if (is_array($value[1])) {
                $value[1] = $this->clean($value[1][0], $value[1][1]);
            }

            if (in_array($value[1], array(true, false, null), true)) {
                $value[1] = $this->quote($value[1]);
            }

            $query[] = $this->quoteName($value[0]) . '=' . $value[1];
        }

        $condition = str_replace('?u', implode(', ', $query), $condition);

        return $condition;
    }

    /**
     * @param string $condition
     * @param string $value
     * @return string
     */
    protected function _cleanArray($condition, $value)
    {
        $value = (array)$value;
        if (count($value) === 0) {
            $value = array(null);
        }

        $value     = '(' . implode(', ', $this->quote($value)) . ')';
        $condition = str_replace('?a', $value, $condition);

        return $condition;
    }

    /**
     * @return string
     */
    public function getTablePrfix()
    {
        return $this->_tablePrefix;
    }

    /**
     * Escapes a string for usage in an SQL statement.
     *
     * @param   string  $text  The string to be escaped.
     * @param   boolean $extra Optional parameter to provide extra escaping.
     * @return  string
     */
    abstract public function escape($text, $extra = false);
}
