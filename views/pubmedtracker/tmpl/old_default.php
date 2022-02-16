<?php
defined('_JEXEC') or die('Restricted access');
$conf = new JConfig;
?>

<style>
.btn-danger, .btn-warning, .uk-button-danger {
    background-color: #585858;
}
</style>

<div class="block-rotates" style="display: none;">
    <div class="block-rotates-img">
        <img src="/images/logo-small.svg" alt="bioinn.ru" class="logo-rotates">
    </div>
</div>

<?php /*
<div class="uk-grid-small uk-child-width-expand@s" uk-grid>
    <p style="background: darkred; color:#FFF; padding: 0.5em">
        Альфа-тестирование. Все данные будут удалены!
    </p>
</div>
*/ ?>
<div class="uk-grid-small uk-child-width-expand@s" uk-grid>
    <div>
        <div class="uk-card uk-card-default uk-flex uk-flex-column">
            <span>
                Почта для получения рассылок:
            </span>
            <p>
                E-mail: <strong id="email-front"><?=$this->email?></strong>
            </p>
            <button uk-toggle="target: #modal-setemail" class="uk-button uk-button-danger uk-button-small" uk-tooltip="title: Сменить E-mail">
                Сменить E-mail
            </button>

        </div>
    </div>
    <div>
        <div class="uk-card uk-card-default uk-flex uk-flex-column">
            <div class="uk-flex uk-flex-middle uk-flex-wrap-between">
                <span>
                    Баланс: <strong id="addbalance-front"><?=$this->balance?></strong>
                </span>
            
                <button uk-toggle="target: #modal-addbalance" class="uk-button uk-button-danger uk-button-small uk-margin-left" uk-tooltip="title: Пополнить баланс">
                    Пополнить
                </button>
            </div>
            <div class="uk-margin-top">
                <a uk-toggle="target: #modal-balance-history" class="a-my-menu" onclick="gethistory();">
                    История пополнения/списания
                </a>
            </div>
            <div class="uk-flex uk-flex-column">
                <span class="uk-margin-top">
                    Действует с 
                    <strong>
                        <?php 
                            if ($this->period_beg != NULL){
                                $date = new DateTime($this->period_beg);
                                echo $date->format('d.m.Y');
                            }
                        ?>
                    </strong> по 
                    <strong>
                        <?php 
                            if ($this->period_end != NULL){
                                $date = new DateTime($this->period_end);
                                echo $date->format('d.m.Y');
                            }
                        ?>
                    </strong>
                </span>
                <p>
                    Автопродление: <?php
                        if($this->auto_renewal==1){
                            echo '<span style="background: darkgreen; color:#FFF; padding: 0.5em">Включено</span>';
                        }else{
                            echo '<span style="background: darkred; color:#FFF; padding: 0.5em">Отключено</span>';
                        }
                    ?>
                </p>
                <span id="autorenewal-front">
                    <?php
                        if($this->auto_renewal==1){
                            echo '<button onclick="autorenewal(\'off\')" class="uk-button uk-button-danger uk-button-small" uk-tooltip="title: Отключить автопродление" style="width: 100%;">Отключить автопродление</button>';
                        }else{
                            echo '<button onclick="autorenewal(\'on\')" class="uk-button uk-button-danger uk-button-small" uk-tooltip="title: Включить автопродление" style="width: 100%;">Включить автопродление</button>';
                        }
                    ?>
                </span>
            </div>
        </div>
    </div>
    <div>
        <div class="uk-card uk-card-default uk-flex uk-flex-column">
            <div class="uk-flex uk-flex-column">
                <p class="uk-margin-top">
                    Рассылка: 
                    <?php 
                        if ($this->sent_status == 1){
                            echo '<span style="background: darkgreen; color:#FFF; padding: 0.5em">Активирована</span>';
                        }
                        else
                        {
                            echo '<span style="background: darkred; color:#FFF; padding: 0.5em">Отключена</span>';
                        }
                    ?>
                </p>
                <?php 
                    if ($this->sent_status == 1){
                        echo '<button onclick="sentstatus(\'off\')" class="uk-button uk-button-danger uk-button-small" uk-tooltip="title: Отключить рассылку">Отключить рассылку</button>';
                    }
                    else
                    {
                        echo '<button onclick="sentstatus(\'on\')" class="uk-button uk-button-danger uk-button-small" uk-tooltip="title: Включить рассылку">Включить рассылку</button>';
                    }
                ?>
                
            </div>
            <div class="uk-flex uk-flex-column">
                <p class="uk-margin-top">
                    Тариф: 
                    <strong>
                        <?php 
                            if ($this->tarife_id != 0){
                                echo $this->tarife;
                            }
                            else
                            {
                                echo "Не выбран";
                            }
                        ?>
                    </strong>
                </p>
                <button onclick="gettarifre();" class="uk-button uk-button-danger uk-button-small" uk-tooltip="title: Сменить тариф">
                    <?php 
                        if ($this->tarife_id != 0){
                            echo "Сменить тариф";
                        }
                        else
                        {
                            echo "Выбрать тариф";
                        }
                    ?>
                </button>
            </div>
        </div>
    </div>
