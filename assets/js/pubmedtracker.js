let tarifes = null;
let user_profile = null;
let current_tarife = null;
let current_date = null;
var preset_tarife = null;

function DateToStr(x) {
  var str = '';
  if (x.getDate() < 10) {
    str += '0';
  }
  str += x.getDate();
  str += '.';
  if (x.getMonth() < 9) {
    str += '0';
  }
  str += (x.getMonth() + 1);
  str += '.';
  str += x.getFullYear();
  return str;
}

function forAll(toggle_wrapper, circle, status) {
  toggle_wrapper.addEventListener("click", checkStatus);
  function checkStatus() {
    switch (status) {
      case "on":
        toggleOff();
        status = "off";
        break;
      case "off":
        toggleOn();
        status = "on";
        break;
    }
  }

  function toggleOn() {
    circle.classList.remove("off");
    circle.classList.add("on");

    toggle_wrapper.classList.remove("toff");
    toggle_wrapper.classList.add("ton");
  }
  function toggleOff() {
    circle.classList.remove("on");
    circle.classList.add("off");

    toggle_wrapper.classList.remove("ton");
    toggle_wrapper.classList.add("toff");
  }
}

var len = document.getElementsByClassName("toggle-wrapper-small").length;
for (var i = 0; i < len; i++) {
  var CurrToggle = document.getElementsByClassName("toggle-wrapper-small")[i];
  var CurrCircle = document.getElementsByClassName("circle-small")[i];
  if (CurrCircle.getAttribute("class").indexOf("on") > 0 && CurrToggle.getAttribute("class").indexOf("ton") > 0) {
    forAll(CurrToggle, CurrCircle, "on");
  }
  else {
    forAll(CurrToggle, CurrCircle, "off");
  }

}


// собираем первоначальные данные в массив 
// потом при потере фокуса сравниваем первоначальные данные с тем что ввели в поле... если данные не совпадают, то обновляем их
var GlobalDataQuerys = {};
jQuery(function ($) {
  var tmpid = 'z';
  var x = new Object();
  $('.pmt-input-q').each(function (index, value) {
    //if ($(this).data('type') == 'query'){
    if (tmpid == 'z') {
      tmpid = $(this).data('id-query');
    }
    if (tmpid != $(this).data('id-query')) {
      //console.log(x);
      GlobalDataQuerys[tmpid] = x;
      tmpid = $(this).data('id-query');
      x = new Object();
    }
    x[$(this).data('type')] = $(this).val();
    //console.log($(this).data('id-query')); 
    //}
  });
  GlobalDataQuerys[tmpid] = x; //последний объект дописываем
  //console.log(GlobalDataQuerys);
});

//отслеживание ввода в поле day
jQuery(function ($) {
  $('.pmt-input-q').on('input', function (e) {
    if ($(this).data('type') == 'day') {
      //console.log(e);
      //$(this).val($(this).val().replace(/[A-Za-zА-Яа-яЁё]/, ''));
      $(this).val($(this).val().replace(/[^\d]/g, '')); //вводим только цифры
    }
  });
});


jQuery(function ($) {
  $('.pmt-input-q').keypress(function (e) {
    if (e.key === "Enter") {
      $(this).blur(); //убираем с элемента фокус
    }
  });
});

//потеря фокуса
jQuery(function ($) {
  $('.pmt-input-q').blur(function () {
    var x = GlobalDataQuerys[$(this).data('id-query')];
    if ($(this).val() != x[$(this).data('type')]) {
      var id = $(this).data('id-query');
      var name = x['name'];
      var day = x['day'];
      var query = x['query'];
      if ($(this).data('type') == 'name') {
        name = $(this).val();
      }
      if ($(this).data('type') == 'day') {
        day = $(this).val();
      }
      if ($(this).data('type') == 'query') {
        query = $(this).val();
      }

      $.ajax({
        type: "GET",
        cache: false,
        dataType: "json",
        contentType: "application/json",
        url: "/component/pubmedtracker",
        data: { "task": "editquery", "id": id, "name": name, "day": day, "query": query },
        success: function (response) {
          //console.log(response);
          if (response.success == true) {
            AlertSuccess("Сохранено!");
            //обновляем первоначальные данные
            x['name'] = name;
            x['day'] = day;
            x['query'] = query;
            GlobalDataQuerys[id] = x;
          }
          else {
            console.log(response);
            AlertError(response.message);
          }
        },
        error: function (response) {
          console.log(response);
          AlertError(response.message);
        }
      });


      //console.log(query);

    }

  });
});



