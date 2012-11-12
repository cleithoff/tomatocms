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
 * @version 	$Id: BannerPageAssoc.php 3352 2010-06-28 06:16:48Z huuphuoc $
 * @since		2.0.5
 */

interface Ad_Models_Interface_BannerPageAssoc
{
	/**
	 * Remove banner-page association
	 * 
	 * @param int $bannerId Id of banner
	 */
	public function removeByBanner($bannerId);

	/**
	 * Add banner-page association
	 * 
	 * @param Ad_Models_BannerPageAssoc $bannerPageAssoc
	 */
	public function add($bannerPageAssoc);
	
	/**
	 * Get banner-page associations
	 * 
	 * @param int $bannerId Id of banner
	 */
	public function getBannerPageAssoc($bannerId);
}
