<h2>{"database.title.connections"|translate}</h2>

{if $urlAdd}
<ul>
    <li><a href="{$urlAdd}">{"database.action.connection.add"|translate}</a></li>
</ul>
{/if}

{if $table->hasRows()}
<div class="table">
    {include file="helper/table" table=$table}
</div>
<div class="default">
    {include file="database/admin/connection.default" form=$formDefault}
</div>
{else}
    <p class="none">{"database.label.connections.none"|translate}</p>
{/if}

<h3>{"database.title.protocols"|translate}</h3>
<ul class="protocols">
{foreach from=$protocols item="driver" key="protocol"}
    <li><span class="protocol">{$protocol}</span> <span class="driver">{$driver}</span></li>
{/foreach}
</ul>