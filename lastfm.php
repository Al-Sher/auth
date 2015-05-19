/*
Sait: web-wost.ru
Author: Al-Sher
Date: 19.05.2014
Update: 19.05.2014
Version: 1.0
*/
/* Данные приложения */
$lfm=array(
'key'=>'Ключ',
'secret'=>'Секретный_ключ',
'redirect'=>'Страница_редиректа?state=lfm'
);
/* ///Данные приложения\\\ */
$link='<a href="http://www.last.fm/api/auth/?api_key='.$lfm["key"].'&cb='.$lfm["redirect"].'">Аутентификация через Last FM</a>'; // Создание ссылки
echo $link;

if(isset($_GET["token"]) and isset($_GET["state"]) and @$_GET["state"]=='lfm') { // Проверяем на наличие токена
$token=$_GET["token"]; 
$key=md5("api_key".$lfm["key"]."methodauth.getSessiontoken".$_GET["token"].$lfm["secret"]); // хеширование строки
/* Пишем POST запрос к lastfm`у */
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, "http://ws.audioscrobbler.com/2.0/?method=auth.getSession");
curl_setopt($curl, CURLOPT_POST, 1);
curl_setopt($curl, CURLOPT_POSTFIELDS, "api_key=".urldecode($lfm["key"])."&token=".urldecode($_GET["token"])."&api_sig=".urldecode($key));
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
$result = curl_exec($curl);
curl_close($curl);
/* ///Пишем POST запрос к lastfm`у\\\ */
$p = xml_parser_create();
xml_parse_into_struct($p, $result, $result);
xml_parser_free($p);
$user=$result[2]["value"]; // вытаскиваем ник пользователя
$userInfo=json_decode(file_get_contents("http://ws.audioscrobbler.com/2.0/?method=user.getinfo&user=".$user."&api_key=".$lfm["key"]."&format=json")); // Вытаскиваем информацию о пользователе
}
