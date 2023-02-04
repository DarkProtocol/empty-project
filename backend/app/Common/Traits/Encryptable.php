<?php

declare(strict_types=1);

namespace App\Common\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

/**
 * @mixin Model
 */
trait Encryptable
{
    public function getAttribute($key)
    {
        /** @var string $value */
        $value = parent::getAttribute($key);

        if (in_array($key, $this->encryptable)) {
            $value = Crypt::decrypt($value);
        }

        return $value;
    }

    public function setAttribute($key, $value)
    {
        if (in_array($key, $this->encryptable)) {
            $value = Crypt::encrypt($value);
        }

        return parent::setAttribute($key, $value);
    }
}
