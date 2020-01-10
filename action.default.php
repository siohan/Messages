<?php
if(!isset($gCms)) exit;
$db = cmsms()->GetDb();
//debug_display($params, 'Parameters');
$error = 0; //on instancie un compteur d'erreur

if(isset($params['message_id']) && $params['message_id'] !='')
{
	$message_id = $params['message_id'];
}
else
{
	$error++;
}
if(isset($params['genid']) && $params['genid'] !='')
{
	$genid = $params['genid'];
	//on vérifie si le genid correspond bien à qqun ds la base	
	$asso_adh = new Asso_adherents;
//	$adh_exists
	$details = $asso_adh->details_adherent_by_genid($genid);
	$prenom = $details['prenom'];
	$smarty->assign('prenom', $prenom);
}
else
{
	$error++;
}


if($error < 1)
{
	$db = cmsms()->GetDb();
	$query = "UPDATE ".cms_db_prefix()."module_messages_recipients SET ar = 1 WHERE message_id = ? AND genid = ? ";
	$dbresult = $db->Execute($query, array($message_id, $genid));
	if($dbresult)
	{
		echo "Merci ".$prenom." d'avoir confirmé réception !";
	}
	else
	{
		echo 'une erreur est apparue !!';		
	}
			
}
else
{
	echo 'une erreur est apparue 2 !!';
}
?>
