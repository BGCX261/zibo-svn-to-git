<p>{"orm.label.wizard.steps"|translate}</p>

<ul class="wizardSteps">
{foreach from=$steps item="url" key="label"}
    <li{if $currentStep == $label} class="selected"{/if}>
    {if $url}
        <a href="{$url}">{$label}</a>
    {else}
        {$label}
    {/if}
    </li>
{/foreach}
</ul>