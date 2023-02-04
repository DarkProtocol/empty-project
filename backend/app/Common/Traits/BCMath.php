<?php

declare(strict_types=1);

namespace App\Common\Traits;

trait BCMath
{
    /*
    protected function bcHexDec(string $hex): string
    {
        $hex = str_replace('0x', '', $hex);
        $dec = '0';
        $len = strlen($hex);
        for ($i = 1; $i <= $len; $i++) {
            $dec = bcadd(
                $dec,
                bcmul(
                    (string)hexdec($hex[$i - 1]),
                    bcpow(
                        '16',
                        (string)($len - $i)
                    )
                )
            );
        }
        return $dec;
    }
    */

    protected function bcHexDec(string $hex): string
    {
        $hex = str_replace('0x', '', $hex);
        if (strlen($hex) === 1) {
            return (string)hexdec($hex);
        } else {
            $remain = substr($hex, 0, -1);
            $last = substr($hex, -1);
            return bcadd(
                bcmul('16', $this->bcHexDec($remain)),
                (string)hexdec($last)
            );
        }
    }

    protected function bcDecHex(string $dec): string
    {
        $last = bcmod($dec, '16');
        $remain = bcdiv(bcsub($dec, $last), '16');

        if ($remain === '0') {
            return (string)dechex((int)$last);
        } else {
            return $this->bcDecHex($remain) . (string)dechex((int)$last);
        }
    }
}
