<?php

use Joomla\CMS\Factory;
defined('_JEXEC') or die('Restricted access');
$conf = new JConfig;


$doc = Factory::getDocument();


$doc->addStyleSheet("components/com_pubmedtracker/assets/css/pubmedtracker.min.css");
$doc->addScript("components/com_pubmedtracker/assets/js/pubmedtracker.js", array("version" => "auto"), array("defer" => "defer"));
/*
echo '<pre>';
var_dump($this);
echo '</pre>';
exit;
*/ 
?>


<svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
  <symbol id="check-circle-fill" fill="currentColor" viewBox="0 0 16 16">
    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
  </symbol>
  <symbol id="info-fill" fill="currentColor" viewBox="0 0 16 16">
    <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
  </symbol>
  <symbol id="exclamation-triangle-fill" fill="currentColor" viewBox="0 0 16 16">
    <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
  </symbol>
</svg>


<div class="row">
    <div class="col-12 d-flex flex-row justify-content-end">
        <div class="pmt-profile-btn">
            <a href="/profile" class="d-flex flex-row align-items-center">
                <img src="/components/com_pubmedtracker/assets/img/user.svg" alt="" class="pe-3">
                <p class="m-0">
                    Профиль
                </p>
            </a>
        </div>
        <div class="pmt-profile-btn">
            <a href="/logout" class="d-flex flex-row align-items-center">
                <img src="/components/com_pubmedtracker/assets/img/logout.svg" alt="" class="pe-3">
                <p class="m-0">
                    Выход
                </p>
            </a>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <h2>
            PubMedTracker
        </h2>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <p class="m-0">
            Сервис отслеживает нужные вам статьи из PubMed и отправляет их список на почту 
            <a href="#" class="pmt-a-blue" data-bs-toggle="modal" data-bs-target="#ModalAbout">подробнее о сервисе</a>
        </p>
    </div>
</div>

<div class="row pt-4">
    <div class="col-12 d-flex flex-column flex-md-row justify-content-md-between p-0">
        <div class="pmt-block-email mb-3 mb-md-0 me-0 me-md-3">
            <p class="pmt-p-grey">
                E-mail для получения рассылки:
            </p>
            <p class="pmt-p-email">
                <span id="email-front"><?=$this->email?></span>
                <span id="btn-setemail" class="pmt-a-blue">изменить</span>
            </p>
            <div class="d-flex flex-row align-items-center">
                
                <?php 
                    if ($this->notif_null == 1){
                        echo '<div id="notif-null" class="toggle-wrapper-small ton" data-status="1"><div class="circle-small on"></div></div>';
                    }else{
                        echo '<div id="notif-null" class="toggle-wrapper-small toff" data-status="0"><div class="circle-small off"></div></div>';
                        
                    }
                ?>
                <p class="pmt-p-toggle-email">Отправлять уведомления о проверке базы, даже если новых статей нет</p>
            </div>

            <div class="d-flex pt-1">
                <div class="d-flex align-items-start justify-content-end" style="min-width: 40px;">
                    <img src="/components/com_pubmedtracker/assets/img/!.svg" alt="!">
                </div>
                
                <p class="pmt-p-grey pmt-p-descr">
                    Письма отправляются с указанием названия поиска и датой отпраки в заголовке
                </p>
            </div>
        </div>

        <div class="pmt-block-tarife">
            <div class="row">
                <div class="col-5">
                    <p class="pmt-p-grey">
                        Баланс:
                    </p>
                    <div class="d-flex flex-row align-items-center pb-3">
                        <p class="pmt-balance">
                            <?=$this->balance?> руб.
                        </p>
                        <img id="btn-addbalance" src="/components/com_pubmedtracker/assets/img/+.svg" alt="+">
                    </div>

                    <span id="pmt-balance-history" class="d-flex flex-row align-items-center pmt-balance-history">
                        <img src="/components/com_pubmedtracker/assets/img/file-text.svg" alt="file-text">
                        <span>
                            история пополнений
                        </span>
                    </span>
                </div>

                <div class="col-7">
                    <p class="pmt-p-grey">
                        Тариф:
                    </p>
                    <div class="row pb-3">
                        <div class="col-6">
                            <p class="pmt-tarife">
                                <span id="pmt-tarife">
                                    <?php 
                                    if ($this->tarife_id != 0){
                                        echo $this->tarife_name;
                                    }
                                    else
                                    {
                                        echo "Не выбран";
                                    }
                                    ?>
                                </span>
                            </p>
                        </div>
                        <div class="col-6">
                            <p class="pmt-tarife">
                                <?php 
                                if ($this->tarife_id != 0){
                                    echo $this->tarife_price;
                                }
                                else
                                {
                                    echo "X";
                                }
                                ?>
                                <span> Руб. / мес.</span>
                            </p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <p class="pmt-p-grey mb-1">
                                оплачено до
                            </p>
                            <p>
                                <?php 
                                if ($this->period_end != NULL){
                                    $date = new DateTime($this->period_end);
                                    echo $date->format('d.m.Y');
                                }
                                ?>
                            </p>
                        </div>
                        <div class="col-6">
                            <p class="pmt-p-grey mb-1">
                                автопродление
                            </p>
                            <?php
                            if($this->auto_renewal==1){
                                echo '<div id="auto-renewal" class="toggle-wrapper-small ton" data-status="1"><div class="circle-small on"></div></div>';
                            }else{
                                echo '<div id="auto-renewal" class="toggle-wrapper-small toff" data-status="0"><div class="circle-small off"></div></div>';
                            }
                            ?>
                        </div>
                    </div>

                    
                </div>
            </div>
        </div>
    </div>
