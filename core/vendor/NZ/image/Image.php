<?php
namespace NZ;
class Image{
	private $imgprops = array();
	private $image;
	private $img;
	private $image_out;
	public $width = 0;
	public $height = 0;
	public $mime;
	private $extension;
	private $supportedMimes = array('jpeg' => true, 'jpg' => true, 'pjpeg' => true, 'gif' => true, 'png' => true);
	
	public function __construct( $image_src ){
			
		if( !$imgprops = @getimagesize($image_src) ){
			throw new \Exception('The given file ('.$image_src.') is not a valid image!');
			return false;
		}
		$this->width = $imgprops[0];
		$this->height = $imgprops[1];
		$m = explode( '/', $imgprops['mime'] );
		$mime = $m[1];
		$this->mime = $mime;
		$this->extension = $mime;
		
		if( !isset( $this->supportedMimes[$mime] ) ){
			throw new \Exception( $this->mime." is not supported!", $code = -101 );
			return false;
		}

		$this->allocateMemory( $imgprops );

		$fName = null;
              /*
		if( $f = file_get_contents( $image_src ) ) {
			$tmp = sys_get_temp_dir();
			$fName = $tmp.'/'.md5( $image_src ).'.'.$this->mime;
			file_put_contents( $fName, $f );
			NZ_Image::cleanJPG( $fName, $fName );
			$image_src = $fName;
		}
		*/
		$this->img = $this->getHandle($image_src, $this->mime);
		$this->imageSrc = $image_src;

		if( $fName ){
			unlink( $fName );
		}
	}

	public function getExtension(){
		return $this->extension;
	}
	
        /**
         * Tage the input name, saved the file in tmp forlder an returns the path
         * @param string $inputName
         * @return string
         * @throws \Exception
         */
	static public function tryUpload( $inputName ){
		if( !$_FILES ){
			throw new \Exception('No $_FILES');
			return false;
		}
		
		if( !is_uploaded_file($_FILES[ $inputName ]['tmp_name']) ){
			throw new \Exception( 'is_uploaded_file returns false' );

			return false;
		}
		
		if( !preg_match( "/image\//", $_FILES[ $inputName ]['type'] ) ){
			throw new \Exception( 'mime type is not an image ');
			return null;
		}
	
		$mimes  = array();   
		$mimes['jpg'] = true;
		$mimes['jpeg'] = true;
		$mimes['gif'] = true;
		$mimes['png'] = true;
		
		$arr = explode( '.', $_FILES[$inputName]['name'] );
		if( !$arr ){
			throw new \Exception( 'No extension');
			return false;
		}
		
		$theMime = (string) array_pop( $arr );
		
		$theMime = strtolower( $theMime );
		if( !isset( $mimes[$theMime] ) ){
			throw new \Exception( 'Unsupported mime type' );
			return null;
		}
		
		$destFile = $inputName.'_'.time().'.'.$theMime ;
		if( !copy( $_FILES[$inputName]['tmp_name'], '/tmp/'.$destFile ) ) {
			throw new \Exception( 'failed to copy the uploaded file' );
			return false;
		}

		return '/tmp/'.$destFile;
	}

