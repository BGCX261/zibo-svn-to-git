<div id="podcasts">
{if !$pages}
    <p>{"joppa.podcast.label.none"|translate}</p>
{else}
	{pagination href=$pageUrl page=$page pages=$pages}
	
    {foreach from=$podcasts item="podcast" name="podcasts"}
        {assign var="podcastUrl" value=$podcast|contentUrl:"Podcast"}
        
	<div class="podcast {cycle values="odd,even"} clear">
		<div class="image">
			<a href="{$podcastUrl}">
				{image src=$podcast->getImage() thumbnail="crop" width=90 height=90}
			</a>
		</div>
		<div class="container">
			<h3>
				<a href="{$podcastUrl}">{$podcast->title}</a>
			</h3>
			{$podcast->teaser}
		</div>
	</div>
	
    {/foreach}

	{pagination href=$pageUrl page=$page pages=$pages}
{/if}
</div>