<?php

namespace zibo\library\image;

use zibo\library\filesystem\File;
use zibo\library\image\exception\ImageException;
use zibo\library\image\thumbnail\ThumbnailFactory;
use zibo\library\Number;

use \Exception;

/**
 * Image data container with basic manipulations
 */
class Image {

    /**
     * Internal resource of this image
     * @var resource
     */
    protected $resource;

    /**
     * The width of this image
     * @var int
     */
    protected $width;

    /**
     * The height of this image
     * @var int
     */
    protected $height;

    /**
     * Array with the identifiers of the allocated colors
     * @var array
     */
    private $colors;

    /**
     * Construct an image object
     * @param mixed null for a new image, another Image instance for a copy or a filename to read a file from filesystem
     * @param int width width used to create a new image (default 100)
     * @param int height height used to create a new image (default 100)
     * @return null
     */
    public function __construct($image = null, $width = 100, $height = 100) {
        if (!extension_loaded('gd')) {
            throw new ImageException('Could not create a Image instance. Your PHP installation does not support graphic draw, please install the gd extension.');
        }

        if ($image instanceof self) {
            $this->createResource($image->width, $image->height);
            $this->copyResource($image->resource, 0, 0, 0, 0, $this->width, $this->height, $this->width, $this->height);
            return;
        } elseif ($image instanceof File) {
            $this->resource = ImageFactory::getInstance()->read($image);
            return;
        }

        $this->createResource($width, $height);
    }

    /**
     * Free the memory of the image
     * @return null
     */
    public function __destruct() {
        if (!$this->resource) {
            return;
        }

        try {
            imageDestroy($this->resource);
        } catch (Exception $exception) {

        }
    }

    /**
     * Get the width of this Image instance
     * @return int width
     */
    public function getWidth() {
        if (!isset($this->width)) {
            $this->width = imagesX($this->resource);
        }
        return $this->width;
    }

    /**
     * Get the height of this Image instance
     * @return int height
     */
    public function getHeight() {
        if (!isset($this->height)) {
            $this->height = imagesY($this->resource);
        }
        return $this->height;
    }

    /**
     * Gets the dimension of this image
     * @return Dimension
     */
    public function getDimension() {
        return new Dimension($this->getWidth(), $this->getHeight());
    }

    /**
     * Crop this image into a new Image instance
     * @param int x x-coordinate where the crop starts
     * @param int y y-coordinate where the crop starts
     * @param int width width to crop
     * @param int height height to crop
     * @return Image new Image instance with a cropped version of this Image instance
     */
    public function crop($x, $y, $width, $height) {
        if (Number::isNegative($x)) {
            throw new ImageException('Invalid x provided ' . $x);
        }
        if (Number::isNegative($y)) {
            throw new ImageException('Invalid y provided ' . $y);
        }
        if ($x > $this->getWidth()) {
            throw new ImageException('X exceeds the image width');
        }
        if ($y > $this->getHeight()) {
            throw new ImageException('Y exceeds the image height');
        }

        $result = new self(null, $width, $height);
        if ($x + $width > $this->getWidth()) {
            throw new ImageException('X + width exceed the image width');
        }
        if ($y + $height > $this->getHeight()) {
            throw new ImageException('Y + height exceed the image height');
        }

        $result->copyResource($this->resource, 0, 0, $x, $y, $width, $height, $width, $height);

        return $result;
    }

    /**
     * Resize this image into a new Image instance
     * @param int width width of the resulting image
     * @param int height of the resulting image
     * @return Image new Image instance with a resized version of this image
     */
    public function resize($width, $height) {
        $result = new self(null, $width, $height);

        $result->copyResource($this->resource, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());

        return $result;
    }

    /**
     * Rotates this image
     * @param float $degrees Rotation angle, in degrees
     * @param string $uncoveredColor
     * @param boolean $handleTransparancy
     * @return Image new Image instance with a rotated version of this image
     */
    public function rotate($degrees, $uncoveredColor = '#000000', $handleTransparancy = false) {
        $uncoveredColor = $this->allocateColor($uncoveredColor);

        $result = new self($this);

        $result->resource = imageRotate($result->resource, $degrees, $uncoveredColor, $handleTransparancy);

        return $result;
    }

