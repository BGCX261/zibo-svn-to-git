{pageTitle}

{assign var="form" value=$table->getForm()}

{form form=$form}
    
    {if $table->hasRows()}
        {$table->getHtml()}
    {/if}
    
    <div class="deniedRoutes">
        <label for="{fieldId form=$form name="deniedRoutes"}">{"security.label.routes.denied"|translate}</label>
        <span>
            {"security.label.routes.denied.description"|translate}
            <br /><br />
            {"security.label.routes.description"|translate}
        </span>            
        {field form=$form name="deniedRoutes"}
        {fieldErrors form=$form name="deniedRoutes"}
    </div>    
    
    {assign var="roles" value=$table->getRoles()}
    {if $roles}
    <div class="allowedRoutes">
        <label>{"security.label.routes.allowed"|translate}</label>
        <span>
            {"security.label.routes.allowed.description"|translate}
            <br /><br />
            {"security.label.routes.description"|translate}
        </span>
            
        <ul>
        {foreach from=$roles item="role"}
            {assign var="roleName" value=$role->getRoleName()}
            {assign var="fieldName" value="allowedRoutes[`$roleName`]"}
            {fieldId form=$form name=$fieldName var="fieldId"}
            {fieldErrors form=$form name=$fieldName var="fieldErrors"}
            <li class="role">
            {if $fieldErrors}            
                <label for="{$fieldId}">{$roleName}</label>
                {field form=$form name=$fieldName}
                {fieldErrors form=$form name=$fieldName}
            {else}
                <a href="#" id="role{$roleName|safeString}">{$roleName}</a>
                <div id="role{$roleName|safeString}Field">
                    {field form=$form name=$fieldName}
                </div>
            {/if}
            </li>
        {/foreach}
        </ul>            
    </div>
    {/if}

    <div class="submit">
        {field form=$form name="save"}
    </div>

{/form}