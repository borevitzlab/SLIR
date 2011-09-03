<?php
abstract class SLIRImage
{
  /**
   * @var string Path to file
   */
  protected $path;

  /**
   * @var string Path to original file
   */
  protected $originalPath;

  /**
   * @var string background color in hex
   */
  protected $background;

  /**
   * @var array information about the image
   */
  protected $info;

  /**
   * Mime types
   * @var array
   * @since 2.0
   */
  private $mimeTypes  = array(
    'JPEG'  => array(
      'image/jpeg'  => 1,
    ),
    'GIF' => array(
      'image/gif'   => 1,
    ),
    'PNG' => array(
      'image/png'   => 1,
      'image/x-png' => 1,
    ),
    'BMP' => array(
      'image/bmp'       => 1,
      'image/x-ms-bmp'  => 1,
    ),
  );

  /**
   * @param string $path
   * @return void
   */
  public function __construct($path = null)
  {
    if ($path !== null) {
      $this->setPath($path);
      $this->setOriginalPath($path);
    }
  }

  /**
   * @return void
   */
  public function __destruct()
  {
    unset(
        $this->path,
        $this->originalPath,
        $this->info
    );
  }

  /**
   * Sets the path of the file
   * @param string $path
   * @return SLIRImageLibrary
   * @since 2.0
   */
  final public function setPath($path)
  {
    $this->path = $path;
    return $this;
  }

  /**
   * Gets the path of the file
   * @return string
   * @since 2.0
   */
  final public function getPath()
  {
    return $this->path;
  }

  /**
   * @return string
   * @since 2.0
   */
  final public function getFullPath()
  {
    return SLIRConfig::$documentRoot . $this->getPath();
  }

  /**
   * Sets the path of the original file
   * @param string $path
   * @return SLIRImageLibrary
   * @since 2.0
   */
  final public function setOriginalPath($path)
  {
    $this->originalPath = $path;
    return $this;
  }

  /**
   * Gets the path of the original file
   * @return string
   * @since 2.0
   */
  final public function getOriginalPath()
  {
    return $this->originalPath;
  }

  /**
   * @return string
   * @since 2.0
   */
  public function getBackground()
  {
    return $this->background;
  }

  /**
   * @param string $color in hex
   * @return SLIRImageLibrary
   */
  public function setBackground($color)
  {
    $this->background = $color;
    return $this;
  }

  /**
   * Checks the mime type to see if it is an image
   *
   * @since 2.0
   * @return boolean
   */
  final public function isImage()
  {
    if (substr($this->getMimeType(), 0, 6) == 'image/') {
      return true;
    } else {
      return false;
    }
  }

  /**
   * @since 2.0
   * @param string $type Can be 'JPEG', 'GIF', 'PNG', or 'BMP'
   * @return boolean
   */
  final public function isOfType($type = 'JPEG')
  {
    if (isset($this->mimeTypes[$type][$this->getMimeType()])) {
      return true;
    } else {
      return false;
    }
  }

  /**
   * @since 2.0
   * @return boolean
   */
  final public function isJPEG()
  {
    return $this->isOfType('JPEG');
  }

  /**
   * @since 2.0
   * @return boolean
   */
  final public function isGIF()
  {
    return $this->isOfType('GIF');
  }

  /**
   * @since 2.0
   * @return boolean
   */
  final public function isBMP()
  {
    return $this->isOfType('BMP');
  }

  /**
   * @since 2.0
   * @return boolean
   */
  final public function isPNG()
  {
    return $this->isOfType('PNG');
  }

  /**
   * @since 2.0
   * @return boolean
   */
  final public function isAbleToHaveTransparency()
  {
    if ($this->isPNG() || $this->isGIF()) {
      return true;
    } else {
      return false;
    }
  }

  /**
   * @since 2.0
   * @return boolean
   */
  final protected function isSharpeningDesired()
  {
    if ($this->isJPEG()) {
      return true;
    } else {
      return false;
    }
  }

  /**
   * @since 2.0
   * @return integer
   */
  final public function getArea()
  {
    return $this->getWidth() * $this->getHeight();
  }

  /**
   * @since 2.0
   * @return float
   */
  final public function getRatio()
  {
    return $this->getWidth() / $this->getHeight();
  }

  /**
   * @since 2.0
   * @return integer
   */
  final public function getCropWidth()
  {
    return $this->getInfo('cropWidth');
  }

  /**
   * @since 2.0
   * @return integer
   */
  final public function getCropHeight()
  {
    return $this->getInfo('cropHeight');
  }

  /**
   * @since 2.0
   * @param integer $width
   * @return integer
   */
  final public function setCropWidth($width)
  {
    return $this->info['cropWidth'] = $width;
  }

  /**
   * @since 2.0
   * @param integer $height
   * @return integer
   */
  final public function setCropHeight($height)
  {
    return $this->info['cropHeight'] = $height;
  }

  /**
   * Gets the width of the image
   * @return integer
   * @since 2.0
   */
  public function getWidth()
  {
    return (integer) $this->getInfo('width');
  }

  /**
   * Gets the height of the image
   * @return integer
   * @since 2.0
   */
  public function getHeight()
  {
    return (integer) $this->getInfo('height');
  }

  /**
   * @since 2.0
   * @param integer $width
   * @return integer
   */
  final public function setWidth($width)
  {
    return $this->info['width'] = $width;
  }

  /**
   * @since 2.0
   * @param integer $height
   * @return integer
   */
  final public function setHeight($height)
  {
    return $this->info['height'] = $height;
  }

  /**
   * Gets the MIME type of the image
   * @return string
   * @since 2.0
   */
  public function getMimeType()
  {
    return (string) $this->getInfo('mime');
  }

  /**
   * @return integer size of image data
   */
  public function getDatasize()
  {
    return strlen($this->getData());
  }

  /**
   * Turns on transparency for image if no background fill color is
   * specified, otherwise, fills background with specified color
   *
   * @param string $color in hex format
   * @since 2.0
   * @return SLIRImageLibrary
   */
  final public function background($color = null)
  {
    if ($this->isAbleToHaveTransparency()) {
      if (empty($color)) {
        // If this is a GIF or a PNG, we need to set up transparency
        $this->enableTransparency();
      } else {
        // Fill the background with the specified color for matting purposes
        $this->fill($color);
      }
    }

    return $this;
  }

  /**
   * @since 2.0
   * @return boolean
   */
  protected function croppingIsNeeded()
  {
    if ($this->getCropWidth() === null || $this->getCropHeight() === null) {
      return false;
    } else if ($this->getCropWidth() < $this->getWidth() || $this->getCropHeight() < $this->getHeight()) {
      return true;
    } else {
      return false;
    }
  }
}
