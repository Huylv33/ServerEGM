<?php 
include_once './../../Helpers/ApiHelper.php';
include_once './../../Models/Connection.php';
include_once './../../Models/JsonHandler.php';
// a
function pageNumberDisplay($current_page, $total_page) {
    $array = [];
    if ($total_page >= 6) {
        if ($current_page + 2 < $total_page) {
            if ($current_page > 4) {
                $array = [1,'...',$current_page - 1,$current_page, $current_page + 1, '...', $total_page];
            }
            elseif ($current_page === 4){
                $array = [1,2,3,4,5,'...',$total_page];
            } 
            elseif ($current_page === 3) {
                $array = [1,2,3,4,'...',$total_page];
            }
            elseif ($current_page === 2) {
                $array = [1,2,3,'...',$total_page];
            }
            elseif ($current_page === 1) {
                $array = [1,2,'...',$total_page];
            }
        }
        elseif ($current_page + 2 === $total_page){
            $array = [1,'...',$current_page - 1,$current_page,$current_page + 1, $total_page];
        }
        elseif ($current_page + 1 === $total_page) {
            $array = [1,'...',$current_page - 1,$current_page,$total_page];
        }
        else {
            $array = [1,'...',$total_page - 1, $total_page];
        }
    }
    else {
        if ($total_page === 1) {
            $array = [1];
        }
        elseif ($total_page === 2) {
            $array = [1,2];
        }
        elseif ($total_page === 3){
            $array = [1,2,3];
        }
        elseif ($total_page === 4){
            if ($current_page === 1) {
                $array = [1,2,'...',4];
            }
            else {
                $array = [1,2,3,4];
            }
        }
        else {
            if ($current_page === 1) {
                $array = [1,2,'...',5];
            }
            elseif ($current_page === 2) {
                $array = [1,2,3,'...',5];
            }
            else {
                $array = [1,2,3,4,5];
            }
        }
    }
    return $array;
} 
function display() {
    $connection = new Connection();
    $con = $connection->getConnection();
    $json_receive = file_get_contents('php://input');
    $obj_receive = JsonHandler::decode($json_receive);
    $ex_id = $obj_receive['ex_id'];
    $current_page = $obj_r['page'];
    if ($result1 = mysqli_query($con, "select count(*) as total from guest 
    where deleted_at is null and exhibition_id = $ex_id")) {
        $row  = mysqli_fetch_assoc($result1);
        $limit = 2;
        $total_page = ceil($row['total'] / $limit);
        if ($current_page > $total_page) {
            $current_page = $total_page;
        }
        else if ($current_page < 1) {
            $current_page = 1;
        }   
        $start = ($current_page - 1) * $limit;  
        $sql_query = "select g.id, exhibition_id, g.name, time, g.image, address, g.memo from guest g
                     join exhibition e  on exhibition_id = e.id where g.deleted_at is null
                     and exhibition_id = $ex_id
                     limit $start, $limit";
        echo $sql_query . PHP_EOL;
        if ($result2 = mysqli_query($con, $sql_query)) {
            $obj = mysqli_fetch_all($result2, MYSQLI_ASSOC);
            $obj["number"] = pageNumberDisplay($current_page, $total_page);
            var_dump($obj);
            $json_return= JsonHandler::encode($obj);
            echo ApiHelper::responseSuccess('display successfully',$json_return);
            mysqli_free_result($result2);
        }        
        else echo ApiHelper::responseFail('display fail','');
        mysqli_free_result($result1);
    }
    else echo ApiHelper::responseFail('display fail','');

    mysqli_close($con);
}
display();
