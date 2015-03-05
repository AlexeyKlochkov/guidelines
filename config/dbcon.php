<?php
/**
 * Created by PhpStorm.
 * User: aklochko
 * Date: 2/11/15
 * Time: 9:23 AM
 */
Class DB
{
    public static function connect() {
        return $db = new PDO('mysql:dbname=lgc;host=localhost', 'root', '', array(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true, PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
    }

    public static function getSectionType() {
        $db = DB::connect();
        if ($stmt = $db->prepare("SELECT st.id,st.name FROM section_type as st")) {
            $stmt->bindColumn('name', $sectiontypename, PDO::PARAM_STR);
            $stmt->bindColumn('id', $sectiontypeid, PDO::PARAM_STR);
            if ($stmt->execute()) {
                $result["success"] = true;
                while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
                    $result["sectiontypes"][$sectiontypeid] = $sectiontypename;
                }
            } else $result["success"] = false;
        } else $result["success"] = false;
        return $result;
    }
    public static function getSectionName($sectionTypeId,$title){
        $db = DB::connect();
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
            } else $result["success"] = false;
        } else $result["success"] = false;
        return $result;
    }
}