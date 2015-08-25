<div id="api">

    {$breadcrumbs->getHtml()}

    <div class="detail">
        <h2>{$shortName}</h2>        
        <p class="namespace">{$namespace}</p>
        
        <p>{$type} <strong>{$shortName}</strong></p>

        {assign var="classDoc" value=$class->getDoc()}
    
        {apiDescription description=$classDoc->getDescription() prefix="<p><span class=\"description\">" suffix="</span></p>" url=$classAction namespace=$currentNamespace classes=$classes}    
        {apiDescription description=$classDoc->getLongDescription() url=$classAction namespace=$currentNamespace classes=$classes}

        <h3>{"api.title.hierarchy"|translate}</h3>

        <ul class="hierarchy">
{assign var="margin" value=0}
{foreach from=$inheritance item="methods" key="className" name="hierarchy"}
            <li{if $smarty.foreach.hierarchy.first} class="first"{/if} style="margin-left: {$margin}px;">
    {if $smarty.foreach.hierarchy.last}
                {$className}
    {else}
                {apiType type=$className url=$classAction namespace=$currentNamespace classes=$classes}
    {/if}
            </li>
    {assign var="margin" value=$margin+20}
{/foreach}
        </ul>

{if $interfaces}
        <h3>{"api.title.implements"|translate}</h3>
        <ul>
    {foreach from=$interfaces item="interface"}
            <li>{apiType type=$interface url=$classAction namespace=$currentNamespace classes=$classes}</li>
    {/foreach}
        </ul>
{/if}

{if $classDoc->isDeprecated()}
<div class="deprecated">
    <h5>{"api.title.deprecated"|translate}</h5>
    {assign var="defaultDeprecatedMessage" value="api.label.deprecated.class"|translate}
    {$classDoc->getDeprecatedMessage()|default:$defaultDeprecatedMessage}
</div>
{/if}        


{assign var="todos" value=$classDoc->getTodos()}
{if $todos}
    <div class="todo">
        <h5>{"api.title.todo"|translate}</h5>    
        <ul class="todos">
        {foreach from=$todos item="todo"}
            <li>{$todo}</li>
        {/foreach}
        </ul>
    </div>
{/if}

{if $inheritance}
        <h3>{"api.title.methods.overview"|translate}</h3>
    {if $inheritance.$name}
        <ul class="methods">
        {foreach from=$inheritance.$name item="method"}
            {if !$method->isPrivate()}
            <li>
                {assign var="methodDoc" value=$method->getDoc()}

                {$method->getTypeString()}                

                <a href="#method{$method->getName()}">{$method->getName()}</a>{apiMethodParameters method=$method url=$classAction namespace=$currentNamespace classes=$classes}
                
                {apiDescription description=$methodDoc->getDescription() prefix="<br /><span class=\"description\">" suffix="</span>" url=$classAction namespace=$currentNamespace classes=$classes}
            </li>
            {/if}
        {/foreach}
        </ul>
    {/if}

    {foreach from=$inheritance item="methods" key="className"}
        {if $methods && $className != $name}
        <div class="inheritedMethods">
            {apiType type=$className url=$classAction html="classLink"}
            <p>{translate key="api.label.inherited.methods" class=$classLink}</p>     
            {foreach from=$methods item="method" name="inheritanceMethods"}
                {if !$method->isPrivate()}
                    {apiType type=$className url=$classAction method=$method->getName()}{if !$smarty.foreach.inheritanceMethods.last}, {/if}
                {/if}
            {/foreach}
        </div> 
        {/if}
    {/foreach}
        <p class="top"><a href="#">{"api.button.top"|translate}</a></p>
{/if}


{if $properties}
        <h3>{"api.title.properties"|translate}</h3>
        <ul class="properties">
    {foreach from=$properties item="property"}
            <li>
                {$property->getTypeString()}
        
        {assign var="doc" value=$property->getDoc()}
        
        {assign var="var" value=$doc->getVar()}
        {if $var}
            {apiType type=$var url=$classAction namespace=$currentNamespace classes=$classes}
        {/if}
        
                ${$property->getName()}


                {apiDescription description=$doc->getDescription() prefix="<br /><span class=\"description\">" suffix="</span>" url=$classAction namespace=$currentNamespace classes=$classes}
        
                {$doc->getLongDescription()}

                {if $doc->isDeprecated()}
                <div class="deprecated">
                    <h5>{"api.title.deprecated"|translate}</h5>
                    {assign var="defaultDeprecatedMessage" value="api.label.deprecated.property"|translate}
                    {$doc->getDeprecatedMessage()|default:$defaultDeprecatedMessage}
                </div>
                {/if}
                
                {assign var="todos" value=$doc->getTodos()}
                {if $todos}
                    <div class="todo">
                        <h5>{"api.title.todo"|translate}</h5>    
                        <ul class="todos">
                        {foreach from=$todos item="todo"}
                            <li>{$todo}</li>
                        {/foreach}
                        </ul>
                    </div>
                {/if}
                        
            </li>
    {/foreach}
        </ul>
        <p class="top"><a href="#">{"api.button.top"|translate}</a></p>
{/if}