// обработка кнопок up/down для поля day
jQuery(function ($) {
  $(".pmt-day-updown").on("click", function (e) {
    var id = $(this).data("id-query");

    var day = $(".pmt-input-q").filter(function () {
      if ($(this).data("id-query") == id && $(this).data("type") == 'day') {
        return $(this);
      }
    });
    //day.focus();
    var n = Number(day.val());

    if ($(this).data("type") == 'up') {
      if (n < 180) {
        day.val(n + 1);
      }
    }
    else {
      if (n > 1) {
        day.val(n - 1);
      }
    }

    //console.log(day.val());
  });
});


//при наведении курсора ставим фокус в поле ввода... когда отводим, убираем фокус с поля
jQuery(function ($) {
  $(".pmt-day-updown")
    .mouseenter(function () {
      // навели курсор на объект (не учитываются переходы внутри элемента)
      //console.log('naveli');
      var id = $(this).data("id-query");
      var day = $(".pmt-input-q").filter(function () {
        if ($(this).data("id-query") == id && $(this).data("type") == 'day') {
          return $(this);
        }
      });
      //day.focus();
    })
    .mouseleave(function () {
      // отвели курсор с объекта (не учитываются переходы внутри элемента)
      //console.log('ubrali');
      var id = $(this).data("id-query");
      var day = $(".pmt-input-q").filter(function () {
        if ($(this).data("id-query") == id && $(this).data("type") == 'day') {
          return $(this);
        }
      });
      day.blur();
    });
});

function AlertSuccess(s = 'Сохранено!') {
  jQuery(function ($) {
    $("#system-message-container").html(
      '<div class="alert alert-success d-flex align-items-center alert-dismissible fade show" role="alert">' +
      '<svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Success:"><use xlink:href="#check-circle-fill"/></svg>' +
      '<div>' +
      s +
      '</div>' +
      '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
      '</div>'
    );
  });
}


function AlertError(s = 'Ошибка!') {
  jQuery(function ($) {
    $("#system-message-container").html(
      '<div class="alert alert-danger d-flex align-items-center alert-dismissible fade show" role="alert">' +
      '<svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Danger:"><use xlink:href="#exclamation-triangle-fill"/></svg>' +
      '<div>' +
      s +
      '</div>' +
      '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
      '</div>'
    );
  });
}



var ClickFlag = false; //костыль
var TimeSave = Date.now();

