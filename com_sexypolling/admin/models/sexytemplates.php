<?php
/**
 * Joomla! component sexypolling
 *
 * @version $Id: sexytemplates.php 2012-04-05 14:30:25 svn $
 * @author 2GLux.com
 * @package Sexy Pollinga
 * @subpackage com_sexypolling
 * @license GNU/GPL
 *
 * Extended by:
 * @version v3.0.0
 * @author Jefferson49
 * @link https://github.com/Jefferson49/Joomla_plugin_sexypolling_reloaded
 * @copyright Copyright (c) 2022 - 2025 Jefferson49
 * @license GNU/GPL v3.0
 * 
 */

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\DatabaseQuery;

// no direct access
defined('_JEXEC') or die('Restircted access');

class SexypollingModelSexyTemplates extends ListModel{

	/**
	 * Constructor.
	 *
	 * @param	array	An optional associative array of configuration settings.
	 * @see		BaseController
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
					'id', 'st.id',
					'name', 'st.name',
					'published', 'st.published',
					'publish_up', 'st.publish_up',
					'publish_down', 'st.publish_down'
			);
		}

		parent::__construct($config);
	}


	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return	void
	 * @since	1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = Factory::getApplication();

		// Adjust the context to support modal layouts.
		if ($layout = Factory::getApplication()->input->get('layout')) {
			$this->context .= '.'.$layout;
		}

		$search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$published = $this->getUserStateFromRequest($this->context.'.filter.published', 'filter_published', '');
		$this->setState('filter.published', $published);

		// List state information.
		parent::populateState('st.name', 'asc');
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param	string		$id	A prefix for the store id.
	 *
	 * @return	string		A store id.
	 * @since	1.6
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':'.$this->getState('filter.search');
		$id	.= ':'.$this->getState('filter.published');

		return parent::getStoreId($id);
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return	DatabaseQuery
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db		= $this->getDatabase();
		$query	= $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
				$this->getState(
						'list.select',
						'st.id, st.name, st.published, st.publish_up, st.publish_down'
				)
		);
			
		$query->from('#__sexy_templates AS st');

		// Filter by published state
		$published = $this->getState('filter.published');
		if (is_numeric($published)) {
			$query->where('st.published = ' . (int) $published);
		}
		elseif ($published === '') {
			$query->where('(st.published = 0 OR st.published = 1)');
		}

		// Filter by search in name.
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$query->where('st.id = '.(int) substr($search, 3));
			}
			else {
				$search = $db->Quote('%'.$db->escape($search, true).'%');
				$query->where('(st.name LIKE '.$search.')');
			}
		}

		// Add the list ordering clause.
		$orderCol	= $this->getState()->get('list.ordering', 'st.name');
		$orderDirn	= $this->getState()->get('list.direction', 'asc');
		/*
		 if ($orderCol == 'a.ordering' || $orderCol == 'category_title') {
		$orderCol = 'c.title '.$orderDirn.', a.ordering';
		}
		*/
		$query->order($db->escape($orderCol.' '.$orderDirn));
		
		return $query;
	}
}