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

use JBZoo\SqlBuilder\Exception;
use JBZoo\SqlBuilder\SqlBuilder;

/**
 * Class DriverTest
 * @package JBZoo\SqlBuilder
 */
class DriverTest extends PHPUnit
{
    /**
     * Performs operation
     * @throws Exception
     */
    protected function setUp()
    {
        static $connection;
        if (is_null($connection)) {
            $connection = mysqli_connect($_ENV['mysql_host'], $_ENV['mysql_user'], $_ENV['mysql_pass'], $_ENV['mysql_db'], $_ENV['mysql_port']);
        }
        SqlBuilder::set('mysqli', $connection, 't_');
    }

    public function testDriverInit()
    {
        $driver = SqlBuilder::get();
        isClass('\JBZoo\SqlBuilder\Driver\Driver', $driver);
    }

    /**
     * @expectedException \JBZoo\SqlBuilder\Exception
     */
    public function testUndefinedDriver()
    {
        SqlBuilder::set('undefined', '');
    }

    public function testQuote()
    {
        $dr = SqlBuilder::get();

        $test = 'O\'Reilly';
        $suc  = "'O\\'Reilly'";

        same($dr->quote($test), $suc);
        same($dr->quote(array($test, $test)), array($suc, $suc));

        $test = 'O\'Reilly';
        $suc  = "'O'Reilly'";
        same($dr->quote($test, false), $suc);
        same($dr->quote(array($test, $test), false), array($suc, $suc));
    }


    public function testQuoteName()
    {
        $dr = SqlBuilder::get();

        same($dr->quoteName('table'), '`table`');
        same($dr->quoteName('table.*'), '`table`.*');
        same($dr->quoteName('table.field'), '`table`.`field`');
        same($dr->quoteName('table.field.'), '`table`.`field`');
        same($dr->quoteName('.table.field.'), '`table`.`field`');
        same($dr->quoteName('.table.field'), '`table`.`field`');
        same($dr->quoteName('.'), '');
        same($dr->quoteName(''), '');

        same($dr->quoteName('table', 'tTable'), '`table` AS `tTable`');
        same($dr->quoteName('table.field', 'tTable.someProp'), '`table`.`field` AS `tTable.someProp`');

        same(
            $dr->quoteName(array(
                'table',
                array('table', 'table2'),
            )),
            array(
                '`table`',
                array('`table`', '`table2`'),
            )
        );

        same(
            $dr->quoteName(
                array('table', 'table2'),
                array('tTable', 'tTable2')
            ),
            array(
                '`table` AS `tTable`',
                '`table2` AS `tTable2`',
            )
        );
    }

    public function testEscape()
    {
        $dr = SqlBuilder::get();

        $test = "O'Reilly % _";

        same($dr->escape($test, false), "O\\'Reilly % _");
        same($dr->escape($test, true), 'O\\\'Reilly \\% \\_');
    }
}
