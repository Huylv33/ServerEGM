<?php
include_once './../../Helpers/ApiHelper.php';
include_once './../../Models/Connection.php';
// insert
function insertGuest() {
    $connection = new Connection();
    $con = $connection->getConnection();
    $json_receive = file_get_contents('php://input');
    $obj_receive = JsonHandler::decode($json_receive,true);
    $address = $obj_receive['address'];
    $ex_id = $obj_receive['ex_id'];
    $name = $obj_receive['name'];
    $time = $obj_receive['time'];
    $memo = $obj_receive['memo'];
    $image = $obj_receive['image'];
    $sql_query = "INSERT into guest(exhibition_id,name,image,address,time,memo) 
    values('$ex_id','$name','$image','$address','$time','$memo');
    UPDATE exhibition SET number_guests = number_guests + 1 WHERE id = $ex_id";
    if (mysqli_multi_query($con,$sql_query)) {
        echo ApiHelper::responseSuccess('Insert Successfully','');
    }
    else {
        echo ApiHelper::responseFail(mysqli_error_list($con),'');
    }
    mysqli_close($con);
}
insertGuest();  
