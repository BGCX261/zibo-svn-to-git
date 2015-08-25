<div id="managerSidebar">
    <ul>
{foreach from=$managers item="manager"}
        <li{if $manager.current} class="selected"{/if}>
            {image src=$manager.icon}
        {if $manager.current && isset($manager.actions)}
            <span>{$manager.name}</span>
                <ul>
            {foreach from=$manager.actions key="route" item="label"}
                    <li><a href="{$route}">{$label}</a></li>
            {/foreach}
                </ul>
        {else}
            <a href="{$manager.action}">{$manager.name}</a>
        {/if}
        </li>
{/foreach}
    </ul>
</div>