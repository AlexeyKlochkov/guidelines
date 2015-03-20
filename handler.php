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
        case "Save Index":{
            $result["success"]=Lgc::saveIndex($_POST["sectionId"],$_POST["name"]);
            echo (json_encode($result));
            break;
        }
        case "Add Section":{
            $sectionId=Lgc::addSection(-1);
            echo (json_encode($sectionId));
            break;
        }
        case "Get Section Types":{
            $sectionType=Lgc::getSectionType();
            echo (json_encode($sectionType));
            break;
        }
        case "Get Current Section Type":{
            $sectionType=Lgc::getCurSectionType($_POST["sectionId"]);
            echo (json_encode($sectionType));
            break;
        }

        case "Save Anchor":{
            $sectionType=false;
            if ($_POST["region"]!="")
                $sectionType=Lgc::saveAnchor($_POST["id"],1,$_POST["region"]);
            if ($_POST["media"]!="")
                $sectionType=Lgc::saveAnchor($_POST["id"],3,$_POST["media"]);
            if ($_POST["program"]!="")
                $sectionType=Lgc::saveAnchor($_POST["id"],2,$_POST["program"]);
            if ($_POST["chanel"]!="")
                $sectionType=Lgc::saveAnchor($_POST["id"],4,$_POST["chanel"]);
            echo (json_encode($sectionType));
            break;
        }
        default:{
            break;
        }
    }
}