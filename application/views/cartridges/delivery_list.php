<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf8">
        <title></title>
        <script type="text/javascript" language="javascript">
         <!--
                        function prn() { i=setTimeout('window.print();',200); }
--></script>
        <style>
            table.border_1px{
                border: solid black 1px;
                font: normal 10pt arial;}
            td.border
            {
                border: solid black 1px;
                font: italic 11pt arial;
            }
        </style>

          </head>
          <?php //if($this->ion_auth->is_admin()) print_r($extracontact->result());?>
    <body  onload="prn()">

<table align=center width=100% border=0>
    <tr><td style="font:bold 12pt arial;" align=center valign=top>
        Квитанція № <?php echo $order->id;?><br>
<div style="font:bold 8pt arial;">про приймання передачу картриджів<br/></div>
<div style="font:bold 9pt arial;">Київ, вул. Мельникова,2/10<br>
тел. 392-86-87, тел.вн. 75-16</div></td>
<td width=20></td>
<td valign=top align=center><img src='/assets/img/logo_new.jpg' height=50 alt=logo title=logo>
<br/><span style="font-size: 20pt;font-family: arial;">75-16</span></td></tr>
</table>
      
        <div><b>Виконавець</b> передав, а </div>
        <div><b>Замовник:</b> <?php if($order->org_id==11||$order->org_id==354) {echo $order->contacter;}else {echo $order->short_name;}?></div>
        <div>прийняв наступні картриджі: </div>  
        <br>
<table border=1 width=100% rules="all" class="border_1px">

   <tr><td style="font:bold 10pt arial;" align=center valign=bottom width="5%">№</td>
    <td style="font:bold 10pt arial;" align=center valign=bottom width="50%">Модель картриджу</td>
    <td style="font:bold 10pt arial;" align=center valign=bottom width="23%">№ картриджу</td>
    <td style="font:bold 10pt arial;" align=center valign=bottom width="22%">Дефекти друку<br>(за даними Виконавця)</td></tr>
   <?php $n=1;?>
<?php foreach($cartridges->result() as $cartridge):?>
    <tr><td height=28 align="center"><?php echo $n;?></td>
        <td><?php echo $cartridge->name;?></td>
        <td><?php echo $cartridge->cart_num;?></td>
        <td></td>
    </tr>
    <?php $n++;?>
<?php endforeach;?>

</table><br>
        
        
<table width=100% border="0">
    <tr><td><b>Дата:</b></td><td><?php echo date('d.m.Y');?></td><td></td></tr>
<tr><td width="20%"></td><td style="font:11pt arial;" align="left" width="40%">Прізвище:</td><td style="font:11pt arial;" align="left" width="40%">Підпис:</td></tr>
<tr><td height=28 style="font: bold 11pt arial;">Замовник</td><td class="border"></td><td class="border"></td></tr>
<tr><td height=28 style="font:bold 11pt arial;">Виконавець</td><td class="border"><?php echo $this->ion_auth->user()->row()->last_name;?></td><td class="border"></td></tr>

</table>
<br/>
<div style="font-size: 10pt; font-weight: bold;text-align: center;">Добрий друк 75-16</div >
        
       

</body>
</html>