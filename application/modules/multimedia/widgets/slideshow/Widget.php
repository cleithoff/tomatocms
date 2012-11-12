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
 * @version 	$Id: Widget.php 5087 2010-08-29 15:23:30Z huuphuoc $
 * @since		2.0.0
 */

class Multimedia_Widgets_Slideshow_Widget extends Tomato_Widget 
{
	protected function _prepareShow() 
	{
		$limit = (int)$this->_request->getParam('limit', 10);
		$limit = ($limit == 0) ? 10 : $limit;
		
		$conn = Tomato_Db_Connection::factory()->getSlaveConnection();
		$fileDao = Tomato_Model_Dao_Factory::getInstance()->setModule('multimedia')->getFileDao();
		$fileDao->setDbConnection($conn);
		$photos = $fileDao->find(0, $limit, array(
									'is_active'	=> true,
									'file_type' => 'image',
								));
								
		$this->_view->assign('photos', $photos);
	}
}