{if $constants}
        <h3>{"api.title.constants"|translate}</h3>
        <ul>
    {foreach from=$constants item="value" key="constant"}
            <li>{$constant} = '{$value|replace:' ':'&nbsp;'}'</li>
    {/foreach}
        </ul>
        <p class="top"><a href="#">{"api.button.top"|translate}</a></p>
{/if}

{if $inheritance.$name}
        <h3>{"api.title.methods"|translate}</h3>
    {foreach from=$inheritance.$name item="method"}
        {if !$method->isPrivate()}
            {assign var="methodDoc" value=$method->getDoc()}    
        <a name="method{$method->getName()}"></a>
        
        <h4>{$method->getName()}</h4>
        
        {if $methodDoc->isDeprecated()}
        <div class="deprecated">
            <h5>{"api.title.deprecated"|translate}</h5>
            {assign var="defaultDeprecatedMessage" value="api.label.deprecated.method"|translate}
            {$methodDoc->getDeprecatedMessage()|default:$defaultDeprecatedMessage}
        </div>
        {/if}
        
        <p>
        {$method->getTypeString()} 
        
        <a href="#method{$method->getName()}">{$method->getName()}</a>{apiMethodParameters method=$method url=$classAction namespace=$currentNamespace classes=$classes}
        </p>
    
        {apiDescription description=$methodDoc->getDescription() prefix="<p><span class=\"description\">" suffix="</span></p>" url=$classAction namespace=$currentNamespace classes=$classes}    
        {$methodDoc->getLongDescription()}
        
        {assign var="see" value=$methodDoc->getSee()}
        {if $see}
            <p><span class="description">{"api.label.see"|translate} {apiType type=$see url=$classAction namespace=$currentNamespace classes=$classes}</span></p>
        {/if}

        {assign var="interface" value=$class->getMethodInterface($method->getName())}
        {if $interface}
            {apiType type=$interface url=$classAction html="interfaceLink"}
            {apiType type=$interface method=$method->getName() url=$classAction html="methodLink"}
            <p>{translate key="api.label.specified" interface=$interfaceLink method=$methodLink}</p>
        {/if}

        {assign var="parameters" value=$method->getParameters()}
        {if $parameters}
        <h5>{"api.title.parameters"|translate}</h5>    
        <ul class="parameters">
            {foreach from=$parameters item="parameter" name="parameters"}
            <li>
                {assign var="parameterName" value=$parameter->getName()}
                {assign var="parameterName" value="$`$parameterName`"}
                {assign var="parameterDoc" value=$methodDoc->getParameter($parameterName)}
                {assign var="type" value=$parameterDoc->getType()}
                {if $type}
                    {apiType type=$type url=$classAction namespace=$currentNamespace classes=$classes}
                {/if}
                
                {$parameterName}

                {apiDescription description=$parameterDoc->getDescription() prefix="<br /><span class=\"description\">" suffix="</span>" url=$classAction namespace=$currentNamespace classes=$classes}
            </li>
            {/foreach}        
        </ul>
        {/if}
        
        {assign var="return" value=$methodDoc->getReturn()}
        {if $return}
            <h5>{"api.title.return"|translate}</h5>
            <p>
                {apiType type=$return->getType() url=$classAction namespace=$currentNamespace classes=$classes}
                {apiDescription description=$return->getDescription() prefix="<br /><span class=\"description\">" suffix="</span>" url=$classAction namespace=$currentNamespace classes=$classes}
            </p>        
        {/if}

        {assign var="exceptions" value=$methodDoc->getExceptions()}
        {if $exceptions}
        <h5>{"api.title.exceptions"|translate}</h5>    
        <ul class="exceptions">
        {foreach from=$exceptions item="exception"}
            <li>
                {apiType type=$exception->getType() url=$classAction namespace=$currentNamespace classes=$classes}
                {apiDescription description=$exception->getDescription() prefix="<br /><span class=\"description\">" suffix="</span>"}
            </li>
        {/foreach}
        </ul>
        {/if}

        {assign var="todos" value=$methodDoc->getTodos()}
        {if $todos}
        <div class="todo">
        <h5>{"api.title.todo"|translate}</h5>    
        <ul class="todos">
        {foreach from=$todos item="todo"}
            <li>{$todo}</li>
        {/foreach}
        </ul>
        </div>
        {/if}
    
        <p class="top"><a href="#">{"api.button.top"|translate}</a></p>
        {/if}
    {/foreach}
{/if}

    </div>
</div>