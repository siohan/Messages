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

$smarty->assign('revenir', 
		$this->CreateLink($id, 'defaultadmin', $returnid, $contents='<= Revenir', array('activetab'=>'sms')));

		
$dbresult= array ();
//SELECT * FROM ping_module_ping_recup_parties AS rec right JOIN ping_module_ping_joueurs AS j ON j.licence = rec.licence  ORDER BY j.id ASC
$query= "SELECT id, message_id, genid, recipients, sent, status, ar FROM ".cms_db_prefix()."module_messages_recipients WHERE message_id = ?";

$dbresult= $db->Execute($query, array($message_id));
$rowclass= 'row1';
$rowarray= array ();
if ($dbresult && $dbresult->RecordCount() > 0)
  {
	$adh_ops = new adherents_spid;
	$ping_ops = new adherents_spid;
    	while ($row= $dbresult->FetchRow())
      	{

		$onerow= new StdClass();
		$onerow->rowclass= $rowclass;
		$onerow->id= $row['id'];
		$onerow->message_id= $row['message_id'];
		$onerow->genid= $ping_ops->get_name($row['genid']);
		$onerow->sent= $row['sent'];
		$onerow->status= $row['status'];
		$onerow->ar= $row['ar'];
		$onerow->recipients =$adh_ops->get_name($row['recipients']);
		/*
		if($ardate == '' || TRUE === is_null($ardate))
		{
			$onerow->ardate = $themeObject->DisplayImage('icons/system/false.gif', $this->Lang('False'), '', '', 'systemicon');//$row['ardate'];
			$onerow->artime = '';//$row['artime'];
		}
		else
		{
			$onerow->ardate = $row['ardate'];
			$onerow->artime = $row['artime'];
		}
		*/
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