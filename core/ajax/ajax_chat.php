<?php



if(isset($_GET['page']) and $_GET['page'] == "getChatUserData") {

    if( empty( $_POST["userId"] ) ) {

        echo json_encode( array(
            "error" => true,
            "msg"   => "Sorry! No data found."
        ) );

    } else {

        // Select the chat user data
        $selectChat = easySelectA(array(
            "table"     => "users",
            "fields"    => "emp_firstname, emp_lastname, user_emp_id, emp_positions",
            "join"      => array(
                "left join {$table_prefix}employees on emp_id = user_emp_id"
            ),
            "where"     => array(
                "user_id"  => $_POST["userId"]
            )
        ));

        if( $selectChat !== false ) {

            $data = $selectChat["data"][0];

            $fromUser = safe_input($_SESSION["uid"]);
            $toUser = safe_input($_POST["userId"]);

            // Retrieve the latest few messages
            // $latest_msg = easySelectA(array(
            //     "table"     => "messages as messages",
            //     "fields"    => "msg_from_user as from_user, msg_to_user as to_user, msg_text, msg_datetime as datetime,
            //                     concat(fromUserEmp.emp_firstname, ' ', fromUserEmp.emp_lastname) as from_username
            //     ",
            //     "join"      => array(
            //         "left join {$table_prefix}users as fromUser on fromUser.user_id = msg_from_user",
            //         "left join {$table_prefix}employees as fromUserEmp on fromUserEmp.emp_id = fromUser.user_emp_id"
            //     ),
            //     "where"     => array(
            //         "messages.is_trash = 0 and msg_from_user in('{$fromUser}', '{$toUser}') "
            //     ),
            //     "limit" => array(
            //         "start"     => 0,
            //         "length"    => 10
            //     ),
            //     "orderby"   => array(
            //         "msg_id" => "DESC",
            //         "msg_datetime"  => "ASC"
            //     )

            // ));

            $latest_msg = easySelectD("
                SELECT data.*
                FROM 
                (
                    SELECT
                        msg_id, msg_from_user as from_user, msg_to_user as to_user, msg_text, msg_datetime as datetime,
                        concat(fromUserEmp.emp_firstname, ' ', fromUserEmp.emp_lastname) as from_username
                    FROM {$table_prefix}messages as messages
                    left join {$table_prefix}users as fromUser on fromUser.user_id = msg_from_user
                    left join {$table_prefix}employees as fromUserEmp on fromUserEmp.emp_id = fromUser.user_emp_id
                    WHERE messages.is_trash = 0 and msg_from_user in('{$fromUser}', '{$toUser}')
                    ORDER BY msg_id DESC
                    LIMIT 0, 10
                ) as data
                order by data.msg_id ASC
            ");


            echo json_encode(array(
                "name"      => $data["emp_firstname"] . " " . $data["emp_lastname"],
                "position"  => $data["emp_positions"],
                "latest_msg" => $latest_msg !== false ? $latest_msg["data"] : ""
            ));


        } else {

            echo json_encode( array(
                "error" => true,
                "msg"   => "Sorry! No data found."
            ) );

        }

    }

}


if(isset($_GET['page']) and $_GET['page'] == "getAvailableAnswer") {

    if(rand(1,3) == 1){
        /* Fake an error */
        header("HTTP/1.0 404 Not Found");
        die();
    }
    
    /* Send a string after a random number of seconds (2-10) */
    sleep(rand(2,10));
    echo("Hi! Have a random number: " . rand(1,10));

}

?>