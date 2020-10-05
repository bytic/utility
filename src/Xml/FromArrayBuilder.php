<?php
/** @noinspection PhpComposerExtensionStubsInspection */

namespace Nip\Utility\Xml;

use DOMDocument;
use DOMText;
use SebastianBergmann\CodeCoverage\XmlException;
use SimpleXMLElement;

/**
 * Class FromArrayBuilder
 * @package Nip\Utility\Xml
 */
class FromArrayBuilder
{

    protected $input;

    /**
     * @var DOMDocument
     */
    protected $dom;
    protected $options = [];


    /**
     * @param          $input
     * @param   array  $options
     *
     * @return DOMDocument|SimpleXMLElement
     */
    public static function build($input, array $options = [])
    {
        if (is_object($input) && method_exists($input, 'toArray') && is_callable([$input, 'toArray'])) {
            $input = $input->toArray();
        }
        if (!is_array($input) || count($input) !== 1) {
            throw new XmlException('Invalid input.');
        }
        $key = key($input);
        if (is_int($key)) {
            throw new XmlException('The key of input must be alphanumeric');
        }

        return (new static($input, $options))->buildXml();
    }

    /**
     * FromArrayBuilder constructor.
     *
     * @param          $input
     * @param   array  $options
     */
    protected function __construct($input, array $options = [])
    {
        $this->input = $input;

        $defaults = [
            'format'   => 'tags',
            'version'  => '1.0',
            'encoding' => mb_internal_encoding(),
            'return'   => 'simplexml',
            'pretty'   => false,
        ];
        $options  += $defaults;

        $this->options = $options;

        $this->dom = new DOMDocument($options['version'], $options['encoding']);
        if ($options['pretty']) {
            $this->dom->formatOutput = true;
        }
    }

    /**
     * @return SimpleXMLElement|DOMDocument
     */
    protected function buildXml()
    {
        $this->addData($this->dom, $this->dom, $this->input, $this->options['format']);

        $return = strtolower($this->options['return']);
        if ($return === 'simplexml' || $return === 'simplexmlelement') {
            return new SimpleXMLElement($this->dom->saveXML());
        }

        return $this->dom;
    }

    /**
     * Recursive method to create childs from array
     *
     * @param   \DOMDocument              $dom     Handler to DOMDocument
     * @param   \DOMDocument|\DOMElement  $node    Handler to DOMElement (child)
     * @param   array                     $data    Array of data to append to the $node.
     * @param   string                    $format  Either 'attributes' or 'tags'. This determines where nested keys go.
     *
     * @return void
     * @throws XmlException
     */
    protected function addData(DOMDocument $dom, $node, &$data, $format): void
    {
        if (empty($data) || !is_array($data)) {
            return;
        }
        foreach ($data as $key => $value) {
            if (!is_string($key)) {
                throw new XmlException('Invalid array');
            }
            $this->addDataItem($dom, $node, $key, $value, $format);
        }
    }

    /**
     * @param   DOMDocument  $dom
     * @param                $node
     * @param                $key
     * @param                $value
     * @param                $format
     */
    protected function addDataItem(DOMDocument $dom, $node, $key, $value, $format): void
    {
        if (is_object($value) && method_exists($value, 'toArray') && is_callable([$value, 'toArray'])) {
            $value = $value->toArray();
        }

        if (!is_array($value)) {
            if (is_bool($value)) {
                $value = (int)$value;
            } elseif ($value === null) {
                $value = '';
            }
            $isNamespace = strpos($key, 'xmlns:');
            if ($isNamespace !== false) {
                /** @psalm-suppress PossiblyUndefinedMethod */
                $node->setAttributeNS('http://www.w3.org/2000/xmlns/', $key, (string)$value);
                return;
            }
            if ($key[0] !== '@' && $format === 'tags') {
                if (!is_numeric($value)) {
                    // Escape special characters
                    // https://www.w3.org/TR/REC-xml/#syntax
                    // https://bugs.php.net/bug.php?id=36795
                    $child = $dom->createElement($key, '');
                    $child->appendChild(new DOMText((string)$value));
                } else {
                    $child = $dom->createElement($key, (string)$value);
                }
                $node->appendChild($child);
            } else {
                if ($key[0] === '@') {
                    $key = substr($key, 1);
                }
                $attribute = $dom->createAttribute($key);
                $attribute->appendChild($dom->createTextNode((string)$value));
                $node->appendChild($attribute);
            }
        } else {
            if ($key[0] === '@') {
                throw new XmlException('Invalid array');
            }
            if (is_numeric(implode('', array_keys($value)))) {
                // List
                foreach ($value as $item) {
                    $itemData          = compact('dom', 'node', 'key', 'format');
                    $itemData['value'] = $item;
                    $this->createChild($itemData);
                }
            } else {
                // Struct
                $this->createChild(compact('dom', 'node', 'key', 'value', 'format'));
            }
        }
    }

    /**
     * Helper to _fromArray(). It will create childs of arrays
     *
     * @param   array  $data  Array with information to create childs
     *
     * @return void
     */
    protected function createChild(array $data): void
    {
        $data += [
            'dom'    => null,
            'node'   => null,
            'key'    => null,
            'value'  => null,
            'format' => null,
        ];

        $value  = $data['value'];
        $dom    = $data['dom'];
        $key    = $data['key'];
        $format = $data['format'];
        $node   = $data['node'];

        $childNS = $childValue = null;
        if (is_object($value) && method_exists($value, 'toArray') && is_callable([$value, 'toArray'])) {
            $value = $value->toArray();
        }
        if (is_array($value)) {
            if (isset($value['@'])) {
                $childValue = (string)$value['@'];
                unset($value['@']);
            }
            if (isset($value['xmlns:'])) {
                $childNS = $value['xmlns:'];
                unset($value['xmlns:']);
            }
        } elseif (!empty($value) || $value === 0 || $value === '0') {
            $childValue = (string)$value;
        }

        $child = $dom->createElement($key);
        if ($childValue !== null) {
            $child->appendChild($dom->createTextNode($childValue));
        }
        if ($childNS) {
            $child->setAttribute('xmlns', $childNS);
        }

        $this->addData($dom, $child, $value, $format);
        $node->appendChild($child);
    }
}