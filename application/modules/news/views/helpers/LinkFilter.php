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
 * @version 	$Id: LinkFilter.php 4488 2010-08-11 17:24:14Z huuphuoc $
 * @since		2.0.7
 */

/**
 * This helper shows link to article listing page based on current search expression
 * and status
 */
class News_View_Helper_LinkFilter extends Zend_View_Helper_Abstract
{
	/**
	 * @param array $exp Search expression
	 * @param string $status Status filter
	 * @param int $pageIndex Page index
	 * @return string
	 */
	public function linkFilter($exp, $status = null, $pageIndex = 1)
	{
		$language = Zend_Controller_Front::getInstance()->getRequest()->getParam('lang');
		
		if (null == $status && isset($exp['status'])) {
			unset($exp['status']);
		} else {
			$exp['status'] = $status;
		}
		$q = rawurlencode(base64_encode(Zend_Json::encode($exp)));
		return $this->view->url(array('pageIndex' => $pageIndex), 'news_article_list_pager') . '/' . $language . '?q=' . $q;
	}
}
