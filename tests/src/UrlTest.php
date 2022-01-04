<?php

namespace Nip\Utility\Tests;

use Nip\Utility\Url;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class StrTest
 * @package Nip\Utility\Tests
 */
class UrlTest extends AbstractTest
{

    public function test_copyQuery()
    {
        self::assertSame(
            'http://test.com/app/admin/users?user=5',
            Url::copyQuery('http://test.com/app/admin/users?user=5', ['user'], Request::create('/test/test?user=test'))
        );

        self::assertSame(
            'http://test.com/app/admin/users?user=5&_format=json',
            Url::copyQuery(
                'http://test.com/app/admin/users?user=5',
                ['user', '_format'],
                Request::create('/test/test?user=test&_format=json')
            )
        );

        self::assertSame(
            'http://test.com/app/admin/users?user=5&_format=json&dnx',
            Url::copyQuery(
                'http://test.com/app/admin/users?user=5',
                ['user', '_format', 'dnx'],
                Request::create('/test/test?user=test&_format=json')
            )
        );
    }

    public function test_addArg()
    {
        // Regular tests
        self::assertSame('user=5', Url::addArg(['user' => 5], ''));
        self::assertSame('/app/admin/users?user=5', Url::addArg(['user' => 5], '/app/admin/users'));

        self::assertSame(
            '/app/admin/users?action=edit&user=5',
            Url::addArg(['user' => 5], '/app/admin/users?action=edit')
        );
        self::assertSame(
            '/app/admin/users?action=edit&tab=personal&user=5',
            Url::addArg(['user' => 5], '/app/admin/users?action=edit&tab=personal')
        );

        // Ensure strips false.
        self::assertSame('/index.php', Url::addArg(['debug' => false], '/index.php'));

        // With valueless parameters.
        self::assertSame('/index.php?debug=', Url::addArg(['debug' => null], '/index.php'));
        self::assertSame('/index.php?debug=#hash', Url::addArg(['debug' => null], '/index.php#hash'));

        // With a URL fragment
        self::assertSame('/app/admin/users?user=5#test', Url::addArg(['user' => 5], '/app/admin/users#test'));

        // Full URL
        self::assertSame('http://example.com/?a=b', Url::addArg(['a' => 'b'], 'http://example.com'));

        // Only the query string
        self::assertSame('?a=b&c=d', Url::addArg(['c' => 'd'], '?a=b'));
        self::assertSame('a=b&c=d', Url::addArg(['c' => 'd'], 'a=b'));

        // Url encoding test
        self::assertSame(
            '/app/admin/users?param=containsa%26sym',
            Url::addArg(['param' => 'containsa&sym'], '/app/admin/users')
        );

        // If not provided, grab the URI from the server.
        $_SERVER['REQUEST_URI'] = '/app/admin/users';
        self::assertSame('/app/admin/users?user=6', Url::addArg(['user' => 6]));
        self::assertSame('/app/admin/users?user=7', Url::addArg(['user' => 7]));
    }

    public function testRemoveArg()
    {
        self::assertSame('/app/admin/users', Url::delArg('user', '/app/admin/users?user=5'));
        self::assertSame('/app/admin/users?action=edit', Url::delArg('user', '/app/admin/users?action=edit&user=5'));
        self::assertSame(
            '/app/admin/users?user=5',
            Url::delArg(['tab', 'action'], '/app/admin/users?action=edit&tab=personal&user=5')
        );
    }

