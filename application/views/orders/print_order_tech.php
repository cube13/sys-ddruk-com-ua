<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf8">
        <title></title>
        <script type="text/javascript" language="javascript">
         <!--
                        function prn() { setTimeout('window.print();',200); }
--></script>
        <style>
            table.border_1px{
                border: solid black 1px;
                font: normal 10pt arial;
            }
             
            td.label
            {
                font:bold 10pt arial;
                text-align: right;
                padding-right: 5px;
            }
            td.border_all
            {
                border: solid black 1px;
            }
            td.border-side
            {
                border-left: solid black 1px;
                border-right: solid black 1px;
                border-top: dashed black 0.5px;
                border-bottom: dashed black 1px;
            }
            td.border-bot-dash
            {
                border-left: solid black 1px;
                border-right: solid black 1px;
                border-top: solid black 1px;
                border-bottom: dashed black 1px;
            }
            td.border-top-dash
            {
                border-left: solid black 1px;
                border-right: solid black 1px;
                border-top: dashed black 0.5px;
                border-bottom: solid black 1px;
            }
      </style>

          </head>
          <?php //if($this->ion_auth->is_admin()) print_r($extracontact->result());?>
         
          
    <body  onload="prn()">
        
        <!-- Начало квитанции нашей -->

<table align=center width=100% border=0>
    <tr><td style="font:bold 12pt arial;" align=center valign=top>
        Квитанція № <?php echo $order->id;?> - В<br>
<div style="font:bold 8pt arial;">про приймання-передачу пристрою</br> для діагностики/профілактики/ремонту</div></td>
<td width=20></td>
<td valign=top align=right><img src='/assets/img/logo_new.jpg' height=50 alt=logo title=logo></td></tr>
</table>
     
<table width=100% border=0 cellspacing="0">
    
<tr height="40" valign="top"><td class="label"  width=80 valign="top">Адреса</td>
    <td style="border: solid black 1px;">
        <?php foreach ($extracontact->result() as $contact)
            {
                if($contact->stage_code=='contact-adres') echo $contact->text.'; ';
            }?>
        &nbsp;<i>
            <?php foreach ($extracontact->result() as $contact)
            {
                if($contact->stage_code=='info') echo $contact->text.'; ';
            }?></i></td></tr>
<tr height="25"><td class="label">Телефон</td>
    <td style="border: solid black 1px;"><?php foreach ($extracontact->result() as $contact)
            {
                if($contact->stage_code=='contact-tel'||$contact->stage_code=='contact-mob') echo $contact->text.'; ';
            }?>
    </td></tr>
<tr height="25"><td class="label"  width=80 valign="top">Замовник</td>
    <td style="border: solid black 1px;"><?php if($order->org_id!=11){ echo $order->short_name.'; ';}
            foreach ($extracontact->result() as $contact)
            {if($contact->stage_code=='contact-name') echo $contact->text.'; ';}?></td></tr>
</table>
        <br>

        <?php $rows=9;?>
        <table width=100% cellspacing="0">
            <tr><td height="28" class="label" valign="top">Тип пристрою</td><td width="80%" class="border_all"></td></tr>
            <tr><td height="28" class="label" valign="top">Модель</td><td class="border_all"></td></tr>
            <tr><td height="28" rowspan="2" class="label">Аксесуари<br>(за наявності)</td><td height="28" class="border-bot-dash"></td></tr>
            <tr><td height="28" class="border-top-dash"></td></tr>
            <tr><td height="28" rowspan="<?php echo $rows;?>" class="label" valign="top">Опис несправності<br>(за даними Замовника)</td><td height="28" class="border-bot-dash"></td></tr>
            <?php for($i=1;$i<=$rows-2;$i++):?>
            <tr><td height="28" class="border-side"></td></tr>
            <?php endfor;?>
            <tr><td height="28" class="border-top-dash"></td></tr>
            
        </table>
        <br>
<table width=100%>
<tr><td colspan=4 height=10></td></tr>
<tr><td style="font:bold 11pt arial;" width=50>Підпис:</td>
<td style="border-bottom: solid 2px;"; width=250></td>
<td style="font:bold 11pt arial;" width=80>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Дата:</td>
<td style="border-bottom: solid 2px;"; width=250></td></tr>

</table>
        
        
        <!-- Конец квитанции нашей -->
        <br><br><br><br>
         <!-- Начало квитанции Клиента -->

<table align=center width=100% border=0>

    <tr><td style="font:bold 12pt arial;" align=center valign=top>
        Квитанція № <?php echo $order->id;?>-З<br>
<div style="font:normal 8pt arial;">про приймання-передачу пристрою</br> для діагностики/профілактики/ремонту<br/>
(пристрій видається на підставі цієї квитанції)</div>
<div style="font:bold 9pt arial;">Київ, вул. Мельникова,2/10<br>
тел. 392-86-87, тел.вн. 75-16 </div></td>
<td width=20></td>
<td valign=top align=right><img src='/assets/img/logo_new.jpg' height=50 alt=logo title=logo></td></tr>
</table>
         <br>   
<table width=100% border=0 cellspacing="0">
<tr height="45" valign="top"><td class="label" width=80>Замовник</td>
    <td style="border: solid black 1px;"><?php if($order->org_id!=11){ echo $order->short_name.'; ';}
            foreach ($extracontact->result() as $contact)
            {if($contact->stage_code=='contact-name') echo $contact->text.'; ';}?></td></tr>
</table>
        <br>
<table width=100% cellspacing="0">
            <tr><td height="28" class="label" valign="top">Тип пристрою</td><td width="80%" class="border_all"></td></tr>
            <tr><td height="28" class="label" valign="top">Модель</td><td class="border_all"></td></tr>
            <tr><td height="28" rowspan="2" class="label">Аксесуари<br>(за наявності)</td><td height="28" class="border-bot-dash"></td></tr>
            <tr><td height="28" class="border-top-dash"></td></tr>
            <tr><td height="28" rowspan="<?php echo $rows;?>" class="label" valign="top">Опис несправності<br>(за даними Замовника)</td><td height="28" class="border-bot-dash"></td></tr>
            <?php for($i=1;$i<=$rows-2;$i++):?>
            <tr><td height="28" class="border-side"></td></tr>
            <?php endfor;?>
            <tr><td height="28" class="border-top-dash"></td></tr>
            
        </table>
        <br>
<table width=100%>
<tr><td colspan=4 height=10></td></tr>
<tr><td style="font:bold 11pt arial;" width=50>Підпис:</td>
<td style="border-bottom: solid 2px;"; width=250></td>
<td style="font:bold 11pt arial;" width=80>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Дата:</td>
<td style="border-bottom: solid 2px;"; width=250></td></tr>

</table>
        
        
        <!-- Конец квитанции клиента -->

</body>
</html>