</div>


<?php
$i=0;
if (count($this->UserQuerys)>0){
    foreach ($this->UserQuerys as $UQ){
        $i++;
        if ($i > $this->tarife_count_query){
            break;
        }
?>

<div class="row py-3">
    <div class="col-12 pmt-row-query" data-id-query="<?=$UQ['id']?>">
        <div class="row">

            <div class="col-1 d-flex justify-content-start align-items-center">
                <?php
                    if ($UQ['status']==1){
                        echo '<div class="toggle-query toggle-wrapper-small ton" data-id-query='.$UQ['id'].' data-status="1"><div class="circle-small on"></div></div>';
                    }else{
                        echo '<div class="toggle-query toggle-wrapper-small toff" data-id-query='.$UQ['id'].' data-status="0"><div class="circle-small off"></div></div>';
                    }
                ?>
            </div>

            <div class="col-10">
                <div class="row">
                    <div class="col-9 col-lg-3">
                        <div class="d-flex align-items-center justify-content-start pb-1">
                            <p class="pmt-p-grey pmt-p-descr">
                                Название поиска
                            </p>
                            <div class="d-flex align-items-center justify-content-end" style="min-width: 40px;">
                                <img src="/components/com_pubmedtracker/assets/img/!.svg" alt="!">
                            </div>
                        </div>
                        
                        
                        <div class="input-group pmt-input-name">
                            <?php
                                /*
                                if ($UQ['name']==''){
                                    echo '<input type="text" class="form-control pmt-input-q" aria-describedby="input-name-'.$UQ['id'].'" value="Поиск" data-id-query="'.$UQ['id'].'" data-type="name">';
                                }else{
                                    echo '<input type="text" class="form-control pmt-input-q" aria-describedby="input-name-'.$UQ['id'].'" value="'.$UQ['name'].'" data-id-query="'.$UQ['id'].'" data-type="name">';
                                }
                                */
                                echo '<input type="text" class="form-control pmt-input-q" aria-describedby="input-name-'.$UQ['id'].'" value="'.htmlspecialchars($UQ['name']).'" data-id-query="'.$UQ['id'].'" data-type="name">';
                            ?>
                            <span class="input-group-text" id="input-name-<?=$UQ['id']?>"><img src="/components/com_pubmedtracker/assets/img/pencil.svg" alt="pencil"></span>
                        </div>


                        <div class="status-query d-flex align-items-center justify-content-start pt-1" data-id-query="<?=$UQ['id']?>">
                            <?php
                                if ($UQ['status']==1){
                            ?>
                            <div class="d-flex align-items-center justify-content-end">
                                <img src="/components/com_pubmedtracker/assets/img/play.svg" alt="play">
                            </div>
                            <p class="pmt-play-green ps-2">
                                работает
                            </p>
                            <?php
                                }else{
                            ?>
                            <div class="d-flex align-items-center justify-content-end">
                                <img src="/components/com_pubmedtracker/assets/img/pause.svg" alt="pause">
                            </div>
                            <p class="pmt-play-gray ps-2">
                                остановлен
                            </p>
                            <?php
                                }
                            ?>
                        </div>
                    </div>

                    <div class="col-3 col-lg-2">
                        <p class="pmt-p-grey pmt-p-descr text-center px-0 pb-1">
                            Проверка один&nbsp;раз&nbsp;в
                        </p>

                        <div class="input-group pmt-input-day">
                            <input type="text" class="form-control pmt-input-q" aria-describedby="input-day-<?=$UQ['id']?>" value="<?=$UQ['number_days']?>" data-id-query="<?=$UQ['id']?>" data-type="day">
                            <span class="input-group-text d-flex flex-column" id="input-day-<?=$UQ['id']?>">
                                <img class="pmt-day-updown" src="/components/com_pubmedtracker/assets/img/up.svg" alt="up" data-id-query="<?=$UQ['id']?>" data-type="up">
                                <img class="pmt-day-updown" src="/components/com_pubmedtracker/assets/img/down.svg" alt="down" data-id-query="<?=$UQ['id']?>" data-type="down">
                            </span>
                        </div>

                        <p class="pmt-p-grey pmt-p-descr text-center px-0 pt-1">
                            дней
                        </p>
                    </div>

                    <div class="col-12 col-lg-7">
                        <div class="pb-1">
                            <p class="pmt-p-grey pmt-p-descr">
                                Введите запрос
                            </p>
                        </div>
                        
                        
                        <div class="input-group pmt-input-query">
                            <input type="text" class="form-control pmt-input-q" aria-describedby="input-query-<?=$UQ['id']?>" value="<?=htmlspecialchars($UQ['query'])?>" data-id-query="<?=$UQ['id']?>" data-type="query">
                            <span class="input-group-text" id="input-query-<?=$UQ['id']?>"><img src="/components/com_pubmedtracker/assets/img/enter.svg" alt="enter"></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-1 d-flex justify-content-end">
                <img class="pmt-img-trash" src="/components/com_pubmedtracker/assets/img/trash.svg" alt="trash" data-id-query="<?=$UQ['id']?>">
            </div>

        </div>
    </div>
</div>

<?php
    }//foreach ($this->UserQuerys as $UQ){
} //if (count($this->UserQuerys)>0){



