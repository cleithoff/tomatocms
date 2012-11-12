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
 * @version 	$Id: TranslatableArticle.php 4810 2010-08-24 03:36:37Z huuphuoc $
 * @since		2.0.8
 */

class News_View_Helper_TranslatableArticle extends Zend_View_Helper_Abstract
{
	/**
	 * Display select box listing all articles
	 * which haven't been translated from the default language
	 * 
	 * @param $attributes array
	 * @param string $lang
	 * @return string
	 */
	public function translatableArticle($attributes = array(), $lang = null)
	{
		$defaultLang = Tomato_Config::getConfig()->localization->languages->default;
		if (null == $lang) {
			$lang = $defaultLang;
		}
		
		$limit = isset($attributes['limit']) ? ($attributes['limit'] + 10) : 10;
		$attributes['limit'] = $limit;
		
		$conn = Tomato_Db_Connection::factory()->getMasterConnection();
		$articleDao = Tomato_Model_Dao_Factory::getInstance()->setModule('news')->getArticleDao();
		$articleDao->setDbConnection($conn);
		$articles = $articleDao->getTranslatable($lang, $limit);
		
		$this->view->assign('attributes', $attributes);
		$this->view->assign('articles', $articles);
		$this->view->assign('lang', $lang);
		$this->view->assign('defaultLang', $defaultLang);
		
		return $this->view->render('_partial/_translatableArticle.phtml');
	}	
}
