<!DOCTYPE html>
<html>
<head>
    <title>Legal Copyrighting Guidelines</title>
    <!-- Make sure the path to CKEditor is correct. -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
    <script src="js/jquery-2.1.3.min.js"></script>
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
    <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
    <link href="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0-rc.2/css/select2.min.css" rel="stylesheet" />
    <script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0-rc.2/js/select2.min.js"></script>
    <link href="css/search.css" rel="stylesheet">
</head>
<script type="text/javascript">
    $(document).ready(function() {
        $(".js-example-basic-single").select2({
            placeholder:"Select keyword",
            allowClear: true
        });
    });
</script>
<body>
<div id="content">
    <h1>Search form</h1>
    <div class="alert alert-danger" role="alert">No search result!</div>
    <form method="POST" action = "index.php" class="form-signin">

    <?php
         session_start();
        require_once("Lgc.php");
        $sections=LGC::getSectionByType();
        foreach ($sections["sections"] as $key=>$value){
            echo '<div><label for='.$value["sectionTypeId"].'>'.$key.': </label><select class="js-example-basic-single form-control" name=content['.$value["sectionTypeId"].'][] id='.$value["sectionTypeId"].' multiple>';
            $i++;
            foreach ($value["anchor"] as $val) {
                    echo '<option value='.$val.' label='.$val.'>'.$val.'</option>';
                }
            echo "</select></div>";
        }
    ?>
        <div><label for="keyword">Keyword</label><input class="form-control" name="keyword" id="keyword" type="text"></div>
        <br>
        <input type="hidden" name="search" value="submited">
        <button class="btn btn-primary" type="submit">Search</button>  <a href="index.php">Show full list</a>
    </form>

</div>
</body>
</html>
