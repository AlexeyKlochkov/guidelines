<!DOCTYPE html>
<html>
<head>
    <title>Legal Copyrighting Guidelines</title>
    <!-- Make sure the path to CKEditor is correct. -->
    <script src="ckeditor/ckeditor.js"></script>
    <script src="ckeditor/adapters/jquery.js"></script>
    <script src="js/jquery-2.1.3.min.js"></script>
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
    <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>

    <!-- Bootstrap Core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="css/shop-item.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-default navbar-fixed-top" role="navigation">
    <div class="container">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">

            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar">1</span>
            </button>
            <a class="navbar-brand" href="index.php">Home</a>
        </div>
        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav">
                <li>
                    <a href="index.php?show_index=1">Index</a>
                </li>
                <li>
                    <a href="#">Settings</a>
                </li>
            </ul>
        </div>
        <!-- /.navbar-collapse -->
    </div>
    <!-- /.container -->
</nav>
<div id="container">
    <div class="row">
        <div class="col-md-2">
            <div class="list-group" id="leftCol">
                <ul class="nav nav-stacked" id="sidebar">
                    <li><a href="search.php" class="list-group-item">New Search</a></li>
                    <li><a href="javascript:createEditor();" class="list-group-item">New Paragraph</a></li>
                    <?php if (isset($_POST["search"])):?>
                    <li>
                        <p>Searching params:</p>
                        <?php

                            foreach($_POST["content"] as $section){
                                foreach ($section as $value){
                                    echo '<p>'.$value.'</p>';
                                }
                            }
                        ?>
                    </li>
                    <?php endif;?>
                </ul>
            </div>
        </div>
        <div class="col-md-9" id="content">
            <?php
            session_start();
            require_once("Lgc.php");
            if (isset($_GET["e"])){

            }
            if (!isset($_SESSION["username"])){
                header('Location:signin.html');
            }
            if (isset($_POST["search"]) && !isset($_GET["show_index"])){
                $sections = LGC::getSectionBySearch($_POST);
                if ($sections["success"]) {
                    foreach ($sections["sections"] as $section) {
                        if ($_SESSION["isAdmin"]) $contenteditable = "contenteditable=true";
                        else $contenteditable = "";
                        echo '<hr><div id=' . $section['sectionId'] . ' ' . $contenteditable . '>' . $section['sectionHTML'] . '</div><hr>';
                    }
                }else {
                    header ("Location:search.php?e=1");
                }
            }
            else {
                if (isset($_GET["show_index"]) && $_GET["show_index"]==1){
                    $indexes=LGC::getIndexes();
                    echo "<h2>Indexes:</h2>";
                    echo "<ol>";
                    foreach ($indexes["indexes"] as $index){
                        foreach ($index as $value) {
                            echo "<a href='#$value'><li>$value</li></a>";
                       }
                    }
                    echo "</ol>";
                }
                $sections = LGC::getAllSections();
                foreach ($sections["sections"] as $section) {
                    if ($_SESSION["isAdmin"]) $contenteditable="contenteditable=true";
                    else $contenteditable="";
                    echo '<hr><div id=' . $section['sectionId'] . ' '.$contenteditable.'>' . $section['sectionHTML'] . '</div><hr>';
                }
            }
            ?>
        </div>
    </div>
</div>
<script>

    CKEDITOR.disableAutoInline = true;

    function createEditor() {
        var sectionId=false;
        $.ajax({
            url: 'handler.php',
            global: false,
            type: "POST",
            data: {request: "Add Section"},
            dataType:"json",
            success: function (result) {
                if (result['sectionId']) {
                    sectionId = result['sectionId'];
                    if (sectionId) {
                        $("#content").prepend('<hr><div id=' + sectionId + ' contenteditable=true>Type new text here...</div><hr>');
                        $('#'+sectionId).focus();
                        CKEDITOR.inline(sectionId, {
                            on: {
                                blur: function (event) {
                                    var params = {
                                        request:"Save Section",
                                        sectionHTML: event.editor.getData(),
                                        sectionId:sectionId
                                    };
                                    $.ajax({
                                        url: 'handler.php',
                                        global: false,
                                        type: "POST",
                                        data: params,
                                        dataType:"html",
                                        success: function (result) {
                                        }
                                    });
                                }
                            }
                        });
                    }
                }
            }
        });

    }

    $(document).ready(function() {
        /* activate sidebar */
        $('#sidebar').affix({
            offset: {
                top: 45
            }
        });

        /* activate scrollspy menu */
        var $body   = $(document.body);
        var navHeight = $('.navbar').outerHeight(true) + 10;

        $body.scrollspy({
            target: '#leftCol',
            offset: navHeight
        });

        /* smooth scrolling sections */
        $('a[href*=#]:not([href=#])').click(function() {
            if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname) {
                var target = $(this.hash);
                target = target.length ? target : $('[name=' + this.hash.slice(1) +']');
                if (target.length) {
                    $('html,body').animate({
                        scrollTop: target.offset().top - 50
                    }, 1000);
                    return false;
                }
            }
        });
                $("div[contenteditable='true']").each(function () {
                    var sectionId=$(this).attr("id");
                    CKEDITOR.inline(sectionId, {
                        on: {
                            blur: function (event) {
                                var params = {
                                    request:"Save Section",
                                    sectionHTML: event.editor.getData(),
                                    sectionId:sectionId
                                };
                                $.ajax({
                                    url: 'handler.php',
                                    global: false,
                                    type: "POST",
                                    data: params,
                                    dataType:"html",
                                    success: function (result) {

                                    }
                                });
                            }
                        }
                    });
                });
        });

</script>
</body>
</html>

