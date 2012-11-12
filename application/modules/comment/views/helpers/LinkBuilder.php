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
 * @version 	$Id: LinkBuilder.php 2750 2010-04-30 15:04:54Z huuphuoc $
 * @since		2.0.1
 */

class Comment_View_Helper_LinkBuilder extends Zend_View_Helper_Abstract 
{
	public function linkBuilder() 
	{
		return $this;
	}
	
	public function getThreadLink($pageUrl) 
	{
		$array = array('page_url' => $pageUrl);
		return rawurlencode(base64_encode(Zend_Json::encode($array)));
	}
	
	public function getReplyLink($commentId, $pageUrl) 
	{
		$array = array(
					'reply_to' => $commentId,
					'page_url' => $pageUrl,
				);
		return rawurlencode(base64_encode(Zend_Json::encode($array)));
	}
}
