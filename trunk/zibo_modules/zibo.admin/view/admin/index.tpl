<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        {head}
    </head>
    <body class="{$_locale}">
        {subview name="taskbar"}
        <div id="bodyContainer">
            {subview name="sidebar"}
            <div id="content">
                <div id="contentContainer">
                    {messages}
                {if isset($contentTemplate)}
                    {include file=$contentTemplate}
                {/if}
                </div>
            </div>
        </div>
    </body>
</html>