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
debug_display($params, 'Parameters');
$error = 0; //on instancie un compteur d'erreurs
if(isset($params['record_id']) && $params['record_id'] != '')
{
	$message_id = (int) $params['record_id'];
}
else
{
	$error++;
}
if(isset($params['genid']) && $params['genid'] != '')
{
	$genid = $params['genid'];
}
else
{
	$error++;
}
if($error <1)
{
	$mess_ops = new T2t_messages;
	$details = $mess_ops->details_message($message_id);//les détails du message générique
	//var_dump($details);
	$message = $details['message'];
	$ar_message = $details['ar'];
	$relance = $details['relance'];
	$occurence = $details['occurence'];
	
	//on envoie le message
	$gp_ops = new groups;
	$cont_ops = new contact;
	
	$email_contact = $cont_ops->email_address($genid);


	if(!is_null($email_contact))
	{
		
			$senttouser = 1;
			$status = "Ok";
			$ar = 0;
	}
	else
	{
		//on indique l'erreur : pas d'email disponible !
			$senttouser = 0;
			$status = "Email absent";
			$ar = 0;
			$email_contact = "rien";
	}

	//echo $status.'<br />';	
	//on vérifie si le message a déjà été envoyé (update) ou non (insert)
	$has_been_sent  = $mess_ops->is_already_sent($message_id, $genid);
	if(true == $has_been_sent)
	{
		$mess_ops->update_recipients_message($message_id, $genid);
	}
	else
	{
		$add_to_recipients = $mess_ops->add_messages_to_recipients($message_id, $genid, $email_contact,$message,$senttouser,$status, $ar);
	}
	$lien='';	
	//	var_dump($add_to_recipients);
	if($ar_message == "1")
	{
		$retourid = $this->GetPreference('pageid_messages');
		$cg_ops = new CGExtensions;
		$page = $cg_ops->resolve_alias_or_id($retourid); 
		$lien = $this->create_url($id,'default',$page, array("message_id"=>$message_id, "genid"=>$genid));
	}
	
		$montpl = $this->GetTemplateResource('tpl_messages.tpl');	
		$smarty = cmsms()->GetSmarty();
		// do not assign data to the global smarty
		$tpl = $smarty->createTemplate($montpl);
		$tpl->assign('lien',$lien);
		$tpl->assign('ar', $ar_message);
		$tpl->assign('relance', $relance);
		$tpl->assign('occurence', $occurence);
		$tpl->assign('message',$message);
	 	$output = $tpl->fetch();
		
		$cmsmailer = new \cms_mailer();
		
		try
			{
				$cmsmailer->SetFromName($details['sender']);//$this->GetPreference('admin_email'));
				$cmsmailer->AddAddress($email_contact);
				$cmsmailer->IsHTML(true);
				$cmsmailer->SetPriority($details['priority']);
				$cmsmailer->SetBody($output);
				$cmsmailer->SetSubject($details['subject']);
				$cmsmailer->Send();
				$mess_ops->sent_email($message_id, $genid);
			}
			catch (phpmailerException $e) 
			{
				
				$status = $e->errorMessage();
				$mess_ops->not_sent_emails($message_id, $genid, $status);
			} 
			catch (Exception $e) 
			{
				echo $e->getMessage(); //Boring error messages from anything else!
			}
	$this->Redirect($id, 'show_recipients', $returnid, array("record_id"=>$message_id));
}
else
{
	$this->SetMessage('Paramètre(s) manquant(s) !');
	$this->RedirectToAdminTab('messages');//($id, 'show_recipients', $returnid, array("record_id"=>$message_id));
}