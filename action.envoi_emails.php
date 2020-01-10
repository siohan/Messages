<?php

if(!isset($gCms)) exit;
//on vérifie les permissions
if(!$this->CheckPermission('Messages use'))
{
	echo $this->ShowErrors($this->Lang('needpermission'));
	return;
}
$db = cmsms()->GetDb();
global $themeObject;
//debug_display($params, 'Parameters');
$aujourdhui = date('Y-m-d');
//$ping = new Ping();
$act = 1;//par defaut on affiche les actifs (= 1 )
$adh_ops = new contact;
$group = $adh_ops->liste_groupes();
$destinataires = array();
$aujourdhui = date('Y-m-d');
$heure_actuelle = date('H:i');
$smarty->assign('formstart',
		    $this->CreateFormStart( $id, 'do_send_emails', $returnid ) );
$smarty->assign('endform', $this->CreateFormEnd());
$smarty->assign('group',
		$this->CreateInputDropdown($id,'group',$group));
$smarty->assign('from', 
		$this->CreateInputText($id, 'from', $this->GetPreference('admin_email'), 50, 200));
$array_priorities = array("Normale"=>"3","Haute"=>"1","Basse"=>"5");
$smarty->assign('priority', $this->CreateInputDropdown($id, 'priority', $array_priorities));
$smarty->assign('senddate',
		$this->CreateInputDate($id,'senddate', (isset($senddate)?$senddate:$aujourdhui)));
$smarty->assign('sendtime',
		$this->CreateInputText($id,'sendtime', (isset($sendtime)?$sendtime:$heure_actuelle)));
$smarty->assign('sujet',
		$this->CreateInputText($id, 'sujet','', 50, 200));		
$smarty->assign('message',
		$this->CreateSyntaxArea($id,$text='','message','', '', '', '', 80, 7));
//$smarty->assign('attachment', $this->CreateInputFile());
$smarty->assign('submit',
		$this->CreateInputSubmit($id, 'submit', $this->Lang('submit'), 'class="button"'));
$smarty->assign('cancel',
		$this->CreateInputSubmit($id,'cancel',
					$this->Lang('cancel')));	

//$query.=" ORDER BY date_compet";
echo $this->ProcessTemplate('envoi_emails.tpl');

?>