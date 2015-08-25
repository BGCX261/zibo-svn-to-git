<div id="forum">
    <table>
    	<tr>
    		<th></th>
    		<th class="numTopics">{"joppa.forum.label.front.topics"|translate}</th>
    		<th class="numPosts">{"joppa.forum.label.front.posts"|translate}</th>
    		<th class="lastPost">{"joppa.forum.label.front.post.last"|translate}</th>
    	</tr>
{foreach from=$categories item="category"}
    	<tr>
    		<td class="category" colspan="4"><a href="{$categoryAction}{$category->id}">{$category->name}</a></td>
    	</tr>
	{foreach from=$category->boards item="board"}
    	<tr class="{cycle values="odd,even"}">
    		<td class="board">
    			<a class="name" href="{$boardAction}{$board->id}">{$board->name}</a>
    			
    			<span class="description">{$board->description}</span>
    			
    			{if $board->moderators}
    			<span class="moderators">{"joppa.forum.label.front.moderators"|translate}: 
    			{foreach from=$board->moderators item="moderator" name="moderators"}{if $moderator->url}<a href="{$moderator->url}">{$moderator->name}</a>{else}{$moderator->name}{/if}{if !$smarty.foreach.moderators.last}, {/if}{/foreach}
    			{/if}
    		</td>
    		<td class="numTopics">{$board->numTopics}</td>
    		<td class="numPosts">{$board->numPosts}</td>
    		<td class="lastPost">
    		{if !$board->lastTopic}
    			---
    		{else}
    			<span class="topic">
    				<a href="{$topicAction}{$board->lastTopic->id}#post{$board->lastTopic->lastPost->id}">{$board->lastTopic->lastPost->subject}</a>
    			</span>
    			<span class="author">
    			{if $board->lastTopic->lastPost->author}
    				<a href="{$_baseUrl}/profile/{$board->lastTopic->lastPost->author->id}">{$board->lastTopic->lastPost->author->name}</a>
				{else}
				    {'joppa.forum.label.front.anonymous'|translate}
				{/if}
    			</span>
    			<span class="date">{$board->lastTopic->lastPost->dateAdded|formatDate:'forum'}</span>
    		{/if}
    		</td>
    	</tr>
	{/foreach}
{/foreach}
    </table>
</div>