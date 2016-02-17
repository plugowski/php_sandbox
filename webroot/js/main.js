$(function(){
    $('[data-toggle="tooltip"]').tooltip();

    var isMac = navigator.platform.toUpperCase().indexOf('MAC') >= 0;
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
        $.post('/execute', {'code': editor.getValue()}, function(response){
            var json = $.parseJSON(response);

            $('.output').html(json.result);
            $.each(json.benchmark, function(key, value) {
                $('#benchmark .' + key).html(value);
            });

            alloweRequest = true;
            $loader.addClass('hidden');
        });
    };

    var saveSnippet = function(code) {
        bootbox.prompt("Enter name for new snippet:", function(result) {
            if (result !== null) {
                $loader.removeClass('hidden');

                // todo: walidacja nazwy (alfanueric)

                $.post('/save_snippet.json', {name: result, code: code}, function(response){

                    // todo: komunikat odpowiedzi
                    $loader.addClass('hidden');
                });
            }

            editor.focus();
        });
    };

    var bindLoadSnippet = function() {
        $('.snippets .file a').unbind().click(function(e){
            e.preventDefault();
            $.getJSON('/get_snippet' + $(this).data('file'), function(response){
                editor.setValue(response.code, 1);
                editor.focus();
            })
        });
    };

    var toggleSnippetList = function() {
        var $snippetLis = $('.col-xs-2');
        var $columns = $('.col-xs-6');

        if ($snippetLis.hasClass('hidden')) {
            $columns.css({width: "41.66666667%"});
            $snippetLis.css({width: 0}).removeClass('hidden').animate({width: "16.66666667%"});
        } else {
            $snippetLis.animate({width: 0}, function(){
                $columns.css({width: "50%"});
                $(this).addClass('hidden');
            });
        }

        //editor.resize();
    };

    $.getJSON('/get_snippets_list.json', function(response){
        var $ul =  $('<ul/>');
        $('.snippets').append($ul);
        buildTree(response, '/', $ul);
    });

    // build snippets tree in navigator
    var buildTree = function(data, reference, container){

        $.each(data, function(i, item){

            var $ul = $('<ul/>');
            var $li = $('<li/>');
            var $link = $('<a href="#"/>');
            var record = item.name;

            if (item.type == 'file') {
                record = $link.attr('data-file', reference + item.name).append(item.name)
            }

            var el = $li.addClass(item.type).append(record);

            if (item.type == 'folder') {
                el.append($ul);
                buildTree(item.data, reference + item.name + '/', $ul);
            }

            container.append(el);
            bindLoadSnippet();
        });
    };

    if (isMac) {
        $('.kbd').each(function(){
            $(this).html($(this).data('macos'));
        });
    }

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

    $('#saveSnippet').click(function(e){
        e.preventDefault();
        saveSnippet(editor.getValue());
    });

    $('#loadSnippet').click(function(e){
        e.preventDefault();
        toggleSnippetList();
    });

    // EDITOR SETTINGS
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

    editor.commands.addCommand({
        name: "savesnippet",
        bindKey: {win: "Ctrl-Shift-S", mac: "Command-Shift-S"},
        exec: function(editor) { saveSnippet(editor.getValue()); },
        scrollIntoView: "cursor"
    });

    editor.commands.addCommand({
        name: "showsnippetlist",
        bindKey: {win: "Ctrl-Shift-L", mac: "Command-Shift-L"},
        exec: function() { toggleSnippetList(); },
        scrollIntoView: "cursor"
    });

    // global for non editor area
    Mousetrap.bind(["ctrl+shift+s", "command+shift+s"], function(){
        saveSnippet(editor.getValue());
    });

    Mousetrap.bind(["ctrl+shift+l", "command+shift+l"], function(){
        toggleSnippetList();
    });
});