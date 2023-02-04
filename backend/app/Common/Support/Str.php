<?php

declare(strict_types=1);

namespace App\Common\Support;

use Illuminate\Support\Str as BaseStr;
use Exception;

class Str extends BaseStr
{
    public static function cleanDecimals(string $value): string
    {
        $pos = strpos($value, '.');

        if ($pos === false) {
            return $value;
        }

        return rtrim(rtrim($value, '0'), '.');
    }

    public static function formatReferralLevel(string $level): string
    {
        return mb_strtoupper(str_replace('_', ' ', $level));
    }

    public static function maskMiddle(string $str, int $first = 1, int $last = 1): string
    {
        $len = strlen($str);
        $toShow = $first + $last;

        return substr($str, 0, $len <= $toShow ? 0 : $first)
            . str_repeat('*', $len - ($len <= $toShow ? 0 : $toShow))
            . substr($str, $len - $last, $len <= $toShow ? 0 : $last);
    }

    public static function maskEmail(string $email, int $first = 2, int $last = 1): string
    {
        $mailParts = explode('@', $email);
        $hostParts = explode('.', $mailParts[1]);

        $mailParts[0] = self::maskMiddle($mailParts[0], $first, $last);
        $hostParts[0] = self::maskMiddle($hostParts[0], $first, $last);
        $mailParts[1] = implode('.', $hostParts);

        return implode('@', $mailParts);
    }
}
