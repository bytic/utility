<?php

declare(strict_types=1);

namespace Nip\Utility\Tests\Social;

use Nip\Utility\Social\SocialNetworks;
use PHPUnit\Framework\TestCase;

/**
 *
 */
class SocialNetworksTest extends TestCase
{
    /**
     * @param $url
     * @param $expected
     *
     * @dataProvider data_networkFromUrl
     */
    public function test_fromUrl($url, $expected)
    {
        static::assertSame($expected, SocialNetworks::fromUrl($url));
    }

    public function data_networkFromUrl(): array
    {
        return [
            ['https://www.facebook.com/', 'facebook'],
            ['https://www.youtube.com/', 'youtube'],
            ['https://www.twitter.com/', 'twitter'],
            ['https://www.instagram.com/', 'instagram'],
            ['https://www.linkedin.com/', 'linkedin'],
            ['https://www.google.com/', 'google'],
            ['https://www.github.com/', 'github'],
            ['https://www.bitbucket.com/', 'bitbucket'],
            ['https://www.medium.com/', 'medium'],
        ];
    }

}
