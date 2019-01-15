<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <script src="/assets/js/jquery-1.8.1.min.js"></script>        
        <script src="/assets/js/jquery-ui-1.8.23.custom.min.js"></script>
        <script src="/assets/js/global.js"></script>
        <script src="/assets/bootstrap/js/bootstrap.min.js"></script>
        <script src="/assets/js/maskedinput.js"></script>
        <link href="/assets/bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
        <link href="/assets/font-awesome/css/font-awesome.min.css" rel="stylesheet"> 

      
    <link rel="stylesheet" type="text/css" href="/assets/cleditor/jquery.cleditor.css" />

    <script type="text/javascript" src="/assets/cleditor/jquery.cleditor.min.js"></script>

      
       <link href="/assets/css/custom-theme/jquery-ui-1.8.23.custom.css" rel="stylesheet" type="text/css" />
        <link href="/assets/css/main.css" rel="stylesheet" type="text/css" />
        
        <script>
	$(function() {
             $("#datepicker").datepicker({
// Полные имена дней недели
dayName: ['Воскресенье', 'Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота'],
        // Сокращенные имена дней недели
dayNamesMin: ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'],
        // Название месяцев
monthNames: ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'],
// Сокращенные названия месяцев
        monthNamesShort: ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'],
        // Формат даты, например 27.04.2011
dateFormat: 'dd.mm.yy',
        // Первый день недели, 0 - воскресенье.
firstDay: 1
}); 
    }
);
 </script>
 
     <title><?php echo $title;?></title>
</head>

