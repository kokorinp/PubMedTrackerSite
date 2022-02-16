<?php
defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Factory; 

jimport('joomla.application.component.controller');
  
class PubMedTrackerController extends JControllerLegacy{

    function responseJSON($message, $data = null){
        return json_encode([
            'success' => true,
            'message' => $message,
            'data' => $data
        ]);
    }

    function responseErrorJSON($message, $data = null){
        return json_encode([
            'success' => false,
            'message' => $message,
            'data' => $data
        ]);
    }

    function getisuser(){
        $user = JFactory::getUser();
        $result = new \stdClass;
        $result->name = $user->name;
        $result->id = $user->id;
        
        exit(json_encode($result));
    }


    //new
    function setemail(){
        $user = JFactory::getUser();
        $result = new \stdClass;
        $result->response = 0;

        if ($user->get('guest') == 1) {
            $message = "Не авторизован!";
            exit($this->responseErrorJSON($message, []));
        }
        
        $email = $this->input->get("email1", "");
        
        if ($email != ""){
            $email .= "@".$this->input->get("email2", "");
        }
        else{
            $message = "Нет email!";
            exit($this->responseErrorJSON($message, []));
        }

        //далее апдейтим мыло в базе
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->update('#__pubmedtracker_users_profiles')
                ->where('user_id='.$user->id);
        $query->set('email="'.$email.'"');
        $db->setQuery($query);
        $db->query();

        $result->email = $email;
        $result->response = 1;
        $message = "Успех!";
        exit($this->responseJSON($message, $result));
    }



    function gethistory(){
        $result = new \stdClass;
        $result->response = 0;
  
        $user = JFactory::getUser();
        $db = JFactory::getDBO();
        $sql = 'SELECT DATE_FORMAT(timestamp, \'%d.%m.%Y %H:%i:%s\') as date , value, descr  FROM #__pubmedtracker_balance_history WHERE user_id = '.$user->id.' ORDER BY timestamp DESC LIMIT 100';
        $db->setQuery($sql);
        $rows = $db->loadAssocList();

        $result->response = 1;
        $result->data = $rows;

        exit(json_encode($result));
    }





    
    //new
    function gettarife(){
        $result = new \stdClass;

        $user = JFactory::getUser();        
        if ($user->get('guest') == 1) {
            $message = "Не авторизован!";
            exit($this->responseErrorJSON($message, []));
        }

        $db = JFactory::getDBO();
        $sql = 'SELECT * FROM #__pubmedtracker_users_profiles WHERE user_id = '.$user->id;
        $db->setQuery($sql);
        $user_profile = $db->loadAssocList();
        if (count($user_profile)<1){
            $message = "Отсутствует профиль пользователя";
            exit($this->responseErrorJSON($message, []));
        }
        $result->user_profile = $user_profile;


        //все тарифы
        $db = JFactory::getDBO();
        $sql = 'SELECT * FROM #__pubmedtracker_tarifes
                WHERE CURDATE() BETWEEN date_beg AND date_end
                AND status = 1
                ORDER BY count_query';
        $db->setQuery($sql);
        $tarifes = $db->loadAssocList();        
        if (count($tarifes)<1){
            $message = "Список тарифов пустой";
            exit($this->responseErrorJSON($message, []));
        }
        $result->tarifes = $tarifes;


        //текущий тариф пользователя
        $db = JFactory::getDBO();
        $sql = 'SELECT * FROM #__pubmedtracker_tarifes WHERE tarife_id = '.$user_profile[0]["tarife_id"];
        $db->setQuery($sql);
        $current_tarife = $db->loadAssocList();
        $result->current_tarife = $current_tarife;


        $result->current_date = date('Y-m-d', time());

        $message = 'Успех!';
        exit($this->responseJSON($message, $result));

    }


