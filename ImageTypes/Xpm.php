<?php

namespace Realitaetsverlust\Wrapper\ImageTypes;

use Realitaetsverlust\Wrapper\Exception\NoXpmSupportException;

class Xpm extends ImageBase {
    /**
     * Dummy function, PHP has no support for writing XPM image files.
     */
    public function output(): bool {
        throw new NoXpmSupportException();
    }
}