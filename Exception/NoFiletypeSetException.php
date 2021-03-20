<?php

namespace Realitaetsverlust\Wrapper\Exception;

use Exception;

class NoFiletypeSetException extends Exception {
    public function __construct() {
        parent::__construct("Image does not posses a filetype. Please use `convert()` to convert it into a image file before attempting an output.", 0, null);
    }
}