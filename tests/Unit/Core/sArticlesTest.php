<?php
/**
 * Shopware 5
 * Copyright (c) shopware AG
 *
 * According to our dual licensing model, this program can be used either
 * under the terms of the GNU Affero General Public License, version 3,
 * or under a proprietary license.
 *
 * The texts of the GNU Affero General Public License with an additional
 * permission and of our proprietary license can be found at and
 * in the LICENSE file you have received along with this program.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * "Shopware" is a registered trademark of shopware AG.
 * The licensing of the program under the AGPLv3 does not imply a
 * trademark license. Therefore any rights, title and interest in
 * our trademarks remain entirely with us.
 */

namespace Shopware\Tests\Unit\Core;

use PHPUnit\Framework\TestCase;

/**
 * @category  Shopware
 * @package   Shopware\Tests
 * @copyright Copyright (c) shopware AG (http://www.shopware.de)
 */
class sArticlesTest extends TestCase
{
    public function provideData()
    {
        return [
            ['foo', 'foo'],
            ['foo ', 'foo'],
            [' foo', 'foo'],
            [' foo ', 'foo'],
            ['   foo   ', 'foo'],
            ['foo<>bar', 'foo bar'],
            ['<h2>foo</h2>', 'foo'],
            ['<h2>foo</h2>bar', 'foo bar'],
            ['bar<h2>foo</h2>bar', 'bar foo bar'],
            ['ελληνικά ', 'ελληνικά'],
            ['foo"bar', 'foo"bar'],
            ['foo\'bar', 'foo\'bar'],
            ['foo&bar', 'foo&bar'],
            ['foo&amp;bar', 'foo&bar'],
            ['A \'quote\' is &lt;b&gt;bold&lt;/b&gt;', 'A \'quote\' is bold'],
            ['<style>body: 1px solid red;</style>', 'body: 1px solid red;'],
            ['<script>alert("foo");</script>', 'alert("foo");'],
        ];
    }

    /**
     * Test case method
     * @dataProvider provideData
     * @param string $input
     * @param string $expectedResult
     */
    public function testStrings($input, $expectedResult)
    {
        /** @var \sArticles $sArticles */
        $sArticles = $this->createPartialMock(\sArticles::class, []);

        $this->assertSame($expectedResult, $sArticles->sOptimizeText($input));
    }

    /**
     * Override method to backport a PHPUnit 5.5.3 feature.
     * See: https://github.com/sebastianbergmann/phpunit/commit/3c423b889e4833b7dc5f77c1c20ce1aa29b2e48d
     *
     * @param string $originalClassName
     * @param array  $methods
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     *
     * @throws \PHPUnit_Framework_Exception
     *
     * @todo Remove once PHPUnit 5.5.3 is in use
     */
    protected function createPartialMock($originalClassName, array $methods)
    {
        return $this->getMockBuilder($originalClassName)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->setMethods(empty($methods) ? null : $methods)
            ->getMock();
    }
}
