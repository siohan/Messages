<?php
if (!isset($gCms)) exit;
if(!$this->CheckPermission('Messages use'));
if(!isset($params['message_id']) || $params['message_id'] == '')
{
	//redir
	$this->SetMessage('Il manque un parametre');
	}
else
{
	$message_id = $params['message_id'];
	//on va supprimer le message et les recipients
	$mess_ops = new T2t_messages;
	$del_mess = $mess_ops->delete_message($message_id);
	if(true === $del_mess)
	{
		//on supprime aussi les message de la table recipients
		$del_recip = $mess_ops->delete_recipients($message_id);
	}
	$this->SetMessage('Message supprimé');
}
$this->RedirectToAdminTab('mess');