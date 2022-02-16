<?php // No direct access

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.controller');
$controller = JControllerLegacy::getInstance('PubMedTracker');
 

 
$input = JFactory::getApplication()->input;
$controller->execute($input->getCmd('task'));
  
$controller->redirect();

/*
$doc = JFactory::getDocument();
$doc->addScript('/components/com_pubmedtracker/assets/js/pubmedtracker.js');

echo 'AAAAA';

$db = JFactory::getDBO();
$db->setQuery('select * from #__content LIMIT 10');
$rows = $db->loadAssocList();

var_dump($rows);
*/

?>