<?php

namespace Nip\Utility;

use Carbon\Carbon;
use IntlDateFormatter;
use Nip\Utility\Date\IntlFormatter;
use Nip\Utility\Exception\Date\MissingIntlExtensionException;

/**
 * Class Date
 * @package Nip\Utility
 */
class Date extends Carbon
{
    /**
     * @param   \IntlDateFormatter::FULL|\IntlDateFormatter::LONG|\IntlDateFormatter::MEDIUM|\IntlDateFormatter::SHORT|\IntlDateFormatter::NONE|null  $format
     */
    public function formatIntl(?int $dateFormat = null, ?int $timeFormat = null, ?string $pattern = null): string
    {
        /** @psalm-suppress ImpureFunctionCall */
        if (!class_exists(IntlDateFormatter::class)) {
            throw MissingIntlExtensionException::fromMethod(__METHOD__); // @codeCoverageIgnore
        }

        return IntlFormatter::formatDateTime(
            $this,
            $dateFormat ?? IntlDateFormatter::LONG,
            $timeFormat ?? IntlDateFormatter::MEDIUM,
            $pattern
        );
    }
}
