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
<h2>Liste des messages</h2>
<a href="{cms_action_url action=add_edit_message}">{admin_icon icon='newobject.gif'}Ajouter un message</a>
<a href="{cms_action_url action=relance_messages}">{admin_icon icon='newobject.gif'}Relancer les messages non confirmés</a>
<div class="pageoptions"><p class="pageoptions">{$itemcount}&nbsp;{$itemsfound} &nbsp;</p></div>
{if $itemcount > 0}
{$form2start}
<table border="0" cellspacing="0" cellpadding="0" class="pagetable">
 <thead>
	<tr>
		<th>Id</th>
		<th>Expéditeur</th>
		<th>Date envoi </th>
		<th>Sujet</th>
		<th>Destinataires</th>
		<th>Envoyé ?</th>
		<th>Erreur(s)</th>
		<th>Accusé(s)</th>
		<th colspan="3">Action(s)</th>
		<th><input type="checkbox" id="selectall" name="selectall"></th>
	</tr>
 </thead>
 <tbody>
{foreach from=$items item=entry}
  <tr class="{$entry->rowclass}">
	<td>{$entry->id}</td>
	<td>{$entry->sender}</td>
	<td>{$entry->senddate|date_format:"%d-%m-%Y"} - {$entry->sendtime}</td>
	<td>{$entry->subject}</td>
	<td>{$entry->group_id}</td>
	<td>{$entry->sent}</td>
	<td>{$entry->nb_errors}</td>
	<td>{$entry->nb_ar}</td>
	<td>{$entry->view}</td>
	<td>{$entry->edit}</td>
	<td>{$entry->delete}</td> 
	<td><input type="checkbox" name="{$actionid}sel[]" value="{$entry->id}" class="select"></td>
  </tr>
{/foreach}
 </tbody>
</table>
<!-- SELECT DROPDOWN -->
<div class="pageoptions" style="float: right;">
<br/>{$actiondemasse}{$submit_massaction}
  </div>
{$form2end}
{/if}
