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

/**
 * Class Options
 * @package JBZoo\SqlBuilder\Block
 */
class Options extends Element
{
    /**
     * @var array
     */
    protected $_validOptions = array(
        'SQL_SMALL_RESULT',
        'SQL_BIG_RESULT',
        'SQL_BUFFER_RESULT',
        array('SQL_CACHE', 'SQL_NO_CACHE'),
        'SQL_CALC_FOUND_ROWS',
        'HIGH_PRIORITY',
        array('DISTINCT', 'DISTINCTROW', 'ALL'),
    );

    /**
     * @param string $name
     * @param array  $elements
     * @param string $glue
     */
    public function __construct($name, $elements = array(), $glue = ',')
    {
        parent::__construct('', $elements, ' ');
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

        foreach ($elements as $element) {
            if (!$this->_isValid($element)) {
                continue;
            }

            $this->_conditions[$element] = $element;
        }
    }

    /**
     * @param $element
     * @return bool
     */
    protected function _isValid($element)
    {
        $element = trim(strtoupper($element));

        foreach ($this->_validOptions as $options) {
            if (is_string($options) && $element === $options) {
                return true;
            } elseif (is_array($options) && in_array($element, $options, true)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return implode($this->_glue, $this->_conditions);
    }
}
