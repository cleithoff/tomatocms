<?php
class News_Hooks_InternalLinks_Hook extends Tomato_Hook
{
	public static function action()
	{
		$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
		$view = $viewRenderer->view;
		$view->addScriptPath(dirname(__FILE__));
		
		echo $view->render('show.phtml');
	}
}
