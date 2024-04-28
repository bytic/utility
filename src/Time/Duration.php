<?php

declare(strict_types=1);

namespace Nip\Utility\Time;

/**
 * Class Duration
 * @package Nip\Utility\Time
 */
class Duration
{
    protected $value = null;
    protected $parts = null;
    protected $seconds = null;


    /**
     * Duration constructor.
     *
     * @param $duration
     */
    public function __construct($duration)
    {
        if (is_numeric($duration)) {
            $duration = floatval($duration);
            $this->setSeconds($duration);
        }
        if (is_string($duration)) {
            $this->setValue($duration);
            $this->parseSeconds();
        }
    }

    /**
     * @param   null|string  $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    public function parseSeconds()
    {
        $parts   = $this->getParts();
        $seconds = 0;
        if ($parts['h'] > 0) {
            $seconds += $parts['h'] * 3600;
        }
        if ($parts['m'] > 0) {
            $seconds += $parts['m'] * 60;
        }
        if ($parts['s'] > 0) {
            $seconds += $parts['s'];
        }
        $this->setSeconds($seconds);
    }

    /**
     * @return []
     */
    public function getParts()
    {
        if ($this->parts === null) {
            $this->parseParts();
        }

        return $this->parts;
    }

    /**
     * @param   array  $parts
     */
    public function setParts($parts)
    {
        $this->parts = $parts;
    }

    public function parseParts()
    {
        $this->parts = [
            'h' => 0,
            'm' => 0,
            's' => 0,
        ];

        if ($this->value && substr_count((string)$this->value, ':') >= 1) {
            $this->parsePartsFromString();

            return;
        }

        if ($this->seconds > 0) {
            $this->parsePartsFromSeconds();

            return;
        }
    }

    public function parsePartsFromString()
    {
        $parts = explode(':', (string)$this->value);

        $seconds      = floatval(array_pop($parts));
        $seconds      = round($seconds, 2);
        $wholeSeconds = intval($seconds);
        $this->setSecondsPart($wholeSeconds);

        $micro = round($seconds - $wholeSeconds, 2);

        $this->setMicroPart($micro);
        $this->setSecondsPart($wholeSeconds);

        $minutes = intval(array_pop($parts));
        $this->setMinutesPart($minutes);

        $hours = intval(array_pop($parts));
        $this->setHoursPart($hours);
    }

    /**
     * @param   string  $value
     */
    public function setHoursPart($value)
    {
        $this->setPart('h', $value);
    }

    /**
     * @param   string  $p
     * @param   string  $v
     */
    public function setPart($p, $v)
    {
        $this->parts[$p] = $v;
    }

    /**
     * @param   int  $v
     */
    public function setMinutesPart(int $v)
    {
        $this->setPart('m', $v);
    }

    /**
     * @param   int  $v
     */
    public function setSecondsPart(int $v)
    {
        $this->setPart('s', $v);
    }

    /**
     * @param   int  $v
     */
    public function setMicroPart(float $v)
    {
        $this->setPart('ms', $v);
    }

    public function parsePartsFromSeconds()
    {
        $seconds = $this->getSeconds();
        $hours   = intval((floor($seconds / 3600)));
        if ($hours) {
            $seconds = $seconds - $hours * 3600;
        }
        $this->setHoursPart($hours);

        $minutes = intval((floor($seconds / 60)));
        if ($minutes) {
            $seconds = $seconds - $minutes * 60;
        }
        $this->setMinutesPart($minutes);

        $seconds      = round($seconds, 2);
        $wholeSeconds = intval($seconds);
        $this->setSecondsPart($wholeSeconds);

        $micro = round($seconds - $wholeSeconds, 2);
        $this->setMicroPart($micro);
    }

    /**
     * @return double
     */
    public function getSeconds()
    {
        if ($this->seconds === null) {
            $this->parseSeconds();
        }

        return $this->seconds;
    }

    /**
     * @param   null|int  $seconds
     */
    public function setSeconds(float $seconds)
    {
        $this->seconds = $seconds;
    }

    /**
     * @return string
     */
    public function getHoursPart()
    {
        return $this->getPart('h');
    }

    /**
     * @param   string  $part
     * @param   int     $default
     *
     * @return int|mixed
     */
    public function getPart($part, $default = 0)
    {
        if ($this->parts === null) {
            $this->parseParts();
        }

        return $this->parts[$part] ?? $default;
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
        $hours   = $this->formatPart($this->getHoursPart());
        $minutes = $this->formatPart($this->getMinutesPart());
        $seconds = $this->formatPart($this->getSecondsPart());
        $micro   = str_pad(
            str_replace('0.', '', (string)$this->getMicroPart()),
            2,
            '0',
            STR_PAD_LEFT
        );

        return $hours . ':' . $minutes . ':' . $seconds . '.' . $micro;
    }

    /**
     * @return string
     */
    public function getHTML()
    {
        $return = '<time>';

        $hours  = $this->formatPart($this->getHoursPart());
        $return .= '<span class="hour">' . $hours . '</span>';

        $minutes = $this->formatPart($this->getMinutesPart());
        $return  .= '<span class="separator">:</span>';
        $return  .= '<span class="minutes">' . $minutes . '</span>';

        $seconds = $this->formatPart($this->getSecondsPart());
        $return  .= '<span class="separator">:</span>';
        $return  .= '<span class="seconds">' . $seconds . '</span>';

        $micro  = str_replace('0.', '', (string)$this->getMicroPart());
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
            $return .= ($return ? ' ' : '') . $this->formatPart($hours) . 'h';
        }

        $minutes = $this->getMinutesPart();
        if ($minutes or $return) {
            $return .= ($return ? ' ' : '') . $this->formatPart($minutes) . 'm';
        }

        $seconds = $this->getSecondsPart();
        if ($seconds) {
            $return .= ($return ? ' ' : '') . $this->formatPart($seconds) . 's';
        }

        return $return;
    }

    /**
     * @param $value
     *
     * @return string
     */
    protected function formatPart($value): string
    {
        return str_pad((string)$value, 2, '0', STR_PAD_LEFT);
    }
}
