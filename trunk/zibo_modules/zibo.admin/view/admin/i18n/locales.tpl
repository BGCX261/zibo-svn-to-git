{pageTitle}

<div id="locales">
    <p>{"locales.label.info"|translate}</p>

    <ul id="listLocales">
{foreach from=$locales item="locale"}
        <li id="locale_{$locale->getCode()}">
            <span class="handle">[{$locale->getCode()}]</span> 
            {$locale->getNativeName()}
            ({$locale->getName()})
            <div>
                {"locales.label.date.formats"|translate}
                <ul>
                {foreach from=$locale->getDateFormats() item="format" key="identifier"}
                    <li><strong>{$identifier}:</strong> {$format}</li>
                {/foreach}
                </ul>
            </div>
        </li>
{/foreach}
    </ul>
    
</div>