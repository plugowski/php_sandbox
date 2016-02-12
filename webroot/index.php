<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>PHP Sandbox</title>

    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/main.css">

    <script src="js/jquery.min.js" type="text/javascript"></script>
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

        <div class="actions">
            <span class="checkbox">
                <label for="formatted"><input type="checkbox" id="formatted"> formatted</label>
            </span>
            &nbsp;
            <a href="#" class="btn btn-primary evaluate"><i class="fa fa-play"></i> Evaluate</a>
        </div>
    </nav>

    <div class="row">
        <div class="col-md-6">
            <div id="editor"></div>
        </div>
        <div class="col-md-6 preview">
            <div class="output">Press Cmd+Enter/Ctrl+Enter to execute...<br/>Output goes here...</div>
        </div>
    </div>

</body>
</html>