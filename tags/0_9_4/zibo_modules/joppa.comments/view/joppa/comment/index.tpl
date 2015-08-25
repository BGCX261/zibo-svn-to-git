<a name="comments"></a>

<h3>{"joppa.comment.title"|translate}</h3>

{if !$comments}
    <p>{"joppa.comment.label.none"|translate}</p>
{else}
    {foreach from=$comments item="comment"}
	   {include file="joppa/comment/comment" comment=$comment}
    {/foreach}
{/if}

{if $form}
	{include file="joppa/comment/form" form=$form}
{/if}