    /**
     * Get a thumbnail for this image
     * @param string name name of the thumbnailer
     * @param int width width to calculate the thumbnail's width
     * @param int height height to calculate the thumbnail's height
     * @return Image Image instance of the thumbnail
     */
    public function thumbnail($name, $width, $height) {
        $thumbnailFactory = ThumbnailFactory::getInstance();
        return $thumbnailFactory->getThumbnail($name, $this, $width, $height);
    }

    /**
     * Draws a line on the image
     * @param Point $p1 Start point of the line
     * @param Point $p2 End point of the line
     * @param Color $color
     * @return null
     */
    public function drawLine(Point $p1, Point $p2, Color $color) {
        $color = $this->allocateColor($color);

        imageLine($this->resource, $p1->getX(), $p1->getY(), $p2->getX(), $p2->getY(), $color);
    }

    /**
     * Draws a polygon on the image
     * @param array $points Array with the vectrices of the polygon
     * @param Color $color The color to draw the polygon in
     * @return null
     */
    public function drawPolygon(array $points, Color $color) {
        $color = $this->allocateColor($color);

        $numPoints = 0;
        $p = array();
        foreach ($points as $point) {
            if (!($point)) {
                throw new ImageException('Provided points array contains a non-Point variable');
            }

            $p[] = $point->getX();
            $p[] = $point->getY();

            $numPoints++;
        }

        imagePolygon($this->resource, $p, $numPoints, $color);
    }

    /**
     * Draws a rectangle on the image
     * @param Point $leftTop Point of the upper left corner
     * @param Dimension $dimension Dimension of the rectangle
     * @param Color $color
     * @param integer $width
     * @return null
     */
    public function drawRectangle(Point $leftTop, Dimension $dimension, Color $color, $width = 1) {
        $color = $this->allocateColor($color);

        $leftTopX = $leftTop->getX();
        $leftTopY = $leftTop->getY();
        $rightBottomX = $leftTopX + $dimension->getWidth();
        $rightBottomY = $leftTopY + $dimension->getHeight();

        for ($i = 1; $i <= $width; $i++) {
            imageRectangle($this->resource, $leftTopX, $leftTopY, $rightBottomX, $rightBottomY, $color);

            $leftTopX++;
            $leftTopY++;
            $rightBottomX--;
            $rightBottomY--;
        }
    }

    /**
     * Fills a rectangle on the image with the provided color
     * @param Point $leftTop
     * @param Dimension $dimension
     * @param Color $color
     * @return null
     */
    public function fillRectangle(Point $leftTop, Dimension $dimension, Color $color) {
        $color = $this->allocateColor($color);

        $leftTopX = $leftTop->getX();
        $leftTopY = $leftTop->getY();
        $rightBottomX = $leftTopX + $dimension->getWidth();
        $rightBottomY = $leftTopY + $dimension->getHeight();

        imageFilledRectangle($this->resource, $leftTopX, $leftTopY, $rightBottomX, $rightBottomY, $color);
    }

    /**
     * Draws a rectangle with rounded corners on the image
     * @param Point $leftTop Point of the upper left corner
     * @param Dimension $dimension Dimension of the rectangle
     * @param integer $cornerSize The number of pixels which should be round of
     * @param Color $color
     * @param integer $width
     * @return null
     */
    public function drawRoundedRectangle(Point $leftTop, Dimension $dimension, $cornerSize, $color) {
        $color = $this->allocateColor($color);

        $x = $leftTop->getX();
        $y = $leftTop->getY();
        $width = $dimension->getWidth();
        $height = $dimension->getHeight();

        $cornerWidth = $cornerSize * 2;
        $innerWidth = $width - $cornerWidth;
        $innerHeight = $height - $cornerWidth;

        // left top
        imageArc($this->resource, $x + $cornerSize, $y + $cornerSize, $cornerWidth, $cornerWidth, 180, 270, $color);

        // top
        imageLine($this->resource, $x + $cornerSize, $y, $x + $width - $cornerSize, $y, $color);

        // right top
        imageArc($this->resource, $x + $width - $cornerSize, $y + $cornerSize, $cornerWidth, $cornerWidth, 270, 360, $color);

        // center
        imageLine($this->resource, $x, $y + $cornerSize, $x, $y + $height - $cornerSize, $color);
        imageLine($this->resource, $x + $width, $y + $cornerSize, $x + $width, $y + $height - $cornerSize, $color);

        // left down
        imageArc($this->resource, $x + $cornerSize, $y + $height - $cornerSize, $cornerWidth, $cornerWidth, 90, 180, $color);

        // down
        imageLine($this->resource, $x + $cornerSize, $y + $height, $x + $width - $cornerSize, $y + $height, $color);

        // right down
        imageArc($this->resource, $x + $width - $cornerSize, $y + $height - $cornerSize, $cornerWidth, $cornerWidth, 0, 90, $color);
    }

