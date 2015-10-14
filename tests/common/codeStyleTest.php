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
 */

namespace JBZoo\PHPUnit;

/**
 * Class CodeStyleTest
 * @package JBZoo\PHPUnit
 */
class CodeStyleTest extends PHPUnit
{
    protected $le = "\n";

    protected $replace = array(
        '_LINK_'       => 'https://github.com/JBZoo/_PACKAGE_',
        '_NAMESPACE_'  => 'JBZoo\_PACKAGE_',
        '_PACKAGE_'    => 'SqlBuilder', // change me!
        '_LICENSE_'    => 'MIT',
        '_COPYRIGHTS_' => 'Copyright (C) JBZoo.com,  All rights reserved.',
        '_AUTHOR_'     => 'Denis Smetannikov <denis@jbzoo.com>',
    );

    /**
     * Valid copyright header
     * @var array
     */
    protected $validHeader = array(
        '<?php',
        '/**',
        ' * JBZoo _PACKAGE_',
        ' *',
        ' * This file is part of the JBZoo CCK package.',
        ' * For the full copyright and license information, please view the LICENSE',
        ' * file that was distributed with this source code.',
        ' *',
        ' * @package   _PACKAGE_',
        ' * @license   _LICENSE_',
        ' * @copyright _COPYRIGHTS_',
        ' * @link      _LINK_',
    );

    /**
     * Ignore list for
     * @var array
     */
    protected $excludeFiles = array(
        '.',
        '..',
        '.idea',
        '.git',
        'build',
        'vendor',
        'composer.lock',
        'codeStyleTest.php',
    );

    /**
     * Render copyrights
     * @param $text
     * @return mixed
     */
    protected function replaceCopyright($text)
    {
        foreach ($this->replace as $const => $value) {
            $text = str_replace($const, $value, $text);
        }

        return $text;
    }

    /**
     * Test line endings
     */
    public function testFiles()
    {
        $files = $this->getFileList(ROOT_PATH, '[/\\\\](src|tests)[/\\\\].*\.php$');

        foreach ($files as $file) {
            $content = $this->openFile($file);
            isNotContain("\r", $content);
        }
    }

    /**
     * Test copyright headers
     */
    public function testHeaders()
    {
        $files = $this->getFileList(ROOT_PATH, '[/\\\\](src|tests)[/\\\\].*\.php$');

        foreach ($files as $file) {
            $content = $this->openFile($file);

            // build copyrights
            $validHeader = $this->validHeader;
            if (isset($this->replace['_AUTHOR_'])) {
                $validHeader[] = ' * @author    _AUTHOR_';
            }
            $validHeader[] = ' */';

            $namespace = $this->replaceCopyright('namespace _NAMESPACE_');
            if (strpos($content, $namespace)) {
                //$validHeader[] = '';
                //$validHeader[] = 'namespace _NAMESPACE_';
            }

            $valid = $this->replaceCopyright(implode($validHeader, $this->le));
            isContain($valid, $content, false, 'File has no valid header: ' . $file);
        }
    }

    /**
     * Try to find cyrilic symbols in the code
     */
    public function testCyrillic()
    {
        $files = $this->getFileList(ROOT_PATH, '[/\\\\](src|tests)[/\\\\].*\.php$');

        foreach ($files as $file) {
            $content = $this->openFile($file);
            isNotLike('#[А-Яа-яЁё]#u', $content, 'File has no valid chars: ' . $file);
        }
    }

}
