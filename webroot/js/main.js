$(function(){
    $('[data-toggle="tooltip"]').tooltip();
    $('#php-version').selectpicker('hide');

    var isMac = navigator.platform.toUpperCase().indexOf('MAC') >= 0;
    var alloweRequest = true;
    var $loader = $('.loader');
    var editor = ace.edit("editor");
    var PhpMode = ace.require("ace/mode/php").Mode;
    var $alert = $('<div class="alert" role="alert">' +
        '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
        '<span class="message" />' +
        '</div>');

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

    var addLibrary = function() {
        bootbox.prompt("Enter package like: components/jquery:2.2.*", function(result) {
            if (result !== null) {
                $loader.removeClass('hidden');

                // todo: walidacja aphanumeric
                // todo: walidacja czy paczka istnieje

                $.post('add_library.json', {name: result}, function(response){

                    response = JSON.parse(response);
                    if (response.status == 'error') {
                        $alert.addClass('alert-warning').find('.message').html('<strong>Problem when adding package!</strong> ' + response.message);
                        $('#alerts').append($alert);
                    }

                    $loader.addClass('hidden');
                    setTimeout(loadLibrariesList(), 1000);
                });
            }
            editor.focus();
        });
    };

    var loadLibrariesList = function() {
        $loader.removeClass('hidden');
        $('.packages ul').not('.main').remove();

        $.getJSON('/get_libraries_list.json', function(response){
            var $ul =  $('<ul/>');
            var $delete = $('<i class="fa fa-trash-o pull-right" />');
            // var ul = $ul.clone();

            $.each(response, function(i, item){
                var ul = $ul.clone();

                ul.append($('<li/>').addClass(i).html(i).append($('<ul />')));

                $.each(item, function(k, v){
                    var delIcon = $delete.clone();
                    ul.find('.' + i + ' ul').append($('<li/>').html(v.name + ' (' + v.version +')').append(delIcon));
                    delIcon.click(function(){ deleteLibrary(v.name); });
                });

                $('.packages .package').append(ul);
            });

            $loader.addClass('hidden');
        });
    };

    var deleteLibrary = function(filename) {
        bootbox.dialog({
            title: "Confirm library deletion",
            message: "Are you sure to delete library: <strong>" + filename + "</strong>",
            buttons: {
                main: {
                    label: "Cancel",
                    callback: function () {
                        editor.focus();
                    }
                },
                danger: {
                    label: "Delete",
                    className: "btn-danger",
                    callback: function () {
                        $loader.removeClass('hidden');

                        $.post('/delete_library/' + filename, {_method: 'DELETE'}, function (response) {
                            $loader.addClass('hidden');
                            setTimeout(loadLibrariesList(), 1000);
                        });
                    }
                }
            }
        });
    };

    var loadSnippetsList = function() {

        $loader.removeClass('hidden');
        $('.snippets ul').not('.main').remove();

        $.getJSON('/get_snippets_list.json', function(response){
            var $ul =  $('<ul/>');
            $('.snippets .folder').append($ul);
            buildTree(response, '/', $ul);
            $loader.addClass('hidden');
        });
    };

    var saveSnippet = function(code) {
        bootbox.prompt("Enter name for new snippet:", function(result) {
            if (result !== null) {
                $loader.removeClass('hidden');

                // todo: walidacja nazwy (alfanueric)

                $.post('/save_snippet.json', {name: result, code: code}, function(response){

                    response = JSON.parse(response);
                    if (response.status == 'error') {
                        $alert.addClass('alert-warning').find('.message').html('<strong>Can\'t save file!</strong> ' + response.message);
                        $('#alerts').append($alert);
                    }

                    $loader.addClass('hidden');
                    setTimeout(loadSnippetsList(), 1000);
                });
            }
            editor.focus();
        });
    };

    var deleteSnippet = function(filename) {
        bootbox.dialog({
            title: "Confirm file deletion",
            message: "Are you sure to delete file: <strong>" + filename + "</strong>",
            buttons: {
                main: {
                    label: "Cancel",
                    callback: function(){
                        editor.focus();
                    }
                },
                danger: {
                    label: "Delete",
                    className: "btn-danger",
                    callback: function(){
                        $loader.removeClass('hidden');

                        $.post('/delete_snippet/' + filename, {_method: 'DELETE'}, function(response){
                            // todo: komunikat odpowiedzi
                            $loader.addClass('hidden');
                            setTimeout(loadSnippetsList(), 1000);
                        });
                    }
                }
            }
        });
    };

    var bindLoadSnippet = function() {
        $('.snippets .file a').unbind().click(function(e){
            e.preventDefault();
            $.getJSON('/get_snippet' + $(this).closest('span').data('file'), function(response){
                editor.setValue(response.code, 1);
                editor.focus();
            })
        });
    };

    var toggleSnippetList = function() {
        $('.editor_area, .preview').toggleClass('col-xs-6 col-xs-5');
        $('.snippets_panel').toggleClass('col-xs-0 col-xs-2');
    };

    // build snippets tree in navigator
    var buildTree = function(data, reference, container){

        $.each(data, function(i, item){

            var $ul = $('<ul/>');
            var $li = $('<li/>');
            var $span = $('<span/>');
            var $link = $('<a href="#"/>');
            var $delete = $('<i class="fa fa-trash-o pull-right" />');
            var record = item.name;

            if (item.type == 'file') {
                $span.attr('data-file', reference + item.name).append($delete);
                $delete.click(function(){ deleteSnippet(reference + item.name); });
                record = $link.append(item.name);
            }

            var el = $li.addClass(item.type).append($span.append(record));

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

    $('#addLibrary').click(function(e){
        e.preventDefault();
        addLibrary();
    });

    $('#saveSnippet').click(function(e){
        e.preventDefault();
        saveSnippet(editor.getValue());
    });

    $('#loadSnippet').click(function(e){
        e.preventDefault();
        toggleSnippetList();
    });

    $('.snippets-reload a').click(function(e){
        e.preventDefault();
        loadSnippetsList();
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

    loadPhpVersions();
    loadSnippetsList();
    loadLibrariesList();
});