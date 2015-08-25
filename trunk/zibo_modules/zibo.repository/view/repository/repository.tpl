<div id="repository">
    <h2>{$title|translate}</h2>

{if $form}
    {form form=$form}
        <div class="module">
            {field form=$form name='module'}
            {field form=$form name='submit'}
            {fieldErrors form=$form name='module'}
        </div>
    {/form}
{/if}

{if $breadcrumbs}
    <div id="breadcrumbs">
        {$breadcrumbs->getHtml()}
    </div>
{/if}

	{include file="helper/table" table=$table}
</div>