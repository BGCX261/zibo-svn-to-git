<h2>{"terminal.title"|translate}</h2>

{form form=$form}
    <div class="result">
        <pre class="result"></pre>
    </div>
    <div class="bash">
        <pre class="bash"><span class="path">{$path}</span> $</pre> {field name="command"}
    </div>
{/form}