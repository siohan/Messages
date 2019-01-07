<?php
if( !isset($gCms) ) exit;
if( !$this->CheckPermission('Modify Site Preferences') ) return;
$this->SetCurrentTab('auto');

if( isset($params['submit']) ) {
	$gettask = cms_utils::get_module('CGJobMgr');
	$job = new cgjobmgr_job($gettask->GetName().' Send Messages',get_userid(FALSE));
	$task = new cgjobmgr_iterativetask('send_messages');
	
	// Specify which function this task will call to actually do the work
	$task->set_function(array('sendTask','execute'));

	// Add the task to the job
	$job->add_task($task);
	$job->save();    
	
	$this->RedirectToTab('mess');
}
else
{
	$smarty->assign('formstart', $this->CreateFormStart($id, 'auto_process_tab', $returnid));
	$smarty->assign('endform', $this->CreateFormEnd());
	$smarty->assign('submit',
			$this->CreateInputSubmit($id, 'submit', 'Démarrez le processus automatique', 'class="button"'));

	echo $this->ProcessTemplate('auto_process.tpl');
}