</div>





<div class="uk-grid-small uk-child-width-expand@s" uk-grid>
    <div>
        <div class="uk-card uk-card-default uk-flex uk-flex-column">
            <p uk-margin>
                <button uk-toggle="target: #modal-editquery" class="uk-button uk-button-danger uk-button-small add-query-btn" uk-tooltip="title: Добавить новый запрос">
                    <span class="uk-margin-small-right" uk-icon="plus"></span> Добавить новый запрос
                </button>
            </p>
        <?php
            if (count($this->UsersQuerys)>0){
        ?>
            <table class="uk-table uk-table-hover uk-table-divider uk-table-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Операции</th>
                        <th>Строка запроса</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $i=0;
                        foreach ($this->UsersQuerys as $UQ){
                            $i++;
                    ?>
                            <tr class="query-tr">
                                <td>
                                    <?php
                                        echo $i;
                                        echo '&nbsp;|&nbsp;';
                                        if ($UQ['status']==1){
                                            echo '<span style="background: darkgreen; color:#FFF; padding: 0.5em" uk-tooltip="title: Запрос активен">ON</span>';
                                        }else{
                                            echo '<span style="background: darkred; color:#FFF; padding: 0.5em" uk-tooltip="title: Запрос отключен">OFF</span>';
                                        }
                                    ?>
                                </td>
                                <td>
                                    <p>
                                        <button class="edit-query-btn uk-button uk-button-danger uk-button-small" data-query-id="<?=$UQ['id']?>" uk-toggle="target: #modal-editquery" uk-tooltip="title: Редактировать">Редактировать</button>
                                        <?php
                                            if ($UQ['status']==1){
                                                //echo '<a href="#" class="uk-icon-button" uk-tooltip="title: Отключить" uk-icon="ban" onclick = "activequery(0,'.$UQ['id'].'); return false;"></a>';
                                                echo '<button class="uk-button uk-button-danger uk-button-small" uk-tooltip="title: Отключить" onclick = "activequery(0,'.$UQ['id'].'); return false;">Отключить</button>';
                                            }else{
                                                //echo '<a href="#" class="uk-icon-button" uk-tooltip="title: Активировать" uk-icon="play-circle" onclick = "activequery(1,'.$UQ['id'].'); return false;"></a>';
                                                echo '<button class="uk-button uk-button-danger uk-button-small" uk-tooltip="title: Активировать" onclick = "activequery(1,'.$UQ['id'].'); return false;">Активировать</button>';
                                            }
                                        ?>                                        
                                        <!-- 
                                            <a href="#" data-query-id="<?=$UQ['id']?>" uk-toggle="target: #modal-editquery" class="uk-icon-button edit-query-btn" uk-tooltip="title: Редактировать" uk-icon="file-edit"></a> 
                                            <a href="#" data-query-id="<?=$UQ['id']?>" uk-toggle="target: #modal-deletequery" class="uk-icon-button delete-query-btn" uk-tooltip="title: Удалить" uk-icon="trash"></a>
                                        -->
                                        <button class="delete-query-btn uk-button uk-button-danger uk-button-small" data-query-id="<?=$UQ['id']?>" uk-toggle="target: #modal-deletequery" uk-tooltip="title: Удалить">Удалить</button>
                                    </p>
                                    <p class="uk-margin-small text-small">
                                        Последнее редактирование: <strong><?=$UQ['upd_timestamp']?></strong>
                                    </p>
                                    <p class="uk-margin-small text-small">
                                        Последний запуск: <strong><?=$UQ['last_query_timestamp']?></strong>
                                    </p>
                                </td>
                                <td>
                                    <p class="uk-margin-small query-string" data-query-id="<?=$UQ['id']?>" data-query-string="<?//=$UQ['query']?>">
                                        <?=$UQ['query']?>
                                    </p>
                                    <a href="https://pubmed.ncbi.nlm.nih.gov/?term=<?=urlencode($UQ['query'])?>" class="a-my-menu text-small" target="_blank">ссылка на PubMed</a>
                                    <p class="uk-margin-small query-days" data-query-id="<?=$UQ['id']?>" data-query-days="<?=$UQ['number_days']?>">
                                        Частота проверки: <strong><?=$UQ['number_days']?> дн</strong>
                                    </p>
                                </td>
                            </tr>
                    <?php
                        } //foreach ($this->UsersQuerys as $UQ)
                    ?>
                </tbody>
            </table>
        <?php
            }// if (count($this->UsersQuerys)>0))
        ?>
        </div>
    </div>
