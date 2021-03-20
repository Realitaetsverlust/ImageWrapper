<?php

namespace Realitaetsverlust\Wrapper\Exception;

use Exception;

class UnrecognizedFiletypeException extends Exception {
    public function __construct() {
        parent::__construct("The filetype was not recognized by exif_filetype(). The file is most likely corrupt.");
    }
}
