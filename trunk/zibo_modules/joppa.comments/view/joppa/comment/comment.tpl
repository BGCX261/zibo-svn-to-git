<a name="comment{$comment->id}"></a>

<div class="comment clear">
	<div class="message">
		{$comment->comment|bbcode}
	</div>
	
	<div class="info clear">
{assign var="authorUrl" value=null}
{if $comment->author}
    {assign var="authorUrl" value=$comment->author|contentUrl:"User"}
{/if}
{if !$authorUrl && $comment->email}
    {assign var="authorUrl" value="mailto:`$comment->email`"}
{/if}

{if $authorUrl}
    <a href="{$authorUrl}">{$comment->name}</a>
{else}
    {$comment->name}
{/if}
		-
		{$comment->dateAdded|formatDate}
	{if $replyUrl}
		- <a href="{$replyUrl}{$comment->id}#commentForm">{"joppa.comment.button.reply"|translate}</a>
	{/if}
	{if $editUrl && ($isAdmin || $comment->author->id == $user->getUserId())}
		- <a href="{$editUrl}{$comment->id}#commentForm">{"joppa.comment.button.edit"|translate}</a>
	{/if}
	{if $deleteUrl}
		- <a href="{$deleteUrl}{$comment->id}" onclick="return confirm('{"joppa.comment.label.confirm.delete"|translate}');">{"joppa.comment.button.delete"|translate}</a>
	{/if}
	</div>

	{if $comment->replies}
	<div class="replies">
		{foreach from=$comment->replies item="reply"}
			{include file="joppa//comment/comment" comment=$reply}
		{/foreach}
	</div>
	{/if}
</div>