
    
    <?php
   
        include 'bot.php';
        
        $accessToken = "tfYqzCth46wetFH8opQlH4BymM5raTaaY7RQk0FFOvGhbtlCltEndm3QZJ/JthqnSSCvDVppj3UA5zmGMIhRv9/dDHa/0EObr0Dzl8ZjmnCyZ+LR21z+S4EBIIYXxsHVz3MfPLCoO6BHqxoKcN8JtwdB04t89/1O/w1cDnyilFU=";//copy ข้อความ Channel access token ตอนที่ตั้งค่า
        $content = file_get_contents('php://input');
        $arrayJson = json_decode($content, true);
        $arrayHeader = array();
        $arrayHeader[] = "Content-Type: application/json";
        $arrayHeader[] = "Authorization: Bearer {$accessToken}";
        //รับข้อความจากผู้ใช้
        $message = $arrayJson['events'][0]['message']['text'];
        $replyToken = $arrayJson['events'][0]['replyToken'];
        //รับ id ของผู้ใช้
        $id = "C3eda853af1be90f0a9220254ce106c27"; // GROUP ID LIVING ROOM
        //$id = "C737317320269979de739f8aa01160019"; // GROUP ID TEST Room
        
        ///// WAIT FOR USER JOIN THE GAME /////
        
        if($message == "LastScore"){
            
            $arrayPostData = LastGameScore();
            pushMsg($arrayHeader,$arrayPostData);
            //transferScore();
        }
        
        if($message == "Score"){
            
            $arrayPostData = GameScore();
            pushMsg($arrayHeader,$arrayPostData);
            //transferScore();
        }
        
        if($message == "MyScore"){
            
            $arrayPostData = MyScore();
            pushMsg($arrayHeader,$arrayPostData);
            //transferScore();
        }
        
        if($message == "MyID"){
            
            $arrayPostData['to'] = $id;
            $arrayPostData['messages'][0]['type'] = "text";
            $arrayPostData['messages'][0]['text'] = substr($userID,-7);
            pushMsg($arrayHeader,$arrayPostData);
            //transferScore();
        }
        
        if($message == "Signin"){
            
            $arrayPostData['to'] = $id;
            $arrayPostData['messages'][0]['type'] = "text";
            $arrayPostData['messages'][0]['text'] = "ใครสนใจจะร่วมเล่นในรอบนี้\nแต่ะที่ปุ่ม JOIN เบาๆได้เลยจ้า";
            pushMsg($arrayHeader,$arrayPostData);
            
            openJoinSql(1); //// OPEN JOIN ////
            $arrayPostData = openJoin();
            pushMsg($arrayHeader,$arrayPostData);
            
        }
        
        if(!strpos($message, 'CheckQ')){
            
            
            $setNumber = substr($message,7);
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
                        
                        $arrayPostData['to'] = $userID;
                        $arrayPostData['messages'][0]['type'] = "text";
                        $arrayPostData['messages'][0]['text'] = "Sorry, Set $setNumber is not yours ;p";
                        pushMsg($arrayHeader,$arrayPostData);
                        mysqli_close($conn);
                        break;
                        
                    }else{
                        
                        CheckQ($setNumber);
                        $sql = "select * from CheckQ";
                        $result=mysqli_query($conn,$sql);
                        $numberOfq = mysqli_num_rows($result);
                        
                        
                        for ($x = 1; $x<=$numberOfq; $x++){
                            
                            $arrayPostData = CheckAnswer($x);
                            pushMsg($arrayHeader,$arrayPostData);
                            
                        }
                        $arrayPostData['to'] = $userID;
                        $arrayPostData['messages'][0]['type'] = "text";
                        $arrayPostData['messages'][0]['text'] = "$numberOfq Questions found in Set $setNumber";
                        pushMsg($arrayHeader,$arrayPostData);
                        mysqli_query($conn,'TRUNCATE TABLE CheckQ');
                        mysqli_close($conn);
                        break;
                        
                    }
                
            }
            }

           //mysqli_close($conn);
            
        }
        /////// MyQ ///////
        
        
        if($message == "MyQ"){
            
            
            //$setNumber = substr($message,4);
            //$setNumber = intval($setNumber);
            $conn = mysqli_connect($servername, $username, $password, $dbname);
            // Check connection
            if (!$conn) {
                die("Connection failed: " . mysqli_connect_error());
            }
            $sql = "select * from storeset where userID='$userID'";
            if ($result=mysqli_query($conn,$sql)){
                
                $numberOfq = mysqli_num_rows($result);
                $arrayPostData['to'] = $userID;
                $arrayPostData['messages'][0]['type'] = "text";
                $arrayPostData['messages'][0]['text'] = "Found $numberOfq Sets!";
                pushMsg($arrayHeader,$arrayPostData);
                
                while ($obj=mysqli_fetch_object($result)){
                    
                    $samData = substr($obj->qData,2,50);
                        $arrayPostData['to'] = $userID;
                        $arrayPostData['messages'][0]['type'] = "text";
                        $arrayPostData['messages'][0]['text'] = "$obj->setNumber";
                        pushMsg($arrayHeader,$arrayPostData);
                        
                    }
                    
                }
            
            mysqli_close($conn);
        }
        
        
        /////// DELETE Q ////////
        
        
        
     /*   if(!strpos($message, 'DelQ')){
            
            
            $setNumber = substr($message,5);
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
                        
                        $arrayPostData['to'] = $userID;
                        $arrayPostData['messages'][0]['type'] = "text";
                        $arrayPostData['messages'][0]['text'] = "Sorry, Set $setNumber is not yours ;p";
                        pushMsg($arrayHeader,$arrayPostData);
                        break;
                        
                    }else{
                        
                       
                        $sql = "DELETE FROM storeset WHERE setNumber='$setNumber'";
                        mysqli_query($conn,$sql);
                        

                        $arrayPostData['to'] = $userID;
                        $arrayPostData['messages'][0]['type'] = "text";
                        $arrayPostData['messages'][0]['text'] = "Set $setNumber Deleted!";
                        pushMsg($arrayHeader,$arrayPostData);
                        mysqli_query($conn,'TRUNCATE TABLE CheckQ');
                        break;
                        
                    }
                    
                }
            }
            mysqli_close($conn);
        } */
        
        
        
        ///// START THE GAME /////
        if($message == "PLAY"){

            $conn = mysqli_connect($servername, $username, $password, $dbname);
            // Check connection
            if (!$conn) {
                die("Connection failed: " . mysqli_connect_error());
            }
            
            $sql = "select * from playingSet";
            $result=mysqli_query($conn,$sql);
            $numberOfq = mysqli_num_rows($result);
            echo "Number of question $numberOfq";
            
            $arrayPostData['to'] = $id;
            $arrayPostData['messages'][0]['type'] = "text";
            $arrayPostData['messages'][0]['text'] = "คำถามมีทั้งหมด $numberOfq ข้อ \nกำลังจะเริ่ม พร้อมน๊าาา \nสัมผัสแรกคือคำตอบ นับถอยหลัง!";
            pushMsg($arrayHeader,$arrayPostData);
            sleep(2);
            
            pushCoutDown(3);
            
            openJoinSql(0); //// CLOSE JOIN ////
            
            mysqli_query($conn,"TRUNCATE TABLE LastGameScore"); // CLEAR LAST GAME SCORE
            
            for ($x = 1; $x<=$numberOfq; $x++){
                
                isPlayingQuestion($x,1); // SET inPlaying Question On
                
                $arrayPostData = PostQuestion($x);
                pushMsg($arrayHeader,$arrayPostData);
                sleep(10);
            
                pushCoutDown(5);
                
                $arrayPostData['to'] = $id;
                $arrayPostData['messages'][0]['type'] = "text";
                $arrayPostData['messages'][0]['text'] = "-- หมดเวลา! -- เฉลย --";
                pushMsg($arrayHeader,$arrayPostData);
                
                isPlayingQuestion($x,0); // SET inPlaying Question Off
                
                sleep(2);
                
                $arrayPostData = PostAnswer($x);
                pushMsg($arrayHeader,$arrayPostData);
                
                sleep(7);
                
                if ($x<$numberOfq){
                    $z = $x+1;
                    $nextQ = "คำถามข้อถัดไป คำถามข้อที่ $z \nพร้อมมม!";
                }else{
                    $nextQ = "-- จบแล้วจ้าาา \(^o^)/";
                    transferScore(); //// WHEN GAME FINISH TRANSFER SCORE
                    $arrayPostData = LastGameScore();
                    pushMsg($arrayHeader,$arrayPostData);
                }
                $arrayPostData['to'] = $id;
                $arrayPostData['messages'][0]['type'] = "text";
                $arrayPostData['messages'][0]['text'] = "$nextQ";
                pushMsg($arrayHeader,$arrayPostData);
                sleep(5);
        
            }
            
        
        }
        
        
        /////////// PUSH QUESTION //////////////
        
        function PostQuestion($qNum){
            
            global $id,$conn;
            $sql = "SELECT * FROM playingSet where qNum='$qNum'";
            if ($result=mysqli_query($conn,$sql)) {
                
                while ($obj=mysqli_fetch_object($result)){
            
                   // echo $obj->Question;
            
            $arrayPostData = array (
                                    'to' => $id,
                                    'messages' => array (
                                                         0 => array(
                                                                    'type'=>'flex',
                                                                    'altText' => "คำถามข้อที่ $obj->qNum",
                                                                    'contents' => array(
                                                                                        
                                                                                        
                                                                                        'type' => 'bubble',
                                                                                        'hero' =>
                                                                                        array (
                                                                                               'type' => 'image',
                                                                                               'url' => "https://png.icons8.com/color/2x/calendar-$obj->qNum.png",
                                                                                               'size' => 'sm',
                                                                                               'aspectMode' => 'cover',
                                                                                               ),
                                                                                        'body' =>
                                                                                        array (
                                                                                               'type' => 'box',
                                                                                               'layout' => 'vertical',
                                                                                               'contents' =>
                                                                                               array (
                                                                                                      0 =>
                                                                                                      array (
                                                                                                             'type' => 'box',
                                                                                                             'layout' => 'baseline',
                                                                                                             'margin' => 'md',
                                                                                                             'contents' =>
                                                                                                             array (
                                                                                                                    0 =>
                                                                                                                    array (
                                                                                                                           'type' => 'text',
                                                                                                                           'wrap' => true,
                                                                                                                           'text' => "$obj->Question",
                                                                                                                           'size' => 'lg',
                                                                                                                           'color' => '#000000',
                                                                                                                           'margin' => 'md',
                                                                                                                           'flex' => 0,
                                                                                                                           ),
                                                                                                                    ),
                                                                                                             ),
                                                                                                      ),
                                                                                               ),
                                                                                        'footer' =>
                                                                                        array (
                                                                                               'type' => 'box',
                                                                                               'layout' => 'vertical',
                                                                                               'spacing' => 'lg',
                                                                                               'contents' =>
                                                                                               array (
                                                                                                      0 =>
                                                                                                      array (
                                                                                                             'type' => 'button',
                                                                                                             'style' => 'secondary',
                                                                                                             'height' => 'sm',
                                                                                                             'action' =>
                                                                                                             array (
                                                                                                                    'type' => 'postback',
                                                                                                                    'label' => "$obj->Choise1",
                                                                                                                    'displayText' => '👌',
                                                                                                                    'data' => '1',
                                                                                                                    ),
                                                                                                             ),
                                                                                                      1 =>
                                                                                                      array (
                                                                                                             'type' => 'button',
                                                                                                             'style' => 'secondary',
                                                                                                             'height' => 'sm',
                                                                                                             'action' =>
                                                                                                             array (
                                                                                                                    'type' => 'postback',
                                                                                                                    'label' => "$obj->Choise2",
                                                                                                                    'displayText' => '👌',
                                                                                                                    'data' => '2',
                                                                                                                    ),
                                                                                                             ),
                                                                                                      2 =>
                                                                                                      array (
                                                                                                             'type' => 'button',
                                                                                                             'style' => 'secondary',
                                                                                                             'height' => 'sm',
                                                                                                             'action' =>
                                                                                                             array (
                                                                                                                    'type' => 'postback',
                                                                                                                    'label' => "$obj->Choise3",
                                                                                                                    'displayText' => '👌',
                                                                                                                    'data' => '3',
                                                                                                                    ),
                                                                                                             ),
                                                                                                      3 =>
                                                                                                      array (
                                                                                                             'type' => 'spacer',
                                                                                                             'size' => 'sm',
                                                                                                             ),
                                                                                                      ),
                                                                                               'flex' => 0,
                                                                                               ),
                                                                                        )
                                                                    
                                                                    
                                                                    
                                                                    )
                                                         )
                                    );
                }
            }
            return $arrayPostData;
        }
        
        ////////////// PUSH ANSWER /////////////
        
        function PostAnswer($qNum){
            
            global $id,$conn;
            $sql = "SELECT * FROM playingSet where qNum='$qNum'";
            if ($result=mysqli_query($conn,$sql)) {
                
                while ($obj=mysqli_fetch_object($result)){
                    
                    switch ($obj->Ans){
                        case 1:
                            $ch1 = "#64CBFF";
                            $ch2 = "#DDDDDD";
                            $ch3 = "#DDDDDD";
                            break;
                        case 2:
                            $ch1 = "#DDDDDD";
                            $ch2 = "#64CBFF";
                            $ch3 = "#DDDDDD";
                            break;
                        case 3:
                            $ch1 = "#DDDDDD";
                            $ch2 = "#DDDDDD";
                            $ch3 = "#64CBFF";
                            break;
                    }
                    
                    // echo $obj->Question;
                    
                    $arrayPostData = array (
                                            'to' => $id,
                                            'messages' => array (
                                                                 0 => array(
                                                                            'type'=>'flex',
                                                                            'altText' => "เฉลย",
                                                                            'contents' => array(
                                                                                                
                                                                                                
                                                                                                'type' => 'bubble',
                                                                                                'hero' =>
                                                                                                array (
                                                                                                       'type' => 'image',
                                                                                                       'url' => "https://png.icons8.com/color/2x/confetti.png",
                                                                                                       'size' => 'sm',
                                                                                                       'aspectMode' => 'cover',
                                                                                                       ),
                                                                                                'body' =>
                                                                                                array (
                                                                                                       'type' => 'box',
                                                                                                       'layout' => 'vertical',
                                                                                                       'contents' =>
                                                                                                       array (
                                                                                                              0 =>
                                                                                                              array (
                                                                                                                     'type' => 'box',
                                                                                                                     'layout' => 'baseline',
                                                                                                                     'margin' => 'md',
                                                                                                                     'contents' =>
                                                                                                                     array (
                                                                                                                            0 =>
                                                                                                                            array (
                                                                                                                                   'type' => 'text',
                                                                                                                                   'wrap' => true,
                                                                                                                                   'text' => "$obj->Question",
                                                                                                                                   'size' => 'lg',
                                                                                                                                   'color' => '#000000',
                                                                                                                                   'margin' => 'md',
                                                                                                                                   'flex' => 0,
                                                                                                                                   ),
                                                                                                                            ),
                                                                                                                     ),
                                                                                                              ),
                                                                                                       ),
                                                                                                'footer' =>
                                                                                                array (
                                                                                                       'type' => 'box',
                                                                                                       'layout' => 'vertical',
                                                                                                       'spacing' => 'lg',
                                                                                                       'contents' =>
                                                                                                       array (
                                                                                                              0 =>
                                                                                                              array (
                                                                                                                     'type' => 'button',
                                                                                                                     'style' => 'secondary',
                                                                                                                     'height' => 'sm',
                                                                                                                     'color'=> "$ch1",
                                                                                                                     'action' =>
                                                                                                                     array (
                                                                                                                            'type' => 'message',
                                                                                                                            'label' => "$obj->Choise1",
                                                                                                                            'text' => ' ',
                                                                                                                            ),
                                                                                                                     ),
                                                                                                              1 =>
                                                                                                              array (
                                                                                                                     'type' => 'button',
                                                                                                                     'style' => 'secondary',
                                                                                                                     'height' => 'sm',
                                                                                                                     'color'=> "$ch2",
                                                                                                                     'action' =>
                                                                                                                     array (
                                                                                                                            'type' => 'message',
                                                                                                                            'label' => "$obj->Choise2",
                                                                                                                            'text' => ' ',
                                                                                                                            ),
                                                                                                                     ),
                                                                                                              2 =>
                                                                                                              array (
                                                                                                                     'type' => 'button',
                                                                                                                     'style' => 'secondary',
                                                                                                                     'height' => 'sm',
                                                                                                                     'color'=> "$ch3",
                                                                                                                     'action' =>
                                                                                                                     array (
                                                                                                                            'type' => 'message',
                                                                                                                            'label' => "$obj->Choise3",
                                                                                                                            'text' => ' ',
                                                                                                                            ),
                                                                                                                     ),
                                                                                                              3 =>
                                                                                                              array (
                                                                                                                     'type' => 'spacer',
                                                                                                                     'size' => 'sm',
                                                                                                                     ),
                                                                                                              ),
                                                                                                       'flex' => 0,
                                                                                                       ),
                                                                                                )
                                                                            
                                                                            
                                                                            
                                                                            )
                                                                 )
                                            );
                }
            }
            return $arrayPostData;
        }
        //////////// CheckQ Function //////////////
        
        
        function CheckAnswer($qNum){ /// Post Answer for Check Q
            
            global $userID,$conn;
            $sql = "SELECT * FROM CheckQ where qNum='$qNum'";
            if ($result=mysqli_query($conn,$sql)) {
                
                while ($obj=mysqli_fetch_object($result)){
                    
                    switch ($obj->Ans){
                        case 1:
                            $ch1 = "#bfff5b";
                            $ch2 = "#DDDDDD";
                            $ch3 = "#DDDDDD";
                            break;
                        case 2:
                            $ch1 = "#DDDDDD";
                            $ch2 = "#bfff5b";
                            $ch3 = "#DDDDDD";
                            break;
                        case 3:
                            $ch1 = "#DDDDDD";
                            $ch2 = "#DDDDDD";
                            $ch3 = "#bfff5b";
                            break;
                    }
                    
                    // echo $obj->Question;
                    
                    $arrayPostData = array (
                                            'to' => $userID,
                                            'messages' => array (
                                                                 0 => array(
                                                                            'type'=>'flex',
                                                                            'altText' => "เฉลย",
                                                                            'contents' => array(
                                                                                                
                                                                                                
                                                                                                'type' => 'bubble',
                                                                                                'hero' =>
                                                                                                array (
                                                                                                       'type' => 'image',
                                                                                                       'url' => "https://png.icons8.com/color/2x/calendar-$obj->qNum.png",
                                                                                                       'size' => 'sm',
                                                                                                       'aspectMode' => 'cover',
                                                                                                       ),
                                                                                                'body' =>
                                                                                                array (
                                                                                                       'type' => 'box',
                                                                                                       'layout' => 'vertical',
                                                                                                       'contents' =>
                                                                                                       array (
                                                                                                              0 =>
                                                                                                              array (
                                                                                                                     'type' => 'box',
                                                                                                                     'layout' => 'baseline',
                                                                                                                     'margin' => 'md',
                                                                                                                     'contents' =>
                                                                                                                     array (
                                                                                                                            0 =>
                                                                                                                            array (
                                                                                                                                   'type' => 'text',
                                                                                                                                   'wrap' => true,
                                                                                                                                   'text' => "$obj->Question",
                                                                                                                                   'size' => 'lg',
                                                                                                                                   'color' => '#000000',
                                                                                                                                   'margin' => 'md',
                                                                                                                                   'flex' => 0,
                                                                                                                                   ),
                                                                                                                            ),
                                                                                                                     ),
                                                                                                              ),
                                                                                                       ),
                                                                                                'footer' =>
                                                                                                array (
                                                                                                       'type' => 'box',
                                                                                                       'layout' => 'vertical',
                                                                                                       'spacing' => 'lg',
                                                                                                       'contents' =>
                                                                                                       array (
                                                                                                              0 =>
                                                                                                              array (
                                                                                                                     'type' => 'button',
                                                                                                                     'style' => 'secondary',
                                                                                                                     'height' => 'sm',
                                                                                                                     'color'=> "$ch1",
                                                                                                                     'action' =>
                                                                                                                     array (
                                                                                                                            'type' => 'message',
                                                                                                                            'label' => "$obj->Choise1",
                                                                                                                            'text' => ' ',
                                                                                                                            ),
                                                                                                                     ),
                                                                                                              1 =>
                                                                                                              array (
                                                                                                                     'type' => 'button',
                                                                                                                     'style' => 'secondary',
                                                                                                                     'height' => 'sm',
                                                                                                                     'color'=> "$ch2",
                                                                                                                     'action' =>
                                                                                                                     array (
                                                                                                                            'type' => 'message',
                                                                                                                            'label' => "$obj->Choise2",
                                                                                                                            'text' => ' ',
                                                                                                                            ),
                                                                                                                     ),
                                                                                                              2 =>
                                                                                                              array (
                                                                                                                     'type' => 'button',
                                                                                                                     'style' => 'secondary',
                                                                                                                     'height' => 'sm',
                                                                                                                     'color'=> "$ch3",
                                                                                                                     'action' =>
                                                                                                                     array (
                                                                                                                            'type' => 'message',
                                                                                                                            'label' => "$obj->Choise3",
                                                                                                                            'text' => ' ',
                                                                                                                            ),
                                                                                                                     ),
                                                                                                              3 =>
                                                                                                              array (
                                                                                                                     'type' => 'spacer',
                                                                                                                     'size' => 'sm',
                                                                                                                     ),
                                                                                                              ),
                                                                                                       'flex' => 0,
                                                                                                       ),
                                                                                                )
                                                                            
                                                                            
                                                                            
                                                                            )
                                                                 )
                                            );
                }
            }
            return $arrayPostData;
        }
        
        
        
        
        //////////// OPEN JOIN FUNCTION //////////////
        
        function openJoin(){
            global $id;
                    $arrayPostData = array (
                                            'to' => $id,
                                            'messages' => array (
                                                                 0 => array(
                                                                            'type'=>'flex',
                                                                            'altText' => "มาเล่นกันเถอะ มาเล่นเกันเถอะ ^^",
                                                                            'contents' => array(
                                                                                                'type' => 'bubble',
                                                                                                'hero' =>
                                                                                                array (
                                                                                                       'type' => 'image',
                                                                                                       'url' => "https://png.icons8.com/color/2x/dancing-party.png",
                                                                                                       'size' => 'sm',
                                                                                                       'aspectMode' => 'fit',
                                                                                                       ),
                                                                                                'body' =>
                                                                                                array (
                                                                                                       'type' => 'box',
                                                                                                       'layout' => 'vertical',
                                                                                                       'contents' =>
                                                                                                       array (
                                                                                                              0 =>
                                                                                                              array (
                                                                                                                     'type' => 'box',
                                                                                                                     'layout' => 'baseline',
                                                                                                                     'margin' => 'md',
                                                                                                                     'contents' =>
                                                                                                                     array (
                                                                                                                            0 =>
                                                                                                                            array (
                                                                                                                                   'type' => 'text',
                                                                                                                                   'wrap' => true,
                                                                                                                                   'text' => "มาเล่นกันเถอะ มาเล่นเกันเถอะ ^^",
                                                                                                                                   'size' => 'lg',
                                                                                                                                   'color' => '#000000',
                                                                                                                                   'margin' => 'md',
                                                                                                                                   'flex' => 0,
                                                                                                                                   ),
                                                                                                                            ),
                                                                                                                     ),
                                                                                                              ),
                                                                                                       ),
                                                                                                'footer' =>
                                                                                                array (
                                                                                                       'type' => 'box',
                                                                                                       'layout' => 'vertical',
                                                                                                       'spacing' => 'lg',
                                                                                                       'contents' =>
                                                                                                       array (
                                                                                                              0 =>
                                                                                                              array (
                                                                                                                     'type' => 'button',
                                                                                                                     'style' => 'primary',
                                                                                                                     'height' => 'sm',
                                                                                                                     'action' =>
                                                                                                                     array (
                                                                                                                            'type' => 'postback',
                                                                                                                            'label' => "JOIN",
                                                                                                                            'data' => 'JOIN',
                                                                                                                            'displayText' => 'Joined!',
                                                                                                                            ),
                                                                                                                     ),
                                                                                                             
                                                                                                              1 =>
                                                                                                              array (
                                                                                                                     'type' => 'spacer',
                                                                                                                     'size' => 'sm',
                                                                                                                     ),
                                                                                                              ),
                                                                                                       'flex' => 0,
                                                                                                       ),
                                                                                                )
                                                                            
                                                                            
                                                                            
                                                                            )
                                                                 )
                                            );
            
            return $arrayPostData;
        } //// END OF FUNCTION
        
        
        
        
      
        function LastGameScore(){
            global $id, $servername, $username, $password, $dbname, $bot;
            

           $arrayPostData = array (
                   'to' => $id,
                   'messages' => array (
                                        0=> array (
                                                   'type'=>'flex',
                                                   'altText' => "Last Game Score",
                                                   'contents' => array (
                                                                        'type' => 'bubble',
                                                                        'body' =>
                                                                        array (
                                                                               'type' => 'box',
                                                                               'layout' => 'vertical',
                                                                               'spacing' => 'md',
                                                                               'contents' =>
                                                                               array (
                                                                                      0 =>
                                                                                      array (
                                                                                             'type' => 'box',
                                                                                             'layout' => 'horizontal',
                                                                                             'spacing' => 'md',
                                                                                             'contents' =>
                                                                                             array (
                                                                                                    0 =>
                                                                                                    array (
                                                                                                           'type' => 'text',
                                                                                                           'text' => 'Last Game Score',
                                                                                                           'size' => 'xl',
                                                                                                           'align' => 'center',
                                                                                                           'wrap' => true,
                                                                                                           ),
                                                                                                    1 =>
                                                                                                    array (
                                                                                                           'type' => 'image',
                                                                                                           'url' => 'https://png.icons8.com/color/2x/leaderboard.png',
                                                                                                           'size' => 'xs',
                                                                                                           ),
                                                                                                    ),
                                                                                             ),
                                                                                      1 =>
                                                                                      array (
                                                                                             'type' => 'separator',
                                                                                             ),
                                                                                      2 =>
                                                                                      array (
                                                                                             'type' => 'box',
                                                                                             'layout' => 'horizontal',
                                                                                             'spacing' => 'md',
                                                                                             'contents' =>
                                                                                             array (
                                                                                                    0 =>
                                                                                                    array (
                                                                                                           'type' => 'text',
                                                                                                           'text' => 'Player',
                                                                                                           'size' => 'sm',
                                                                                                           'weight' => 'bold',
                                                                                                           'flex' => 5,
                                                                                                           ),
                                                                                                    1 => array (
                                                                                                    'type' => 'separator',
                                                                                                    ),
                                                                                                    2 =>
                                                                                                    array (
                                                                                                           'type' => 'text',
                                                                                                           'text' => 'Score',
                                                                                                           'size' => 'sm',
                                                                                                           'weight' => 'bold',
                                                                                                           'flex' => 2,
                                                                                                           ),
                                                                                                    3 => array (
                                                                                                                'type' => 'separator',
                                                                                                                ),
                                                                                                    4 =>
                                                                                                    array (
                                                                                                           'type' => 'text',
                                                                                                           'text' => 'Played',
                                                                                                           'size' => 'sm',
                                                                                                           'weight' => 'bold',
                                                                                                           'flex' => 2,
                                                                                                           ),
                                                                                                    ),
                                                                                             ),
                                                                                      3 =>
                                                                                      array (
                                                                                             'type' => 'separator',
                                                                                             ),
                                                                                      
                                                                                      ), ////////// STAY
                                                                               ),
                                                                        )
                                                   
                                                   
                                                   
                                                   )
                                        )
                                   );
            
            $conn = mysqli_connect($servername, $username, $password, $dbname);
            $sql = "SELECT * FROM LastGameScore ORDER BY playingUserScore DESC";
            $result = mysqli_query($conn,$sql);
            $i=1;
            while ($obj = mysqli_fetch_object($result))
            {
                if ($obj->DisplayName==""){
                    $displayName = substr($obj->userId,-7);
                    $displayName = "User *$displayName";
                } else {
                    $displayName = $obj->DisplayName;
                }
            $score = array ( ///// ADDING THIS ARRAY EACH ROW
                            'type' => 'box',
                            'layout' => 'horizontal',
                            'spacing' => 'md',
                            'contents' =>
                            array (
                                   0 =>
                                   array (
                                          'type' => 'text',
                                          'text' => "$i.$displayName", /// User Name
                                          'size' => 'xs',
                                          'color' => '#828282',
                                          'align' => 'start',
                                          'flex' => 5,
                                          ),
                                   
                                   1 => array (
                                               'type' => 'separator',
                                               ),
                                   
                                   2 =>
                                   array (
                                          'type' => 'text',
                                          'text' => "$obj->playingUserScore",
                                          'size' => 'xs',
                                          'color' => '#828282',
                                          'align' => 'center',
                                          'flex' => 2,
                                          ),
                                   
                                   3 => array (
                                               'type' => 'separator',
                                               ),
                                   4 =>
                                   array (
                                          'type' => 'text',
                                          'text' => "$obj->ansNum",
                                          'size' => 'xs',
                                          'color' => '#828282',
                                          'align' => 'center',
                                          'flex' => 2,
                                          ),
                                   ),
                            );
            
            $arrayPostData['messages'][0]['contents']['body']['contents'][] = $score;
                $i++;
            }
                
            


            return $arrayPostData;
        }//// END OF FUNCTION
        
        
        
        
        function GameScore(){
            global $id, $servername, $username, $password, $dbname, $bot;
            
            
            $arrayPostData = array (
                                    'to' => $id,
                                    'messages' => array (
                                                         0=> array (
                                                                    'type'=>'flex',
                                                                    'altText' => "Summary Score",
                                                                    'contents' => array (
                                                                                         'type' => 'bubble',
                                                                                         'body' =>
                                                                                         array (
                                                                                                'type' => 'box',
                                                                                                'layout' => 'vertical',
                                                                                                'spacing' => 'md',
                                                                                                'contents' =>
                                                                                                array (
                                                                                                       0 =>
                                                                                                       array (
                                                                                                              'type' => 'box',
                                                                                                              'layout' => 'horizontal',
                                                                                                              'spacing' => 'md',
                                                                                                              'contents' =>
                                                                                                              array (
                                                                                                                     0 =>
                                                                                                                     array (
                                                                                                                            'type' => 'text',
                                                                                                                            'text' => 'Summary Score',
                                                                                                                            'size' => 'xl',
                                                                                                                            'align' => 'center',
                                                                                                                            'wrap' => true,
                                                                                                                            ),
                                                                                                                     1 =>
                                                                                                                     array (
                                                                                                                            'type' => 'image',
                                                                                                                            'url' => 'https://png.icons8.com/color/2x/leaderboard.png',
                                                                                                                            'size' => 'xs',
                                                                                                                            ),
                                                                                                                     ),
                                                                                                              ),
                                                                                                       1 =>
                                                                                                       array (
                                                                                                              'type' => 'separator',
                                                                                                              ),
                                                                                                       2 =>
                                                                                                       array (
                                                                                                              'type' => 'box',
                                                                                                              'layout' => 'horizontal',
                                                                                                              'spacing' => 'md',
                                                                                                              'contents' =>
                                                                                                              array (
                                                                                                                     0 =>
                                                                                                                     array (
                                                                                                                            'type' => 'text',
                                                                                                                            'text' => 'Player',
                                                                                                                            'size' => 'sm',
                                                                                                                            'weight' => 'bold',
                                                                                                                            'flex' => 5,
                                                                                                                            ),
                                                                                                                     1 => array (
                                                                                                                                 'type' => 'separator',
                                                                                                                                 ),
                                                                                                                     2 =>
                                                                                                                     array (
                                                                                                                            'type' => 'text',
                                                                                                                            'text' => 'Score',
                                                                                                                            'size' => 'sm',
                                                                                                                            'weight' => 'bold',
                                                                                                                            'flex' => 2,
                                                                                                                            ),
                                                                                                                     3 => array (
                                                                                                                                 'type' => 'separator',
                                                                                                                                 ),
                                                                                                                     4 =>
                                                                                                                     array (
                                                                                                                            'type' => 'text',
                                                                                                                            'text' => 'Played',
                                                                                                                            'size' => 'sm',
                                                                                                                            'weight' => 'bold',
                                                                                                                            'flex' => 2,
                                                                                                                            ),
                                                                                                                     ),
                                                                                                              ),
                                                                                                       3 =>
                                                                                                       array (
                                                                                                              'type' => 'separator',
                                                                                                              ),
                                                                                                       
                                                                                                       ), ////////// STAY
                                                                                                ),
                                                                                         )
                                                                    
                                                                    
                                                                    
                                                                    )
                                                         )
                                    );
            
            $conn = mysqli_connect($servername, $username, $password, $dbname);
            $sql = "SELECT * FROM userScore ORDER BY playScore DESC";
            $result = mysqli_query($conn,$sql);
            $i=1;
            while ($obj = mysqli_fetch_object($result))
            {
                
                if ($obj->DisplayName==""){
                    $displayName = substr($obj->userID,-7);
                    $displayName = "User *$displayName";
                } else {
                    $displayName = $obj->DisplayName;
                }
                
                $score = array ( ///// ADDING THIS ARRAY EACH ROW
                                'type' => 'box',
                                'layout' => 'horizontal',
                                'spacing' => 'md',
                                'contents' =>
                                array (
                                       0 =>
                                       array (
                                              'type' => 'text',
                                              'text' => "$i.$displayName", /// User Name
                                              'size' => 'xs',
                                              'color' => '#828282',
                                              'align' => 'start',
                                              'flex' => 5,
                                              ),
                                       
                                       1 => array (
                                                   'type' => 'separator',
                                                   ),
                                       
                                       2 =>
                                       array (
                                              'type' => 'text',
                                              'text' => "$obj->playScore",
                                              'size' => 'xs',
                                              'color' => '#828282',
                                              'align' => 'center',
                                              'flex' => 2,
                                              ),
                                       
                                       3 => array (
                                                   'type' => 'separator',
                                                   ),
                                       4 =>
                                       array (
                                              'type' => 'text',
                                              'text' => "$obj->playQuestion",
                                              'size' => 'xs',
                                              'color' => '#828282',
                                              'align' => 'center',
                                              'flex' => 2,
                                              ),
                                       ),
                                );
                
                $arrayPostData['messages'][0]['contents']['body']['contents'][] = $score;
                $i++;
            }
            
            
            
            
            return $arrayPostData;
        }//// END OF FUNCTION GAMESCORE
        
        
        
        function MyScore(){ /// MyScore
            global $id, $servername, $username, $password, $dbname, $bot, $userID;
            
            
            $arrayPostData = array (
                                    'to' => $id,
                                    'messages' => array (
                                                         0=> array (
                                                                    'type'=>'flex',
                                                                    'altText' => "Summary Score",
                                                                    'contents' => array (
                                                                                         'type' => 'bubble',
                                                                                         'body' =>
                                                                                         array (
                                                                                                'type' => 'box',
                                                                                                'layout' => 'vertical',
                                                                                                'spacing' => 'md',
                                                                                                'contents' =>
                                                                                                array (
                                                                                                       0 =>
                                                                                                       array (
                                                                                                              'type' => 'box',
                                                                                                              'layout' => 'horizontal',
                                                                                                              'spacing' => 'md',
                                                                                                              'contents' =>
                                                                                                              array (
                                                                                                                     0 =>
                                                                                                                     array (
                                                                                                                            'type' => 'text',
                                                                                                                            'text' => 'individual Score',
                                                                                                                            'size' => 'xl',
                                                                                                                            'align' => 'center',
                                                                                                                            'wrap' => true,
                                                                                                                            ),
                                                                                                                     1 =>
                                                                                                                     array (
                                                                                                                            'type' => 'image',
                                                                                                                            'url' => 'https://png.icons8.com/color/2x/leaderboard.png',
                                                                                                                            'size' => 'xs',
                                                                                                                            ),
                                                                                                                     ),
                                                                                                              ),
                                                                                                       1 =>
                                                                                                       array (
                                                                                                              'type' => 'separator',
                                                                                                              ),
                                                                                                       2 =>
                                                                                                       array (
                                                                                                              'type' => 'box',
                                                                                                              'layout' => 'horizontal',
                                                                                                              'spacing' => 'md',
                                                                                                              'contents' =>
                                                                                                              array (
                                                                                                                     0 =>
                                                                                                                     array (
                                                                                                                            'type' => 'text',
                                                                                                                            'text' => 'Player',
                                                                                                                            'size' => 'sm',
                                                                                                                            'weight' => 'bold',
                                                                                                                            'flex' => 5,
                                                                                                                            ),
                                                                                                                     1 => array (
                                                                                                                                 'type' => 'separator',
                                                                                                                                 ),
                                                                                                                     2 =>
                                                                                                                     array (
                                                                                                                            'type' => 'text',
                                                                                                                            'text' => 'Score',
                                                                                                                            'size' => 'sm',
                                                                                                                            'weight' => 'bold',
                                                                                                                            'flex' => 2,
                                                                                                                            ),
                                                                                                                     3 => array (
                                                                                                                                 'type' => 'separator',
                                                                                                                                 ),
                                                                                                                     4 =>
                                                                                                                     array (
                                                                                                                            'type' => 'text',
                                                                                                                            'text' => 'Played',
                                                                                                                            'size' => 'sm',
                                                                                                                            'weight' => 'bold',
                                                                                                                            'flex' => 2,
                                                                                                                            ),
                                                                                                                     ),
                                                                                                              ),
                                                                                                       3 =>
                                                                                                       array (
                                                                                                              'type' => 'separator',
                                                                                                              ),
                                                                                                       
                                                                                                       ), ////////// STAY
                                                                                                ),
                                                                                         )
                                                                    
                                                                    
                                                                    
                                                                    )
                                                         )
                                    );
            
            $conn = mysqli_connect($servername, $username, $password, $dbname);
            $sql = "SELECT * FROM userScore WHERE userID='$userID' LIMIT 1";
            $result = mysqli_query($conn,$sql);
            $i=1;
            while ($obj = mysqli_fetch_object($result))
            {
                
                if ($obj->DisplayName==""){
                    $displayName = substr($obj->userID,-7);
                    $displayName = "User *$displayName";
                } else {
                    $displayName = $obj->DisplayName;
                }
                
                $score = array ( ///// ADDING THIS ARRAY EACH ROW
                                'type' => 'box',
                                'layout' => 'horizontal',
                                'spacing' => 'md',
                                'contents' =>
                                array (
                                       0 =>
                                       array (
                                              'type' => 'text',
                                              'text' => "$displayName", /// User Name
                                              'size' => 'xs',
                                              'color' => '#828282',
                                              'align' => 'start',
                                              'flex' => 5,
                                              ),
                                       
                                       1 => array (
                                                   'type' => 'separator',
                                                   ),
                                       
                                       2 =>
                                       array (
                                              'type' => 'text',
                                              'text' => "$obj->playScore",
                                              'size' => 'xs',
                                              'color' => '#828282',
                                              'align' => 'center',
                                              'flex' => 2,
                                              ),
                                       
                                       3 => array (
                                                   'type' => 'separator',
                                                   ),
                                       4 =>
                                       array (
                                              'type' => 'text',
                                              'text' => "$obj->playQuestion",
                                              'size' => 'xs',
                                              'color' => '#828282',
                                              'align' => 'center',
                                              'flex' => 2,
                                              ),
                                       ),
                                );
                
                $arrayPostData['messages'][0]['contents']['body']['contents'][] = $score;
                $i++;
            }
            
            
            
            
            return $arrayPostData;
        }//// END OF FUNCTION GAMESCORE
        
        
        function CheckQ($QNum){
            
            global $servername, $username, $password, $dbname, $bot, $userID;
            $conn = mysqli_connect($servername, $username, $password, $dbname);
            $sql = "select * from storeset where setNumber='$QNum'";
            if ($result=mysqli_query($conn,$sql)){
                while ($obj=mysqli_fetch_object($result))
                {
                    if ($userID<>$obj->userID){
                        
                        
                        $arrayPostData['to'] = $userID;
                        $arrayPostData['messages'][0]['type'] = "text";
                        $arrayPostData['messages'][0]['text'] = "Sorry, Set $QNum is not yours (sly smirk) ";
                        pushMsg($arrayHeader,$arrayPostData);
                        //echo "Sorry, Set $QNum is not yours (sly smirk) ";
                        break;
                    }
           
                    $qList = explode("\n",$obj->qData); //parse the rows
                    //echo count($qList); // Number of questions
                   
                    
                    //////////////////// RECORD PLAYING TABLE ////////////////////
                    foreach ($qList as $value){
                        $qLine = explode(",",$value);
                        $sql = "INSERT INTO CheckQ (qNum, Question, Choise1, Choise2, Choise3, Ans) VALUES ('$qLine[0]', '$qLine[1]', '$qLine[2]', '$qLine[3]', '$qLine[4]', '$qLine[5]')";
                        if (mysqli_query($conn, $sql)) {
                            echo "\nCheckQ Table Loaded Successfully";
                            //$textReplyMessage = "Question Set $setNumber is now playing";
                        } else {
                            echo "Error: " . $sql . "<br>" . mysqli_error($conn);
                        }
                    }
                    

                    }

            }else {
                echo "Error: " . $sql . "<br>" . mysqli_error($conn);
            }
            
        }/// END OF FUNCTION CheckQ
        
        
        
        function transferScore(){ // TRANSFER SCORE FROM PLAYING USERS AND UPDATE TO USERSCORE
            global $servername, $username, $password, $dbname;
            $conn = mysqli_connect($servername, $username, $password, $dbname);
            $sql = "SELECT * FROM playingUsers";
            if ($result = mysqli_query($conn,$sql)){
                while ($obj = mysqli_fetch_object($result)){
                    $sql2 = "SELECT * FROM userScore WHERE userID='$obj->userId' LIMIT 1";
                    
                    if($result2 = mysqli_query($conn,$sql2)){
                        $numOfRow = mysqli_num_rows($result2);
                        if($numOfRow==0){ // NEW USER
                            mysqli_query($conn,"INSERT INTO userScore (userID, playScore, bonusScore, playQuestion, DisplayName) VALUES ('$obj->userId', '$obj->playingUserScore', 0,'$obj->ansNum', '$obj->DisplayName')");
                        }else{ // CURRENT USER
                            $obj2 = mysqli_fetch_object($result2);
                            $newPlayingScore = $obj2->playScore + $obj->playingUserScore; // ADD NEW SCORE
                            $newPlayQuestion = $obj2->playQuestion + $obj->ansNum; // ADD Number of Questions played
                            mysqli_query($conn,"UPDATE userScore SET playScore = '$newPlayingScore', playQuestion = '$newPlayQuestion', DisplayName = '$obj->DisplayName' WHERE userID = '$obj->userId'");
                            echo "CURRENT USER FOUND UPDATE SCORE\n";
                        }
                        
                    }else{
                        echo "Error: " . $sql2 . "<br>" . mysqli_error($conn);
                    }
                    
                } // END WHILE LOOP
            } //END IF
            mysqli_query($conn,"INSERT INTO LastGameScore SELECT * FROM playingUsers"); //// TRANSFER DATA TO LAST GAME SCORE
            mysqli_query($conn,"UPDATE storeset SET isPlaying=0"); /// RESET IS PLAYING
            mysqli_query($conn,"TRUNCATE TABLE playingUsers"); //// CLEAR PLAYINGUSERS
            mysqli_query($conn,"TRUNCATE TABLE playingSet"); //// CLEAR PLAYINGSET
            mysqli_close($conn);
        } // END OF FUNCTION
        
        
        function ansCheck($userAns){
            
            global $servername, $username, $password, $dbname, $userID;
            $conn = mysqli_connect($servername, $username, $password, $dbname);
            $sql = "SELECT * FROM playingSet WHERE isPlaying=1 LIMIT 1";
            $obj = mysqli_fetch_object(mysqli_query($conn,$sql));
            $qAns = $obj->Ans; // GET QUESTION ANSWER
            $isPlayingQ = $obj->qNum; // GET PLAYING QUESTION NUMBER
            
            $sql = "SELECT * FROM playingUsers WHERE userId='$userID'";
            $obj = mysqli_fetch_object(mysqli_query($conn,$sql));
            $lastAnsQnum = $obj->ansNum;
            $userScore = $obj->playingUserScore;
            $userScore++; ///// + USER SCCORE
            
            if($userAns == $qAns){
                if ($isPlayingQ <> $lastAnsQnum){
                    $sql ="UPDATE playingUsers SET playingUserScore=$userScore,ansNum=$isPlayingQ WHERE userId='$userID'";
                    if(mysqli_query($conn,$sql)){
                        echo "\n YOU ARE CORRECTED \n";
                    }else{
                        echo "ERROR: Could not able to execute $sql. " . mysqli_error($conn);
                    }
                }
            }else{
                $sql ="UPDATE playingUsers SET ansNum=$isPlayingQ WHERE userId='$userID'";
                if(mysqli_query($conn,$sql)){
                    echo "\n YOUR ANW IS INCORRECTED \n";
                }else{
                    echo "ERROR: Could not able to execute $sql. " . mysqli_error($conn);
                }
                
            }
            mysqli_close($conn);
        }
        
        
        
        function isPlayingQuestion($qNum,$onOff){
            
            global $servername, $username, $password, $dbname;
            $conn = mysqli_connect($servername, $username, $password, $dbname);
            $sql ="UPDATE playingSet SET isPlaying=$onOff WHERE qNum=$qNum";
            if(mysqli_query($conn, $sql)){
                if ($j){echo "\nQ $qNum is Playing\n";}else{echo "\nQ $qNum is Closed\n";}
            }else{
                echo "ERROR: Could not able to execute $sql. " . mysqli_error($conn);
            }
            mysqli_close($conn);
            
        }
        
        
        function openJoinSql($j){
            
            global $servername, $username, $password, $dbname;
            $conn = mysqli_connect($servername, $username, $password, $dbname);
            $sql ="UPDATE storeset SET openJOIN=$j WHERE isPlaying=1";
            if(mysqli_query($conn, $sql)){
                if ($j){echo "\nOpen Join!!\n";}else{echo "\nClose Join!!\n";}
            }else{
                echo "ERROR: Could not able to execute $sql. " . mysqli_error($conn);
            }
            mysqli_close($conn);
            
        }
        
        
        function pushCoutDown($y){
            global $id,$arrayHeader;
            for ($y = 5; $y >0; $y--){
                $arrayPostData['to'] = $id;
                $arrayPostData['messages'][0]['type'] = "text";
                $arrayPostData['messages'][0]['text'] = "-- $y --";
                pushMsg($arrayHeader,$arrayPostData);
                sleep(1);
                
            }
        }
        
        
        
        function pushMsg($arrayHeader,$arrayPostData){
            $strUrl = "https://api.line.me/v2/bot/message/push";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL,$strUrl);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $arrayHeader);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($arrayPostData));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $result = curl_exec($ch);
            echo $result;
            curl_close ($ch);
        }
        exit;
    ?>
    

