<?php
if ( !isset($gCms) ) exit; 
	if (!$this->CheckPermission('Messages use'))
	{
		echo $this->ShowErrors($this->Lang('needpermission'));
		return;
	}
	
//debug_display($params, 'Parameters');
echo $this->StartTabheaders();

if(isset($params['activetab']) && !empty($params['activetab']))
  {
    $tab = $params['activetab'];
  } else {
  $tab = 'mess';
 }	
	
	echo $this->SetTabHeader('mess', 'Messages', ('mess' == $tab)?true:false);
	echo $this->SetTabHeader('auto', 'Automatisation', ('auto' == $tab)?true:false);	
//	echo $this->SetTabHeader('config', 'Configuration', ('config' == $tab)?true:false);	
	echo $this->EndTabHeaders();

	echo $this->StartTabContent();
	
	
	echo $this->StartTab('mess', $params);
	include(dirname(__FILE__).'/action.admin_messages_tab.php');
   	echo $this->EndTab();

	echo $this->StartTab('auto', $params);
	include(dirname(__FILE__).'/action.auto_process_tab.php');
    	echo $this->EndTab();
/*
	echo $this->StartTab('config', $params);
	include(dirname(__FILE__).'/action.admin_config_tab.php');
    	//include(dirname(__FILE__).'/action.admin_joueurs_tab.php');
   	echo $this->EndTab();
*/

echo $this->EndTabContent();

?>


