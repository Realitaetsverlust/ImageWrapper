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

    /**
     * Sends the headers for the image type. Is called by output() if no destination is given, or can be called manually
     */
    public function sendHeaders() {
        header('Content-Type: image/vnd.wap.wbmp');
    }
}