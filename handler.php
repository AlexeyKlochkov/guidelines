<?php
/**
 * Created by PhpStorm.
 * User: aklochko
 * Date: 2/18/15
 * Time: 10:31 AM
 */
require_once("Lgc.php");


if (isset($_POST["request"])){
    switch($_POST["request"]){
        case "Get All Sections":{
            $sections=Lgc::getAllSections();
            echo (json_encode($sections));
            break;
        }
        case "Save Section":{
            $result["success"]=Lgc::saveSectionHtml($_POST["sectionId"],$_POST["sectionHTML"]);
            echo (json_encode($result));
            break;
        }
        case "Add Section":{
            $sectionId=Lgc::addSection(-1);
            echo (json_encode($sectionId));
            break;
        }
        default:{
            break;
        }
    }
}