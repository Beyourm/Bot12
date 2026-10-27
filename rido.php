<?php
require_once __DIR__ . '/init.php';

chdir(__DIR__);
// --- إنشاء المجلدات والملفات الأساسية تلقائياً لمنع الأخطاء ---
@mkdir("sudo", 0755, true);
@mkdir("data", 0755, true);
@mkdir("botmak", 0755, true);
@mkdir("user", 0755, true);
@mkdir("from_id", 0755, true);
@mkdir("wataw", 0755, true);

$required_files = [
    'sudo/ban.txt',
    'sudo/member.txt',
    'sudo/amr.txt',
    'admin.txt',
    'infoidbots.txt',
    'botfreeid.txt',
    'no3send.txt',
    'chatsend.txt',
    'sudo.json',
    'botmak/wataw.json',
    'code.json',
    'datatime.json',
    'prodate.json'
];

foreach ($required_files as $file) {
    $path = __DIR__ . '/' . $file;

    // إنشاء المجلد الأب إذا لم يكن موجودًا
    $directory = dirname($path);

    if (!is_dir($directory)) {
        mkdir($directory, 0755, true);
    }

    // إنشاء الملف إذا لم يكن موجودًا
    if (!file_exists($path)) {
        if (strtolower(pathinfo($path, PATHINFO_EXTENSION)) === 'json') {
            file_put_contents(
                $path,
                json_encode(
                    ['info' => []],
                    JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
                ),
                LOCK_EX
            );
        } else {
            touch($path);
        }
    }
}

ob_start(); 
define("API_KEY", $token);

// استخدام $token و $user_bot_sudo مباشرة دون إعادة تعريف


function bot($method,$datas=[]){
$url = "https://api.telegram.org/bot".API_KEY."/".$method;
$ch = curl_init();
curl_setopt($ch,CURLOPT_URL,$url);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
curl_setopt($ch,CURLOPT_POSTFIELDS,$datas);
$res = curl_exec($ch);
if(curl_error($ch)){
var_dump(curl_error($ch));
}else{
return json_decode($res);}}



function remove_dir($path){
if(is_dir($path) === false){
return false;}	
$dir = opendir($path);
while (($file = readdir($dir) )!== false)	{
if($file == '.' OR $file == '..' OR $file == 'sudo' OR $file == 'pro.json'){
continue;}
if(is_file($path.'/'.$file)){
unlink($path.'/'.$file);
}elseif(is_dir($path.'/'.$file)){
remove_dir($path.'/'.$file);}}
rmdir($path);closedir($dir);}


$raw_update = file_get_contents("php://input");
$update = json_decode($raw_update);

if (!is_object($update)) {
    http_response_code(200);
    exit;
}

$callback = $update->callback_query ?? null;
$inline = $update->inline_query ?? null;

$message = $update->message ?? ($callback->message ?? null);

$txt = $message->caption ?? null;
$text = $message->text ?? null;

$chat_id = $message->chat->id
    ?? ($callback->message->chat->id ?? null);

$from_id = $message->from->id
    ?? ($callback->from->id ?? ($inline->from->id ?? null));

$data = $callback->data ?? null;

$new = $message->new_chat_members ?? null;

$message_id = $message->message_id
    ?? ($callback->message->message_id ?? null);

$type = $message->chat->type ?? null;

$name = $message->from->first_name
    ?? ($callback->from->first_name ?? ($inline->from->first_name ?? null));

$user = $callback->from->username
    ?? ($inline->from->username ?? null);

$id = $inline->from->id ?? null;


if ($from_id !== null && ctype_digit((string) $from_id)) {
    $user_dir = __DIR__ . '/from_id/' . $from_id;

    if (!is_dir($user_dir)) {
        mkdir($user_dir, 0755, true);
    }

    $user_files = [
        'countuserbot.txt',
        'countbot.txt',
        'amr.txt'
    ];

    foreach ($user_files as $filename) {
        $filepath = $user_dir . '/' . $filename;

        if (!file_exists($filepath)) {
            file_put_contents($filepath, '', LOCK_EX);
        }
    }
}


mkdir("sudo");


if (
    isset($message->document) &&
    isset($message->caption) &&
    is_string($message->caption) &&
    (
        strpos($message->caption, 'FBK1|') === 0 ||
        strpos($message->caption, 'FBK1-MANIFEST|') === 0
    )
) {
    restore_receive_backup_document(
        $chat_id,
        $from_id,
        $message->document,
        $message->caption,
        $sudo
    );

    return;
}




$get_ban=file_get_contents('sudo/ban.txt');
$ban =explode("\n",$get_ban);

$member = explode("\n",file_get_contents("sudo/member.txt"));
$cunte = count($member)-1;


$reply = $message->reply_to_message->message_id ?? null;
$rep = $message->reply_to_message->forward_from ?? null;


$watawjson = json_decode(
    file_get_contents(__DIR__ . '/botmak/wataw.json'),
    true
);

if (!is_array($watawjson)) {
    $watawjson = [];
}

$wataw_defaults = [
    'st_ch_bots' => '❌',
    'user_bot' => $user_bot_sudo,
    'id_channel' => '',
    'link_channel' => '',
    'id_channel2' => '',
    'link_channel2' => ''
];

$wataw_info = is_array($watawjson['info'] ?? null)
    ? $watawjson['info']
    : [];

$watawjson['info'] = array_merge(
    $wataw_defaults,
    $wataw_info
);

file_put_contents(
    __DIR__ . '/botmak/wataw.json',
    json_encode($watawjson, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
    LOCK_EX
);

$st_ch_bots = $watawjson['info']['st_ch_bots'];

$infosudo = json_decode(
    file_get_contents(__DIR__ . '/sudo.json'),
    true
);

if (!is_array($infosudo)) {
    $infosudo = [];
}

$info_defaults = [
    'update' => '✅',
    'propots' => 'مجانية',
    'fwrmember' => '❌',
    'tnbih' => '✅',
    'silk' => '✅',
    'allch' => 'مفردة',
    'start' => '',
    'klish_sil' => 'كليشة الاشتراك الاجباري',
    'updatechannel' => '',
    'admins' => [],
    'info_kl' => '',
    'token_kl' => '',
    'sudo' => '',
    'channel_id' => 'null',
    'amr' => 'null',
    'channel' => []
];

$existing_info = is_array($infosudo['info'] ?? null)
    ? $infosudo['info']
    : [];

$infosudo['info'] = array_merge(
    $info_defaults,
    $existing_info
);

if (!is_array($infosudo['info']['admins'])) {
    $infosudo['info']['admins'] = [];
}

if (!is_array($infosudo['info']['channel'])) {
    $infosudo['info']['channel'] = [];
}

file_put_contents(
    __DIR__ . '/sudo.json',
    json_encode($infosudo, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
    LOCK_EX
);

$updatenew = $infosudo['info']['update'];
$propots = $infosudo['info']['propots'];
$fwrmember = $infosudo['info']['fwrmember'];
$tnbih = $infosudo['info']['tnbih'];
$silk = $infosudo['info']['silk'];
$allch = $infosudo['info']['allch'];
$start = $infosudo['info']['start'];
$klish_sil = $infosudo['info']['klish_sil'];
$updatechannel = $infosudo['info']['updatechannel'];
$admins = $infosudo['info']['admins'];
$info_kl = $infosudo['info']['info_kl'];
$token_kl = $infosudo['info']['token_kl'];

if( $text=="تحديث" ){
bot('sendmessage',['chat_id'=>$chat_id,
'text'=>"تم تحديث البوت بنجاح",
'reply_to_message_id'=>$message->message_id,
]);return false;}

if($message and $updatenew=="❌" and $from_id!= $ameed){
bot('sendmessage',['chat_id'=>$chat_id,
'text'=>"البوت قيد التحديث الرجاء الانتظار حتى يتم الانتهاء من التحديث سيتم اشعاركم بذالك عند الانتهاء",
'reply_to_message_id'=>$message->message_id,
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>'اضغط هنا وتابعنا 🔬.','url'=>"t.me/botFilec"]],
]])]);return false;}

function getChatstats($chat_id,$token) {
$url = 'https://api.telegram.org/bot'.$token.'/getChatAdministrators?chat_id='.$chat_id;
$result = file_get_contents($url);
$result = json_decode ($result);
$result = $result->ok;
return $result;}

function getmember($token,$idchannel,$from_id) {
$join = file_get_contents("https://api.telegram.org/bot".$token."/getChatMember?chat_id=$idchannel&user_id=".$from_id);
if((strpos($join,'"status":"left"') or strpos($join,'"Bad Request: USER_ID_INVALID"') or strpos($join,'"Bad Request: user not found"') or strpos($join,'"ok": false') or strpos($join,'"status":"kicked"')) !== false){
$wataw="no";}else{$wataw="yes";}
return $wataw;}

@mkdir("sudo");
@mkdir("data");
$member = explode("\n",file_get_contents("sudo/member.txt"));
$cunte = count($member)-1;
$admin=file_get_contents("admin.txt");

$watawjson = json_decode(file_get_contents("botmak/wataw.json"),true);
$id_ch_sudo = $watawjson['info']['id_channel'] ?? '';
$link_ch_sudo = $watawjson['info']['link_channel'] ?? '';
$user_bot_sudo=$watawjson["info"]["user_bot"];

if($message){
$false="";
if($allch!="مفردة"){
$infosudo = json_decode(file_get_contents("sudo.json"),true);
$orothe = isset($infosudo["info"]["channel"]) ? $infosudo["info"]["channel"] : [];
$keyboard["inline_keyboard"]=[];

if(is_array($orothe) && count($orothe) > 0){
    foreach($orothe as $co=>$s ){
        $namechannel= isset($s["name"]) ? $s["name"] : null;
        $st= isset($s["st"]) ? $s["st"] : null;
        $userchannel=str_replace('@','', isset($s["user"]) ? $s["user"] : '');
        if($namechannel!=null){
            $stuts = getmember($token,$co,$from_id);
            if($stuts=="no"){
                if($st=="عامة"){
                    $url="t.me/$userchannel";
                    $tt=isset($s["user"]) ? $s["user"] : '';
                }else{
                    $url =isset($s["user"]) ? $s["user"] : '';
                    $tt=isset($s["user"]) ? $s["user"] : '';
                }

                if($silk=="✅"){
                    $keyboard["inline_keyboard"][] = [['text'=>$namechannel,'url'=>$url]];
                }else{
                    $txt=$txt."\n".$tt;
                }
                $false = "yes";
            }
        }
    }
}


$reply_markup=json_encode($keyboard);
if($false=="yes"){
bot('sendmessage',['chat_id'=>$chat_id, 
'text'=>"$klish_sil | $txt",
'disable_web_page_preview'=>true,
"message_id"=>$message_id,
'reply_markup'=>$reply_markup,
]);return $false;}
}else{
$infosudo = json_decode(file_get_contents("sudo.json"),true);
$orothe= $infosudo["info"]["channel"];

foreach($orothe as $co=>$s ){
$keyboard["inline_keyboard"]=[];
$namechannel= $s["name"];
$st= $s["st"];
$userchannel=str_replace('@','', $s["user"]);
if($namechannel!=null){
$stuts = getmember($token,$co,$from_id);
if($stuts=="no"){
if($st=="عامة"){
$url="t.me/$userchannel";
$tt=$s["user"];
}else{
$url =$s["user"];
$tt=$s["user"];}

if($silk=="✅"){
$keyboard["inline_keyboard"][] = [['text'=>$namechannel,'url'=>$url]];}
bot('sendmessage',['chat_id'=>$chat_id, 
'text'=>"$klish_sil | $tt",
'disable_web_page_preview'=>true,
"message_id"=>$message_id,
'reply_markup'=>json_encode($keyboard),
]);return $false;}}}}}

if($update and !in_array($from_id,$member)){file_put_contents("sudo/member.txt","$from_id\n",FILE_APPEND);
if($tnbih == "✅" ){
bot("sendmessage",["chat_id"=>$ameed,
"text"=>"
- دخل شخص إلى البوت
[Hi - $name](tg://user?id=$from_id) 
- عدد اعضاء بوتك هو: *$cunte*
",
'disable_web_page_preview'=>'true',
'parse_mode'=>"markdown",]);}}

$ban = explode("\n",file_get_contents("sudo/ban.txt"));
$countban = count($ban)-1;

$botfreeid=explode("\n",file_get_contents("infoidbots.txt"));
$countbots = count($botfreeid)-1;
if($countbots<=0){
$countbots = "لايوجد بوتات مصنوعة";}

if($message  and in_array($from_id,$ban)){
bot('sendmessage',['chat_id'=>$chat_id,
'text'=>"❌ لا تستطيع استخدام البوت انت محظور",
]);return false;}

if($countban<=0){
$countban = "لايوجد محظورين";}

#ــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــ
/*

if($text == "/m" and in_array($from_id,$sudo)){
bot('sendmessage',['chat_id'=>$chat_id, 
'text'=>"
- اهلا بك عزيزي في لوحة اوامر البوت يمكنك التحكم بها مثل ماتشاء..
اليك الاحصائيات:
• الاعضاء: $cunte
• المصنوعات: $countbots
",
'disable_web_page_preview'=>true,
'message_id'=>$message_id,
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"حالة البوت: $updatenew",'callback_data'=>"updatenew"],['text'=>"اشتراك المصنوعات: $st_ch_bots",'callback_data'=>"st_ch_bots"]],
[['text'=>"اشتراك للبوتات 1.",'callback_data'=>"channelbots"],['text'=>"اشتراك للبوتات 2.",'callback_data'=>"channelbots2"]],
[['text'=>'صنع بوت جديد','callback_data'=>"sn3botfre"],['text'=>'قائمة بوتاتك','callback_data'=>"botsmember"]],
[['text'=>'اضافة ملف الى الصانع','callback_data'=>"uplode"]],
[['text'=>"تعيين قناة تحديثات البوت",'callback_data'=>"updatechannel"]],
[['text'=>"إلغاء اشتراك مدفوع",'callback_data'=>"delprobot"],['text'=>"اضافة اشتراك مدفوع",'callback_data'=>"addprobot"]],
[['text'=>"تعيين حساب المطور",'callback_data'=>"sudo"],['text'=>"تعيين (Start)",'callback_data'=>"start"]],
[['text'=>"تعيين معلومات البوت",'callback_data'=>"info_kl"],['text'=>"تعيين كليشة التوكن",'callback_data'=>"token_kl"]],
[['text'=>"التوجية: $fwrmember",'callback_data'=>"fwrmember"],['text'=>"التنبية: $tnbih",'callback_data'=>"tnbih"]],
[['text'=>"قسم الاذاعة",'callback_data'=>"sendmessage", 'style' => 'primary'],['text'=>"النسخ والاستعادة",'callback_data'=>"BackRest", 'style' => 'success']],
[['text'=>"حظر عضو",'callback_data'=>"ban"],['text'=>"إلغاء حظر عضو",'callback_data'=>"unban"]],
[['text'=>"مسح قناة",'callback_data'=>"delchannel"],['text'=>"إضافة قناة",'callback_data'=>"addchannel"]],
[['text'=>"قنوات الاشتراك",'callback_data'=>"viwechannel"],['text'=>"تعيين رسالة الاشتراك",'callback_data'=>"klish_sil"]],
[['text'=>"عرض انلاين: $silk",'callback_data'=>"silk"],['text'=>"عرض الرسالة: $allch",'callback_data'=>"allch"]],
]])]);}

*/

#ــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــ



function sendwataw($chat_id,$message_id){
$watawjson = json_decode(file_get_contents("botmak/wataw.json"),true);
$st_ch_bots=$watawjson["info"]["st_ch_bots"];
$infosudo = json_decode(file_get_contents("sudo.json"),true);
$updatenew=$infosudo["info"]["update"];
$propots=$infosudo["info"]["propots"];
$fwrmember=$infosudo["info"]["fwrmember"];
$tnbih=$infosudo["info"]["tnbih"];
$silk=$infosudo["info"]["silk"];
$allch=$infosudo["info"]["allch"];
$member = explode("\n",file_get_contents("sudo/member.txt"));
$cunte = count($member)-1;
$ban = explode("\n",file_get_contents("sudo/ban.txt"));
$countban = count($ban)-1;

if (!function_exists('btn')) {
    function btn($text, $callback, $style = 'primary') {
        return [
            'text' => $text,
            'callback_data' => $callback,
            'style' => $style
        ];
    }
}


if($countban<=0){
$countban="لايوجد محظورين";}
$botfreeid=explode("\n",file_get_contents("infoidbots.txt"));

$countbots = count($botfreeid)-1;
if($countbots<=0){
    $countbots="لايوجد بوتات مصنوعة";
}

bot('editmessagetext',[
'chat_id'=>$chat_id,
'text'=>"
- اهلا بك عزيزي في لوحة اوامر البوت يمكنك التحكم بها مثل ماتشاء..
اليك الاحصائيات:
• الاعضاء: $cunte
• المصنوعات: $countbots
",
'disable_web_page_preview'=>true,
'message_id'=>$message_id,
'reply_markup'=>json_encode(['inline_keyboard'=>[
[
btn("حالة البوت: $updatenew","updatenew"),
btn("اشتراك المصنوعات: $st_ch_bots","st_ch_bots")
],
[
btn("اشتراك للبوتات 1.","channelbots"),
btn("اشتراك للبوتات 2.","channelbots2")
],
[
btn("صنع بوت جديد","sn3botfre","success"),
btn("قائمة بوتاتك","botsmember")
],
[
btn("اضافة ملف الى الصانع","uplode","success")
],
[
btn("تعيين قناة تحديثات البوت","updatechannel")
],
[
btn("إلغاء اشتراك مدفوع","delprobot","danger"),
btn("اضافة اشتراك مدفوع","addprobot","success")
],
[
btn("تعيين حساب المطور","sudo"),
btn("تعيين (Start)","start")
],
[
btn("تعيين معلومات البوت","info_kl"),
btn("تعيين كليشة التوكن","token_kl")
],
[
btn("التوجية: $fwrmember","fwrmember"),
btn("التنبية: $tnbih","tnbih")
],
[
btn("قسم الاذاعة","sendmessage"),
btn("النسخ والاستعادة","BackRest","success")
],
[
btn("حظر عضو","ban","danger"),
btn("إلغاء حظر عضو","unban","success")
],
[
btn("مسح قناة","delchannel","danger"),
btn("إضافة قناة","addchannel","success")
],
[
btn("قنوات الاشتراك","viwechannel"),
btn("تعيين رسالة الاشتراك","klish_sil")
],
[
btn("عرض انلاين: $silk","silk"),
btn("عرض الرسالة: $allch","allch")
]
]])]);
}





if($data == "addprobot"){
$infosudo["info"]["amr"]="addprobot";
file_put_contents("sudo.json", json_encode($infosudo));
bot('editmessagetext',['chat_id'=>$chat_id, 
'text'=>"✴ اضافة اشتراك مدفوع : 
قم بارسال معرف البوت المصنوع الذي تود اضافة الاشتراك المدفوع له",
'parse_mode'=>markdown,
'disable_web_page_preview'=>true,
"message_id"=>$message_id,
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"إلغاء",'callback_data'=>"home"]],
]])]);}