    // Установка нового тарифа
    function settarife(){
        $result = new \stdClass;
        $result->reload = 0;

        $user = JFactory::getUser();

        $db = JFactory::getDBO();
        $sql = 'SELECT * FROM #__pubmedtracker_users_profiles WHERE user_id = '.$user->id;
        $db->setQuery($sql);
        $user_profile = $db->loadAssocList();
        $message = "";
        if (count($user_profile)<1){
            $message = "Отсутствует профиль пользователя";
            exit($this->responseErrorJSON($message, []));    
        }

        $tarife_id = $this->input->get("tarife", 0); // выбраный тариф
        
        if ($tarife_id == 0){
            $message = "Не выбран тариф";
            exit($this->responseErrorJSON($message, []));
        }

        //запрашиваем тариф
        $db = JFactory::getDBO();
        $sql = 'SELECT * FROM #__pubmedtracker_tarifes 
                WHERE status = 1 
                AND CURDATE() BETWEEN date_beg AND date_end 
                AND tarife_id = '.$tarife_id;
        $db->setQuery($sql);
        $tarife_new = $db->loadAssocList();

        if (count($tarife_new)<1){
            exit($this->responseErrorJSON("Выбранный вами тариф более не обслуживается", []));
        }

        $result->tarife_new = $tarife_new;
        
        $current_date = strtotime("now");
        $result->current_date = date("Y-m-d",$current_date);
        
        $dayTMP = date('d', $current_date);
        $monthTMP = date('m', $current_date);

        if (($monthTMP == 1)&&($dayTMP == 30 || $dayTMP == 31)){
            $D_TMP = strtotime("10 day", $current_date);
            $current_date_plus_month = strtotime('last day of this month',$D_TMP);
            //exit($this->responseJSON("30 31 января", $result));
        }
        else{
            $D_TMP = strtotime("1 month", $current_date);
            $current_date_plus_month = strtotime("-1 day", $D_TMP);
            //exit($this->responseJSON($monthTMP."30 31 января НЕ СРАБАТЫВАЕТ".$dayTMP, $result));
        }
        
        $vozvrat = 0;
        //date("Y-m-d", strtotime("now"));
        
        if ($user_profile[0]["tarife_id"] > 0){
            if ($user_profile[0]["tarife_id"] == $tarife_new[0]["tarife_id"]){
                exit($this->responseErrorJSON("Тарифы совпадают!", []));
            }

            $period_beg = strtotime($user_profile[0]["period_beg"]);
            $period_end = strtotime($user_profile[0]["period_end"]);

            $result->period_beg = $period_beg;
            $result->period_end = $period_end;

            
            
            if (($current_date <= $period_end) && ($current_date >= $period_beg)){
                $day_in_month = floor(($period_end - $period_beg) / 86400) + 1;
                $result->day_in_month = $day_in_month;
                $day = floor(($period_end - $current_date) / 86400) + 1;
                $result->day = $day;

                
                $db = JFactory::getDBO();
                $sql = 'SELECT * FROM #__pubmedtracker_tarifes 
                        WHERE tarife_id = '.$user_profile[0]["tarife_id"];
                $db->setQuery($sql);
                $tarife_old = $db->loadAssocList();
                $result->tarife_old = $tarife_old;
                
                if (count($tarife_old)<1){
                    $message = "Старый тариф не найден, поэтому возврата не будет! ";
                    $vozvrat = 0;
                }
                else{
                    $vozvrat = round(($tarife_old[0]["price"] / $day_in_month) * $day, 2);
                }
                
            }
            
        }
        
        $result->vozvrat = $vozvrat;
        
        if (($vozvrat + $user_profile[0]["balance"]) >= $tarife_new[0]["price"]){
            if ($vozvrat > 0){
                // добавляем запись в о возврате
                $db = JFactory::getDBO();
                $query = $db->getQuery(true);
                $query
                    ->insert('#__pubmedtracker_balance_history')
                    ->columns('user_id, value, descr')
                    ->values(implode(',', array($user->id, $vozvrat ,"'Возврат за неиспользуемые дни ".$day."'")));
                $db->setQuery($query);
                $db->query();
                $db->insertid();
            }



            $db = JFactory::getDBO();
            $query = $db->getQuery(true);
                
            $query->update('#__pubmedtracker_users_profiles')
                    ->where('user_id = '.$user->id);

            $query->set('sent_status = 1');
            $query->set('balance = '.($user_profile[0]["balance"] - $tarife_new[0]["price"] + $vozvrat));
            $query->set('tarife_id = '.$tarife_new[0]["tarife_id"]);
            $query->set("period_beg = '".date("Y-m-d", $current_date)."'");
            $query->set("period_end = '".date("Y-m-d", $current_date_plus_month)."'");
            
            $db->setQuery($query);
            $db->query();


            // добавляем запись в chzv6_pubmedtracker_balance_history
            $db = JFactory::getDBO();
            $query = $db->getQuery(true);
            $query
                ->insert('#__pubmedtracker_balance_history')
                ->columns('user_id, value, descr')
                ->values(implode(',', array($user->id, -$tarife_new[0]["price"],"'Продление подписки ".$tarife_new[0]["name"]." c ".date("d.m.Y", $current_date)." по ".date("d.m.Y", $current_date_plus_month)."'")));
            $db->setQuery($query);
            $db->query();
            $db->insertid();

            
            // после смены тарифа деактивируем все строки запроса
            $db = JFactory::getDBO();
            $query = $db->getQuery(true);
            $query->update('#__pubmedtracker_users_querys')
                    ->where('user_id = '.$user->id);
            $query->set('status = 0');            
            $db->setQuery($query);
            $db->query();

            // добавляем необходимое количество строк запросов, если это необходимо
            $db = JFactory::getDBO();
            $sql = 'SELECT COUNT(*) as C FROM #__pubmedtracker_users_querys
                    WHERE user_id = '.$user->id;
            $db->setQuery($sql);
            $fact_count_query = $db->loadAssocList();
            //$tarife_old[0]["count_query"]
            //$tarife_new[0]["count_query"]
            for ($i = $fact_count_query[0]["C"]; $i < $tarife_new[0]["count_query"]; $i++){
                $result->fact_count_query = $i+1;
                $db = JFactory::getDBO();
                $q = $db->getQuery(true);
                $q  ->insert('#__pubmedtracker_users_querys')
                    ->columns('user_id, query, number_days')
                    ->values(implode(',', array($user->id, "''" , 1)));
                $db->setQuery($q);
                $db->query();
                $db->insertid();
            }

            $result->reload = 1;
        }
        else{
            $message = "Недостаточно средств для смены тарифа. Пополните баланс.";
            exit($this->responseErrorJSON($message, $result));
        }
        
        $message .= "Тариф успешно изменён!";
        exit($this->responseJSON($message, $result));
    }


