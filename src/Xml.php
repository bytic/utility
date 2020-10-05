<?php

/** @noinspection PhpComposerExtensionStubsInspection */

namespace Nip\Utility;

use DOMDocument;
use Exception;
use Nip\Utility\Xml\FromArrayBuilder;
use SimpleXMLElement;

/**
 * Class Xml
 * @package Nip\Utility
 *
 * @inspiration https://github.com/cakephp/utility/blob/master/Xml.php
 */
class Xml
{
    /**
     * @param $xml
     *
     * @return SimpleXMLElement
     */
    public static function toObject($xml): SimpleXMLElement
    {
        return simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
    }

    /**
     * @param   array  $input
     * @param   array  $options
     *
     * @return DOMDocument|SimpleXMLElement
     */
    public static function fromArray($input, array $options = [])
    {
        return FromArrayBuilder::build($input, $options);
    }


    /**
     * @param $xml
     * @param $schema
     *
     * @throws Exception
     */
    public static function validate($xml, $schema)
    {
        libxml_use_internal_errors(true);
        $xmlDocument = new DOMDocument();
        $xmlDocument->loadXML($xml);

        $schema = static::prepareSchema($schema);

        if (!$xmlDocument->schemaValidateSource($schema)) {
            $errors = libxml_get_errors();

            foreach ($errors as $error) {
                throw new Exception(static::displayError($error));
            }
            libxml_clear_errors();
            throw new Exception('INVALID XML');
        }
    }

    /**
     * @param $error
     *
     * @return string
     */
    public static function displayError($error): string
    {
        $return = "\n";
        switch ($error->level) {
            case LIBXML_ERR_WARNING:
                $return .= "Warning $error->code: ";
                break;
            case LIBXML_ERR_ERROR:
                $return .= "Error $error->code: ";
                break;
            case LIBXML_ERR_FATAL:
                $return .= "Fatal Error $error->code: ";
                break;
        }
        $return .= trim($error->message);
        $return .= "\n";

        return $return;
    }

    /**
     * @param $schema
     *
     * @return false|string
     */
    protected static function prepareSchema($schema)
    {
        if (!Url::isValid($schema)) {
            return $schema;
        }

        return file_get_contents($schema);
    }
}
