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
	$message_id = $params['record_id'];
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
	$details_recip = $mess_ops->details_message_recipients($message_id, $genid);
	$message = $details['message'];
	$ar_message = $details['ar'];
	$relance = $details['relance'];
	$occurence = $details['occurence'];
	
	//on envoie le message
	$gp_ops = new groups;
	
	
	$contacts_ops = new contact;
	$adherents = $contacts_ops->UsersFromGroup($details['group_id']);

//	var_dump($adherents);
	
		$query = "SELECT contact FROM ".cms_db_prefix()."module_adherents_contacts WHERE genid = ? AND type_contact = 1 LIMIT 1";
		$dbresult = $db->Execute($query, array($genid));
		$row = $dbresult->FetchRow();

		$email_contact = $row['contact'];
		//var_dump($email_contact);

		if(!is_null($email_contact))
		{
			
				$senttouser = 1;
				$status = "Email Ok";
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
			$add_to_recipients = $mess_ops->add_messages_to_recipients($message_id, $genid, $email_contact,$details_recip['message'],$senttouser,$status, $ar);
		}
		/*
	//	var_dump($add_to_recipients);
		$retourid = $this->GetPreference('pageid_messages');
		$cg_ops = new CGExtensions;
		$page = $cg_ops->resolve_alias_or_id($retourid); 
		$lien = $this->create_url($id,'default',$page, array("message_id"=>$message_id, "genid"=>$genid));
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
		*/
		$cmsmailer = new \cms_mailer();
		
	//	$cmsmailer->SetFrom($details['sender']);//$this->GetPreference('admin_email'));
		$cmsmailer->AddAddress($details_recip['recipients']);
		$cmsmailer->IsHTML(true);
		$cmsmailer->SetPriority($details['priority']);
		$cmsmailer->SetBody($details_recip['message']);
		$cmsmailer->SetSubject($details['subject']);
		
                if( !$cmsmailer->Send() ) 
		{			
                    	$mess_ops->not_sent_emails($message_id, $details_recip['recipients']);
			$this->Audit('',$this->GetName(),'Problem sending email to '.$email_contact);

                }
		$cmsmailer->reset();
	$this->Redirect($id, 'show_recipients', $returnid, array("record_id"=>$message_id));
}
else
{
	//message + redirection
}