if($text  and $text !="/start" and $infosudo["info"]["amr"]=="addprobot" and in_array($from_id,$sudo)){
$us_bo=str_replace('@','',$text);
$idbots=file_get_contents("user/$us_bo.txt");

if($idbots!=null){
$infobot=explode("\n",file_get_contents("botmak/$idbots/info.txt"));
$tokenbot=$infobot['0'];
$userbot=$infobot['1'];
$namebot=$infobot['2'];
$id=$infobot['3'];
$idbots=$infobot['4'];
$no3mak=$infobot['6'];
bot('sendmessage',['chat_id'=>$chat_id,
'message_id'=>$message_id,
"text"=>"معلومات البوت
النوع: $no3mak
توكن : `$tokenbot`
يوزر البوت : *@$userbot*
ايدي البوت : `$idbots`
معلومات صاحب البوت
الايدي : `$id`
[صاحب البوت ](tg://user?id=$id)
",
'parse_mode'=>"MarkDown",
'disable_web_page_preview'=>true,
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"",'callback_data'=>"probotyes yars_".$idbots],['text'=>"",'callback_data'=>"probotyes 6mo_".$idbots],['text'=>"",'callback_data'=>"probotyes 3mo_".$idbots],['text'=>"شهر واحد",'callback_data'=>"probotyes 1mo_".$idbots]],
[['text'=>"إلغاء",'callback_data'=>"home"]],
]])]);
}else{
bot('sendmessage',['chat_id'=>$chat_id, 
'text'=>"❌ لايوجد بوت مصنوع بنفس هذا المعرف $text",
'disable_web_page_preview'=>true,
"message_id"=>$message_id,
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"معاودة المحاولة",'callback_data'=>"addprobot"]],
]])]);}

$infosudo["info"]["amr"]="null";
file_put_contents("sudo.json", json_encode($infosudo));}

if(preg_match('/^(probotyes) (.*)/s', $data)){
date_default_timezone_set('Asia/Baghdad');
$nn = str_replace('probotyes ',"",$data);
$ex=explode('_',$nn);
$ash=$ex[0];
$idbots=$ex[1];
if($idbots!=null){

$mo = 86400;
$time = time()+(3600 * 1);
$ti=$time+($mo * 30);

if($ash=="yars"){$ti=$time+($mo * 12);}

if($ash=="1mo_"){$ti=$time+($mo * 1);}

$projsonmem["info"]["pro"]="yes";
$projsonmem["info"]["dateon"]="$time";
$projsonmem["info"]["dateoff"]="$ti";
file_put_contents("botmak/$idbots/pro.json", json_encode($projsonmem));

$projson = json_decode(file_get_contents("prodate.json"),true);
$projson["info"][$idbots]["pro"]="yes";
$projson["info"][$idbots]["dateon"]="$time";
$projson["info"][$idbots]["dateoff"]="$ti";
file_put_contents("prodate.json", json_encode($projson));

$infobot=explode("\n",file_get_contents("botmak/$idbots/info.txt"));
$tokenbot=$infobot['0'];
$userbot=$infobot['1'];
$namebot=$infobot['2'];
$id=$infobot['3'];
$idbots=$infobot['4'];
$no3mak=$infobot['6'];

$dayon = date('Y/m/d',$time);
$timeon =date('H:i:s A',$time);
$dayoff = date('Y/m/d',$ti);
$timeoff =date('H:i:s A',$ti);

bot('editmessagetext',['chat_id'=>$chat_id, 
'text'=>"✅ تم اضافة الاشتراك المدفوع بنجاح 

النوع: $no3mak
يوزر البوت : @$userbot
ايدي البوت : $idbots

معلومات الاشتراك
- وقت الاشتراك :
⏰ $timeon
📅 $dayon
- موعد انتهاء الاشتراك :
⏰ $timeoff
📅 $dayoff
",
'disable_web_page_preview'=>true,
"message_id"=>$message_id,
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"• رجوع •",'callback_data'=>"home"]],
]])]);
bot('sendmessage',['chat_id'=>$id, 
'text'=>"✅ تم اضافة الاشتراك لبوتك المصنوع بنجاح

النوع: $no3mak
يوزر البوت : @$userbot
ايدي البوت : $idbots

معلومات الاشتراك
- وقت الاشتراك :
⏰ $timeon
📅 $dayon
- موعد انتهاء الاشتراك :
⏰ $timeoff
📅 $dayoff
",
'disable_web_page_preview'=>true,
]);}}

if($data == "delprobot"){
$infosudo["info"]["amr"]="delprobot";
file_put_contents("sudo.json", json_encode($infosudo));
bot('editmessagetext',['chat_id'=>$chat_id, 
'text'=>"حذف اشتراك مدفوع :
قم بارسال معرف البوت المصنوع الذي تود حذف الاشتراك المدفوع له",
'parse_mode'=>markdown,
'disable_web_page_preview'=>true,
"message_id"=>$message_id,
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"إلغاء",'callback_data'=>"home"]],
]])]);}

if($text  and $text !="/start" and $infosudo["info"]["amr"]=="delprobot" and in_array($from_id,$sudo)){
$us_bo=str_replace('@','',$text);
$idbots=file_get_contents("user/$us_bo.txt");

if($idbots!=null){
$projson = json_decode(file_get_contents("prodate.json"),true);
if($projson["info"][$idbots]["pro"]=="yes"){
date_default_timezone_set('Asia/Baghdad');
$infobot=explode("\n",file_get_contents("botmak/$idbots/info.txt"));
$tokenbot=$infobot['0'];
$userbot=$infobot['1'];
$namebot=$infobot['2'];
$id=$infobot['3'];
$idbots=$infobot['4'];
$no3mak=$infobot['6'];
$time=$projson["info"][$idbots]["dateon"];
$ti=$projson["info"][$idbots]["dateoff"];

$dayon = date('Y/m/d',$time);
$timeon =date('H:i:s A',$time);
$dayoff = date('Y/m/d',$ti);
$timeoff =date('H:i:s A',$ti);

bot('sendmessage',['chat_id'=>$chat_id,
'message_id'=>$message_id,
"text"=>"
النوع: $no3mak
يوزر البوت : @$userbot
ايدي البوت : $idbots
",
'disable_web_page_preview'=>true,
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"حذف",'callback_data'=>"delprobotyes ".$idbots],['text'=>"• رجوع •",'callback_data'=>"home"]],
]])]);
$projson = json_decode(file_get_contents("prodate.json"),true);
$projson["info"][$idbots]["pro"]="no";
file_put_contents("prodate.json", json_encode($projson));
}else{
bot('sendmessage',['chat_id'=>$chat_id, 
'text'=>"❌ هذا البوت لا يمتلك اشتراك مدفوع  $text",
'disable_web_page_preview'=>true,
"message_id"=>$message_id,
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"معاودة المحاولة",'callback_data'=>"delprobot"]],
]])]);}
}else{
bot('sendmessage',['chat_id'=>$chat_id, 
'text'=>"❌ لايوجد بوت مصنوع بنفس هذا المعرف $text",
'disable_web_page_preview'=>true,
"message_id"=>$message_id,
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"معاودة المحاولة",'callback_data'=>"delprobot"]],
]])]);}

$infosudo["info"]["amr"]="null";
file_put_contents("sudo.json", json_encode($infosudo));}

if(preg_match('/^(delprobotyes) (.*)/s', $data)){
$idbots = str_replace('delprobotyes ',"",$data);
if($idbots!=null){

$projsonmem["info"]["pro"]="no";
file_put_contents("botmak/$idbots/pro.json", json_encode($projsonmem));

$projson = json_decode(file_get_contents("prodate.json"),true);
$projson["info"][$idbots]["pro"]="no";
file_put_contents("prodate.json", json_encode($projson));

$infobot=explode("\n",file_get_contents("botmak/$idbots/info.txt"));
$tokenbot=$infobot['0'];
$userbot=$infobot['1'];
$namebot=$infobot['2'];
$id=$infobot['3'];
$idbots=$infobot['4'];
$no3mak=$infobot['6'];
bot('editmessagetext',['chat_id'=>$chat_id, 
'text'=>"🚫 تم حذف الاشتراك المدفوع بنجاح
النوع: $no3mak
يوزر البوت : @$userbot
ايدي البوت : $idbots
",
'disable_web_page_preview'=>true,
"message_id"=>$message_id,
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"• رجوع •",'callback_data'=>"home"]],
]])]);
bot('sendmessage',['chat_id'=>$id, 
'text'=>"❌ تم حذف الاشتراك المدفوع لبوتك المصنوع
النوع: $no3mak
يوزر البوت : @$userbot
ايدي البوت : $idbots
",
'disable_web_page_preview'=>true,]);}}

if($data == "ban"){
$infosudo["info"]["amr"]="ban";
file_put_contents("sudo.json", json_encode($infosudo));
bot('editmessagetext',['chat_id'=>$chat_id, 
'text'=>"- قم بارسال أيدي العضو لحظره",
'parse_mode'=>markdown,
'disable_web_page_preview'=>true,
"message_id"=>$message_id,
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"إلغاء",'callback_data'=>"home"]],
]])]);}

if($text  and $text !="/start" and $infosudo["info"]["amr"]=="ban" and in_array($from_id,$sudo) and is_numeric($text)){
if(!in_array($text,$ban)){
file_put_contents("sudo/ban.txt","$text\n",FILE_APPEND);
bot('sendmessage',['chat_id'=>$chat_id, 
'text'=>"✅ تم حظر العضو بنجاح $text",
'disable_web_page_preview'=>true,
"message_id"=>$message_id,
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"• رجوع •",'callback_data'=>"home"]],
]])]);
bot('sendmessage',['chat_id'=>$text,
'text'=>"❌ لقد قام الادمن بحظرك من استخدام البوت",]);
}else{
bot('sendmessage',['chat_id'=>$chat_id, 
'text'=>"🚫 العضو محظور مسبقاً",
'disable_web_page_preview'=>true,
"message_id"=>$message_id,
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"• رجوع •",'callback_data'=>"home"]],
]])]);}

$infosudo["info"]["amr"]="null";
file_put_contents("sudo.json", json_encode($infosudo));}

if($data == "unban"){
$infosudo["info"]["amr"]="unban";
file_put_contents("sudo.json", json_encode($infosudo));
bot('editmessagetext',['chat_id'=>$chat_id, 
'text'=>"- قم بارسال أيدي العضو للإلغاء الحظر عنه",
'parse_mode'=>markdown,
'disable_web_page_preview'=>true,
"message_id"=>$message_id,
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"إلغاء",'callback_data'=>"home"]],
]])]);}

if($text  and $text !="/start" and $infosudo["info"]["amr"]=="ban" and in_array($from_id,$sudo) and is_numeric($text)){
if(in_array($text,$ban)){
$str=file_get_contents("sudo/ban.txt");
$str=str_replace("$text\n",'',$str);
file_put_contents("sudo/ban.txt",$str);
bot('sendmessage',['chat_id'=>$chat_id, 
'text'=>"✅ تم إلغاء حظر العضو بنجاح $text",
'disable_web_page_preview'=>true,
"message_id"=>$message_id,
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"• رجوع •",'callback_data'=>"home"]],
]])]);
bot('sendmessage',['chat_id'=>$text,
'text'=>"✅ لقد قام الادمن بإلغاء الحظر عنك.",]);
}else{
bot('sendmessage',['chat_id'=>$chat_id, 
'text'=>"🚫 العضو ليسِ محظور مسبقاً",
'disable_web_page_preview'=>true,
"message_id"=>$message_id,
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"• رجوع •",'callback_data'=>"home"]],
]])]);}

$infosudo["info"]["amr"]="null";
file_put_contents("sudo.json", json_encode($infosudo));}

if($data == "unbanall"){
if($countban>0){
bot('editmessagetext',['chat_id'=>$chat_id, 
'text'=>"- ✅ تم مسح قائمة المحظورين بنجاح ",
'parse_mode'=>markdown,
'disable_web_page_preview'=>true,
"message_id"=>$message_id,
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"إلغاء",'callback_data'=>"home"]],
]])]);}else{
bot('answercallbackquery',[
'callback_query_id'=>$update->callback_query->id,
'text'=>"🚫 ليس لديك اعضاء محظورين ",
'show_alert'=>true]);}}

if($data == "updatenew"){
$infosudo = json_decode(file_get_contents("sudo.json"),true);
$join=$infosudo["info"]["update"];

if($join=="✅"){
$infosudo["info"]["update"]="❌";}

if($join=="❌"){
$infosudo["info"]["update"]="✅";}
file_put_contents("sudo.json", json_encode($infosudo));
sendwataw($chat_id,$message_id);}

if($data == "st_ch_bots"){
$watawjson = json_decode(file_get_contents("botmak/wataw.json"),true);
$join=$watawjson["info"]["st_ch_bots"];

if($join=="✅"){
$watawjson["info"]["st_ch_bots"]="❌";}

if($join=="❌"){
$watawjson["info"]["st_ch_bots"]="✅";}
file_put_contents("botmak/wataw.json", json_encode($watawjson));
sendwataw($chat_id,$message_id);}

if($data == "propots"){
$infosudo = json_decode(file_get_contents("sudo.json"),true);
$join=$infosudo["info"]["propots"];
if($join=="مجانية"){
$infosudo["info"]["propots"]="مدفوعة";}
if($join=="مدفوعة"){
$infosudo["info"]["propots"]="مجانية";}
file_put_contents("sudo.json", json_encode($infosudo));
sendwataw($chat_id,$message_id);}
if($data == "channelbots"){
$infosudo = json_decode(file_get_contents("sudo.json"),true);
$infosudo["info"]["amr"]="channelbots";
file_put_contents("sudo.json", json_encode($infosudo));
bot('editmessagetext',['chat_id'=>$chat_id, 
'text'=>"- حسننا عزيزي المدير قم بإعادة توجية منشور من القناة التي تريد جعلها قناة الاشتراك الاجباري في كل البوتات المصنوعة",
'disable_web_page_preview'=>true,
"message_id"=>$message_id,
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"إلغاء",'callback_data'=>"home"]],
]])]);}

if (isset($message->forward_from_chat) && $infosudo['info']['amr'] == 'channelbots' && in_array($from_id, $sudo)) {
$id_channel= $message->forward_from_chat->id;
if($id_channel != null){
$checkadmin = getChatstats($id_channel,$token);
if($checkadmin == true){
$namechannel = json_decode(file_get_contents("http://api.telegram.org/bot$token/getChat?chat_id=$id_channel"))->result->title;
$watawjson["info"]["id_channel"]="$id_channel";
$watawjson["info"]["name_channel"]="$namechannel";

file_put_contents("botmak/wataw.json", json_encode($watawjson));
$infosudo["info"]["amr"]="channel_idlink";
bot('sendmessage',['chat_id'=>$chat_id, 
'text'=>"
✅ تم إضافة القناة بنجاح عزيزي الادمن
info channel 
user : • قناة خاصة • 
name : $namechannel
id : $id_channel
*يجب عليك ارسال رابط القناة الخاص قم بارسالة الان
",
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"- إلغاء ",'callback_data'=>"home"]],
]])]);
}else{
bot('sendmessage',['chat_id'=>$chat_id, 
'text'=>"❌ البوت ليس ادمن في القناة 
- قم برفع البوت اولا لكي تتمكن من إضافتها
",
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"إعادة المحاولة",'callback_data'=>"channelbots"]],
]])]);
$infosudo["info"]["amr"]="null";}}
file_put_contents("sudo.json", json_encode($infosudo));}

if($text  and $text !="/start" and $infosudo["info"]["amr"]=="channel_idlink" and in_array($from_id,$sudo) and empty($message->forward_from_chat) ){
$tex=str_replace(['https://t.me/','http://t.me/'],'',$text);
$watawjson["info"]["st_ch_bots"]="✅";
$watawjson["info"]["link_channel"]="$tex";
file_put_contents("botmak/wataw.json", json_encode($watawjson));
bot('sendmessage',['chat_id'=>$chat_id, 
'text'=>"
✅ تم إضافة القناة بنجاح عزيزي الادمن
info channel 
link : $text 
t : $tex",
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"- تتغيير القناة   ",'callback_data'=>"chaneelbots"]],
]])]);

$infosudo["info"]["amr"]="null";
file_put_contents("sudo.json", json_encode($infosudo));}

if($data == "channelbots2"){
$infosudo = json_decode(file_get_contents("sudo.json"),true);
$infosudo["info"]["amr"]="channelbots2";
file_put_contents("sudo.json", json_encode($infosudo));
bot('editmessagetext',['chat_id'=>$chat_id, 
'text'=>"- حسننا عزيزي المدير قم بإعادة توجية منشور من القناة  2 التي تريد جعلها قناة الاشتراك الاجباري في كل البوتات المصنوعة",
'disable_web_page_preview'=>true,
"message_id"=>$message_id,
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"إلغاء",'callback_data'=>"home"]],
]])]);}

if($message->forward_from_chat and $infosudo["info"]["amr"]=="channelbots2" and in_array($from_id, $sudo)){
$id_channel= $message->forward_from_chat->id;
if($id_channel != null){
$checkadmin = getChatstats($id_channel,$token);
if($checkadmin == true){
$namechannel = json_decode(file_get_contents("http://api.telegram.org/bot$token/getChat?chat_id=$id_channel"))->result->title;
$watawjson["info"]["id_channel2"]="$id_channel";
$watawjson["info"]["name_channel2"]="$namechannel";
file_put_contents("botmak/wataw.json", json_encode($watawjson));
$infosudo["info"]["amr"]="channel_idlink2";
bot('sendmessage',['chat_id'=>$chat_id, 
'text'=>"
✅ تم إضافة القناة بنجاح عزيزي الادمن
info channel 
user : • قناة خاصة • 
name : $namechannel
id : $id_channel
*يجب عليك ارسال رابط القناة الخاص قم بارسالة الان
",
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"- إلغاء ",'callback_data'=>"home"]],
]])]);
}else{
bot('sendmessage',['chat_id'=>$chat_id, 
'text'=>"❌ البوت ليس ادمن في القناة 
- قم برفع البوت اولا لكي تتمكن من إضافتها
",
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"إعادة المحاولة",'callback_data'=>"channelbots"]],
]])]);
$infosudo["info"]["amr"]="null";}}
file_put_contents("sudo.json", json_encode($infosudo));}

