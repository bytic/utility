<?php

declare(strict_types=1);

namespace Nip\Utility\Exception\Date;

use LogicException;
use Nip\Utility\Exception\Exception;

/**
 *
 */
final class MissingIntlExtensionException extends LogicException implements Exception
{
    private function __construct(string $message)
    {
        parent::__construct($message);
    }

    /** @internal */
    public static function fromMethod(string $method): self
    {
        return new self(sprintf('%s can not be used without the intl extension.', $method));
    }
}