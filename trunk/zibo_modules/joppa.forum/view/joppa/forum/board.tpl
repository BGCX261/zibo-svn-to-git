<div id="forum">
{if $topicAddAction}
    <ul>
    	<li><a href="{$topicAddAction}">{"joppa.forum.button.topic.add"|translate}</a></li>
    </ul>
{/if}

{if $pages > 1}
	{pagination href=$pageAction page=$page pages=$pages}
{/if}
    <table>
    	<tr>
    		<th class="topic">{"joppa.forum.label.front.topic"|translate}</th>
    		<th class="author">{"joppa.forum.label.front.author"|translate}</th>
    		<th class="numViews">{"joppa.forum.label.front.views"|translate}</th>
    		<th class="numPosts">{"joppa.forum.label.front.posts"|translate}</th>
    		<th class="lastPost">{"joppa.forum.label.front.post.last"|translate}</th>
{if $topicStickyAction || $topicDeleteAction}
    		<th class="actions"></th>
{/if}		
        </tr>
{foreach from=$topics item="topic"}
        <tr class="{cycle values="odd,even"}">
    		<td class="topic">
    			{if $topic->isSticky}
    			{image src="web/images/forum/sticky.png"}&nbsp;
    			{/if}
    			<a class="name" href="{$topicAction}{$topic->id}">{$topic->firstPost->subject}</a>
    			{if $topic->pages > 1}
    				{pagination page=0 pages=$topic->pages href="`$topicAction``$topic->id`/%page%" label="joppa.forum.label.front.pages"|translate}
    			{/if}
    		</td>
    		<td class="author">
    		{if $topic->firstPost->author}
    			<a href="{$_baseUrl}/profile/{$topic->firstPost->author->id}">
    				{$topic->firstPost->author->name}
    			</a>			
			{else}
                {'joppa.forum.label.front.anonymous'|translate}
			{/if}
    		</td>
		<td class="numViews">{if !$topic->numViews}0{else}{$topic->numViews}{/if}</td>
		<td class="numPosts">{if !$topic->numPosts}0{else}{$topic->numPosts}{/if}</td>
		<td class="lastPost">
			<span class="author">
			{if $topic->lastPost->author}
				<a href="{$_baseUrl}profile/{$topic->lastPost->author->id}">				
					{$topic->lastPost->author->name}
				</a>
			{else}
			    {'joppa.forum.label.front.anonymous'|translate}
			{/if}
			</span>
			<span class="date">{$topic->lastPost->dateAdded|formatDate:'forum'}</span>
		</td>
{if $topicStickyAction || $topicDeleteAction}
		<td class="actions">
		{if $topicStickyAction}
			<a href="{$topicStickyAction}{$topic->id}">
				{image src="web/images/forum/sticky.png" alt="joppa.forum.button.sticky"|translate}
			</a>
		{/if}		
		{if $topicDeleteAction}
			<a href="{$topicDeleteAction}{$topic->id}" onclick="return confirm('zeker?');">
				{image src="web/images/forum/delete.png" alt="joppa.forum.button.delete"|translate}
			</a>
		{/if}		
		</td>
{/if}		
	</tr>
{/foreach}
</table>

{if $pages > 1}
	{pagination href=$pageAction page=$page pages=$pages}
{/if}


</div>