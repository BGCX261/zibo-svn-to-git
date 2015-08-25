<div id="podcasts">
	<div class="podcast detail clear">
	   <div class="clear">
    		<div class="image">
    			{image src=$podcast->getImage() thumbnail="crop" width=150 height=150}
    		</div>
    		
    		<div class="container">
    			<h3>{$podcast->title}</h3>
    			
    			{$podcast->teaser}
    			
    			{$podcast->text}
    		</div>
		</div>
			
		{subview name="player"}
			
		{if $downloadUrl}
		<p class="download">
            <a href="{$downloadUrl}">
                <span>{"joppa.podcast.button.download"|translate}</span>
            </a>
        </p>
        {/if}
		
		{if $podcast->author}
			{assign var="author" value=$podcast->author->getUserName()}
		{else}
			{assign var="author" value="joppa.podcast.label.anonymous"|translate}
		{/if}
		<p class="published">{translate key="joppa.podcast.label.published" date=$podcast->datePublication|formatDate author=$author}</p>
	</div>
</div>