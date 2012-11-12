<?php
/**
 * TomatoCMS
 * 
 * LICENSE
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE Version 2 
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-2.0.txt
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@tomatocms.com so we can send you a copy immediately.
 * 
 * @copyright	Copyright (c) 2009-2010 TIG Corporation (http://www.tig.vn)
 * @license		http://www.gnu.org/licenses/gpl-2.0.txt GNU GENERAL PUBLIC LICENSE Version 2
 * @version 	$Id: GD.php 3986 2010-07-25 16:32:46Z huuphuoc $
 * @since		2.0.0
 */

class Tomato_Image_GD extends Tomato_Image_Abstract 
{
	public function rotate($newFile, $angle) 
	{
		$newFile = (null == $newFile) ? $this->_file : $newFile;

		$source = $this->_createSourceFile($this->_file);
		$rotate = @imagerotate($source, 360 - $angle, -1);

		$this->_createDesFile($rotate, $newFile);

		@imagedestroy($source);
		@imagedestroy($rotate);
	}

	/**
	 * @since 2.0.4
	 */
	public function flip($newFile, $mode)
	{
		$newFile = (null == $newFile) ? $this->_file : $newFile;
		$source  = $this->_createSourceFile($this->_file);
		
		$srcX = 0;
		$srcY = 0;
		$srcWidth  = $this->_width;
		$srcHeight = $this->_height;
		
		switch ($mode){
			case Tomato_Image_Abstract::FLIP_VERTICAL:
            	$srcY 	   = $this->_height - 1;
            	$srcHeight = -$this->_height;
        		break;
			case Tomato_Image_Abstract::FLIP_HORIZONTAL:
				$srcX	  = $this->_width - 1;
				$srcWidth = -$this->_width;
				break;
			default:
				return $source;
		}
		
		$des = @imagecreatetruecolor($this->_width, $this->_height);
		@imagecopyresampled($des, $source, 0, 0, $srcX, $srcY , $this->_width, $this->_height, $srcWidth, $srcHeight);
		
		$this->_createDesFile($des, $newFile);
		
		@imagedestroy($source);
		@imagedestroy($des);
	}
	
	protected function _resize($newFile, $newWidth, $newHeight) 
	{
		$newFile = (null == $newFile) ? $this->_file : $newFile;

		$source = $this->_createSourceFile($this->_file);
		$des = @imagecreatetruecolor($newWidth, $newHeight);
		@imagecopyresampled($des, $source, 0, 0, 0, 0, $newWidth, $newHeight, $this->_width, $this->_height);

		$this->_createDesFile($des, $newFile);

		@imagedestroy($source);
		@imagedestroy($des);
	}

	protected function _crop($newFile, $resizeWidth, $resizeHeight, $newWidth, $newHeight, $cropX, $cropY, $resize = true) 
	{
		$newFile = (null == $newFile) ? $this->_file : $newFile;
		
		if ($resize) {
			/**
			 * Resize
			 */
			$this->_resize($newFile, $resizeWidth, $resizeHeight);
			$source = $this->_createSourceFile($newFile);
		} else {
			/**
			 * Crop
			 */ 
			$source = $this->_createSourceFile($this->_file);			
		}
			
		$des = @imagecreatetruecolor($newWidth, $newHeight);
		
		//@imagecopyresized($des, $source, 0, 0, $cropX, $cropY, $newWidth, $newHeight, $resizeWidth, $resizeHeight);
		@imagecopy($des, $source, 0, 0, $cropX, $cropY, $newWidth, $newHeight);

		$this->_createDesFile($des, $newFile);

		@imagedestroy($source);
		@imagedestroy($des);
	}

	private function _createSourceFile($file) 
	{
		$ext = explode('.', $file);
		$fileType = strtolower($ext[count($ext) - 1]);
		switch($fileType) {
			case 'jpg':
			case 'jpeg':
				return @imagecreatefromjpeg($file);
				break;
			case 'png':
				return @imagecreatefrompng($file);
				break;
			case 'gif':
				return @imagecreatefromgif($file);
				break;
			case 'wbmp':
				return @imagecreatefromwbmp($file);
				break;
			default:
				throw new Exception('Do not support '.$this->_fileType.' type of image');
				break;
		}
		return null;
	}

	private function _createDesFile($file, $newFile, $quality = 100) 
	{
		switch($this->_fileType) {
			case 'jpg':
			case 'jpeg':
				@imagejpeg($file, $newFile, $quality);
				break;
			case 'png':
				@imagepng($file, $newFile);
				break;
			case 'gif':
				@imagegif($file, $newFile);
				break;
			case 'wbmp':
				@imagewbmp($file, $newFile);
				break;
			default:
				throw new Exception('Do not support '.$this->_fileType.' type of image');
				break;
		}
	}	
	
