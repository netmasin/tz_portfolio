<?php
/*------------------------------------------------------------------------

# TZ Portfolio Extension

# ------------------------------------------------------------------------

# author    DuongTVTemPlaza

# copyright Copyright (C) 2012 templaza.com. All Rights Reserved.

# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Websites: http://www.templaza.com

# Technical Support:  Forum - http://templaza.com/Forum

-------------------------------------------------------------------------*/

// No direct access
defined('_JEXEC') or die;

class CategoriesHelper
{
	/**
	 * Configure the Submenu links.
	 *
	 * @param	string	The extension being used for the categories.
	 *
	 * @return	void
	 * @since	1.6
	 */
	public static function addSubmenu($extension)
	{
		// Avoid nonsense situation.
		if ($extension == 'com_categories') {
			return;
		}

		$parts = explode('.', $extension);
		$component = $parts[0];

		if (count($parts) > 1) {
			$section = $parts[1];
		}

		// Try to find the component helper.
		$eName	= str_replace('com_', '', $component);
		$file	= JPath::clean(JPATH_ADMINISTRATOR.'/components/'.$component.'/helpers/'.$eName.'.php');

		if (file_exists($file)) {
			require_once $file;

			$prefix	= ucfirst(str_replace('com_', '', $component));
			$cName	= $prefix.'Helper';

			if (class_exists($cName)) {

				if (is_callable(array($cName, 'addSubmenu'))) {
					$lang = JFactory::getLanguage();
					// loading language file from the administrator/language directory then
					// loading language file from the administrator/components/*extension*/language directory
						$lang->load($component, JPATH_BASE, null, false, false)
					||	$lang->load($component, JPath::clean(JPATH_ADMINISTRATOR.'/components/'.$component), null, false, false)
					||	$lang->load($component, JPATH_BASE, $lang->getDefault(), false, false)
					||	$lang->load($component, JPath::clean(JPATH_ADMINISTRATOR.'/components/'.$component), $lang->getDefault(), false, false);
 					call_user_func(array($cName, 'addSubmenu'), 'categories'.(isset($section)?'.'.$section:''));
				}
			}
		}
	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @param	string	$extension	The extension.
	 * @param	int		$categoryId	The category ID.
	 *
	 * @return	JObject
	 * @since	1.6
	 */
	public static function getActions($extension, $categoryId = 0)
	{
		$user		= JFactory::getUser();
		$result		= new JObject;
		$parts		= explode('.', $extension);
		$component	= $parts[0];

		if (empty($categoryId)) {
			$assetName = $component;
		}
		else {
			$assetName = $component.'.category.'.(int) $categoryId;
		}

		$actions = array(
			'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.edit.state', 'core.delete'
		);

		foreach ($actions as $action) {
			$result->set($action, $user->authorise($action, $assetName));
		}

		return $result;
	}
}