if($text  and $text !="/start" and $infosudo["info"]["amr"]=="channel_idlink2" and in_array($from_id,$sudo) and empty($message->forward_from_chat) ){
$tex=str_replace(['https://t.me/','http://t.me/'],'',$text);
$watawjson["info"]["st_ch_bots"]="✅";
$watawjson["info"]["link_channel2"]="$tex";
file_put_contents("botmak/wataw.json", json_encode($watawjson));
bot('sendmessage',['chat_id'=>$chat_id, 
'text'=>"
✅ تم إضافة القناة بنجاح عزيزي الادمن
info channel 
link : $text 
t : $tex",
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"تتغيير القناة",'callback_data'=>"chaneelbots2"]],
]])]);

$infosudo["info"]["amr"]="null";
file_put_contents("sudo.json", json_encode($infosudo));}

if($data == "updatechannel"){
$infosudo["info"]["amr"]="updatechannel";
file_put_contents("sudo.json", json_encode($infosudo));
bot('editmessagetext',['chat_id'=>$chat_id, 
'text'=>"- قم بارسال الرابط الخاص لقناة التحديثات",
'parse_mode'=>markdown,
'disable_web_page_preview'=>true,
"message_id"=>$message_id,
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"إلغاء",'callback_data'=>"home"]],
]])]);}

if($text  and $text !="/start" and $infosudo["info"]["amr"]=="updatechannel" and in_array($from_id,$sudo)){
$tex=str_replace(['https://t.me/','http://t.me/'],'',$text);
bot('sendmessage',['chat_id'=>$chat_id, 
'text'=>"✅ تم حفظ الرابط الخاص لقناة التحديثات 
الرابط: $text | $t",
'disable_web_page_preview'=>true,
"message_id"=>$message_id,
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"• رجوع •",'callback_data'=>"home"]],
]])]);
$infosudo["info"]["amr"]="null";
$infosudo["info"]["updatechannel"]="$tex";
file_put_contents("sudo.json", json_encode($infosudo));}

if($data == "start"){
$infosudo["info"]["amr"]="start";
file_put_contents("sudo.json", json_encode($infosudo));
bot('editmessagetext',['chat_id'=>$chat_id, 
'text'=>"- قم بارسال نص رسالة /start",
'parse_mode'=>markdown,
'disable_web_page_preview'=>true,
"message_id"=>$message_id,
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"إلغاء",'callback_data'=>"home"]],
]])]);}

if($text  and $text !="/start" and $infosudo["info"]["amr"]=="start" and in_array($from_id,$sudo)){
bot('sendmessage',['chat_id'=>$chat_id, 
'text'=>"✅ تم حفظ كليشة /start 
الكليشة:
$text",
'disable_web_page_preview'=>true,
"message_id"=>$message_id,
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"• رجوع •",'callback_data'=>"home"]],
]])]);
$infosudo["info"]["amr"]="null";
$infosudo["info"]["start"]="$text";
file_put_contents("sudo.json", json_encode($infosudo));}

if($data == "info_kl"){
$infosudo["info"]["amr"]="info_kl";
file_put_contents("sudo.json", json_encode($infosudo));
bot('editmessagetext',['chat_id'=>$chat_id, 
'text'=>"- قم بارسال نص كليشة معلومات عن البوت",
'parse_mode'=>markdown,
'disable_web_page_preview'=>true,
"message_id"=>$message_id,
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"إلغاء",'callback_data'=>"home"]],
]])]);}

if($text  and $text !="/start" and $infosudo["info"]["amr"]=="info_kl" and in_array($from_id,$sudo)){
bot('sendmessage',['chat_id'=>$chat_id, 
'text'=>"- ✅ تم حفظ كليشة معلومات عن البوت 
الكليشة:
$text",
'disable_web_page_preview'=>true,
"message_id"=>$message_id,
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"• رجوع •",'callback_data'=>"home"]],
]])]);
$infosudo["info"]["amr"]="null";
$infosudo["info"]["info_kl"]="$text";
file_put_contents("sudo.json", json_encode($infosudo));}

if($data == "token_kl"){
$infosudo["info"]["amr"]="token_kl";
file_put_contents("sudo.json", json_encode($infosudo));
bot('editmessagetext',['chat_id'=>$chat_id, 
'text'=>"- قم بارسال نص كليشة إرسال التوكن",
'parse_mode'=>markdown,
'disable_web_page_preview'=>true,
"message_id"=>$message_id,
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"إلغاء",'callback_data'=>"home"]],
]])]);}

if($text  and $text !="/start" and $infosudo["info"]["amr"]=="token_kl" and in_array($from_id,$sudo)){
bot('sendmessage',['chat_id'=>$chat_id, 
'text'=>"✅ تم حفظ كليشة إرسال التوكن
الكليشة:
$text",
'disable_web_page_preview'=>true,
"message_id"=>$message_id,
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"• رجوع •",'callback_data'=>"home"]],
]])]);
$infosudo["info"]["amr"]="null";
$infosudo["info"]["token_kl"]="$text";
file_put_contents("sudo.json", json_encode($infosudo));}

if($data == "klish_sil"){
$infosudo["info"]["amr"]="klish_sil";
file_put_contents("sudo.json", json_encode($infosudo));
bot('editmessagetext',['chat_id'=>$chat_id, 
'text'=>"قم بارسال كليشة الاشتراك الاجباري",
'parse_mode'=>markdown,
'disable_web_page_preview'=>true,
"message_id"=>$message_id,
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"إلغاء",'callback_data'=>"home"]],
]])]);}
if($text  and $text !="/start" and $infosudo["info"]["amr"]=="klish_sil" and in_array($from_id,$sudo)){
bot('sendmessage',['chat_id'=>$chat_id, 
'text'=>"✅ تم حفظ كليشة الاشتراك الاجباري 
الكليشة:
$text",
'disable_web_page_preview'=>true,
"message_id"=>$message_id,
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"• رجوع •",'callback_data'=>"home"]],
]])]);
$infosudo["info"]["amr"]="null";
$infosudo["info"]["klish_sil"]="$text";
file_put_contents("sudo.json", json_encode($infosudo));}

if($data == "sudo"){
$infosudo["info"]["amr"]="sudo";
file_put_contents("sudo.json", json_encode($infosudo));
bot('editmessagetext',['chat_id'=>$chat_id, 
'text'=>"- قم بارسال ايدي مطور البوت.",
'parse_mode'=>markdown,
'disable_web_page_preview'=>true,
"message_id"=>$message_id,
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"إلغاء",'callback_data'=>"home"]],
]])]);}

if($text  and $text !="/start" and $infosudo["info"]["amr"]=="sudo" and in_array($from_id,$sudo)){
bot('sendmessage',['chat_id'=>$chat_id, 
'text'=>"✅ تم حفظ حساب مطور البوت.
الحساب: $text",
'disable_web_page_preview'=>true,
"message_id"=>$message_id,
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"• رجوع •",'callback_data'=>"home"]],
]])]);
$infosudo["info"]["amr"]="null";
$infosudo["info"]["sudo"]="$text";
file_put_contents("sudo.json", json_encode($infosudo));}

if($data == "home"){
$infosudo["info"]["amr"]="null";
file_put_contents("sudo.json", json_encode($infosudo));
sendwataw($chat_id,$message_id);}
if($data == "fwrmember"){
$infosudo = json_decode(file_get_contents("sudo.json"),true);
$fwrmember=$infosudo["info"]["fwrmember"];
if($fwrmember=="✅"){
$infosudo["info"]["fwrmember"]="❌";}
if($fwrmember=="❌"){
$infosudo["info"]["fwrmember"]="✅";}
file_put_contents("sudo.json", json_encode($infosudo));
sendwataw($chat_id,$message_id);}
if($data == "tnbih"){
$infosudo = json_decode(file_get_contents("sudo.json"),true);
$tnbih=$infosudo["info"]["tnbih"];
if($tnbih=="✅"){
$infosudo["info"]["tnbih"]="❌";}
if($tnbih=="❌"){
$infosudo["info"]["tnbih"]="✅";}
file_put_contents("sudo.json", json_encode($infosudo));
sendwataw($chat_id,$message_id);}

if($data == "silk"){
$infosudo = json_decode(file_get_contents("sudo.json"),true);
$skil=$infosudo["info"]["silk"];
if($skil=="✅"){
$infosudo["info"]["silk"]="❌";}
if($skil=="❌"){
$infosudo["info"]["silk"]="✅";}
file_put_contents("sudo.json", json_encode($infosudo));
sendwataw($chat_id,$message_id);}

if($data == "allch"){
$infosudo = json_decode(file_get_contents("sudo.json"),true);
$allch=$infosudo["info"]["allch"];
if($allch=="مفردة"){
$infosudo["info"]["allch"]="مجموعة";}
if($allch=="مجموعة"){
$infosudo["info"]["allch"]="مفردة";}
file_put_contents("sudo.json", json_encode($infosudo));
sendwataw($chat_id,$message_id);}

if($data == "addchannel"){
$infosudo = json_decode(file_get_contents("sudo.json"),true);
$orothe= $infosudo["info"]["channel"];
$count=count($orothe);
if($count<4){
$infosudo["info"]["amr"]="addchannel";
file_put_contents("sudo.json", json_encode($infosudo));
bot('editmessagetext',['chat_id'=>$chat_id, 
'text'=>"- اذا كانت القناة التي تريد اضافتها عامة قم بارسال معرفها .
* اذا كانت خاصة قم بإعادة توجية منشور من القناة إلى هنا .
",
'disable_web_page_preview'=>true,
"message_id"=>$message_id,
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"إلغاء",'callback_data'=>"home"]],
]])]);}else{
bot('editmessagetext',['chat_id'=>$chat_id, 
'text'=>"🚫 لا يمكنك اضافة اكثر من  3 قنوات للإشتراك الاجباري",
'disable_web_page_preview'=>true,
"message_id"=>$message_id,
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"• رجوع •",'callback_data'=>"home"]],
]])]);}}

if($text  and $text !="/start" and $infosudo["info"]["amr"]=="addchannel" and in_array($from_id,$sudo) and empty($message->forward_from_chat) ){
$ch_id = json_decode(file_get_contents("http://api.telegram.org/bot$token/getChat?chat_id=$text"))->result->id;
$idchan=$ch_id;
if($ch_id != null){
$checkadmin = getChatstats($text,$token);
if($checkadmin == true){
$namechannel = json_decode(file_get_contents("http://api.telegram.org/bot$token/getChat?chat_id=$text"))->result->title;
$infosudo["info"]["channel"][$ch_id]["st"]="عامة";
$infosudo["info"]["channel"][$ch_id]["user"]="$text";
$infosudo["info"]["channel"][$ch_id]["name"]="$namechannel";
bot('sendmessage',['chat_id'=>$chat_id, 
'text'=>"
✅ تم إضافة القناة بنجاح عزيزي الادمن
info channel 
user : $text 
name : $namechannel
id : $ch_id",
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"- إضافة قناة آخرى  ",'callback_data'=>"addchannel"]],
]])]);
}else{
bot('sendmessage',['chat_id'=>$chat_id, 
'text'=>"❌ البوت ليس ادمن في القناة 
- قم برفع البوت اولا لكي تتمكن من إضافتها
",
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"إعادة المحاولة",'callback_data'=>"addchannel"]],
]])]);}
}else{
bot('sendmessage',['chat_id'=>$chat_id, 
'text'=>"❌ لم تتم إضافة القناة لا توجد قناة تمتلك هذا المعرف: $text",
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"• رجوع •",'callback_data'=>"home"]],
]])]);}
$infosudo["info"]["amr"]="null";
file_put_contents("sudo.json", json_encode($infosudo));}

if($message->forward_from_chat and $infosudo["info"]["amr"]=="addchannel" and in_array($from_id, $sudo)){
$id_channel= $message->forward_from_chat->id;
if($id_channel != null){
$checkadmin = getChatstats($id_channel,$token);
if($checkadmin == true){
$namechannel = json_decode(file_get_contents("http://api.telegram.org/bot$token/getChat?chat_id=$id_channel"))->result->title;
$infosudo["info"]["channel_id"]="$id_channel";
bot('sendmessage',['chat_id'=>$chat_id, 
'text'=>"
✅ تم إضافة القناة بنجاح عزيزي الادمن
info channel 
user : • قناة خاصة • 
name : $namechannel
id : $id_channel
*يجب عليك ارسال رابط القناة الخاص قم بارسالة الان
",
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"إلغاء",'callback_data'=>"addchannel"]],
]])]);
}else{
bot('sendmessage',['chat_id'=>$chat_id, 
'text'=>"❌ البوت ليس ادمن في القناة 
- قم برفع البوت اولا لكي تتمكن من إضافتها
",
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"إعادة المحاولة",'callback_data'=>"addchannel"]],
]])]);}}

$infosudo["info"]["amr"]="channel_id";
file_put_contents("sudo.json", json_encode($infosudo));}
$channel_id = $infosudo['info']['channel_id'] ?? 'null';

if($text  and $text !="/start" and $infosudo["info"]["amr"]=="channel_id" and in_array($from_id,$sudo) and empty($message->forward_from_chat) ){
$checkadmin = getChatstats($channel_id,$token);
if($checkadmin == true){
$namechannel = json_decode(file_get_contents("http://api.telegram.org/bot$token/getChat?chat_id=$channel_id"))->result->title;
$infosudo["info"]["channel"][$channel_id]["st"]="خاصة";
$infosudo["info"]["channel"][$channel_id]["user"]="$text";
$infosudo["info"]["channel"][$channel_id]["name"]="$namechannel";
bot('sendmessage',['chat_id'=>$chat_id, 
'text'=>"
✅ تم إضافة القناة بنجاح عزيزي الادمن
info channel 
link : $text 
name : $namechannel
id : $channel_id",
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"+ إضافة قناة آخرى",'callback_data'=>"addchannel"]],
]])]);
}else{
bot('sendmessage',['chat_id'=>$chat_id, 
'text'=>"❌ البوت ليس ادمن في القناة 
- قم برفع البوت اولا لكي تتمكن من إضافتها
",
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"إعادة المحاولة",'callback_data'=>"addchannel"]],
]])]);}

$infosudo["info"]["amr"]="null";
$infosudo["info"]["channel_id"]="null";
file_put_contents("sudo.json", json_encode($infosudo));}

if($data == "viwechannel" and in_array($from_id, $sudo)){
$infosudo = json_decode(file_get_contents("sudo.json"),true);
$orothe= $infosudo["info"]["channel"];
$keyboard["inline_keyboard"]=[];

foreach($orothe as $co ){

$namechannel= $co["name"];
$st= $co["st"];
$userchannel= $co["user"];
if($namechannel!=null){
$keyboard["inline_keyboard"][] = [['text'=>$namechannel,'callback_data'=>'null']];

if($st=="خاصة"){
$userchannel="null";}
$keyboard["inline_keyboard"][] =
[['text'=>$userchannel,'callback_data'=>'cull'],['text'=>$st,'callback_data'=>'null']];}}

$keyboard["inline_keyboard"][] = [['text'=>"• رجوع •",'callback_data'=>"home"]];
$reply_markup=json_encode($keyboard);
bot('editmessagetext',['chat_id'=>$chat_id, 
'text'=>"- هذة هي قنوات الاشتراك الاجباري الخاصة بك",
'parse_mode'=>markdown,
'disable_web_page_preview'=>true,
"message_id"=>$message_id,
'reply_markup'=>$reply_markup]);}

if($data == "delchannel" and in_array($from_id, $sudo)){
$infosudo = json_decode(file_get_contents("sudo.json"),true);
$orothe= $infosudo["info"]["channel"];
$keyboard["inline_keyboard"]=[];
foreach($orothe as $co=>$s ){
$namechannel= $s["name"];
$st= $s["st"];
$userchannel= $s["user"];
if($namechannel!=null){
$keyboard["inline_keyboard"][] = [['text'=>$namechannel,'callback_data'=>'null']];
if($st=="خاصة"){
$userchannel="null";}
$keyboard["inline_keyboard"][] =
[['text'=>'🚫 حذف','callback_data'=>'deletchannel '.$co],['text'=>$st,'callback_data'=>'null']];}}
$keyboard["inline_keyboard"][] = [['text'=>"• رجوع •",'callback_data'=>"home"]];
$reply_markup=json_encode($keyboard);
bot('editmessagetext',['chat_id'=>$chat_id, 
'text'=>"- قم بالضغط على خيار الحذف بالاسفل",
'parse_mode'=>markdown,
'disable_web_page_preview'=>true,
"message_id"=>$message_id,
'reply_markup'=>$reply_markup
]);}

if(preg_match('/^(deletchannel) (.*)/s', $data)){
$nn = str_replace('deletchannel ',"",$data);
bot('editmessagetext',['chat_id'=>$chat_id, 
'text'=>"✅ تم حذف القناة بنجاح 
- id $nn",
'parse_mode'=>markdown,
'disable_web_page_preview'=>true,
"message_id"=>$message_id,
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"• رجوع •",'callback_data'=>"delchannel"]],
]])]);
unset($infosudo["info"]["channel"][$nn]);
file_put_contents("sudo.json", json_encode($infosudo));}

if($message and $fwrmember=="✅"){
bot('ForwardMessage',['chat_id'=>$admin,
'from_chat_id'=>$chat_id,
'message_id'=>$message->message_id,]);}

$amr = file_get_contents("sudo/amr.txt");
$no3send =file_get_contents("no3send.txt");
$chatsend=file_get_contents("chatsend.txt");
if($data == "sendmessage" and  in_array($from_id,$sudo)){
bot('editmessagetext',['chat_id'=>$chat_id,
'text'=>"أهلا بك عزيزي في قسم الاذاعة
 قم بتحديد نوع الاذاعة ومكان ارسال الاذاعة
ثم قم الضغط على ارسال الرسالة 
",'message_id'=>$message_id,
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"نوع الاذاعة: $no3send",'callback_data'=>"button"]],
[['text'=>"توجية",'callback_data'=>"forward"],['text'=>"MARKDOWN",'callback_data'=>"MARKDOWN"],['text'=>"HTML",'callback_data'=>"HTML"]],
[['text'=>"الارسال الى: $chatsend",'callback_data'=>"button"]],
[['text'=>"الاعضاء",'callback_data'=>"member"],['text'=>"كل البوتات",'callback_data'=>"botsall"]],
[['text'=>"ارسال الرسالة",'callback_data'=>"post"]],
[['text'=>"• رجوع •",'callback_data'=>"home"]],
]])]);}

