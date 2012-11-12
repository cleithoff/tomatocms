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
 * @version 	$Id: TranslatableQuestion.php 4810 2010-08-24 03:36:37Z huuphuoc $
 * @since		2.0.8
 */

class Poll_View_Helper_TranslatableQuestion
{
	const EOL = "\n";
	
	/**
	 * Display select box listing all polls
	 * which haven't been translated from the default language
	 * 
	 * @param $attributes array
	 * @param string $lang
	 * @return string
	 */
	public function translatableQuestion($attributes = array(), $lang = null)
	{
		$defaultLang = Tomato_Config::getConfig()->localization->languages->default;
		if (null == $lang) {
			$lang = $defaultLang;
		}
		
		$conn = Tomato_Db_Connection::factory()->getMasterConnection();
		$questionDao = Tomato_Model_Dao_Factory::getInstance()->setModule('poll')->getQuestionDao();
		$questionDao->setDbConnection($conn);
		$questions = $questionDao->getTranslatable($lang);
		
		$output = sprintf("<select name='%s' id='%s' viewHelperClass='%s' viewHelperAttributes='%s'>", 
							$attributes['name'], $attributes['id'], get_class($this), Zend_Json::encode($attributes)) . self::EOL
				. '<option value=\'{"id": "", "language": ""}\'>---</option>' . self::EOL;
		
		foreach ($questions as $question) {
			$disabled = (0 == (int)$question->translatable
							&& ((0 == (int)$attributes['disabled'] && $question->question_id != (int)$attributes['selected'])
								||
							((int)$attributes['disabled'] == (int)$attributes['selected'])))
						? ' disabled="disabled"' : '';
			$selected = ($disabled == ''
							&& (int)$question->question_id == (int)$attributes['selected'])
						? ' selected="selected"' : '';
			
			$output  .= sprintf("<option value='%s'%s%s>%s</option>", 
								Zend_Json::encode(array('id' => $question->question_id, 'language' => $defaultLang)), 
								$selected,
								$disabled,
								$question->title) . self::EOL;
		}
		$output .= '</select>' . self::EOL;

		return $output;
	}	
}
