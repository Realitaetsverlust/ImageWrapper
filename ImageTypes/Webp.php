<?php

namespace Realitaetsverlust\Wrapper\ImageTypes;

class Webp extends ImageBase {
    protected int $minQuality = 0;
    protected int $maxQuality = 100;

    /**
     * Outputs the image. If $destination is set to null, the image will be output to the browser
     *
     * @param string $destination
     * @param int $quality
     * @return bool
     * @throws \Realitaetsverlust\Wrapper\Exception\InvalidQualitySettingException
     */
    public function output(string $destination = null, int $quality = 80): bool {
        if($destination === null) {
            $this->sendHeaders();
        }

        return imagewebp($this->getResource(), $destination, $quality);
    }
}