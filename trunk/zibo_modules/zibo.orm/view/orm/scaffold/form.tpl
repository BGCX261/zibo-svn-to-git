<h2>{$title}</h2>

{form form=$form}
    {foreach from=$hiddenFields item='name'}
        {field form=$form name=$name}
        {fieldErrors form=$form name=$name}        
    {/foreach}    
    
    {foreach from=$fields item="name"}
    <div class="{$name}">
        {if isset($fieldLabels.$name)}
            <label for="{fieldId form=$form name=$name}">{$fieldLabels.$name|translate}</label>
            
            {assign var="translationKeyDescription" value="`$fieldLabels.$name`.description"}
            {assign var="translationDescription" value=$translationKeyDescription|translate}            
            {if $translationDescription != "[`$translationKeyDescription`]"}
                <span>{$translationDescription}</span>
            {/if}
        {else}
            <label for="{fieldId form=$form name=$name}">{$name|capitalize}</label>
        {/if}
        
        {assign var="field" value=$form->getField($name)}
        {if get_class($field) == 'zibo\\library\\html\\form\\field\\ListField' && $field->isMultiple()}
            <span>{"label.multiselect"|translate}</span>
        {/if}  
        
        {field form=$form name=$name}
        {fieldErrors form=$form name=$name}
    {if isset($_views.localize) && isset($localizedFields.$name)}        
        {fieldLocales form=$form name=$name locales=$_views.localize.view->get('locales')}
    {/if}        
    </div>
    {/foreach}
    
    <div class="submit">
        {field form=$form name="submit"}
        {field form=$form name="cancel"}
    </div>
{/form}

{subview name="localize"}

{subview name="log"}