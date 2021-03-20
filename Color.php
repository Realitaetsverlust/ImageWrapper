<?php

namespace Realitaetsverlust\Wrapper;

class Color {
    protected int $redComponent;
    protected int $greenComponent;
    protected int $blueComponent;
    protected int $alpha;

    /**
     * Color constructor. Accepts three different inputs:
     *
     * 1. A string only. The string has to be in the hex based color format with 6 characters.
     * 2. RGB. In this case, $value1 is the red component, $value2 is the green component, and $value3 is the blue component. Min value is 0, max value is 255
     * 3. CMYK. In this case, $value1 is Cyan, $value2 is magenta, $value3 is yellow and $value4 is key (black) Min value is 0, max value is 100
     *
     * Alpha can be set in everyy version, the minimum value is 0 for completely solid up to 127 for completely transparent
     *
     * Not the cleanest way to do it. It probably would be better if I'd just create a new color object and then call
     * independent methods of filling it. However, I think that in terms of usability, this is the best solution because
     * it saves an additional function call.
     *
     * @param string|int $value1
     * @param int|null $value2
     * @param int|null $value3
     * @param int|null $value4
     * @param int $alpha
     * @throws \Exception
     */
    public function __construct(string|int $value1, int $value2 = null, int $value3 = null, int $value4 = null, int $alpha = 0) {
        $this->alpha = $alpha;
        // Origin is a hex string in the format #ffffff
        if(is_string($value1)) {
            if(preg_match("/^#([A-Fa-f0-9]{6})$/", $value1)) {
                $this->convertHex($value1);
                return;
            } else {
                throw new \Exception("Hex-Based color formats must be 6 characters long, prefixed with #");
            }
        }

        // Origin is CMYK
        if(isset($value4)) {
            $this->convertCmyk($value1, $value2, $value3, $value4);
            return;
        }

        // Origin is RGB
        $this->readRgb($value1, $value2, $value3);
    }

    /**
     * Converts a hexcode color value to RGB
     *
     * @param string $hexString
     */
    private function convertHex(string $hexString) : void {
        $color= sscanf($hexString, "#%2x%2x%2x");
        $this->readRgb($color[0], $color[1], $color[2]);
    }

    /**
     * Converts a CMYK color value to RGB. If a value is smaller than 0, it will be automatically set to 0. If a value is higher than 100, the value will be automatically set to 100
     *
     * @param int $cyan
     * @param int $magenta
     * @param int $yellow
     * @param int $black
     */
    private function convertCmyk(int $cyan, int $magenta, int $yellow, int $black) : void {
        $cyan = $cyan > 100 ? 100 : $cyan;
        $cyan = $cyan < 0 ? 0 : $cyan;
        $magenta = $magenta > 100 ? 100 : $magenta;
        $magenta = $magenta < 0 ? 0 : $magenta;
        $yellow = $yellow > 100 ? 100 : $yellow;
        $yellow = $yellow < 0 ? 0 : $yellow;
        $black = $black > 100 ? 100 : $black;
        $black = $black < 0 ? 0 : $black;

        $this->readRgb(ceil(255 * (1 - $cyan / 100) * (1 - $black / 100)), ceil(255 * (1 - $magenta / 100) * (1 - $black / 100)), ceil(255 * (1 - $yellow / 100) * (1 - $black / 100)));
    }

    /**
     * Reads RGB values into the object.
     *
     * @param int $red
     * @param int $green
     * @param int $blue
     */
    private function readRgb(int $red, int $green, int $blue) : void {
        $red = $red > 255 ? 255 : $red;
        $red = $red < 0 ? 0 : $red;
        $green = $green > 255 ? 255 : $green;
        $green = $green < 0 ? 0 : $green;
        $blue = $blue > 255 ? 255 : $blue;
        $blue = $blue < 0 ? 0 : $blue;

        $this->redComponent = $red;
        $this->greenComponent = $green;
        $this->blueComponent = $blue;
    }

    public function getRed() : int {
        return $this->redComponent;
    }

    public function getGreen() : int {
        return $this->greenComponent;
    }

    public function getBlue() : int {
        return $this->blueComponent;
    }

    public function getAlpha() : int {
        return $this->alpha;
    }
}