<div id="xmlrpcClient">

    <h2>{"xmlrpc.title.client"|translate}</h2>

    {form form=$form}
        <div class="server">
            <label for="{fieldId form=$form name="server"}">{"xmlrpc.label.server"|translate}</label>
            {field form=$form name="server"}
            {fieldErrors form=$form name="server"}
        </div>
    
        <div class="method">
            <label for="{fieldId form=$form name="method"}">{"xmlrpc.label.method"|translate}</label>
            {field form=$form name="method"}
            {fieldErrors form=$form name="method"}
        </div>
    
        <div class="parameters">
            <label for="{fieldId form=$form name="parameters"}">{"xmlrpc.label.parameters"|translate}</label>
            {field form=$form name="parameters"}
            {fieldErrors form=$form name="parameters"}
        </div>
        
        <div class="submit">
            {field form=$form name="invoke"}
        </div>
    {/form}
    
{if isset($result)}
    <div class="result">
        <h3>{"xmlrpc.title.result"|translate}</h3>
        <pre>{$result}</pre>
    </div>
{/if}    

{if isset($error)}
    <div class="result">
        <h3>{"xmlrpc.title.result"|translate}</h3>
        <pre class="error">{$error}</pre>
    </div>
{/if}    

{if $time}
    <p>{translate key="xmlrpc.label.time" time=$time|string_format:"%.4f"}</p>
{/if}

{if $requestString}
    <div class="request">
        <h3>{"xmlrpc.title.request"|translate}</h3>
        <pre>{$requestString|escape:"html"}</pre>
    </div>
{/if}

{if $responseString}
    <div class="response">
        <h3>{"xmlrpc.title.response"|translate}</h3>
        <pre>{$responseString|escape:"html"}</pre>
    </div>
{/if}

</div>