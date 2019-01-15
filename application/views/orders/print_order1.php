<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf8">
        <title></title>
        <script type="text/javascript" language="javascript">
         <!--
                        function prn() { setTimeout('window.print();',1000); }
--></script>
        <style>
            table.border_1px{
                border: solid black 1px;
                font: normal 10pt arial;

}        </style>

          </head>
          <?php //if($this->ion_auth->is_admin()) print_r($extracontact->result());?>
         
    <body  onload="prn()">
        <!-- Начало квитанции нашей -->

<table align=center width=100% border=0>

    <tr><td style="font:bold 12pt arial;" align=center valign=top>
        Квитанція № <?php echo $order->id;?>-В<br>
<div style="font:bold 8pt arial;">про приймання передачу картриджу</br> на заправку/відновлення</div></td>
<td width=20></td>
<td width=50 style="border:2px black dotted"></td>
<td valign=top align=right><img src='/assets/img/logo_new.jpg' height=50 alt=logo title=logo></td></tr>
</table>
     
<table width=100% border=0 cellspacing="0">
    
<tr height="40" valign="top"><td style="font:bold 10pt arial;" width=80 valign="top">Адреса:</td>
    <td style="border: solid black 1px;">
        <?php foreach ($extracontact->result() as $contact)
            {
                if($contact->stage_code=='contact-adres') echo $contact->text.'; ';
            }?>
        &nbsp;<i>
            <?php foreach ($extracontact->result() as $contact)
            {
                if($contact->stage_code=='info') echo $contact->text.'; ';
            }?></i>
    </td></tr>
<tr height="25"><td style="font:bold 10pt arial;" width=80 valign="top">Телефон:</td>
    <td style="border: solid black 1px;">
        <?php if($order->mob_tel) echo 'м.т.'.$order->mob_tel.' ';
              if($order->tel) echo ' т.'.$order->tel;?>
     <?php foreach ($extracontact->result() as $contact)
            {
                if($contact->stage_code=='contact-tel'||$contact->stage_code=='contact-mob') echo $contact->text.'; ';
            }?>
    </td></tr>
<tr height="25"><td style="font:bold 10pt arial;" width=80 valign="top">Замовник:</td>
    <td style="border: solid black 1px;">
        <?php if($order->org_id!=11){ echo $order->short_name.'; ';}
            foreach ($extracontact->result() as $contact)
            {if($contact->stage_code=='contact-name') echo $contact->text.'; ';}?></td></tr>
</table>
        <br>
<table border=1 width=100% rules="all" class="border_1px">

   <tr><td style="font:bold 10pt arial;" align=center valign=bottom width="5%">№</td>
    <td style="font:bold 10pt arial;" align=center valign=bottom width="50%">Модель картриджу</td>
    <td style="font:bold 10pt arial;" align=center valign=bottom width="23%">№ картриджу<br>(за наявності)</td>
    <td style="font:bold 10pt arial;" align=center valign=bottom width="22%">Дефекти друку<br>(за даними Замовника)</td></tr>

<?php $k=1;?>
<?php foreach ($cartridges->result() as $cartridge):?>
    <tr><td height=28 align="right"><?php echo $k;?></td>
        <td>&nbsp;<?php echo $cartridge->name;?></td>
        <td>&nbsp;<?php echo $cartridge->cart_num;?></td><td></td></tr>
    <?php $k++;
    if($k>12) break;?>
<?php endforeach;?>

<?php for($n=$k;$n<=12;$n++):?>
    <tr><td height=28 align="right"><?php echo $n;?></td><td></td><td></td><td></td></tr>
<?php endfor;?>

</table>
        <br>
<table width=100%>
<tr><td colspan=4 height=10></td></tr>
<tr><td style="font:bold 11pt arial;" width=50>Підпис:</td>
<td style="border-bottom: solid 2px;"; width=250></td>
<td style="font:bold 11pt arial;" width=80>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Дата:</td>
<td style="border-bottom: solid 2px;"; width=250></td></tr>

</table>
        
        <br><br><br><br>
        <table border=1 width=100% rules="all" class="border_1px">

   <tr><td style="font:bold 10pt arial;" align=center valign=bottom width="5%">№</td>
    <td style="font:bold 10pt arial;" align=center valign=bottom width="50%">Модель картриджу</td>
    <td style="font:bold 10pt arial;" align=center valign=bottom width="23%">№ картриджу<br>(за наявності)</td>
    <td style="font:bold 10pt arial;" align=center valign=bottom width="22%">Дефекти друку<br>(за даними Замовника)</td></tr>

 <?php $k=1;?>
<?php foreach ($cartridges->result() as $cartridge):?>
   <?php if($k>12):?> 
   <tr><td height=28 align="right"><?php echo $k;?></td>
        <td>&nbsp;<?php echo $cartridge->name;?></td>
        <td>&nbsp;<?php echo $cartridge->cart_num;?></td><td></td></tr>
   <?php endif;?>
    <?php $k++;?>
    <?php if($k>30) break;?>
