$(function(){
    $('[data-toggle="tooltip"]').tooltip();
    $('#php-version').selectpicker('hide');

    var alloweRequest = true;
    var $loader = $('.loader');
    var editor = ace.edit("editor");
    var PhpMode = ace.require("ace/mode/php").Mode;
    var postCode = function(editor) {

        if (alloweRequest === false) {
            bootbox.dialog({
                title: "Script is running",
                message: "Please wait until previous request finished!",
                buttons: {
                    main: {
                        label: "OK",
                        callback: function(){
                            editor.focus();
                        }
                    }
                }
            });
            return;
        }

        alloweRequest = false;
        $loader.removeClass('hidden');

        var php = $('#php-version').val();

        $.post('/execute/' + php + '.json', {'code': editor.getValue()}, function(response){
            var json = $.parseJSON(response);

            $('.output').html(json.result);
            $.each(json.benchmark, function(key, value) {
                $('#benchmark .' + key).html(value);
            });

            alloweRequest = true;
            $loader.addClass('hidden');
        });
    };

    var loadPhpVersions = function() {

        $.getJSON('/get_php_versions.json', function(response){

            if (response.versions.length > 0) {

                $.each(response.versions, function(i, item) {
                    $('#php-version').append($('<option>').attr('value', item).html('PHP ' + item));
                });

                $('#php-version').selectpicker('refresh').selectpicker('show');
            }
        });
    };

    // ACTIONS
    $('.reload').click(function(e){
        e.preventDefault();
        $.get('/get_last', function(response){
            editor.setValue(response, 1);
            editor.focus();
        })
    });

    $('.evaluate').click(function(e){
        e.preventDefault();
        postCode(editor);
    });

    $('#formatted').click(function(){
        $('.output').toggleClass('format', $(this).is(':checked'));
    });

    editor.setTheme("ace/theme/ambiance");
    editor.session.setMode(new PhpMode());
    editor.getSession().setUseWrapMode(true);
    editor.setShowPrintMargin(false);
    editor.focus();


    // PhpStorm key bindings
    editor.commands.addCommand({
        name: "execute",
        bindKey: {win: "Ctrl-Enter", mac: "Command-Enter"},
        exec: function(editor) { postCode(editor); }
    });
    editor.commands.addCommand({
        name: "movelinesup",
        bindKey: {win: "Alt-Shift-Up", mac: "Option-Shift-Up"},
        exec: function(editor) { editor.moveLinesUp(); },
        scrollIntoView: "cursor"
    });
    editor.commands.addCommand({
        name: "movelinesdown",
        bindKey: {win: "Alt-Shift-Down", mac: "Option-Shift-Down"},
        exec: function(editor) { editor.moveLinesDown(); },
        scrollIntoView: "cursor"
    });

    editor.commands.addCommand({
        name: "copylinedown",
        bindKey: {win: "Ctrl-D", mac: "Command-D"},
        exec: function(editor) { editor.copyLinesDown(); },
        scrollIntoView: "cursor"
    });

    // czesto klikam zapisz, chcac uzyskac execute - taki fix
    editor.commands.addCommand({
        name: "save",
        bindKey: {win: "Ctrl-S", mac: "Command-S"},
        exec: function(editor) { postCode(editor); },
        scrollIntoView: "cursor"
    });

    // PhpStor behavior - after comment jump to next line
    editor.commands.addCommand({
        name: "togglecomment",
        bindKey: {win: "Ctrl-/", mac: "Command-/"},
        exec: function(editor) {
            editor.toggleCommentLines();
            editor.navigateDown();
        },
        multiSelectAction: "forEachLine",
        scrollIntoView: "selectionPart"
    });

    loadPhpVersions();

});