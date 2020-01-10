<?php
if( !isset($gCms) ) exit;

if (!$this->CheckPermission('Messages use'))
{
	echo $this->ShowErrors($this->Lang('needpermission'));
	return;
}
//debug_display($_POST, 'Parameters');
if( !empty($_POST) ) {
        if( isset($_POST['cancel']) ) {
            $this->RedirectToAdminTab();
        }
	//on sauvegarde ! Ben ouais !
	//on construit l'intervalle de temps
	
	$this->SetPreference('pageid_messages', $_POST['pageid_messages']);
//	$this->SetTemplate('messages_template', $_POST['messages_template']);
	
	//on redirige !
	$this->RedirectToAdminTab('config');
}
else
{
	
	$tpl = $smarty->CreateTemplate($this->GetTemplateResource('config.tpl'), null, null, $smarty);
	$tpl->assign('pageid_messages', $this->GetPreference('pageid_messages'));
//	$tpl->assign('messages_template', $this->GetTemplate('messages_template'));
	$tpl->display();
		
}
#
# EOF
#
?>