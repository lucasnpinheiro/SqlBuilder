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

namespace JBZoo\SqlBuilder;

/**
 * Class Element
 * @package JBZoo\SqlBuilder
 */
class Element
{
    /**
     * @var string The name of the element.
     */
    protected $name = '';

    /**
     * @var array An array of elements.
     */
    protected $elements = array();

    /**
     * @var string Glue piece.
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

        $this->append($elements);
    }

    /**
     * Magic function to convert the query element to a string
     * @return string
     */
    public function __toString()
    {
        return ' ' . $this->name . ' ' . implode($this->glue, $this->elements);
    }

    /**
     * Appends element parts to the internal list.
     * @param string|array $elements
     * @return void
     */
    public function append($elements)
    {
        $this->elements = array_merge($this->elements, (array)$elements);
    }

}
