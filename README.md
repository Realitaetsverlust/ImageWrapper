# ImageWrapper
### A simple wrapper for image in PHP

ImageWrapper is one of my simplistic OOP Wrappers for PHP. My goal with this project was to create a library which is lightweight, has little to no dependencies and requires little to no explanation on how it works. And I think that I definitely succeeded. Image wrapper has no dependencies, is not very complex and extremely simple to integrate in existing or new web applications.

Keep in mind that this software is still in development. It's currently in a state that is absolutely useable in productive environments, but it may have occasional bugs here and there, especially with older GDLb versions. If you notice a bug, please leave an issue, stating exactly the PHP Version and GDLib version you're using.

#### What can ImageWrapper do for you:

- Load, edit, convert and output images of any kind with ease
- Provide a nice to use API for all filetypes
- No fancy stuff: Every method maps to one, sometimes two PHP functions. Very straighforward

For example: You want to load an image called `test.png`, convert it into a JPEG file and output it:

```php
use Realitaetsverlust\Wrapper\ImageTypes\Jpeg;
use Realitaetsverlust\Wrapper\ImageTypes\Png;

$image = new Png('test.png');
$image->convert(new Jpeg());
$image->output('test.jpeg');
```

Tadaa, that's all necessary to convert an image from PNG to JPEG. If you prefer it a little bit fancier, you can also make use of the method chaining ImageWrapper provides:

```php
use Realitaetsverlust\Wrapper\ImageTypes\Jpeg;
use Realitaetsverlust\Wrapper\ImageTypes\Png;

$image = new Png('test.png');
$image->convert(new Jpeg())->output('test.jpeg');
```

Almost every method returns itself in the end, with a few exceptions. So you can chain methods as often and as much as you like!

Also, if you don't know which kind of image you have there or you simply don't want to bother, no worries, ImageWrapper got your back. There is `ImageFactory::create()` that automatically checks the filetype using `exif_imagetype()` and returns the corresponding object:

```php
use Realitaetsverlust\Wrapper\ImageFactory;

$image = ImageFactory::create('test.png'); // Will return a Png Object
$image = ImageFactory::create('test.jpg'); // Will return a Jpeg Object
$image = ImageFactory::create('image_with_no_extension'); // Will return the correct object, whatever it is
```

ImageWrapper has support for all image types PHP supports, which are:

- .png
- .jpeg
- .gif
- .webp
- .bmp
- .wbmp
- .xbm
- .xpm (only read, no write)

### The elements of the library:

#### ImageBase:

`ImageBase` is the base class of all images. It's an abstract class that contains definitions for all methods. Every method maps to one PHP function, sometimes two if the two methods are very similar. A full documentation of methods inside `ImageBase` is below

#### Color:

The `Color()` class is a RGB representation which is used throughout the the library. The main advantage of it is that it can convert RGB values as well as a hex-based string (#ff00ff) and even CMYK. 

Every time a function expects a color represenation, you should pass a Color object. This may look like the following:

```php
$image->colorize(new \Realitaetsverlust\Wrapper\Color(255, 0, 0));
```

This will colorize the image red. However, you can, as mentioned above, also add other values that represent a color:

```php
$image->colorize(new \Realitaetsverlust\Wrapper\Color("#FF0000")); // Hex
$image->colorize(new \Realitaetsverlust\Wrapper\Color(0, 100, 100, 0)); // CMYK
```

Usually, you never have to care about the allocation of the color, the picture class handles those by itself. However, the method `allocateColor()` allows you to perform the allocation of colors yourself.

### Method-Documentation:

```php
public function output(string $destination = null, ?): bool 
```

Outputs the image. If `$destination` is set, the image is saved at the given path. If it is not, it's directly send as output.

This method is ont inside `ImageBase`, but inside the child classes. PHPs output functions are not very streamlined which makes it really hard to write a proper function for all of them.

```php
public function convert(ImageBase $imageType): ImageBase
```

Converts an image from one image type to another. Please keep in mind that this function returns a new object you have to use. So something like the following will not work.
```php
$image = new Png('test.png');
$image->convert(new Jpeg());
$image->output('test.jpeg');
```

Chaining the methods, however, will obviously work.

```php
public function sendHeaders() : void
```

This method sends the headers as a browser would expect them in the format `Content-Type: image/png`. They are determined by `image_type_to_mime_type()`. In general, you don't have to call this function yourself, it is called automatically if the method `output()` does not get any `$destination` parameter. However, there may be a situation where you want to fire the function yourself, for example if you want to set some parameters before you send the image to the receiver.

```php
public function getFileType(): string
```

Returns the current file type of the image. This is not determined by the extension, but by `exif_imagetype()`

```php
public function getImageSize(): array 
```

Returns the file size of the image. 

```php
public function getMinQuality(): int 
```

Returns the minimum quality PHP accepts in the output function. 

```php
public function getMaxQuality(): int 
```

Returns the maximum quality PHP accepts in the output function.

```php
public function alphaBlending(bool $blendmode): ImageBase
```

Set the blending mode for the image

```php
public function enableAntialias(bool $enabled): ImageBase
```

Activate the fast drawing antialiased methods for lines and wired polygons.

```php
public function drawArc(int $cx, int $cy, int $width, int $height, int $start, int $end, Color $color, int $style = null): ImageBase
```

Draws an arc. if $style is passed, the arc will be filled

```php
public function drawChar(bool $drawVertical, int $font, int $x, int $y, string $c, int $color): ImageBase
```

Draws a char horizontally or vertically onto the image.

```php
public function getColoarAt(int $x, int $y): int|false 
```

Fetches the color value at a specific location

```php
public function cutPartial(int $x, int $y, int $width, int $height): ImageBase
```

Cuts a part from the current image and copies it into a new image

```php
public function crop(int $x, int $y, int $width, int $height): ImageBase
```

Crops image to given dimension

```php
public function drawDashedLine(int $srcX, int $srcY, int $destX, int $destY, Color $color): ImageBase
```

Draws a line onto the image

```php
public function drawEllipse(int $x, int $y, int $width, int $height, Color $color, bool $fill = false): ImageBase
```

Draws an ellipse. If $fill isp passed, the ellipse will be filled with the passed color.

```php
public function fill(int $x, int $y, Color $color): ImageBase
```

Performs a flood fill onto the image

```php
public function drawPolygon(array $points, int $numberOfPoints, Color $color, bool $fill = false): ImageBase
```

Draws a polygon. If $fill is passed, the polygon will be filled with the passed color.

```php
public function drawRectangle(int $topLeft, int $topRight, int $bottomLeft, int $bottomRight, Color $color, bool $fill = false)
```

Draws a rectangle. If $fill is passed, the rectangle will be filled with the given color.

```php
public function invertColors(): ImageBase
```

Inverts the color of an image (white to black, black to white etc)

```php
public function grayscaleImage(): ImageBase
```

Convert image into a grayscale image. Alpha components are retained.

```php
public function setBrightness(int $brightness): ImageBase
```

Set brightness of given image

```php
public function setContrast(int $contrast): ImageBase
```

Sets contrast of the given image

```php
public function colorize(Color $color): ImageBase
```

Colorizes an image

```php
public function edgedetect(): ImageBase
```

Uses edge detection to highlight the edges in the image.

```php
public function emboss(): ImageBase
```

Embosses the image

```php
public function blur(bool $useGauss = false): ImageBase
```

Blurs an image. If $useGauss is passed, the gauss blur will be used instead of the selective blur

```php
public function sketchImage(): ImageBase
```

Transforms the image to have a sketchy look

```php
public function smooth(int $smoothness): ImageBase
```

Smoothes the iamge

```php
public function pixelate(int $blockSize, bool $advancedPixelationMode = false): ImageBase
```

Pixelates the iamge

```php
public function scatter(int $effectSubstractionLevel, int $effectAdditionLevel, array $onlyApplyTo = []): ImageBase
```

Applies a scatter effect to the image.

```php
public function gammaCorrection(float $inputGamma, float $outputGamma): ImageBase
```

Gamma correction method

```php
public function flip(int $mode): ImageBase
```

Flips the image using the given mode. Modes are IMG_FLIP_HORIZONTAL, IMG_FLIP_VERTICAL and IMG_FLIP_BOTH

```php
public function setInterlace(bool $interlaceMode): ImageBase
```

Enables/Disables interlace

```php
public function isTrueColor(): bool
```

Determines if an image is a true color image

```php
public function allocateColor(Color $color): false|int
```

Allocates a color and adds it to the list of allocated colors.


