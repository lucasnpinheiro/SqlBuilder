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
 * Class Joomla
 * @package JBZoo\SqlBuilder\Driver
 */
class Joomla extends Driver
{
    /**
     * @var \JDatabaseDriver
     */
    protected $_db = null;

    /**
     * {@inheritdoc}
     */
    public function __construct($connection = null, $tablePrefix = null)
    {
        $this->_db   = \JFactory::getDbo();
        $tablePrefix = $this->_db->getPrefix();
        $connection  = $this->_db->getConnection();

        parent::__construct($connection, $tablePrefix);
    }

    /**
     * {@inheritdoc}
     */
    public function escape($text, $extra = false)
    {
        return $this->_db->escape($text, $extra);
    }
}
