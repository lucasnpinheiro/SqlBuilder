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

namespace JBZoo\SqlBuilder;

/**
 * Class DriverTest
 * @package JBZoo\SqlBuilder
 */
class DriverTest extends PHPUnit
{
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

        self::assertSame($dr->quote($test), $suc);
        self::assertSame($dr->quote(array($test, $test)), array($suc, $suc));

        $test = 'O\'Reilly';
        $suc  = "'O'Reilly'";
        self::assertSame($dr->quote($test, false), $suc);
        self::assertSame($dr->quote(array($test, $test), false), array($suc, $suc));
    }


    public function testQuoteName()
    {
        $dr = SqlBuilder::get();

        self::assertSame($dr->quoteName('table'), '`table`');
        self::assertSame($dr->quoteName('table.field'), '`table`.`field`');
        self::assertSame($dr->quoteName('table.field.'), '`table`.`field`');
        self::assertSame($dr->quoteName('.table.field.'), '`table`.`field`');
        self::assertSame($dr->quoteName('.table.field'), '`table`.`field`');
        self::assertSame($dr->quoteName('.'), '');
        self::assertSame($dr->quoteName(''), '');

        self::assertSame($dr->quoteName('table', 'tTable'), '`table` AS `tTable`');
        self::assertSame($dr->quoteName('table.field', 'tTable.someProp'), '`table`.`field` AS `tTable.someProp`');

        self::assertSame(
            $dr->quoteName(array(
                'table',
                array('table', 'table2'),
            )),
            array(
                '`table`',
                array('`table`', '`table2`'),
            )
        );

        self::assertSame(
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

        self::assertSame($dr->escape($test, false), "O\\'Reilly % _");
        self::assertSame($dr->escape($test, true), 'O\\\'Reilly \\% \\_');
    }
}
