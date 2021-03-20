<?php

namespace Realitaetsverlust\Wrapper;

use Realitaetsverlust\Wrapper\Exception\NoLoaderForImageTypeException;
use Realitaetsverlust\Wrapper\Exception\UnrecognizedFiletypeException;
use Realitaetsverlust\Wrapper\ImageTypes\Bmp;
use Realitaetsverlust\Wrapper\ImageTypes\Gif;
use Realitaetsverlust\Wrapper\ImageTypes\ImageBase;
use Realitaetsverlust\Wrapper\ImageTypes\Jpeg;
use Realitaetsverlust\Wrapper\ImageTypes\Png;
use Realitaetsverlust\Wrapper\ImageTypes\Wbmp;
use Realitaetsverlust\Wrapper\ImageTypes\Webp;
use Realitaetsverlust\Wrapper\ImageTypes\Xbm;

class ImageFactory {
    /**
     * Determines the appropriate class by utilizing exif_filename() and returns the created class.
     *
     * @param string $filename
     * @return ImageBase
     * @throws Exception\UnableToLoadImageException
     * @throws NoLoaderForImageTypeException
     * @throws UnrecognizedFiletypeException
     */
    public static function create(string $filename): ImageBase {
        switch($filetype = exif_imagetype($filename)) {
            case IMAGETYPE_BMP:
                return new Bmp($filename);
                break;
            case IMAGETYPE_GIF:
                return new Gif($filename);
                break;
            case IMAGETYPE_JPEG:
                return new Jpeg($filename);
                break;
            case IMAGETYPE_PNG:
                return new Png($filename);
                break;
            case IMAGETYPE_WBMP:
                return new Wbmp($filename);
                break;
            case IMAGETYPE_WEBP:
                return new Webp($filename);
                break;
            case IMAGETYPE_XBM:
                return new Xbm($filename);
                break;
            case IMAGETYPE_JPX:
            case IMAGETYPE_JB2:
            case IMAGETYPE_SWC:
            case IMAGETYPE_IFF:
            case IMAGETYPE_ICO:
            case IMAGETYPE_TIFF_II:
            case IMAGETYPE_TIFF_MM:
            case IMAGETYPE_JPC:
            case IMAGETYPE_JP2:
            case IMAGETYPE_PSD:
            case IMAGETYPE_SWF:
                throw new NoLoaderForImageTypeException();
                break;
            default:
                throw new UnrecognizedFiletypeException();
                break;
        }
    }
}