	/**
	 * @since 2.0.4
	 */
	public function watermarkImage($overlayFile, $position) 
	{
		$overlay = $this->_createSourceFile($overlayFile);
		$source  = $this->_createSourceFile($this->_file);
		
		$info = getimagesize($overlayFile);
		
		$overlayWidth  = $info[0];
		$overlayHeight = $info[1];
		
		switch($position) {
			case Tomato_Image_Abstract::POS_TOP_LEFT:
				imagecopy($source, $overlay, 0, 0, 0, 0, $overlayWidth, $overlayHeight);
				break;
			case Tomato_Image_Abstract::POS_TOP_RIGHT:
				imagecopy($source, $overlay, $this->_width - $overlayWidth, 0, 0, 0, $overlayWidth, $overlayHeight);
				break;
			case Tomato_Image_Abstract::POS_MIDDLE_CENTER:
				imagecopy($source, $overlay, ($this->_width - $overlayWidth) / 2, ($this->_height - $overlayHeight) / 2, 0, 0, $overlayWidth, $overlayHeight);
//				imagecopymerge($source, $overlay, $this->_width - $overlayWidth, 0, 0, 0, $overlayWidth, $overlayHeight, $opacity);
				break;
			case Tomato_Image_Abstract::POS_BOTTOM_LEFT:
				imagecopy($source, $overlay, 0, $this->_height - $overlayHeight, 0, 0, $overlayWidth, $overlayHeight);
				break;
			case Tomato_Image_Abstract::POS_BOTTOM_RIGHT:
				imagecopy($source, $overlay, $this->_width - $overlayWidth, $this->_height - $overlayHeight, 0, 0, $overlayWidth, $overlayHeight);
				break;
			default:
				throw new Exception('Do not support '.$position.' type of position');
				break;
		}
		
		$this->_createDesFile($source, $this->_file);

		@imagedestroy($overlay);
		@imagedestroy($source);
	}
	
	public function watermarkText($overlayText, $position, 
							$param = array('rotation' => 0, 'opacity' => 50, 'color' => 'FFF', 'size' => null))	 
	{
		$size = null;
		if ($param['size']) {
			$size = $param['size'];
		} else {
			$stringBox12 = imagettfbbox(12, 0, $this->_watermarkFont, $overlayText);
			$string12 	 = $stringBox12[2];
			$size = (int)($this->_width / 2) * 12 / $string12;
		}
		
		$source = $this->_createSourceFile($this->_file);
		
		$bb = imagettfbbox($size, 0, $this->_watermarkFont, $overlayText);
		$aa = deg2rad($param['rotation']);
		$cc = cos($aa);
		$ss = sin($aa);
		$rr = array();
		for($i = 0; $i < 7; $i += 2) {
			$rr[$i + 0] = round($bb[$i + 0] * $cc + $bb[$i + 1] * $ss);
			$rr[$i + 1] = round($bb[$i + 1] * $cc - $bb[$i + 0] * $ss);
		}
 
		$x0 = min($rr[0], $rr[2], $rr[4], $rr[6]) - 5;
		$x1 = max($rr[0], $rr[2], $rr[4], $rr[6]) + 5;
		$y0 = min($rr[1], $rr[3], $rr[5], $rr[7]) - 5;
		$y1 = max($rr[1], $rr[3], $rr[5], $rr[7]) + 5;

		$bbWidth  = abs($x1 - $x0);
		$bbHeight = abs($y1 - $y0);

		switch ($position) {
			case Tomato_Image_Abstract::POS_TOP_LEFT:
				$bpy = -$y0;
				$bpx = -$x0;
				break;			
			case Tomato_Image_Abstract::POS_TOP_RIGHT:
				$bpy = -$y0;
				$bpx = $this->_width - $x1;
				break;			
			case Tomato_Image_Abstract::POS_MIDDLE_CENTER:
				$bpy = $this->_height / 2 - $bbHeight / 2 - $y0;
				$bpx = $this->_width / 2 - $bbWidth / 2 - $x0;
				break;
			case Tomato_Image_Abstract::POS_BOTTOM_LEFT:
				$bpy = $this->_height - $y1;
				$bpx = -$x0;
				break;
			case Tomato_Image_Abstract::POS_BOTTOM_RIGHT;
				$bpy = $this->_height - $y1;
				$bpx = $this->_width - $x1;
				break;
			default:
				throw new Exception('Do not support '.$position.' type of position');
				break;
		} 
		$alphaColor = @imagecolorallocatealpha($source, 
			hexdec(substr($param['color'], 0, 2)), hexdec(substr($param['color'], 2, 2)), 
			hexdec(substr($param['color'], 4, 2)), 127 * (100 - $param['opacity']) / 100);
 
		@imagettftext($source, $size, $param['rotation'], $bpx, $bpy, $alphaColor, $this->_watermarkFont, $overlayText);
		
		$this->_createDesFile($source, $this->_file);		
		@imagedestroy($source);
	}
}
