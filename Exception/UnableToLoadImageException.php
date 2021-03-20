<?php

namespace Realitaetsverlust\Wrapper\Exception;

use Throwable;

class UnableToLoadImageException extends \Exception {
    public function __construct(string $inputFunction) {
        parent::__construct("$inputFunction was unable to load the image. Check if it's damaged and really the correct filetype");
    }
}