    //new
    function autorenewal(){
        $user = JFactory::getUser();
        $result = new \stdClass;
        $result->response = 0;

        if ($user->get('guest') == 1) {
            $message = "Не авторизован!";
            exit($this->responseErrorJSON($message, $result));
        }

        $status = $this->input->get("status", 5, "INT");

        if (!is_numeric($status)){
            $message = "status не является числом!";
            exit($this->responseErrorJSON($message, []));
        }

        if ($status <= 1){
            //$status = !$status; //инвертировать статус
            if ($status == 1){
                $status = 0;
            }else{
                $status = 1;
            }
            $db = JFactory::getDBO();
            $q = $db->getQuery(true);
            $q->update('#__pubmedtracker_users_profiles')
                    ->where('user_id='.$user->id);
            $q->set('auto_renewal="'.$status.'"');
            $db->setQuery($q);
            $db->query();
            $message = "Успех!";
        }
        else{
            $message = "status не корректный!";
            exit($this->responseErrorJSON($message, $result));
        }

        $result->response = 1;
        exit($this->responseJSON($message, $result));
    }



    function sentstatus(){
        $result = new \stdClass;
        $result->reload = 0;
        $result->user_profile = 1;
        $result->tarife = 1;
        $result->tarife_id = 0;
        $result->tarife_period = 1;
        $result->balance = 0.00;

        $x = $this->input->get("x", "off");
        $y = 0;
        $message = "Рассылка отключена";
        
        $user = JFactory::getUser();
        
        if ($x=="on"){
            $y=1;
            $message = "Рассылка активирована";

            // нужно проверить действует ли тариф?
            $db = JFactory::getDBO();
            $sql = 'SELECT * FROM #__pubmedtracker_users_profiles
                    WHERE user_id = '.$user->id;
            //WHERE CURDATE() BETWEEN period_beg AND period_end AND
            $db->setQuery($sql);
            $user_profile = $db->loadAssocList();        
            
            if (count($user_profile)<1){
                $result->user_profile = 0;
                exit($this->responseJSON("Отсутствует профиль пользователя", $result));
            }
            else
            {
                if ($user_profile[0]["tarife_id"] == 0){
                    $result->tarife = 0;
                    exit($this->responseJSON("Для активации рассылки необходимо выбрать тариф!", $result));
                }
                else{
                    //теперь смотрим оплачен ли период?
                    //$period_beg=strtotime($user_profile[0]["period_beg"]);
                    $period_end=strtotime($user_profile[0]["period_end"]);
                    $current_time = time();

                    //$result->period_beg = $period_beg;
                    $result->period_end = $period_end;
                    $result->current_time = $current_time;
                    
                    if ($current_time > $period_end){
                        $result->tarife_period = 0; // период не оплачен... нада идти продлять период
                        $result->balance = (float)$user_profile[0]["balance"];
                        $result->tarife_id = $user_profile[0]["tarife_id"];
                    }
                }
            }
        }

        
        if ($result->tarife_period === 0)
        {
            // пробуем продлить период

            $db = JFactory::getDBO();
            $sql = 'SELECT * FROM #__pubmedtracker_tarifes
                    WHERE status = 1
                    AND tarife_id = '.$result->tarife_id;
            $db->setQuery($sql);
            $tarife = $db->loadAssocList();        
            
            if (count($tarife)<1){
                $result->tarife = 0;
                exit($this->responseJSON("Ваш тариф более не обслуживается. Смените тариф!", $result));
            }
            else{
                if ($tarife[0]["price"] <= $result->balance){
                    // если денег для пробления хватает, тогда продляем

                    $current_date = strtotime("now");
                    $D_TMP = strtotime("1 month", $current_date);
                    $current_date_plus_month = strtotime("-1 day", $D_TMP);

                    $db = JFactory::getDBO();
                    $query = $db->getQuery(true);
                        
                    $query->update('#__pubmedtracker_users_profiles')
                            ->where('user_id = '.$user->id);

                    $query->set('sent_status = 1');
                    $query->set('balance = '.($result->balance - $tarife[0]["price"]));

                    $query->set("period_beg = '".date("Y-m-d", $current_date)."'");
                    $query->set("period_end = '".date("Y-m-d", $current_date_plus_month)."'");
                    
                    $db->setQuery($query);
                    $db->query();


                    // добавляем запись в chzv6_pubmedtracker_balance_history
                    $db = JFactory::getDBO();
                    $query = $db->getQuery(true);
                    $query
                        ->insert('#__pubmedtracker_balance_history')
                        ->columns('user_id, value, descr')
                        ->values(implode(',', array($user->id,-$tarife[0]["price"],"'Продление подписки ".$tarife[0]["name"]." c ".date("d.m.Y", $current_date)." по ".date("d.m.Y", $current_date_plus_month)."'")));
                    $db->setQuery($query);
                    $db->query();
                    $db->insertid();

                    $message = "Рассылка активирована. Списано с баланса ".$tarife[0]["price"]." руб";
                    $result->reload = 1;
                }
                else{
                    $message = "Недостаточно средств для продления подписки. Пополните баланс на ".($tarife[0]["price"] - $result->balance)." руб";
                }

                
            }

        }
        else
        {
            $db = JFactory::getDBO();
            $query = $db->getQuery(true);
                
            $query->update('#__pubmedtracker_users_profiles')
                    ->where('user_id='.$user->id);

            $query->set('sent_status='.$y);

            $db->setQuery($query);
            $db->query();
            $result->reload = 1;
        }

        exit($this->responseJSON($message, $result));
    }
    
/*
// читерское пополнение баланса))
    function addbalance(){
        $result = new \stdClass;
        $result->response = 0;

        $addbalance = $this->input->get("addbalance", 0);
        
        if (!is_numeric($addbalance)){
            $result->err = "addbalance не является числом";
            exit(json_encode($result));
        }

        $newbalance = $addbalance + 100;

        $user = JFactory::getUser();
        $db = JFactory::getDBO();
        $sql = 'SELECT balance FROM #__pubmedtracker_users_profiles WHERE user_id = '.$user->id;
        $db->setQuery($sql);
        $rows = $db->loadAssocList();
        
        if (count($rows)<1){ //если записей нет, то обрываем
            $result->err = "Нет записи с балансом в БД";
            exit(json_encode($result));
        }

        $oldbalance = (float)$rows[0]["balance"];
        $newbalance = $addbalance + $oldbalance;
        //далее апдейтим баланс в базе
        
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
            
        $query->update('#__pubmedtracker_users_profiles')
                ->where('user_id='.$user->id);

        $query->set('balance='.$newbalance);

        $db->setQuery($query);
        $db->query();
        

        // добавляем запись в chzv6_pubmedtracker_balance_history
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query
            ->insert('#__pubmedtracker_balance_history')
            ->columns('user_id, value, descr')
            ->values(implode(',', array($user->id,$addbalance,"'ТЕСТ ОПЛАТА'")));
        $db->setQuery($query);
        $db->query();
        $db->insertid();

        $result->newbalance = $newbalance;
        $result->response = 1;
        exit(json_encode($result));
    }
*/
//new
function addbalance(){
    $result = new \stdClass;
    $descr = new \stdClass;
    $result->response = 0;

    $addbalance = $this->input->get("addbalance", 0);
    
    
    if (!is_numeric($addbalance)){
        $message = "Переданный параметр не является числом";
        exit($this->responseErrorJSON($message, $result));
    }
    $addbalance = round($addbalance,2);

    $user = JFactory::getUser();

    if ($user->get('guest') == 1) {
        $message = "Не авторизован!";
        exit($this->responseErrorJSON($message, []));
    }

    $db = JFactory::getDBO();
    $sql = 'SELECT balance FROM #__pubmedtracker_users_profiles WHERE user_id = '.$user->id;
    $db->setQuery($sql);
    $rows = $db->loadAssocList();
    
    if (count($rows)<1){ //если записей нет, то обрываем
        $message = "Нет записи с балансом в БД / отсутствует профиль";
        exit($this->responseErrorJSON($message, $result));       
    }

    $conf = new JConfig;

    $pass = $conf->RB_pass1;
    if ($conf->RB_IsTest){
        $pass = $conf->RB_test1;
    }
    
    $client  = @$_SERVER['HTTP_CLIENT_IP'];
    $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
    $remote  = @$_SERVER['REMOTE_ADDR'];
    
    if(filter_var($client, FILTER_VALIDATE_IP)) $descr->ip = $client;
    elseif(filter_var($forward, FILTER_VALIDATE_IP)) $descr->ip = $forward;
    else $descr->ip = $remote;
    
    $descr->user = $user;
    //$descr->browser = get_browser();
    $descr->browser = "";
    $descr->useragent = @$_SERVER['HTTP_USER_AGENT'];

    $db = JFactory::getDBO();
    $q = $db->getQuery(true);
    $q  ->insert('#__pubmedtracker_robokassa')
        ->columns('user_id, summ, descr')
        ->values(implode(',', array($user->id, $addbalance , "'".json_encode($descr)."'")));
    $db->setQuery($q);
    $db->query();
    $InvId = $db->insertid();

    $result->MerchantLogin = $conf->RB_MerchantLogin;
    $result->OutSum = $addbalance;
    $result->InvId = $InvId;
    $result->Description = 'Пополнение баланса аккаунта PubMed Tracker';
    $result->Email = $user->email;
    
    
    $result->SignatureValue = md5("$conf->RB_MerchantLogin:$addbalance:$InvId:$pass");;

    
    
    $result->response = 1;
    $message = "Успех";
    exit($this->responseJSON($message, $result));
}


    
    //new
    function editquery(){ 
        $user = JFactory::getUser();
        if ($user->get('guest') == 1) {
            $message = "Не авторизован!";
            exit($this->responseErrorJSON($message, []));
        }

        $result = new \stdClass;

        $query_name = $this->input->get("name", "", "STRING");
        $query_string = $this->input->get("query", "", "STRING");
        $query_days = $this->input->get("day", 1, "INT");
        $query_id = $this->input->get("id", 0, "INT");
        
        $result->query_name = $query_name;
        $result->query_string = $query_string;
        $result->query_days = $query_days;
        $result->query_id = $query_id;

        if (!is_numeric($query_id)){
            $message = "id не является числом!";
            exit($this->responseErrorJSON($message, $result));
        }

        if (!is_numeric($query_days)){
            $message = "Количество дней не является числом!";
            exit($this->responseErrorJSON($message, $result));
        }

        if ($query_id > 0){ //edit
            $db = JFactory::getDBO();
            $q = $db->getQuery(true);
                        
            $q->update('#__pubmedtracker_users_querys')
                    ->where($db->qn('user_id').' = '.$db->q($user->id).' AND '.$db->qn('id').' = '.$db->q($query_id));
            
            //$q->set($db->qn('query').' = '.$db->q($db->escape($query_string)));
            $q->set($db->qn('query').' = '.$db->q($query_string));
            $q->set($db->qn('number_days').' = '.$db->q($query_days));
            $q->set($db->qn('name').' = '.$db->q($query_name));
            $q->set($db->qn('upd_timestamp').' = CURRENT_TIMESTAMP()');
            $q->set($db->qn('last_query_timestamp').' = NULL');
            $db->setQuery($q);
            $db->query();

            $message = "Строка запроса успешно изменена!";
        }
        else{
            $message = "Не верный параметр id запроса";
            exit($this->responseErrorJSON($message, $result));
        }
        exit($this->responseJSON($message, $result));
        /*
        if ($query_id > 0){ //edit
            $db = JFactory::getDBO();
            $q = $db->getQuery(true);
                        
            $q->update('#__pubmedtracker_users_querys')
                    ->where('user_id='.$user->id.' AND id='.$query_id);
            
            $q->set('query="'.$query_string.'"');
            $q->set('number_days='.$query_days);
            $q->set('upd_timestamp=CURRENT_TIMESTAMP()');
            $q->set('last_query_timestamp=NULL');
                    
            $db->setQuery($q);
            $db->query();

            $message = "Строка запроса успешно изменена!";
        }
        else{ //add
            
            $db = JFactory::getDBO();
            $q = $db->getQuery(true);
            $q  ->insert('#__pubmedtracker_users_querys')
                ->columns('user_id, query, number_days')
                ->values(implode(',', array($user->id, "'".$query_string."'" , $query_days)));
            $db->setQuery($q);
            $db->query();
            $db->insertid();
            
            $message = "Строка запроса успешно добавлена!";
        }
        */
    }


    
    function deletequery(){
        $user = JFactory::getUser();
        if ($user->get('guest') == 1) {
            $message = "Не авторизован!";
            exit($this->responseErrorJSON($message, []));
        }

        $result = new \stdClass;
        
        $query_id = $this->input->get("id", 0, "INT");
        $result->query_id = $query_id;

        if (!is_numeric($query_id)){
            $message = "id не является числом!";
            exit($this->responseErrorJSON($message, []));
        }

        if ($query_id > 0){ //edit
            $db = JFactory::getDBO();
            $q = $db->getQuery(true);
                        
            $q->update('#__pubmedtracker_users_querys')
                    ->where('user_id='.$user->id.' AND id='.$query_id);
            
            //$q->set('del = 1');
            $q->set('status = 0');
            $q->set('query=""');
            $q->set('number_days=1');
            $q->set('name=""');
            $q->set('upd_timestamp=CURRENT_TIMESTAMP()');
            $q->set('last_query_timestamp=NULL');
                    
            $db->setQuery($q);
            $db->query();

            $message = "Строка запроса успешно очищена!";
        }
        else{
            $message = "Не верный id";
            exit($this->responseErrorJSON($message, []));
        }

        exit($this->responseJSON($message, $result));
    }




