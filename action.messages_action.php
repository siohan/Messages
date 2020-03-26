<?php
//ce fichier fait des actions de masse, il est appelé depuis l'onglet de récupération des infos sur les joueurs
if( !isset($gCms) ) exit;
debug_display($params, 'Parameters');
//var_dump($params['sel']);
$db =& $this->GetDb();
$mess_ops = new T2t_messages;

		switch($params['obj'])
		{
			case "delete" :
			foreach( $params['sel'] as $message_id )
	  		{
	    			$mess = $mess_ops->delete_message($message_id );
				if(true === $mess)
				{
					$mess_ops->delete_recipients($message_id);
				}
	  		}
			$this->SetMessage('Joueurs désactivés');
			$this->RedirectToAdminTab('joueurs');
			break;
			//confirme la réception d'un email
			case "confirmed":
			
			if(isset($params['message_id']) && $params['message_id'] != '')
			{
				$message_id = $params['message_id'];
			}
			if(isset($params['record_id']) && $params['record_id'] != '')
			{
				$record_id = $params['record_id'];
			}
			
			$ar = $mess_ops->ar($record_id);
			if(true == $ar)
			{
				$this->SetMessage('Marqué comme lu');
			}
			else
			{
				$this->SetMessage('Marqué comme non lu');
			}
			$this->Redirect($id, 'show_recipients', $returnid, array("record_id"=>$message_id));
			break;
			
	
      		}//fin du switch
  	

#
# EOF
#
?>