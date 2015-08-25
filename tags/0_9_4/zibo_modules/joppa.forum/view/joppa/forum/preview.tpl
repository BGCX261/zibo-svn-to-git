<h4><a href="{$urlForum}">{"joppa.forum.title.preview"|translate}</a></h4>

<ul>
{foreach from=$posts item="post"}
	<li>
		<span class="date">{$post->dateAdded|formatDate:"forum"}</span>
		<a href="{$post->url}" class="post">{$post->topic->firstPost->subject}</a>
		-
		{if $post->author}
            {if $post->author->url}
                <a href="{$post->author->url}" class="profile">{$post->author->name}</a>
            {else}
                <span class="profile">{$post->author->name}</span>
            {/if}
		{else}
            <span class="author">{"joppa.forum.label.front.anonymous"|translate}</span>
		{/if}
	</li>
{/foreach}
</ul>

<a href="{$urlForum}/last" class="last">{"joppa.forum.button.last"|translate}</a>