jQuery(function ($) {

  //-----------------------------
  // включаем отключаем режим отправки уведомлении о проверке базы, даже если новых статей нет
  $("#notif-null").on("click", function (e) {
    var status = $(this).data("status");
    $.ajax({
      type: "GET",
      cache: false,
      dataType: "json",
      contentType: "application/json",
      url: "/component/pubmedtracker",
      data: { "task": "setnotifnull", "status": status },
      success: function (response) {
        //console.log(response);
        if (response.success == true) {
          if (status == 1) {
            AlertSuccess("НЕ отправлять уведомления о проверке базы, даже если новых статей нет!");
            $("#notif-null").data("status", 0);
          }
          else {
            AlertSuccess("Отправлять уведомления о проверке базы, даже если новых статей нет!");
            $("#notif-null").data("status", 1);
          }
        }
        else {
          console.log(response);
          AlertError(response.message);
        }
      },
      error: function (response) {
        console.log(response);
        AlertError(response.message);
      }
    });
  });


  //ВКЛ/ВЫКЛ автопродление
  $("#auto-renewal").on("click", function (e) {
    var status = $(this).data("status");

    $.ajax({
      type: "GET",
      cache: false,
      dataType: "json",
      contentType: "application/json",
      url: "/component/pubmedtracker",
      data: { "task": "autorenewal", "status": status },
      success: function (response) {
        //console.log(response);
        if (response.success == true) {
          if (status == 1) {
            AlertSuccess("Автопродление отключено!");
            $("#auto-renewal").data("status", 0);
          }
          else {
            AlertSuccess("Автопродление включено!");
            $("#auto-renewal").data("status", 1);
          }
        }
        else {
          console.log(response);
          AlertError(response.message);
        }
      },
      error: function (response) {
        console.log(response);
        AlertError(response.message);
      }
    });

  });



  //показываем историю по балансу
  $("#pmt-balance-history").on("click", function (e) {

    var ModalBalanceHistory = new bootstrap.Modal(document.getElementById('modal-balance-history'), {
      keyboard: false
    })

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
        if (response.response == 1) {
          var res = response.data;
          var counter = 0;
          if (res.length > 0) {
            str = '<table class="table table-striped table-sm">';
            str += '<thead>';
            str += '<tr>';
            str += '<th>#</th>';
            str += '<th>Время</th>';
            str += '<th>Сумма</th>';
            str += '<th>Комментарий</th>';
            str += '</tr>';
            str += '</thead>';
            str += '<tbody>';
            for (var i = 0; i < res.length; i++) {
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
        }
        else {
          console.log(response);
          AlertError(response.message);
          str = 'Ошибка запроса!';
        }

        $("#modal-balance-history .modal-body").html(str);
        ModalBalanceHistory.show();
      },
      error: function (response) {
        console.log(response);
        AlertError(response.message);
      }
    });

  });




  $("#btn-addbalance").on("click", function (e) {
    var ModalAddBalance = new bootstrap.Modal(document.getElementById('modal-addbalance'), {
      keyboard: false
    });
    ModalAddBalance.show();
  });


  $("#addbalance-submit").on("click", function (e) {
    var addbalance = Number($("#addbalance").val());
    $.ajax({
      type: "GET",
      cache: false,
      dataType: "json",
      contentType: "application/json",
      url: "/component/pubmedtracker",
      data: { "task": "addbalance", "addbalance": addbalance },
      success: function (response) {
        //console.log(response);
        if (response.success == true) {
          $("input[name=Description]").val(response.data.Description);
          $("input[name=InvId]").val(response.data.InvId);
          $("input[name=MerchantLogin]").val(response.data.MerchantLogin);
          $("input[name=Email]").val(response.data.Email);
          $("input[name=OutSum]").val(response.data.OutSum);
          $("input[name=SignatureValue]").val(response.data.SignatureValue);
          $("#send-form-addbalance").submit();
        }
        else {
          console.log(response);
          AlertError(response.message);
        }
      },
      error: function (response) {
        console.log(response);
        AlertError(response.message);
      }
    });
  });




  $("#btn-setemail").on("click", function (e) {
    var ModalSetemail = new bootstrap.Modal(document.getElementById('modal-setemail'), {
      keyboard: false
    });
    ModalSetemail.show();
  });


  $("#setemail-submit").on("click", function (e) {
    var email = $("#setemail").val();
    var ModalSetemail = bootstrap.Modal.getInstance(document.getElementById('modal-setemail'));
    ModalSetemail.hide();

    if (email.indexOf("@") > 0) {
      var arrayOfStrings = email.split("@");
      //console.log(arrayOfStrings);

      if (arrayOfStrings[1].indexOf(".") > 0) {
        $.ajax({
          type: "GET", cache: false, dataType: "json", contentType: "application/json", url: "/component/pubmedtracker",
          data: { "task": "setemail", "email1": arrayOfStrings[0], "email2": arrayOfStrings[1] },
          success: function (response) {
            //console.log(response);
            if (response.success == true) {
              $("#email-front").html(email);
              AlertSuccess('E-mail обновлён на ' + email + '!');
            }
            else {
              console.log(response);
              AlertError(response.message);
            }
          },
          error: function (response) {
            console.log(response);
            AlertError(response.message);
          }
        });
      }
      else {
        AlertError('В E-mail отсутствует точка .');
      }

    }
    else {
      AlertError("В E-mail отсутствует @");
    }
  });




  //ВКЛ/ВЫКЛ запроса
  $(".toggle-query").on("click", function (e) {
    var id = $(this).data("id-query");
    var str = "";

    if (ClickFlag == true) {
      ClickFlag = false;
      return 0;
    }

    /*
    var s = Date.now();
    console.log(s);
    */
    //TimeSave

    var status = $(".toggle-query").filter(function () {
      return $(this).data("id-query") == id;
    }).data("status");

    var CurrToggle = $(this);
    //var CurrCircle = $(this).find("circle-small");

    $.ajax({
      type: "GET", cache: false, dataType: "json", contentType: "application/json", url: "/component/pubmedtracker",
      data: { "task": "activequery", "id": id, "status": status },
      success: function (response) {
        //console.log(response);
        if (response.success == true) {
          if (status == 1) {
            str = "<div class='d-flex align-items-center justify-content-end'>" +
              "<img src='/components/com_pubmedtracker/assets/img/pause.svg' alt='pause'>" +
              "</div>" +
              "<p class='pmt-play-gray ps-2'>" +
              "остановлен" +
              "</p>";
            AlertSuccess("Запрос отключен!");
            $(".toggle-query").filter(function () {
              return $(this).data("id-query") == id;
            }).data("status", 0);
          }
          else {
            str = "<div class='d-flex align-items-center justify-content-end'>" +
              "<img src='/components/com_pubmedtracker/assets/img/play.svg' alt='play'>" +
              "</div>" +
              "<p class='pmt-play-green ps-2'>" +
              "работает" +
              "</p>";
            AlertSuccess("Запрос включен!");
            $(".toggle-query").filter(function () {
              return $(this).data("id-query") == id;
            }).data("status", 1);
          }

          $(".status-query").filter(function () {
            return $(this).data("id-query") == id;
          }).html(str);

        }
        else {
          console.log(response);
          AlertError(response.message);
          ClickFlag = true;
          CurrToggle.click();
          //setInterval(CurrToggle.click(), 500);


        }
      },
      error: function (response) {
        console.log(response);
        AlertError(response.message);

      }
    });


    //AlertSuccess(id);
  });


});



