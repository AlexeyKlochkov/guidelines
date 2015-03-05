<?php
/**
 * Created by PhpStorm.
 * User: aklochko
 * Date: 2/11/15
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
}