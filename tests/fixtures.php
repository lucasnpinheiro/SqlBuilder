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

namespace JBZoo\PHPUnit;

// @codingStandardsIgnoreFile
// @codeCoverageIgnoreStart

use JBZoo\SqlBuilder\SqlBuilder;

// init life hack
$connection = mysqli_connect($_ENV['mysql_host'], $_ENV['mysql_user'], $_ENV['mysql_pass'], $_ENV['mysql_db'], $_ENV['mysql_port']);
SqlBuilder::set('mysqli', $connection);

// @codeCoverageIgnoreEnd