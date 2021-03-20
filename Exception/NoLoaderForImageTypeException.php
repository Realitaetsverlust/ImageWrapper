<?php

namespace Realitaetsverlust\Wrapper\Exception;

use Exception;

class NoLoaderForImageTypeException extends Exception {
    public function __construct() {
        parent::__construct("For the given filetype is no loader available.");
    }
}
