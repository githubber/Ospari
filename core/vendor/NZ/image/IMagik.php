<?php
namespace NZ;
class iMagik{
	public $width = 0;
	public $height = 0;
	private $image;
	private $tmpImage = null;
	
	/**
	 * Constructor
	 *
	 * @param str $image
	 */
	public function __construct( $image ){
		if( !$imgprops = @getimagesize( $image ) ){
			throw new Exception('The given file is not a valid image!');
			return false;
		}

		/*
		$m = explode( '/', $imgprops['mime'] );
		$mime = $m[1];

			
		$fName = null;
		
		if( $f = file_get_contents( $image ) ) {
			$tmp = sys_get_temp_dir();
			$fName = $tmp.'/'.md5( $image ).'.'.$mime;
			file_put_contents( $fName, $f );
			NZ_Image::cleanJPG( $fName, $fName );
			$image = $fName;
			
		}
		
	
		$this->tmpImage = $fName;
		*/

		$this->width = $imgprops[0];
		$this->height = $imgprops[1];	
		$this->image =  $image;
	}

	/**
	 * Resizes an image
	 *
	 * @param int $newWidth
	 * @param int $newHeight
	 * @param str $destFile
	 * @param int $quality
	 */
	public function resize( $newWidth, $newHeight, $destFile,  $quality = 80){
		return $this->resizeJPG( $newWidth, $newHeight, $destFile,  $quality);
	}
	
	/**
	 * Resizes an image
	 *
	 * @param int $newWidth
	 * @param int $newHeight
	 * @param str $destFile
	 * @param int $quality
	 */
	public function resizeJPG( $newWidth, $newHeight, $destFile,  $quality = 80){
		$h = $this->height;
		$w = $this->width;	
	
		$new_w = intval( $newWidth );
		$new_h =  intval( $newHeight );
		
		$quality = intval( $quality );
		
		$ratio_w = $h / $w;
		$ratio_h = $w / $h;
		
		$destFile  = $destFile;

		if ($w > $h){
			$res_h = (int)($ratio_w * $new_w);
			$res_w = (int)($new_w);
		}else{
			$res_h = (int)($new_w);
			$res_w = (int)($ratio_h * $new_w);
		}

		if( $res_h >  $h ){
			$res_h = $h;
		}

		if( $res_w >  $w ){
			$res_w = $w;
		}

		
		$exec_str = "convert -size ".($res_w)."x".($res_h)." -resize ".$res_w."x".$res_h."! -sharpen 0x.5 -quality $quality '".$this->image."' '".$destFile."'";
			
		exec( $exec_str );
	}
	
	/**
	 * Crops the image automatically
	 *
	 * @param int $newWidth
	 * @param int $newHeight
	 * @param str $destFile
	 * @param int $quality
	 */
	public function autoCrop( $newWidth, $newHeight, $destFile, $quality = 80){
		$h = $this->height;
		$w = $this->width;	

		$new_w = intval( $newWidth );
		$new_h =  intval( $newHeight );
		
		$quality = intval( $quality );
		
		$ratio_w = $h / $w;
		$ratio_h = $w / $h;

		$destFile  = $destFile;
		
		$res_h = (int)($ratio_w * $new_w);
		$res_w = (int)($new_w);
			
		if ($res_h < $new_h){
			$res_h = (int)($new_h);
			$res_w = (int)($ratio_h * $new_h);
		}
			
		$start_x = (int)(($res_w - $new_w) / 2);
		$start_y = (int)(($res_h - $new_h) / 3);

		$exec_str = "convert -size ".($res_w)."x".($res_h)." -resize ".$res_w."x".$res_h."! -sharpen 0.5 -quality ".$quality." -crop '".$new_w."x".$new_h."+".$start_x."+".$start_y."' '".$this->image."' '".$destFile."'";
		exec( $exec_str );
	}
	
	/**
	 * Inserts an image into another
	 *
	 * @param str $image
	 * @param stri $location
	 * @param int $quality
	 */
	public function insertImage( $image, $location = 'SouthEast', $quality = 80  ){
		$image  = escapeshellarg( $image );
		$location  = escapeshellarg( $location );
		$quality = intval( $quality );
		
		$exec_str = "composite -dissolve 100% -gravity ".$location." -quality ".$quality." '".$image."' '".$this->image."' '".$this->image."'";
		echo $exec_str;
		exec( $exec_str );
	}
	
	/**
	 * For our Scouts/Users who are to silly to rotate
	 * IMAGICK MUST BE UPDATED TO USE THIS
	 */
	public function autoRotate(){
		$exec_str = "composite -auto-orient '".$this->image."' '".$this->image."'";
//		exec( $exec_str );

		// reset
		$imgprops = @getimagesize( $this->image);
		$this->width = $imgprops[0];
		$this->height = $imgprops[1];
	}
	
	/**
	 * Rotate image
	 * 
	 * @param int $degrees Degrees
	 */
	public function rotate( $degrees ){
		$degrees = intval( $degrees );
		
		$exec_str = "convert -rotate ".$degrees." '".$this->image."' '".$this->image."'";
		exec( $exec_str );
		
		// reset
		$imgprops = @getimagesize( $this->image );
		$this->width = $imgprops[0];
		$this->height = $imgprops[1];
	}
	
	/**
	 * Destructor
	 *
	 */
	public function __destruct(){
		if( $this->tmpImage ){
			//unlink($this->tmpImage);
		}
	}
}