function sendwataw2($chat_id,$message_id){
$no3send =file_get_contents("no3send.txt");
$chatsend=file_get_contents("chatsend.txt");
bot('editmessagetext',['chat_id'=>$chat_id,
'text'=>"أهلا بك عزيزي في قسم الاذاعة
قم بتحديد نوع الاذاعة ومكان ارسال الاذاعة
ثم قم  الضغط على ارسال الرسالة
",
'message_id'=>$message_id,
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"نوع الاذاعة: $no3send",'callback_data'=>"button"]],
[['text'=>"توجية",'callback_data'=>"forward"],['text'=>"MARKDOWN",'callback_data'=>"MARKDOWN"],['text'=>"HTML",'callback_data'=>"HTML"]],
[['text'=>"الارسال الى: $chatsend",'callback_data'=>"button"]],
[['text'=>"الاعضاء",'callback_data'=>"member"],['text'=>"كل البوتات",'callback_data'=>"botsall"]],
[['text'=>"ارسال الرسالة",'callback_data'=>"post"]],
[['text'=>"• رجوع •",'callback_data'=>"home"]],
]])]);} 


if($data == "forward"){
file_put_contents("no3send.txt","forward");
sendwataw2($chat_id,$message_id);}

if($data == "MARKDOWN"){
file_put_contents("no3send.txt","MARKDOWN");
sendwataw2($chat_id,$message_id);}

if($data == "HTML"){
file_put_contents("no3send.txt","html");
sendwataw2($chat_id,$message_id);}

if($data == "member"){
file_put_contents("chatsend.txt","member");
sendwataw2($chat_id,$message_id);}

if($data == "botsall"){
file_put_contents("chatsend.txt","botsall");
sendwataw2($chat_id,$message_id);}

$no3send =file_get_contents("no3send.txt");
$chatsend=file_get_contents("chatsend.txt");
if($data == "post" and $no3send!=null and $chatsend!=null and  in_array($from_id,$sudo) ){
file_put_contents("sudo/amr.txt","sendsend");
bot('EditMessageText',[
'message_id'=>$message_id,
'chat_id'=>$chat_id,
'text'=>"قم بارسال رسالتك الان  
نوع الارسال : $no3send
مكان الارسال : $chatsend
",
'message_id'=>$message_id,
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"إلغاء",'callback_data'=>"set"]],]])]);}

if($data == "set" and  in_array($from_id,$sudo) ){
unlink("sudo/amr.txt");
bot('editmessagetext',['chat_id'=>$chat_id,
'text'=>"تم إلغاء الارسال بنجاح",
'message_id'=>$message_id,
]);}

$forward = $message->forward_from ?? null;
$photo = $message->photo ?? null;
$video = $message->video ?? null;
$document = $message->document ?? null;
$sticker = $message->sticker ?? null;
$voice = $message->voice ?? null;
$audio = $message->audio ?? null;
$member =file_get_contents("sudo/member.txt");

if($photo){
$sens="sendphoto";
$file_id = $update->message->photo[1]->file_id;}

if($document){
$sens="senddocument";
$file_id = $update->message->document->file_id;}

if($video){
$sens="sendvideo";
$file_id = $update->message->video->file_id;}

if($audio){
$sens="sendaudio";
$file_id = $update->message->audio->file_id;}

if($voice){
$sens="sendvoice";
$file_id = $update->message->voice->file_id;}

if($sticker){
$sens="sendsticker";
$file_id = $update->message->sticker->file_id;}

if($message  and $text !="الاذاعة" and $amr == "sendsend" and $no3send=="forward" and  in_array($from_id,$sudo) ){
unlink("sudo/amr.txt");

if($chatsend=="member"){
$for=$member;
$txt="تم التوجية - خاص - للاعضاء فقط";
$foor=explode("\n",$for);
bot('ForwardMessage',['chat_id'=>$chat_id,
'from_chat_id'=>$chat_id,
'message_id'=>$message->message_id,]);
for($i=0;$i<count($foor); $i++){
bot('ForwardMessage',['chat_id'=>$foor[$i],
'from_chat_id'=>$chat_id,
'message_id'=>$message->message_id,
]);}
bot('sendmessage',['chat_id'=>$chat_id,
'text'=>"✅ $txt",
'message_id'=>$message_id,
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>'• رجوع •','callback_data'=>"home"]],
]])]);}

if($chatsend=="botsall"){
$bots=explode("\n",file_get_contents("infoidbots.txt"));
$coo=count($bots)-1;
for ($i=0; $i < count($bots); $i++) { 
$idbots=$bots[$i];

include("wataw/$idbots.php");

$tokenboot="$tokenbot";
$mm=explode("\n",file_get_contents("botmak/$idbots/sudo/member.txt"));
for ($l=0; $l < count($mm); $l++) {
$id=$mm[$l];
file_get_contents("https://api.telegram.org/bot".$tokenboot."/ForwardMessage?chat_id=".$id."&from_chat_id=$chat_id&message_id=".$message->message_id);}
$co=$co+$l-1;}
bot('sendmessage',['chat_id'=>$chat_id,
"text"=>"- تمت الاذاعة في جميع البوتات المصنوعة 
- تم الارسال الى $co مستخدم.
- عدد البوتات : $coo
",
'reply_to_message_id'=>$message_id,
]);}

unlink("no3send.txt");
unlink("chatsend.txt");}

if($message and $text !="الاذاعة"  and $amr == "sendsend"and $no3send !="forward" and  in_array($from_id,$sudo) ){
unlink("sudo/amr.txt");

if($chatsend=="member"){
$for=$member;
$txt=" تم النشر - خاص - للاعضاء فقط";
$foor=explode("\n",$for);
if($text){
bot('sendMessage',['chat_id'=>$chat_id,
'text'=>"$text",
'parse_mode'=>"$no3send",
'disable_web_page_preview'=>true,]);

for($i=0;$i<count($foor); $i++){
bot('sendMessage',['chat_id'=>$foor[$i],
'text'=>"$text",
'parse_mode'=>"$no3send",
'disable_web_page_preview'=>true,
]);}
}else{
$ss=str_replace("send","",$sens);
bot($sens,["chat_id"=>$chat_id,
"$ss"=>"$file_id",
'caption'=>"$caption",]);
for($i=0;$i<count($foor); $i++){
$ss=str_replace("send","",$sens);
bot($sens,["chat_id"=>$foor[$i],
"$ss"=>"$file_id",
'caption'=>"$caption",]);}}
bot('sendmessage',['chat_id'=>$chat_id,
'text'=>"✅ $txt",
'message_id'=>$message_id,
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>'• رجوع •','callback_data'=>"home"]],
]])]);
unlink("no3send.txt");
unlink("chatsend.txt");}

if($chatsend=="botsall"){
$bots=explode("\n",file_get_contents("infoidbots.txt"));
$coo=count($bots)-1;
for ($i=0; $i < count($bots); $i++) { 
$idbots=$bots[$i];

include("wataw/$idbots.php");

$tokenboot="$tokenbot";
$mm=explode("\n",file_get_contents("botmak/$idbots/sudo/member.txt"));
for ($l=0; $l < count($mm); $l++) {
$id=$mm[$l];
if($text){
file_get_contents("https://api.telegram.org/bot".$tokenboot."/sendMessage?chat_id=".$id."&text=$text&parse_mode=".$no3send);
}else{
$ss=str_replace("send","",$sens);
file_get_contents("https://api.telegram.org/bot".$tokenboot."/$sens?chat_id=".$id."&$ss=$file_id&caption=".$caption);}}
$co=$co+$l-1;}
bot('sendmessage',['chat_id'=>$chat_id,
"text"=>"- تمت الاذاعة في جميع البوتات المصنوعة 
- تم الارسال الى $co مستخدم.
- عدد البوتات : $coo
",
'reply_to_message_id'=>$message_id,
]);}} 

if($data == "admins" and $from_id ==$ameed){
$infosudo = json_decode(file_get_contents("sudo.json"),true);
$orothe= $infosudo["info"]["admins"];
$keyboard["inline_keyboard"]=[];
foreach($orothe as $co=>$sss ){
if($co!=null and $co!=$ameed ){
$keyboard["inline_keyboard"][] =
[['text'=>' 🗑','callback_data'=>'deleteadmin '.$co.'#'.$sss],['text'=>$sss,'callback_data'=>'null']];
}}
$keyboard["inline_keyboard"][] = [['text'=>"- اضافة ادمن  ",'callback_data'=>"addadmin"]];
$keyboard["inline_keyboard"][] = [['text'=>"- عودة  ",'callback_data'=>"home"]];
$reply_markup=json_encode($keyboard);
bot('EditMessageText',['chat_id'=>$chat_id, 
'text'=>"- تستطيع فقط رفع 5 ادمنية 
*تنوية : الادمنية يستطيعون التحكم بإعدادات البوت ماعدا قسم الادمنية .
",
'disable_web_page_preview'=>true,
"message_id"=>$message_id,
'reply_markup'=>$reply_markup
]);}

if($data == "addadmin"){
$infosudo["info"]["amr"]="addadmin";
file_put_contents("sudo.json", json_encode($infosudo));
bot('EditMessageText',['chat_id'=>$chat_id, 
'text'=>"- قم بارسال ايدي الادمن ",
'parse_mode'=>markdown,
'disable_web_page_preview'=>true,
"message_id"=>$message_id,
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"- الغاء",'callback_data'=>"home"]],
]])]);}

if($text  and $text !="/start" and $infosudo["info"]["amr"]=="addadmin" and $from_id ==$ameed and is_numeric($text)){
if(!in_array($text,$admins)){
$infosudo = json_decode(file_get_contents("sudo.json"),true);
$orothe= $infosudo["info"]["channel"];
$count=count($orothe);
if($count<6){
bot('sendmessage',['chat_id'=>$chat_id, 
'text'=>"- ✅ تم حفظ  رفع الادمن بنجاح",
'disable_web_page_preview'=>true,
"message_id"=>$message_id,
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"- عودة",'callback_data'=>"admins"]],
]])]);
$infosudo["info"]["admins"][]="$text";
}else{
bot('sendmessage',['chat_id'=>$chat_id, 
'text'=>"🚫 لايمكنك اضافة اكثر من 5 ادمنية ً",
'disable_web_page_preview'=>true,
"message_id"=>$message_id,
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"- عودة  ",'callback_data'=>"admins"]],
]])]);}
}else{
bot('sendmessage',['chat_id'=>$chat_id, 
'text'=>"- ⚠ الادمن مضاف مسبقاً",
'disable_web_page_preview'=>true,
"message_id"=>$message_id,
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"- عودة  ",'callback_data'=>"admins"]],
]])]);}
$infosudo["info"]["amr"]="null";
file_put_contents("sudo.json", json_encode($infosudo));}

if(preg_match('/^(deleteadmin) (.*)/s', $data)){
$nn = str_replace('deleteadmin ',"",$data);
$ex=explode('#',$nn);
$id=$ex[1];
$n=$ex[0];
bot('EditMessageText',['chat_id'=>$chat_id, 
'text'=>"- ✅ تم حذف الادمن بنجاح 
$id",
'parse_mode'=>markdown,
'disable_web_page_preview'=>true,
"message_id"=>$message_id,
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"- عودة",'callback_data'=>"admins"]],
]])]);
unset($infosudo["info"]["admins"][$n]);
file_put_contents("sudo.json", json_encode($infosudo));}

$botfree=explode("\n",file_get_contents("from_id/$from_id/countuserbot.txt"));
$countbot=count($botfree)-1;
$infobots=file_get_contents("from_id/$from_id/countuserbot.txt");
if($infobots!=null ){
$infobotsmember="♻ بوتاتك المصنوعة ($countbot) هي : \n$infobots";
}else{
$infobotsmember = "لم تقم بصنع اي بوت مسبقاً";}

if($start==null){
$start = "لم يتم تعيين كليشة /start من قبل المدير";}

if($info_kl==null){
$info_kl = "لم يتم تعيين الكليشة من قبل المدير";}

if($token_kl==null){
$token_kl="
- لإنشاء بوت قم بإرسال التوكن من بوت : 
( @botfather ).
الان قم بارسال التوكن الخاص بك لصنع:
";}

if (!function_exists('btn')) {
    function btn($text, $callback, $style = null) {
        $button = [
            'text' => $text,
            'callback_data' => $callback
        ];

        if ($style !== null) {
            $button['style'] = $style;
        }

        return $button;
    }
}

if (!function_exists('adminKeyboard')) {
    function adminKeyboard($updatenew, $st_ch_bots, $fwrmember, $tnbih, $silk, $allch) {
        return [
            [btn("حالة البوت: $updatenew", "updatenew", "primary"), btn("اشتراك المصنوعات: $st_ch_bots", "st_ch_bots", "secondary")],
            [btn("اشتراك للبوتات 1.", "channelbots", "primary"), btn("اشتراك للبوتات 2.", "channelbots2", "primary")],
            [btn("صنع بوت جديد", "sn3botfre", "success"), btn("قائمة بوتاتك", "botsmember", "primary")],
            [btn("اضافة ملف الى الصانع", "uplode", "success")],
            [btn("تعيين قناة تحديثات البوت", "updatechannel", "primary")],
            [btn("إلغاء اشتراك مدفوع", "delprobot", "danger"), btn("اضافة اشتراك مدفوع", "addprobot", "success")],
            [btn("تعيين حساب المطور", "sudo", "secondary"), btn("تعيين (Start)", "start", "primary")],
            [btn("تعيين معلومات البوت", "info_kl", "secondary"), btn("تعيين كليشة التوكن", "token_kl", "secondary")],
            [btn("التوجية: $fwrmember", "fwrmember", "primary"), btn("التنبية: $tnbih", "tnbih", "primary")],
            [btn("قسم الاذاعة", "sendmessage", "primary"), btn("النسخ والاستعادة", "BackRest", "success")],
            [btn("حظر عضو", "ban", "danger"), btn("إلغاء حظر عضو", "unban", "success")],
            [btn("مسح قناة", "delchannel", "danger"), btn("إضافة قناة", "addchannel", "success")],
            [btn("قنوات الاشتراك", "viwechannel", "primary"), btn("تعيين رسالة الاشتراك", "klish_sil", "secondary")],
            [btn("عرض انلاين: $silk", "silk", "primary"), btn("عرض الرسالة: $allch", "allch", "secondary")]
        ];
    }
}
if (!function_exists('userKeyboard')) {
    function userKeyboard($updatechannel) {
        return [
            [btn("معلومات اكثر عن البوت💡", "infobot")],
            [btn("صنع بوت جديد", "sn3botfre"), btn("قائمة بوتاتك", "botsmember")],
            [btn("اضافة ملف الى الصانع", "uplode")],
            [['text' => 'قناة تحديثات البوت 🔬', 'url' => "https://t.me/$updatechannel"]]
        ];
    }
}

if (!function_exists('sendUserPanel')) {
    function sendUserPanel($chat_id, $name, $start, $updatechannel) {
        bot('sendmessage', [
            'chat_id' => $chat_id,
            'text' => "أهلاً بك [$name](tg://user?id=$chat_id)\n\n$start",
            'parse_mode' => "MarkDown",
            'disable_web_page_preview' => true,
            'reply_markup' => json_encode([
                'inline_keyboard' => userKeyboard($dev_channel)
            ])
        ]);
    }
}

if (!function_exists('sendInfoPanel')) {
    function sendInfoPanel($chat_id, $message_id, $name, $info_kl) {
        bot('editmessagetext', [
            'chat_id' => $chat_id,
            'message_id' => $message_id,
            'text' => "أهلا بك يا [$name](tg://user?id=$chat_id)\n\n$info_kl",
            'parse_mode' => "MarkDown",
            'disable_web_page_preview' => true,
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [btn('• رجوع •', 'freebot')]
                ]
            ])
        ]);
    }
}

$amrmem = file_exists("from_id/$from_id/amr.txt")
    ? file_get_contents("from_id/$from_id/amr.txt")
    : "";

if ($text == "/start") {
    file_put_contents("from_id/$from_id/amr.txt", "");

    if (in_array((string)$from_id, array_map('strval', $sudo), true)) {
        bot('sendmessage', [
            'chat_id' => $chat_id,
            'text' => "
- اهلا بك عزيزي في لوحة اوامر البوت يمكنك التحكم بها مثل ماتشاء..
اليك الاحصائيات:
• الاعضاء: $cunte
• المصنوعات: $countbots
",
            'disable_web_page_preview' => true,
            'reply_markup' => json_encode([
                'inline_keyboard' => adminKeyboard(
                    $updatenew,
                    $st_ch_bots,
                    $fwrmember,
                    $tnbih,
                    $silk,
                    $allch
                )
            ])
        ]);
    } else {
        sendUserPanel($chat_id, $name, $start, $updatechannel);
    }
}

if ($data == "infobot") {
    file_put_contents("from_id/$from_id/amr.txt", "");
    sendInfoPanel($chat_id, $message_id, $name, $info_kl);
}

if ($data == "freebot") {
    file_put_contents("from_id/$from_id/amr.txt", "");
    bot('editmessagetext', [
        'chat_id' => $chat_id,
        'message_id' => $message_id,
        'text' => "أهلاً بك [$name](tg://user?id=$chat_id)\n\n$start",
        'parse_mode' => "MarkDown",
        'disable_web_page_preview' => true,
        'reply_markup' => json_encode([
            'inline_keyboard' => userKeyboard($updatechannel)
        ])
    ]);
}


if($data == "sn3botfre" ){
bot('editmessagetext',['chat_id'=>$chat_id,
'message_id'=>$message_id,
"text"=>"- قم بإختيار نوعية البوت الذي تود صنعه 🎙️ ..",
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>'• قائمة البوتات •','callback_data'=>"null"]],
[['text'=>'','callback_data'=>"mak 6"],['text'=>'بوت سايت','callback_data'=>"mak 2"]],
[['text'=>'بوت الازرار','callback_data'=>"mak 3"],['text'=>'بوت المتجر','callback_data'=>"mak 4"]],
[['text'=>'بوت البصمات','callback_data'=>"mak 5"],['text'=>'بوت تواصل','callback_data'=>"mak 1"]],
[['text'=>'بوت التعليقات','callback_data'=>"mak 7"],['text'=>'ادارة منشورات القناة','callback_data'=>"mak 8"]],
[['text'=>'بوت زخرفة','callback_data'=>"mak 9"],['text'=>'فيديوهات ستوري','callback_data'=>"mak 10"]],
[['text'=>'تحليل الشخصية','callback_data'=>"mak 11"],['text'=>'العاب انلاين','callback_data'=>"mak 12"]],
[['text'=>'تحويل الصيغ','callback_data'=>"mak 13"],['text'=>'إستخراج روابط القنوات','callback_data'=>"mak 14"]],
[['text'=>'بوت همسة','callback_data'=>"mak 15"],['text'=>'بوت كشف الكذب','callback_data'=>"mak 16"]],
[['text'=>'• المزيد من البوتات • ','callback_data'=>"makll"]],
[['text'=>'• رجوع •','callback_data'=>"freebot"]],
]])]);}

