<?php

namespace Realitaetsverlust\Wrapper\ImageTypes;

class Gif extends ImageBase {
    /**
     * Outputs the image. If $destination is set to null, the image will be output to the browser
     *
     * @param string|null $destination
     * @param bool $compressed
     */
    public function output(string $destination): bool {
        if($destination === null) {
            $this->sendHeaders();
        }
        return imagegif($this->getResource(), $destination);
    }

    /**
     * Sends the headers for the image type. Is called by output() if no destination is given, or can be called manually
     */
    public function sendHeaders() {
        header('Content-Type: image/gif');
    }
}