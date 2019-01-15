/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
  
/* ПОИСК ВАРИАНТОВ ДЛЯ АВТОМАТИЧЕСКОГО ЗАВЕРШЕНИЯ  */
// срабатывает по событию onkeyup поля ввода
function autosuggest(str){
  // если текст не введен, скрываем div-элемент со списком вариантов
  if (str.length == 0) {
    $('#autosuggest_list').fadeOut(500);
  } else {
    // сначала показываем анимацию загрузки
    $('#class_activity').addClass('loading');
    
    // Ajax-запрос к методу "autosuggest" контроллера "ajax" 
    // отправляем значение параметра str
    $.post('/partners/select',
      { 'str':str },
      function(result) {
        // если есть результат, заносим его в div-элемент со списком вариантов 
        // и показываем его 
        // затем удаляем анимацию загрузки
        if(result) {
          $('#autosuggest_list').html(result);
          $('#autosuggest_list').fadeIn(500);
          $('#class_activity').removeClass('loading');
      }
    });
  }
}

function autosuggest_cart(str){
  // если текст не введен, скрываем div-элемент со списком вариантов
  if (str.length == 0) {
    $('#autosuggest_cart').fadeOut(500);
  } else {
    
    
    // Ajax-запрос к методу "autosuggest" контроллера "ajax" 
    // отправляем значение параметра str
    $.post('/cartridges/cart_list',
      { 'str':str },
      function(result) {
        // если есть результат, заносим его в div-элемент со списком вариантов 
        // и показываем его 
       
        if(result) {
          $('#autosuggest_cart').html(result);
          $('#autosuggest_cart').fadeIn(500);
         
      }
    });
  }
}

function get_tech(str){
  // если текст не введен, скрываем div-элемент со списком вариантов
  if (str.length == 0) {
    $('#get_tech').fadeOut(500);
  } else {
    
    
    // Ajax-запрос к методу "autosuggest" контроллера "ajax" 
    // отправляем значение параметра str
    $.post('/techs/get_tech',
      { 'str':str },
      function(result) {
        // если есть результат, заносим его в div-элемент со списком вариантов 
        // и показываем его 
       
        if(result) {
          $('#get_tech').html(result);
          $('#get_tech').fadeIn(500);
         
      }
    });
  }
}

function get_cartridge(str){
  // если текст не введен, скрываем div-элемент со списком вариантов
  if (str.length == 0) {
    $('#get_cartridge').fadeOut(500);
  } else {
    
    
    // Ajax-запрос к методу "autosuggest" контроллера "ajax" 
    // отправляем значение параметра str
    $.post('/cartridges/get_cartridge',
      { 'str':str },
      function(result) {
        // если есть результат, заносим его в div-элемент со списком вариантов 
        // и показываем его 
       
        if(result) {
          $('#get_cartridge').html(result);
          $('#get_cartridge').fadeIn(500);
         
      }
    });
  }
}

function get_carts_for_item(str){
  // если текст не введен, скрываем div-элемент со списком вариантов
  //if (str.length == 0) {
   // $('#cartridges').fadeOut(500);
  //} else {
    
    
    // Ajax-запрос к методу "autosuggest" контроллера "ajax" 
    // отправляем значение параметра str
     $.post('/cartridges/cart_list/1',
      { 'str':str },
      function(result) {
        // если есть результат, заносим его в div-элемент со списком вариантов 
        // и показываем его 
       
        if(result) {
          $('#cartridges').html(result);
          $('#cartridges').fadeIn(500);
         
      }
    });
  //}
}

function get_items_for_cart(str){
  // если текст не введен, скрываем div-элемент со списком вариантов
  if (str.length == 0) {
    $('#materials').fadeOut(500);
    
  } else {
    
    
    // Ajax-запрос к методу "autosuggest" контроллера "ajax" 
    // отправляем значение параметра str
    $.post('/store/get_items_for_cart',
      { 'str':str },
      function(result) {
        // если есть результат, заносим его в div-элемент со списком вариантов 
        // и показываем его 
       
        if(result) {
          $('#materials').html(result);
          $('#materials').fadeIn(500);
         
         
      }
    });
  }
}


function check_cart_adres(adres)
{
    if (adres.length == 0) {
    $('#answer').fadeOut(500);
  } else {
    $.post('/cartridges/check_cartridge_adres',
    {'adres':adres
    },
    function(result){
        if(result) {
          $('#answer').html(result);
          $('#answer').fadeIn(500); 
    }
    });
  }
    
}

function to_apprv(cart_num){
  
    $.post('/cartridges/to_apprv',
      { 'cart_num':cart_num
      },
      function(result) {
        // если есть результат, заносим его в div-элемент со списком вариантов 
        // и показываем его 
       if(result) {
          $('#answer').html(result);
          $('#answer').fadeIn(500);         
      }
    });
  }
  
  function update_cart(str){
  
    $.post('/cartridges/client_answer',
      { 'str':str
      },
      function(result) {
        // если есть результат, заносим его в div-элемент со списком вариантов 
        // и показываем его 
       if(result) {
          $('#answer').html(result);
          $('#answer').fadeIn(500);         
      }
    });
  }
 
function sortList(){
    var ids = [];
    $("#cart-list tr").each(function(){ ids[ids.length] = $(this).attr('sortid'); });
    $.ajax({
        type: 'POST',
        dataType: 'text',
        url: '/cartridges/sort',
        data: ({ ids: ids.join() }),
        success: function(html){  $("#answer").html(html);}
    });
}

function sortTechList(){
    var ids = [];
    $("#cart-list tr").each(function(){ ids[ids.length] = $(this).attr('sortid'); });
    $.ajax({
        type: 'POST',
        dataType: 'text',
        url: '/techs/sort',
        data: ({ ids: ids.join() }),
        success: function(html){  $("#answer").html(html);}
    });
}


          





