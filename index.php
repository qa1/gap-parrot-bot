<?php
define('API_KEY','XXX');
define('DEV',true);
function errorLog($d){
    if(DEV)
        error_log(print_r($d,true));
}
function runCommandFile($api_key, $method,$datas=[]){
    $url = "https://api.gap.im/".$method;
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'token: '.$api_key,
    ));
    curl_setopt($ch,CURLOPT_POSTFIELDS,$datas);
    $res = curl_exec($ch);
    if(curl_error($ch)){
        errorLog(curl_error($ch));
    }else{
        return json_decode($res);
    }
}
function runCommand($api_key, $method, $datas=[]){
    $url = "https://api.gap.im/".$method;
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'token: '.$api_key,
    ));
    curl_setopt($ch,CURLOPT_POSTFIELDS,http_build_query($datas));
    $res = curl_exec($ch);
    if(DEV)
        errorLog($res);
    if(curl_error($ch)){
        errorLog(curl_error($ch));
    }else{
        return json_decode($res);
    }
}
$chat_id = $_POST['chat_id'];
if($_POST['type'] == 'joint'){
    $d = runCommand(API_KEY, 'sendMessage',[
        'chat_id'=>$chat_id,
        'data'=>'به ربات ما خوش آمدید
        
چه کاری براتون انجام بدم ؟',
        'type'=>'text',
        'reply_keyboard'=>json_encode(
            [
                'keyboard'=>[
                    [
                        ['answer'=>'پاسخ'],
                    ],
                    [
                        ['help'=>'راهنما استفاده']
                    ]
                ]
            ]
        )
    ]);
//    errorLog($d);
}
if($_POST['type'] == 'text'){
    $text = $_POST['data'];
    if($text == 'help'){
        runCommand(API_KEY, 'sendMessage',[
            'chat_id'=>$chat_id,
            'data'=>'🛠 خوش اومدید
برای استفاده از این سرویس روی دکمه مربوطه کلیک کنید و سپس سوال مد نظر رو ارسال کنید .',
            'type'=>'text',
            'reply_keyboard'=>json_encode(
                [
                    'keyboard'=>[
                        [
                            ['answer'=>'پاسخ'],
                        ],
                        [
                            ['help'=>'راهنما استفاده']
                        ]
                    ]
                ]
            )
        ]);
        die;
    }
    if($text == 'cancel'){
        runCommand(API_KEY, 'sendMessage',[
            'chat_id'=>$chat_id,
            'data'=>'حله .
            
چه کاری براتون انجام بدم ؟',
            'type'=>'text',
            'reply_keyboard'=>json_encode(
                [
                    'keyboard'=>[
                        [
                            ['answer'=>'پاسخ'],
                        ],
                        [
                            ['help'=>'راهنما استفاده']
                        ]
                    ]
                ]
            )
        ]);
        apcu_store($chat_id.'-location','home');
        die;
    }
    if($text == 'answer'){
        runCommand(API_KEY, 'sendMessage',[
            'chat_id'=>$chat_id,
            'data'=>null,
            'type'=>'text',
            'reply_keyboard'=>json_encode(
                [
                    'keyboard'=>[
                        [
                            ['answer'=>'پاسخ'],
                        ],
                        [
                            ['help'=>'راهنما استفاده']
                        ]
                    ]
                ]
            )
        ]);
        apcu_store($chat_id.'-location','answer');
        die;
    }
    switch (apcu_fetch($chat_id.'-location')){
        case 'answer':{
            $text = trim($text);
            $r = runCommand(API_KEY, 'sendMessage',
                    [
                        'type'=>'text',
                        'data'=>$text,
                        'chat_id'=>$chat_id,
                    ]);

        }break;
        default:{
            runCommand(API_KEY, 'sendMessage',[
                'chat_id'=>$chat_id,
                'data'=>'متوجه نشدم
                
چه کاری براتون انجام بدم ؟',
                'type'=>'text',
                'reply_keyboard'=>json_encode(
                    [
                        'keyboard'=>[
                            [
                                ['answer'=>'پاسخ'],
                            ],
                            [
                                ['help'=>'راهنما استفاده']
                            ]
                        ]
                    ]
                )
            ]);
        }break;
    }
//    errorLog($d);
}