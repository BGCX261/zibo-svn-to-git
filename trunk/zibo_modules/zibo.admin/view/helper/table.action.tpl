{if $form->hasField($actionField)}
<div class="actions">    
    <input type="checkbox" id="{$form->getId()}ActionAll" />
    {field form=$form name=$actionField}
</div>
{/if}