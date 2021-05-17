<?php
class SendTask implements CmsRegularTask
{

//envoi des messages programmés...	
	
	public function get_name()
   	{
      		return get_class();
   	}

   public function get_description() {}
   

   public function test($time = '')
   {

      // Instantiation du module
      $mess = cms_utils::get_module('Messages');
	//$interval =  $mess->GetPreference('LastSendMessage');

      // Récupération de la dernière date d'exécution de la tâche
      if (!$time)
      {
         $time = time();
      }

      $last_execute = $mess->GetPreference('LastSendMessage');
      
      // Définition de la périodicité de la tâche (24h ici)
      if( $time - $last_execute > 900 ) return true; // hardcoded to 15 minutes
      return false;
      
   }

   public function execute($time = '')
   {

      	if (!$time)
      	{
         	$time = time();
      	}

      	$mess = cms_utils::get_module('Messages');
      
      	// Ce qu'il y a à exécuter ici
	
	$db = cmsms()->GetDb();
	
	$query = "SELECT id ,sender, replyto, senddate, sendtime,priority, subject, message,group_id, sent, ar, relance, occurence FROM ".cms_db_prefix()."module_messages_messages WHERE sent = 0 AND timbre < UNIX_TIMESTAMP()";
	$dbresult = $db->Execute($query);
	if($dbresult && $dbresult->RecordCount()>0)  //la requete est ok et il y a des résultats
	{
		//on instancie la classe
		
		
		$mess_ops = new T2t_messages;
		$cg_ops = new CGExtensions;

		while($row = $dbresult->FetchRow())
		{	
		//	on sort les variables
			$sender = $row['sender'];
			$replyto = $row['replyto'];
			$group_id = $row['group_id'];
			$priority = $row['priority'];
			$mess_id = $row['id'];
			$ar = $row['ar'];
			$relance = $row['relance'];
			$occurence = $row['occurence'];
			$envoi = $mess_ops->sent_message($mess_id);
			
			$gp_ops = new groups;
			$recipients_number = $gp_ops->count_users_in_group($group_id);
			
			$message = $row['message'];
			$retourid = $mess->GetPreference('pageid_messages');
			$page = $cg_ops->resolve_alias_or_id($retourid);
			
			//$priority
			$subject = $row['subject'];
		//	$sender = $row['sender'];
		
		
			$adherents = $gp_ops->liste_licences_from_group($group_id);
		
		
			foreach($adherents as $sels)
			{
				//avant on envoie dans le module emails pour tous les utilisateurs et sans traitement

				$query = "SELECT contact FROM ".cms_db_prefix()."module_adherents_contacts WHERE genid = ? AND type_contact = 1 LIMIT 1";
				$dbresult = $db->Execute($query, array($sels));
				$row = $dbresult->FetchRow();

				$email_contact = $row['contact'];
				//var_dump($email_contact);

				if(!is_null($email_contact))
				{
					$destinataires[] = $email_contact;
				}
				
				$lien = $mess->create_url($id,'default',$page, array("message_id"=>$mess_id, "genid"=>$sels));
				/*
				if($ar == 1)
				{
					$message.= '<p><strong>Merci de bien vouloir confirmer réception de ce message en cliquant sur le lien ci-après : <a href="'.$lien.'" >Je confirme réception</a></p>';
				}
				if($relance == 1)
				{
					$message.= '<p><strong>Sans confirmation de réception, ce message se répétera.</strong></p>';
				}
				*/
				$montpl = $mess->GetTemplateResource('tpl_messages.tpl');						
				$smarty = cmsms()->GetSmarty();
				// do not assign data to the global smarty
				$tpl = $smarty->createTemplate($montpl);
				$tpl->assign('lien',$lien);
				$tpl->assign('message',$message);
				$tpl->assign('ar', $ar);
				$tpl->assign('relance', $relance);
			 	$output = $tpl->fetch();
				
			}	
			
			
			foreach($destinataires as $item=>$v)
			{

			//var_dump($item);
				//on crée un lien de confirmation si nécessaire
				
				
				$cmsmailer = new \cms_mailer();
				$cmsmailer->reset();
			//	$cmsmailer->SetFrom($sender);//$this->GetPreference('admin_email'));
				$cmsmailer->AddAddress($v,$name='');
				$cmsmailer->IsHTML(true);
				$cmsmailer->SetPriority($priority);
				$cmsmailer->SetBody($output);
				$cmsmailer->SetSubject($subject);
				
		                if( !$cmsmailer->Send() ) 
				{			
		                    	$mess_ops->not_sent_emails($mess_id, $v);
					$this->Audit('',$this->GetName(),'Problem sending email to '.$v);
		                }
				else
				{
					$mess_ops->sent_email($mess_id,$v);
				}
			}
		}
		
		return true; // Ou false si ça plante	
	}
	else
	{
		return false;
	}
	//return true; // Ou false si ça plante
	
	
	

      

   }

   public function on_success($time = '')
   {

      if (!$time)
      {
         $time = time();
      }
      
      $mess = cms_utils::get_module('Messages');
      $mess->SetPreference('LastSendMessage', $time);
      $mess->Audit('','Messages','Messages Ok');
      
   }

   public function on_failure($time = '')
   {
      $mess = cms_utils::get_module('Messages');
	$mess->Audit('','Messages','Messages KO');
   }

}
?>