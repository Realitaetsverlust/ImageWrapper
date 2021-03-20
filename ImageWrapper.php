<?php

namespace Realitaetsverlust\Wrapper;

use Realitaetsverlust\Wrapper\Exception\ColorCouldNotBeAllocatedException;
use Realitaetsverlust\Wrapper\Exception\HeightForBlankImageNotSetException;
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
     * @var ImageInterface|Blank|Bmp|Gif|Jpeg|Png|Wbmp|Webp|Xbm
     */
    protected ImageInterface $image;

    /**
     * @var string
     */
    private string $imageName;

    /**
     * @var array
     */
    private array $colorPallet;

    /**
     * ImageWrapper constructor.
     *
     * Bit confusing, but nicer to use for the end user. $imagePathOrWidth can either be a directory string pointing to
     * a file, or it can be an integer designating the width of the image. In the last case, height has to be declared as
     * well in order for imagecreate() to work in the constructor of Blank(). It's not the nicest thing codewise, but
     * I believe it is the easiest for a user because we don't need any init functions for Blank().
     *
     * @param null $imagePathOrWidth
     * @param int $height
     * @throws NoLoaderForImageException
     * @throws UnrecognizedFiletypeException
     */
    public function __construct(string|int $imagePathOrWidth = null, int $height = null) {
        if(is_int($imagePathOrWidth)) {
            if($height === null) {
                throw new HeightForBlankImageNotSetException();
            }
            $this->image = new Blank($imagePathOrWidth, $height);
            return;
        }

        switch(exif_imagetype($imagePathOrWidth)) {
            case self::IMAGETYPE_BMP:
                $this->image = new Bmp($imagePathOrWidth);
                break;
            case self::IMAGETYPE_GIF:
                $this->image = new Gif($imagePathOrWidth);
                break;
            case self::IMAGETYPE_JPEG:
                $this->image = new Jpeg($imagePathOrWidth);
                break;
            case self::IMAGETYPE_PNG:
                $this->image = new Png($imagePathOrWidth);
                break;
            case self::IMAGETYPE_WBMP:
                $this->image = new Wbmp($imagePathOrWidth);
                break;
            case self::IMAGETYPE_WEBP:
                $this->image = new Webp($imagePathOrWidth);
                break;
            case self::IMAGETYPE_XBM:
                $this->image = new Xbm($imagePathOrWidth);
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
        $this->imageName = pathinfo($imagePathOrWidth)['filename'];
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
    public function convert(int $fileType) : ImageWrapper {
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
        return $this;
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

    /**
     * Set the blending mode for the image
     *
     * @param bool $blendmode
     * @return ImageWrapper $this
     */
    public function alphaBlending(bool $blendmode) : ImageWrapper {
        imagealphablending($this->image->getResource(), $blendmode);
        return $this;
    }

    /**
     *  Activate the fast drawing antialiased methods for lines and wired polygons.
     *
     * @param bool $enabled
     * @return ImageWrapper $this
     */
    public function enableAntialias(bool $enabled) : ImageWrapper {
        imageantialias($this->image->getResource(), $enabled);
        return $this;
    }

    /**
     * Draws an arc. if $style is passed, the arc will be filled
     *
     * @param int $cx
     * @param int $cy
     * @param int $width
     * @param int $height
     * @param int $start
     * @param int $end
     * @param int $color
     * @param int|null $style
     * @return ImageWrapper $this
     */
    public function drawArc(int $cx, int $cy, int $width, int $height, int $start, int $end, Color $color, int $style = null) : ImageWrapper {
        $allocatedColor = $this->allocateColor($color);
        if($style) {
            imagefilledarc($this->image->getResource(), $cx, $cy, $width, $height, $start, $end, $allocatedColor, $style);
        } else {
            imagearc($this->image->getResource(), $cx, $cy, $width, $height, $start, $end, $allocatedColor);
        }
        return $this;
    }

    /**
     * Draws a char horizontally or vertically onto the image.
     *
     * @param bool $drawVertical
     * @param int $font
     * @param int $x
     * @param int $y
     * @param string $c
     * @param int $color
     * @return ImageWrapper $this
     */
    public function drawChar(bool $drawVertical, int $font, int $x, int $y, string $c, int $color) : ImageWrapper {
        if($drawVertical) {
            imagecharup($this->image->getResource(), $font, $x, $y, $c, $color);
        } else {
            imagechar($this->image->getResource(), $font, $x, $y, $c, $color);
        }

        return $this;
    }

    /**
     * Fetches the color value at a specific location
     *
     * @param int $x
     * @param int $y
     * @return int|false
     */
    public function getColoarAt(int $x, int $y) : int|false{
        return imagecolorat($this->image->getResource(), $x, $y);
    }

    /**
     * Cuts a part from the current image and copies it into a new image
     *
     * @param int $x
     * @param int $y
     * @param int $width
     * @param int $height
     * @return ImageWrapper $this
     * @throws NoLoaderForImageException
     * @throws UnrecognizedFiletypeException
     */
    public function cutPartial(int $x, int $y, int $width, int $height) : ImageWrapper {
        $cutImage = new ImageWrapper($width, $height);
        imagecopy($cutImage->image->getResource(), $this->image->getResource(), 0, 0, $x, $y, $width, $height);
        $this->image->loadResource($cutImage->image->getResource());
        return $this;
    }

    /**
     * Crops image to given
     *
     * @param int $x
     * @param int $y
     * @param int $width
     * @param int $height
     * @return $this
     */
    public function crop(int $x, int $y, int $width, int $height) : ImageWrapper {
        imagecrop($this->image->getResource(), [$x, $y, $width, $height]);
        return $this;
    }

    /**
     * Draws a line onto the image
     *
     * @param int $srcX
     * @param int $srcY
     * @param int $destX
     * @param int $destY
     * @param int $color
     * @return $this
     */
    public function drawDashedLine(int $srcX, int $srcY, int $destX, int $destY, Color $color) : ImageWrapper {
        imageline($this->image->getResource(), $srcX, $srcY, $destX, $destY, $this->allocateColor($color));
        return $this;
    }

    /**
     * Draws an ellipse. If $fill isp passed, the ellipse will be filled with the passed color.
     *
     * @param int $x
     * @param int $y
     * @param int $width
     * @param int $height
     * @param int $color
     * @return $this
     */
    public function drawEllipse(int $x , int $y , int $width , int $height , Color $color, bool $fill = false) : ImageWrapper {
        if($fill) {
            imagefilledellipse($this->image->getResource(), $x, $y, $width, $height, $this->allocateColor($color));
        } else {
            imageellipse($this->image->getResource(), $x, $y, $width, $height, $this->allocateColor($color));
        }
        return $this;
    }

    /**
     * Performs a flood fill onto the image
     *
     * @param int $x
     * @param int $y
     * @param int $color
     * @return $this
     */
    public function fill(int $x, int $y, Color $color) : ImageWrapper {
        imagefill($this->image->getResource(), $x, $y, $this->allocateColor($color));
        return $this;
    }


    /**
     * Draws a polygon. If $fill is passed, the polygon will be filled with the passed color.
     *
     * @param array $points
     * @param int $numberOfPoints
     * @param int $color
     * @param bool $fill
     * @return $this
     */
    public function drawPolygon(array $points, int $numberOfPoints, Color $color, bool $fill = false) : ImageWrapper {
        if($fill) {
            imagefilledpolygon($this->image->getResource(), $points, $numberOfPoints, $this->allocateColor($color));
        } else {
            imagepolygon($this->image->getResource(), $points, $numberOfPoints, $this->allocateColor($color));
        }
        return $this;
    }

    /**
     * Draws a rectangle. If $fill is passed, the rectangle will be filled with the given color.
     *
     * @param int $topLeft
     * @param int $topRight
     * @param int $bottomLeft
     * @param int $bottomRight
     * @param int $color
     * @param bool $fill
     */
    public function drawRectangle(int $topLeft, int $topRight, int $bottomLeft, int $bottomRight, Color $color, bool $fill = false) {
        if($fill) {
            imagefilledrectangle($this->image->getResource(), $topLeft, $topRight, $bottomLeft, $bottomRight, $this->allocateColor($color));
        } else {
            imagerectangle($this->image->getResource(), $topLeft, $topRight, $bottomLeft, $bottomRight, $this->allocateColor($color));
        }
    }

    /**
     * Inverts the color of an image (white to black, black to white etc)
     *
     * @param int $filterType
     * @return ImageWrapper
     */
    public function invertColors() : ImageWrapper {
        imagefilter($this->image->getResource(), IMG_FILTER_NEGATE);
        return $this;
    }

    /**
     * Convert image into a grayscale image. Alpha components are retained.
     *
     * @return $this
     */
    public function grayscaleImage() : ImageWrapper {
        imagefilter($this->image->getResource(), IMG_FILTER_GRAYSCALE);
        return $this;
    }

    /**
     * Set brightness of given image
     *
     * @param int $brightness
     * @return ImageWrapper
     */
    public function setBrightness(int $brightness) : ImageWrapper {
        $brightness = $brightness > 255 ? 255 : $brightness;
        $brightness = $brightness < -255 ? -255 : $brightness;
        imagefilter($this->image->getResource(), IMG_FILTER_BRIGHTNESS, $brightness);
        return $this;
    }

    /**
     * Sets contrast of the given image
     *
     * @param int $contrast
     * @return ImageWrapper
     */
    public function setContrast(int $contrast) : ImageWrapper {
        imagefilter($this->image->getResource(), IMG_FILTER_CONTRAST, $contrast);
        return $this;
    }

    /**
     * Colorizes an image
     *
     * @param int $red
     * @param int $green
     * @param int $blue
     * @param int $alpha
     * @return ImageWrapper
     */
    public function colorize(Color $color) : ImageWrapper {
        $red = $color->getRed();
        $green = $color->getGreen();
        $blue = $color->getBlue();
        $alpha = $color->getAlpha();

        $alpha = $alpha > 127 ? 127 : $alpha;
        $alpha = $alpha < 0 ? 0 : $alpha;
        $red = $red > 255 ? 255 : $red;
        $red = $red < 0 ? 0 : $red;
        $green = $green > 255 ? 255 : $green;
        $green = $green < 0 ? 0 : $green;
        $blue = $blue > 255 ? 255 : $blue;
        $blue = $blue < 0 ? 0 : $blue;

        imagefilter($this->image->getResource(), IMG_FILTER_COLORIZE, $red, $green, $blue, $alpha);

        return $this;
    }

    /**
     * Uses edge detection to highlight the edges in the image.
     *
     * @return ImageWrapper
     */
    public function edgedetect() {
        imagefilter($this->image->getResource(), IMG_FILTER_EDGEDETECT);
        return $this;
    }

    /**
     * Embosses the image
     *
     * @return ImageWrapper
     */
    public function emboss() : ImageWrapper {
        imagefilter($this->image->getResource(), IMG_FILTER_EMBOSS);
        return $this;
    }

    /**
     * Blurs an image. If $useGauss is passed, the gauss blur will be used instead of the selective blur
     *
     * @param bool $useGauss
     * @return ImageWrapper
     */
    public function blur(bool $useGauss = false) : ImageWrapper {
        if($useGauss) {
            imagefilter($this->image->getResource(), IMG_FILTER_GAUSSIAN_BLUR);
        } else {
            imagefilter($this->image->getResource(), IMG_FILTER_SELECTIVE_BLUR);
        }

        return $this;
    }

    /**
     *  TODO: Figure out what this "sketchy" effect is
     */
    public function sketchImage() : ImageWrapper {
        imagefilter($this->image->getResource(), IMG_FILTER_MEAN_REMOVAL);
        return $this;
    }

    /**
     * @param int $smoothness
     * @return ImageWrapper
     */
    public function smooth(int $smoothness) : ImageWrapper {
        imagefilter($this->image->getResource(), IMG_FILTER_SMOOTH, $smoothness);
        return $this;
    }

    /**
     * Pixelates an image.
     *
     * @param int $blockSize
     * @param bool $advancedPixelationMode
     * @return ImageWrapper
     */
    public function pixelate(int $blockSize, bool $advancedPixelationMode = false) : ImageWrapper {
        imagefilter($this->image->getResource(), $blockSize, $advancedPixelationMode);
        return $this;
    }

    /**
     * Applies a scatter effect to the image.
     *
     * @param int $effectSubstractionLevel
     * @param int $effectAdditionLevel
     * @param array $onlyApplyTo
     * @return ImageWrapper
     */
    public function scatter(int $effectSubstractionLevel, int $effectAdditionLevel, array $onlyApplyTo = []) : ImageWrapper {
        if($effectSubstractionLevel >= $effectAdditionLevel) {
            //@TODO: Throw exception
        }

        imagefilter($this->image->getResource(), $effectSubstractionLevel, $effectAdditionLevel, $onlyApplyTo);

        return $this;
    }

    /**
     * @param float $inputGamma
     * @param float $outputGamma
     * @return ImageWrapper
     */
    public function gammaCorrection(float $inputGamma, float $outputGamma) : ImageWrapper {
        imagefilter($this->image->getResource(), $inputGamma, $outputGamma);
        return $this;
    }

    /**
     * Flips the image using the given mode
     *
     * @param int $mode
     * @return $this
     */
    public function flip(int $mode) : ImageWrapper {
        imageflip($this->image->getResource(), $mode);
        return $this;
    }

    /**
     * Set the interlace mode
     *
     * @param bool $interlaceMode
     * @return $this
     */
    public function setInterlace(bool $interlaceMode) : ImageWrapper {
        imageinterlace($interlaceMode);
        return $this;
    }

    /**
     * Determines if an image is a true color image
     * @return bool
     */
    public function isTrueColor() : bool {
        return imageistruecolor($this->image->getResource());
    }

    public function allocateColor(Color $color) : false|int {
        $allocatedColor = imagecolorallocatealpha($this->image->getResource(), $color->getRed(), $color->getGreen(), $color->getBlue(), $color->getAlpha());

        if($allocatedColor === false) {
            throw new ColorCouldNotBeAllocatedException("The passed color object could not be allocated. Please check the parameters.");
        }

        $this->colorPallet[] = $allocatedColor;
        return $allocatedColor;
    }
}