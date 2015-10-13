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

use JBZoo\SqlBuilder\SqlBuilder;

/**
 * Class Query
 * @package JBZoo\SqlBuilder
 */
abstract class Query
{
    /**
     * @return \JBZoo\SqlBuilder\Driver\Driver
     */
    public function getDriver()
    {
        return SqlBuilder::get();
    }
}