// очистка строки запроса
jQuery(function ($) {
  $(".pmt-img-trash").on("click", function (e) {
    var id = $(this).data("id-query");


    $.ajax({
      type: "GET", cache: false, dataType: "json", contentType: "application/json", url: "/component/pubmedtracker",
      data: { "task": "deletequery", "id": id },
      success: function (response) {
        //console.log(response);
        if (response.success == true) {
          AlertSuccess('Строка очищена!');
          var CurrToggle = $(".toggle-query").filter(function () {
            if ($(this).data("id-query") == id) {
              return $(this);
            }
          });
          if (CurrToggle.data('status') == 1) {
            CurrToggle.click();
          }
          var x = GlobalDataQuerys[id];
          x['name'] = '';
          x['day'] = 1;
          x['query'] = '';
          GlobalDataQuerys[id] = x;

          $('.pmt-input-q').each(function (index, value) {
            if ($(this).data("id-query") == id) {
              if ($(this).data("type") == 'day') {
                $(this).val(1);
              }
              else {
                $(this).val('');
              }
            }
          });
        }
        else {
          console.log(response);
          AlertError(response.message);
        }
      },
      error: function (response) {
        console.log(response);
        AlertError(response.message);
      }
    });
  });
});



// загружаем информацию о профиле и тарифах в форму смены тарифа
jQuery(function ($) {
  $("#pmt-tarife").on("click", function (e) {
    if (tarifes == null) {
      $.ajax({
        type: "GET", cache: false, dataType: "json", contentType: "application/json", url: "/component/pubmedtracker",
        data: { "task": "gettarife" },
        success: function (response) {
          //console.log(response);

          tarifes = response.data.tarifes;
          user_profile = response.data.user_profile;
          current_tarife = response.data.current_tarife;
          current_date = response.data.current_date;
          //current_date = '2021-02-28';
          //console.log(current_date);
          if (tarifes.length > 0 && user_profile.length > 0) {
            $("#current-balance").html(user_profile[0].balance);

            var str = '';
            if (current_tarife.length > 0) {
              $("#current-tarife-name").html(current_tarife[0].name);
              $("#current-tarife-count-query").html(current_tarife[0].count_query);
              $("#current-tarife-price").html(current_tarife[0].price);
              if (current_tarife[0].status == 1) {
                str = "Тариф доступен для продления";
              }
              else {
                str = "Отключен. Недоступен для продления";
              }
              $("#current-tarife-status").html(str);

              var period_beg = new Date(user_profile[0].period_beg);
              var period_end = new Date(user_profile[0].period_end);
              $("#current-tarife-date-beg").html(DateToStr(period_beg));
              $("#current-tarife-date-end").html(DateToStr(period_end));
            }

            if (user_profile[0].tarife_id == 0){
            str = '<option value="0" selected>-</option>';
            }
            else{
              str = '';
            }
            
            for (var i = 0; i < tarifes.length; i++) {
              //console.log(tarifes[i]);
              str += '<option value="' + tarifes[i].tarife_id + '"';
              if (tarifes[i].tarife_id == user_profile[0].tarife_id) {
                str += 'selected';
                str += '>';
                str += '*';
              }
              else {
                str += '>';
              }

              str += 'Запросов: ' + tarifes[i].count_query + ' | ' + tarifes[i].price + ' руб/мес | ' + tarifes[i].name;
              str += '</option>';
              $("#tarife-select").html(str);
            }
            
            ModalSettarife.show();
            if (preset_tarife != null){
              $("#tarife-select").val(preset_tarife).change();
            }
            preset_tarife = null;
          }
          else {
            console.log(response);
            AlertError();
          }

        },
        error: function (response) {
          console.log(response);
          AlertError(response.message);
        }
      });
    }
    else {
      ModalSettarife.show();
      if (preset_tarife != null){
        $("#tarife-select").val(preset_tarife).change();
      }
      preset_tarife = null;
    }
  });
});



