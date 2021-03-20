<?php

namespace Realitaetsverlust\Wrapper\ImageTypes;

use Realitaetsverlust\Wrapper\Color;

class Wbmp extends ImageBase {
    /**
     * Outputs the image. If $destination is set to null, the image will be output to the browser
     *
     * @param string $destination
     * @param int $quality
     * @throws \Realitaetsverlust\Wrapper\Exception\InvalidQualitySettingException
     */
    public function output(string $destination, Color $color = null): bool {
        if($destination === null) {
            $this->sendHeaders();
        }

        if($color === null) {
            return imagewbmp($this->getResource(), $destination);
        }
        return imagewbmp($this->getResource(), $destination, $this->allocateColor($color));
    }

    /**
     * Sends the headers for the image type. Is called by output() if no destination is given, or can be called manually
     */
    public function sendHeaders() {
        header('Content-Type: image/vnd.wap.wbmp');
    }
}