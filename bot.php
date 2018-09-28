<?php
    // กรณีต้องการตรวจสอบการแจ้ง error ให้เปิด 3 บรรทัดล่างนี้ให้ทำงาน กรณีไม่ ให้ comment ปิดไป
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    
    // include composer autoload
    require_once 'vendor/autoload.php';
    
    // การตั้งเกี่ยวกับ bot
    require_once 'bot_settings.php';
    require_once 'mysql_connect.php';
    //require_once 'quiz.php';
    // กรณีมีการเชื่อมต่อกับฐานข้อมูล
    //require_once("dbconnect.php");
    ///////////// ส่วนของการเรียกใช้งาน class ผ่าน namespace
    use LINE\LINEBot;
    use LINE\LINEBot\HTTPClient;
    use LINE\LINEBot\HTTPClient\CurlHTTPClient;
    //use LINE\LINEBot\Event;
    //use LINE\LINEBot\Event\BaseEvent;
    //use LINE\LINEBot\Event\MessageEvent;
    use LINE\LINEBot\MessageBuilder;
    use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
    use LINE\LINEBot\MessageBuilder\StickerMessageBuilder;
    use LINE\LINEBot\MessageBuilder\ImageMessageBuilder;
    use LINE\LINEBot\MessageBuilder\LocationMessageBuilder;
    use LINE\LINEBot\MessageBuilder\AudioMessageBuilder;
    use LINE\LINEBot\MessageBuilder\VideoMessageBuilder;
    use LINE\LINEBot\ImagemapActionBuilder;
    use LINE\LINEBot\ImagemapActionBuilder\AreaBuilder;
    use LINE\LINEBot\ImagemapActionBuilder\ImagemapMessageActionBuilder ;
    use LINE\LINEBot\ImagemapActionBuilder\ImagemapUriActionBuilder;
    use LINE\LINEBot\MessageBuilder\Imagemap\BaseSizeBuilder;
    use LINE\LINEBot\MessageBuilder\ImagemapMessageBuilder;
    use LINE\LINEBot\MessageBuilder\MultiMessageBuilder;
    use LINE\LINEBot\TemplateActionBuilder;
    use LINE\LINEBot\TemplateActionBuilder\DatetimePickerTemplateActionBuilder;
    use LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder;
    use LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder;
    use LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder;
    use LINE\LINEBot\MessageBuilder\TemplateBuilder;
    use LINE\LINEBot\MessageBuilder\TemplateMessageBuilder;
    use LINE\LINEBot\MessageBuilder\TemplateBuilder\ButtonTemplateBuilder;
    use LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselTemplateBuilder;
    use LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselColumnTemplateBuilder;
    use LINE\LINEBot\MessageBuilder\TemplateBuilder\ConfirmTemplateBuilder;
    use LINE\LINEBot\MessageBuilder\TemplateBuilder\ImageCarouselTemplateBuilder;
    use LINE\LINEBot\MessageBuilder\TemplateBuilder\ImageCarouselColumnTemplateBuilder;
    
    // เชื่อมต่อกับ LINE Messaging API
    
    $httpClient = new CurlHTTPClient(LINE_MESSAGE_ACCESS_TOKEN);
    $bot = new LINEBot($httpClient, array('channelSecret' => LINE_MESSAGE_CHANNEL_SECRET));
    
    // คำสั่งรอรับการส่งค่ามาของ LINE Messaging API
    $content = file_get_contents('php://input');
    
    // แปลงข้อความรูปแบบ JSON  ให้อยู่ในโครงสร้างตัวแปร array
    $events = json_decode($content, true);
    if(!is_null($events)){
        // ถ้ามีค่า สร้างตัวแปรเก็บ replyToken ไว้ใช้งาน
        $replyToken = $events['events'][0]['replyToken'];
        $userID = $events['events'][0]['source']['userId'];
        $sourceType = $events['events'][0]['source']['type'];
        $is_postback = NULL;
        $is_message = NULL;
        if(isset($events['events'][0]) && array_key_exists('message',$events['events'][0])){
            $is_message = true;
            $typeMessage = $events['events'][0]['message']['type'];
            $userMessage = $events['events'][0]['message']['text'];
            $idMessage = $events['events'][0]['message']['id'];
        }
        if(isset($events['events'][0]) && array_key_exists('postback',$events['events'][0])){
            $is_postback = true;
            $dataPostback = NULL;
            parse_str($events['events'][0]['postback']['data'],$dataPostback);;
            $paramPostback = NULL;
            if(array_key_exists('params',$events['events'][0]['postback'])){
                if(array_key_exists('date',$events['events'][0]['postback']['params'])){
                    $paramPostback = $events['events'][0]['postback']['params']['date'];
                }
                if(array_key_exists('time',$events['events'][0]['postback']['params'])){
                    $paramPostback = $events['events'][0]['postback']['params']['time'];
                }
                if(array_key_exists('datetime',$events['events'][0]['postback']['params'])){
                    $paramPostback = $events['events'][0]['postback']['params']['datetime'];
                }
            }
        }
    }
    
        
        ///// RECIVE POSTBACK /////
    if(!is_null($is_postback)){
        
        $dataPost = substr(json_encode($dataPostback),2,-5);
        switch ($dataPost){
            case "JOIN":
                $res = $bot->getProfile($userID);
                if ($res->isSucceeded()) {
                    $profile = $res->getJSONDecodedBody();
                    $displayName = $profile['displayName'];
                    echo "DISPLAY NAME : $displayName \n";
                    $pictureUrl = $profile['pictureUrl'];
                }

                    $conn = mysqli_connect($servername, $username, $password, $dbname);
                    $sql ="SELECT * FROM storeset WHERE isPlaying=1";
                    
                    //$sql = "INSERT INTO playingUsers (userId) VALUES ('$userID')";
                    $result = mysqli_query($conn, $sql);

                    if (mysqli_num_rows($result)) {
                        $sql ="INSERT INTO playingUsers (userId, DisplayName) VALUES ('$userID','$displayName')";
                        if(mysqli_query($conn, $sql)){
                            echo "YOU ARE JOINED";
                        }
                        echo "\nNew record created successfully\n";
                        //$textReplyMessage = "You are Joined";
                    } else {
                        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
                    }
   
                mysqli_close($conn);
                break;
                
            case 1:
                    ansCheck($dataPost);
                break;
            case 2:
                    ansCheck($dataPost);
                break;
            case 3:
                    ansCheck($dataPost);
                break;
        }
        mysqli_close($conn);
    }
        
        
        
        
       if(!is_null($is_postback)){
            //$textReplyMessage = "ข้อความจาก Postback Event Data ";
            if(is_array($dataPostback)){
               // $textReplyMessage = json_encode($dataPostback);
                $textReplyMessage = " ";
            }
           // if(!is_null($paramPostback)){
            //    $textReplyMessage.= " \r\nParams = ".$paramPostback;
           // }
            $replyData = new TextMessageBuilder($textReplyMessage);
        }
        
        
        if(!is_null($is_message)){
            switch ($typeMessage){
                case 'text':
                    //$userMessage = strtolower($userMessage); // แปลงเป็นตัวเล็ก สำหรับทดสอบ
                    switch ($userMessage) {
                        case "INFO":
                            
                            echo "INFO";

                            $res = $bot->getProfile('Uf69c696453ccd159aa2da4a4a6fd50cd');
                            if ($res->isSucceeded()) {
                                $profile = $res->getJSONDecodedBody();
                                $displayName = $profile['displayName'];
                                $pictureUrl = $profile['pictureUrl'];
                            }
                            

                            echo "NAME: $displayName\n";
                            echo "IMG $pictureUrl\n";
                            
                            break;
                            
                        case "CLEAR":
                            
                            $conn = mysqli_connect($servername, $username, $password, $dbname);
                            // Check connection
                            if (!$conn) {
                                die("Connection failed: " . mysqli_connect_error());
                            }
                            
                            //CHECK CURRENT DATA
                            $sql = "select * from storeset where isPlaying=1";
                            if ($result=mysqli_query($conn,$sql))
                            {
                                while ($obj=mysqli_fetch_object($result))
                                {
                                    echo "USER ID : $obj->userID";
                                    if ($userID<>$obj->userID){
                                        echo "You are not authorize to clear the game.";
                                        break;
                                    } else {
                                        mysqli_query($conn,'TRUNCATE TABLE playingSet');
                                        mysqli_query($conn,'TRUNCATE TABLE playingUsers');
                                        mysqli_query($conn,'UPDATE storeset SET isPlaying=0');
                                        $textReplyMessage = "Playing table cleared!";
                                        $replyData = new TextMessageBuilder($textReplyMessage);
                                        break;
                                    }
                                }
                            }
                            
                            
                            break;

                            //////////////////// PLAYING ////////////////////
                        case strpos($userMessage,'LoadQ'): // Running Question
                            
                            $setNumber = substr($userMessage,6);
                            $setNumber = intval($setNumber);
                            // Create connection
                            $conn = mysqli_connect($servername, $username, $password, $dbname);
                            // Check connection
                            if (!$conn) {
                                die("Connection failed: " . mysqli_connect_error());
                            }
                            //CHECK CURRENT DATA
                            $sql = "select * from storeset where setNumber='$setNumber'";
                            if ($result=mysqli_query($conn,$sql))
                            {
                                while ($obj=mysqli_fetch_object($result))
                                {
                                    echo "USER ID : $obj->userID";
                                    if ($userID<>$obj->userID){
                                        echo "Sorry, Set $setNumber is not yours";
                                        $textReplyMessage = "Sorry, set $setNumber is not yours";
                                        $replyData = new TextMessageBuilder($textReplyMessage);
                                        break;
                                }
                                    
                                    //echo $obj->qData;
                                    
                                    $qList = explode("\n",$obj->qData); //parse the rows
                                    echo count($qList); // Number of questions
                                    //echo $qList[3];
                                    
                                    //////////////////// RECORD PLAYING TABLE ////////////////////
                                    foreach ($qList as $value){
                                        $qLine = explode(",",$value);
                                        echo "\n";
                                        echo $qLine[1];
                                        $sql = "INSERT INTO playingSet (qNum, Question, Choise1, Choise2, Choise3, Ans) VALUES ('$qLine[0]', '$qLine[1]', '$qLine[2]', '$qLine[3]', '$qLine[4]', '$qLine[5]')";
                                        if (mysqli_query($conn, $sql)) {
                                            echo "New record created successfully";
                                          $textReplyMessage = "Question Set $setNumber is now playing";
                                        } else {
                                            echo "Error: " . $sql . "<br>" . mysqli_error($conn);
                                        }
                                    }
                                    
                                    echo "Set Number $setNumber is Ready to play";
                                    $textReplyMessage = "Set $setNumber is ready to play";
                                    $replyData = new TextMessageBuilder($textReplyMessage);
                                    
                                    $sql = "UPDATE storeset SET isPlaying=1 WHERE setNumber='$setNumber'";
                                    if (mysqli_query($conn, $sql)) {
                                        echo "WHATTT";

                                    }

                                    
                                }
                                mysqli_free_result($result);
                            }
                            
                            mysqli_close($conn);
                           
                            break;
                            
                           
                            
                            
                        case strpos($userMessage,'StoreQ'): // Record Questions to SQL
                            
                            $firstComma = stripos($userMessage,",");
                            $firstLine = stripos($userMessage,"\n");
                            
                            $userIdData = substr($userMessage,$firstLine+1,33); // DONE
                            $setNumber = substr($userMessage,$firstComma+1,3); // DONE
                            $qData = substr($userMessage,$firstLine+1);
                            $qData = substr($qData,stripos($qData,"\n")+1); // DONE
                            
                            if ($userIdData == $userID){
                                
                                // Create connection
                                $conn = mysqli_connect($servername, $username, $password, $dbname);
                                // Check connection
                                if (!$conn) {
                                    die("Connection failed: " . mysqli_connect_error());
                                }
                                
                                //CHECK CURRENT DATA
                                $sql = "SELECT * FROM storeset WHERE setNumber='$setNumber'";
                                $result = $conn->query($sql);
                                
                                if ($result->num_rows > 0) {
                                    // output data of each row
                                    while($row = $result->fetch_assoc()) {
                                        if($row["setNumber"]==$setNumber){
                                            $textReplyMessage = "Set $setNumber มีคนใช้แล้ว ลองเปลี่ยนเลขใหม่ดูค่ะ";
                                            $replyData = new TextMessageBuilder($textReplyMessage);
                                            break;
                                        }
  
                                        //iNSERT DATA
                                        $sql = "INSERT INTO storeset (setNumber, userID, qData) VALUES ('$setNumber', '$userID', '$qData')";
                                        if (mysqli_query($conn, $sql)) {
                                            echo "New record created successfully";
                                            $textReplyMessage = "Question Set $setNumber saved!";
                                            break;
                                        } else {
                                            echo "Error: " . $sql . "<br>" . mysqli_error($conn);
                                        }
                                    }
                                    
                                } else {
                                    

                                        //iNSERT DATA
                                        $sql = "INSERT INTO storeset (setNumber, userID, qData) VALUES ('$setNumber', '$userID', '$qData')";
                                        if (mysqli_query($conn, $sql)) {
                                            echo "New record created successfully";
                                            $textReplyMessage = "Question Set $setNumber saved!";
                                        } else {
                                            echo "Error: " . $sql . "<br>" . mysqli_error($conn);
                                        }

                                }
                                //mysqli_close($conn);
                            }
                            $textReplyMessage = "Question set $setNumber saved!";
                            $replyData = new TextMessageBuilder($textReplyMessage);
                            mysqli_close($conn);
                            break;
                        


                        case strpos($userMessage,'DelQ'):
                            
                            
                            $setNumber = substr($userMessage,5);
                            $setNumber = intval($setNumber);
                            $conn = mysqli_connect($servername, $username, $password, $dbname);
                            // Check connection
                            if (!$conn) {
                                die("Connection failed: " . mysqli_connect_error());
                            }
                            
                            $sql = "select * from storeset where setNumber='$setNumber'";
                            if ($result=mysqli_query($conn,$sql)){
                                while ($obj=mysqli_fetch_object($result)){
                                    
                                    if ($userID<>$obj->userID){

                                        $textReplyMessage = "Sorry, Set $setNumber is not yours ;p";
                                        break;
                                        
                                    }else{
                                        $sql = "DELETE FROM storeset WHERE setNumber='$setNumber'";
                                        mysqli_query($conn,$sql);
                                        
                                        mysqli_query($conn,'TRUNCATE TABLE CheckQ');
                                        $textReplyMessage = "Set $setNumber Deleted!";
                                        break;
                                        
                                    }
                                    
                                }
                            }
                            $textReplyMessage = "Set $setNumber Deleted!";
                            $replyData = new TextMessageBuilder($textReplyMessage);
                            mysqli_close($conn);
                            
                            break;
                            
                            
                        case "aaa":
                            

                            
                            break;
                            
                            
                        case "MyFulliD":
                            
                            $textReplyMessage = $userID;
                            $replyData = new TextMessageBuilder($textReplyMessage);
                            
                            break;
                    
                        default:
                            $textReplyMessage = " ";
                            $replyData = new TextMessageBuilder($textReplyMessage);

                            break;
                    }
                    break;
                default:
                    //$textReplyMessage = json_encode($events);
                    $textReplyMessage = " ";
                    $replyData = new TextMessageBuilder($textReplyMessage);
                    break;
            }
        }
    
    
    $response = $bot->replyMessage($replyToken,$replyData);

    if ($response->isSucceeded()) {
        echo 'Succeeded!';
        return;
    }

    
    
    
    // Failed
    echo $response->getHTTPStatus() . ' ' . $response->getRawBody();

    
    
    
    //////////////////// FUNCTIONS
    
    
    
    
    
    
    function questionGen($qNum,$question,$choise1,$choise2,$choise3){
        $actionBuilder = array(
                               new PostbackTemplateActionBuilder(
                                $choise1, // ข้อความแสดงในปุ่ม
                                http_build_query(array(
                                'action'=>'ans',
                                'item'=>1
                                          )) // ข้อมูลที่จะส่งไปใน webhook ผ่าน postback event
                                             //'Postback Text'  // ข้อความที่จะแสดงฝั่งผู้ใช้ เมื่อคลิกเลือก
                                    ),
                               new PostbackTemplateActionBuilder(
                                  $choise2, // ข้อความแสดงในปุ่ม
                                  http_build_query(array(
                                  'action'=>'ans',
                                   'item'=>2
                                    )) // ข้อมูลที่จะส่งไปใน webhook ผ่าน postback event
                                    //'Postback Text'  // ข้อความที่จะแสดงฝั่งผู้ใช้ เมื่อคลิกเลือก
                                       ),
                               new PostbackTemplateActionBuilder(
                                    $choise3, // ข้อความแสดงในปุ่ม
                                    http_build_query(array(
                                    'action'=>'ans',
                                     'item'=>3
                                     )) // ข้อมูลที่จะส่งไปใน webhook ผ่าน postback event
                                        //'Postback Text'  // ข้อความที่จะแสดงฝั่งผู้ใช้ เมื่อคลิกเลือก
                                   ),
                               );
        //settype($qNum, "string")
        $imageUrl = "https://png.icons8.com/color/2x/about/$qNum";
        $replyData = new TemplateMessageBuilder('Button Template',
                                                new ButtonTemplateBuilder(
                                ' ', // กำหนดหัวเรื่อง
                                 $question, // กำหนดรายละเอียด
                                 $imageUrl, // กำหนด url รุปภาพ
                                 $actionBuilder  // กำหนด action object
                              )
                    );
        return $replyData;
        
    }
    
 
    

    
    
    ?>
