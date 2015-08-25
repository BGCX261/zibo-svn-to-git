<script type="text/javascript">
    var {$form->getId()}Messages = new Object();
{assign var="messages" value=$table->getActionConfirmationMessages()}
{if $messages}
    {foreach from=$messages key="label" item="message"}
    {$form->getId()}Messages['{$label}'] = '{$message}';
    {/foreach}
{/if}
    $(function() {ldelim} new ZiboTable('{$form->getId()}', {$form->getId()}Messages, '{"table.label.search"|translate}...'); {rdelim});
</script> 