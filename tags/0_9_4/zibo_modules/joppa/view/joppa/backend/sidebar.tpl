{form form=$formSiteSelect}

    {field form=$formSiteSelect name="site"}

    <div id="createMenu">
        <a href="#" id="createMenuAnchor" class="actionMenu">{"joppa.button.create"|translate}</a>
        <ul id="createMenuActions">
        {foreach from=$createActions item="action"}
            <li><a href="{$action.url}" class="{$action.type}">{$action.label}</a></li>
        {/foreach}
        </ul>
    </div>

{/form}

<br class="clear" />

<div id="joppaTree">
{subview name="tree"}
</div>