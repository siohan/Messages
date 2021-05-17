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
}
else
{
	$this->SetMessage('pas de message_id !');
	$this->RedirectToAdminTab('mess');
}
if(isset($params['submit']))
{
	
}
$db =& $this->GetDb();
global $themeObject;

		
$dbresult= array ();
//SELECT * FROM ping_module_ping_recup_parties AS rec right JOIN ping_module_ping_joueurs AS j ON j.licence = rec.licence  ORDER BY j.id ASC
$query= "SELECT id, message_id, genid, recipients, sent, status, ar, relance, FROM_UNIXTIME(timbre, '%d/%m/%Y %H:%i:%s') AS envoi FROM ".cms_db_prefix()."module_messages_recipients WHERE message_id = ?";

$dbresult= $db->Execute($query, array($message_id));
$rowclass= 'row1';
$rowarray= array ();
if ($dbresult && $dbresult->RecordCount() > 0)
  {
	$adh_ops = new Asso_adherents;
    	while ($row= $dbresult->FetchRow())
      	{

		$sent = $row['sent'];
		$ar = $row['ar'];
		$onerow= new StdClass();
		$onerow->rowclass= $rowclass;
		$onerow->id= $row['id'];
		$onerow->message_id= $row['message_id'];
		$onerow->genid= $row['genid'];
		$onerow->nom= $adh_ops->get_name($row['genid']);
		$onerow->ar= $row['ar'];
		$onerow->status= $row['status'];
		$onerow->sent= $row['sent'];
		$onerow->relance= $row['relance'];
		$onerow->envoi= $row['envoi'];
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
$articles = array("Marqué comme reçu"=>"read", "Marqué comme non reçu"=>"unread");
$smarty->assign('id_message', $this->CreateInputHidden($id,'id_message', $message_id));
$smarty->assign('actiondemasse',
		$this->CreateInputDropdown($id,'actiondemasse',$articles));
$smarty->assign('submit_massaction',
		$this->CreateInputSubmit($id,'submit_massaction',$this->Lang('apply_to_selection'),'','',$this->Lang('areyousure_actionmultiple')));
echo $this->ProcessTemplate('show_recipients.tpl');


#
# EOF
#
?>