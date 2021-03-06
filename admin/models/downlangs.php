<?php
/**
 * @package   	Egolt Project Publisher
 * @link 		http://www.egolt.com
 * @copyright 	Copyright (C) Egolt www.egolt.com
 * @author    	Soheil Novinfard
 * @license    	GNU/GPL 2
 *
 * Name:			Egolt Project Publisher
 * License:    		GNU/GPL 2
 * Project Page: 	http://www.egolt.com/products/egoltproject
 */
 
defined('_JEXEC') or die;
jimport('joomla.application.component.modellist');

class EgoltProjectModelDownlangs extends JModelList
{

	/**
	 * Constructor.
	 *
	 * @param	array	An optional associative array of configuration settings.
	 * @see		JController
	 * @since	1.6
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'id', 'general.id',
				'hit', 'general.hit',
				'lang_code', 'general.lang_code',
				'user_id', 'general.user_id',
				'pubdate', 'general.pubdate',
				'published', 'general.published',
			);
		}

		parent::__construct($config);
	}


	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since	1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication('administrator');

		// Load the filter state.
		$search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$published = $this->getUserStateFromRequest($this->context.'.filter.published', 'filter_published', '', 'string');
		$this->setState('filter.published', $published);

		$language = $this->getUserStateFromRequest($this->context.'.filter.language', 'filter_language', '');
		$this->setState('filter.language', $language);

		// Load the parameters.
		$params = JComponentHelper::getParams('com_egoltproject');
		$this->setState('params', $params);

		// List state information.
		parent::populateState('general.id', 'DESC');
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param	string		$id	A prefix for the store id.
	 * @return	string		A store id.
	 * @since	1.6
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id.= ':' . $this->getState('filter.search');
		$id.= ':' . $this->getState('filter.published');
		$id.= ':' . $this->getState('filter.language');

		return parent::getStoreId($id);
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return	JDatabaseQuery
	 * @since	1.6
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);
		$user	= JFactory::getUser();

		// Select the required fields from the table.
		$query->select('general.*, general.lang_code as glang');
		$query->from('#__egoltproject_downloads_langs as general');

		// Join over the language Content
		$query->select('dl.title,dl.download_id,dl.lang_code');
		$query->join('LEFT', '`#__egoltproject_downloads_lg` AS dl ON dl.download_id = general.download_id');
		$lang =& JFactory::getLanguage();
		$lang_tag = $lang->getTag();
		$query->where('dl.lang_code = ' . $db->quote($lang_tag));			
		
		// Filter by search in title
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$query->where('general.id = '.(int) substr($search, 3));
			} else {
				$search = $db->Quote('%'.$db->getEscaped($search, true).'%');
				$query->where('(dl.title LIKE '.$search.')');
			}
		}

		// Filter on the language.
		if ($language = $this->getState('filter.language')) {
			$query->where('general.lang_code = ' . $db->quote($language));
		}
		
		// Filter on the published.
		$published = $this->getState('filter.published');
		if (is_numeric($published)) {
			$query->where('general.published = '.(int) $published);
		} elseif ($published === '') {
			$query->where('(general.published IN (0, 1))');
		}

		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering');
		$orderDirn	= $this->state->get('list.direction');

		$query->order($db->getEscaped($orderCol.' '.$orderDirn));

		return $query;
	}
	
	function getLangs()
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('lang_code, title_native');
		$query->from('#__languages');
		$query->order('lang_code ASC');
		$this->_data = $this->_getList($query);
		return $this->_data;
	}
}