if($data == "makll" ){
bot('editmessagetext',['chat_id'=>$chat_id,
'message_id'=>$message_id,
"text"=>"- قم بإختيار نوعية البوت الذي تود صنعه 🎙️ ..",
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>'• قائمة البوتات •','callback_data'=>"null"]],
[['text'=>'','callback_data'=>"mak 6"],['text'=>'بوت سايت','callback_data'=>"mak 2"]],
[['text'=>'بوت الازرار','callback_data'=>"mak 3"],['text'=>'بوت المتجر','callback_data'=>"mak 4"]],
[['text'=>'بوت البصمات','callback_data'=>"mak 5"],['text'=>'بوت تواصل','callback_data'=>"mak 1"]],
[['text'=>'بوت التعليقات','callback_data'=>"mak 7"],['text'=>'ادارة منشورات القناة','callback_data'=>"mak 8"]],
[['text'=>'بوت زخرفة','callback_data'=>"mak 9"],['text'=>'فيديوهات ستوري','callback_data'=>"mak 10"]],
[['text'=>'تحليل الشخصية','callback_data'=>"mak 11"],['text'=>'العاب انلاين','callback_data'=>"mak 12"]],
[['text'=>'تحويل الصيغ','callback_data'=>"mak 13"],['text'=>'إستخراج روابط القنوات','callback_data'=>"mak 14"]],
[['text'=>'بوت همسة','callback_data'=>"mak 15"],['text'=>'بوت كشف الكذب','callback_data'=>"mak 16"]],
[['text'=>'حذف الحساب','callback_data'=>"mak 17"],['text'=>'بوت الاعلانات','callback_data'=>"mak 18"]],
[['text'=>'بوت الاذكار','callback_data'=>"mak 19"],['text'=>'الاكواد الجاهزة','callback_data'=>"mak 20"]],
[['text'=>'حساب العمر','callback_data'=>"mak 21"],['text'=>'ستوري انستكرام','callback_data'=>"mak 22"]],
[['text'=>'البلورة السحرية','callback_data'=>"mak 23"],['text'=>'زخرفة تمبلر','callback_data'=>"mak 24"]],
[['text'=>'مشاركة بدون توجية','callback_data'=>"mak 25"],['text'=>'حجر ورقة مقص','callback_data'=>"mak 26"]],
[['text'=>'حماية القنوات','callback_data'=>"mak 27"],['text'=>'تمويل القنوات','callback_data'=>"mak 28"]],
[['text'=>'نسبة الحب','callback_data'=>"mak 29"],['text'=>'دعم القنوات','callback_data'=>"mak 30"]],
[['text'=>'نسبة الجمال','callback_data'=>"mak 31"],['text'=>'معاني الاسماء','callback_data'=>"mak 32"]],
[['text'=>'يوتيوب','callback_data'=>"mak 33"],['text'=>'تيك توك','callback_data'=>"mak 34"]],
[['text'=>'اسئلة جريئه','callback_data'=>"mak 35"],['text'=>'الفقرات','callback_data'=>"mak 36"]],
[['text'=>'','callback_data'=>"mak 37"],['text'=>'بوت ردود','callback_data'=>"mak 38"]],
[['text'=>'ماسح الخلفيات','callback_data'=>"mak 39"],['text'=>'ملصقات','callback_data'=>"mak 40"]],
[['text'=>'تعديل حقوق الاغاني','callback_data'=>"mak 41"],['text'=>'الفلاتر','callback_data'=>"mak 42"]],
[['text'=>'مسح الميديا','callback_data'=>"mak 50"]],
[['text'=>'نسبة الرجوله','callback_data'=>"mak 51"],['text'=>'نسبة المثلية','callback_data'=>"mak 52"]],
[['text'=>'الابراج','callback_data'=>"mak 53"],['text'=>'حذف رسايل القنوات','callback_data'=>"mak 54"]],
[['text'=>'تصميم اجمل ثنائي','callback_data'=>"mak 55"],['text'=>'خيره','callback_data'=>"mak 56"]],
[['text'=>'كشف المستقبل','callback_data'=>"mak 57"],['text'=>'ستوريات وصور','callback_data'=>"mak 58"]],
[['text'=>'باقات انترنت','callback_data'=>"mak 59"],['text'=>'تصاميم صور','callback_data'=>"mak 60"]],
[['text'=>'اهداء الاغاني','callback_data'=>"mak 61"],['text'=>'سمسمي','callback_data'=>"mak 62"]],
[['text'=>'كت تويت','callback_data'=>"mak 63"],['text'=>'نودز','callback_data'=>"mak 64"]],
[['text'=>'ازرار شفافه','callback_data'=>"mak 65"],['text'=>'فيزه','callback_data'=>"mak 66"]],
[['text'=>'خاص بالانستكرام','callback_data'=>"mak 67"],['text'=>'لعبة محيبس','callback_data'=>"mak 68"]],
[['text'=>'الالعاب','callback_data'=>"mak 69"]],
[['text'=>'• رجوع •','callback_data'=>"freebot"]],
]])]);}

$botfree=explode("\n",file_get_contents("from_id/$from_id/countbot.txt"));
$countbot=count($botfree);
if(preg_match('/^(mak) (.*)/s', $data)){
$nu = str_replace('mak ',"",$data);
if($nu == 1){ $b= "بوت تواصل";}
if($nu == 2){ $b= "بوت سايت";}
if($nu == 3){ $b= "بوت الازرار";}
if($nu == 4){ $b= "بوت المتجر";}
if($nu == 5){ $b= "بوت البصمات";}
if($nu == 6){ $b= "بوت ";}
if($nu == 7){ $b= "بوت التعليقات";}
if($nu == 8){ $b= "بوت ادارة منشور القنوات";}
if($nu == 9){ $b= "بوت الزخرفة";}
if($nu == 10){ $b= "بوت فيديوهات ستوري";}
if($nu == 11){ $b= "بوت تحليل الشخصية";}
if($nu == 12){ $b= "بوت العاب انلاين";}
if($nu == 13){ $b= "بوت تحويل الصيغ";}
if($nu == 14){ $b= "بوت إستخراج روابط القنوات";}
if($nu == 15){ $b= "بوت همسة";}
if($nu == 16){ $b= "بوت كشف الكذب";}
if($nu == 17){ $b= "حذف الحساب";}
if($nu == 18){ $b= "بوت الاعلانات";}
if($nu == 19){ $b= "بوت الاذكار";}
if($nu == 20){ $b= "بوت الاكواد الجاهزة";}
if($nu == 21){ $b= "بوت حساب العمر";}
if($nu == 22){ $b= "بوت تحميل انستقرام";}
if($nu == 23){ $b= "بوت البلورة السحرية";}
if($nu == 24){ $b= "بوت زخرفة تمبلر";}
if($nu == 25){ $b= "بوت مشاركة بدون توجية";}
if($nu == 26){ $b= "بوت حجر ورقة مقص";}
if($nu == 27){ $b= "بوت حماية القنوات";}
if($nu == 28){ $b= "بوت تمويل القنوات";}
if($nu == 29){ $b= "بوت نسبة الحب";}
if($nu == 30){ $b= "بوت دعم القنوات";}
if($nu == 31){ $b = "بوت نسبة الجمال";}
if($nu == 32){ $b = "بوت معاني الاسماء";}
if($nu == 33){ $b = "بوت تحميل من يوتيوب";}
if($nu == 34){ $b = "بوت تحميل من تيك توك";}
if($nu == 35){ $b = "بوت الاسئلة الجريئه";}
if($nu == 36){ $b = "بوت انشاء الفقرات";}
if($nu == 37){ $b = "بوت لايك";}
if($nu == 38){ $b = "بوت ردود";}
if($nu == 39){ $b = "بوت ادارة ماسح الخلفيات";}
if($nu == 40){ $b = "بوت صنع حزم ملصقات";}
if($nu == 41){ $b = "بوت تعديل حقوق الاغاني";}
if($nu == 42){ $b = "بوت الفلاتر";}
if($nu == 43){ $b = "بوت رشق تليكرام";}
if($nu == 44){ $b = "بوت رشق فيسبوك";}
if($nu == 45){ $b = "بوت رشق يوتيوب";}
if($nu == 46){ $b = "بوت رشق سناب جات";}
if($nu == 47){ $b = "بوت رشق انستكرام";}
if($nu == 48){ $b = "بوت شحن شدات ببجي";}
if($nu == 49){ $b = "بوت رشق تيك توك";}
if($nu == 50){ $b = "بوت مسح الميديا";}
if($nu == 51){ $b = "بوت نسبة الرجوله";}
if($nu == 52){ $b = "بوت نسبة المثلية";}
if($nu == 53){ $b = "بوت الابراج";}
if($nu == 54){ $b = "بوت حذف رسايل القناة";}
if($nu == 55){ $b = "بوت تصميم اجمل ثنائي";}
if($nu == 56){ $b = "بوت الخيره";}
if($nu == 57){ $b = "بوت كشف المستقبل";}
if($nu == 58){ $b = "بوت الصور و الاستوريات";}
if($nu == 59){ $b = "بوت باقات انترنت مجانية";}
if($nu == 60){ $b = "بوت تصاميم الصور";}
if($nu == 61){ $b = "بوت اهداء الاغاني";}
if($nu == 62){ $b = "بوت سمسمي";}
if($nu == 63){ $b = "بوت كت تويت";}
if($nu == 64){ $b = "بوت النودز";}
if($nu == 65){ $b = "بوت الازرار الشفافه";}
if($nu == 66){ $b = "بوت الفيزات";}
if($nu == 67){ $b = "بوت الخاص بالانستكرام";}
if($nu == 68){ $b = "بوت لعبة المحيبس";}
if($nu == 69){ $b = "بوت الالعاب";}

file_put_contents("from_id/$from_id/botmak.txt","mak$nu");
file_put_contents("from_id/$from_id/no3mak.txt","$b");
file_put_contents("from_id/$from_id/amr.txt","sn3free");
bot('editmessagetext',['chat_id'=>$chat_id,
'message_id'=>$message_id,
"text"=>"$token_kl : $b",
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>'• رجوع •','callback_data'=>"freebot"]],
]])]);}

mkdir("wataw");
if($text and $amrmem =="sn3free"){
file_put_contents("from_id/$from_id/amr.txt","");

$no3mak=file_get_contents("from_id/$from_id/no3mak.txt");
$botmak=file_get_contents("from_id/$from_id/botmak.txt");
$url = "https://api.telegram.org/bot".$text."/getWebhookInfo";
$check_token = json_decode(file_get_contents($url));
$check = $check_token;
$yes=$check->ok ;
bot('sendmessage',['chat_id'=>$chat_id,
'message_id'=>$message_id,
"text"=>"⏰ انتظر قليلا جارٍ فحص التوكن",]);

if($yes == "true"){
$url = "https://api.telegram.org/bot$text/getme";
$getidbots = json_decode(file_get_contents($url) , true);
$idbot = $getidbots['result']['id'];
$userbot = $getidbots['result']['username'];
$name1bot = $getidbots["result"]["first_name"];
$userbot=trim($userbot);
$idbot=trim($idbot);

mkdir("botmak");
mkdir("user");
mkdir("botmak/$idbot");

$botfree=explode("\n",file_get_contents("botfreeid.txt"));
$botfreeid=explode("\n",file_get_contents("infoidbots.txt"));
$botfrom=explode("\n",file_get_contents("from_id/$from_id/countbot.txt"));
$idbotfrom=explode("\n",file_get_contents("from_id/$from_id/idbot.txt"));
$infobots="$userbot==code#$userbot#$idbot";

# تخزين البوتات للعضو
if(!in_array($userbot,$botfrom )){
file_put_contents("from_id/$from_id/countuserbot.txt","@$userbot\n",FILE_APPEND);

file_put_contents("from_id/$from_id/countbot.txt",$userbot."\n",FILE_APPEND);}

if(!in_array($infobots,$idbotfrom )){
file_put_contents("from_id/$from_id/idbot.txt","$infobots\n",FILE_APPEND);}

if(!in_array($from_id,$botfree )){
file_put_contents("botfreeid.txt",$from_id."\n",FILE_APPEND);}

if(!in_array($idbot,$botfreeid )){
file_put_contents("infoidbots.txt",$idbot."\n",FILE_APPEND);}

file_put_contents("botmak/$idbot/admin.txt","$from_id");

$mak=file_get_contents("bots/mak.php");
$bot=file_get_contents("bots/$botmak.php");

$mak=str_replace("[*[TOKEN]*]","$text",$mak);
$mak=str_replace("[*[TOKENSAN3]*]","$token",$mak);

$bot=str_replace("<?php#*wataw*","$mak",$bot);
file_put_contents("botmak/$idbot/$userbot.php","$bot");

if($botmak=="mak28" ){
$bot=file_get_contents("bots/$botmak.php");

$bot=str_replace("[*[TOKEN]*]","$text",$bot);
$bot=str_replace("[*[TOKENSAN3]*]","$token",$bot);

file_put_contents("botmak/$idbot/$userbot.php","$bot");}

if( $botmak=="mak6" ){
$bot=file_get_contents("bots/$botmak.php");

$bot=str_replace("[*[TOKENBOT]*]","$text",$bot);
$bot=str_replace("[*[TOKENSAN3BOT]*]","$token",$bot);

file_put_contents("botmak/$idbot/$userbot.php","$bot");

#$wjson=file_get_contents("botmak/wataw.json");
#mkdir("botmak/$idbot/botmak");
#file_put_contents("botmak/$idbot/watawsudo.json",$wjson);
}
file_get_contents("https://api.telegram.org/bot".$text."/setwebhook?url=".$folder."/botmak/".$idbot."/$userbot.php");
file_put_contents("botmak/$idbot/info.txt","-- محمي --\n$userbot\n$name1bot\n$from_id\n$idbot\n$botmak\n$no3mak");
$propots=$infosudo["info"]["propots"];
file_put_contents("user/$userbot.txt","$idbot");
file_put_contents("wataw/$idbot.php",'<?php '."\n".'$tokenbot= "'.$text.'";');
bot('editmessagetext',['chat_id'=>$chat_id,
'message_id'=>$message_id+1,
"text"=>"مبروك تم صنع بوتك بنجاح
- معرف البوت : @$userbot
- نوع البوت: $no3mak
",
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"اضغط هنا للدخول 📌",'url'=>"https://t.me/$userbot?start"]],
[['text'=>'• رجوع •','callback_data'=>"freebot"]],
]])]);
bot('sendmessage',['chat_id'=>$ameed,
'message_id'=>$message_id,
"text"=>"
تم صنع بوت مجاني بنجاح ✅
النوع : $no3mak
توكن : `$text`
يوزر البوت : `$userbot@`
namebot : `$name1bot`
idbot : `$idbot`
معلومات صاحب البوت 🙎 
الاسم : *$name*
الايدي : `$from_id`
[$name](tg://user?id=$from_id)",
'parse_mode'=>"MarkDown",
'disable_web_page_preview'=>true,
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"اضغط هنا للدخول 📌",'url'=>"https://t.me/$userbot?start"]],
[['text'=>'• رجوع •','callback_data'=>"freebot"]],
]])]);
}else{
bot('editmessagetext',['chat_id'=>$chat_id,
'message_id'=>$message_id+1,
"text"=>"🚫 هناك خطاء التوكن الذي قمت بارسالة خاطئ قم بالارسال مره اخرى",
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>'حاول مرةً اخرئ.','callback_data'=>"sn3botfre"]],
]])]);}}

$botfree=explode("\n",file_get_contents("botfreeid.txt"));
$botf=file_get_contents("from_id/$from_id/countuserbot.txt");
if($data=="botsmember"){
if(in_array($from_id, $botfree) and $botf != "" and $botf != " " and $botf!= null){
$idbotfrom=explode("\n",file_get_contents("from_id/$from_id/idbot.txt"));

$keyboard["inline_keyboard"]=[];
for ($i=0; $i < count($idbotfrom); $i++) { 
$ex = explode("#", $idbotfrom[$i]);
$idbot=$ex['2'];
$userbot="@".$ex['1'];
$in="infobot ".$ex['1'];
$number = strlen($idbot);

$infobot=explode("\n",file_get_contents("botmak/$idbot/info.txt"));
$userbott=$infobot['1'];
$namebot=$infobot['2'];
$id=$infobot['3'];
$idbots=$infobot['4'];
$no3mak=$infobot['6'];

if($number > 4){
$keyboard["inline_keyboard"][$i] = [['text'=>$userbot,'url'=>"t.me/$userbott"],
['text'=>$no3mak,'url'=>"t.me/$userbott"],['text'=>'معلومات اكثر ','callback_data'=>$in]];
}}
$keyboard["inline_keyboard"][] = [['text'=>"- إذاعة لكل البوتات ",'callback_data'=>"sendpostbotsall"]];
$keyboard["inline_keyboard"][] = [['text'=>"• رجوع • ",'callback_data'=>"freebot"]];	$reply_markup=json_encode($keyboard);
unlink("from_id/$from_id/yes.txt");
bot('editmessagetext',['chat_id'=>$chat_id,
'message_id'=>$message_id,
'text'=>"
• هذه هي قائمة بوتاتك المصنوعة ..
$infobotsmember
- قم بالضغط على معلومات اكثر لعرض معلوماتة وامكانية التعديل علية.
",
"message_id"=>$message_id,
'reply_markup'=>$reply_markup]);
}else{
bot('editmessagetext',['chat_id'=>$chat_id,
'message_id'=>$message_id,
"text"=>"❌ عذراً لم تقم بصنع بوت من قبل ",
'reply_markup'=>json_encode([ 'inline_keyboard'=>[
[['text'=>'• رجوع •','callback_data'=>"freebot"]],
]])]);}}

if(preg_match('/^(infobot) (.*)/s', $data)){
$userbot = str_replace('infobot ',"",$data);
$userbot = str_replace(' ',"",$userbot);
$idbots=file_get_contents("user/$userbot.txt");
$infobot=explode("\n",file_get_contents("botmak/$idbots/info.txt"));
$userbot=$infobot['1'];
$namebot=$infobot['2'];
$id=$infobot['3'];
$idbots=$infobot['4'];
$no3mak=$infobot['6'];
include("wataw/$idbots.php");
$tokenboot="$tokenbot";
$mm=explode("\n",file_get_contents("botmak/$idbots/sudo/member.txt"));
$co=count($mm)-1;
bot('editmessagetext',['chat_id'=>$chat_id,
'message_id'=>$message_id,
"text"=>"معلومات البوت المصنوع  : `$use`
النوع : $no3mak
ايدي البوت : `$idbots`
اسم البوت : `$namebot`
التوكن : `$tokenboot`
• عدد الاعضاء المشتركين في البوت : $co
",
'reply_to_message_id'=>$message->message_id,
'parse_mode'=>"MarkDown",
'disable_web_page_preview'=>true,
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>'🚫 حذف','callback_data'=>"deletebot ".$userbot]],
[['text'=>'♻ تغيير النوع','callback_data'=>"naglbot ".$userbot],['text'=>'','callback_data'=>"naglbotmember ".$userbot]],
[['text'=>'• رجوع •','callback_data'=>"botsmember"]],
]])]);}

