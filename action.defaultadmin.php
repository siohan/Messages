<?php
if ( !isset($gCms) ) exit; 
	if (!$this->CheckPermission('Messages use'))
	{
		echo $this->ShowErrors($this->Lang('needpermission'));
		return;
	}
	
//debug_display($params, 'Parameters');
echo $this->StartTabheaders();
$active_tab = empty($params['active_tab']) ? '' : $params['active_tab'];

	
	echo $this->SetTabHeader('mess', 'Messages', ($active_tab == 'mess')?true:false);
	echo $this->SetTabHeader('config', 'Configuration', ($active_tab == 'config')?true:false);	
	echo $this->EndTabHeaders();

	echo $this->StartTabContent();
	
	
	echo $this->StartTab('mess', $params);
	include(dirname(__FILE__).'/action.admin_messages_tab.php');
   	echo $this->EndTab();

	echo $this->StartTab('config', $params);
	include(dirname(__FILE__).'/action.admin_options_tab.php');
    	echo $this->EndTab();


echo $this->EndTabContent();

?>


