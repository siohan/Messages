<?php
if(!isset($gCms)) exit;
//on vérifie les permissions
if(!$this->CheckPermission('Messages use'))
{
	echo $this->ShowErrors($this->Lang('needpermission'));
	return;
}
	
	$db = cmsms()->GetDb();
	
	$query = "SELECT id, occurence, timbre, subject, message, priority, ar, relance FROM ".cms_db_prefix()."module_messages_messages WHERE relance = 1";
	$dbresult = $db->Execute($query);
	//on a donc les n licences pour faire la deuxième requete
	//on commence à boucler
	if($dbresult && $dbresult->RecordCount()>0)  //la requete est ok et il y a des résultats
	{
		//on instancie la classe
		
		
		$mess_ops = new T2t_messages;
		$retourid = $this->GetPreference('pageid_messages');
		$cg_ops = new CGExtensions;
		$page = $cg_ops->resolve_alias_or_id($retourid);
		

		while($row = $dbresult->FetchRow())
		{	
			$message_id = $row['id'];
			$occurence = $row['occurence'];
			$timbre = $row['timbre'];
			$subject = $row['subject'];
			$message = $row['message'];
			$priority = $row['priority'];
			$ar = $row['ar'];
			$relance = $row['relance'];

			//on va chercher le dernier envoi
			//on refait une requete pour sélectionner les personnes n'ayant pas confirmé réception
			$query2 = "SELECT recipients, genid FROM ".cms_db_prefix()."module_messages_recipients WHERE message_id = ? AND ar = 0 AND timbre + ? > UNIX_TIMESTAMP()";
			$dbresult2 = $db->Execute($query2, array($message_id, $occurence));
		
			if($dbresult2 && $dbresult2->RecordCount() >0)
			{
				$mess_ops = new T2t_messages;
				while($row2 = $dbresult2->FetchRow())
				{
					 	
						$recipients = $row2['recipients'];
						$genid = $row2['genid'];
						$lien = $this->create_url($id,'default',$page, array("message_id"=>$message_id, "genid"=>$genid));
						$message.= '<p><strong>Merci de bien vouloir confirmer réception de ce message en cliquant sur le lien ci-après : <a href="'.$lien.'" >Je confirme réception</a></p>';
						$message.= '<p><strong>Sans confirmation de réception, ce message se répétera.</strong></p>';
						//$message.= $this->create_url($id,'default',$page, array("message_id"=>$message_id, "genid"=>$genid));
						$priority = 3;
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
							}
						}
		}
		
	
	}
	else
	{
		echo "Erreur ou pas de messages à renvoyer";
	}
	//return true; // Ou false si ça plante
$this->SetMessage('Relances effectuées');
$this->RedirectToAdminTab('mess');	
	
	

      

  
?>