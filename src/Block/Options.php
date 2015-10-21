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
class Options extends Block
{
    /**
     * @var array
     */
    protected $_validOptions = array(
        'SELECT' => array(
            'SQL_SMALL_RESULT',
            'SQL_BIG_RESULT',
            'SQL_BUFFER_RESULT',
            array('SQL_CACHE', 'SQL_NO_CACHE'),
            'SQL_CALC_FOUND_ROWS',
            'HIGH_PRIORITY',
            array('DISTINCT', 'DISTINCTROW', 'ALL'),
        ),

        'INSERT' => array(
            array('LOW_PRIORITY', 'DELAYED'),
            'IGNORE',
        ),

        'DELETE' => array(
            'LOW_PRIORITY',
            'QUICK',
        ),

        'UPDATE' => array(
            'LOW_PRIORITY',
            'IGNORE',
        ),
    );

    /**
     * Appends element parts to the internal list.
     * @param string|array $elements
     * @param mixed        $extra
     * @return void
     */
    public function append($elements, $extra = null)
    {
        $elements = (array)$elements;

        foreach ($elements as $element) {
            if ($element = $this->_isValid($element, $extra)) {
                $this->_conditions[$element] = $element;
            }
        }
    }

    /**
     * @param string $element
     * @param string $queryType
     * @return bool
     */
    protected function _isValid($element, $queryType)
    {
        $element   = trim(strtoupper($element));
        $queryType = strtoupper($queryType);

        if (isset($this->_validOptions[$queryType])) {
            foreach ($this->_validOptions[$queryType] as $options) {
                if (is_string($options) && $element === $options) {
                    return $element;
                } elseif (is_array($options) && in_array($element, $options, true)) {
                    return $element;
                }
            }
        }

        return false;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return implode(' ', $this->_conditions);
    }
}
