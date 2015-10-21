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

        if (strpos($condition, '?n') !== false) {
            $value     = $this->quoteName($value);
            $condition = str_replace('?n', $value, $condition);

        } elseif (strpos($condition, '?s') !== false) {
            $condition = str_replace('?s', $this->quote($value), $condition);

        } elseif (strpos($condition, '?i') !== false) {
            $value     = $value === null ? self::NULL : (int)$value;
            $condition = str_replace('?i', $value, $condition);

        } elseif (strpos($condition, '?f') !== false) {
            $value     = $value === null ? self::NULL : (float)$value;
            $condition = str_replace('?f', $value, $condition);

        } elseif (strpos($condition, '?b') !== false) {
            $value     = (bool)$value ? self::TRUE : self::FALSE;
            $condition = str_replace('?b', $value, $condition);

        } elseif (strpos($condition, '?u') !== false) {
            $query = array();
            foreach ((array)$value as $key => $value) {
                if (!is_numeric($key)) {
                    $query[] = $this->quoteName($key) . '=' . $this->quote($value);
                }
            }

            $condition = str_replace('?u', implode(', ', $query), $condition);

        } elseif (strpos($condition, '?a') !== false) {
            $value = (array)$value;
            if (count($value) === 0) {
                $value = array(null);
            }

            $value     = '(' . implode(', ', $this->quote($value)) . ')';
            $condition = str_replace('?a', $value, $condition);
        }

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
