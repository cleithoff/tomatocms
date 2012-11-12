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
 * @version 	$Id: ZipArchive.php 3348 2010-06-28 06:14:09Z huuphuoc $
 * @since		2.0.4
 */

class Tomato_Zip_Adapter_ZipArchive extends Tomato_Zip_Abstract
{
	/**
	 * @var ZipArchive
	 */
	private $_zip;
	
	public function __construct($file)
	{
		parent::__construct($file);
		$this->_zip = new ZipArchive();
	}
	
	public function open()
	{
		return $this->_zip->open($this->_file);
	}
	
	public function close()
	{
		$this->_zip->close();
	}
	
	/**
	 * Extract to destination directory
	 * @param string $destination
	 */
	public function extract($destination)
	{
		$this->_zip->extractTo($destination);	
	}
}
