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
 
// No direct access
defined('_JEXEC') or die('Direct Access Not Allowed');

jimport('joomla.application.component.view');

class EgoltProjectViewCompats extends JView
{
	protected $items;
	protected $pagination;
	protected $state;

	public function display($tpl = null)
	{
		$this->params		= &JComponentHelper::getParams('com_egoltproject');
		$this->state		= $this->get('State');
		$this->items		= $this->get('Items');
		$this->langs		= $this->get('Langs');
		$this->pagination	= $this->get('Pagination');

		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$this->addToolbar();
		parent::display($tpl);
	}

	protected function addToolbar()
	{
			JToolBarHelper::addNew('compat.add');
			JToolBarHelper::divider();
			JToolBarHelper::publish('compats.publish', 'JTOOLBAR_PUBLISH', true);
			JToolBarHelper::unpublish('compats.unpublish', 'JTOOLBAR_UNPUBLISH', true);
			JToolBarHelper::deleteList(JText::_('COM_EGOLTPROJECT_SUREDELETE'), 'compats.delete');
			JToolBarHelper::divider();
			JToolBarHelper::preferences('com_egoltproject');
	}
}