</div>

<div class="uk-grid-small uk-child-width-expand@s" uk-grid>
    <div>
        <div class="uk-flex uk-flex-column">
            <p>
                <a class="a-my-menu" href="/pubmedtracker-terms-of-use" target="_blank">Пользовательское соглашение</a>
            </p>
        </div>
    </div>
</div>




<div id="modal-setemail" uk-modal>
    <div class="uk-modal-dialog">
        <button class="uk-modal-close-default" type="button" uk-close></button>
        <div class="uk-modal-header">
            <h2 class="uk-modal-title">Сменить E-mail</h2>
        </div>
        <div class="uk-modal-body">
            <p>
                Укажите новый E-mail адрес, на который хотите получать рыссылки.
            </p>
            <div class="uk-grid-small" uk-grid>
                <div class="uk-inline uk-width-1-1">
                    <span class="uk-form-icon" uk-icon="icon: mail"></span>
                    <input id="setemail" class="uk-input" type="email" value="<?=$this->email?>">
                </div>
            </div>
        </div>
        <div class="uk-modal-footer uk-text-right">
            <button class="uk-button uk-button-default uk-modal-close" type="button">Закрыть</button>
            <button class="uk-button uk-button-danger" type="button" onclick="setemail();">Сохранить</button>
        </div>
    </div>
</div>





<div id="modal-addbalance" uk-modal>
    <div class="uk-modal-dialog">
        <button class="uk-modal-close-default" type="button" uk-close></button>
        <div class="uk-modal-header">
            <h2 class="uk-modal-title">Пополнить баланс</h2>
        </div>
        <div class="uk-modal-body">
            <p>
                Пополните баланс.
            </p>
            <div class="uk-grid-small" uk-grid>
                <div class="uk-inline uk-width-1-1">
                    <form id="send-form-addbalance" action="https://auth.robokassa.ru/Merchant/Index.aspx" method="POST">
                        <?php
                            if ($conf->RB_IsTest)
                            {
                                echo "ТЕСТ ";
                            }
                        ?>
                        <span class="uk-form-icon" uk-icon="icon: cart"></span>
                        <input id="addbalance" class="uk-input" type="number" value="100">
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
            </div>
        </div>
        <div class="uk-modal-footer uk-text-right">
            <button class="uk-button uk-button-default uk-modal-close" type="button">Закрыть</button>
            <button class="uk-button uk-button-danger" type="button" onclick="addbalance();">Пополнить</button>
        </div>
    </div>
</div>


<div id="modal-balance-history" uk-modal>
    <div class="uk-modal-dialog">
        <button class="uk-modal-close-default" type="button" uk-close></button>
        <div class="uk-modal-header">
            <h2 class="uk-modal-title" style="font-size: 1.6em;">История пополнения/списания баланса</h2>
        </div>
        <div class="uk-modal-body">
            <div class="uk-text-center">
                <img src="/images/logo-small.svg" alt="bioinn.ru" class="logo-rotates">
            </div>
        </div>
        <div class="uk-modal-footer uk-text-right">
            <button class="uk-button uk-button-default uk-modal-close" type="button">Закрыть</button>
        </div>
    </div>
</div>



