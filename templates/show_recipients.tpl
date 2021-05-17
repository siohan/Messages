<script type="text/javascript">
//<![CDATA[
$(document).ready(function(){
  $('#selectall').click(function(){
    var v = $(this).attr('checked');
    if( v == 'checked' ) {
      $('.select').attr('checked','checked');
    } else {
      $('.select').removeAttr('checked');
    }
  });
  $('.select').click(function(){
    $('#selectall').removeAttr('checked');
  });
  $('#toggle_filter').click(function(){
    $('#filter_form').toggle();
  });
  {if isset($tablesorter)}
  $('#articlelist').tablesorter({ sortList:{$tablesorter} });
  {/if}
});
//]]>
</script>
<h2>Liste des destinataires du message</h2>
<p><a href="{cms_action_url action='defaultadmin'}">{admin_icon icon='back.gif'} Revenir</a></p>
<div class="pageoptions"><p class="pageoptions">{$itemcount}&nbsp;{$itemsfound} &nbsp; </p></div>
{if $itemcount > 0}
{$form2start}
<table border="0" cellspacing="0" cellpadding="0" class="pagetable">
 <thead>
	<tr>
		<th>Id</th>
		<th>Destinataire</th>
		<th>Envoyé ?</th>
		<th>Statut</th>	
		<th>Réception confirmée ?</th>
		<th>Nb relances</th>
		<th>Dernier envoi</th>
		<th>Action(s)</th>
		<th><input type="checkbox" id="selectall" name="selectall"></th>
	</tr>
 </thead>
 <tbody>
{foreach from=$items item=entry}
  <tr class="{$entry->rowclass}">
	<td>{$entry->id}</td>
	<td>{$entry->nom}</td>
	<td>{if $entry->sent =="1"}{admin_icon icon="true.gif"}{else}{admin_icon icon="false.gif"}{/if}</td>
	<td>{$entry->status}</td>
	<td>{if $entry->ar =="0"}<a href="{cms_action_url action='messages_action' obj=confirmed record_id=$entry->id message_id=$entry->message_id}">{admin_icon icon="false.gif"}{else}{admin_icon icon="true.gif"}{/if}</td>
	<td>{$entry->relance}</td>
	<td>{$entry->envoi}</td>
  <td>{if $entry->sent == "0" || $entry->ar == "0"}<a href="{cms_action_url action='sent_back_to_user' genid=$entry->genid record_id=$entry->message_id}">Renvoyer</a>{/if}</td>
<td><input type="checkbox" name="{$actionid}sel[]" value="{$entry->id}" class="select"></td>
  </tr>
{/foreach}
 </tbody>
</table>

<!--SELECT DROPDOWN -->
<div class="pageoptions" style="float: right;">
<br/>{$actiondemasse}{$submit_massaction}
  </div>

{$form2end}
{/if}
