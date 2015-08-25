{form form=$wizard}

    {subview name="wizardStep"}

    <div class="submit">
        {field form=$wizard name="previous"}
        {field form=$wizard name="next"}
        {field form=$wizard name="cancel"}
        {field form=$wizard name="finish"}
    </div>

{/form}