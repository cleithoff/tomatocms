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
 * @version 	$Id: Alexa.php 3526 2010-07-10 16:37:07Z huuphuoc $
 * @since		2.0.7
 */

class Tomato_Seo_Toolkit_Alexa extends Tomato_Seo_Toolkit_Abstract
{
	const REQUEST_URI = 'http://xml.alexa.com/data?cli=10&dat=nsa&url=%s';
	
	public function getBackLinksCount()
	{
		$url  = sprintf(self::REQUEST_URI, $this->_url);		
	    $data = Tomato_Seo_Request::getResponse($url); 
	   	return 0;
	}
	
	public function getBackLinks($offset, $count)
	{		
		return array();
	}

	public function getIndexedPagesCount()
	{
		return 0;
	}
	
	public function getIndexedPages($offset, $count)
	{
		return array();		 
	}
}
