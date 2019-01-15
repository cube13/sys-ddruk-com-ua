<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf8">
        <title></title>
        <script type="text/javascript" language="javascript">
         <!--
                        function prn() { i=setTimeout('window.print();',2000); }
--></script>
        <style>
            table.border_1px{
                border: solid black 1px;
                font: normal 10pt times-new-roman;

}        </style>

          </head>
    <body  onload="prn()">

<table align=center width=100% border=0>
<tr><td style="font:bold 16pt times-new-roman;" align=center valign=top>
        Квитанція № <?php echo $order->id;?><br>
<div style="font:bold 8pt times-new-roman;">про приймання передачу картриджу</br> на заправку/відновлення</div></td>
<td width=20></td>
<td valign=top align=right><img src='/assets/img/logo_new.jpg' width=100 alt=logo title=logo></td></tr>
<tr><td colspan=3 >
<table width=100% border=0>
<tr><td style="font:bold 11pt times-new-roman;" width=80>Замовник:</td>
    <td><?php echo $order->short_name;?></td></tr>
</table>
</td></tr>
<tr><td colspan=3 style="font:bold 11pt times-new-roman;">
<table width=100%>
<tr><td style="font:bold 10pt times-new-roman;" width=140>Контактні дані:</td>
    <td style="font:normal 10pt times-new-roman;" align="left">
        <?php echo $order->contacter;
        if($order->phonemob) echo ', '.$order->phonemob;
        if($order->phone) echo ', '.$order->phone;
        if($order->adres) echo ', '.$order->adres;
        if($order->other_info) echo br().'<b>'.$order->other_info.'</b>';
        ?></td></tr>
</table>
</td></tr>
<tr><td colspan=3>
<table border=1 width=100% rules="all" class=border_1px>

<tr><td style="font:bold 12pt times-new-roman;" align=left valign=top>Найменування картриджу</td><td style="font:bold 12pt times-new-roman;" align=center valign=top>кількість</td></tr>
<tr><td height=23></td><td></td></tr>
<tr><td height=23></td><td></td></tr>
<tr><td height=23></td><td></td></tr>
<tr><td height=23></td><td></td></tr>

</td></tr>

</table>
<table width=100%>
<tr><td colspan=4 height=10></td></tr>
<tr><td style="font:bold 13pt times-new-roman;" width=50>Підпис:</td>
<td style="border-bottom: solid 2px;"; width=250></td>
<td style="font:bold 13pt times-new-roman;" width=80>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Дата:</td>
<td font:normal 12pt times-new-roman; width=250><?php echo date('d.m.Y');?></td></tr>

</table>

<br><table align=center width=100% border=0>
<tr><td style="font:bold 16pt times-new-roman;" align=center valign=top>Квитанція № <?php echo $order->id;?><br>
<div style="font:bold 8pt times-new-roman;">про приймання передачу картриджу на заправку/відновлення</div>
<div style="font:normal 8pt times-new-roman;">(картридж видається на підставі цієї квитанції)</div>
<div style="font:bold 10pt times-new-roman;">Київ, вул. Мельникова, 2/10</br>тел. 237-15-55 тел.вн. 75-16</td>

<td valign=top align=right><img src='/assets/img/logo_new.jpg' width=90 alt=logo title=logo>
</td></tr>
<tr><td colspan=2>
<table width=100% border=0>
<tr><td style="font:bold 13pt times-new-roman;" width=80>Замовник:</td>
    <td><?php if($order->org_id==11) echo $order->contacter;?>
    <?php if($order->org_id!=11) echo $order->short_name;?>
    </td></tr>
</table>
</td></tr>

<tr><td colspan=2>
<table border=1 width=100% rules="all" class=border_1px>

<tr><td style="font:bold 12pt times-new-roman;" align=left valign=top>Найменування картриджу</td><td style="font:bold 12pt times-new-roman;" align=center valign=top>кількість</td></tr>
<tr><td height=23></td><td></td></tr>
<tr><td height=23></td><td></td></tr>
<tr><td height=23></td><td></td></tr>
<tr><td height=23></td><td></td></tr>

</td></tr>

</table>
<table width=100%>
<tr><td colspan=4 height=10></td></tr>
<tr><td style="font:bold 13pt times-new-roman;" width=50>Підпис:</td>
<td style="border-bottom: solid 2px;"; width=250></td>
<td style="font:bold 13pt times-new-roman;" width=80>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Дата:</td>
<td font:normal 12pt times-new-roman; width=250><?php echo date('d.m.Y');?></td></tr>

</table>
        
        

</body>
</html>