    public function test_build()
    {
        self::assertSame(
            'http://example.com:8080/path/?query#fragment',
            Url::build('http://example.com:8080/path/?query#fragment')
        );

        self::assertSame(
            'https://dev.example.com/',
            Url::build('http://example.com/', ['scheme' => 'https', 'host' => 'dev.example.com'])
        );

        self::assertSame(
            'http://example.com/#hi',
            Url::build('http://example.com/', ['fragment' => 'hi'])
        );

        self::assertSame(
            'http://example.com/page',
            Url::build('http://example.com', ['path' => 'page'])
        );
        self::assertSame(
            'http://example.com/page',
            Url::build('http://example.com/', ['path' => 'page'])
        );

        self::assertSame(
            'http://example.com/?hi=Bro',
            Url::build('http://example.com/', ['query' => 'hi=Bro'])
        );

        self::assertSame(
            'http://example.com/?show=1&hi=Bro',
            Url::build('http://example.com/?show=1', ['query' => 'hi=Bro'], Url::URL_JOIN_QUERY)
        );
        self::assertSame('http://admin@example.com/', Url::build('http://example.com/', ['user' => 'admin']));
        self::assertSame(
            'http://admin:1@example.com/',
            Url::build(
                'http://example.com/',
                ['user' => 'admin', 'pass' => '1']
            )
        );
    }

    public function testCreate()
    {
        self::assertSame(
            'https://example.com/?foo=bar',
            Url::create(
                [
                    'host'  => 'example.com',
                    'user'  => '',
                    'pass'  => '123456',
                    'query' => [
                        'foo' => 'bar',
                    ],
                ]
            )
        );

        self::assertSame(
            'https://example.com/',
            Url::create(
                [
                    'host' => 'example.com',
                    'part' => '',
                ]
            )
        );

        self::assertSame(
            'ssh://example.com/',
            Url::create(
                [
                    'host'   => 'example.com',
                    'scheme' => 'ssh',
                    'part'   => '',
                ]
            )
        );

        self::assertSame(
            'http://example.com/',
            Url::create(
                [
                    'host' => 'example.com',
                    'port' => 80,
                ]
            )
        );

        self::assertSame(
            'https://example.com/',
            Url::create(
                [
                    'host' => 'example.com',
                    'port' => 443,
                ]
            )
        );

        self::assertSame(
            'https://example.com/page#hash',
            Url::create(
                [
                    'host'     => 'example.com',
                    'path'     => 'page',
                    'fragment' => 'hash',
                ]
            )
        );

        self::assertSame(
            'https://user:123456@example.com/page?foo=bar#hash',
            Url::create(
                [
                    'scheme'   => 'https',
                    'host'     => 'example.com',
                    'user'     => 'user',
                    'pass'     => '123456',
                    'path'     => 'page',
                    'query'    => [
                        'foo' => 'bar',
                    ],
                    'fragment' => '#hash',
                ]
            )
        );
    }


    public function testIsAbsolute()
    {
        self::assertTrue(Url::isAbsolute('https://site.com'));
        self::assertTrue(Url::isAbsolute('http://site.com'));
        self::assertTrue(Url::isAbsolute('//site.com'));
        self::assertTrue(Url::isAbsolute('ftp://site.com'));

        self::assertFalse(Url::isAbsolute('/path/to/file'));
        self::assertFalse(Url::isAbsolute('w:/path/to/file'));
        self::assertFalse(Url::isAbsolute('W:/path/to/file'));
        self::assertFalse(Url::isAbsolute('W:\path\to\file'));
    }

    /**
     * @dataProvider data_isValid()
     *
     * @param $url
     * @param $output
     */
    public function test_isValid($url, $absolute, $output)
    {
        self::assertSame($output, Url::isValid($url, $absolute));
    }

    /**
     * @return array
     */
    public function data_isValid()
    {
        return [
            ['test', true, false],
            ['google.ro', true, false],
            ['http://google.ro', true, true],
        ];
    }

    public function test_linkify()
    {
        $input  = 'great websites: http://www.google.com?param=test and http://yahoo.com/a/nested/folder';
        $expect = 'great websites: <a href="http://www.google.com?param=test">http://www.google.com?param=test</a> and <a href="http://yahoo.com/a/nested/folder">http://yahoo.com/a/nested/folder</a>';
        static::assertEquals($expect, Url::linkify($input));
        static::assertEquals($expect, Url::linkify($expect), 'linkify() tried to double linkify an href.');
    }
}
