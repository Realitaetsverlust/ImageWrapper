<?php

namespace Realitaetsverlust\Wrapper\ImageTypes;

class Png extends ImageBase {
    public int $minQuality = -1;
    public int $maxQuality = 9;

    /**
     * Outputs the image. If $destination is set to null, the image will be output to the browser
     *
     * @param string $destination
     * @param int $quality
     * @return bool
     * @throws \Realitaetsverlust\Wrapper\Exception\InvalidQualitySettingException
     */
    public function output(string $destination, int $quality = -1): bool {
        $this->validateQuality($quality);

        if($destination === null) {
            $this->sendHeaders();
        }

        return imagejpeg($this->resource, $destination, $quality);
    }

    /**
     * Sends the headers for the image type. Is called by output() if no destination is given, or can be called manually
     */
    public function sendHeaders(): void {
        header('Content-Type: image/png');
    }
}