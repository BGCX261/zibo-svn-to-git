<h3>{"joppa.mailinglist.title.subscribe"|translate}</h3>
<p>{"joppa.mailinglist.label.subscribe"|translate}</p>

{form form=$formSubscribe}
	<div class="email">
		{field form=$formSubscribe name="email"}
		{field form=$formSubscribe name="subscribe"}
		{fieldErrors form=$formSubscribe name="email"}
	</div>
{/form}


<h3>{"joppa.mailinglist.title.unsubscribe"|translate}</h3>
<p>{"joppa.mailinglist.label.unsubscribe"|translate}</p>

{form form=$formUnsubscribe}
	<div class="email">
		{field form=$formUnsubscribe name="email"}
		{field form=$formUnsubscribe name="unsubscribe"}
		{fieldErrors form=$formUnsubscribe name="email"}
	</div>
{/form}