    /**
     * Draws a rectangle with rounded corners on the image
     * @param Point $leftTop Point of the upper left corner
     * @param Dimension $dimension Dimension of the rectangle
     * @param integer $cornerSize The number of pixels which should be round of
     * @param Color $color
     * @param integer $width
     * @return null
     */
    public function fillRoundedRectangle(Point $leftTop, Dimension $dimension, $cornerSize, $color) {
        $color = $this->allocateColor($color);

        $x = $leftTop->getX();
        $y = $leftTop->getY();
        $width = $dimension->getWidth();
        $height = $dimension->getHeight();

        $cornerWidth = $cornerSize * 2;
        $innerWidth = $width - $cornerWidth;
        $innerHeight = $height - $cornerWidth;

        // left top
        imageFilledArc($this->resource, $x + $cornerSize, $y + $cornerSize, $cornerWidth, $cornerWidth, 180, 270, $color, IMG_ARC_PIE);

        // top
        imageFilledRectangle($this->resource, $x + $cornerSize, $y, $x + $width - $cornerSize, $y + $cornerSize, $color);

        // right top
        imageFilledArc($this->resource, $x + $width - $cornerSize, $y + $cornerSize, $cornerWidth, $cornerWidth, 270, 360, $color, IMG_ARC_PIE);

        // center
        imageFilledRectangle($this->resource, $x, $y + $cornerSize, $x + $width, $y + $height - $cornerSize, $color);

        // left down
        imageFilledArc($this->resource, $x + $cornerSize, $y + $height - $cornerSize, $cornerWidth, $cornerWidth, 90, 180, $color, IMG_ARC_PIE);

        // down
        imageFilledRectangle($this->resource, $x + $cornerSize, $y + $height - $cornerSize, $x + $width - $cornerSize, $y + $height, $color);

        // right down
        imageFilledArc($this->resource, $x + $width - $cornerSize, $y + $height - $cornerSize, $cornerWidth, $cornerWidth, 0, 90, $color, IMG_ARC_PIE);
    }

    /**
     * Draws a arc  of a circle on the image
     * @param Point $center Point of the circles center
     * @param Dimension $dimension Dimension of the circle
     * @param integer $angleStart 0° is at 3 o'clock and the arc is drawn clockwise
     * @param integer $angleStop
     * @param Color $color
     * @return null
     */
    public function drawArc(Point $center, Dimension $dimension, $angleStart, $angleStop, Color $color) {
        $color = $this->allocateColor($color);

        $x = $center->getX();
        $y = $center->getY();
        $width = $dimension->getWidth();
        $height = $dimension->getHeight();

        imageArc($this->resource, $x, $y, $width, $height, $angleStart, $angleStop, $color);
    }

    /**
     * Fills a arc of a circle on the image
     * @param Point $center Point of the circles center
     * @param Dimension $dimension Dimension of the circle
     * @param integer $angleStart 0° is at 3 o'clock and the arc is drawn clockwise
     * @param integer $angleStop
     * @param Color $color
     * @return null
     */
    public function fillArc(Point $center, Dimension $dimension, $angleStart, $angleStop, Color $color, $type = null) {
        if (!$type) {
            $type = IMG_ARC_PIE;
        }

        $color = $this->allocateColor($color);

        $x = $center->getX();
        $y = $center->getY();
        $width = $dimension->getWidth();
        $height = $dimension->getHeight();

        imageFilledArc($this->resource, $x, $y, $width, $height, $angleStart, $angleStop, $color, $type);
    }

