<?php

namespace Realitaetsverlust\Wrapper\ImageTypes;

class Bmp extends ImageBase {
    /**
     * Outputs the image. If $destination is set to null, the image will be output to the browser
     *
     * @param string|null $destination
     * @param bool $compressed
     */
    public function output(string $destination = null, bool $compressed = false): void {
        if($destination === null) {
            $this->sendHeaders();
        }
        imagebmp($this->getResource(), $destination, $compressed);
    }

    /**
     * Sends the headers for the image type. Is called by output() if no destination is given, or can be called manually
     */
    public function sendHeaders() : void {
        header('Content-Type: image/bmp');
    }
}