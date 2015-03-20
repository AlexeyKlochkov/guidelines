<?php
/**
 * Created by PhpStorm.
 * User: aklochko
 * Date: 2/11/15'ceT6b)dFxt6p'
 * Time: 9:44 AM
 */
date_default_timezone_set('America/Los_Angeles');


class Lgc {

    /**
     * @return PDO
     */
    public static function connect() {
        return $db = new PDO('mysql:dbname=lgc;host=localhost', 'root', '', array(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true, PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
    }

    /**
     *
     * @return array of sections name
     */
    public static function getSectionType() {
        $db = Lgc::connect();
        if ($stmt = $db->prepare("SELECT st.id,st.name FROM section_type as st")) {
            $stmt->bindColumn('name', $sectiontypename, PDO::PARAM_STR);
            $stmt->bindColumn('id', $sectiontypeid, PDO::PARAM_STR);
            if ($stmt->execute()) {
                $result["success"] = true;
                while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
                    $result["sectiontypes"][$sectiontypeid] = $sectiontypename;
                }
            } else
                $result["success"] = false;
        } else
            $result["success"] = false;
        return $result;
    }

    /**
     *
     * @param $sectionTypeId
     * @param $title
     * @return mixed
     */
    public static function getSectionName($sectionTypeId,$title){
        $db = Lgc::connect();
        if ($stmt = $db->prepare("SELECT s.name,s.title,s.html FROM section as s WHERE s.section_type_id = :sectiontypeid AND s.title LIKE :title ")) {
            $stmt->bindValue(':sectiontypeid', $sectionTypeId, PDO::PARAM_INT);
            $stmt->bindValue(':title','%'.$title.'%', PDO::PARAM_STR);
            $stmt->bindColumn('name', $sectionName, PDO::PARAM_STR);
            $stmt->bindColumn('title', $sectionTitle, PDO::PARAM_STR);
            $stmt->bindColumn('html', $sectionHtml, PDO::PARAM_STR);

            if ($stmt->execute()) {
                $result["success"] = true;
                while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
                    $result["sectionname"][] = $sectionName;
                }
            } else
                $result["success"] = false;
        } else
            $result["success"] = false;
        return $result;
    }

