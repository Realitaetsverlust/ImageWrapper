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
}