<?php
    $host = "172.17.23.227";
    $port = 8080;


    /**-------------------------------创建，绑定，监听-------------------------------*/
    $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP) or die("create socket fail\n");
    socket_bind($socket, $host, $port) or die("bind socket fail\n");
    socket_listen($socket) or die("listen socket fail\n");
    $conn_pool = array($socket); 
    /**----------------------------------------------------------------------------*/


    while(true){

        $reads = $conn_pool;
        $writes = $except = $tv = null;
        socket_select($reads, $writes, $except, $tv); 


        foreach ($reads as $read) { // 本身存在一个socket=4，遍历conn_pool中所有连接，如果是4就等待新的连接，如果不是4就保持连接
            // $socket的id永远等于4
            if($read == $socket) { // 等待新客户的加入;
                $new_conn = socket_accept($socket);
                $conn_pool[] = $new_conn; // 把新用户的连接加入conn_pool
                echo "添加" . $new_conn . "成功\n";
            }
            else {
                // 保持连接
                $msg_receive = socket_read($read, 4096);
                
                if ("" == $msg_receive) { // 如果客户端关闭了连接，则释放
                    socket_close($read);
                    $conn_pool = array_diff($conn_pool, [$read]); // 从conn_pool中删除被释放的连接
                    echo $read . "已关闭\n\n";
                } else {

                    // if (socket_getpeername($read,$remote_ip)) {
                    //     echo $read . "(" . $remote_ip . ")：" . $msg_receive . "\n";
                    // } else { // 如果无法获取ip
                    //     echo socket_last_error($read)."\n";
                    //     echo $read . "(未知ip)：" . $msg_receive . "\n";
                    // }


                    /**---------------------HTTP数据报头部处理--------------------- */
                    
                    $requ_line = explode("\r\n", $msg_receive)[0];
                    $requ_line_split = explode(" ", $requ_line);

                    // 请求行
                    $method = $requ_line_split[0];
                    $url = $requ_line_split[1];
                    $edition = $requ_line_split[2];

                    // 首部行
                    $host = explode(" ", explode("\r\n", $msg_receive)[1])[1];
                    $connection = explode(" ", explode("\r\n", $msg_receive)[2])[1];
                    $user_agent = explode(" ", explode("\r\n", $msg_receive)[3])[1];
                    $accept_language = explode(" ", explode("\r\n", $msg_receive)[4])[1];
                  	echo "=====================TCP=====================\n";
                    echo "Method:".$method."\n";
                    echo "Url:".$url."\n";
                    echo "Edition:".$edition."\n";
                    echo "Host:".$host."\n";
                    echo "Connection:".$connection."\n";
                    echo "User_Agent:".$user_agent."\n";
                    echo "Accept_Language:".$accept_language."\n";
                  	echo "=============================================\n";
                    
                    /**----------------------------------------------------------- */


                    /**------------------------------------返回数据报------------------------------------ */

                    $msg_back = "HTTP/1.1" . " " . "200" . " " . "OK" . "\r\n" . "content-type: text/html" . "\r\ndate: " .date("h:i:sa") . "\r\n\r\n";
                    $html = '<html>
                    <head>
                        <title>计网大作业</title>
                        <meta charset="utf-8">
                        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>         
                    </head>
                    <body>
                        <div>
                            <h1>Hello TCP World</h1> 
                            <h2>Hello TCP World</h2> 
                            <h3>Hello TCP World</h3> 
                            <h4>Hello TCP World</h4> 
                            <h5>Hello TCP World</h5> 
                            <h6>Hello TCP World</h6>
                        </div>
                    </body>
                    </html>';
                    $msg_back .= $html;
                    socket_write($read, $msg_back); // 返回数据报

                    /**---------------------------------------------------------------------------------- */


                    /**------------------------------------客户端之间交互----------------------------------- */

                    // foreach ($conn_pool as $others_conn) { // 用于不同客户端之间的交互
                    //     if($others_conn != $socket && $others_conn != $read) {
                    //         socket_getpeername($read,$remote_ip);
                    //         socket_write($others_conn, "msg_receive from \"".$remote_ip."\":".$msg_receive);
                    //     }
                    // }

                    /**------------------------------------------------------------------------------------ */


                }
            }
        }

    }
    socket_close($socket);