    /**
     * @return mixed
     */
    public static function getAllSections(){
        $db = Lgc::connect();
        $i=0;
        if ($stmt = $db->prepare("SELECT s.id,s.html FROM section as s ORDER BY s.ord ASC")) {
            $stmt->bindColumn('id', $sectionId, PDO::PARAM_INT);
            $stmt->bindColumn('html', $sectionHTML, PDO::PARAM_STR);
            if ($stmt->execute()) {
                $result["success"] = true;
                while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
                    $result["sections"][$i]["sectionId"] = $sectionId;
                    $result["sections"][$i]["sectionHTML"] = $sectionHTML;
                    $i++;
                }
            } else
                $result["success"] = false;
        } else
            $result["success"] = false;
        return $result;
    }
    /**
     * @return mixed
     */
    private static function getSectionIdByType($type,$value){
        $db = Lgc::connect();
        if ($stmt = $db->prepare("SELECT DISTINCT section_id FROM anchor WHERE (section_type_id=:type AND name in ($value))")) {
            $stmt->bindValue(':type', $type, PDO::PARAM_INT);
            $stmt->bindColumn('section_id', $sectionId, PDO::PARAM_INT);
            if ($stmt->execute()) {
                $result["success"] = true;
                while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
                    $result["sections"][] = $sectionId;
                }
            } else
                $result["success"] = false;
        } else
            $result["success"] = false;
        return $result;
    }

    /**
     * @return mixed
     */
    public static function getSectionBySearch($array){
        $sections=$array["content"];
        $keyword=$array["keyword"];
        $i=0;
        if (!is_null($sections)) {
            $where = "SELECT DISTINCT section_id FROM anchor WHERE ";
            foreach ($sections as $key => $value) {
                $list="'";
                $list .= implode("','", $value);
                $list .="'";
                $section[]=LGC::getSectionIdByType($key,$list);
                $i++;
            }
            $result=Array();
            foreach ($section as $item){
                foreach($item["sections"] as $v) {
                    array_push($result,$v);
                }
            }
            $tmp=array_count_values($result);
            $final=Array();

            foreach($tmp as $key=>$value){
                if ($value==$i){
                    array_push($final,$key);
                }
            }

            $where = implode(",", $final);
            $where = "WHERE s.id in (" . $where . ") ";
        }
        else $where="";
        if ($keyword!="" && $where!=""){
            $keyword=" AND s.html LIKE '%".$keyword."%'";
        }
        elseif ($keyword!=""){
            $keyword="WHERE s.html LIKE '%".$keyword."%'";
        }
        else $keyword="";
        $db = Lgc::connect();
        $i=0;
        if ($stmt = $db->prepare("SELECT s.id,s.html FROM section s ".$where.$keyword)) {
            $stmt->bindColumn('id', $sectionId, PDO::PARAM_INT);
            $stmt->bindColumn('html', $sectionHTML, PDO::PARAM_STR);
            if ($stmt->execute()) {
                while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
                    $result["sections"][$i]["sectionId"] = $sectionId;
                    $result["sections"][$i]["sectionHTML"] = $sectionHTML;
                    $i++;
                }
                if ($i>0) {
                    $result["success"] = true;
                }
                else{
                    $result["success"]=false;
                }
            } else
                $result["success"] = false;
        } else
            $result["success"] = false;
        return $result;
    }
    /**
     * @return mixed
     */
    public static function getSectionByType(){
        $db = Lgc::connect();
        $i=0;
        if ($stmt = $db->prepare("SELECT DISTINCT a.name as anchor,st.name as sectionName,a.section_type_id as sectionTypeId FROM anchor a INNER JOIN section_type st on a.section_type_id=st.id order by st.name")) {

            $stmt->bindColumn('sectionTypeId', $sectionTypeId, PDO::PARAM_INT);
            $stmt->bindColumn('anchor', $anchor, PDO::PARAM_STR);
            $stmt->bindColumn('sectionName', $section, PDO::PARAM_STR);
            if ($stmt->execute()) {
                $result["success"] = true;
                while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
                    $result["sections"][$section]["sectionName"] = $section;
                    $result["sections"][$section]["sectionTypeId"] = $sectionTypeId;
                    $result["sections"][$section]["anchor"][] = $anchor;
                    $i++;
                }
            } else
                $result["success"] = false;
        } else
            $result["success"] = false;
        return $result;
    }

    /**
     * @param $sectionid
     * @param $sectiontext
     */
    public static function saveSectionHtml($sectionid,$sectiontext){
    $db = Lgc::connect();
        if ($stm = $db->prepare("UPDATE section SET html=:sectiontext WHERE id=:sectionid")){
            $stm->bindValue(":sectiontext",$sectiontext,PDO::PARAM_STR);
            $stm->bindValue(":sectionid",$sectionid,PDO::PARAM_STR);
            if ($stm->execute())
                $result["success"]=true;
            else
                $result["success"]=false;
        }
        else
            $result["success"]=false;
        return($result);
    }

    /**
     * @param $order
     */
    public static function addSection($order){
        $db = Lgc::connect();
        if (!isset($order) || ($order==-1)){
            $order="SELECT max(ord)+1 FROM section";
        }
        else $order="(:sectiontext,$order)";
        if ($stm = $db->prepare("INSERT IGNORE INTO section (ord) (".$order.")")){
            if ($stm->execute()) {
                $last_id = $db->lastInsertId();
                $result["sectionId"]=$last_id;
                $result["success"]=true;
            }
            else {
                $result["success"]=false;
                $result["reason"]="Cannot insert into DB".$order;
            }
        }
        else {
            $result["success"]=false;
            $result["reason"]="DB connection problem";
        }
        return $result;
    }
    /**
     * @param $sectionId
     * @param $newOrder
     */
    public static function sectionReorder($sectionId,$newOrder){
        $db = Lgc::connect();
        if ($stm = $db->prepare("UPDATE section SET ord=:ord WHERE id=:sectionId")){
            $stm->bindValue(":sectionId",$sectionId,PDO::PARAM_INT);
            $stm->bindValue(":ord",$newOrder,PDO::PARAM_INT);
            if ($stm->execute()) {
                $result["success"]=true;
            }
            else {
                $result["success"]=false;
            }
        }
    }
    /**
     * @param $sectionId
     */
    public static function deleteSection($sectionId){
        $db = Lgc::connect();
        if ($stm = $db->prepare("DELETE FROM section WHERE id=:sectionId")){
            $stm->bindValue(":sectionId",$sectionId,PDO::PARAM_INT);
            if ($stm->execute())
                $result["success"]=true;
            else
                $result["success"]=false;
        }
        else
            $result["success"]=false;
        return $result;
    }

    /**
     * @param $id
     * @return mixed
     */
    public static function getCurSectionType($id) {
        $db = Lgc::connect();
        if ($stmt = $db->prepare("SELECT section_type_id,name FROM anchor where section_id=:id")) {
            $stmt->bindValue(":id",$id,PDO::PARAM_INT);
            $stmt->bindColumn('name', $name, PDO::PARAM_STR);
            $stmt->bindColumn('section_type_id', $type, PDO::PARAM_STR);
            if ($stmt->execute()) {
                $result["success"] = true;
                while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
                    $result[$type]=$name;
                }
            } else
                $result["success"] = false;
        } else
            $result["success"] = false;
        return $result;
    }
    public static function getIndexes() {
        $db = Lgc::connect();
        if ($stmt = $db->prepare("SELECT section_id,name FROM indexes order by name")) {
            $stmt->bindColumn('name', $name, PDO::PARAM_STR);
            $stmt->bindColumn('section_id', $sectionId, PDO::PARAM_INT);
            if ($stmt->execute()) {
                $result["success"] = true;
                while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
                    $result["indexes"][$sectionId][]=$name;
                }
            } else
                $result["success"] = false;
        } else
            $result["success"] = false;
        return $result;
    }

    /**
     * @param $id
     * @param $sectionId
     * @param $value
     * @return mixed
     */
    public static function saveAnchor($id,$sectionId,$value){
        $db = Lgc::connect();

        if ($stm = $db->prepare("INSERT INTO anchor (section_id,section_type_id,name) VALUES (:id,:sectionId,:value)
                                 ON DUPLICATE KEY UPDATE section_id=:id, section_type_id=:sectionId, name=:value")){
            $stm->bindValue(":id",$id,PDO::PARAM_INT);
            $stm->bindValue(":sectionId",$sectionId,PDO::PARAM_INT);
            $stm->bindValue(":value",$value,PDO::PARAM_STR);
            if ($stm->execute()) {

                $result["success"]=true;
            }
            else {
                $result["success"]=false;
                $result["reason"]="Cannot insert into DB";
            }
        }
        else {
            $result["success"]=false;
            $result["reason"]="DB connection problem";
        }
        return $result;
    }

    public static function saveIndex($sectionId,$name){
        $db = Lgc::connect();

        if ($stm = $db->prepare("INSERT INTO indexes (section_id,name) VALUES (:sectionId,:name)
                                 ON DUPLICATE KEY UPDATE name=:name")){
            $stm->bindValue(":sectionId",$sectionId,PDO::PARAM_INT);
            $stm->bindValue(":name",$name,PDO::PARAM_STR);
            if ($stm->execute()) {

                $result["success"]=true;
            }
            else {
                $result["success"]=false;
                $result["reason"]="Cannot insert into DB";
            }
        }
        else {
            $result["success"]=false;
            $result["reason"]="DB connection problem";
        }
        return $result;
    }
}