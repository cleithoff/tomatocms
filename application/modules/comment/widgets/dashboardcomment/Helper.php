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
 * @copyright	Copyright (c) 2008-2009 TIG Corporation (http://www.tig.vn)
 * @license		GNU GPL license, see http://www.tomatocms.com/license.txt or license.txt
 * @version 	$Id: Helper.php 3558 2010-07-11 08:41:13Z huuphuoc $
 * @since		2.0.7
 */

class Comment_Widgets_DashboardComment_Helper extends Zend_View_Helper_Abstract 
{
	public function helper()
	{
		return $this;
	}
	
	public function gravatar($email, $size = 50) 
	{
		return sprintf('http://gravatar.com/avatar/%s?s=%d', md5($email), $size);
	}
}
