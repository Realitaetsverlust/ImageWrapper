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

    public function create(string $filename): ImageBase {
        switch($filetype = exif_imagetype($filename)) {
            case self::IMAGETYPE_BMP:
                return new Bmp($filename);
                break;
            case self::IMAGETYPE_GIF:
                return new Gif($filename);
                break;
            case self::IMAGETYPE_JPEG:
                return new Jpeg($filename);
                break;
            case self::IMAGETYPE_PNG:
                return new Png($filename);
                break;
            case self::IMAGETYPE_WBMP:
                return new Wbmp($filename);
                break;
            case self::IMAGETYPE_WEBP:
                return new Webp($filename);
                break;
            case self::IMAGETYPE_XBM:
                return new Xbm($filename);
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
                throw new NoLoaderForImageTypeException();
                break;
            default:
                throw new UnrecognizedFiletypeException();
                break;
        }
    }
}
