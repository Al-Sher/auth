<?
/*
Sait: chat.ws-cat.pw
Author: Al-Sher
Date: 29.10.2014
Update: 29.10.2014
Version: 1.0
*/
/*
Данные приложения
*/
$id = "***"; // ID приложения
$url = "http://***"; // Ссылка, куда будет отправлен пользователь после авторизации
$secret = "***"; // Секретный ключ приложения
/* ---- */
?>
<?php
$auth = "https://oauth.vk.com/authorize?client_id=".$id."&scope=offline,status&redirect_uri=".$url."&response_type=code&v=5.21"; // Ссылка, по которой должен перейти пользователь, чтобы мы получили данные
?>

<?
if(isset($_GET["code"])) { // Проверяем на наличие кода
$code = $_GET["code"]; // Переводим запрос в обычную переменную
$auth = file_get_contents("https://oauth.vk.com/access_token?client_id=".$id."&client_secret=".$secret."&code=".$code."&redirect_uri=".$url); // Получаем данные от сервера
$token = json_decode($auth); // Разбираем ответ сервера
$id_user = $token -> user_id; // Добавляем ID пользователя в переменную id_user
$token = $token -> access_token;
        $params = array(
            'uids'         => $id_user,
            'fields'       => 'uid,first_name,last_name,photo_100,status',
            'access_token' => $token
        ); // Обрабатываем дальше ответ
$auth_1 = file_get_contents("https://api.vk.com/method/users.get?".urldecode(http_build_query($params))); // Отправляем запрос серверу
$user_info = json_decode($auth_1);  // Разбираем ответ
$photo = $user_info -> response[0] -> photo_100; // Получаем фотографию пользователя в размере 100х100
$id_user = $user_info -> response[0] -> uid; // Получаем ID пользователя
$array = $user_info -> response[0];
$name = $array -> first_name; // Получаем имя пользователя
$name2 = $array -> last_name; // Получаем фамилию пользователя
$name = $name." ".$name2; // Соединяем имя и фамилию
$status = $array -> status; // Получаем статус пользователя
$date = date("Y-m-d G:i"); // Дата регистрации в формате ГГГГ-ММ-ДД ЧЧ:ММ*
if($name != "") { // Проверяем наличие полученных данных
    $result = mysql_query("SELECT * FROM users WHERE id_vk='$id_user'"); // Делаем запрос в БД на проверку такого пользователя
    $myrow = mysql_fetch_array($result);
    if($myrow["name"] != "") { // Проверяем
        //Есть пользователь
        SetCookie("ID", $myrow["id"], time()+604800); // Создаем куки
        SetCookie("Name", $myrow["name"], time()+604800);
        header("Location: ".$url_home);
        $result_new = mysql_query("UPDATE `users` SET photo='$photo', name='$name', status='$status', date_on='$date', online='1' WHERE id_vk='$id_user'");
    }
    else {
        //Нет пользователя
        $result_new = mysql_query("INSERT INTO `users` (`id_vk`,`name`,`photo`, `status`, `date_reg`, `date_on`) VALUES ('$id_user', '$name', '$photo', '$status', '$date', '$date')");
        if($result == true) header("Location: ".$url_home."?reg");
        else mysql_error();
    }
}
else exit("Ошибка"); // Уведомляем об ошибке
}
?>
