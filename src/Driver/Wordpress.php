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
 * Class Wordpress
 * @package JBZoo\SqlBuilder\Driver
 */
class Wordpress extends Driver
{
    /**
     * @var \wpdb
     */
    protected $_db = null;

    /**
     * {@inheritdoc}
     */
    public function __construct($connection = null, $tablePrefix = null)
    {
        // @codeCoverageIgnoreStart
        $this->_db   = $GLOBALS['wpdb'];
        $tablePrefix = $this->_db->get_blog_prefix();

        parent::__construct($connection, $tablePrefix);
        // @codeCoverageIgnorEnd
    }

    /**
     * {@inheritdoc}
     */
    public function escape($text, $extra = false)
    {
        // @codeCoverageIgnoreStart
        return $this->_db->_escape($text);
        // @codeCoverageIgnorEnd
    }
}
