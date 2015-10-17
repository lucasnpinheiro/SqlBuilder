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
 * Class Where
 * @package JBZoo\SqlBuilder\Element
 */
class Where extends Element
{
    /**
     * @var array
     */
    protected $_logicValid = array('AND', 'OR');

    /**
     * Constructor
     * @param string $name     The name of the element
     * @param array  $elements String or array
     * @param string $glue     The glue for elements
     */
    public function __construct($name, $elements = array(), $glue = ',')
    {
        parent::__construct($name, $elements, ' ');
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
        $elements = (array)$elements;
        $elements = array_filter(array_unique($elements));

        if (!$elements) {
            return;
        }

        $extra = trim(strtoupper($extra));
        if (in_array($extra, $this->_logicValid, true)) {
            if (count($this->conditions) === 0) {
                $result = $elements[0]; // default
            } else {
                $result = $extra . ' ' . $elements[0]; // default
            }

            $this->conditions = array_merge($this->conditions, (array)$result);
        }
    }
}