// далее выводим тарифы на которые можно перейти
foreach ($this->tarifes as $tarife){
    if ($tarife['count_query'] > $this->tarife_count_query){
?>

<div class="row py-3">
    <div class="col-12 pmt-row-query d-flex align-items-center">
        <div class="pe-5">
            <img class="pmt-addtarife" src="/components/com_pubmedtracker/assets/img/plus-circle.svg" alt="+" data-tarife-id="<?=$tarife['tarife_id']?>">
        </div>
        <div class="pe-5">
            <p class="m-0">
                Добавление активирует тариф 
            </p>
            <p class="m-0 pmt-tarife">
                «<?=$tarife['name']?>»
            </p>
        </div>
        <div>
            <p class="m-0">
                Стоимость тарифа
            </p>
            <p class="m-0 pmt-tarife">
                <?=$tarife['price']?> руб.&nbsp;/&nbsp;мес.
            </p>
        </div>
    </div>
</div>
<?php
    }//if ($tarife['count_query'] > $this->tarife_count_query){
} //foreach ($this->tarifes as $tarife){
?>












<div id="modal-balance-history" class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modal-balance-history-Label" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modal-balance-history-Label">История пополнения/списания баланса</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        ...
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Закрыть</button>
      </div>
    </div>
  </div>
</div>


<div id="modal-addbalance" class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modal-addbalance-Label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-addbalance-Label">Пополнить баланс</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="send-form-addbalance" action="https://auth.robokassa.ru/Merchant/Index.aspx" method="POST">
                    <?php
                        if ($conf->RB_IsTest)
                        {
                            echo "ТЕСТ ";
                        }
                    ?>
                   
                    <input id="addbalance" type="number" class="form-control" value="100">
                    <input type="hidden" name="MerchantLogin" value="BIOINN_PubMed_Tracker">
                    <input type="hidden" name="Encoding" value="utf-8">
                    <input type="hidden" name="OutSum" value="0">
                    <input type="hidden" name="InvId" value="x">
                    <input type="hidden" name="Description" value="x">
                    <input type="hidden" name="SignatureValue" value="x">
                    <?
                        if ($conf->RB_IsTest)
                        {
                            echo "<input type='hidden' name='IsTest' value='1'>";
                        }
                    ?>
                </form>
            </div>
            <div class="modal-footer">
                <button id="addbalance-submit" type="button" class="btn btn-success">Пополнить</button>
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Отмена</button>
            </div>
        </div>
    </div>
