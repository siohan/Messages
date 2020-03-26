<?php
//ce fichier fait des actions de masse, il est appelé depuis l'onglet de récupération des infos sur les joueurs
if( !isset($gCms) ) exit;
debug_display($params, 'Parameters');
//var_dump($params['sel']);
$db =& $this->GetDb();
if (isset($params['submit_massaction']) && isset($params['actiondemasse']) )
  {
     if( isset($params['sel']) && is_array($params['sel']) &&
	count($params['sel']) > 0 )
      	{
        	$mess_ops = new T2t_messages;
		switch($params['actiondemasse'])
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
			$this->SetMessage('Messages effacés');
			$this->RedirectToAdminTab('mess');
			break;
			
			//marque les messages comme lus
			case "read" :
			foreach( $params['sel'] as $message_id )
	  		{
	    			$mess = $mess_ops->ar($message_id);
				
	  		}
			$this->SetMessage('Marqués comme lus et donc reçus');
			$this->Redirect($id, 'show_recipients', $returnid, array("record_id"=>$params['id_message']));
			break;
			
			case "unread" :
			foreach( $params['sel'] as $record_id )
	  		{
	    			$mess = $mess_ops->not_ar($record_id);

	  		}
			$this->SetMessage('Marqués comme non lus');
			$this->Redirect($id, 'show_recipients', $returnid, array("record_id"=>$params['id_message']));
			break;
			//marque les messages comme non lus
			case "not_sent" :
			foreach( $params['sel'] as $message_id )
	  		{
	    			$mess = $mess_ops->not_sent_to_recipients($message_id);
				
	  		}
			$this->SetMessage('Marqués comme non envoyés et non reçus');
			$this->Redirect($id, 'show_recipients', $returnid, array("record_id"=>$params['id_message']));
			break;
			
			case "sent" :
			foreach( $params['sel'] as $message_id )
	  		{
	    			$mess = $mess_ops->sent_to_recipients($message_id);
				
	  		}
			$this->SetMessage('Marqués comme  envoyés');
			$this->Redirect($id, 'show_recipients', $returnid, array("record_id"=>$params['id_message']));
			break;
			
			
			
	
      		}//fin du switch
  	}
	else
	{
		$this->SetMessage('PB de sélection de masse !!');
		$this->RedirectToAdminTab('recuperation');
	}
}
/**/
#
# EOF
#
?>