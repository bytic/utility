<?php

namespace Nip\Utility\Time;

/**
 * Class Duration
 * @package Nip\Utility\Time
 */
class Duration
{
    protected $_value = null;
    protected $_parts = null;
    protected $_seconds = null;

    /**
     * Duration constructor.
     * @param $duration
     */
    public function __construct($duration)
    {
        if (is_numeric($duration)) {
            $this->setSeconds($duration);
        }
        if (is_string($duration)) {
            $this->setValue($duration);
            $this->parseSeconds();
        }
    }


    /**
     * @param null $value
     */
    public function setValue($value)
    {
        $this->_value = $value;
    }

    public function parseSeconds()
    {
        $parts = $this->getParts();
        if (count($parts) == 3) {
            $seconds = 0;
            $seconds += $parts['h'] * 3600;
            $seconds += $parts['m'] * 60;
            $seconds += $parts['s'];
            $this->setSeconds($seconds);
        }
    }

    public function getParts()
    {
        if ($this->_parts === null) {
            $this->parseParts();
        }

        return $this->_parts;
    }

    /**
     * @param array $parts
     */
    public function setParts($parts)
    {
        $this->_parts = $parts;
    }

    public function parseParts()
    {
        if ($this->_value && substr_count($this->_value, ':') == 2) {
            $this->parsePartsFromString();
        } elseif ($this->_seconds > 0) {
            $this->parsePartsFromSeconds();
        }
    }

    public function parsePartsFromString()
    {
        list($h, $m, $s) = explode(':', $this->_value);

        $this->setHoursPart($h);
        $this->setMinutesPart($m);
        $this->setSecondsPart($s);
    }

    /**
     * @param string $v
     */
    public function setHoursPart($v)
    {
        $this->setPart('h', $v);
    }

    /**
     * @param string $p
     * @param string $v
     */
    public function setPart($p, $v)
    {
        $this->_parts[$p] = $v;
    }

    /**
     * @param string $v
     */
    public function setMinutesPart($v)
    {
        $this->setPart('m', $v);
    }

    /**
     * @param string $v
     */
    public function setSecondsPart($v)
    {
        $this->setPart('s', $v);
    }

    /**
     * @param $v
     */
    public function setMicroPart($v)
    {
        $this->setPart('ms', $v);
    }

    public function parsePartsFromSeconds()
    {
        $seconds = $this->getSeconds();
        if ($hours = intval((floor($seconds / 3600)))) {
            $seconds = $seconds - $hours * 3600;
        }

        $this->setHoursPart($hours);

        if ($minutes = intval((floor($seconds / 60)))) {
            $seconds = $seconds - $minutes * 60;
        }

        $this->setMinutesPart($minutes);

        $seconds = round($seconds, 2);
        $this->setSecondsPart(intval($seconds));

        $micro = round($seconds - intval($seconds), 2);
        $this->setMicroPart($micro);
    }

    /**
     * @return double
     */
    public function getSeconds()
    {
        if ($this->_seconds === null) {
            $this->parseSeconds();
        }

        return $this->_seconds;
    }

    /**
     * @param null $seconds
     */
    public function setSeconds($seconds)
    {
        $this->_seconds = $seconds;
    }

    /**
     * @return string
     */
    public function getHoursPart()
    {
        return $this->getPart('h');
    }

    /**
     * @param string $p
     */
    public function getPart($p)
    {
        if ($this->_parts === null) {
            $this->parseParts();
        }

        return $this->_parts[$p];
    }

    /**
     * @return string
     */
    public function getMinutesPart()
    {
        return $this->getPart('m');
    }

    /**
     * @return string
     */
    public function getSecondsPart()
    {
        return $this->getPart('s');
    }

    /**
     * @return string
     */
    public function getMicroPart()
    {
        return $this->getPart('ms');
    }

    /**
     * @return string
     */
    public function getDefaultString()
    {
        $hours = str_pad($this->getHoursPart(), 2, 0, STR_PAD_LEFT);
        $minutes = str_pad($this->getMinutesPart(), 2, 0, STR_PAD_LEFT);
        $seconds = str_pad($this->getSecondsPart(), 2, 0, STR_PAD_LEFT);
        $micro = str_replace('0.', '', $this->getMicroPart());
        return $hours . ':' . $minutes . ':' . $seconds . '.' . $micro;
    }

    /**
     * @return string
     */
    public function getHTML()
    {
        $return = '<time>';

        $hours = str_pad($this->getHoursPart(), 2, 0, STR_PAD_LEFT);
        $return .= '<span class="hour">' . $hours . '</span>';

        $minutes = str_pad($this->getMinutesPart(), 2, 0, STR_PAD_LEFT);
        $return .= '<span class="separator">:</span>';
        $return .= '<span class="minutes">' . $minutes . '</span>';

        $seconds = str_pad($this->getSecondsPart(), 2, 0, STR_PAD_LEFT);
        $return .= '<span class="separator">:</span>';
        $return .= '<span class="seconds">' . $seconds . '</span>';

        $micro = str_replace('0.', '', $this->getMicroPart());
        $return .= '<span class="micro">.' . $micro . '</span>';

        $return .= '</time>';

        return $return;
    }

    /**
     * @return string
     */
    public function getFormatedString()
    {
        $return = '';

        $hours = $this->getHoursPart();
        if ($hours or $return) {
            $return .= ($return ? ' ' : '') . str_pad($hours, 2, 0, STR_PAD_LEFT) . 'h';
        }

        $minutes = $this->getMinutesPart();
        if ($minutes or $return) {
            $return .= ($return ? ' ' : '') . str_pad($minutes, 2, 0, STR_PAD_LEFT) . 'm';
        }

        $seconds = $this->getSecondsPart();
        if ($seconds) {
            $return .= ($return ? ' ' : '') . str_pad($seconds, 2, 0, STR_PAD_LEFT) . 's';
        }

        return $return;
    }
}
