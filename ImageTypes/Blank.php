<?php

namespace Realitaetsverlust\Wrapper\ImageTypes;

use Realitaetsverlust\Wrapper\Exception\NoFiletypeSetException;

/**
 * Class representing an untyped image just created.
 * @package Realitaetsverlust\Wrapper\ImageTypes
 */
class Blank extends ImageBase {
    /**
     * Overload parent constructor to avoid unnecessary routines being fired.
     */
    public function __construct(int $width, int $height) {
        $this->resource = imagecreatetruecolor($width, $height);
    }

    /**
     * Overload output function to avoid output. An image created by PHP is an resource and has no filetype, therefore
     * it can't be saved yet.
     *
     * @param string|null $destination
     * @param int|null $quality
     * @return mixed|void
     * @throws NoFiletypeSetException
     */
    public function output(string $destination = null, int $quality = null) {
        throw new NoFiletypeSetException("Image does not posses a filetype. Please use `convert()` to convert it into a image file before attempting an output.");
    }
}