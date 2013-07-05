<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

    <title>Model Documantation</title>
    <link rel="shortcut icon" href="http://monorom.com/favicon.ico" type="image/x-icon"/>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <link type="text/css" href="<?php echo Base::app()->theme->baseUrl; ?>/css/style.css" rel="stylesheet" media="screen"/>
</head>
<body>

    <div class="navbar">
        <div class="navbar-inner">
            <div class="container">
                <ul class="nav">
                    <li><a href="#summary">Summary</a></li>
                    <li><a href="#basic-example">Basic Example</a></li>
                    <li><a href="#tutorial">Tutorial</a></li>
                    <li><a href="#api">API</a></li>
                    <li><a href="#book">Book</a></li>
                    <li><a href="https://github.com/kebap-framework">GitHub Page</a></li>
                </ul>
            </div>
        </div>
    </div>
<div id="container">
    <div class="logo">
        <img src="<?php echo Base::app()->theme->baseUrl; ?>/images/logo.png"/>
    </div>
    <div id="content">
        <?php echo $content; ?>
    </div>
</div>
</body>
</html>