<?php
/**
 * Joomla! component sexypolling
 *
 * @version $Id: view.html.php 2012-04-05 14:30:25 svn $
 * @author 2GLux.com
 * @package Sexy Polling
 * @subpackage com_sexypolling
 * @license GNU/GPL
 *
 */

use Joomla\CMS\HTML\Helpers\Sidebar;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView;

// no direct access
defined('_JEXEC') or die('Restircted access');

// Import Joomla! libraries
jimport( 'joomla.application.component.view');

class SexypollingViewSexytemplates extends HtmlView {
	
	protected $items;
	protected $pagination;
	protected $state;
	
	/**
	 * Display the view
	 *
	 * @return	void
	 */
    public function display($tpl = null) {
    	
    	$this->items		= $this->get('Items');
    	$this->pagination	= $this->get('Pagination');
    	$this->state		= $this->get('State');
 		$styles = $this->get('Styles');
 		
       	if(JV == 'j3') {
    		Sidebar::addFilter(
    				Text::_('JOPTION_SELECT_PUBLISHED'),
    				'filter_published',
    				HTMLHelper::_('select.options', HTMLHelper::_('jgrid.publishedOptions'), 'value', 'text', $this->state->get('filter.published'), true)
    		);
    	}
    	$this->addToolbar();
    	if(JV == 'j3')
    		$this->sidebar = Sidebar::render();
		parent::display($tpl);
    }
    
    /**
     * Add the page title and toolbar.
     *
     * @since	1.6
     */
    protected function addToolbar()
    {
    	JToolBarHelper::addNew('sexytemplate.add');
    	JToolBarHelper::editList('sexytemplate.edit');
	    	
    	JToolBarHelper::divider();
    	JToolBarHelper::publish('sexytemplates.publish', 'JTOOLBAR_PUBLISH', true);
    	JToolBarHelper::unpublish('sexytemplates.unpublish', 'JTOOLBAR_UNPUBLISH', true);
    	JToolBarHelper::deleteList('', 'sexytemplates.delete', 'JTOOLBAR_DELETE');
    }
    
    /**
     * Returns an array of fields the table can be sorted by
     *
     * @return  array  Array containing the field name to sort by as the key and display text as value
     *
     * @since   3.0
     */
    protected function getSortFields()
    {
    	return array(
    			'st.name' => Text::_('COM_SEXYPOLLING_NAME'),
    			'st.published' => Text::_('JSTATUS'),
    			'st.id' => Text::_('JGRID_HEADING_ID')
    	);
    }
}