<?php

namespace Nip\Utility\Tests;

use Nip\Utility\Email;

/**
 * Class EmailTest
 * @package Nip\Utility\Tests
 */
class EmailTest extends AbstractTest
{
    /**
     * @param $email
     * @param $masked
     * @dataProvider maskData()
     */
    public function testMask($email, $masked)
    {
        self::assertSame($masked, Email::mask($email));
    }

    /**
     * @return array
     */
    public function maskData()
    {
        return [
            ['gabi@gmail.com', 'g***@g****.com'],
            ['solomon@gmail.com', 's******@g****.com'],
            ['solomon@gmail.co.uk', 's******@g****.c*.uk'],
            ['solomon@gmail', 's******@gmail'],
            ['solomon', 's******'],
        ];
    }
}
