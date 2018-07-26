<?php
include_once './../../Helpers/ApiHelper.php';
include_once './../../Models/Connection.php';
include_once './../../Models/JsonHandler.php';
function deleteGuest() {
    $connection = new Connection();
    $con = $connection->getConnection();
    $json_receive = file_get_contents('php://input');
    $obj_receive = JsonHandler::decode($json_receive,true);
    $id = $obj_receive['id'];
    $ex_id = $obj_receive['ex_id'];
    $sql_query = "update guest set deleted_at = CURRENT_TIMESTAMP where id = $id; 
                  update exhibition set number_guests = number_guests - 1 where id = $ex_id;
                  ";
    $obj = array("id" => $id);
    if (mysqli_multi_query($con,$sql_query)) {
        $json_return = JsonHandler::encode($obj);
        echo ApiHelper::responseSuccess('delete successfully',$json_return);
    }
    else {
        echo ApiHelper::responseFail('delete fail','');
    }
    mysqli_close($con);
}
deleteGuest();
