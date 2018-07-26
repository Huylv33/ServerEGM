<?php 
include_once './../../Helpers/ApiHelper.php';
include_once './../../Models/Connection.php';
include_once './../../Models/JsonHandler.php';
function orderBy($sort) {
    switch ($sort) {
        case null: $sort = 'created_at';break;
        case 'timeAZ': $sort = 'time_begin ASC';break;
        case 'timeZA': $sort = 'time_end DESC';break;
        case 'nameAZ': $sort = 'name ASC';break;
        case 'nameZA': $sort = 'name DESC';break;
        case 'countAZ' : $sort = 'number_guests ASC';break;
        case 'countZA' : $sort = 'number_guests DESC';break;
    }
    return $sort;
}
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
    $obj_receive = JsonHandler::decode($json_receive,true);
    $sort = orderBy($obj_receive['sort']);
    if ($result1 = mysqli_query($con, "select count(*) as total from exhibition where deleted_at is null")) {
        $row  = mysqli_fetch_assoc($result1);
        $limit = 3;
        $total_page = ceil($row['total'] / $limit);
        $current_page = $obj_receive['page'];
        if ($current_page > $total_page) {
            $current_page = $total_page;
        }
        else if ($current_page < 1) {
            $current_page = 1;
        }
        $start = ($current_page - 1) * $limit;  
        $sql_query = "select id, name, number_guests, time_begin, memo from exhibition 
                where deleted_at is null
                order by $sort limit $start, $limit";
        echo $sql_query;
        if ($result2 = mysqli_query($con, $sql_query)) {
            $obj = mysqli_fetch_all($result2, MYSQLI_ASSOC);
            $obj["number"] = pageNumberDisplay($current_page, $total_page);
            $obj_return = JsonHandler::encode($obj);
            echo ApiHelper::responseSuccess('display successfully',$obj_return);
            mysqli_free_result($result2);
        }        
        else echo ApiHelper::responseFail('display fail','');
        mysqli_free_result($result1);
    }
    else echo ApiHelper::responseFail('display fail','');
    mysqli_close($con);
}
display();
