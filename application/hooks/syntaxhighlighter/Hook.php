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
 * @version 	$Id: Hook.php 3741 2010-07-17 06:54:08Z huuphuoc $
 */

class Hooks_SyntaxHighlighter_Hook extends Tomato_Hook 
{
	private static $_scriptAttached = false;
	
	public static function filter($content) 
	{
		/**
		 * Call script only once
		 */
		if (self::$_scriptAttached) {
			return $content;
		}
		
		$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer'); 
		$view = $viewRenderer->view;

		/**
		 * FIXME: The following code work correctly if it is called from action script,
		 * does not work if we call from widget (applying the hook for widget)
		 * Hence we have to use Javascript to attach JS/CSS file to head section of page
		 */
		/*
		$view->headLink()->appendStylesheet($view->APP_STATIC_SERVER.'/js/syntaxhighlighter/styles/shCore.css');
		$view->headLink()->appendStylesheet($view->APP_STATIC_SERVER.'/js/syntaxhighlighter/styles/shThemeDefault.css');
		$view->headScript()->appendFile($view->APP_STATIC_SERVER.'/js/syntaxhighlighter/src/shCore.js');
		*/

		$append = <<<END
		<style type="text/css">
		.syntaxhighlighter { width: 100%; overflow: scroll; }
		</style>
		<script type="text/javascript">	
		$(document).ready(function() {
			$('<link rel="stylesheet" type="text/css" href="$view->APP_STATIC_SERVER/js/syntaxhighlighter/styles/shCore.css" />').appendTo('head');
			$('<link rel="stylesheet" type="text/css" href="$view->APP_STATIC_SERVER/js/syntaxhighlighter/styles/shThemeDefault.css" />').appendTo('head');
			$('<script type="text/javascript" src="$view->APP_STATIC_SERVER/js/syntaxhighlighter/src/shCore.js"><' + '/script>').prependTo('body');
END;
		$bushes = array(
			'shBrushAS3', 'shBrushBash', 'shBrushColdFusion', 'shBrushCpp', 'shBrushCSharp', 'shBrushCss', 
			'shBrushDelphi', 'shBrushDiff', 'shBrushErlang', 'shBrushGroovy', 'shBrushJava', 'shBrushJavaFX', 
			'shBrushJScript', 'shBrushPerl', 'shBrushPhp', 'shBrushPlain', 'shBrushPowerShell', 'shBrushPython', 
			'shBrushRuby', 'shBrushScala', 'shBrushSql', 'shBrushVb', 'shBrushXml',
		);
		foreach ($bushes as $bush) {
			/*
			 * $view->headScript()->appendFile($view->APP_STATIC_SERVER.'/js/syntaxhighlighter/scripts/'.$bush.'.js');
			 */
$append .= <<<END
			$('<script type="text/javascript" src="$view->APP_STATIC_SERVER/js/syntaxhighlighter/scripts/$bush.js"><' + '/script>').prependTo('body');
END;
		}
		
		$append .= <<<END
			SyntaxHighlighter.all();
    	});
		</script>
END;
		self::$_scriptAttached = true;
		return $content.$append;
	}
}