// обработка события выбора тарифа
jQuery(function ($) {
  $("#tarife-select").change(function () {
    var id_tarife = $(this).val();
    //console.log(user_profile);
    //if (id_tarife != user_profile[0].tarife_id) {
    var cur_date = new Date(current_date);
    //console.log(cur_date);
    var cur_date_plus_month = new Date(current_date);
    if ((cur_date_plus_month.getMonth() == 0) && (cur_date_plus_month.getDate() == 30 || cur_date_plus_month.getDate() == 31)) {
      //console.log('январь 30-31');
      cur_date_plus_month.setMonth(2); // ставим март
      cur_date_plus_month.setDate(0); // ставим нулевой день и получаем последний день предыдущего месяца
    }
    else {
      cur_date_plus_month.setMonth(cur_date_plus_month.getMonth() + 1);
      cur_date_plus_month.setDate(cur_date_plus_month.getDate() - 1);
    }


    if (tarifes.length > 0 && user_profile.length > 0 && current_tarife.length > 0 && id_tarife != user_profile[0].tarife_id) {


      for (var i = 0; i < tarifes.length; i++) {
        if (tarifes[i].tarife_id == id_tarife) {
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

      if (cur_date <= old_period_end) {
        if (cur_date >= old_period_beg) {

          var count_day = ((old_period_end - cur_date) / 86400000);
          var month_day = (old_period_end - old_period_beg) / 86400000 + 1;
          //console.log(month_day);

          var vozvrat = (current_tarife[0].price / month_day) * count_day;
          $("#set-tarife-day").html(count_day);
          $("#set-tarife-vozvrat").html(vozvrat.toFixed(2));
          var itogo = tarifes[i].price - vozvrat;
          $("#set-tarife-itogo").html(itogo.toFixed(2));
        }
      }
      else {
        $("#set-tarife-day").html(0);
        $("#set-tarife-vozvrat").html(0);
        $("#set-tarife-itogo").html(tarifes[i].price);
      }

    }
    else if (tarifes.length > 0 && user_profile.length > 0 && current_tarife.length == 0 && id_tarife > 0) {
      // это случай, если тариф вообще не выбран у пользователя
      for (var i = 0; i < tarifes.length; i++) {
        if (tarifes[i].tarife_id == id_tarife) {
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
    } else {
      $("#set-tarife-name").html("-");
      $("#set-tarife-count-query").html("-");
      $("#set-tarife-price").html("-");

      $("#set-tarife-date-beg").html("-");
      $("#set-tarife-date-end").html("-");

      $("#set-tarife-day").html("-");
      $("#set-tarife-vozvrat").html("-");
      $("#set-tarife-itogo").html("-");
    }
  });
});



// установка нового тарифа
jQuery(function ($) {
  $("#settarife-submit").on("click", function (e) {
    var tarife_id = $("#tarife-select").val();
    console.log(tarife_id);
    if (tarife_id > 0) {

      ModalSettarife.hide();

      $.ajax({
        type: "GET", cache: false, dataType: "json", contentType: "application/json", url: "/component/pubmedtracker",
        data: { "task": "settarife", "tarife": tarife_id },
        success: function (response) {
          //console.log(response);
            if (response.success == true && response.data.reload == 1) {
              AlertSuccess(response.message);
              setTimeout(function () { location.reload(); }, 1500);
            } else {
              AlertError(response.message);
            }
        },
        error: function (response) {
          console.log(response);
          AlertError(response.message);
        }
      });

    }
  });
});


//обработка кнопки + расширить тариф
jQuery(function ($) {
  $(".pmt-addtarife").on("click", function (e) {
    preset_tarife = $(this).data("tarife-id");
    console.log(preset_tarife);
    $("#pmt-tarife").click();
    /*
    if (tarifes == null) {
    }
    */
  });
});



var ModalSettarife = new bootstrap.Modal(document.getElementById('modal-settarife'), {
  keyboard: false
});