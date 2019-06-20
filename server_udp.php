<?php
$host = "172.17.23.227";
$port = 8080;

/**-------------------------------------套接字创建------------------------------------- */

$socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP) or die("socket create fail");
socket_bind($socket, $host, $port) or die("bind socket fail\n");

/**----------------------------------------------------------------------------------- */

while (true) {

    socket_recvfrom($socket, $buf, 4096, 0, $remote_ip, $remote_port);

    // echo "UDP已获取数据：" . $buf . "\n";


    /**---------------------HTTP数据报头部处理--------------------- */
                        
    $requ_line = explode("\r\n", $buf)[0];
    $requ_line_split = explode(" ", $requ_line);

    // 请求行
    $method = $requ_line_split[0];
    $url = $requ_line_split[1];
    $edition = $requ_line_split[2];

    // 首部行
    $host = explode(" ", explode("\r\n", $buf)[1])[1];
    $connection = explode(" ", explode("\r\n", $buf)[2])[1];
    $user_agent = explode(" ", explode("\r\n", $buf)[3])[1];
    $accept_language = explode(" ", explode("\r\n", $buf)[4])[1];
  	echo "=====================UDP=====================\n";
    echo "Method:".$method."\n";
    echo "Url:".$url."\n";
    echo "Edition:".$edition."\n";
    echo "Host:".$host."\n";
    echo "Connection:".$connection."\n";
    echo "User_Agent:".$user_agent."\n";
    echo "Accept_Language:".$accept_language."\n";
  	echo "=============================================\n\n";

    /**----------------------------------------------------------- */


    /**----------------------------------------------数据返回---------------------------------------------- */

    $msg_back = "HTTP/1.1" . " " . "200" . " " . "OK" . "\r\n" . "content-type: text/html" . "\r\ndate: " .date("h:i:sa") . "\r\n\r\n";
    $html = '<html>
    <head>
        <title>计网大作业</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>         
    </head>
    <body>
        <div>
            <h1>Hello UDP World</h1> 
            <h2>Hello UDP World</h2> 
            <h3>Hello UDP World</h3> 
            <h4>Hello UDP World</h4> 
            <h5>Hello UDP World</h5> 
            <h6>Hello UDP World</h6>
        </div>
    </body>
    </html>';
    $msg_back .= $html;
    $len = strlen($msg_back);
    socket_sendto($socket, $msg_back, $len, 0, $remote_ip, $remote_port);

    /**---------------------------------------------------------------------------------------------------- */

}

socket_close($socket);
