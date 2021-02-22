<?php

namespace Realitaetsverlust\Wrapper\ImageTypes;

interface ImageInterface {
    /**
     * Output function for the image
     *
     * @param string|null $destination
     * @param int $quality
     * @return mixed
     */
    public function output(string $destination = null, int $quality = 90);

    /**
     * Fetches the resource from the object
     * @return mixed
     */
    public function getResource();

    /**
     * Load a resource into the object
     *
     * @param $resource
     * @return mixed
     */
    public function loadResource($resource);
}