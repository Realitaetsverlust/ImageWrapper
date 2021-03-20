<?php

namespace Realitaetsverlust\Wrapper\Exception;

use Exception;
use Throwable;

class ColorCouldNotBeAllocatedException extends Exception {
    public function __construct() {
        parent::__construct("The passed color object could not be allocated. Please check the parameters.");
    }

}