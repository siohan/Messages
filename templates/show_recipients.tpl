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
<h2>Liste des destinataires</h2>
<div class="pageoptions"><p class="pageoptions">{$itemcount}&nbsp;{$itemsfound} &nbsp; {$revenir}</p></div>
{if $itemcount > 0}
{*$form2start*}
<table border="0" cellspacing="0" cellpadding="0" class="pagetable">
 <thead>
	<tr>
		<th>Id</th>
		<th>Destinataire</th>
		<th>Envoyé ?</th>
		<th>Statut</th>	
		<th>Accusé réception</th>
	<!--	<th><input type="checkbox" id="selectall" name="selectall"></th>-->
	</tr>
 </thead>
 <tbody>
{foreach from=$items item=entry}
  <tr class="{$entry->rowclass}">
	<td>{$entry->message_id}</td>
	<td>{$entry->genid}</td>
	<td>{$entry->sent}</td>
	<td>{$entry->status}</td>
	<td>{$entry->ar}</td>
<!--	<td><input type="checkbox" name="{$actionid}sel[]" value="{$entry->id}" class="select"></td>-->
  </tr>
{/foreach}
 </tbody>
</table>
<!--
//SELECT DROPDOWN 
<div class="pageoptions" style="float: right;">
<br/>{*$actiondemasse*}{*$submit_massaction*}
  </div>
-->
{*$form2end*}
{/if}
