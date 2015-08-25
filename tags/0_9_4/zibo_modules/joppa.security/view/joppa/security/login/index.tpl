{if $form}
	{form form=$form}
	    <div class="username">
	        <label for="{fieldId form=$form name="username"}">{"joppa.security.label.username"|translate}</label>
	        {field form=$form name="username"}
	        {fieldErrors form=$form name="username"}
	    </div>
	    
	    <div class="password">
	        <label for="{fieldId form=$form name="password"}">{"joppa.security.label.password"|translate}</label>
	        {field form=$form name="password"}
	        {fieldErrors form=$form name="password"}
	    </div>
	    
	    <div class="submit">
	        {field form=$form name="submit"}
	        {field form=$form name="cancel"}
	    </div>
	{/form}
{/if}