</div>




<div id="modal-setemail" class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modal-setemail-Label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-setemail-Label">Сменить E-mail</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>
                    Укажите новый E-mail адрес, на который хотите получать рыссылки.
                </p>
                <input id="setemail" class="form-control" type="email" value="<?=$this->email?>">                    
            </div>
            <div class="modal-footer">
                <button id="setemail-submit" type="button" class="btn btn-success">Сохранить</button>
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Отмена</button>
            </div>
        </div>
    </div>
</div>




<div id="modal-settarife" class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modal-settarife-Label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-settarife-Label">Смена тарифа</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="m-0">
                    <strong>Баланс:</strong> <span id="current-balance">-</span> руб
                </p>
                <p class="m-0">
                    <strong>Текущий тариф:</strong> <span id="current-tarife-name">-</span>
                </p>
                <p class="m-0">
                    <strong>Запросов:</strong> <span id="current-tarife-count-query">-</span>
                </p>
                <p class="m-0">
                    <strong>Стоимость: </strong><span id="current-tarife-price">-</span> руб/мес
                </p>
                <p class="m-0">
                    <strong>Статус:</strong> <span id="current-tarife-status">-</span>
                </p>
                <p class="m-0">
                    <strong>Действует: </strong><span id="current-tarife-date-beg">-</span> - <span id="current-tarife-date-end">-</span>
                </p>
                <div class="py-3">
                    <p class="m-0">
                        <strong>Выберите новый тариф: </strong>
                    </p>
                    <select id="tarife-select" class="form-select">
                        <option selected>Open this select menu</option>
                        <option value="1">One</option>
                        <option value="2">Two</option>
                        <option value="3">Three</option>
                    </select>
                </div>
                <p class="m-0">
                    <strong>Новый тариф:</strong> <span id="set-tarife-name">-</span>
                </p>
                <p class="m-0">
                    <strong>Запросов: </strong><span id="set-tarife-count-query">-</span>
                </p>
                <p class="m-0">
                    <strong>Стоимость нового периода: </strong><span id="set-tarife-price">-</span> руб/мес
                </p>
                <p class="m-0">
                    <strong>Возврат за неиспользуемые дни:</strong> <span id="set-tarife-vozvrat">-</span> руб (<span id="set-tarife-day">-</span> дней)
                </p>
                <p class="m-0">
                    <strong>Действие:</strong> <span id="set-tarife-date-beg">-</span> - <span id="set-tarife-date-end">-</span>
                </p>
                <p class="m-0">
                    <strong>Итого к списанию: </strong><span id="set-tarife-itogo">-</span> руб/мес
                </p>

            </div>
            <div class="modal-footer">
                <button id="settarife-submit" type="button" class="btn btn-success">Применить</button>
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Отмена</button>
            </div>
        </div>
    </div>
</div>





<div class="modal fade" id="ModalAbout" tabindex="-1" aria-labelledby="ModalAboutLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="ModalAboutLabel">PubMedTracker</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
          <div class="w-100 text-center py-3">
              <img src="templates/bioinn-template/images/svg/logo-bottom.svg" alt="bioinn">
          </div>
        <p>
            PubMedTracker - уникальный сервис, который по вашим запросам собирает подборки статей из PubMed и высылает на почту удобным списком со ссылками.
        </p>
        <p>
            <a href="https://pubmed.ncbi.nlm.nih.gov/" target="_blank">PubMed</a> - ведущая мировая база статей, книг и протоколов из областей биологии, медицины и смежных дисциплин.
        </p>
        <p>
            PubMedTracker работает через официальный API PubMed, запросы можно формировать с использованием <a href="https://pubmed.ncbi.nlm.nih.gov/help/#search-tags" target="_blank">стандартного синтаксиса PubMed</a>
        </p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Закрыть</button>
      </div>
    </div>
  </div>
</div>


<?php
    if ($this->RB_message != ''){
?>
        <script type="text/javascript">
            jQuery(function ($) {
                $("#system-message-container").html(
                '<div class="alert alert-success d-flex align-items-center alert-dismissible fade show" role="alert">' +
                '<svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Success:"><use xlink:href="#check-circle-fill"/></svg>' +
                '<div>' +
                '<?=$this->RB_message?>' +
                '</div>' +
                '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                '</div>'
                );
            });
            
            window.history.replaceState(null, null, window.location.pathname);
        </script>
<?php
    }
?>
