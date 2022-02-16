<?php
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');

class PubMedTrackerViewPubMedTracker extends JViewLegacy
{
    function display($tpl = null)
    {

        $user = JFactory::getUser();

        $db = JFactory::getDBO();
        $sql = 'SELECT * FROM #__pubmedtracker_users_profiles WHERE user_id = ' . $user->id;
        $db->setQuery($sql);
        $rows = $db->loadAssocList();

        if (count($rows) > 0) {
            $row = $rows[0];

            //текущий тариф пользователя
            $db = JFactory::getDBO();
            $sql = 'SELECT * FROM #__pubmedtracker_tarifes WHERE tarife_id = ' . $row["tarife_id"];
            $db->setQuery($sql);
            $current_tarife = $db->loadAssocList();

            if (count($current_tarife) > 0) {
                //$this->assign("tarife", $current_tarife[0]["name"] . "(запросов: " . $current_tarife[0]["count_query"] . " | " . $current_tarife[0]["price"] . "руб/мес)");
                $this->assign("tarife_name", $current_tarife[0]["name"]);
                $this->assign("tarife_price", $current_tarife[0]["price"]);
                $this->assign("tarife_count_query", $current_tarife[0]["count_query"]);
                
            } else {
                $this->assign("tarife_name", "");
                $this->assign("tarife_price", 0);
            }

            $this->assign("sent_status", $row["sent_status"]);
            $this->assign("auto_renewal", $row["auto_renewal"]);
            $this->assign("email", $row["email"]);
            $this->assign("tarife_id", $row["tarife_id"]);
            $this->assign("period_beg", $row["period_beg"]);
            $this->assign("period_end", $row["period_end"]);
            $this->assign("balance", $row["balance"]);
            $this->assign("notif_null", $row["notif_null"]);


            // запрашиваем строки запросов
            $db = JFactory::getDBO();
            $sql = 'SELECT id, user_id, name, query, status, add_timestamp, DATE_FORMAT(upd_timestamp,\'%d.%m.%Y %H:%i\') as upd_timestamp, number_days
              , DATE_FORMAT(last_query_timestamp,\'%d.%m.%Y %H:%i\') as last_query_timestamp FROM #__pubmedtracker_users_querys 
              WHERE del = 0 AND user_id = ' . $user->id . ' ORDER BY id';
            $db->setQuery($sql);
            $UserQuerys = $db->loadAssocList();

            if (count($UserQuerys) > 0) {
                $this->assign("UserQuerys", $UserQuerys);
            } else {
                $this->assign("UserQuerys", []);
            }


            $jinput = JFactory::getApplication()->input;
            $RB_message = $jinput->get("RBmessage", '', "string");
            $this->assign("RB_message", $RB_message);
        } else { // это первый вход в приложение
            $this->assign("UserQuerys", []);

            $this->assign("sent_status", 0);
            $this->assign("auto_renewal", 1);
            $this->assign("email", $user->email);
            $this->assign("tarife_id", 0);
            $this->assign("tarife_name", "");
            $this->assign("tarife_price", 0);
            $this->assign("tarife_count_query", 0);
            
            $this->assign("period_beg", NULL);
            $this->assign("period_end", NULL);
            $this->assign("notif_null", 1);

            $StartBalance = 0;

            $this->assign("balance", $StartBalance);

            $db = JFactory::getDBO();
            $query = $db->getQuery(true);
            $query
                ->insert('#__pubmedtracker_users_profiles')
                ->columns('user_id, balance, sent_status, auto_renewal, email, tarife_id')
                ->values(implode(',', array($user->id, $StartBalance, 0, 1, "'" . $user->email . "'", 0)));
            $db->setQuery($query);
            $db->query();
            $db->insertid();


            // добавляем запись в о возврате
            $db = JFactory::getDBO();
            $query = $db->getQuery(true);
            $query
                ->insert('#__pubmedtracker_balance_history')
                ->columns('user_id, value, descr')
                ->values(implode(',', array($user->id, $StartBalance, "'Стартовый баланс'")));
            $db->setQuery($query);
            $db->query();
            $db->insertid();
        }

        //все тарифы
        $db = JFactory::getDBO();
        $sql = 'SELECT * FROM #__pubmedtracker_tarifes
                      WHERE CURDATE() BETWEEN date_beg AND date_end
                      AND status = 1
                      ORDER BY count_query';
        $db->setQuery($sql);
        $tarifes = $db->loadAssocList();
        if (count($tarifes) < 1) {
            $this->assign("tarifes", []);
        }
        else {
            $this->assign("tarifes", $tarifes);
        }
        //var_dump($user);
        parent::display($tpl);
    }
}
