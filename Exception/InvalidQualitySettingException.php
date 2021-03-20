<?php

namespace Realitaetsverlust\Wrapper\Exception;

use Exception;
use Throwable;

class InvalidQualitySettingException extends Exception {
    public function __construct(int $usedQuality, int $minQuality, int $maxQuality) {
        parent::__construct("The used quality parameter was invalid! Used was $usedQuality, the min value is $minQuality and the max value is $maxQuality");
    }
}