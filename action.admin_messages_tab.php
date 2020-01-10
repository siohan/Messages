<?php

if( !isset($gCms) ) exit;
if(!$this->CheckPermission('Messages use'))
{
	echo $this->ShowErrors($this->Lang('needpermission'));
	return;
}
$db =& $this->GetDb();
global $themeObject;
$gp_ops = new groups;
$smarty->assign('envoi_sms', 
		$this->CreateLink($id, 'envoi_emails', $returnid,$themeObject->DisplayImage('icons/system/add.gif', 'Envoyer un mail', '', '', 'systemicon')));


		
$dbresult= array ();
//SELECT * FROM ping_module_ping_recup_parties AS rec right JOIN ping_module_ping_joueurs AS j ON j.licence = rec.licence  ORDER BY j.id ASC
$query= "SELECT id,sender, replyto, group_id, senddate, sendtime, subject, message, recipients_number, sent FROM ".cms_db_prefix()."module_messages_messages ORDER BY timbre DESC";

$dbresult= $db->Execute($query);
$rowclass= 'row1';
$rowarray= array ();
if ($dbresult && $dbresult->RecordCount() > 0)
  {
	$mess_ops = new T2t_messages;
    while ($row= $dbresult->FetchRow())
      {
	//$id_envoi = (int) $row['id_envoi'];
	$onerow= new StdClass();
	$onerow->rowclass= $rowclass;
	$sent = $row['sent'];
	$group_id = $gp_ops->details_groupe($row['group_id']);
	
	
	if($sent == '0')
	{
		$onerow->sent= $themeObject->DisplayImage('icons/system/false.gif', $this->Lang('false'), '', '', 'systemicon');
		$onerow->view = $this->CreateLink($id, 'sent_back', $returnid, $themeObject->DisplayImage('icons/topfiles/cmsmailer.gif', 'Renvoyer le mail', '', '', 'systemicon'),array("record_id"=>$row['id']));
	}
	else
	{
		$onerow->sent= $themeObject->DisplayImage('icons/system/true.gif', $this->Lang('true'), '', '', 'systemicon');
		$onerow->view = $this->CreateLink($id, 'show_recipients', $returnid, $themeObject->DisplayImage('icons/system/view.gif', $this->Lang('view'), '', '', 'systemicon'),array("record_id"=>$row['id']));
	}
	
	$onerow->id= $row['id'];
//	$onerow->id_envoi= $row['id_envoi'];
	$onerow->sender= $row['sender'];
	$onerow->senddate= $row['senddate'];
	$onerow->sendtime= $row['sendtime'];
	$onerow->subject= $row['subject'];
	$onerow->group_id = $group_id['nom'];
//	$onerow->nb_recipients = $this->CreateLink($id, 'show_recipients', $returnid, $row['recipients_number'],array("record_id"=>$row['id']));;
	$onerow->nb_errors = $mess_ops->count_errors_per_message($row['id']);
	$onerow->nb_ar = $mess_ops->count_ar_per_message($row['id']);
	
	$onerow->renvoyer = $this->CreateLink($id, 'sent_back', $returnid, $themeObject->DisplayImage('icons/system/new.gif', $this->Lang('new'), '', '', 'systemicon'),array("message_id"=>$row['id']));
	$onerow->edit = $this->CreateLink($id, 'add_edit_message', $returnid, $themeObject->DisplayImage('icons/system/edit.gif', $this->Lang('edit'), '', '', 'systemicon'),array("record_id"=>$row['id']));	
	$onerow->delete= $this->CreateLink($id, 'admin_delete', $returnid, $themeObject->DisplayImage('icons/system/delete.gif', $this->Lang('delete'), '', '', 'systemicon'),array('message_id'=>$row['id']));
	($rowclass == "row1" ? $rowclass= "row2" : $rowclass= "row1");
	$rowarray[]= $onerow;
      }
  }

$smarty->assign('itemsfound', $this->Lang('resultsfoundtext'));
$smarty->assign('itemcount', count($rowarray));
$smarty->assign('items', $rowarray);
$smarty->assign('form2start',
		$this->CreateFormStart($id,'mass_action',$returnid));
$smarty->assign('form2end',
		$this->CreateFormEnd());
$articles = array("Supprimer"=>"delete");
$smarty->assign('actiondemasse',
		$this->CreateInputDropdown($id,'actiondemasse',$articles));
$smarty->assign('submit_massaction',
		$this->CreateInputSubmit($id,'submit_massaction',$this->Lang('apply_to_selection'),'','',$this->Lang('areyousure_actionmultiple')));
echo $this->ProcessTemplate('messages.tpl');


#
# EOF
#
?>