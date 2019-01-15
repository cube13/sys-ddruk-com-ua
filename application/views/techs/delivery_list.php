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
    <body  onload="prn_()">

<table align=center width=100% border=0>
    <tr><td style="font:bold 12pt arial;" align=center valign=top>
        Квитанція № <?php echo $order->id;?><br>
<div style="font:bold 8pt arial;">про приймання передачу пристрою<br/>
після діагностики/профілактики/ремонту</div>
<div style="font:bold 9pt arial;">Київ, вул. Мельникова,2/10<br>
тел. 237-15-55, тел.вн. 75-16</div></td>
<td width=20></td>
<td valign=top align=right><img src='/assets/img/logo_new.jpg' height=50 alt=logo title=logo></td></tr>
</table>
      
        <div><b>Виконавець</b> передав, а </div>
        <div><b>Замовник:</b> <?php if($order->org_id==11||$order->org_id==354) {echo $order->contacter;}else {echo $order->short_name;}?></div>
        <div>прийняв наступні пристрої: </div>  
        <br>
<table border=1 width=100% rules="all" class="border_1px">

   <tr><td style="font:bold 10pt arial;" align=center valign=bottom width="5%">№</td>
    <td style="font:bold 10pt arial;" align=center valign=bottom width="50%">Найменування</td>
    <td style="font:bold 10pt arial;" align=center valign=bottom width="45%">Аксесуари</td>
    
   <?php $n=1;?>
<?php foreach($techs->result() as $tech):?>
    <tr><td height=28 align="center"><?php echo $n;?></td>
        <td valign="top"><?php echo $tech->tech_name;?><br>
            <b>s/n:</b><?php echo $tech->serial_num;?></td>
        <td>
            <?php foreach ($attach[$tech->serial_num] as $att): ?>
            <?php echo $att->text;?><br>
            <?php endforeach;?>
        </td>
        
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
        
       

</body>
</html>