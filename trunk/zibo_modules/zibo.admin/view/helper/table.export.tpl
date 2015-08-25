{assign var="exportUrl" value=$table->getExportUrl()}
{if $exportUrl && $exportExtensions}
    <div class="export">
        {"table.label.export"|translate}
    {foreach from=$exportExtensions item="extension"}
        <a href="{$exportUrl|replace:"%extension%":$extension}" title="{$extension}">
            {image src="web/images/export.`$extension`.png" alt=$extension}
        </a>
    {/foreach}
    </div>
{/if}