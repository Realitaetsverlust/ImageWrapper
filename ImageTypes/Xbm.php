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
}