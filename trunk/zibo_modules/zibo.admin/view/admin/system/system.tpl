{pageTitle}

<div id="system">

<h3>{"system.label.client"|translate}</h3>
<table class="settings">
    <tr>
        <td class="key">{"system.label.useragent"|translate}</td>
        <td>{$userAgent}</td>
    </tr>
    <tr>
        <td class="key">{"system.label.ip"|translate}</td>
        <td>{$ip}</td>
    </tr>
    <tr>
        <td class="key">{"system.label.browser"|translate}</td>
        <td>{if isset($browserIcon)}<img src="{$browserIcon}" /> {/if}{$browserName}{if $browserVersion} ({$browserVersion}){/if}</td>
    </tr>
    <tr>
        <td class="key">{"system.label.os"|translate}</td>
        <td>{if isset($osIcon)}<img src="{$osIcon}" /> {/if}{$osName}{if $osVersion} ({$osVersion}){/if}</td>
    </tr>
</table>

<h3>{"system.label.server"|translate}</h3>
<table class="settings">
    <tr>
        <td class="key">{"system.label.php"|translate}</td>
        <td>{$phpVersion}</td>
    </tr>
    <tr>
        <td class="key">{"system.label.zibo"|translate}</td>
        <td>{$ziboVersion}</td>
    </tr>
</table>

<div class="ziboConfiguration">
    <a href="#" id="ziboConfigurationLink">{"system.button.zibo.configuration"|translate}</a>
    
    <div id="ziboConfigurationData">
        <table class="settings">
        {foreach from=$ziboConfiguration item="value" key="key"}
            <tr class="{cycle values="odd,even"}">
                <td class="key">{$key}</td>
                <td>{$value}</td>
            </tr>
        {/foreach}
        </table>
    </div>
</div>

<h3>{"system.label.security"|translate}</h3>
<p>{translate key="system.label.security.description" numVisitors=$numVisitors numUsers=$numUsers numGuests=$numGuests}</p>
{if $currentUsers}
<p>{"system.label.security.authenticated"|translate}</p>
<ul>
    {foreach from=$currentUsers item="currentUser"}
    <li>{$currentUser}</li>
    {/foreach}
</ul>
{/if}

<h3>{"system.label.routes"|translate}</h3>
<table class="settings">
{foreach from=$routes item="route"}
    <tr class="{cycle values="odd,even"}">
        <td class="key"><a href="{$baseUrl}/{$route->getPath()}">{$route->getPath()}</a></td>
        <td>{$route->getControllerClass()}->{$route->getAction()}</td>
    </tr>
{/foreach}
</table>

</div>