<div id="modal-settarife" uk-modal>
    <div class="uk-modal-dialog">
        <button class="uk-modal-close-default" type="button" uk-close></button>
        <div class="uk-modal-header">
            <h2 class="uk-modal-title">Смена тарифа</h2>
            
        </div>
        <div class="uk-modal-body">
            <p class="uk-margin-small">
                <strong>Баланс:</strong> <span id="current-balance">-</span> руб
            </p>
            <p class="uk-margin-small">
                <strong>Текущий тариф:</strong> <span id="current-tarife-name">-</span>
            </p>
            <p class="uk-margin-small">
                <strong>Запросов:</strong> <span id="current-tarife-count-query">-</span>
            </p>
            <p class="uk-margin-small">
                <strong>Стоимость: </strong><span id="current-tarife-price">-</span> руб/мес
            </p>
            <p class="uk-margin-small">
                <strong>Статус:</strong> <span id="current-tarife-status">-</span>
            </p>
            <p class="uk-margin-small">
                <strong>Действует: </strong><span id="current-tarife-date-beg">-</span> - <span id="current-tarife-date-end">-</span>
            </p>
            <div class="uk-margin">
                <div uk-form-custom="target: > * > span:first-child">
                    <select id="tarife-select">
                    </select>
                    <button class="uk-button uk-button-default" type="button" tabindex="-1">
                        <span></span>
                        <span uk-icon="icon: chevron-down"></span>
                    </button>
                </div>
            </div>
            <p class="uk-margin-small">
                <strong>Новый тариф:</strong> <span id="set-tarife-name">-</span>
            </p>
            <p class="uk-margin-small">
                <strong>Запросов: </strong><span id="set-tarife-count-query">-</span>
            </p>
            <p class="uk-margin-small">
                <strong>Стоимость нового периода: </strong><span id="set-tarife-price">-</span> руб/мес
            </p>
            <p class="uk-margin-small">
                <strong>Возврат за неиспользуемые дни:</strong> <span id="set-tarife-vozvrat">-</span> руб (<span id="set-tarife-day">-</span> дней)
            </p>
            <p class="uk-margin-small">
                <strong>Действие:</strong> <span id="set-tarife-date-beg">-</span> - <span id="set-tarife-date-end">-</span>
            </p>
            <p class="uk-margin-small">
                <strong>Итого к списанию: </strong><span id="set-tarife-itogo">-</span> руб/мес
            </p>
        </div>
        <div class="uk-modal-footer uk-text-right">
            <button class="uk-button uk-button-default uk-modal-close" type="button">Закрыть</button>
            <button class="uk-button uk-button-danger" type="button" onclick="settarife();">Применить</button>
        </div>
    </div>
</div>





<div id="modal-editquery" uk-modal>
    <div class="uk-modal-dialog">
        <button class="uk-modal-close-default" type="button" uk-close></button>
        <div class="uk-modal-header">
            <h2 class="uk-modal-title">Редактирование запроса</h2>
        </div>
        <div class="uk-modal-body">
            <p class="uk-margin-small uk-text-bold">
                Введите поисковой запрос для PubMed, включая необходимый синтаксис
            </p>
            <div class="uk-grid-small" uk-grid>
                <div class="uk-inline uk-width-1-1">
                    <textarea id="set-query" class="uk-textarea uk-margin-small" value="" rows="4"></textarea>
                </div>
                <p class="uk-margin-small">
                    пример: Berberine[tiab] (pathway OR signaling) "mitochondrial function"
                </p>
            </div>
            <p class="uk-margin-small uk-text-bold">
                Количество дней
            </p>
            <div class="uk-grid-small" uk-grid>
                <div class="uk-inline uk-width-1-1">
                    <input id="set-days" class="uk-input" type="number" value="1">
                </div>
            </div>
            <p class="uk-margin-small">
                Каждые Х дней выполняется запрос материалов за Х дней.
            </p>
            <input type="hidden" id="set-query-id" value="0">
        </div>
        <div class="uk-modal-footer uk-text-right">
            <button class="uk-button uk-button-default uk-modal-close" type="button">Закрыть</button>
            <button class="uk-button uk-button-danger" type="button" onclick="setquery();">Сохранить</button>
        </div>
    </div>
</div>





<div id="modal-deletequery" uk-modal>
    <div class="uk-modal-dialog">
        <button class="uk-modal-close-default" type="button" uk-close></button>
        <div class="uk-modal-header">
            <h2 class="uk-modal-title">Удалить запрос?</h2>
        </div>
        <div class="uk-modal-body">
            <p id="del-query" class="uk-margin-small">
                Строка запроса
            </p>
            
            <p id="del-days" class="uk-margin-small">
                Количество дней
            </p>
            
            <input type="hidden" id="del-query-id" value="0">
        </div>
        <div class="uk-modal-footer uk-text-right">
            <button class="uk-button uk-button-default uk-modal-close" type="button">Закрыть</button>
            <button class="uk-button uk-button-danger" type="button" onclick="deletequery();" style="background: darkred; color:#FFF;">Удалить</button>
        </div>
    </div>
</div>





