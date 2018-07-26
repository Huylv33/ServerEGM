<?php
include_once './../../Helpers/ApiHelper.php';
include_once './../../Models/Connection.php';
function editExhibition() {
    $connection = new Connection();
    $con = $connection->getConnection();
    $json_receive = file_get_contents('php://input');
    $obj_receive = JsonHandler::decode($json_receive,true);
    $id = $obj_receive['id'];
    $name = $obj_receive['name'];
    $image = $obj_receive['image'];
    $address = $obj_receive['address'];
    $time = $obj_receive['time'];
    $memo = $obj_receive['memo'];
    $sql_query = "Update guest set name = '$name', image = '$image', address = '$address',
                  time = '$time', memo = '$memo'
                  where id = '$id'";
    echo $sql_query;
    if (mysqli_query($con,$sql_query)) {
        echo ApiHelper::responseSuccess('edit successfully','');
    }
    else {
        echo ApiHelper::responseFail(mysqli_error_list($con),'');
    }
    mysqli_close($con);
}
editExhibition();   