if(preg_match('/^(deletebot) (.*)/s', $data)){
$userbot = str_replace('deletebot ',"",$data);
$userbot = str_replace(' ',"",$userbot);

$idbots=file_get_contents("user/$userbot.txt");
$botfrom=explode("\n",file_get_contents("from_id/$from_id/countbot.txt"));
			
if(in_array($userbot,$botfrom ) and $idbots!=null){
$infobot=explode("\n",file_get_contents("botmak/$idbots/info.txt"));
$tokenbot=$infobot['0'];
$namebot=$infobot['2'];
$id=$infobot['3'];
$idbots=$infobot['4'];
$no3mak=$infobot['6'];
#حذف البوت 
$us=file_get_contents("from_id/$from_id/countbot.txt");
$us=str_replace("$userbot\n","",$us);
file_put_contents("from_id/$from_id/countbot.txt",$us);

#حذف ازرار
$ussss="$userbot==code#$userbot#$idbots";
$uss=file_get_contents("from_id/$from_id/idbot.txt");
$uss=str_replace("$ussss\n","",$uss);
file_put_contents("from_id/$from_id/idbot.txt",$uss);

unlink("botmak/user/$userbot.txt");

unlink("botmak/$idbots/$userbot.php");

$us2="@$userbot";
$us1=file_get_contents("from_id/$from_id/countuserbot.txt");
$us1=str_replace("$us2\n","",$us1);
file_put_contents("from_id/$from_id/countuserbot.txt",$us1);
$us11=file_get_contents("infoidbots.txt");
$us11=str_replace("$idbots\n","",$us11);
file_put_contents("infoidbots.txt",$us11);
if(is_dir("botmak/$idbots")){

remove_dir("botmak/$idbots");}

include("wataw/$idbots.php");

$tokenboot="$tokenbot";
bot('editmessagetext',['chat_id'=>$chat_id,
'message_id'=>$message_id,
'text'=>"✅ تم حذف البوت بنجاح",
'disable_web_page_preview'=>'true',
'parse_mode'=>"MarkDown",
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>'• رجوع •','callback_data'=>"botsmember"]],
]])]);
bot('sendmessage',['chat_id'=>$ameed,
'message_id'=>$message_id,
"text"=>"تم حذف بوت مجاني  ❌ 
النوع : $no3mak
توكن : `$tokenboot`
يوزر البوت : *@$userbot*
ايدي البوت : `$idbot`
معلومات صاحب البوت
الاسم : *$name*
الايدي : `$from_id`
[$name](tg://user?id=$from_id)",
'parse_mode'=>"MarkDown",
'disable_web_page_preview'=>true,
]);}}

if(preg_match('/^(naglbot) (.*)/s', $data)){
$userbot = str_replace('naglbot ',"",$data);
$userbot = str_replace(' ',"",$userbot);
if($userbot!=null){
file_put_contents("from_id/$from_id/nagl.txt","$userbot");
bot('editmessagetext',['chat_id'=>$chat_id,
'message_id'=>$message_id,
"text"=>"أهلاً بك عزيزي :
- قم بأختيار نوع البوت الذي تريد نقل @$userbot اليه..
",
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>'• قائمة البوتات •','callback_data'=>"null"]],
[['text'=>'','callback_data'=>"ngl 6"],['text'=>'بوت سايت','callback_data'=>"ngl 2"]],
[['text'=>'بوت الازرار','callback_data'=>"ngl 3"],['text'=>'بوت المتجر','callback_data'=>"ngl 4"]],
[['text'=>'بوت البصمات','callback_data'=>"ngl 5"],['text'=>'بوت تواصل','callback_data'=>"ngl 1"]],
[['text'=>'بوت التعليقات','callback_data'=>"ngl 7"],['text'=>'ادارة منشورات القناة','callback_data'=>"ngl 8"]],
[['text'=>'بوت زخرفة','callback_data'=>"ngl 9"],['text'=>'فيديوهات ستوري','callback_data'=>"ngl 10"]],
[['text'=>'تحليل الشخصية','callback_data'=>"ngl 11"],['text'=>'العاب انلاين','callback_data'=>"ngl 12"]],
[['text'=>'تحويل الصيغ','callback_data'=>"ngl 13"],['text'=>'إستخراج روابط القنوات','callback_data'=>"ngl 14"]],
[['text'=>'بوت همسة','callback_data'=>"ngl 15"],['text'=>'بوت كشف الكذب','callback_data'=>"ngl 16"]],
[['text'=>'حذف الحساب','callback_data'=>"ngl 17"],['text'=>'بوت الاعلانات','callback_data'=>"ngl 18"]],
[['text'=>'بوت الاذكار','callback_data'=>"ngl 19"],['text'=>'الاكواد الجاهزة','callback_data'=>"ngl 20"]],
[['text'=>'حساب العمر','callback_data'=>"ngl 21"],['text'=>'ستوري انستكرام','callback_data'=>"ngl 22"]],
[['text'=>'البلورة السحرية','callback_data'=>"ngl 23"],['text'=>'زخرفة تمبلر','callback_data'=>"ngl 24"]],
[['text'=>'مشاركة بدون توجية','callback_data'=>"ngl 25"],['text'=>'حجر ورقة مقص','callback_data'=>"ngl 26"]],
[['text'=>'حماية القنوات','callback_data'=>"ngl 27"],['text'=>'تمويل القنوات','callback_data'=>"ngl 28"]],
[['text'=>'نسبة الحب','callback_data'=>"ngl 29"],['text'=>'دعم القنوات','callback_data'=>"ngl 30"]],
[['text'=>'نسبة الجمال','callback_data'=>"ngl 31"],['text'=>'معاني الاسماء','callback_data'=>"ngl 32"]],
[['text'=>'يوتيوب','callback_data'=>"ngl 33"],['text'=>'تيك توك','callback_data'=>"ngl 34"]],
[['text'=>'اسئلة جريئه','callback_data'=>"ngl 35"],['text'=>'الفقرات','callback_data'=>"ngl 36"]],
[['text'=>'','callback_data'=>"ngl 37"],['text'=>'بوت ردود','callback_data'=>"ngl 38"]],
[['text'=>'ماسح الخلفيات','callback_data'=>"ngl 39"],['text'=>'ملصقات','callback_data'=>"ngl 40"]],
[['text'=>'تعديل حقوق الاغاني','callback_data'=>"ngl 41"],['text'=>'الفلاتر','callback_data'=>"ngl 42"]],
[['text'=>'مسح الميديا','callback_data'=>"ngl 50"]],
[['text'=>'نسبة الرجوله','callback_data'=>"ngl 51"],['text'=>'نسبة المثلية','callback_data'=>"ngl 52"]],
[['text'=>'الابراج','callback_data'=>"ngl 53"],['text'=>'حذف رسايل القنوات','callback_data'=>"ngl 54"]],
[['text'=>'تصميم اجمل ثنائي','callback_data'=>"ngl 55"],['text'=>'خيره','callback_data'=>"ngl 56"]],
[['text'=>'كشف المستقبل','callback_data'=>"ngl 57"],['text'=>'ستوريات وصور','callback_data'=>"ngl 58"]],
[['text'=>'باقات انترنت','callback_data'=>"ngl 59"],['text'=>'تصاميم صور','callback_data'=>"ngl 60"]],
[['text'=>'اهداء الاغاني','callback_data'=>"ngl 61"],['text'=>'سمسمي','callback_data'=>"ngl 62"]],
[['text'=>'كت تويت','callback_data'=>"ngl 63"],['text'=>'نودز','callback_data'=>"ngl 64"]],
[['text'=>'ازرار شفافه','callback_data'=>"ngl 65"],['text'=>'فيزه','callback_data'=>"ngl 66"]],
[['text'=>'خاص بالانستكرام','callback_data'=>"ngl 67"],['text'=>'لعبة محيبس','callback_data'=>"ngl 68"]],
[['text'=>'الالعاب','callback_data'=>"ngl 69"]],
[['text'=>'• رجوع •','callback_data'=>"botsmember"]],
]])]);}}

if(preg_match('/^(ngl) (.*)/s', $data)){
$nu = str_replace('ngl ',"",$data);
if($nu == 1){ $b= "بوت تواصل";}
if($nu == 2){ $b= "بوت سايت";}
if($nu == 3){ $b= "بوت الازرار";}
if($nu == 4){ $b= "بوت المتجر";}
if($nu == 5){ $b= "بوت البصمات";}
if($nu == 6){ $b= "بوت ";}
if($nu == 7){ $b= "بوت التعليقات";}
if($nu == 8){ $b= "بوت ادارة منشور القنوات";}
if($nu == 9){ $b= "بوت الزخرفة";}
if($nu == 10){ $b= "بوت فيديوهات ستوري";}
if($nu == 11){ $b= "بوت تحليل الشخصية";}
if($nu == 12){ $b= "بوت العاب انلاين";}
if($nu == 13){ $b= "بوت تحويل الصيغ";}
if($nu == 14){ $b= "بوت إستخراج روابط القنوات";}
if($nu == 15){ $b= "بوت همسة";}
if($nu == 16){ $b= "بوت كشف الكذب";}
if($nu == 17){ $b= "بوت حذف الحساب";}
if($nu == 18){ $b= "بوت الاعلانات";}
if($nu == 19){ $b= "بوت الاذكار";}
if($nu == 20){ $b= "بوت الاكواد الجاهزة";}
if($nu == 21){ $b= "بوت حساب العمر";}
if($nu == 22){ $b= "بوت تحميل انستقرام";}
if($nu == 23){ $b= "بوت البلورة السحرية";}
if($nu == 24){ $b= "بوت زخرفة تمبلر";}
if($nu == 25){ $b= "بوت مشاركة بدون توجية";}
if($nu == 26){ $b= "بوت حجر ورقة مقص";}
if($nu == 27){ $b= "بوت حماية القنوات";}
if($nu == 28){ $b= "بوت تمويل القنوات";}
if($nu == 29){ $b= "بوت نسبة الحب";}
if($nu == 30){ $b= "بوت دعم القنوات";}
if($nu == 31){ $b = "بوت نسبة الجمال";}
if($nu == 32){ $b = "بوت معاني الاسماء";}
if($nu == 33){ $b = "بوت تحميل من يوتيوب";}
if($nu == 34){ $b = "بوت تحميل من تيك توك";}
if($nu == 35){ $b = "بوت الاسئلة الجريئه";}
if($nu == 36){ $b = "بوت انشاء الفقرات";}
if($nu == 37){ $b = "بوت لايك";}
if($nu == 38){ $b = "بوت ردود";}
if($nu == 39){ $b = "بوت ادارة ماسح الخلفيات";}
if($nu == 40){ $b = "بوت صنع حزم ملصقات";}
if($nu == 41){ $b = "بوت تعديل حقوق الاغاني";}
if($nu == 42){ $b = "بوت الفلاتر";}
if($nu == 43){ $b = "بوت رشق تليكرام";}
if($nu == 44){ $b = "بوت رشق فيسبوك";}
if($nu == 45){ $b = "بوت رشق يوتيوب";}
if($nu == 46){ $b = "بوت رشق سناب جات";}
if($nu == 47){ $b = "بوت رشق انستكرام";}
if($nu == 48){ $b = "بوت شحن شدات ببجي";}
if($nu == 49){ $b = "بوت رشق تيك توك";}
if($nu == 50){ $b = "بوت مسح الميديا";}
if($nu == 51){ $b = "بوت نسبة الرجوله";}
if($nu == 52){ $b = "بوت نسبة المثلية";}
if($nu == 53){ $b = "بوت الابراج";}
if($nu == 54){ $b = "بوت حذف رسايل القناة";}
if($nu == 55){ $b = "بوت تصميم اجمل ثنائي";}
if($nu == 56){ $b = "بوت الخيره";}
if($nu == 57){ $b = "بوت كشف المستقبل";}
if($nu == 58){ $b = "بوت الصور و الاستوريات";}
if($nu == 59){ $b = "بوت باقات انترنت مجانية";}
if($nu == 60){ $b = "بوت تصاميم الصور";}
if($nu == 61){ $b = "بوت اهداء الاغاني";}
if($nu == 62){ $b = "بوت سمسمي";}
if($nu == 63){ $b = "بوت كت تويت";}
if($nu == 64){ $b = "بوت النودز";}
if($nu == 65){ $b = "بوت الازرار الشفافه";}
if($nu == 66){ $b = "بوت الفيزات";}
if($nu == 67){ $b = "بوت الخاص بالانستكرام";}
if($nu == 68){ $b = "بوت لعبة المحيبس";}
if($nu == 69){ $b = "بوت الالعاب";}

$ngluser=file_get_contents("from_id/$from_id/nagl.txt");
unlink("from_id/$from_id/nagl.txt");
$idbots=file_get_contents("user/$ngluser.txt");

include("wataw/$idbots.php");
$tokenboot="$tokenbot";
$url = "https://api.telegram.org/bot".$tokenboot."/getWebhookInfo";
$check_token = json_decode(file_get_contents($url));
$check = $check_token;
$yes=$check->ok ;

if($yes == "true"){
$url = "https://api.telegram.org/bot$tokenboot/getme";
$getidbots = json_decode(file_get_contents($url) , true);
$idbot = $getidbots['result']['id'];
$userbot = $getidbots['result']['username'];
$name1bot = $getidbots["result"]["first_name"];
$userbot=trim($userbot);
$idbot=trim($idbot);
if(is_dir("botmak/$idbot")){

remove_dir("botmak/$idbot");}

mkdir("botmak");
mkdir("user");
mkdir("botmak/$idbot");

$mak=file_get_contents("bots/mak.php");
$bot=file_get_contents("bots/mak$nu.php");
$mak=str_replace("[*[TOKEN]*]","$tokenboot",$mak);
$mak=str_replace("[*[TOKENSAN3]*]","$token",$mak);
$bot=str_replace("<?php#*wataw*","$mak",$bot);
file_put_contents("botmak/$idbot/$userbot.php","$bot");

if($nu == "28" ){
$bot=file_get_contents("bots/mak$nu.php");
$bot=str_replace("[*[TOKEN]*]","$tokenboot",$bot);
$bot=str_replace("[*[TOKENSAN3]*]","$token",$bot);
file_put_contents("botmak/$idbot/$userbot.php","$bot");}

if( $nu == "6" ){
$bot=file_get_contents("bots/mak$nu.php");
$bot=str_replace("[*[TOKENBOT]*]","$tokenboot",$bot);
$bot=str_replace("[*[TOKENSAN3BOT]*]","$token",$bot);
file_put_contents("botmak/$idbot/$userbot.php","$bot");}
file_put_contents("botmak/$idbot/admin.txt","$from_id");
file_get_contents("https://api.telegram.org/bot".$tokenboot."/setwebhook?url=".$folder."/botmak/".$idbot."/$userbot.php");
file_put_contents("botmak/$idbot/info.txt","-- محمي --\n$userbot\n$name1bot\n$from_id\n$idbot\nmak$nu\n$b");

$iin = "infobot ".$userbot;
bot('editmessagetext',['chat_id'=>$chat_id,
'message_id'=>$message_id,
"text"=>"
- تم تتغير نوع بوتك بنجاح.
- معرف البوت : @$userbot.
النوع : $b
",
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"اضغط هنا للدخول 📌",'url'=>"https://t.me/$userbot?start"]],
[['text'=>'• رجوع •','callback_data'=>"freebot"]],
]])]);

bot('sendmessage',['chat_id'=>$ameed,
'message_id'=>$message_id,
"text"=>"👮|  تم تغيير نوع بوت مجاني بنجاح ✅ 
معلومات البوت: 
النوع : $b
توكن : *$tokenbot*
يوزر البوت : `@$userbot`
namebot : *$name1bot*
idbot : $idbot
معلومات صاحب البوت:
الاسم : *$name*
الايدي : $from_id
[$name](tg://user?id=$from_id)",
'parse_mode'=>"MarkDown",
'disable_web_page_preview'=>true,
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>"اضغط هنا للدخول 📌",'url'=>"https://t.me/$userbot?start"]],
[['text'=>'• رجوع •','callback_data'=>"freebot"]],
]])]);
}else{
bot('editmessagetext',['chat_id'=>$chat_id,
'message_id'=>$message_id,
"text"=>"🚫 هناك خطاء التوكن المخزن في البيانات الخاصة بك قد تم تحديثه قم بإعادة صنع البوت من جديد بالتوكن الجديد",
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>'حاول مرةً اخرئ.','callback_data'=>"botsmember"]],
]])]);}}

$codejson = json_decode(file_get_contents("code.json"),true);
if (!file_exists("code.json")) {
$put = [];
file_put_contents("code.json", json_encode($put));}

if(preg_match('/^(naglbotmember) (.*)/s', $data)){
$userbotfree = str_replace('naglbotmember ',"",$data);
$code = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), -35);
$idbots=file_get_contents("user/$userbotfree.txt");
$infobot=explode("\n",file_get_contents("botmak/$idbots/info.txt"));
$userbot=$infobot['1'];
$id=$infobot['3'];
$no3mak=$infobot['6'];
$codejson["info"][$code]["st"]="yes";
$codejson["info"][$code]["idbot"]="$idbots";
$codejson["info"][$code]["userbot"]="$userbot";
$codejson["info"][$code]["admin"]="$id";

file_put_contents("code.json", json_encode($codejson));
bot('sendmessage',['chat_id'=>$chat_id,
"text"=>"• هذا هو كود النقل الخاص ببوتك 
https://t.me/$user_bot_sudo?start=$code
- قم بارسالة للشخص الذي تريد نقل البوت اليه.
تحذير:
عند نقل البوت لشخص اخر ستفقد امكانية التحكم في البوت .
",
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>'• رجوع •','callback_data'=>"botsmember"]],
]])]);}

