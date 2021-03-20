<?php

namespace Realitaetsverlust\Wrapper\Exception;

use Throwable;

class NoXpmSupportException extends \Exception {
    public function __construct() {
        parent::__construct("PHP has no support for writing XPM Images! Please use another format.", 0, null);
    }
}