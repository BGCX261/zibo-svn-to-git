<h2>{"install.title.requirements"|translate}</h2>

<p>{"install.label.requirements"|translate}</p>

<table class="requirements">
{foreach from=$requirements item="requirement"}
<tr>
    <td class="status"><span class="{if $requirement->isMet()}success{else}fail{/if}"></span></td>
    <td class="info">
        <span class="name">{$requirement->getName($translator)}</span>
        <span class="message">{$requirement->getMessage($translator)}</span>
    </td>
</tr>
{/foreach}
</table>