    /**
     * Draws a ellipse on the image
     * @param Point $center Point of the ellipse center
     * @param Dimension $dimension Dimension of the ellipse
     * @param Color $color
     * @return null
     */
    public function drawEllipse(Point $center, Dimension $dimension, Color $color) {
        $color = $this->allocateColor($color);

        $x = $center->getX();
        $y = $center->getY();
        $width = $dimension->getWidth();
        $height = $dimension->getHeight();

        imageEllipse($this->resource, $x, $y, $width, $height, $color);
    }

    /**
     * Fills a ellipse on the image
     * @param Point $center Point of the ellipse center
     * @param Dimension $dimension Dimension of the ellipse
     * @param Color $color
     * @return null
     */
    public function fillEllipse(Point $center, Dimension $dimension, Color $color) {
        $color = $this->allocateColor($color);

        $x = $center->getX();
        $y = $center->getY();
        $width = $dimension->getWidth();
        $height = $dimension->getHeight();

        imageFilledEllipse($this->resource, $x, $y, $width, $height, $color);
    }

    /**
     * Draws text on the image
     * @param Point $leftTop Point of the upper left corner
     * @param Color $color
     * @param string $text
     * @return null
     */
    public function drawText(Point $leftTop, Color $color, $text) {
        $color = $this->allocateColor($color);

        imageString($this->resource, 2, $leftTop->getX(), $leftTop->getY(), $text, $color);
    }

    /**
     * Write this Image instance to file
     * @param File file to write the image to
     * @return null
     */
    public function write(File $file) {
        ImageFactory::getInstance()->write($file, $this->resource);
    }

    /**
     * Create a new internal image resource with the given width and height
     * @param int width width of the new image resource
     * @param int height height of the new image resource
     * @return null
     */
    protected function createResource($width, $height) {
        if (Number::isNegative($width)) {
            throw new ImageException('Invalid width provided ' . $width);
        }
        if (Number::isNegative($height)) {
            throw new ImageException('Invalid height provided ' . $height);
        }

        $this->resource = @imageCreateTrueColor($width, $height);

        if ($this->resource === false) {
            $error = error_get_last();
            throw new ImageException('Could not create the image resource: ' . $error['message']);
        }

        $this->width = $width;
        $this->height = $height;
    }

    /**
     * Copy an existing internal image resource, or part of it, to this Image instance
     * @param resource existing internal image resource as source for the copy
     * @param int x x-coordinate where the copy starts
     * @param int y y-coordinate where the copy starts
     * @param int resourceX starting x coordinate of the source image resource
     * @param int resourceY starting y coordinate of the source image resource
     * @param int width resulting width of the copy (not of the resulting image)
     * @param int height resulting height of the copy (not of the resulting image)
     * @param int resourceWidth width of the source image resource to copy
     * @param int resourceHeight height of the source image resource to copy
     * @return null
     */
    protected function copyResource($resource, $x, $y, $resourceX, $resourceY, $width, $height, $resourceWidth, $resourceHeight) {
        if (!imageCopyResampled($this->resource, $resource, $x, $y, $resourceX, $resourceY, $width, $height, $resourceWidth, $resourceHeight)) {
            if (!imageCopyResized($this->resource, $resource, $x, $y, $resourceX, $resourceY, $width, $height, $resourceWidth, $resourceHeight)) {
                throw new ImageException('Could not copy the image resource');
            }
        }

        $transparent = imageColorAllocate($this->resource, 0, 0, 0);
        imageColorTransparent($this->resource, $transparent);

        $this->width = $width;
        $this->height = $height;
    }

    /**
     * Allocates the color in the image
     * @param Color $color Color definition
     * @return integer identifier of the color
     */
    protected function allocateColor(Color $color) {
        if ($this->colors === null) {
            $this->colors = array();
        }

        $code = $color->__toString();

        if (array_key_exists($code, $this->colors)) {
            return $this->colors[$code];
        }

        $this->colors[$code] = imageColorAllocate($this->resource, $color->getRed(), $color->getGreen(), $color->getBlue());

        return $this->colors[$code];
    }

}