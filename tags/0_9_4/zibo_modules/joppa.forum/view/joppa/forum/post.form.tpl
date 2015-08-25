{if $title}
	<h3>{$title|translate}</h3>
{/if}

{form form=$form}	
	{field form=$form name="id"}
	{fieldErrors form=$form name="id"}

{if $preview}
	<div class="preview">
		<label>{'joppa.forum.label.front.preview'|translate}</label>
		<div class="previewMessage">
		{$preview->message|bbcode|emoticons:$emoticonParser}
		</div>
	</div>
{/if}
	
	<div class="subject">
		<label for="{fieldId form=$form name="subject"}">{"joppa.forum.label.front.subject"|translate}</label>
		{field form=$form name="subject"}
		{fieldErrors form=$form name="subject"}
	</div>
	
	<div class="message">
		<label for="{fieldId form=$form name="message"}">{"joppa.forum.label.front.message"|translate}</label>
		{field form=$form name="message"}
		{fieldErrors form=$form name="message"}
	</div>

	<div class="submit">
		{field form=$form name="preview"}
		{field form=$form name="submit"}
		{field form=$form name="cancel"}
	</div>
{/form}