<?php
/**
 * Created by PhpStorm.
 * User: aklochko
 * Date: 3/9/15
 * Time: 3:03 PM
 */
include "adLDAP/src/adLDAP.php";
require_once("Lgc.php");
if (strpos($_SERVER['HTTP_REFERER'],"index.php")){
if (isset($_SESSION["username"])){
    header('Location:'.$_SERVER['HTTP_REFERER']);
}
    else {
        header('Location: signin.html');
    }
}
session_start();
function isAdmin($name){
    $db = new PDO('mysql:dbname=lgc;host=localhost', 'root', '', array(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true, PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
    if ($stmt = $db->prepare("SELECT is_admin FROM user WHERE name=:name")) {
        $stmt->bindValue(':name', $name, PDO::PARAM_STR);
        $stmt->bindColumn('is_admin', $isAdmin, PDO::PARAM_INT);
        if ($stmt->execute()) {
            $result["success"]=true;
            $stmt->fetchAll();
            $result["isAdmin"]=$isAdmin;
        } else {
            $result["success"] = false;
            $result["reason"] = "No user with this credentials in DB";
        }
    } else {
        $result["success"]=false;
        $result["reason"] = "Database error";
        }
    return $result;

}

if (isset($_POST)){
    $username=$_POST["login"];
    $password=$_POST["password"];
}
try{
    $adLDAP=new adLDAP();
}
catch(adLDAPException $e){
    $location="signin.html?e=1";
}
if ($adLDAP->authenticate($username, $password)) {
    $isAdminCheck=isAdmin($username);
    if ($isAdminCheck["success"]){
        $_SESSION["username"]=$username;
        $_SESSION["isAdmin"]=$isAdminCheck["isAdmin"];
        $location="search.php";
    }

}
else {
    $location="signin.html?e=2";
}
header('Location:'.$location);
