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
 * @version 	$Id: Hook.php 4558 2010-08-12 10:42:36Z huuphuoc $
 * @since		2.0.2
 */

class Tag_Hooks_Tagger_Hook 
{
	public static function show($itemName, $routeName, $detailsRouteName, $itemId = null)
	{
		$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
		$view = $viewRenderer->view;
		
		if ($itemId) {
			$conn = Tomato_Db_Connection::factory()->getMasterConnection();
			$tagDao = Tomato_Model_Dao_Factory::getInstance()->setModule('tag')->getTagDao();
			$tagDao->setDbConnection($conn);
			$tags = $tagDao->getByItem(new Tag_Models_TagItem(array(
												'item_id' 			 => $itemId,
												'item_name' 		 => $itemName,
												'route_name' 		 => $routeName,
												'details_route_name' => $detailsRouteName,
											)));
			$view->assign('tags', $tags);
		}
		
		$view->assign('tagItemName', $itemName);
		$view->assign('tagItemRouteName', $routeName);
		$view->assign('tagDetailsRouteName', $detailsRouteName);
		$view->addScriptPath(TOMATO_APP_DIR . DS . 'modules' . DS . 'tag' . DS . 'views' . DS . 'scripts');
		echo $view->render('partial/_tagger.phtml');
	}
	
	public static function add($itemId)
	{
		$request = Zend_Controller_Front::getInstance()->getRequest();
		$itemName 	   = $request->getParam('tagItemName');
		$itemRouteName = $request->getParam('tagItemRouteName');
		$detailsRoute  = $request->getParam('tagDetailsRouteName');
		$tagIds 	   = $request->getParam('tagIds');
		
		if ($tagIds) {
			$conn = Tomato_Db_Connection::factory()->getMasterConnection();
			$tagItemDao = Tomato_Model_Dao_Factory::getInstance()->setModule('tag')->getTagItemDao();
			$tagItemDao->setDbConnection($conn);
			$tagItemDao->delete(new Tag_Models_TagItem(array(
									'item_id' 			 => $itemId,
									'item_name' 		 => $itemName,
									'route_name' 		 => $itemRouteName,
									'details_route_name' => $detailsRoute,
								)));
			
			foreach ($tagIds as $tagId) {
				$tagItemDao->add(new Tag_Models_TagItem(array(
									'tag_id' 			 => $tagId,
									'item_id' 			 => $itemId,
									'item_name' 		 => $itemName,
									'route_name' 		 => $itemRouteName,
									'details_route_name' => $detailsRoute,
									'params' 			 => $itemName . ':' . $itemId,
								)));
			}
		}
	}
}
