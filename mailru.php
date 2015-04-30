<?
<?
/*
Sait: web-wost.ru
Author: Al-Sher
Date: 30.04.2015
Update: 30.04.2015
Version: 1.0
*/
/* Данные приложения */
$mail=array(
'id'=>'ID_приложения',
'key'=>'ключ_приложения',
'secret'=>'секретный_ключ_приложения',
'redirect'=>'страница_редиректа'
); 
/* ///Данные приложения\\\ */
$link='<a href="https://connect.mail.ru/oauth/authorize?client_id='.$mail["id"].'&response_type=code&redirect_uri='.urldecode($mail["redirect"]).'?state=mail">Аутентификация через Mail.ru</a>'; //Создание ссылки для аутентификации
echo $link;
if(isset($_GET["state"]) and @$_GET["state"]=='mail' and isset($_GET["code"])) {
/* Пишем POST запрос к mail.ru */
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, 'https://connect.mail.ru/oauth/token');
curl_setopt($curl, CURLOPT_POST, 1);
curl_setopt($curl, CURLOPT_POSTFIELDS, 'client_id='.$mail["id"].'&client_secret='.urldecode($mail["secret"]).'&grant_type=authorization_code&code='.urldecode($_GET["code"]).'&redirect_uri='.urldecode($mail["redirect"]).'?state=mail');
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
$result = curl_exec($curl);
curl_close($curl);
/* ///Пишем POST запрос к mail.ru\\\ */
$tokenInfo = json_decode($result, true); //Расшифровываем полученный ответ
if(isset($tokenInfo["access_token"])) { // Проверяем пришел ли токен
$userInfo = json_decode(file_get_contents('http://www.appsmail.ru/platform/api?method=users.getInfo&secure=1&app_id='.$mail["id"].'&session_key='.urldecode($tokenInfo["access_token"]).'&sig='.urldecode(md5("app_id=".$mail["id"]."method=users.getInfosecure=1session_key=".$tokenInfo['access_token'].$mail["secret"])), true)); // Получаем данные о пользователе
$userInfo=$userInfo[0]; //Переводим полученный результат в объект
print_r($userInfo); // Вывод всех полученных данных о пользователе
// $userInfo->uid; // Выводим ID пользователя
}
else echo "Error"; // Сообщаем об ошибки связанной с получением токена
}
