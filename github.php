<?
/*
Sait: web-wost.ru
Author: Al-Sher
Date: 04.05.2014
Update: 04.05.2014
Version: 1.0
*/
/* Данные приложения */
$gh=array(
'id'=>'ID_Приложения',
'key'=>'Секретный_ключ',
'redirect'=>'страница_редиректа'
);
/* ///Данные приложения\\\ */
$link= '<a href="https://github.com/login/oauth/authorize?client_id='.$gh["id"].'&redirect_uri='.$gh["redirect"].'&scope=user,&state=github">Аутентификация черези github</a>'; //Создание ссылки для аутентификации
echo $link;

if(isset($_GET["state"]) and @$_GET["state"]=='github' and isset($_GET["code"])) {
	    $params = array(
        'client_id'=> $gh["id"],
        'client_secret'=> $gh["key"],
        'code'=> $_GET["code"],
        'redirect_uri'=> $url_r
    ); // Преобразуем все данные в массив для более удобной передачи данных
/* Пишем POST запрос к github`у */
$url = 'https://github.com/login/oauth/access_token';
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_POST, 1);
curl_setopt($curl, CURLOPT_POSTFIELDS, urldecode(http_build_query($params)));
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
$result = curl_exec($curl);
curl_close($curl);
/* ///Пишем POST запрос к github`у\\\ */
parse_str($result, $result); // Преобразуем ответ в массив
/* Пишем запрос к github`у с включенными куками и от браузера "Mozilla" */
$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_URL, 'https://api.github.com/user?access_token='.urlencode($result["access_token"]));
curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (Windows; U; Windows NT 5.0; En; rv:1.8.0.2) Gecko/20070306 Firefox/1.0.0.4");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$result=curl_exec($ch);
curl_close($ch);
/* ///Пишем запрос к github`у с включенными куками и от браузера "Mozilla"\\\ */
if(strpos($result, "HTTP/1.1 500 Internal Server Error") === true) $result = false; // Проверяем ответ(не пришла ли ошибка скрипта)
$userInfo = json_decode($result); // Превращаем ответ в класс
