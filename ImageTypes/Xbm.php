<?php

namespace Realitaetsverlust\Wrapper\ImageTypes;

use Realitaetsverlust\Wrapper\Color;

class Xbm extends ImageBase {
    /**
     * Outputs the image. If $destination is set to null, the image will be output to the browser
     *
     * @param string $destination
     * @param int $quality
     * @throws \Realitaetsverlust\Wrapper\Exception\InvalidQualitySettingException
     */
    public function output(string $destination, Color $color): bool {
        if($destination === null) {
            $this->sendHeaders();
        }
        return imagexbm($this->getResource(), $destination, $this->allocateColor($color));
    }

    /**
     * Sends the headers for the image type. Is called by output() if no destination is given, or can be called manually
     */
    public function sendHeaders() {
        header('Content-Type: image/vnd.wap.wbmp');
    }
}