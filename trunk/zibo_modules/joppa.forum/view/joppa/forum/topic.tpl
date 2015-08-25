<div id="forum">

{if $title}
	<h3>{$title}</h3>
{/if}

{if $postAddAction}
	<ul>
		<li><a href="{$postAddAction}">{"joppa.forum.button.topic.reply"|translate}</a></li>
	</ul>
{/if}

{if $pages > 1}
	{pagination page=$page pages=$pages href=$pageUrl}
{/if}

<div class="posts">
{foreach from=$posts item="post"}
	<a name="post{$post->id}"></a>
	<div class="post {cycle values="odd,even"} clear">
		<div class="info">
		{if $postAddAction}
			<div class="actions">
			{if $postEditAction && $profile && ($isModerator || $post->author->id == $profile->id)}
			<a href="{$postEditAction}/{$post->id}" class="edit">
				{"button.edit"|translate}
			</a>
			|
			{/if}
			<a href="{$postAddAction}/{$post->id}" class="quote">
				{"joppa.forum.button.post.quote"|translate}
			</a>
			</div>
		{/if}
			<span class="subject">
			{if $post->url}
				<a href="{$post->url}">{$post->subject}</a>
			{else}
				{$post->subject}
			{/if}
			</span>
			<span class="date">{$post->dateAdded|formatDate:"forum"}</span>
		</div>
		<div class="author">
		{if $post->author}
			<div class="image">
				<a href="{$_baseUrl}profile/{$post->author->id}">
					{* {image src=$post->author->getImage() thumbnail="crop" width=50 height=50} *}
				</a>
			</div>
			<div class="name">
				<a href="{$_baseUrl}profile/{$post->author->id}">
					<span class="author">{$post->author->name}</span>
				</a>
			</div>
			<div class="ranking">
				<div class="stars">
				{section name=stars start=0 loop=$post->author->ranking->stars step=1}
					<img src="web/images/star.png" />
				{/section}
				</div>
				<div class="name">
					{$post->author->ranking->name}
				</div>
			</div>
			<div class="community">
				<a href="pm/send/{$post->author->id}">
					<img src="web/images/pm.png" alt="PM" title="{"joppa.forum.button.pm"|translate}" />
				</a>
				{if $post->author->msn}
					<img src="web/images/msn.png" alt="MSN" title="MSN" />
				{/if}
				{if $post->author->myspace}
					<a href="{$post->profile->myspace}">
						<img src="web/images/myspace.png" alt="MySpace" title="MySpace" />
					</a>
				{/if}
				{if $post->author->facebook}
					<a href="{$post->profile->facebook}">
						<img src="web/images/facebook.png" alt="Facebook" title="Facebook" />
					</a>
				{/if}
			</div>
			<div class="info">
				{if $post->author->gender}
				<div>
				{"joppa.forum.label.gender"|translate}: {"joppa.forum.gender.`$post->author->gender`"|translate}
				<img src="web/images/{$post->author->gender}.png" />
				</div>
				{/if}
				{if $post->author->location}
				<div>
				{"joppa.forum.label.location"|translate}: {$post->author->location}
				</div>
				{/if}
				{if isset($post->author->user->dateAdded)}
				<div>
				{"joppa.forum.label.joined"|translate}: {$post->author->user->dateAdded|formatDate}
				</div>
				{/if}
				<div>
				{"joppa.forum.label.posts"|translate}: {$post->author->numPosts}
				</div>
			</div>
			{else}
            <div class="name">
                <span class="author">{"joppa.forum.label.front.anonymous"|translate}</span>
            </div>			
			{/if}
		</div>
		<div class="message">
			<div class="text">
				{$post->message|bbcode|emoticons:$emoticonParser}
			</div>
			{if $post->dateModified && $post->authorModified}
			<div class="modified">
				{assign var="authorModified" value="<a href=\"`$_baseUrl`profile/`$post->authorModified->id`\">`$post->authorModified->name`</a>"}
				{translate key="joppa.forum.label.edited" date=$post->dateModified|formatDate:"forum" author=$authorModified}
			</div>
			{/if}
			{if $post->author && $post->author->signature}
			<div class="signature">
				{$post->author->signature|bbcode}
			</div>
			{/if}
		</div>
	</div>
{/foreach}
</div>

{if $pages > 1}
	{pagination page=$page pages=$pages href=$pageUrl}
{/if}

{if $postAddAction}
	<ul>
		<li><a href="{$postAddAction}">{"joppa.forum.button.topic.reply"|translate}</a></li>
	</ul>
{/if}
</div>
