<?php

namespace Realitaetsverlust\Wrapper\ImageTypes;

class Jpeg extends ImageBase {
    public int $minQuality = 0;
    public int $maxQuality = 100;

    /**
     * Outputs the image. If $destination is set to null, the image will be output to the browser
     *
     * @param string $destination
     * @param int $quality
     * @throws \Realitaetsverlust\Wrapper\Exception\InvalidQualitySettingException
     */
    public function output(string $destination, int $quality = 90): bool {
        $this->validateQuality($quality);

        if($destination === null) {
            $this->sendHeaders();
        }

        return imagejpeg($this->resource, $destination, $quality);
    }
}