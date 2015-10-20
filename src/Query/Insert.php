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

namespace JBZoo\SqlBuilder\Query;

use JBZoo\SqlBuilder\Exception;

/**
 * Class Insert
 * @package JBZoo\SqlBuilder
 */
class Insert extends Query
{
    /**
     * Query scheme
     * @var array
     */
    protected $_blocks = array(
        'options'     => null,
        'into'        => null,
        'insertrow'   => null,
        'insertmulti' => null,
    );

    /**
     * Constructor
     * @param array|string $tableName
     * @throws Exception
     */
    public function __construct($tableName)
    {
        if (!$tableName) {
            throw new Exception('Table name is undefined');
        }

        $this->into($tableName);
    }

    /**
     * @param string $tableName
     * @return $this
     */
    public function into($tableName)
    {
        $tableName = $this->quoteName($tableName);

        $this->cleanBlock('into');
        $this->_append('into', 'into', $tableName);

        return $this;
    }

    /**
     * @param array $data
     * @return $this
     */
    public function row(array $data)
    {
        //$this->cleanBlock('insertrow');
        $this->_append('insertrow', 'insertrow', $data);

        return $this;
    }

    /**
     * @param array $data
     * @return $this
     */
    public function multi(array $data)
    {
        $this->cleanBlock('insertmulti');
        $this->cleanBlock('insertrow');
        $this->_append('insertmulti', 'insertmulti', $data);

        return $this;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function __toString()
    {
        return 'INSERT ' . parent::__toString();
    }
}
