
<?php
    $Host = "localhost"; # no http://
    $Page = "/quiz.php"; # leading slash. Could be like /path/to/script.php, even /path/to/script.php?get_data=here
    $Data = "foo=1&another_foo=2";
    $Port = 80;
    $TimeOut = 30; # seconds
    if ($Conn = @fsockopen($Host,$Port,$ErrNo,$ErrStr,$TimeOut)) {
        fwrite($Conn,"POST {$Page} HTTP/1.1\\r\");
        fwrite($Conn,"Host: {$Host}\\r\");
        fwrite($Conn,"User-Agent: {$_SERVER['USER_AGENT']}\\r\");
        fwrite($Conn,"Keep-Alive: 300\\r\");
        fwrite($Conn,"Connection: keep-alive\\r\");
        fwrite($Conn,"Content-Type: application/x-www-form-urlencoded\\r\");
        fwrite($Conn,"Content-Length: " . strlen($Data) . "\\r\");
        fwrite($Conn,"\\r\");
        fwrite($Conn,$Data);
        $Response = "";
        while (!feof($Conn)) $Response .= fread($Conn,1024);
        fclose($Conn);
    }
    else die("Error {$ErrNo}: <b>{$ErrStr}</b>");
    
    echo($Response);
