<ul>
{foreach from=$logs item="log"}
    {assign var="modelLog" value=$log.log}
    <li>
        {if $log.table->hasRows()}
            <a href="#">{if $modelLog->user}{translate key="orm.label.change.user" version=$modelLog->dataVersion date=$modelLog->dateAdded|formatDate time=$modelLog->dateAdded|formatDate:"H:i:s" user=$modelLog->user}{else}{translate key="orm.label.change" version=$modelLog->dataVersion date=$modelLog->dateAdded|formatDate time=$modelLog->dateAdded|formatDate:"H:i:s"}{/if}</a>
            {include file="helper/table" table=$log.table}
        {else}
            {if $modelLog->user}{translate key="orm.label.change.user" version=$modelLog->dataVersion date=$modelLog->dateAdded|formatDate time=$modelLog->dateAdded|formatDate:"H:i:s" user=$modelLog->user}{else}{translate key="orm.label.change" version=$modelLog->dataVersion date=$modelLog->dateAdded|formatDate time=$modelLog->dateAdded|formatDate:"H:i:s"}{/if}        
        {/if}
    </li>
    {/foreach}
</ul>