	static public function cleanJPG( $srcImage, $destImage = null  ){
		//Diese Funktion dient dazu nicht ben�tigten Code aus JPEG Bildern zu filtern
		//Zuerst handle erstellen um $filename bin�r auszulesen
		$handle = fopen( $srcImage, "rb");

		//Anschlie�end immer die Segmente mit der gr��e auslesen
		$segment = array();
		$segment[] = fread($handle, 2);

		//Wenn die ersten beiden Bytes nicht 0xFF 0xD8 entsprechen - abbruch!
		if($segment[0] === "\xFF\xD8")  {
			//Jetzt schauen ob neues Segment 0xFF entspricht - wenn nicht abbruch
			$segment[] = fread($handle, 1);
			//Wenn Segment vorhanden - fahre fort!
			if($segment[1] === "\xFF") {
				//Dateizeiger an den Anfang setzen
				rewind ($handle);
				//Nun wird die ganze Datei durchsucht
				while( !feof($handle) ) {
					$daten = fread($handle, 2);
					//Pr�fe auf spezielle Segmente - falls diese vorhanden sind -> Zeiger neu setzen
					if( ( preg_match("/FFE[1-9a-zA-Z]{1,1}/i",bin2hex($daten))) || ($daten === "\xFF\xFE") ) {
						//Position des Dateizeigers
						$position = ftell($handle);
						//Gr��e des Segments auslesen
						$size = fread($handle, 2);
						//Gr��e ausrechnen
						$newsize = 256 * ord($size{0}) + ord($size{1});
						//Hier nun neue Position bestimmen -> Position hinter dieser Zone
						$newpos = $position + $newsize;
						//Dateizier setzen
						fseek($handle, $newpos);
					}else {
						$newfile[] = $daten;
					}
				}

				//Hier File Handle schlie�en
				fclose($handle);
				//Wenn Schleife durch ist haben wir newfile als Array
				//Dieses wird nun in einen String umgewandelt
				$newfile = implode('',$newfile);
				if( $destImage ){
					return file_put_contents( $destImage, $newfile);
				}
				return $newfile;
			}
		}
		
		return false;
	}
	
	private function allocateMemory( $imageInfo ){
		/*
		$imageSize = $imageInfo[0] * $imageInfo[1] * $imageInfo['bits'];
		$memoryNeeded = round(( $imageSize  * $imageInfo['channels'] / 8 + Pow(2, 16)) * 1.65 );

		$memoryNeeded = $memoryNeeded / pow(1024, 2);
		$memoryNeeded = $memoryNeeded * 5;
		$memoryLimitMB = (integer) ini_get('memory_limit');

		$memoryNeeded = ceil( $memoryNeeded );
		if( $memoryNeeded < ($memoryLimitMB * 3 ) ){
			//return false;
		}
		
		$newMemory = ceil( $memoryNeeded + $memoryLimitMB );
		*/
		ini_set( 'memory_limit', '1050M' );
	}

	public function cut($src_x, $src_y, $src_w, $src_h, $width = 0, $height = 0 ){
		$this->image_out = imagecreatetruecolor( $width,  $height );
		imagecopyresampled ( $this->image_out, $this->img, $dst_x = 0, $dst_y = 0, $src_x, $src_y, $dst_w = $width, $dst_h = $height,  $src_w, $src_h );
	}
	
	public function cutscale($src_x, $src_y, $src_w, $src_h, $width = 0, $height = 0 , $destwidth=0 , $destheight=0){
		$this->image_out = imagecreatetruecolor( $destwidth,  $destheight );
		imagecopyresampled ( $this->image_out, $this->img, $dst_x = 0, $dst_y = 0, $src_x, $src_y, $dst_w = $width, $dst_h = $height,  $src_w, $src_h );
	}

	public function scale( $max_width, $max_height ){
		
		$width = $this->width;
		$height = $this->height;

		$mx = $max_width;
		$my = $max_height;

		$sx = $this->width;
	       $sy = $this->height;
         	
	       $v = min(min($mx,$sx)/$sx,min($my,$sy)/$sy);
       	$dx = $v*$sx;
	       $dy = $v*$sy;

       	$this->image_out = imagecreatetruecolor($dx,$dy);
		
           
           	imagecopyresampled ($this->image_out, $this->img, 0, 0, 0, 0, $dx, $dy, $sx, $sy);
	}

