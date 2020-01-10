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
		$onerow->genid= $adh_ops->get_name($row['genid']);
		if($sent == 1)
		{
			$onerow->sent= $themeObject->DisplayImage('icons/system/true.gif', $this->Lang('true'), '', '', 'systemicon');
		}
		else
		{
			$onerow->sent= $themeObject->DisplayImage('icons/system/false.gif', $this->Lang('false'), '', '', 'systemicon');
			$onerow->sent_back= $this->CreateLink($id, 'sent_back_to_user', $returnid,$contents='Renvoyer', array("genid"=>$row['genid'], "record_id"=>$row['message_id']));
		}
		if($ar == 1)
		{
			$onerow->ar= $themeObject->DisplayImage('icons/system/true.gif', $this->Lang('true'), '', '', 'systemicon');
		}
		else
		{
			$onerow->ar= $themeObject->DisplayImage('icons/system/false.gif', $this->Lang('false'), '', '', 'systemicon');
			$onerow->sent_back= $this->CreateLink($id, 'sent_back_to_user', $returnid,$contents='Renvoyer', array("genid"=>$row['genid'], "record_id"=>$row['message_id']));
		}
		
		$onerow->status= $row['status'];
		$onerow->relance= $row['relance'];
		$onerow->envoi= $row['envoi'];
	
	
		($rowclass == "row1" ? $rowclass= "row2" : $rowclass= "row1");
		$rowarray[]= $onerow;
      }
  }

$smarty->assign('itemsfound', $this->Lang('resultsfoundtext'));
$smarty->assign('itemcount', count($rowarray));
$smarty->assign('items', $rowarray);

echo $this->ProcessTemplate('show_recipients.tpl');


#
# EOF
#
?>