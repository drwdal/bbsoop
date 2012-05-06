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

class Media extends _IMGBOARD {
	/* CONSTANTS
	================================================== */
	const table = 'media';
	
	/* PROPERTIES
	================================================== */
	protected	$user_ID;
	protected	$URL;
	protected	$thumbnail_URI;
	protected	$type;
	public		$height;
	public		$width;
	public		$t_height; // thumbnail
	public		$t_width; // thumbnail
	public		$duration; // useful for videos or animated gifs; not implemented, yet
	protected	$original_file_name;
	public		$file_size;
	protected	$signature;
	public		$status;
	protected	$posts_count;

	/* RECORD-LEVEL FUNCTIONS
	================================================== */
	public static function find ( $arguments = NULL ) {
		if ( $results = parent::find ( self::table, $arguments ) ) {
			if ( is_int ( key ( $results ) ) ) {
				$count = count ( $results );
				for ( $i = 0; $i < $count; $i++ ) {
					$results[$i] = new self ( $results[$i] );
				}
				return $results;
			} else {
				return new self ( $results );
			}
		}
	}
	public function validate ( ) {
		if ( $this->apply_class_validations ( ) && $this->apply_special_validations ( ) ) {
			$this->_hash = var_to_hash ( $this );
			return true;
		} else {
			// TODO: return human-readable errors?
			return false;
		}
	}
	public function create ( ) {
		global $errors;
		$valid_file = 1;
		// ONE WAY TO IMPROVE THIS!
		// build these transformations into a list of actions, much like building an SQL query
		// then run all of these as a `command` that gets executed on the command line
		// faster and less memory!
		if ( isset ( $_FILES ) ) {
			// build file name
			$time = explode ( ' ', microtime ( false ) );
			$my_microtime_string = number_format ( ( $time[1] + $time[0] ), 10, '', '' );
			$new_filename = BASE_PATH . "content/uploads/to_process/$my_microtime_string";
			// verify file
			if ( move_uploaded_file ( $_FILES['file_upload']['tmp_name'], $new_filename ) ) {
				// verify image
				$exif_types = array ( );
				if ( strpos ( ACCEPT_MIME_TYPES, 'gif' ) !== FALSE ) { $exif_types[] = 1; }
				if ( strpos ( ACCEPT_MIME_TYPES, 'jpeg' ) !== FALSE ) { $exif_types[] = 2; }
				if ( strpos ( ACCEPT_MIME_TYPES, 'png' ) !== FALSE ) { $exif_types[] = 3; }
				if ( in_array ( exif_imagetype ( $new_filename ), $exif_types, TRUE ) ) {
					if ( USE_IMAGEMAGICK == 1 ) {
						$test_image = new Imagick ( );
						$test_image->readImage ( $new_filename );
						$this->signature = $test_image->getImageSignature ( );
						if ( DUPLICATE_IMAGES > 0 && $original_file = Media::find ( array ( 'first', 'conditions' => array ( "`signature` = '%s'", $this->signature ) ) ) ) {
							if ( $original_file->status == 'illegal content' || ( $original_file->status == 'adult content' && ALLOW_ADULT_CONTENT != 1 ) || $original_file->status == 'rule violation' ) {
								$errors[] = 'Upload failed. ID: ' . $original_file->ID ( );
								return false;
							}
							if ( DUPLICATE_IMAGES == 1 ) {
								$test_image->clear ( );
								$test_image->destroy ( );
								unlink ( $new_filename );
								return $original_file->ID ( );
							} else if ( DUPLICATE_IMAGES == 2 ) {
								$test_image->clear ( );
								$test_image->destroy ( );
								unlink ( $new_filename );
								$errors[] = "File already uploaded—duplicates are not allowed. ID: " . $original_file->ID ( );
								return false;
							}
						} else {
							// determine file type
							// determine operations to perform
							// build commands to do these operations
							// run commands
							// save new image stuff
/* MOST OF THE FOLLOWING CAN BE REPLACED */
							$valid_mime_types = strtoupper ( ACCEPT_MIME_TYPES );
							$image_format = strtoupper ( $test_image->getImageFormat ( ) );
							if ( strpos ( $valid_mime_types, $image_format ) !== FALSE ) {
								// FIRST: check if we can do some things that end up using less memory
								while ( $test_image->previousImage ( ) ) { }
								$image_dimensions = $test_image->getImageGeometry ( );
								$test_image->clear ( );
								$test_image->destroy ( );
								// build thumbnail
								$thumbnail_image = new Imagick ( );
								$thumbnail_image->setResolution ( 72, 72 );
								$thumbnail_image->readImage ( $new_filename );
								// rewind to first image
								if ( $thumbnail_image->getImageDepth > 8 ) {
									// reduce to 8 bits if not already
									$thumbnail_image->setImageDepth ( 8 );
								}
								if ( $thumbnail_image->getImageFormat ( ) == 'GIF' ) {
									// go to beginning
									while ( $thumbnail_image->previousImage ( ) ) { }
								}
								$thumbnail_image->setImagePage ( $image_dimensions['width'], $image_dimensions['height'], 0, 0 );
								if ( $image_dimensions['height'] > THUMBNAIL_HEIGHT || $image_dimensions['width'] > THUMBNAIL_WIDTH ) {
									// resize
									if ( THUMBNAILS_CROP_TO_FIT == 1 ) {
										$thumbnail_image->cropThumbnailImage ( THUMBNAIL_WIDTH, THUMBNAIL_HEIGHT );
									} else {
										if ( $image_dimensions['height'] > $image_dimensions['width'] ) {
											$width = floor ( ( THUMBNAIL_HEIGHT / $image_dimensions['height'] ) * ( int ) $image_dimensions['width'] );
											$thumbnail_image->thumbnailImage ( $width, THUMBNAIL_HEIGHT );
										} else {
											$height = floor ( ( THUMBNAIL_WIDTH / $image_dimensions['width'] ) * $image_dimensions['height'] );
											$thumbnail_image->thumbnailImage ( THUMBNAIL_WIDTH, $height );
										}
									}
								}
								$new_dimensions = $thumbnail_image->getImageGeometry ( );
								$this->t_height = $new_dimensions['height'];
								$this->t_width = $new_dimensions['width'];
								if ( $thumbnail_image->getImageFormat ( ) == 'GIF' ) {
									$thumbnail_image->setImagePage ( $new_dimensions['width'], $new_dimensions['height'], 0, 0 );
								}
								// build large
								$large_image = new Imagick ( );
								$large_image->setResolution ( 72, 72 );
								$large_image->readImage ( $new_filename );
								if ( $image_format == 'GIF' && GIF_ALLOW_ANIMATION != 1 ) {
									// flatten
								}
								if ( RESIZE_ORIGINAL_IMAGE == 1 && ( $image_dimensions['height'] > ORIGINAL_IMAGE_RESIZE_HEIGHT || $image_dimensions['width'] > ORIGINAL_IMAGE_RESIZE_WIDTH ) ) {
									if ( $image_dimensions['height'] > $image_dimensions['width'] ) {
										$large_image->resizeImage ( 0, ORIGINAL_IMAGE_RESIZE_HEIGHT, Imagick::FILTER_MITCHELL, 1 );
									} else {
										$large_image->resizeImage ( ORIGINAL_IMAGE_RESIZE_WIDTH, 0, Imagick::FILTER_MITCHELL, 1 );
									}
									$new_dimensions = $large_image->getImageGeometry ( );
									$this->height = $new_dimensions['height'];
									$this->width = $new_dimensions['width'];
								} else {
									$this->height = $image_dimensions['height'];
									$this->width = $image_dimensions['width'];
								}
								if ( PHOTO_AUTO_ROTATE == 1 ) {
									// read exif, see if possible
									// then rotate
								}
								if ( CONVERT_IMAGES_GRAYSCALE == 1 ) {
									$thumbnail_image->setImageColorspace ( Imagick::COLORSPACE_GRAY );
									$large_image->setImageColorspace ( Imagick::COLORSPACE_GRAY );
								} else {
									$thumbnail_image->setImageColorspace ( Imagick::COLORSPACE_RGB );
									$large_image->setImageColorspace ( Imagick::COLORSPACE_RGB );
								}
								// BACKGROUND_FILL_COLOR
								if ( ( $image_format == 'GIF' || $image_format == 'PNG' ) && GIF_ALLOW_TRANSPARENCY != 1 ) {
									$large_image->setImageMatteColor ( BACKGROUND_FILL_COLOR );
								}
								if ( $image_format == 'GIF' ) {
									$thumbnail_image->setImageOpacity ( 1.0 );
								}
								if ( REMOVE_EXIF_AND_PROFILE == 1 ) {
									$thumbnail_image->stripImage ( );
									$large_image->stripImage ( );
								}
								// COMPRESS
								if ( $image_format == 'JPEG' ) {
									$thumbnail_image->setImageCompression ( Imagick::COMPRESSION_JPEG );
									$large_image->setImageCompression ( Imagick::COMPRESSION_JPEG );
									$thumbnail_image->setImageCompressionQuality ( JPEG_THUMBNAIL_QUALITY );
									if ( JPEG_LARGE_QUALITY == 0 ) {
										$quality = $large_image->getCompressionQuality ( );
										$large_image->setImageCompressionQuality ( $quality );
									} else {
										$large_image->setImageCompressionQuality ( JPEG_LARGE_QUALITY );
									}
								} else if ( $image_format == 'PNG' ) {
									$thumbnail_image->setImageCompressionQuality ( PNG_THUMBNAIL_COMPRESSION );
									$large_image->setImageCompressionQuality ( PNG_LARGE_COMPRESSION );
								} else if ( $image_format == 'GIF' ) {
									//$thumbnail_image->posterizeImage ( GIF_THUMBNAIL_MAXIMUM_COLORS, TRUE );
								}
								$thumbnail_image->writeImage ( BASE_PATH . "content/uploads/thumb/$my_microtime_string" . strtolower ( ".$image_format" ) );
								$thumbnail_image->clear ( );
								$thumbnail_image->destroy ( );
								$large_image->writeImage ( BASE_PATH . "content/uploads/large/$my_microtime_string" . strtolower ( ".$image_format" ) );
								$this->file_size = filesize ( BASE_PATH . "content/uploads/large/$my_microtime_string" . strtolower ( ".$image_format" ) );
								$large_image->clear ( );
								$large_image->destroy ( );
								unlink ( $new_filename );
								$this->thumbnail_URI = BASE_URI . "content/uploads/thumb/$my_microtime_string" . strtolower ( ".$image_format" );
								$this->URL = BASE_URI . "content/uploads/large/$my_microtime_string" . strtolower ( ".$image_format" );
								$this->type = 'image';
								if ( MEDIA_GOES_LIVE_WHEN_POSTED == 1 ) {
									$this->status = 'published';
								}
								$this->posts_count = 1;
							} else {
								// this can be removed; this case will not occur
								$errors[] = 'File type “' . $test_image->getImageFormat ( ) . '” is not allowed.';
								$test_image->clear ( );
								$test_image->destroy ( );
								unlink ( $new_filename );
								$valid_file = 0;
							}
						}
					} else {
						// GD PROCESSING
						$this->signature = hash ( 'sha256', file_get_contents ( $new_filename ) );
						if ( DUPLICATE_IMAGES > 0 && $original_file = Media::find ( array ( 'first', 'conditions' => array ( "`signature` = '%s'", $this->signature ) ) ) ) {
							if ( $original_file->status == 'illegal content' || ( $original_file->status == 'adult content' && ALLOW_ADULT_CONTENT != 1 ) || $original_file->status == 'rule violation' ) {
								$errors[] = 'Upload failed. ID: ' . $original_file->ID ( );
								return false;
							}
							if ( DUPLICATE_IMAGES == 1 ) {
								unlink ( $new_filename );
								return $original_file->ID ( );
							} else if ( DUPLICATE_IMAGES == 2 ) {
								unlink ( $new_filename );
								$errors[] = "File already uploaded—duplicates are not allowed. ID: " . $original_file->ID ( );
								return false;
							}
						} else {
							$original_image = imagecreatefromstring ( file_get_contents ( $new_filename ) );
							$image_info = getimagesize ( $new_filename ); // 0 = width; 1 = height
							$image_format = strtoupper ( str_replace ( 'image/', '', $image_info['mime'] ) );
							// test for correct dimensions and filesize
							// test for duplicate image - NOT POSSIBLE WITH GD?!
							// build thumbnail
							if ( $image_info[1] > THUMBNAIL_HEIGHT || $image_info[0] > THUMBNAIL_WIDTH ) {
								// resize
								if ( THUMBNAILS_CROP_TO_FIT == 1 ) {
									$thumb_image = imagecreatetruecolor ( THUMBNAIL_WIDTH, THUMBNAIL_HEIGHT );
									imagecopyresampled ( $thumb_image, $original_image, 0, 0, 0, 0, THUMBNAIL_WIDTH, THUMBNAIL_HEIGHT, $image_info[0], $image_info[1] );
									$this->t_width = THUMBNAIL_WIDTH;
									$this->t_height = THUMBNAIL_HEIGHT;
								} else {
									if ( $image_info[1] > $image_info[0] ) {
										$width = floor ( ( THUMBNAIL_HEIGHT / $image_info[1] ) * ( int ) $image_info[0] );
										$thumb_image = imagecreatetruecolor ( $width, THUMBNAIL_HEIGHT );
										imagecopyresampled ( $thumb_image, $original_image, 0, 0, 0, 0, $width, THUMBNAIL_HEIGHT, $image_info[0], $image_info[1] );
										$this->t_width = $width;
										$this->t_height = THUMBNAIL_HEIGHT;
									} else {
										$height = floor ( ( THUMBNAIL_WIDTH / $image_info[0] ) * $image_info[1] );
										$thumb_image = imagecreatetruecolor ( THUMBNAIL_WIDTH, $height );
										imagecopyresampled ( $thumb_image, $original_image, 0, 0, 0, 0, THUMBNAIL_WIDTH, $height, $image_info[0], $image_info[1] );
										$this->t_width = THUMBNAIL_WIDTH;
										$this->t_height = $height;
									}
								}
							}
							// handle GIF (animation and transparency)
							if ( $image_format == 'GIF' && GIF_ALLOW_ANIMATION != 1 ) {
								// flatten
							}
							// handle PNG (transparency)
							// resize large
							if ( RESIZE_ORIGINAL_IMAGE == 1 && ( $image_info[1] > ORIGINAL_IMAGE_RESIZE_HEIGHT || $image_info[0] > ORIGINAL_IMAGE_RESIZE_WIDTH ) ) {
								if ( $image_info[1] > $image_info[0] ) {
									$width = floor ( ( ORIGINAL_IMAGE_RESIZE_HEIGHT / $image_info[1] ) * ( int ) $image_info[0] );
									$height = ORIGINAL_IMAGE_RESIZE_HEIGHT;
									$large_image = imagecreatetruecolor ( $width, ORIGINAL_IMAGE_RESIZE_HEIGHT );
									imagecopyresampled ( $large_image, $original_image, 0, 0, 0, 0, $width, ORIGINAL_IMAGE_RESIZE_HEIGHT, $image_info[0], $image_info[1] );
								} else {
									$height = floor ( ( ORIGINAL_IMAGE_RESIZE_WIDTH / $image_info[0] ) * $image_info[1] );
									$width = ORIGINAL_IMAGE_RESIZE_WIDTH;
									$large_image = imagecreatetruecolor ( ORIGINAL_IMAGE_RESIZE_WIDTH, $height );
									imagecopyresampled ( $large_image, $original_image, 0, 0, 0, 0, ORIGINAL_IMAGE_RESIZE_WIDTH, $height, $image_info[0], $image_info[1] );
								}
								$this->height = $width;
								$this->width = $height;
							} else {
								$large_image = imagecreatetruecolor ( $image_info[0], $image_info[1] );
								imagecopy ( $large_image, $original_image, 0, 0, 0, 0, $image_info[0], $image_info[1] );
								$this->height = $image_info[1];
								$this->width = $image_info[0];
							}
							// save
							$thumb_data = '';
							$large_data = '';
							switch ( $image_format ) {
								case 'JPEG':
									ob_start ( );
									imagejpeg ( $thumb_image, NULL, JPEG_THUMBNAIL_QUALITY );
									$thumb_data = ob_get_clean ( );
									ob_start ( );
									if ( JPEG_LARGE_QUALITY == 0 ) {
										// default
										imagejpeg ( $large_image );
									} else {
										imagejpeg ( $large_image, NULL, JPEG_LARGE_QUALITY );
									}
									$large_data = ob_get_clean ( );
									break;
								case 'GIF':
									imagegif ( $thumb_image, BASE_PATH . "content/uploads/thumb/$my_microtime_string" . strtolower ( ".$image_format" ) );
									imagegif ( $large_image, BASE_PATH . "content/uploads/large/$my_microtime_string" . strtolower ( ".$image_format" ) );
									break;
								case 'PNG':
									// PNG_LARGE_COMPRESSION stored as 0 to 100
									// PNG quality here is 0 (none) to 9 (max)
									$large_quality = ( PNG_LARGE_COMPRESSION / 10 ) * -1 + 9;
									if ( $large_quality < 0 ) {
										$large_quality = 0;
									}
									$thumb_quality = ( PNG_THUMBNAIL_COMPRESSION / 10 ) * -1 + 9;
									if ( $thumb_quality < 0 ) {
										$thumb_quality = 0;
									}
									ob_start ( );
									imagepng ( $thumb_image, NULL, $thumb_quality );
									$thumb_data = ob_get_clean ( );
									ob_start ( );
									imagepng ( $large_image, NULL, $large_quality );
									$large_data = ob_get_clean ( );
									break;
							}
							if ( ! empty ( $thumb_data ) ) {
								file_put_contents ( BASE_PATH . "content/uploads/thumb/$my_microtime_string" . strtolower ( ".$image_format" ), $thumb_data );
								unset ( $thumb_data );
							}
							if ( ! empty ( $large_data ) ) {
								file_put_contents ( BASE_PATH . "content/uploads/large/$my_microtime_string" . strtolower ( ".$image_format" ), $large_data );
								unset ( $large_data );
							}
							$this->file_size = filesize ( BASE_PATH . "content/uploads/large/$my_microtime_string" . strtolower ( ".$image_format" ) );
							imagedestroy ( $large_image );
							imagedestroy ( $thumb_image );
							unlink ( $new_filename );
							$this->thumbnail_URI = BASE_URI . "content/uploads/thumb/$my_microtime_string" . strtolower ( ".$image_format" );
							$this->URL = BASE_URI . "content/uploads/large/$my_microtime_string" . strtolower ( ".$image_format" );
							$this->type = 'image';
							if ( MEDIA_GOES_LIVE_WHEN_POSTED == 1 ) {
								$this->status = 'published';
							}
							$this->posts_count = 1;
						}
					}
				} else {
					$errors[] = 'File type not allowed.';
					unlink ( $new_filename );
					$valid_file = 0;
				}
			} else {
				$valid_file = 0;
			}
		}
		if ( $valid_file == 1 ) {
			return parent::create ( self::table );
		}
		return false;
	}
	public function update ( $fields ) {
		return parent::update ( self::table, $fields );
	}
	public function update_counter ( $field, $value ) {
		return parent::update_counter ( self::table, $field, $value );
	}
	public function delete_my_files ( ) {
		if ( strpos ( $this->URL ( ), '/content/uploads/large' ) !== false ) {
			unlink ( str_replace ( BASE_URI, '/', BASE_PATH ) . $this->URL ( ) );
		}
		unlink ( str_replace ( BASE_URI, '/', BASE_PATH ) . $this->thumbnail_URI ( ) );
		return true;
	}

