<?php
require 'vendor/autoload.php';

use DingNotice\DingTalk;
use Overtrue\Weather\Weather;
use Carbon\Carbon;

$today = Carbon::today()->toDateString();
//获取今天是一周的第几天
$index = Carbon::now()->dayOfWeek;
$indexTxt = '';
switch ($index){
    case 0:
        $index = '星期日';
        break;
    case 1 :
        $indexTxt = '星期一';
        break;
    case 2 :
        $indexTxt = '星期二';
        break;
    case 3 :
        $indexTxt = '星期三';
        break;
    case 4 :
        $indexTxt = '星期四';
        break;
    case 5 :
        $indexTxt = '星期五';
        break;
    case 6 :
        $indexTxt = '星期六';
        break;
}

$ding = new DingTalk([
    "default" => [
        'enabled' => true,
        'token' => "",
        'timeout' => 2.0,
        'ssl_verify' => true,
        'secret' => '',
    ]
]);
$key = '';
$weather = new Weather($key);
$response = $weather->getLiveWeather('郑州');
$weaTxt = '';
if($response['lives'] && count($response['lives']) ){
    $info = $response['lives']['0'];

    $weaTxt = $info['weather'].',当前室外温度:'.$info['temperature'].'℃';
}

$title = 'hello,斯普莱斯提醒';
$markdown = '';
if($weaTxt){
    $markdown = "今天是".$today.','.$indexTxt."  \n".
        "#### 郑州天气  \n ".
        "> ".$weaTxt."\n ";
}

// 从文件中读取数据到PHP变量
$json_string = file_get_contents('./data.json');

// 用参数true把JSON字符串强制转成PHP数组
$data = json_decode($json_string, true);
$random = random_int(0,607);
$poetry = $data[$random];

$poetryTitle = $poetry['title'];
$poetryAuthor = $poetry['author'];
$poetryContent = '';

foreach ($poetry['paragraphs'] as $paragraph){
    $poetryContent .= "> ".$paragraph."  \n";
}


$markdown .= "#### 今日诗词  \n " .
            "> ".$poetryTitle ."  \n" .
            "> ".$poetryAuthor."  \n".
            $poetryContent;
$ding->markdown($title,$markdown);
