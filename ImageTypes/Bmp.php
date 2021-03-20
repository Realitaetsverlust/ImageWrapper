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
}