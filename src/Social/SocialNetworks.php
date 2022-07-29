<?php

declare(strict_types=1);

namespace Nip\Utility\Social;

/**
 *
 */
class SocialNetworks
{
    public const FACEBOOK = 'facebook';
    public const YOUTUBE = 'youtube';
    public const TWITTER = 'twitter';
    public const INSTAGRAM = 'instagram';
    public const LINKEDIN = 'linkedin';
    public const GOOGLE = 'google';
    public const GITHUB = 'github';
    public const BITBUCKET = 'bitbucket';
    public const MEDIUM = 'medium';

    public static function fromUrl(string $url): ?string
    {
        $pieces = parse_url($url);
        $domain = $pieces['host'] ?? '';
        switch ($domain) {
            case 'www.facebook.com':
            case 'facebook.com':
                return self::FACEBOOK;

            case 'www.youtube.com':
            case 'youtube.com':
                return self::YOUTUBE;

            case 'www.twitter.com':
            case 'twitter.com':
                return self::TWITTER;

            case 'www.instagram.com':
            case 'instagram.com':
                return self::INSTAGRAM;

            case 'www.linkedin.com':
            case 'linkedin.com':
                return self::LINKEDIN;

            case 'www.google.com':
            case 'google.com':
                return self::GOOGLE;

            case 'www.github.com':
            case 'github.com':
                return self::GITHUB;

            case 'www.bitbucket.com':
            case 'bitbucket.com':
                return self::BITBUCKET;

            case 'www.medium.com':
            case 'medium.com':
                return self::MEDIUM;
        }

        return null;
    }
}
