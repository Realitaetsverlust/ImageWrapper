<?php

namespace Realitaetsverlust\Wrapper;

use Realitaetsverlust\Wrapper\Exception\NoLoaderForImageException;
use Realitaetsverlust\Wrapper\Exception\UnrecognizedFiletypeException;
use Realitaetsverlust\Wrapper\ImageTypes\Blank;
use Realitaetsverlust\Wrapper\ImageTypes\Bmp;
use Realitaetsverlust\Wrapper\ImageTypes\Gif;
use Realitaetsverlust\Wrapper\ImageTypes\ImageInterface;
use Realitaetsverlust\Wrapper\ImageTypes\Jpeg;
use Realitaetsverlust\Wrapper\ImageTypes\Png;
use Realitaetsverlust\Wrapper\ImageTypes\Wbmp;
use Realitaetsverlust\Wrapper\ImageTypes\Webp;
use Realitaetsverlust\Wrapper\ImageTypes\Xbm;

class ImageWrapper {

    /**
     * Image filetypes as defined by exif_imagetype (https://www.php.net/manual/en/function.exif-imagetype.php)
     */
    public const IMAGETYPE_GIF = 1;
    public const IMAGETYPE_JPEG = 2;
    public const IMAGETYPE_PNG = 3;
    public const IMAGETYPE_SWF = 4;
    public const IMAGETYPE_PSD = 5;
    public const IMAGETYPE_BMP = 6;
    public const IMAGETYPE_TIFF_II = 7;
    public const IMAGETYPE_TIFF_MM = 8;
    public const IMAGETYPE_JPC = 9;
    public const IMAGETYPE_JP2 = 10;
    public const IMAGETYPE_JPX = 11;
    public const IMAGETYPE_JB2 = 12;
    public const IMAGETYPE_SWC = 13;
    public const IMAGETYPE_IFF = 14;
    public const IMAGETYPE_WBMP = 15;
    public const IMAGETYPE_XBM = 16;
    public const IMAGETYPE_ICO = 17;
    public const IMAGETYPE_WEBP = 18;

    /**
     * Currently loaded image
     *
     * @var ImageInterface|Blank|Bmp|Gif|Jpeg|Png|Wbmp|Webp|Xbm c
     */
    public ImageInterface $image;

    /**
     * ImageWrapper constructor.
     *
     * @param string|null $imagePath
     * @throws NoLoaderForImageException
     * @throws UnrecognizedFiletypeException
     */
    public function __construct(string $imagePath = null) {
        if($imagePath === null) {
            $this->image = new Blank();
            return;
        }

        switch(exif_imagetype($imagePath)) {
            case self::IMAGETYPE_BMP:
                $this->image = new Bmp($imagePath);
                break;
            case self::IMAGETYPE_GIF:
                $this->image = new Gif($imagePath);
                break;
            case self::IMAGETYPE_JPEG:
                $this->image = new Jpeg($imagePath);
                break;
            case self::IMAGETYPE_PNG:
                $this->image = new Png($imagePath);
                break;
            case self::IMAGETYPE_WBMP:
                $this->image = new Wbmp($imagePath);
                break;
            case self::IMAGETYPE_WEBP:
                $this->image = new Webp($imagePath);
                break;
            case self::IMAGETYPE_XBM:
                $this->image = new Xbm($imagePath);
                break;
            case self::IMAGETYPE_JPX:
            case self::IMAGETYPE_JB2:
            case self::IMAGETYPE_SWC:
            case self::IMAGETYPE_IFF:
            case self::IMAGETYPE_ICO:
            case self::IMAGETYPE_TIFF_II:
            case self::IMAGETYPE_TIFF_MM:
            case self::IMAGETYPE_JPC:
            case self::IMAGETYPE_JP2:
            case self::IMAGETYPE_PSD:
            case self::IMAGETYPE_SWF:
                throw new NoLoaderForImageException();
                break;
            default:
                throw new UnrecognizedFiletypeException();
                break;
        }
        $this->imageName = pathinfo($imagePath)['filename'];
    }

    /**
     * Convert an image into another one. This actually doesn't do any conversion yet since conversion is done on output
     * and resources in PHP don't have any filetype, but prepares everything for the actual output.
     *
     * We do this by extracting the resource from the old image and load it into the new one.
     *
     * @param int $fileType
     * @throws NoLoaderForImageException
     * @throws UnrecognizedFiletypeExceptionc
     */
    public function convert(int $fileType) : void {
        $imageResource = $this->image->getResource();

        switch($fileType) {
            case self::IMAGETYPE_BMP:
                $this->image = new Bmp();
                break;
            case self::IMAGETYPE_GIF:
                $this->image = new Gif();
                break;
            case self::IMAGETYPE_JPEG:
                $this->image = new Jpeg();
                break;
            case self::IMAGETYPE_PNG:
                $this->image = new Png();
                break;
            case self::IMAGETYPE_WBMP:
                $this->image = new Wbmp();
                break;
            case self::IMAGETYPE_WEBP:
                $this->image = new Webp();
                break;
            case self::IMAGETYPE_XBM:
                $this->image = new Xbm();
                break;
            case self::IMAGETYPE_JPX:
            case self::IMAGETYPE_JB2:
            case self::IMAGETYPE_SWC:
            case self::IMAGETYPE_IFF:
            case self::IMAGETYPE_ICO:
            case self::IMAGETYPE_TIFF_II:
            case self::IMAGETYPE_TIFF_MM:
            case self::IMAGETYPE_JPC:
            case self::IMAGETYPE_JP2:
            case self::IMAGETYPE_PSD:
            case self::IMAGETYPE_SWF:
                throw new NoLoaderForImageException();
                break;
            default:
                throw new UnrecognizedFiletypeException();
                break;
        }

        $this->image->loadResource($imageResource);
    }

    /**
     * Call the designated output function which is set in the parent constructor in order to output the image. Behaves
     * exactly like image output functions, like imagepng or imagejpeg
     *
     * @param string|null $destination
     * @param int $quality
     * @throws Exception\NoFiletypeSetException
     */
    public function output(string $destination = null, int $quality = 90) : void {
        $this->image->output($destination, $quality);
    }

    /**
     * Fetch the filetype of the current file
     * @return string
     */
    public function getFileType() : string {
        return $this->image->getFileType();
    }
}