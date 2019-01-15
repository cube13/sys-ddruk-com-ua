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
                font: normal 10pt times-new-roman;

}        </style>

          </head>
          
    <body >
        <?php $MONTH_UA=array("Нуль","Січня","Лютого","Березня","Квітня", "Травня", "Червня","Липня","Серпня","Вересня","Жовтня","Листопада","Грудня");
            $invoiceType=array('nal' => 'Квитанція про оплату',
                'bnlfop' => 'Рахунок-фактура',
                'bnltov' => 'Рахунок-фактура',
                'subscr' => 'Використання обенементу',
                                );
//            foreach ($accounting->result() as $key => $val)
//            {
//                $accData[$key]=$val;
//            }
//            echo $accData['date'];
          $DATE=date('d')." ".$MONTH_UA[intval(date('m'))]." ".date('Y')." р.";
         //$DATE=date('d.m.Y',);
        ?>

    <div align="right" style="font:bold 10pt times-new-roman;"><?php echo $DATE;?></div><br/>
    <div align="center" style="font:bold 11pt times-new-roman;"><?php echo $invoiceType[$paymethod]." № ".$order_id;?></div><br/>
    <div align="left" style="font:bold 11pt times-new-roman;">Платник: </div><br/>

    <table align="center" rules="all" border="1" style="font: normal 10pt times-new-roman;">
     <tr valign="top"><td width="20" align="center">№</td>
         <td width="450" align="center">Найменування</td>
         <td width="50" align="center">Од. вим.</td>
         <td width="40" align="center">К-сть</td>
         <td width="40" align="center">Ціна</td>
         <td width="50" align="center">Сума</td></tr>
<?php $SUMA=0;$n=0;$prev_text="";$prev_num="";$count=1;$nums="";?>   
<?php foreach ($invoice->result() as $invoice_item) :?>
<?php 

if($invoice_item->text==$prev_text)
{
    $count++;
   $nums.='; '.$invoice_item->uniq_num;
    
} 
else
{
    $n++;
    $count=1;
    $nums=$invoice_item->uniq_num;
}


$suma[$n]=$invoice_item->price*$count;

 $prev_text=$invoice_item->text;
         $prev_num=$invoice_item->uniq_num;
         ?>
 
   <?php 
   $search=array('(delivery)','(extra)');
   $text=str_replace($search, '', $invoice_item->text.' ('.$nums.')');
   
   $table[$n]= '<tr><td align="center">'.$n.'</td>
        <td align="left">'.$text.'</td>
        <td align="center">шт.</td>
        <td align="center">'.$count.'</td>
        <td align="center">'.$invoice_item->price.'</td>
        <td align="center">'.$suma[$n].'</td></tr>';
   
   ?>
   

<?php  endforeach;?>
    
    <?php 
     $n=1;
    foreach ($table as $tr) {
        echo $tr;
        $SUMA+=$suma[$n];
        $n++;
        }?>
  
    </table>
    <br/><br/>

    <div style="font: normal 11pt times-new-roman;">
    Всього на суму <?php echo $SUMA;?> грн. без ПДВ<br/><br/>
    Послуги надав______________________________</div>
    
    <br/><br/>
 <table align=left width=100% border=0>
    <tr>
        <td style="border-top:1px black dashed;" valign=top align=center><img src='/assets/img/logo_new.jpg' height=50 alt=logo title=logo>
            
        </td>
        <td width="20" style="border-top:1px black dashed;"></td>
        <td style="font:normal 12pt arial;border-top:1px black dashed;" align=left valign=top>
            Сервісний центр "Добрий друк"
            <div style="font:bold 11pt arial;">Київ, вул. Мельникова,2/10, офіс 413<br>
            тел. 392-86-87; 093-755-98-38; 097-672-73-74</div>
        </td>
     </tr>
     
</table>   
</body>


</html>