    //new
    function activequery(){
        $result = new \stdClass;
        $result->response = 0;
        $result->reload = 0;

        $query_id = $this->input->get("id", 0, "INT");
        $status = $this->input->get("status", 55, "INT");
        
        $result->query_id = $query_id;

        if (!is_numeric($query_id)){
            $message = "id не является числом!";
            exit($this->responseErrorJSON($message, []));
        }

        if (!is_numeric($status)){
            $message = "Неверный статус!";
            exit($this->responseErrorJSON($message, []));
        }

        if ($status > 1){
            $message = "status не корректный!";
            exit($this->responseErrorJSON($message, []));
        }

        $user = JFactory::getUser();
        if ($user->get('guest') == 1) {
            $message = "Не авторизован!";
            exit($this->responseErrorJSON($message, []));
        }

        if ($query_id > 0){
            //$status = !$status; //инвертировать статус
            if ($status == 1){
                $status = 0;
            }else{
                $status = 1;
            }
            if ($status == 1){
                $db = JFactory::getDBO();
                $sql = 'SELECT * FROM #__pubmedtracker_users_profiles WHERE user_id = '.$user->id;
                $db->setQuery($sql);
                $user_profile = $db->loadAssocList();
                if (count($user_profile)<1){
                    $message = "Отсутствует профиль пользователя";
                    exit($this->responseErrorJSON($message, []));
                }
                $user_profile = $user_profile[0];

                //$result->user_profile = $user_profile;

                $period_beg = strtotime($user_profile["period_beg"]);
                $period_end = strtotime($user_profile["period_end"]);
                $now = strtotime("now");

                if (($now < $period_beg)||($now > $period_end)){
                    $message = "Подписка не активна. Необходимо продлить период действия!";
                    exit($this->responseErrorJSON($message, []));
                }

               
                // нужно проверить тариф на количество подписок
                // запрашиваем количество активированных строк запросов
                $db = JFactory::getDBO();
                $sql = 'SELECT COUNT(*) as C FROM #__pubmedtracker_users_querys 
                        WHERE user_id = '.$user->id.' 
                        AND status = 1 
                        AND del = 0';
                $db->setQuery($sql);
                $C = $db->loadAssocList();

                $C = $C[0]["C"];
                //$result->C = $C;

                
                //запрашиваем тариф
                $db = JFactory::getDBO();
                $sql = 'SELECT * FROM #__pubmedtracker_tarifes 
                        WHERE status = 1 
                        AND tarife_id = '.$user_profile["tarife_id"];
                $db->setQuery($sql);
                $tarife = $db->loadAssocList();
                
                if (count($tarife)<1){
                    $message = "Ваш тариф более не действует. Выберите другой тариф!";
                    exit($this->responseErrorJSON($message, []));
                }
                $tarife = $tarife[0];
                
                //$result->tarife = $tarife;
                
                if ((INT)$tarife["count_query"] <= (INT)$C){
                    $message = "У вас уже активированно максимально возможное количество строк запросов!";
                    exit($this->responseErrorJSON($message, []));
                }

                $message = "Активирована";
            }
            else{
                $message = "Отключена";
            }
            
            $result->reload = 1;

            $db = JFactory::getDBO();
            $q = $db->getQuery(true);
            $q->update('#__pubmedtracker_users_querys')
                    ->where('user_id='.$user->id.' AND id='.$query_id);
            $q->set('status = '.$status);
            $q->set('upd_timestamp=CURRENT_TIMESTAMP()');
            $q->set('last_query_timestamp=NULL');
                    
            $db->setQuery($q);
            $db->query();

        }
        else{
            $message = "Неверный id";
            exit($this->responseErrorJSON($message, []));
        }

        $result->response = 1;
        exit($this->responseJSON($message, $result));
    }



    
    //new иправил под новый интерфейс
    function setnotifnull(){
        $user = JFactory::getUser();
        if ($user->get('guest') == 1) {
            $message = "Не авторизован!";
            exit($this->responseErrorJSON($message, []));
        }

        $result = new \stdClass;
        $result->response = 0;

        $status = $this->input->get("status", 5, "INT");
        $result->$status;

        if (!is_numeric($status)){
            $message = "status не является числом!";
            exit($this->responseErrorJSON($message, $result));
        }

        if ($status <= 1){
            //$status = !$status; //инвертировать статус
            if ($status == 1){
                $status = 0;
            }else{
                $status = 1;
            }
            
            $db = JFactory::getDBO();
            $q = $db->getQuery(true);
            $q->update('#__pubmedtracker_users_profiles')
                    ->where('user_id='.$user->id);
            $q->set('notif_null="'.$status.'"');
            $db->setQuery($q);
            $db->query();
            $message = "Успех!";
        }
        else{
            $message = "status не корректный!";
            exit($this->responseErrorJSON($message, $result));
        }

        $result->response = 1;
        
        exit($this->responseJSON($message, $result));
    }




    public function display($cachable = false, $urlparams = array()) {

        $user = JFactory::getUser();

        if ($user->get('guest') == 1) {
            $this->setRedirect(JRoute::_('login?return=' . base64_encode(JUri::current()), "You must be logged in to view this content"));
            return;
        }

        parent::display($cachable, $urlparams);
    }


}

?>