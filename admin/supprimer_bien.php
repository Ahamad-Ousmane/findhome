<?php 
session_start();
include 'db.php';

if(isset($_GET['id'])){
    $id = $_GET['id'];

    $sql= "UPDATE properties SET is_delete = true WHERE id=:id";
    $stmt = $pdo ->prepare($sql);
    $stmt->execute(array(':id' => $id));

    if($stmt-> execute()){
        header('Location: properties_views.php');
        exit;
    } else {
        echo "Error";
    }
   

}elseif(isset($_GET['id_admin'])){
    $id_admin = $_GET['id_admin'];

    $sql= "UPDATE properties_admin SET is_delete = true WHERE id=:id";
    $stmt = $pdo ->prepare($sql);
    $stmt->execute(array(':id' => $id_admin));

    if($stmt-> execute()){
        header('Location: properties_views.php');
        exit;
    } else {
        echo "Error";
    }
   

}
?>