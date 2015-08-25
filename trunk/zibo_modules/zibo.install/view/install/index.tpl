<!doctype html>
<!--[if lt IE 7 ]> <html lang="en" class="no-js ie ie6" xmlns="http://www.w3.org/1999/xhtml"> <![endif]-->
<!--[if IE 7 ]>    <html lang="en" class="no-js ie ie7" xmlns="http://www.w3.org/1999/xhtml"> <![endif]-->
<!--[if IE 8 ]>    <html lang="en" class="no-js ie ie8" xmlns="http://www.w3.org/1999/xhtml"> <![endif]-->
<!--[if IE 9 ]>    <html lang="en" class="no-js ie ie9" xmlns="http://www.w3.org/1999/xhtml"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html lang="en" class="no-js" xmlns="http://www.w3.org/1999/xhtml"> <!--<![endif]-->
    <head>
        {head}
    </head>
    
    <body class="{$locale}">
        <div id="body-container">
            <h1>{"install.title"|translate}</h1>
            
            <div class="content {$step}">
                {subview name="content"}
            </div>
        </div>
    </body>

</html>