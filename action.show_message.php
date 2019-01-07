<?php
if( !isset($gCms) ) exit;
if (!$this->CheckPermission('Messages use'))
{
	echo $this->ShowErrors($this->Lang('needpermission'));
	return;   
}
if(isset($params['record_id']) && $params['record_id'] !='')
{
	$message_id = $params['record_id'];
	$mess_ops = new T2t_messages;
	$message = $mess_ops->details_message($message_id);
}
else
{
	$this->SetMessage('pas de message_id !');
	$this->RedirectToAdminTab('sms');
}

$smarty->assign('revenir', 
		$this->CreateLink($id, 'defaultadmin', $returnid, $contents='<= Revenir', array('activetab'=>'sms')));


	
    
		$onerow= new StdClass();
		$onerow->id= $message['message_id'];
		$onerow->message= $message['message'];
		$onerow->sender = $message['sender'];
		$onerow->senddate = $message['senddate'];
		$onerow->sendtime = $message['sendtime']; 
		$onerow->replyto = $message['replyto'];
		$onerow->group_id = $message['group_id']; 
		$onerow->recipients_number = $message['recipients_number'];
		$onerow->subject = $message['subject'];
		$onerow->message = $message['message'];
		$onerow->sent = $message['sent'];
		$rowarray[]= $onerow;
      
  

$smarty->assign('itemsfound', $this->Lang('resultsfoundtext'));
$smarty->assign('itemcount', count($rowarray));
$smarty->assign('items', $rowarray);

echo $this->ProcessTemplate('show_message.tpl');


#
# EOF
#
?>