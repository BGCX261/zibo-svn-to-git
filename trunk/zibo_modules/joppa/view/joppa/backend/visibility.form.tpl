{form form=$form}
    <h3>{"joppa.title.publish"|translate}</h3>
    <p>{"joppa.label.publish.description"|translate}</p>

    <div class="published">
        <label for="{fieldId form=$form name="published"}">{"joppa.label.publish"|translate}</label>
        {field form=$form name="published"}
        {fieldErrors form=$form name="published"}
    </div>

    <div class="publishStart">
        <label for="{fieldId form=$form name="publishStart"}">{"joppa.label.publish.start"|translate}</label>
        {field form=$form name="publishStart"}
        {fieldErrors form=$form name="publishStart"}
    </div>

    <div class="publishStop">
        <label for="{fieldId form=$form name="publishStop"}">{"joppa.label.publish.stop"|translate}</label>
        <span>{"joppa.label.publish.stop.description"|translate}</span>
        {field form=$form name="publishStop"}
        {fieldErrors form=$form name="publishStop"}
    </div>
    
    <h3>{"joppa.title.permissions"|translate}</h3>
    <p>{"joppa.label.permissions.description"|translate}</p>

    {assign var="nodeSettings" value=$form->getNodeSettings(false)}    
    {if $nodeSettings->getInheritedNodeSettings()}
    <div class="permissionsInherit">
    	{field form=$form name="authenticationStatus" option="inherit"}
    </div>
    {/if}
    <div class="permissionsEverybody">
    	{field form=$form name="authenticationStatus" option="everybody"}
    	<span>{"joppa.label.everybody.description"|translate}</span>
    </div>
    <div class="permissionsAnonymous">
    	{field form=$form name="authenticationStatus" option="anonymous"}
    	<span>{"joppa.label.anonymous.description"|translate}</span>
    </div>
    <div class="permissionsAuthenticated">
    	{field form=$form name="authenticationStatus" option="authenticated"}
    	<span>{"joppa.label.authenticated.description"|translate}</span>
    	{field form=$form name="permissions"}    	
    	{fieldErrors form=$form name="permissions"}    	
    </div>
    
    <div class="submit">
        {field form=$form name="save"}
        {field form=$form name="cancel"}
    </div>
{/form}