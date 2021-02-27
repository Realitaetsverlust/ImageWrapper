<?php

namespace Realitaetsverlust\Wrapper\ImageTypes;

abstract class ImageBase implements ImageInterface {
    /**
     * The currently used image resource
     * @var false|mixed
     */
    protected $resource;

    /**
     * The output function. Differs from filetype to filetype. Set in constructor
     * @var string
     */
    protected string $outputFunction = "";

    /**
     * The output function. Differs from filetype to filetype. Set in constructor
     * @var string
     */
    protected string $inputFunction = "";

    /**
     * The current filetype. Fetched and saved in constructor to save a teensy tiny little bit of performance
     * @var string
     */
    protected string $fileType;

    /**
     * Image size, loaded in constructor
     * @var array
     */
    protected array $imageSize;

    /**
     * ImageBase constructor.
     * @param string|null $imagePath
     */
    public function __construct(string $imagePath = null) {
        $reflect = new \ReflectionClass($this);
        $this->fileType = strtolower($reflect->getShortName());
        $this->inputFunction = "imagecreatefrom{$this->fileType}";
        $this->outputFunction = "image{$this->fileType}";

        if($imagePath !== null) {
            $this->resource = call_user_func($this->inputFunction, $imagePath);
            $this->imageSize = getimagesize($imagePath);
        }
    }

    /**
     * Calls the defind output function. May be overloaded by children.
     *
     * @param string|null $destination
     * @param int|null $quality
     * @return mixed|void
     */
    public function output(string $destination = null, int $quality = null) {
        call_user_func($this->outputFunction, $this->resource, $destination);
    }

    /**
     * Fetches the dcurrently used resource
     *
     * @return false|mixed
     */
    public function getResource() {
        return $this->resource;
    }

    /**
     * Load the currently used resource
     *
     * @param $resource
     * @return mixed|void
     */
    public function loadResource($resource) {
        $this->resource = $resource;
    }

    /**
     * Getter for filetype
     *
     * @return string
     */
    public function getFileType() {
        return $this->fileType;
    }

    /**
     * Returns the image size as array.
     *
     * WARNING: Image size is set on construction of the object. If you perform individual changes, like adding text,
     * the size will not be updated.
     *
     * @return array|false
     */
    public function getImageSize() : array {
        return $this->imageSize;
    }

}