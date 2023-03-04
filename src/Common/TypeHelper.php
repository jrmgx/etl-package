<?php

namespace Jrmgx\Etl\Common;

class TypeHelper
{
    public static function isFloat(mixed $candidate): bool
    {
        return null === $candidate || (
            is_numeric($candidate) && (string) (float) $candidate === (string) $candidate
        );
    }

    public static function isInt(mixed $candidate): bool
    {
        return null === $candidate || (
            is_numeric($candidate) && (string) (int) $candidate === (string) $candidate
        );
    }

    public static function isStringable(mixed $candidate): bool
    {
        return (null === $candidate || \is_scalar($candidate)) && !\is_bool($candidate);
    }

    public static function isComplex(mixed $candidate): bool
    {
        return null !== $candidate &&
            !\is_bool($candidate) &&
            !self::isFloat($candidate) &&
            !self::isStringable($candidate);
    }

    /**
     * Given a multidimensional array of keys => values, returns the whole list of keys used.
     *
     * @param array<array<string, mixed>> $data
     *
     * @return array<string>
     */
    public static function wholeKeys(array $data): array
    {
        return array_values(array_unique(array_merge(...array_map(array_keys(...), $data))));
    }
}