<style>
    #tm-main{
        padding-top: 1em;
        padding-bottom: 0;
    }
    .uk-card{
        padding: 0.5em;
    }
    .uk-button-primary {
        background-color: #0a0a0a;
    }

    .a-my-menu {
        border-bottom: 1px dotted #000;
        margin: 0 0.3em;
        white-space: nowrap;
    }
    .a-my-menu:hover {
        border-bottom: 1px solid #909090;
    }

    @keyframes spin {
        from {
            transform: rotate(0deg);
        }
        to {
            transform: rotate(360deg);
        }
    }
    .logo-rotates{
        animation: spin 1s linear 0s infinite;
        width: 3em;
    }
    .block-rotates{
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        z-index: 228;
        background: rgb(192 191 255 / 30%);
        width: 100%;
        height: 100%;
    }
    .block-rotates-img{
        position: fixed;
        width: 100vw;
        height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .text-small{
        font-size: 0.7em;
    }
</style>


<script>


function settarife(){
    jQuery(function($) {
        var tarife_id = $("#tarife-select").val();
        if (tarife_id > 0)
        {
            UIkit.modal("#modal-settarife").hide();
            $(".block-rotates").show();
            $.ajax({
                type: "GET",
                cache: false,
                processData: false,
                dataType: "json",
                contentType: "application/json",
                url: "/component/pubmedtracker?task=settarife&tarife="+tarife_id,
                success: function (response) {
                    //console.log(response);
                    $(".block-rotates").hide();
                    UIkit.modal.alert(response.message);
                    
                    if (response.data.reload == 1){
                        setTimeout(function(){location.reload();}, 2500);
                    }

                },
                error: function(response){
                    console.log(response);
                    $(".block-rotates").hide();
                    UIkit.modal.alert('Ошибка выполнения запроса!');
                }
            });
        }
        
    });
}


function setemail(){
    jQuery(function($) {
        var email = $("#setemail").val();
        //console.log(email);
        UIkit.modal("#modal-setemail").hide();

        if (email.indexOf("@")>0){
            var arrayOfStrings = email.split("@");
            //console.log(arrayOfStrings);
            if (arrayOfStrings[1].indexOf(".")>0){
                $.ajax({
                    type: "GET",
                    cache: false,
                    processData: false,
                    dataType: "json",
                    contentType: "application/json",
                    url: "/component/pubmedtracker?task=setemail&email1="+encodeURIComponent(arrayOfStrings[0])+"&email2="+encodeURIComponent(arrayOfStrings[1]),
                    success: function (response) {
                        //console.log(response);
                        if (response.response==1){
                            $("#email-front").html(email);
                            UIkit.modal.alert('E-mail обновлён на '+email+'!');
                        }
                        else{
                            console.log(response);
                            UIkit.modal.alert('Ошибка выполнения запроса!');    
                        }
                    },
                    error: function(response){
                        console.log(response);
                        UIkit.modal.alert('Ошибка выполнения запроса!');
                        //alert("Ошибка выполнения запроса!");
                    }
                });
            }
            else{
                UIkit.modal.alert('В E-mail отсутствует точка .');
            }
        }
        else{
            UIkit.modal.alert('В E-mail отсутствует @');
        }
        
    });
}



function addbalance(){
    jQuery(function($) {
        var addbalance = Number($("#addbalance").val());
        //console.log(addbalance);
        UIkit.modal("#modal-addbalance").hide();

        $.ajax({
            type: "GET",
            cache: false,
            processData: false,
            dataType: "json",
            contentType: "application/json",
            url: "/component/pubmedtracker?task=addbalance&addbalance="+addbalance,
            success: function (response) {
                //console.log(response);
                if (response.response==1){
                    $("input[name=Description]").val(response.Description);
                    $("input[name=InvId]").val(response.InvId);
                    $("input[name=MerchantLogin]").val(response.MerchantLogin);
                    $("input[name=Email]").val(response.Email);
                    $("input[name=OutSum]").val(response.OutSum);
                    $("input[name=SignatureValue]").val(response.SignatureValue);
                    
        
                    $("#send-form-addbalance").submit();
                    //UIkit.modal.alert('ok');
                }
                else{
                    console.log(response);
                    UIkit.modal.alert('Ошибка выполнения запроса!');
                }
            },
            error: function(response){
                console.log(response);
                UIkit.modal.alert('Ошибка выполнения запроса!');
            }
        });
        
    });
}





function gethistory(){
    jQuery(function($) {
        $("#modal-balance-history .uk-modal-body").html('<div class="uk-text-center"><img src="/images/logo-small.svg" alt="bioinn.ru" class="logo-rotates"></div>');
        $.ajax({
            type: "GET",
            cache: false,
            processData: false,
            dataType: "json",
            contentType: "application/json",
            url: "/component/pubmedtracker?task=gethistory",
            success: function (response) {
                //console.log(response);
                var str = 'Нет данных';
                if (response.response==1){
                    var res = response.data;
                    var counter = 0;
                    if (res.length > 0)
                    {
                        str = '<table class="uk-table uk-table-striped uk-table-small">';
                        str += '<thead>';
                        str += '<tr>';
                            str += '<th>#</th>';
                            str += '<th>Время</th>';
                            str += '<th>Сумма</th>';
                            str += '<th>Комментарий</th>';
                        str += '</tr>';
                        str += '</thead>';
                        str += '<tbody>';
                        for (var i = 0; i < res.length; i++){
                            counter++;
                            str += '<tr>';
                                str += '<td>' + counter + '</td>';
                                str += '<td>' + res[i].date + '</td>';
                                str += '<td>' + res[i].value + '</td>';
                                str += '<td>' + res[i].descr + '</td>';
                            str += '</tr>';
                        }
                        str += '</tbody>';
                        str += '</table>';
                    }
                    
                    //$("#addbalance-front").html(response.newbalance.toFixed(2));
                    //UIkit.modal.alert('Счёт пополнен на '+addbalance.toFixed(2)+'! Текущий баланс: '+response.newbalance.toFixed(2));
                }
                else{
                    console.log(response);
                    UIkit.modal.alert('Ошибка выполнения запроса!');
                }

                $("#modal-balance-history .uk-modal-body").html(str);
            },
            error: function(response){
                console.log(response);
                UIkit.modal.alert('Ошибка выполнения запроса!');
            }
        });
        
    });
}




function autorenewal(x){
    jQuery(function($) {
        $(".block-rotates").show();
        $.ajax({
            type: "GET",
            cache: false,
            processData: false,
            dataType: "json",
            contentType: "application/json",
            url: "/component/pubmedtracker?task=autorenewal&x="+x,
            success: function (response) {
                //console.log(response);
                $(".block-rotates").hide();
                UIkit.modal.alert(response.message);
                
                setTimeout(function(){location.reload();}, 2500);
            },
            error: function(response){
                console.log(response);
                $(".block-rotates").hide();
                UIkit.modal.alert('Ошибка выполнения запроса!');
            }
        });
    });
}


function sentstatus(x){
    jQuery(function($) {
        $(".block-rotates").show();
        $.ajax({
            type: "GET",
            cache: false,
            processData: false,
            dataType: "json",
            contentType: "application/json",
            url: "/component/pubmedtracker?task=sentstatus&x="+x,
            success: function (response) {
                //console.log(response);
                $(".block-rotates").hide();
                UIkit.modal.alert(response.message);
                
                if(response.data.reload == 1){
                    setTimeout(function(){location.reload();}, 2500);
                }
            },
            error: function(response){
                console.log(response);
                $(".block-rotates").hide();
                UIkit.modal.alert('Ошибка выполнения запроса!');
            }
        });
    });
}


function DateToStr(x){
    var str = '';
    if (x.getDate()<10){
        str += '0';
    }
    str += x.getDate();
    str += '.';
    if (x.getMonth()<9){
        str += '0';
    }
    str += (x.getMonth()+1);
    str += '.';
    str += x.getFullYear();
    return str;
}




let tarifes = null;
let user_profile = null;
let current_tarife = null;
let current_date = null;




function gettarifre(){
    jQuery(function($) {
        $(".block-rotates").show();
        $.ajax({
            type: "GET",
            cache: false,
            processData: false,
            dataType: "json",
            contentType: "application/json",
            url: "/component/pubmedtracker?task=gettarifre",
            success: function (response) {
                //console.log(response);
                $(".block-rotates").hide();
               
                if (response.response==1){
                    tarifes = response.tarifes;
                    user_profile = response.user_profile;
                    current_tarife = response.current_tarife;
                    current_date = response.current_date;
                    //current_date = '2021-02-28';
                    //console.log(current_date);
                    if (tarifes.length>0 && user_profile.length>0){
                        $("#current-balance").html(user_profile[0].balance);

                        var str = '';
                        if (current_tarife.length>0){
                            $("#current-tarife-name").html(current_tarife[0].name);
                            $("#current-tarife-count-query").html(current_tarife[0].count_query);
                            $("#current-tarife-price").html(current_tarife[0].price);
                            if (current_tarife[0].status == 1){
                                str="Тариф доступен для продления";
                            }
                            else{
                                str="Отключен. Недоступен для продления";
                            }
                            $("#current-tarife-status").html(str);

                            var period_beg = new Date(user_profile[0].period_beg);
                            var period_end = new Date(user_profile[0].period_end);
                            $("#current-tarife-date-beg").html(DateToStr(period_beg));
                            $("#current-tarife-date-end").html(DateToStr(period_end));
                        }
                        
                        str = '<option value="0">Выберите тариф</option>';
                        for (var i = 0; i < tarifes.length; i++){
                            //console.log(tarifes[i]);
                            str += '<option value="'+ tarifes[i].tarife_id +'"';
                            if (tarifes[i].tarife_id==user_profile[0].tarife_id){
                                str += 'selected';
                            }
                            str += '>';
                            if (tarifes[i].tarife_id==user_profile[0].tarife_id){
                                str += '*';
                            }
                            str += 'Запросов: '+ tarifes[i].count_query + ' | ' + tarifes[i].price + ' руб/мес | ' + tarifes[i].name;
                            str += '</option>';
                            $("#tarife-select").html(str);
                        }

                        UIkit.modal("#modal-settarife").show();
                    }
                    else{
                        console.log(response);
                        UIkit.modal.alert('Ошибка выполнения запроса!');
                    }
                }
                else{
                    console.log(response);
                    UIkit.modal.alert('Ошибка выполнения запроса!');
                }

            },
            error: function(response){
                console.log(response);
                $(".block-rotates").hide();
                UIkit.modal.alert('Ошибка выполнения запроса!');
            }
        });
        
    });
}






jQuery(function($) {
    $("#tarife-select").change(function(){
        //if($(this).val() == current_tarife[0].tarife_id) return false;
        
        var id_tarife = $(this).val();
        //console.log(id_tarife);


        var cur_date = new Date(current_date);
        //console.log(cur_date);
        var cur_date_plus_month = new Date(current_date);
        if ((cur_date_plus_month.getMonth() == 0) && (cur_date_plus_month.getDate() == 30 || cur_date_plus_month.getDate() == 31)){
            //console.log('январь 30-31');
            cur_date_plus_month.setMonth(2); // ставим март
            cur_date_plus_month.setDate(0); // ставим нулевой день и получаем последний день предыдущего месяца
        }
        else
        {
            cur_date_plus_month.setMonth(cur_date_plus_month.getMonth() + 1);
            cur_date_plus_month.setDate(cur_date_plus_month.getDate() - 1);
        }
        

        if (tarifes.length>0 && user_profile.length>0 && current_tarife.length>0){

            
            for (var i = 0; i < tarifes.length; i++){
                if (tarifes[i].tarife_id == id_tarife){
                    $("#set-tarife-name").html(tarifes[i].name);
                    $("#set-tarife-count-query").html(tarifes[i].count_query);
                    $("#set-tarife-price").html(tarifes[i].price);
                    

                    $("#set-tarife-date-beg").html(DateToStr(cur_date));
                    $("#set-tarife-date-end").html(DateToStr(cur_date_plus_month));
                    break;
                }
            }

            var old_period_beg = new Date(user_profile[0].period_beg);
            var old_period_end = new Date(user_profile[0].period_end);

            if (cur_date <= old_period_end){
                if (cur_date >= old_period_beg){

                    var count_day = ((old_period_end - cur_date)/86400000);
                    var month_day = (old_period_end - old_period_beg)/86400000+1;
                    //console.log(month_day);

                    var vozvrat = (current_tarife[0].price / month_day) * count_day ;
                    $("#set-tarife-day").html(count_day);
                    $("#set-tarife-vozvrat").html(vozvrat.toFixed(2));
                    var itogo = tarifes[i].price - vozvrat;
                    $("#set-tarife-itogo").html(itogo.toFixed(2));
                }
            }
            else{
                $("#set-tarife-day").html(0);
                $("#set-tarife-vozvrat").html(0);
                $("#set-tarife-itogo").html(tarifes[i].price);
            }
            
        }
        else if(tarifes.length>0 && user_profile.length>0 && current_tarife.length==0 && id_tarife > 0){
            // это случай, если тариф вообще не выбран у пользователя
            for (var i = 0; i < tarifes.length; i++){
                if (tarifes[i].tarife_id == id_tarife){
                    $("#set-tarife-name").html(tarifes[i].name);
                    $("#set-tarife-count-query").html(tarifes[i].count_query);
                    $("#set-tarife-price").html(tarifes[i].price);
                    
                    $("#set-tarife-date-beg").html(DateToStr(cur_date));
                    $("#set-tarife-date-end").html(DateToStr(cur_date_plus_month));

                    $("#set-tarife-day").html(0);
                    $("#set-tarife-vozvrat").html(0);
                    $("#set-tarife-itogo").html(tarifes[i].price);
                    break;
                }
            }
        }else{
            $("#set-tarife-name").html("");
            $("#set-tarife-count-query").html("");
            $("#set-tarife-price").html("");
                    
            $("#set-tarife-date-beg").html("");
            $("#set-tarife-date-end").html("");

            $("#set-tarife-day").html("");
            $("#set-tarife-vozvrat").html("");
            $("#set-tarife-itogo").html("");
        }
    });




    $(".edit-query-btn").on("click",function(e){
        var query_id = $(this).data("query-id");
        //console.log(query_id);

        var query_days = $(".query-days").filter(function(){
                            return $(this).data("query-id") == query_id
                        }).data("query-days");
        
        var query_string = $(".query-string").filter(function(){
                            return $(this).data("query-id") == query_id
                            }).text();
                        //}).data("query-string");
        //console.log(query_string);
        query_string = query_string.trim();

        $("#set-query").val(query_string);
        $("#set-days").val(query_days);
        $("#set-query-id").val(query_id);
        $("#modal-editquery").find(".uk-modal-title").html("Редактировать запрос");
        //console.log(query_string);
    });


    $(".delete-query-btn").on("click",function(e){
        var query_id = $(this).data("query-id");
        var query_days = $(".query-days").filter(function(){
                            return $(this).data("query-id") == query_id
                        }).data("query-days");
        
        var query_string = $(".query-string").filter(function(){
                            return $(this).data("query-id") == query_id
                            }).text();
                        //}).data("query-string");
        query_string = query_string.trim();
        
        $("#del-query").html("Строка запроса: " + query_string);
        $("#del-days").html("Частота проверки:" + query_days + " дн");
        $("#del-query-id").val(query_id);

    });


    $(".add-query-btn").on("click",function(e){
        $("#modal-editquery").find(".uk-modal-title").html("Создать запрос");
        $("#set-query").val("");
        $("#set-days").val(1);
        $("#set-query-id").val(0);
    });

});


function setquery(){
    jQuery(function($) {
        var query_string = $("#set-query").val();
        var query_days = $("#set-days").val();
        var query_id = $("#set-query-id").val();
        //query_string = encodeURI(query_string.trim());
        query_string = query_string.trim();
        //console.log(query_string);

        UIkit.modal("#modal-editquery").hide();
        $(".block-rotates").show();

        $.ajax({
            type: "GET",
            cache: false,
            dataType: "json",
            contentType: "application/json",
            url: "/component/pubmedtracker",
            data: {"task":"editquery", "days":query_days, "query":query_string, "id":query_id},
            success: function (response) {
                //console.log(response);
                $(".block-rotates").hide();
                UIkit.modal.alert(response.message);
                
                setTimeout(function(){location.reload();}, 2500);
            },
            error: function(response){
                console.log(response);
                $(".block-rotates").hide();
                UIkit.modal.alert('Ошибка выполнения запроса!');
            }
        });

    });
}



function deletequery(){
    jQuery(function($) {
        
        var query_id = $("#del-query-id").val();

        UIkit.modal("#modal-deletequery").hide();
        $(".block-rotates").show();

        $.ajax({
            type: "GET",
            cache: false,
            dataType: "json",
            contentType: "application/json",
            url: "/component/pubmedtracker",
            data: {"task":"deletequery", "id":query_id},
            success: function (response) {
                //console.log(response);
                $(".block-rotates").hide();
                UIkit.modal.alert(response.message);
                setTimeout(function(){location.reload();}, 2500);
            },
            error: function(response){
                console.log(response);
                $(".block-rotates").hide();
                UIkit.modal.alert('Ошибка выполнения запроса!');
            }
        });
    });
}

function activequery(x = 0, id = 0){

    jQuery(function($) {
        $(".block-rotates").show();
        $.ajax({
            type: "GET",
            cache: false,
            dataType: "json",
            contentType: "application/json",
            url: "/component/pubmedtracker",
            data: {"task":"activequery", "id":id, "x":x},
            success: function (response) {
                //console.log(response);
                $(".block-rotates").hide();
                UIkit.modal.alert(response.message);
                
                if(response.data.reload == 1){
                    setTimeout(function(){location.reload();}, 2500);
                }
            },
            error: function(response){
                console.log(response);
                $(".block-rotates").hide();
                UIkit.modal.alert('Ошибка выполнения запроса!');
            }
        });
    });
}


<?php
    if ($this->RB_message != ''){
        echo "UIkit.modal.alert('".$this->RB_message."');";
        echo "window.history.replaceState(null, null, window.location.pathname);";
    }
?>
</script>


