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

namespace JBZoo\SqlBuilder\Element;

/**
 * Class Element
 * @package JBZoo\SqlBuilder\Element
 */
class Element
{
    /**
     * @var string The name of the element
     */
    protected $name = '';

    /**
     * @var array An array of conditions
     */
    protected $conditions = array();

    /**
     * @var string Glue piece
     */
    protected $glue = ',';

    /**
     * Constructor
     * @param string $name     The name of the element
     * @param array  $elements String or array
     * @param string $glue     The glue for elements
     */
    public function __construct($name, $elements = array(), $glue = ',')
    {
        $this->name = strtoupper(trim($name));
        $this->glue = $glue;
    }

    /**
     * Magic function to convert the query element to a string
     * @return string
     */
    public function __toString()
    {
        $result = '';
        if ($this->name) {
            $result .= $this->name . ' ';
        }

        $result .= implode($this->glue, $this->conditions);

        return $result;
    }

    /**
     * Appends element parts to the internal list.
     * @param string       $name
     * @param string|array $elements
     * @param mixed        $extra
     * @return void
     */
    public function append($name, $elements, $extra = null)
    {
        $this->conditions = array_merge($this->conditions, (array)$elements);
    }
}