	/* PROPERTY-LEVEL FUNCTIONS
	================================================== */
	public function table ( ) {
		return self::table;
	}
	public function user_ID ( ) {
		return ( int ) $this->user_ID;
	}
	public function thumbnail_URI ( ) {
		return ( string ) $this->thumbnail_URI;
	}
	public function URL ( ) {
		return ( string ) $this->URL;
	}
	public function type ( ) {
		return ( string ) $this->type;
	}
	public function original_file_name ( ) {
		return ( string ) $this->original_file_name;
	}
	public function signature ( ) {
		return ( string ) $this->signature;
	}
	public function posts_count ( ) {
		return ( int ) $this->posts_count;
	}


	/* RELATED RECORDS
	================================================== */
	public function topics ( ) {
		return Topic::find ( array ( 'conditions' => array ( "`media_ID` = '%u'", $this->ID ( ) ), 'order' => 'ID ASC' ) );
	}
	public function replies ( ) {
		return Media::find ( array ( 'conditions' => array ( "`media_ID` = '%u'", $this->ID ( ) ) ) );
	}

	/* PRIVATE
	================================================== */
	private function apply_class_validations ( ) {
		// validate the content of each property
		$this->ID =			$this->validate_integer ( $this->ID, TRUE, TRUE );
		$this->created_at =		$this->validate_datetime ( $this->created_at, TRUE );
		$this->updated_at =		$this->validate_datetime ( $this->updated_at, TRUE );
		$this->user_ID =		$this->validate_integer ( $this->user_ID, TRUE, TRUE );
		$this->thumbnail_URI =		$this->validate_string ( $this->thumbnail_URI, TRUE, 0, TRUE );
		$this->URL =			$this->validate_string ( $this->URL, TRUE, 0, TRUE );
		$this->type =			$this->validate_string ( $this->type, TRUE, 30, TRUE );
		$this->height =			$this->validate_integer ( $this->height, TRUE, TRUE );
		$this->width =			$this->validate_integer ( $this->width, TRUE, TRUE );
		$this->duration =		$this->validate_integer ( $this->duration, TRUE, TRUE );
		$this->original_file_name =	$this->validate_string ( $this->original_file_name, TRUE, 255, TRUE );
		$this->file_size =		$this->validate_integer ( $this->file_size, TRUE, TRUE );
		$this->signature =		$this->validate_string ( $this->signature, TRUE, 255, TRUE );
		$this->status =			$this->validate_string ( $this->status, TRUE, 30, TRUE );
		$this->posts_count =		$this->validate_integer ( $this->posts_count, TRUE, 0, TRUE );
		return true;
		// TODO: return human-readable errors?
	}
	private function apply_special_validations ( ) {
		return true;
	}

}

?>