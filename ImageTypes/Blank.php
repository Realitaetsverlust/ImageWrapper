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
     * Overload output function to avoid output. An image created by PHP is a GDImage and has no filetype, therefore
     * it can't be saved yet.
     *
     * @param string|null $destination
     * @param int|null $quality
     * @return mixed|void
     * @throws NoFiletypeSetException
     */
    public function output() {
        throw new NoFiletypeSetException();
    }
}