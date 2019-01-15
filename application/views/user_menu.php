<?php
$start_date=mktime(0, 0, 0, date('m'), 1, date('Y'));
$end_date=mktime(23, 59, 59, date('m')+1, 0, date('Y'));
//$start_date=mktime(0, 0, 0, date('m')-1, 1, date('Y'));
//$end_date=mktime(23, 59, 59, date('m'), 0, date('Y'));

$cart_done=$this->cartridge->get_count($start_date,$end_date,$org_id=false);
?>
<body>
<div class="row">
    <div class="span8 alert alert-error">
        ВНИМАНИЕ! Данные в заказах не обновляются автоматически. <b>Для обновление данных нажмите F5 или ctrl+R</b>
    </div>
    <div style="width: 350px;" class="span4 alert alert-success" id="cart_done">
        В этом месяце Мы заправили <b><?php echo $cart_done;?></b> картриджей
    </div>
</div>
   <table width="100%">
       <tr>
           <td width="*"><?php echo $tomain;?></td>
           <td width="20"></td>
           <td width="80" align="right"><?php echo anchor('main/logout',lang('menu_exit'));?></td>
       </tr>
   </table>
        <div style="font: 16px arial normal;
             border-bottom: 1px red solid;
             border-top:1px red solid;
             padding-bottom: 3px;
             padding-top: 3px;">
<table border="0" width="100%"><tr><td align="left" width="" valign="top"><?php echo $usermenu;?></td><td width="*"></td><td width=250 valign="top">
<?php if($this->ion_auth->is_admin()) echo $searchform;?>
        </td></tr></table></div>


