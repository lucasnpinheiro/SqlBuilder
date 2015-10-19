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

namespace JBZoo\SqlBuilder\Block;

use JBZoo\SqlBuilder\SqlBuilder;

/**
 * Class Element
 * @package JBZoo\SqlBuilder\Block
 */
class Element
{
    /**
     * @var string The name of the element
     */
    protected $_name = '';

    /**
     * @var array An array of conditions
     */
    protected $_conditions = array();

    /**
     * @var string Glue piece
     */
    protected $_glue = ',';

    /**
     * Constructor
     * @param string $name     The name of the element
     * @param array  $elements String or array
     * @param string $glue     The glue for elements
     */
    public function __construct($name, $elements = array(), $glue = ',')
    {
        $this->_name = strtoupper(trim($name));
        $this->_glue = $glue;
    }

    /**
     * Magic function to convert the query element to a string
     * @return string
     */
    public function __toString()
    {
        $result = '';
        if ($this->_name) {
            $result .= $this->_name . ' ';
        }

        $result .= implode($this->_glue, $this->_conditions);

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
        $this->_conditions = array_merge($this->_conditions, (array)$elements);
    }

    /**
     * @return \JBZoo\SqlBuilder\Driver\Driver|null
     */
    protected function _getDriver()
    {
        return SqlBuilder::get();
    }
}
