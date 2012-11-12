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
 * @version 	$Id: HookLoader.php 3971 2010-07-25 10:26:42Z huuphuoc $
 * @since		2.0.0
 */

/**
 * This plugin load all hook targets and regiter all hooks if they are available
 */
class Core_Controllers_Plugin_HookLoader extends Zend_Controller_Plugin_Abstract 
{
	public function preDispatch(Zend_Controller_Request_Abstract $request)
	{
		/**
		 * Get list of targets
		 */
		$conn = Tomato_Db_Connection::factory()->getSlaveConnection();
		$targetDao = Tomato_Model_Dao_Factory::getInstance()->setModule('core')->getTargetDao();
		$targetDao->setDbConnection($conn);
		$targets = $targetDao->getTargets();
		
		if ($targets) {
			foreach ($targets as $target) {
				/**
				 * Determine the hook class
				 */
				$hookClass = (null == $target->hook_module || '' == $target->hook_module)
						? 'Hooks_' . ucfirst($target->hook_name) . '_Hook'
						: ucfirst(strtolower($target->hook_module)) . '_Hooks_' . ucfirst($target->hook_name) . '_Hook';

				/**
				 * Create new hook instance and register it
				 */
				if (class_exists($hookClass) && (($hook = new $hookClass()) instanceof Tomato_Hook)) {
					Tomato_Hook_Registry::getInstance()->register($target->target_name, array($hook, $target->hook_type));
				}
			}
		}
	}
}
