<?php

namespace Nip\Utility\Tests;


use Nip\Utility\Json;
use Nip\Utility\Tests\Fixtures\BaseClass;
use Nip\Utility\Tests\Fixtures\JsonModel;
use stdClass;

/**
 * Class JsonTest
 * @package Nip\Utility\Tests
 */
class JsonTest extends AbstractTest
{

    public function testEncode()
    {
        // Arrayable data encoding
        $dataArrayable = $this->getMockBuilder(BaseClass::class)->getMock();
        $dataArrayable->method('toArray')->willReturn([]);
        $actual = Json::encode($dataArrayable);
        static::assertSame('{}', $actual);

        // basic data encoding
        $data = '1';
        static::assertSame('"1"', Json::encode($data));

        // simple array encoding
        $data = [1, 2];
        static::assertSame('[1,2]', Json::encode($data));
        $data = ['a' => 1, 'b' => 2];
        static::assertSame('{"a":1,"b":2}', Json::encode($data));

        // simple object encoding
        $data = new stdClass();
        $data->a = 1;
        $data->b = 2;
        static::assertSame('{"a":1,"b":2}', Json::encode($data));

        // empty data encoding
        $data = [];
        static::assertSame('[]', Json::encode($data));
        $data = new stdClass();
        static::assertSame('{}', Json::encode($data));

        // https://github.com/yiisoft/yii2/issues/957
        $data = (object) null;
        static::assertSame('{}', Json::encode($data));
    }

    public function testHtmlEncode()
    {
        // HTML escaped chars
        $data = '&<>"\'/';
        static::assertSame('"\u0026\u003C\u003E\u0022\u0027\/"', Json::htmlEncode($data));

        // basic data encoding
        $data = '1';
        static::assertSame('"1"', Json::htmlEncode($data));

        // simple array encoding
        $data = [1, 2];
        static::assertSame('[1,2]', Json::htmlEncode($data));
        $data = ['a' => 1, 'b' => 2];
        static::assertSame('{"a":1,"b":2}', Json::htmlEncode($data));

        // simple object encoding
        $data = new stdClass();
        $data->a = 1;
        $data->b = 2;
        static::assertSame('{"a":1,"b":2}', Json::htmlEncode($data));

        // https://github.com/yiisoft/yii2/issues/957
        $data = (object) null;
        static::assertSame('{}', Json::htmlEncode($data));

        // JsonSerializable
        $data = new JsonModel();
        static::assertSame('{"json":"serializable"}', Json::htmlEncode($data));

        // https://github.com/yiisoft/yii2/issues/10278
        $xml = '<?xml version="1.0" encoding="UTF-8"?>
<file>
  <apiKey>ieu2iqw4o</apiKey>
  <methodProperties>
    <FindByString>Kiev</FindByString>
  </methodProperties>
</file>';

        $document = simplexml_load_string($xml);
        static::assertSame(
            '{"apiKey":"ieu2iqw4o","methodProperties":{"FindByString":"Kiev"}}',
            Json::encode($document)
        );
    }

    public function testDecode()
    {
        // empty value
        $json = '';
        $actual = Json::decode($json);
        static::assertNull($actual);

        // basic data decoding
        $json = '"1"';
        static::assertSame('1', Json::decode($json));

        // array decoding
        $json = '{"a":1,"b":2}';
        static::assertSame(['a' => 1, 'b' => 2], Json::decode($json));

        // exception
        $json = '{"a":1,"b":2';
        $this->expectException(\InvalidArgumentException::class);
        Json::decode($json);
    }

    public function testDecodeInvalidParamException()
    {
        $this->expectExceptionMessage("Invalid JSON data.");
        $this->expectException(\InvalidArgumentException::class);
        /** @noinspection PhpParamsInspection */
        Json::decode([]);
    }

    public function testHandleJsonError()
    {
        // Basic syntax error
        try {
            $json = "{'a': '1'}";
            Json::decode($json);
        } catch (\InvalidArgumentException $e) {
            static::assertSame(Json::$jsonErrorMessages['JSON_ERROR_SYNTAX'], $e->getMessage());
        }

        // Unsupported type since PHP 5.5
        try {
            $fp = fopen('php://stdin', 'r');
            $data = ['a' => $fp];
            Json::encode($data);
            fclose($fp);
        } catch (\InvalidArgumentException $e) {
            if (PHP_VERSION_ID >= 50500) {
                static::assertSame(Json::$jsonErrorMessages['JSON_ERROR_UNSUPPORTED_TYPE'], $e->getMessage());
            } else {
                static::assertSame(Json::$jsonErrorMessages['JSON_ERROR_SYNTAX'], $e->getMessage());
            }
        }
    }

    /**
     * @link https://github.com/yiisoft/yii2/issues/17760
     */
    public function testEncodeDateTime()
    {
        $input = new \DateTime('October 12, 2014', new \DateTimeZone('UTC'));
        $output = Json::encode($input);
        static::assertEquals('{"date":"2014-10-12 00:00:00.000000","timezone_type":3,"timezone":"UTC"}', $output);
    }
}