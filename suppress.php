<?php
require_once(__DIR__ .'/admin/db.php');


if($_SERVER['REQUEST_METHOD']==='POST'){


    $id = $_POST['id'];

    $sql="UPDATE rdvs SET is_delete=true WHERE id=:id";
    $stmt = $pdo->prepare($sql);
    $result=$stmt -> execute([':id'=>$id]);
    var_dump($result);
    header("Location: notifications.php");
    exit();
}

?>