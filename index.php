<!DOCTYPE html>
<html>
<head>
    <title>Legal Copyrighting Guidelines</title>
    <!-- Make sure the path to CKEditor is correct. -->
    <script src="ckeditor/ckeditor.js"></script>
    <script src="ckeditor/adapters/jquery.js"></script>
    <script src="js/jquery-2.1.3.min.js"></script>
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
<h1 style="margin-left:40%;">Legal Copywriting Guidelines</h1>
<div id="content">
    <?php
    session_start();
    require_once("Lgc.php");
    if (isset($_GET["e"])){

    }
    if (!isset($_SESSION["username"])){
        header('Location:signin.html');
    }
    if (isset($_POST["search"])){
        $sections = LGC::getSectionBySearch($_POST);
        if ($sections["success"]) {
            foreach ($sections["sections"] as $section) {
                if ($_SESSION["isAdmin"]) $contenteditable = "contenteditable=true";
                else $contenteditable = "";
                echo '<div id=' . $section['sectionId'] . ' ' . $contenteditable . '>' . $section['sectionHTML'] . '</div>';
            }
        }else {
            header ("Location:search.php?e=1");
        }
    }
    else {
        $sections = LGC::getAllSections();
        foreach ($sections["sections"] as $section) {
            if ($_SESSION["isAdmin"]) $contenteditable="contenteditable=true";
            else $contenteditable="";
            echo '<div id=' . $section['sectionId'] . ' '.$contenteditable.'>' . $section['sectionHTML'] . '</div>';
        }
    }
    ?>
</div>
<input onclick="createEditor();" type="button" value="Add paragraph">
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
                        $("#content").append('<div id=' + sectionId + ' contenteditable=true>Type new text here...</div>');
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