	public function scale_up( $max_width, $max_height ){

		$width = $this->width;
		$height = $this->height;
		
		if ( $width < $max_width ) {
			$height = ( $max_width / $width) * $height;
			$width = $max_width;
			
			if($height > $max_height){
				$width = ( $max_height / $height) * $width;
				$height = $max_height;
			}
		}			
		else if( $height < $max_height ){
			$width = ( $max_height / $height) * $width;
			$height = $max_height;
			
			if($width > $max_width){
				$height = ( $max_width / $width) * $height;
				$width = $max_width;
			}
		}
		else{
			return $this->scale( $max_width, $max_height );
		}
       	$this->image_out = imagecreatetruecolor( $width, $height );
           	imagecopyresampled ($this->image_out, $this->img, 0, 0, 0, 0, $width, $height, $this->width, $this->height);
	}

	public function rotate( $degree ){
		$this->image_out = imagerotate( $this->img, $degree, 0);
	}
	
	public function writeText( $text, $x, $y, $size, $color, $font, $angle = 0  ){
		$theY = $y;
		$theX = $x;
		$width = imagesx(  $this->image_out );
		$height = imagesy(  $this->image_out );
		if( $y < 0 ){
			$theY = $height + $y + intval( $size );
		}
		if( $x < 0 ){
			$theX = $width + $x;
		}
		$theColor = $this->colorFromHex( $color );
		imagettftext( $this->image_out , $size, $angle, $theX, $theY, $theColor, $font, $text  );
	}

	public function insertImage( $img, $x, $y ){
		try{
			$ip = new ImageManipulator( $img );
		}catch( Exception $e ){
			throw $e;
		}
		$width = imagesx(  $this->image_out );
		$height = imagesy(  $this->image_out );
		$theY = $y;
		$theX = $x;
		if( $y < 0 ){
			$theY = $height + $y;
		}
		if( $x < 0 ){
			$theX = $width + $x;
		}
		return imagecopy( $this->image_out, $ip->getImage() , $theX, $theY, 0, 0, $ip->getWidth(),  $ip->getHeight() );
	}

	private function colorFromHex( $hexaColor ) {
		$hexaColor = str_replace('#', '', $hexaColor);
		sscanf($hexaColor, "%2x%2x%2x", $red, $green, $blue);
     	return ImageColorAllocate( $this->image_out , $red, $green, $blue );
   	}
	
	public function save($name, $ext = null ){
		if(!$this->image_out){
			$this->image_out = $this->img;
		}
		$mime = $this->mime;
		if($ext != null ){
			if( isset( $this->supportedMimes[$ext] ) ){
				$mime = $ext;
				$name = $name.'.'.$ext;
			}
		}

		if( $mime == 'jpeg' || $mime == 'jpg' || $mime == 'pjpeg'){
			$ret = imagejpeg( $this->image_out, $name, 90 );
			//$this->destroy();
			return $ret;
		}
		if( $mime == 'gif'){
			$ret = imagegif ( $this->image_out, $name );
			//$this->destroy();
			return $ret;
		}
		
		if( $mime == 'png'){
			$ret = imagepng($this->image_out, $name);
			//$this->destroy();
			return $ret;
		}
		return $this->save($name, $ext = null );
	}
	
	public function destroy(){
		if( $this->image_out != null ){
			imagedestroy( $this->image_out );
			$this->image_out = null;
		}
	}
	public function getImage(){
		return $this->img;
	}

	public function getWidth(){
		return $this->width;
	}
	
	public function getHeight(){
		return $this->height;
	}
	
	private function getHandle($imgname, $mime ){
		$img = null;
		if( $mime == 'jpeg' || $mime == 'jpg' || $mime == 'pjpeg'){
			$img = imagecreatefromjpeg($imgname);
		}
		if( $mime == 'gif'){
			$img = imagecreatefromgif($imgname);
		}
		if( $mime == 'png'){
			$img = imagecreatefrompng($imgname);
			imagealphablending($img, true); // setting alpha blending on
			imagesavealpha($img, true); // save alphablending setting (important)

		}
		return $img;
	}
	
	function __destruct(){
		$this->destroy();
	}
}