if(preg_match('/^\/([Ss]tart) (.*)/',$text)){
preg_match('/^\/([Ss]tart) (.*)/',$text,$match);
$code=$match[2];
$codejson = json_decode(file_get_contents("code.json"),true);
$st=$codejson["info"][$code]["st"];
$idbots=$codejson["info"][$code]["idbot"];
$userbots=$codejson["info"][$code]["userbot"];
$admin=$codejson["info"][$code]["admin"];
$idbots=file_get_contents("user/$userbots.txt");
$botfrom=explode("\n",file_get_contents("from_id/$admin/countbot.txt"));
if($admin!=$from_id){
if($st=="yes" and $admin!=null){	
if(in_array($userbots,$botfrom ) and $idbots!=null){

$infobot=explode("\n",file_get_contents("botmak/$idbots/info.txt"));
$tokenboot=$infobot['0'];
$userbot=$infobot['1'];
$namebot=$infobot['2'];
$id=$infobot['3'];
$idbots=$infobot['4'];
$no3mak=$infobot['6'];
#حذف البوت 
$us=file_get_contents("from_id/$admin/countbot.txt");
$us=str_replace("$userbot\n","",$us);
file_put_contents("from_id/$admin/countbot.txt",$us);

#حذف ازرار
$ussss="$userbot==code#$userbot#$idbots";
$uss=file_get_contents("from_id/$admin/idbot.txt");
$uss=str_replace("$ussss\n","",$uss);
file_put_contents("from_id/$admin/idbot.txt",$uss);

$us2="@$userbot";
$us1=file_get_contents("from_id/$admin/countuserbot.txt");
$us1=str_replace("$us2\n","",$us1);
file_put_contents("from_id/$admin/countuserbot.txt",$us1);

$us5=file_get_contents("botmak/$idbots/info.txt");
$us5=str_replace("$admin","$from_id",$us5);
file_put_contents("botmak/$idbots/info.txt",$us5);

# تخزين البوتات للعضو

file_put_contents("from_id/$from_id/countuserbot.txt","@$userbot\n",FILE_APPEND);
file_put_contents("from_id/$from_id/countbot.txt",$userbot."\n",FILE_APPEND);
#bots

$idbotfrom=explode("\n",file_get_contents("from_id/$from_id/idbot.txt"));
if(!in_array($ussss,$idbotfrom )){
file_put_contents("from_id/$from_id/idbot.txt","$ussss\n",FILE_APPEND);}

$botfree=explode("\n",file_get_contents("botfreeid.txt"));
if(!in_array($from_id,$botfree )){
file_put_contents("botfreeid.txt",$from_id."\n",FILE_APPEND);}

file_put_contents("botmak/$idbots/admin.txt","$from_id");
$us6=file_get_contents("botmak/$idbots/sudo.json");
$us6=str_replace("$admin","$from_id",$us6);
file_put_contents("botmak/$idbots/sudo.json",$us6);

#unlink("botmak/$idbots/sudo.json");

$mm=explode("\n",file_get_contents("botmak/$idbots/sudo/member.txt"));
$co=count($mm)-1;
bot('sendmessage',['chat_id'=>$chat_id,
"text"=>"✅ تم نقل البوت اليك بنجاح 
بيانات البوت
- اسم البوت : $namebot
- معرف البوت : @$userbots
- نوع البوت : $no3mak
- توكن البوت : $tokenboot
- عدد الاعضاء المشتركين في البوت : $co
",]);
bot('sendmessage',['chat_id'=>$ameed,
"text"=>"تم نقل [بوت](t.me/$userbot) الى [$from_id](tg://user?id=$from_id)",
'parse_mode'=>"MarkDown",]);
unset($codejson["info"][$code]);
file_put_contents("code.json", json_encode($codejson));}
}else{
bot('sendmessage',['chat_id'=>$chat_id,
"text"=>"ارسال /start .!",
'reply_to_message_id'=>$message_id,
]);}
}else{
bot('sendmessage',['chat_id'=>$chat_id,
"text"=>"لايمكنك نقل البوت لنفسك",
'reply_to_message_id'=>$message_id,
]);}}

$datatime = json_decode(
    file_get_contents(__DIR__ . '/datatime.json'),
    true
);

if (!is_array($datatime)) {
    $datatime = [];
}

if (!isset($datatime['info']) || !is_array($datatime['info'])) {
    $datatime['info'] = [];
}

$datatimesend = $from_id !== null
    ? ($datatime['info'][$from_id]['date'] ?? null)
    : null;

if($data=="sendpostbotsall"){
$timeuoto=time()+(3600 * 1);
$day = date('Y-m-d',$timeuoto);
if($day!=$datatimesend){
$datatime["info"][$from_id]["date"]="$day";
file_put_contents("datatime.json", json_encode($datatime));
file_put_contents("from_id/$from_id/amr.txt","sendpostbotsall");
bot('editmessagetext',['chat_id'=>$chat_id,
'message_id'=>$message_id,
"text"=>" حسنا قم بارسال النص لكي يتم 
ارسال الاذاعة في هذة البوتات

$infobotsmember",
'disable_web_page_preview'=>true,
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>'• رجوع •','callback_data'=>"botsmember"]],
]])]);}
else{
bot('answercallbackquery',[
'callback_query_id'=>$up->id,
"text"=>"🚫 عذراً لاتستطيع عمل الاذاعة لكل بوتاتك المصنوعة اكثر من مرة واحدة فقط في اليوم $day 
- ستتمكن من نشر الاذاعة غداً
-  يمكنك عمل الاذاعة من داخل بوتاتك المصنوعة
",'show_alert'=>true,]);}}

if($text and $amrmem =="sendpostbotsall"){
file_put_contents("from_id/$from_id/amr.txt","");
bot('sendmessage',['chat_id'=>$chat_id,
"text"=>"جارٍ عمل الاذاعة",
'reply_to_message_id'=>$message_id,]);
$bots=explode("\n",file_get_contents("from_id/$from_id/countbot.txt"));
$coo=count($bots)-1;
for ($i=0; $i < count($bots); $i++) { 
$userbots=$bots[$i];
$idbots=file_get_contents("user/$userbots.txt");

include("wataw/$idbots.php");

$tokenboot="$tokenbot";
$mm=explode("\n",file_get_contents("botmak/$idbots/sudo/member.txt"));
for ($l=0; $l < count($mm); $l++) {
$id=$mm[$l];
file_get_contents("https://api.telegram.org/bot".$tokenboot."/sendmessage?chat_id=".$id."&text=$text");}
$co=$co+$l-1;}
bot('sendmessage',['chat_id'=>$chat_id,
"text"=>"- تمت الاذاعة في جميع البوتات المصنوعة 
- تم الارسال الى $co مستخدم.
- عدد البوتات : $coo
",
'reply_to_message_id'=>$message_id,
]);}

if($data=="uplode"){
file_put_contents("from_id/$from_id/amr.txt","uplode");
bot('editmessagetext',['chat_id'=>$chat_id,
'message_id'=>$message_id,
"text"=>" اهلا [$name](tg://user?id=$chat_id)
*• أرسل ملف php الآن !*
• بشروط :

1. لا يحتوي الملف على أي أخطاء برمجيه
2. يجب أن يكون عمله دون الحاجة إلى اتصال بخدمات خارجية (api)
3. لا يحتوي على معرف لقناة أو مطور ( يتم وضع معرف المطور عند صنع البوت من قبل المستخدم )
",
'parse_mode'=>"MarkDown",
'disable_web_page_preview'=>true,
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>'إلغاء الارسال','callback_data'=>"freebot"]],
]])]);}

$tw_sudo = $infosudo['info']['sudo'] ?? '';
if($message and $amrmem =="uplode" and !$data){
if( $update->message->document ){
file_put_contents("from_id/$from_id/amr.txt","");
$file_id = $update->message->document->file_id;
bot('senddocument',["chat_id"=>"$ameed",
"document"=>"$file_id",]);
bot('sendmessage',["chat_id"=>"$ameed",
'message_id'=>$message_id,
"text"=>"- طلب ارسال ملف جديد
معلومات المرسل:
الاسم : *$name*
الايدي : $from_id
[$name](tg://user?id=$from_id)",
'parse_mode'=>"MarkDown",
'disable_web_page_preview'=>true,]);
bot('sendmessage',["chat_id"=>"$chat_id",
'message_id'=>$message_id,
"text"=>"✅ تم الارسال الى: [📣 مطور البوت .](https://t.me/ريو)",
'parse_mode'=>"MarkDown",]);
}else{
bot('sendmessage',["chat_id"=>"$chat_id",
'message_id'=>$message_id,
"text"=>"🚫 قم بإرسال الملفات فقط",
'reply_markup'=>json_encode(['inline_keyboard'=>[
[['text'=>'إلغاء الارسال','callback_data'=>"freebot"]],
]])]);}}





//----------------------------------------------------------

// قسم النسخ الاحتياطي واستعادة البيانات المحدث والمحمي بالكامل
if($data == "BackRest"){
    bot('answercallbackquery',[
        'callback_query_id'=>$update->callback_query->id,
        'text'=>"جاري فتح قائمة النسخ الاحتياطي...",
        'show_alert'=>false
    ]);

    bot('EditMessageText',[
        'chat_id'=>$chat_id,
        'message_id'=>$message_id,
        "text"=>"
- أهلاً بك عزيزي المطور في قسم النسخ والاستعادة 📦.
من هنا يمكنك حماية بيانات بوتاتك ومستخدميك من الضياع عند تحديث الاستضافة.

• اختر الإجراء المناسب:
",
        'parse_mode'=>"markdown",
        'reply_markup'=>json_encode(['inline_keyboard'=>[
            [
                ['text'=>'تحميل نسخة احتياطية (Backup) 📥', 'callback_data'=>'get_backup']
            ],
            [
                ['text'=>'استعادة النسخة (Restore) 📤', 'callback_data'=>'set_backup']
            ],
            [
                ['text'=>'• رجوع •', 'callback_data'=>'home']
            ],
        ]])
    ]);
}




if (isset($data) && $data === "get_backup") {
    $callback_from_id = (string)(
        $update->callback_query->from->id ?? ''
    );

    if (
        $callback_from_id !== (string)$ameed
        && !in_array(
            $callback_from_id,
            array_map('strval', $sudo),
            true
        )
    ) {
        bot('answercallbackquery', [
            'callback_query_id' => $update->callback_query->id,
            'text' => 'غير مصرح لك',
            'show_alert' => true
        ]);
        return;
    }

    bot('answercallbackquery', [
        'callback_query_id' => $update->callback_query->id,
        'text' => 'جاري إنشاء النسخة الاحتياطية...',
        'show_alert' => false
    ]);

    create_factory_backup($chat_id);
    return;
}








if (isset($data) && $data === "set_backup") {
    if (!in_array(
        (string)$from_id,
        array_map('strval', $sudo),
        true
    )) {
        bot('answercallbackquery', [
            'callback_query_id' => $update->callback_query->id,
            'text' => 'غير مصرح لك',
            'show_alert' => true
        ]);
        return;
    }

    bot('answercallbackquery', [
        'callback_query_id' => $update->callback_query->id,
        'text' => 'أرسل ملفات النسخة الاحتياطية',
        'show_alert' => false
    ]);

    backup_send_message(
        $chat_id,
        "📤 **بدء الاستعادة**\n\n"
        . "1. أرسل جميع ملفات النسخة الاحتياطية كما وصلتك، مع أوصافها الأصلية.\n"
        . "2. لا تعدّل أسماء الملفات أو أوصافها.\n"
        . "3. أرسل ملف manifest.json أخيرًا.\n"
        . "4. لن يبدأ الدمج قبل التحقق من الفهرس وجميع الملفات.\n\n"
        . "⚠️ احتفظ بنسخة من بيانات الاستضافة الحالية قبل المتابعة."
    );

    return;
}





/* ==========================================
   النسخ الاحتياطي المشفر - داخل rido.php
   لا يحتاج إلى factory_backup_restore.php
========================================== */

function backup_send_message($chat_id, $text)
{
    return bot('sendMessage', [
        'chat_id' => $chat_id,
        'text' => $text
    ]);
}

function backup_encryption_key()
{
    $encoded = getenv('BACKUP_ENCRYPTION_KEY');
    $key = is_string($encoded)
        ? base64_decode($encoded, true)
        : false;

    if (
        !is_string($key) ||
        strlen($key) !== SODIUM_CRYPTO_SECRETBOX_KEYBYTES
    ) {
        throw new RuntimeException(
            'مفتاح النسخ الاحتياطي غير صالح.'
        );
    }

    return $key;
}

function backup_encrypt_data($data)
{
    $key = backup_encryption_key();

    $nonce = random_bytes(
        SODIUM_CRYPTO_SECRETBOX_NONCEBYTES
    );

    return 'FBK1'
        . $nonce
        . sodium_crypto_secretbox($data, $nonce, $key);
}

function backup_encode_path($path)
{
    return rtrim(
        strtr(base64_encode($path), '+/', '-_'),
        '='
    );
}


function backup_allowed_path($relative)
{
    if (!is_string($relative)) {
        return false;
    }

    $relative = str_replace('\\', '/', $relative);

    if (
        $relative === '' ||
        $relative[0] === '/' ||
        strpos($relative, "\0") !== false ||
        preg_match('/^[a-zA-Z]:/', $relative) ||
        preg_match('/[\x00-\x1F\x7F]/', $relative)
    ) {
        return false;
    }

    foreach (explode('/', $relative) as $part) {
        if (
            $part === '' ||
            $part === '.' ||
            $part === '..'
        ) {
            return false;
        }
    }

    $root_files = [
        'admin.txt',
        'info.txt',
        'infoidbots.txt',
        'botfreeid.txt',
        'code.json',
        'datatime.json',
        'prodate.json',
        'sudo.json',
        'no3send.txt',
        'chatsend.txt',
        'get.txt'
    ];

    if (in_array($relative, $root_files, true)) {
        return true;
    }

    $first = explode('/', $relative, 2)[0];

    if (!in_array(
        $first,
        ['sudo', 'data', 'user', 'from_id', 'botmak', 'wataw'],
        true
    )) {
        return false;
    }

    if (preg_match(
        '/\.(phtml|phar|sh|so|pem|key|log|sqlite-wal|sqlite-shm)$/i',
        $relative
    )) {
        return false;
    }

    $generated_php =
        strpos($relative, 'botmak/') === 0 &&
        preg_match('/\.php[0-9]?$/i', $relative);

    if (
        preg_match('/\.php[0-9]?$/i', $relative) &&
        !$generated_php
    ) {
        return false;
    }

    if (preg_match(
        '~(^|/)(\.git|\.svn|vendor|node_modules|cache|tmp|temp|backups?)(/|$)~i',
        $relative
    )) {
        return false;
    }

    if (preg_match(
        '~(^|/)\.(env|htaccess|htpasswd)(\.|$)~i',
        $relative
    )) {
        return false;
    }

    return true;
}

function backup_collect_files()
{
    $root = realpath(__DIR__);

    if ($root === false) {
        throw new RuntimeException(
            'تعذر تحديد مجلد المصنع.'
        );
    }

    $files = [];

    $root_files = [
        'admin.txt',
        'info.txt',
        'infoidbots.txt',
        'botfreeid.txt',
        'code.json',
        'datatime.json',
        'prodate.json',
        'sudo.json',
        'no3send.txt',
        'chatsend.txt',
        'get.txt'
    ];

    foreach ($root_files as $name) {
        $path = $root . DIRECTORY_SEPARATOR . $name;

        if (is_file($path) && !is_link($path)) {
            $files[$name] = $path;
        }
    }

    $directories = [
        'sudo',
        'data',
        'user',
        'from_id',
        'botmak',
        'wataw'
    ];

    foreach ($directories as $directory) {
        $base = $root . DIRECTORY_SEPARATOR . $directory;

        if (!is_dir($base) || is_link($base)) {
            continue;
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(
                $base,
                FilesystemIterator::SKIP_DOTS
            )
        );

        foreach ($iterator as $file) {
            if (
                !$file->isFile() ||
                $file->isLink()
            ) {
                continue;
            }

            $absolute = $file->getPathname();

            $relative = str_replace(
                '\\',
                '/',
                substr($absolute, strlen($root) + 1)
            );

            if (backup_allowed_path($relative)) {
                $files[$relative] = $absolute;
            }
        }
    }

    ksort($files, SORT_STRING);

    return $files;
}

function backup_send_document($chat_id, $path, $caption)
{
    if (!class_exists('CURLFile')) {
        throw new RuntimeException(
            'دعم CURLFile غير متوفر.'
        );
    }

    return bot('sendDocument', [
        'chat_id' => $chat_id,
        'document' => new CURLFile($path),
        'caption' => $caption
    ]);
}

function create_factory_backup($chat_id)
{
    if (
        !extension_loaded('sodium') ||
        !extension_loaded('curl')
    ) {
        backup_send_message(
            $chat_id,
            '❌ يلزم تفعيل إضافتي PHP: sodium و cURL.'
        );
        return;
    }

    $temp_dir = null;

    try {
        backup_encryption_key();

        $files = backup_collect_files();

        $backup_id = bin2hex(random_bytes(8));

        $manifest = [
            'format' => 'factory-backup-v2',
            'backup_id' => $backup_id,
            'created_utc' => gmdate('c'),
            'files' => [],
            'sent_files' => 0,
            'failed_files' => 0
        ];

        $temp_dir = rtrim(
            sys_get_temp_dir(),
            DIRECTORY_SEPARATOR
        ) . DIRECTORY_SEPARATOR
          . 'factory_backup_'
          . bin2hex(random_bytes(8));

        if (!mkdir($temp_dir, 0700, true)) {
            throw new RuntimeException(
                'تعذر إنشاء المجلد المؤقت.'
            );
        }

        foreach ($files as $relative => $absolute) {
            $plain = @file_get_contents($absolute);

            if ($plain === false) {
                $manifest['failed_files']++;
                continue;
            }

            $hash = hash('sha256', $plain);
            $bytes = strlen($plain);

            $encrypted = backup_encrypt_data($plain);

            $temp_file = $temp_dir
                . DIRECTORY_SEPARATOR
                . bin2hex(random_bytes(8))
                . '.fbk';

            if (
                file_put_contents(
                    $temp_file,
                    $encrypted,
                    LOCK_EX
                ) === false
            ) {
                $manifest['failed_files']++;
                continue;
            }

            $caption = 'FBK1|'
                . $backup_id . '|'
                . backup_encode_path($relative) . '|'
                . $hash . '|'
                . $bytes;

            try {
                $response = backup_send_document(
                    $chat_id,
                    $temp_file,
                    $caption
                );
            } finally {
                @unlink($temp_file);
            }

            if (
                is_object($response) &&
                !empty($response->ok)
            ) {
                $manifest['sent_files']++;

                $manifest['files'][] = [
                    'path' => $relative,
                    'sha256' => $hash,
                    'bytes' => $bytes
                ];
            } else {
                $manifest['failed_files']++;
            }

            // تخفيف الضغط على واجهة تيليجرام.
            usleep(200000);
        }

        $manifest_path = $temp_dir
            . DIRECTORY_SEPARATOR
            . 'manifest.json';

        $manifest_json = json_encode(
            $manifest,
            JSON_UNESCAPED_UNICODE |
            JSON_PRETTY_PRINT |
            JSON_THROW_ON_ERROR
        );

        if (
            file_put_contents(
                $manifest_path,
                $manifest_json,
                LOCK_EX
            ) === false
        ) {
            throw new RuntimeException(
                'تعذر إنشاء فهرس النسخة.'
            );
        }

        $manifest_response = backup_send_document(
            $chat_id,
            $manifest_path,
            'FBK1-MANIFEST|' . $backup_id
        );

        $manifest_sent =
            is_object($manifest_response) &&
            !empty($manifest_response->ok);

        $complete =
            $manifest_sent &&
            $manifest['failed_files'] === 0 &&
            $manifest['sent_files'] === count($files);

        $summary =
            "📦 انتهت عملية النسخ الاحتياطي.\n\n"
            . "معرّف النسخة: " . $backup_id . "\n"
            . "عدد الملفات الأصلية: " . count($files) . "\n"
            . "الملفات المرسلة: "
            . $manifest['sent_files'] . "\n"
            . "الملفات التي فشل إرسالها: "
            . $manifest['failed_files'] . "\n"
            . "الفهرس: "
            . ($manifest_sent ? 'تم إرساله' : 'فشل إرساله')
            . "\n\n"
            . (
                $complete
                    ? '✅ أُرسلت الملفات والفهرس بنجاح.'
                    : '⚠️ النسخة غير مكتملة. لا تعتمد عليها للاستعادة.'
            );

        backup_send_message($chat_id, $summary);

    } catch (Throwable $e) {
        error_log(
            'Factory backup error: ' . $e->getMessage()
        );

        backup_send_message(
            $chat_id,
            '❌ تعذر إكمال النسخ الاحتياطي. راجع سجل أخطاء PHP.'
        );

    } finally {
        if (is_string($temp_dir) && is_dir($temp_dir)) {
            foreach (glob($temp_dir . DIRECTORY_SEPARATOR . '*') ?: [] as $item) {
                if (is_file($item) && !is_link($item)) {
                    @unlink($item);
                }
            }

            @rmdir($temp_dir);
        }
    }
}


/* ==========================================
   دوال الاستعادة - داخل rido.php
========================================== */

function restore_decrypt_data($payload)
{
    if (!is_string($payload)) {
        throw new RuntimeException('بيانات الملف غير صالحة.');
    }

    $nonce_length = SODIUM_CRYPTO_SECRETBOX_NONCEBYTES;
    $mac_length = SODIUM_CRYPTO_SECRETBOX_MACBYTES;

    if (
        strlen($payload) < 4 + $nonce_length + $mac_length ||
        substr($payload, 0, 4) !== 'FBK1'
    ) {
        throw new RuntimeException(
            'الملف ليس ملف نسخة احتياطية مشفرًا صالحًا.'
        );
    }

    $key = backup_encryption_key();
    $nonce = substr($payload, 4, $nonce_length);
    $ciphertext = substr($payload, 4 + $nonce_length);

    $plain = sodium_crypto_secretbox_open(
        $ciphertext,
        $nonce,
        $key
    );

    if ($plain === false) {
        throw new RuntimeException(
            'فشل فك التشفير؛ تحقق من مفتاح النسخة وسلامة الملف.'
        );
    }

    return $plain;
}

function restore_decode_path($encoded)
{
    $encoded = strtr($encoded, '-_', '+/');
    $encoded .= str_repeat('=', (4 - strlen($encoded) % 4) % 4);

    return base64_decode($encoded, true);
}

function restore_session_directory($chat_id, $backup_id)
{
    if (!preg_match('/^[a-f0-9]{16}$/', $backup_id)) {
        throw new RuntimeException('معرّف النسخة غير صالح.');
    }

    $root = realpath(__DIR__);

    if ($root === false) {
        throw new RuntimeException('تعذر تحديد مجلد المصنع.');
    }

    $stage_root = getenv('BACKUP_STAGING_DIR');

    if (!is_string($stage_root) || $stage_root === '') {
        $stage_root = $root . DIRECTORY_SEPARATOR
            . '.fb_restore_sessions';
    }

    if (is_link($stage_root)) {
        throw new RuntimeException(
            'مجلد الاستعادة المؤقت لا يجوز أن يكون رابطًا.'
        );
    }

    if (
        !is_dir($stage_root) &&
        !@mkdir($stage_root, 0700, true)
    ) {
        throw new RuntimeException(
            'تعذر إنشاء مجلد الاستعادة المؤقت.'
        );
    }

    $stage_real = realpath($stage_root);

    if ($stage_real === false || !is_dir($stage_real)) {
        throw new RuntimeException(
            'مجلد الاستعادة المؤقت غير صالح.'
        );
    }

    $session = $stage_real
        . DIRECTORY_SEPARATOR
        . 'restore_'
        . hash('sha256', (string)$chat_id)
        . '_'
        . $backup_id;

    if (is_link($session)) {
        throw new RuntimeException(
            'مسار جلسة الاستعادة غير آمن.'
        );
    }

    if (!is_dir($session) && !@mkdir($session, 0700, true)) {
        throw new RuntimeException(
            'تعذر إنشاء جلسة الاستعادة.'
        );
    }

    @chmod($session, 0700);

    return $session;
}

function restore_download_telegram_document($document)
{
    if (
        !is_object($document) ||
        empty($document->file_id)
    ) {
        throw new RuntimeException(
            'تعذر تحديد ملف تيليجرام.'
        );
    }

    $result = bot('getFile', [
        'file_id' => $document->file_id
    ]);

    if (
        !is_object($result) ||
        empty($result->ok) ||
        empty($result->result->file_path)
    ) {
        throw new RuntimeException(
            'تعذر الحصول على مسار الملف من تيليجرام.'
        );
    }

    $remote_path = $result->result->file_path;

    if (
        strpos($remote_path, '..') !== false ||
        strpos($remote_path, '/') === 0 ||
        strpos($remote_path, '\\') !== false
    ) {
        throw new RuntimeException(
            'مسار الملف المستلم غير صالح.'
        );
    }

    $url = 'https://api.telegram.org/file/bot'
        . API_KEY . '/' . $remote_path;

    $ch = curl_init($url);

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => false,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_TIMEOUT => 90
    ]);

    $content = curl_exec($ch);
    $http_code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($ch);

    curl_close($ch);

    if (
        !is_string($content) ||
        $http_code !== 200
    ) {
        error_log('Restore download error: ' . $curl_error);

        throw new RuntimeException(
            'تعذر تنزيل الملف من تيليجرام.'
        );
    }

    // حد وقائي لحجم الملف الذي سيُحفظ مؤقتًا.
    if (strlen($content) > 20 * 1024 * 1024) {
        throw new RuntimeException(
            'حجم الملف يتجاوز الحد المسموح به للاستعادة.'
        );
    }

    return $content;
}

