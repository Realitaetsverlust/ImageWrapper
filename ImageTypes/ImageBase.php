<?php

namespace Realitaetsverlust\Wrapper\ImageTypes;

use GdImage;
use Realitaetsverlust\Wrapper\Color;
use Realitaetsverlust\Wrapper\Exception\ColorCouldNotBeAllocatedException;
use Realitaetsverlust\Wrapper\Exception\InvalidQualitySettingException;
use Realitaetsverlust\Wrapper\Exception\UnableToLoadImageException;
use Realitaetsverlust\Wrapper\Exception\UnrecognizedFiletypeException;

abstract class ImageBase {
    /**
     * The currently used image resource
     * @var GdImage
     */
    protected GdImage $resource;

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
     * Sets the minimum quality for an image
     * @var int
     */
    protected int $minQuality;

    /**
     * Sets the maximum quality for an image
     * @var int
     */
    protected int $maxQuality;

    /**
     * All colors that have been allocated by allocateColor()
     *
     * @var array
     */
    public array $colorPallet = [];

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
            $imageResource = call_user_func($this->inputFunction, $imagePath);

            if($imageResource === false) {
                throw new UnableToLoadImageException($this->inputFunction);
            }
            $this->resource = $imageResource;
            $this->imageSize = getimagesize($imagePath);
        }
    }

    public function convert(ImageBase $imageType): ImageBase {
        $desiredType = new $imageType;
        $desiredType->resource = $this->resource;
        $desiredType->colorPallet = $this->colorPallet;
        return $desiredType;
    }

    /**
     * Writes the currently used resource to a stream and returns the handle.
     *
     * Currently not used, but I thought it's fancy so I kept it
     *
     * @return false|resource
     */
    protected function writeImageToMemory() {
        $memory = fopen('php://memory', 'rw');
        ob_start();
        imagepng($this->output());
        $imageString = ob_get_contents();
        fputs($memory, $imageString);
        rewind($memory);
        return $memory;
    }

    /**
     * Reads the file from the given file descriptor
     *
     * Currently not used, but I thought it's fancy so I kept it
     *
     * @param $memory
     * @return false|GdImage
     */
    protected function readImageFromMemory($memory) {
        while($string = fgets($memory))
        {
            $output .= $string;
        }

        return imagecreatefromstring($output);
    }

    /**
     * Validates the quality against the set parameters of the class
     *
     * @param int $quality
     * @return bool
     * @throws InvalidQualitySettingException
     */
    protected function validateQuality(int $quality): bool {
        if($quality < $this->minQuality || $quality > $this->maxQuality) {
            throw new InvalidQualitySettingException($quality, $this->minQuality, $this->maxQuality);
        }

        return true;
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
    public function getImageSize(): array {
        return $this->imageSize;
    }

    public function getMinQuality(): int {
        return $this->minQuality;
    }

    public function getMaxQuality(): int {
        return $this->maxQuality;
    }

    # Imagefunctions wrapping starts here

    /**
     * Set the blending mode for the image
     *
     * @param bool $blendmode
     * @return ImageBase $this
     */
    public function alphaBlending(bool $blendmode): ImageBase {
        imagealphablending($this->resource, $blendmode);
        return $this;
    }

    /**
     *  Activate the fast drawing antialiased methods for lines and wired polygons.
     *
     * @param bool $enabled
     * @return ImageBase $this
     */
    public function enableAntialias(bool $enabled): ImageBase {
        imageantialias($this->resource, $enabled);
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
     * @return ImageBase $this
     */
    public function drawArc(int $cx, int $cy, int $width, int $height, int $start, int $end, Color $color, int $style = null): ImageBase {
        $allocatedColor = $this->allocateColor($color);
        if($style) {
            imagefilledarc($this->resource, $cx, $cy, $width, $height, $start, $end, $allocatedColor, $style);
        } else {
            imagearc($this->resource, $cx, $cy, $width, $height, $start, $end, $allocatedColor);
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
     * @return ImageBase $this
     */
    public function drawChar(bool $drawVertical, int $font, int $x, int $y, string $c, int $color): ImageBase {
        if($drawVertical) {
            imagecharup($this->resource, $font, $x, $y, $c, $color);
        } else {
            imagechar($this->resource, $font, $x, $y, $c, $color);
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
    public function getColoarAt(int $x, int $y): int|false {
        return imagecolorat($this->resource, $x, $y);
    }

    /**
     * Cuts a part from the current image and copies it into a new image
     *
     * @param int $x
     * @param int $y
     * @param int $width
     * @param int $height
     * @return ImageBase $this
     * @throws UnrecognizedFiletypeException
     */
    public function cutPartial(int $x, int $y, int $width, int $height): ImageBase {
        $cutImage = new Blank($width, $height);
        imagecopy($cutImage->image->getResource(), $this->resource, 0, 0, $x, $y, $width, $height);
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
    public function crop(int $x, int $y, int $width, int $height): ImageBase {
        imagecrop($this->resource, [$x, $y, $width, $height]);
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
    public function drawDashedLine(int $srcX, int $srcY, int $destX, int $destY, Color $color): ImageBase {
        imageline($this->resource, $srcX, $srcY, $destX, $destY, $this->allocateColor($color));
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
    public function drawEllipse(int $x, int $y, int $width, int $height, Color $color, bool $fill = false): ImageBase {
        if($fill) {
            imagefilledellipse($this->resource, $x, $y, $width, $height, $this->allocateColor($color));
        } else {
            imageellipse($this->resource, $x, $y, $width, $height, $this->allocateColor($color));
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
    public function fill(int $x, int $y, Color $color): ImageBase {
        imagefill($this->resource, $x, $y, $this->allocateColor($color));
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
    public function drawPolygon(array $points, int $numberOfPoints, Color $color, bool $fill = false): ImageBase {
        if($fill) {
            imagefilledpolygon($this->resource, $points, $numberOfPoints, $this->allocateColor($color));
        } else {
            imagepolygon($this->resource, $points, $numberOfPoints, $this->allocateColor($color));
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
            imagefilledrectangle($this->resource, $topLeft, $topRight, $bottomLeft, $bottomRight, $this->allocateColor($color));
        } else {
            imagerectangle($this->resource, $topLeft, $topRight, $bottomLeft, $bottomRight, $this->allocateColor($color));
        }
    }

    /**
     * Inverts the color of an image (white to black, black to white etc)
     *
     * @param int $filterType
     * @return ImageBase
     */
    public function invertColors(): ImageBase {
        imagefilter($this->resource, IMG_FILTER_NEGATE);
        return $this;
    }

    /**
     * Convert image into a grayscale image. Alpha components are retained.
     *
     * @return $this
     */
    public function grayscaleImage(): ImageBase {
        imagefilter($this->resource, IMG_FILTER_GRAYSCALE);
        return $this;
    }

    /**
     * Set brightness of given image
     *
     * @param int $brightness
     * @return ImageBase
     */
    public function setBrightness(int $brightness): ImageBase {
        $brightness = $brightness > 255 ? 255 : $brightness;
        $brightness = $brightness < -255 ? -255 : $brightness;
        imagefilter($this->resource, IMG_FILTER_BRIGHTNESS, $brightness);
        return $this;
    }

    /**
     * Sets contrast of the given image
     *
     * @param int $contrast
     * @return ImageBase
     */
    public function setContrast(int $contrast): ImageBase {
        imagefilter($this->resource, IMG_FILTER_CONTRAST, $contrast);
        return $this;
    }

    /**
     * Colorizes an image
     *
     * @param int $red
     * @param int $green
     * @param int $blue
     * @param int $alpha
     * @return ImageBase
     */
    public function colorize(Color $color): ImageBase {
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

        imagefilter($this->resource, IMG_FILTER_COLORIZE, $red, $green, $blue, $alpha);

        return $this;
    }

    /**
     * Uses edge detection to highlight the edges in the image.
     *
     * @return ImageBase
     */
    public function edgedetect() {
        imagefilter($this->resource, IMG_FILTER_EDGEDETECT);
        return $this;
    }

    /**
     * Embosses the image
     *
     * @return ImageBase
     */
    public function emboss(): ImageBase {
        imagefilter($this->resource, IMG_FILTER_EMBOSS);
        return $this;
    }

    /**
     * Blurs an image. If $useGauss is passed, the gauss blur will be used instead of the selective blur
     *
     * @param bool $useGauss
     * @return ImageBase
     */
    public function blur(bool $useGauss = false): ImageBase {
        if($useGauss) {
            imagefilter($this->resource, IMG_FILTER_GAUSSIAN_BLUR);
        } else {
            imagefilter($this->resource, IMG_FILTER_SELECTIVE_BLUR);
        }

        return $this;
    }

    /**
     *  TODO: Figure out what this "sketchy" effect is
     */
    public function sketchImage(): ImageBase {
        imagefilter($this->resource, IMG_FILTER_MEAN_REMOVAL);
        return $this;
    }

    /**
     * @param int $smoothness
     * @return ImageBase
     */
    public function smooth(int $smoothness): ImageBase {
        imagefilter($this->resource, IMG_FILTER_SMOOTH, $smoothness);
        return $this;
    }

    /**
     * Pixelates an image.
     *
     * @param int $blockSize
     * @param bool $advancedPixelationMode
     * @return ImageBase
     */
    public function pixelate(int $blockSize, bool $advancedPixelationMode = false): ImageBase {
        imagefilter($this->resource, $blockSize, $advancedPixelationMode);
        return $this;
    }

    /**
     * Applies a scatter effect to the image.
     *
     * @param int $effectSubstractionLevel
     * @param int $effectAdditionLevel
     * @param array $onlyApplyTo
     * @return ImageBase
     */
    public function scatter(int $effectSubstractionLevel, int $effectAdditionLevel, array $onlyApplyTo = []): ImageBase {
        if($effectSubstractionLevel >= $effectAdditionLevel) {
            //@TODO: Throw exception
        }

        imagefilter($this->resource, $effectSubstractionLevel, $effectAdditionLevel, $onlyApplyTo);

        return $this;
    }

    /**
     * @param float $inputGamma
     * @param float $outputGamma
     * @return ImageBase
     */
    public function gammaCorrection(float $inputGamma, float $outputGamma): ImageBase {
        imagefilter($this->resource, $inputGamma, $outputGamma);
        return $this;
    }

    /**
     * Flips the image using the given mode
     *
     * @param int $mode
     * @return $this
     */
    public function flip(int $mode): ImageBase {
        imageflip($this->resource, $mode);
        return $this;
    }

    /**
     * Set the interlace mode
     *
     * @param bool $interlaceMode
     * @return $this
     */
    public function setInterlace(bool $interlaceMode): ImageBase {
        imageinterlace($this->resource, $interlaceMode);
        return $this;
    }

    /**
     * Determines if an image is a true color image
     * @return bool
     */
    public function isTrueColor(): bool {
        return imageistruecolor($this->resource);
    }

    /**
     * Allocates a color and adds it to the list of allocated colors.
     *
     * @param Color $color
     * @return false|int
     * @throws ColorCouldNotBeAllocatedException
     */
    public function allocateColor(Color $color): false|int {
        $allocatedColor = imagecolorallocatealpha($this->resource, $color->getRed(), $color->getGreen(), $color->getBlue(), $color->getAlpha());

        if($allocatedColor === false) {
            throw new ColorCouldNotBeAllocatedException();
        }

        $this->colorPallet[] = $allocatedColor;
        return $allocatedColor;
    }

}