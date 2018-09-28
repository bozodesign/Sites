<?php
    // include composer autoload
    require_once 'vendor/autoload.php';
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
                                                                          $question, // กำหนดหัวเรื่อง
                                                                          ' ', // กำหนดรายละเอียด
                                                                          $imageUrl, // กำหนด url รุปภาพ
                                                                          $actionBuilder  // กำหนด action object
                                                                          )
                                                );
        
    }
    
    
    
    
    ?>