<?php endforeach;?>

   <?php if($k<13) $k=13;?>
<?php for($n=$k;$n<=30;$n++):?>
    <tr><td height=28 align="right"><?php echo $n;?></td><td></td><td></td><td></td></tr>
<?php endfor;?>
</table>



<table width=100%>
<tr><td colspan=4 height=10></td></tr>
<tr><td style="font:bold 11pt arial;" width=50>Підпис:</td>
<td style="border-bottom: solid 2px;"; width=250></td>
<td style="font:bold 11pt arial;" width=80>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Дата:</td>
<td style="border-bottom: solid 2px;"; width=250></td></tr>

</table>
        <!-- Конец квитанции нашей -->
        <br><br><br>
         <!-- Начало квитанции Клиента -->

<table align=center width=100% border=0>

    <tr><td style="font:bold 12pt arial;" align=center valign=top>
        Квитанція № <?php echo $order->id;?><br>
<div style="font:normal 8pt arial;">про приймання передачу картриджу</br> 
(картриджі видаються на підставі цієї квитанції)</div>
<div style="font:bold 12pt arial;">Київ, вул. Мельникова,2/10<br>
тел. 392-86-87, тел.вн. 75-16</div></td>
<td width=20></td>
<td valign=top align=center><img src='/assets/img/logo_new.jpg' height=50 alt=logo title=logo>
    <br/><span style="font-size: 20pt;font-family: arial;"></span></td></tr>
</table>
         <br>   
<table width=100% border=0 cellspacing="0">
<tr height="45" valign="top"><td style="font:bold 10pt arial;" width=80>Замовник:</td>
    <td style="border: solid black 1px;">
        <?php if($order->org_id!=11)
            { 
                echo $order->short_name.'; ';
            }
            foreach ($extracontact->result() as $contact)
            {
                if($contact->stage_code=='contact-name') echo $contact->text.'; ';
            }?> 
        <?php foreach ($extracontact->result() as $contact)
            {
                if($contact->stage_code=='contact-tel'||$contact->stage_code=='contact-mob') echo $contact->text.'; ';
            }?></td></tr>
</table>
        <br>
<table border=1 width=100% rules="all" class="border_1px">

   <tr><td style="font:bold 10pt arial;" align=center valign=bottom width="5%">№</td>
    <td style="font:bold 10pt arial;" align=center valign=bottom width="50%">Модель картриджу</td>
    <td style="font:bold 10pt arial;" align=center valign=bottom width="23%">№ картриджу<br>(за наявності)</td>
    <td style="font:bold 10pt arial;" align=center valign=bottom width="22%">Дефекти друку<br>(за даними Замовника)</td></tr>

<?php $k=1;?>
<?php foreach ($cartridges->result() as $cartridge):?>
    <tr><td height=28 align="right"><?php echo $k;?></td>
        <td>&nbsp;<?php echo $cartridge->name;?></td>
        <td>&nbsp;<?php echo $cartridge->cart_num;?></td><td></td></tr>
    <?php $k++;
    if($k>12) break;?>
<?php endforeach;?>

<?php for($n=$k;$n<=12;$n++):?>
    <tr><td height=28 align="right"><?php echo $n;?></td><td></td><td></td><td></td></tr>
<?php endfor;?>

</table>
        <br>
<table width=100%>
<tr><td colspan=4 height=10></td></tr>
<tr><td style="font:bold 11pt arial;" width=50>Підпис:</td>
<td style="border-bottom: solid 2px;"; width=250></td>
<td style="font:bold 11pt arial;" width=80>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Дата:</td>
<td style="border-bottom: solid 2px;"; width=250></td></tr>

</table>
        
        <br><br><br><br>
        <table border=1 width=100% rules="all" class="border_1px">

   <tr><td style="font:bold 10pt arial;" align=center valign=bottom width="5%">№</td>
    <td style="font:bold 10pt arial;" align=center valign=bottom width="50%">Модель картриджу</td>
    <td style="font:bold 10pt arial;" align=center valign=bottom width="23%">№ картриджу<br>(за наявності)</td>
    <td style="font:bold 10pt arial;" align=center valign=bottom width="22%">Дефекти друку<br>(за даними Замовника)</td></tr>

<?php $k=1;?>
<?php foreach ($cartridges->result() as $cartridge):?>
   <?php if($k>12):?> 
   <tr><td height=28 align="right"><?php echo $k;?></td>
        <td>&nbsp;<?php echo $cartridge->name;?></td>
        <td>&nbsp;<?php echo $cartridge->cart_num;?></td><td></td></tr>
   <?php endif;?>
        <?php $k++;?>
    <?php if($k>30) break;?>
<?php endforeach;?>
<?php if($k<13) $k=13;?>
<?php for($n=$k;$n<=30;$n++):?>
    <tr><td height=28 align="right"><?php echo $n;?></td><td></td><td></td><td></td></tr>
<?php endfor;?>

</table>

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