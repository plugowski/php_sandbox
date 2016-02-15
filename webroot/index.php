<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
    <link rel="icon" href="favicon.ico" type="image/x-icon">

    <title>PHP Sandbox</title>

    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/main.css">

    <script src="js/jquery.min.js" type="text/javascript"></script>
    <script src="js/bootstrap.min.js" type="text/javascript"></script>
    <script src="js/ace/ace.js" type="text/javascript"></script>
    <script src="js/ace/theme-ambiance.js" type="text/javascript"></script>
    <script src="js/ace/mode-php.js" type="text/javascript"></script>
    <script src="js/main.js" type="text/javascript"></script>

</head>
<body>

<nav class="navbar navbar-inverse">

    <div class="navbar-header">
        <span class="navbar-brand">PHP Sandbox v1.0 <small> - PHP: <?php echo phpversion(); ?></small></span>
    </div>

    <ul class="nav navbar-nav">
        <li><a href="#" class="reload"><i class="fa fa-refresh"></i> Load last</a></li>
    </ul>

    <div class="actions">
        <span class="checkbox">
            <label for="formatted"><input type="checkbox" id="formatted"> formatted</label>
        </span>

        &nbsp;

        <a href="#" class="btn btn-primary evaluate"><i class="fa fa-play"></i> Evaluate</a>
    </div>

</nav>
<div class="row">
    <div class="col-xs-6">
        <div id="editor"></div>
    </div>
    <div class="col-xs-6 preview">
        <div class="loader hidden"><i class="fa fa-cog fa-spin"></i></div>
        <div id="benchmark">
            <span data-toggle="tooltip" data-placement="top" title="Memory Usage"><i class="fa fa-tachometer"></i> <span class="memory">0.000</span> MB</span>,
            <span data-toggle="tooltip" data-placement="top" title="Memory Peak"><i class="fa fa-area-chart"></i> <span class="memory_peak">0.000</span> MB</span>,
            <span data-toggle="tooltip" data-placement="top" title="Time"><i class="fa fa-clock-o"></i> <span class="time">0.00</span> ms</span>
        </div>
        <div class="output">Press Cmd+Enter/Ctrl+Enter to execute...<br/>Output goes here...</div>
    </div>
</div>

</body>
</html>