function restore_receive_backup_document(
    $chat_id,
    $from_id,
    $document,
    $caption,
    $sudo_ids
) {
    if (!in_array(
        (string)$from_id,
        array_map('strval', $sudo_ids),
        true
    )) {
        return;
    }

    if (
        !extension_loaded('sodium') ||
        !extension_loaded('curl')
    ) {
        backup_send_message(
            $chat_id,
            '❌ يلزم تفعيل إضافتي PHP: sodium و cURL.'
        );
        return;
    }

    try {
        backup_encryption_key();

        /*
         * استقبال فهرس النسخة والتحقق من الملفات
         * ثم دمج الملفات الجديدة فقط.
         */
        if (strpos($caption, 'FBK1-MANIFEST|') === 0) {
            $backup_id = substr($caption, 14);

            if (!preg_match('/^[a-f0-9]{16}$/', $backup_id)) {
                throw new RuntimeException(
                    'معرّف فهرس النسخة غير صالح.'
                );
            }

            $session = restore_session_directory(
                $chat_id,
                $backup_id
            );

            $manifest_raw = restore_download_telegram_document(
                $document
            );

            if (strlen($manifest_raw) > 5 * 1024 * 1024) {
                throw new RuntimeException(
                    'حجم فهرس النسخة أكبر من المسموح.'
                );
            }

            $manifest = json_decode(
                $manifest_raw,
                true,
                64,
                JSON_THROW_ON_ERROR
            );

            if (
                !is_array($manifest) ||
                ($manifest['format'] ?? '') !== 'factory-backup-v2' ||
                ($manifest['backup_id'] ?? '') !== $backup_id ||
                !isset($manifest['files']) ||
                !is_array($manifest['files'])
            ) {
                throw new RuntimeException(
                    'فهرس النسخة غير صالح أو لا يطابق الملفات.'
                );
            }

            if (
                (int)($manifest['failed_files'] ?? -1) !== 0 ||
                (int)($manifest['sent_files'] ?? -1) !== count($manifest['files'])
            ) {
                throw new RuntimeException(
                    'النسخة الأصلية غير مكتملة؛ رُفضت الاستعادة.'
                );
            }

            $validated = [];
            $seen_paths = [];

            /*
             * المرحلة الأولى: التحقق من جميع الملفات
             * قبل تعديل أي ملف في المصنع.
             */
            foreach ($manifest['files'] as $entry) {
                if (
                    !is_array($entry) ||
                    !isset(
                        $entry['path'],
                        $entry['sha256'],
                        $entry['bytes']
                    )
                ) {
                    throw new RuntimeException(
                        'يوجد إدخال غير صالح في فهرس النسخة.'
                    );
                }

                $relative = $entry['path'];
                $expected_hash = $entry['sha256'];
                $expected_bytes = filter_var(
                    $entry['bytes'],
                    FILTER_VALIDATE_INT
                );

                if (
                    !is_string($relative) ||
                    !backup_allowed_path($relative) ||
                    !is_string($expected_hash) ||
                    !preg_match('/^[a-f0-9]{64}$/', $expected_hash) ||
                    $expected_bytes === false ||
                    $expected_bytes < 0 ||
                    isset($seen_paths[$relative])
                ) {
                    throw new RuntimeException(
                        'يوجد مسار مكرر أو بيانات غير آمنة في الفهرس.'
                    );
                }

                $seen_paths[$relative] = true;

                $staged_file = $session
                    . DIRECTORY_SEPARATOR
                    . hash('sha256', $relative)
                    . '.payload';

                if (
                    !is_file($staged_file) ||
                    is_link($staged_file)
                ) {
                    throw new RuntimeException(
                        "ملف مفقود: {$relative}\n"
                        . "أرسل الملف الناقص، ثم أعد إرسال فهرس النسخة أخيرًا."
                    );
                }

                $encrypted = @file_get_contents($staged_file);

                if ($encrypted === false) {
                    throw new RuntimeException(
                        'تعذر قراءة ملف مستلم: ' . $relative
                    );
                }

                $plain = restore_decrypt_data($encrypted);

                if (
                    strlen($plain) !== $expected_bytes ||
                    !hash_equals(
                        $expected_hash,
                        hash('sha256', $plain)
                    )
                ) {
                    throw new RuntimeException(
                        'فشل فحص سلامة الملف: ' . $relative
                    );
                }

                $validated[$relative] = [
                    'plain' => $plain,
                    'hash' => $expected_hash
                ];
            }

            /*
             * المرحلة الثانية: الدمج الآمن.
             * الملفات المتطابقة تُترك كما هي.
             * الملفات المختلفة الموجودة مسبقًا لا تُستبدل.
             */
            $root = realpath(__DIR__);

            if ($root === false) {
                throw new RuntimeException(
                    'تعذر تحديد مجلد المصنع.'
                );
            }

            $added = 0;
            $same = 0;
            $conflicts = 0;

            foreach ($validated as $relative => $item) {
                $target = $root
                    . DIRECTORY_SEPARATOR
                    . str_replace(
                        '/',
                        DIRECTORY_SEPARATOR,
                        $relative
                    );

                $parent_relative = dirname($relative);
                $cursor = $root;

                if ($parent_relative !== '.') {
                    foreach (explode('/', $parent_relative) as $part) {
                        $cursor .= DIRECTORY_SEPARATOR . $part;

                        if (is_link($cursor)) {
                            throw new RuntimeException(
                                'رُفض مسار يحتوي على رابط رمزي: ' . $relative
                            );
                        }

                        if (!file_exists($cursor)) {
                            if (!@mkdir($cursor, 0750)) {
                                throw new RuntimeException(
                                    'تعذر إنشاء مجلد الملف: ' . $relative
                                );
                            }
                        }

                        if (!is_dir($cursor)) {
                            throw new RuntimeException(
                                'أحد أجزاء المسار ليس مجلدًا: ' . $relative
                            );
                        }
                    }
                }

                $root_real = realpath($root);
                $parent_real = realpath(dirname($target));

                if (
                    $root_real === false ||
                    $parent_real === false ||
                    (
                        $parent_real !== $root_real &&
                        strpos(
                            $parent_real,
                            $root_real . DIRECTORY_SEPARATOR
                        ) !== 0
                    ) ||
                    is_link($target)
                ) {
                    throw new RuntimeException(
                        'رُفض مسار الوجهة لأسباب أمنية: ' . $relative
                    );
                }

                if (file_exists($target)) {
                    if (!is_file($target)) {
                        throw new RuntimeException(
                            'الوجهة موجودة وليست ملفًا عاديًا: ' . $relative
                        );
                    }

                    $existing_hash = @hash_file('sha256', $target);

                    if (
                        is_string($existing_hash) &&
                        hash_equals($item['hash'], $existing_hash)
                    ) {
                        $same++;
                        continue;
                    }

                    $conflict_dir = $session
                        . DIRECTORY_SEPARATOR
                        . 'conflicts';

                    if (
                        !is_dir($conflict_dir) &&
                        !@mkdir($conflict_dir, 0700, true)
                    ) {
                        throw new RuntimeException(
                            'تعذر حفظ الملف المتعارض.'
                        );
                    }

                    $conflict_file = $conflict_dir
                        . DIRECTORY_SEPARATOR
                        . hash('sha256', $relative)
                        . '.conflict';

                    if (
                        file_put_contents(
                            $conflict_file,
                            $item['plain'],
                            LOCK_EX
                        ) === false
                    ) {
                        throw new RuntimeException(
                            'تعذر حفظ تعارض الملف: ' . $relative
                        );
                    }

                    @chmod($conflict_file, 0600);
                    $conflicts++;
                    continue;
                }

                $temporary_target = $target
                    . '.restore-'
                    . bin2hex(random_bytes(6));

                if (
                    file_put_contents(
                        $temporary_target,
                        $item['plain'],
                        LOCK_EX
                    ) === false
                ) {
                    @unlink($temporary_target);

                    throw new RuntimeException(
                        'تعذرت كتابة الملف: ' . $relative
                    );
                }

                @chmod($temporary_target, 0640);

                if (!@rename($temporary_target, $target)) {
                    @unlink($temporary_target);

                    throw new RuntimeException(
                        'تعذر تثبيت الملف المستعاد: ' . $relative
                    );
                }

                $added++;
            }

            /*
             * لا نحذف الملفات المتعارضة.
             * نحذف الملفات المؤقتة المستلمة بعد نجاح الدمج،
             * ونبقي مجلد التعارضات إن وُجد.
             */
            foreach (glob($session . DIRECTORY_SEPARATOR . '*.payload') ?: [] as $staged) {
                if (is_file($staged) && !is_link($staged)) {
                    @unlink($staged);
                }
            }

            backup_send_message(
                $chat_id,
                "✅ انتهت معالجة النسخة الاحتياطية.\n\n"
                . "الملفات الجديدة: {$added}\n"
                . "الملفات المطابقة الموجودة مسبقًا: {$same}\n"
                . "الملفات المتعارضة المحفوظة للمراجعة: {$conflicts}\n\n"
                . (
                    $conflicts > 0
                        ? "⚠️ لم تُستبدل الملفات الحالية المتعارضة. "
                            . "حُفظت نسخها الواردة في مجلد conflicts المؤقت."
                        : "لم تُكتشف تعارضات."
                )
            );

            return;
        }

        /*
         * استقبال ملف مشفر عادي وتخزينه مؤقتًا فقط.
         * لا يحدث دمج عند استقبال الملفات الفردية.
         */
        $parts = explode('|', $caption);

        if (count($parts) !== 5 || $parts[0] !== 'FBK1') {
            throw new RuntimeException(
                'الملف غير معروف. أرسل ملف FBK1 الأصلي مع وصفه.'
            );
        }

        $backup_id = $parts[1];
        $relative = restore_decode_path($parts[2]);
        $expected_hash = $parts[3];
        $expected_bytes = filter_var(
            $parts[4],
            FILTER_VALIDATE_INT
        );

        if (
            !preg_match('/^[a-f0-9]{16}$/', $backup_id) ||
            !is_string($relative) ||
            !backup_allowed_path($relative) ||
            !preg_match('/^[a-f0-9]{64}$/', $expected_hash) ||
            $expected_bytes === false ||
            $expected_bytes < 0
        ) {
            throw new RuntimeException(
                'وصف الملف أو مساره أو بصمته غير صالح.'
            );
        }

        $session = restore_session_directory(
            $chat_id,
            $backup_id
        );

        $payload = restore_download_telegram_document($document);

        // فحص التشفير والبصمة قبل تخزين الملف المؤقت.
        $plain = restore_decrypt_data($payload);

        if (
            strlen($plain) !== $expected_bytes ||
            !hash_equals(
                $expected_hash,
                hash('sha256', $plain)
            )
        ) {
            throw new RuntimeException(
                'فشل التحقق من سلامة الملف: ' . $relative
            );
        }

        $staged_file = $session
            . DIRECTORY_SEPARATOR
            . hash('sha256', $relative)
            . '.payload';

        if (
            file_put_contents(
                $staged_file,
                $payload,
                LOCK_EX
            ) === false
        ) {
            throw new RuntimeException(
                'تعذر حفظ الملف في مساحة الاستعادة المؤقتة.'
            );
        }

        @chmod($staged_file, 0600);

        backup_send_message(
            $chat_id,
            "📥 استُلم الملف وفُحص بنجاح، ولم يُدمج بعد.\n"
            . "المسار: {$relative}\n"
            . "معرّف النسخة: {$backup_id}\n\n"
            . "أرسل بقية ملفات النسخة، ثم أرسل manifest.json أخيرًا."
        );

    } catch (Throwable $e) {
        error_log(
            'Factory restore error: ' . $e->getMessage()
        );

        backup_send_message(
            $chat_id,
            '❌ لم تتم الاستعادة: ' . $e->getMessage()
        );
    }
}



//----------------------------------------------------------
