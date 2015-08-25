<div id="repository">
	<h2>{$module->getNamespace()}.{$module->getName()}</h2>

{if $urlAction}
    <p><a href="{$urlAction}">{translate key=$translationAction version=$module->getVersion()}</a></p>
{/if}

    <p>{"repository.label.dependencies"|translate}</p>
    <ul>
        <li>zibo {$module->getZiboVersion()}</li>
{if $module->hasDependencies()}
    {foreach from=$module->getDependencies() item="dependency"}
        <li>
        {if $urlModule}
            <a href="{$urlModule}{$dependency->getNamespace()}/{$dependency->getName()}">{$dependency->getNamespace()}.{$dependency->getName()} {$dependency->getVersion()}</a>
        {else}
            {$dependency->getNamespace()}.{$dependency->getName()} {$dependency->getVersion()}
        {/if}
        </li>
    {/foreach}
{/if}
    </ul>

{if $urlBack}
    <p><a href="{$urlBack}">{"button.back"|translate}</a></p>
{/if}

	<p>{translate_plural key="repository.label.version.count" n=$module->countVersions()}</p>

	{include file="helper/table" table=$table}
</div>