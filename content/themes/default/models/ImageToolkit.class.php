<?php
/* ==================================================

IMGBOARD Copyright 2008–2010 Authorized Clone LLC.

http://authorizedclone.com/
authorizedclone@gmail.com

This file is part of IMGBOARD.

IMGBOARD is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

IMGBOARD is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with IMGBOARD.  If not, see <http://www.gnu.org/licenses/>.

================================================== */
if ( ! defined ( 'IMGBOARD_INIT' ) ) { header ( 'Status: 403', TRUE, 403 ); die( ); }

class ImageToolkit {
	/* CONSTANTS
	================================================== */
	public static $TYPE_GIF = IMAGETYPE_GIF;
	public static $TYPE_JPEG = IMAGETYPE_JPEG;
	public static $TYPE_PNG = IMAGETYPE_PNG;
	
	/* FUNCTIONS
	================================================== */
	
	/**
	 * Load an image from a file name.
	 *
	 * @param string $filename The file name to load.
	 * @return boolean True if the file loading succeeded; false otherwise.
	 */
	public function loadFromFile($filename) {
		// Step 1: Try getting the image information.
		$iptcInfo = array();
		$imageInfo = @getimagesize($filename, $iptcInfo);
		
		// Only continue if the image had successfully loaded.
		if ($imageInfo !== false) {
			// Step 2: Determine the function we should use to load the image.
			$this->_imageHandle = false;
			
			switch ($imageInfo[2]) {
				case self::$TYPE_GIF:
					$this->_imageHandle = @imagecreatefromgif($filename);
					break;
				
				case self::$TYPE_JPEG:
					$this->_imageHandle = @imagecreatefromjpeg($filename);
					break;
				
				case self::$TYPE_PNG:
					$this->_imageHandle = imagecreatefrompng($filename);
					break;
			}
			
			if ($this->_imageHandle !== false) {
				$this->_imageX = $imageInfo[0];
				$this->_imageY = $imageInfo[1];
				$this->_imageType = $imageInfo[2];
				$this->_imageMimeType = $imageInfo['mime'];
			}
		}
		
		// If the image was loaded, return true. Otherwise, return false.
		return ($this->_imageHandle !== false);
	}
	
	
	/**
	 * Calculates a SHA-256 hash for an image data stream.
	 *
	 * @return string The SHA-256 hash, or false upon failure.
	 */
	public function getDataSignature() {
		$hash = false;
		
		if ($this->_imageHandle && file_exists($this->_imageFileName)) {
			$hash = hash('sha512', file_get_contents($this->_imageFileName));
		}
		
		return $hash;
	}
	
	
	/**
	 * Generate a cropped thumbnail for this image.
	 *
	 * @param integer $width The maximum thumbnail width, in pixels.
	 * @param integer $height The maximum thumbnail height, in pixels.
	 * @return boolean True if the thumbnail generation succeeded; false
	 *                 otherwise.
	 */
	public function generateCroppedThumbnail($width, $height) {
		$retval = false;
		$dstratio = $width / $height;
		$srcratio = $this->_imageX / $this->_imageY;
		$min_src = min($this->_imageX, $this->_imageY);
		
		$this->_imageThumbnailHandle = imagecreatetruecolor($width, $height);
		
		if ($this->_imageThumbnailHandle) {
			// Don't bother resizing the image if the thumbnail width and height are
			// equal to the source width and height.
			if ($this->_imageX == $width && $this->_imageY == $height) {
				$retval = imagecopy($this->_imageThumbnailHandle, $this->_imageHandle, 0, 0, 0, 0, $this->_imageX, $this->_imageY);
			} else {
				$start_x = ($min_src == $this->_imageX ? 0 : ($this->_imageY - $this->_imageX) >> 1);
				$start_y = ($min_src == $this->_imageY ? 0 : ($this->_imageX - $this->_imageY) >> 1);
				$retval = imagecopyresampled($this->_imageThumbnailHandle, $this->_imageHandle, 0, 0, $start_x, $start_y, $width, $height, $min_src, $min_src);
			}
		}
		
		return $retval;
	}
	
	
	/**
	 * Generate a thumbnail for this image.
	 *
	 * @param integer $width The maximum thumbnail width, in pixels.
	 * @param integer $height The maximum thumbnail height, in pixels.
	 * @return boolean True if the thumbnail generation succeeded, false
	 *                 otherwise.
	 */
	public function generateThumbnail($width, $height) {
		$retval = false;
		
		$this->_imageThumbnailHandle = imagecreatetruecolor($width, $height);
		
		if ($this->_imageThumbnailHandle) {
			$retval = imagecopyresampled($this->_imageThumbnailHandle, $this->_imageHandle, 0, 0, 0, 0, $width, $height, $this->_imageX, $this->_imageY);
		}
		
		return $retval;
	}
	
	
	/**
	 * Resize the current image.
	 *
	 * @param integer $width The destination width.
	 * @param integer $height The destination height.
	 * @return boolean True if the resizing succeeded; false otherwise.
	 */
	public function resizeImage($width, $height) {
		$retval = false;
		
		$newhandle = imagecreatetruecolor($width, $height);
		
		if ($newhandle) {
			$retval = imagecopyresampled($newhandle, $this->_imageHandle, 0, 0, 0, 0, $width, $height, $this->_imageX, $this->_imageY);
			
			if ($retval) {
				imagedestroy($this->_imageHandle);
				$this->_imageHandle = $newhandle;
			}
		}
		
		return $retval;
	}
	
	
	/**
	 * Convert the current image to grayscale.
	 *
	 * @return boolean True if the conversion succeeded; false otherwise.
	 */
	public function convertToGrayscale() {
		return imagefilter($this->_imageHandle, IMG_FILTER_GRAYSCALE);
	}
	
	
	/**
	 * Convert the thumbnail to grayscale.
	 *
	 * @return boolean True if the conversion succeeded; false otherwise.
	 */
	public function convertThumbnailToGrayscale() {
		return imagefilter($this->_imageThumbnailHandle);
	}
	
	
	/**
	 * Gets the image MIME type.
	 *
	 * @return string The image MIME type, or false upon failure.
	 */
	public function getMimeType() {
		return (strlen($this->_imageMimeType) ? $this->_imageMimeType : false);
	}
	
	
	/**
	 * Gets the image type as a string in uppercase.
	 *
	 * @return string The image type, or false upon failure.
	 */
	public function getTypeAsString() {
		$type = false;
		
		switch ($this->_imageType) {
			case self::$TYPE_GIF:
				$type = 'GIF';
				break;
			
			case self::$TYPE_JPEG:
				$type = 'JPEG';
				break;
			
			case self::$TYPE_PNG:
				$type = 'PNG';
				break;
		}
		
		return $type;
	}
	
	
	/**
	 * Get the image width and height.
	 *
	 * @return array The image width and height in an associative array, or false
	 *               upon failure.
	 */
	public function getDimensions() {
		return ($this->_imageHandle ? array('width' => $this->_imageX, 'height' => $this->_imageY) : false);
	}
	
	
	/**
	 * Get the bit depth.
	 *
	 * @return The bit depth, or false upon failure.
	 */
	public function getBits() {
		return ($this->_imageHandle ? $this->_imageBits : false);
	}
	
	
	/**
	 * Close the image handle.
	 *
	 * @return boolean True if the destruction succeeded; false otherwise.
	 */
	public function destroy() {
		return @imagedestroy($this->_imageHandle);
	}

	/* PRIVATE
	================================================== */
	protected $_imageFileName;
	protected $_imageHandle;
	protected $_imageX;
	protected $_imageY;
	protected $_imageType;
	protected $_imageBits;
	protected $_imageMimeType;
	
	protected $_imageThumbnailHandle;
	protected $_imageThumbnailX;
	protected $_imageThumbnailY;
}

?>