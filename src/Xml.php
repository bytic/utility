<?php
/** @noinspection PhpComposerExtensionStubsInspection */

namespace Nip\Utility;

/**
 * Class Xml
 * @package Nip\Utility
 */
class Xml
{
    /**
     * @param $xml
     * @param $schema
     *
     * @throws \Exception
     */
    public static function validate($xml, $schema)
    {
        libxml_use_internal_errors(true);
        $xmlDocument = new \DOMDocument();
        $xmlDocument->loadXML($xml);

        $schema = static::prepareSchema($schema);

        if (!$xmlDocument->schemaValidateSource($schema)) {
            $errors = libxml_get_errors();

            foreach ($errors as $error) {
                throw new \Exception(static::displayError($error));
            }
            libxml_clear_errors();
            throw new \Exception('INVALID XML');
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
        $response = file_get_contents($schema);

        return $response;
    }
}