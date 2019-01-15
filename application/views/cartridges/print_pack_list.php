<script type="text/javascript" language="javascript">
         function prn() { i=setTimeout("window.print();",500); }</script>

<body onload="prn()">

<?php if($param=="from_master"):?>
    <a href="/master_cartridge">
<?php endif;?> 
    
<?php foreach ($cartridges->result() as $cartridge):?>
    <table  rules="all" >
        <tr>
            <td style="font: bold 14pt arial;text-align: center;" width="20"><?php echo $cartridge->adres;?></td>
            <td style="font: bold 14pt arial;text-align: center;" width="90"><?php echo $cartridge->cart_num;?></td>
            <td style="font: bold 14pt arial;text-align: center;" width="90"><?php echo $order_id;?></td>
            <td style="font: bold 14pt arial;text-align: center;" width="20"><?php echo $cart_in_order;?></td>
            
        </tr>
        <tr>
            <td colspan="4" style="font: normal 12pt arial; border: 1px solid black;">
                <?php echo $cartridge->cart_name;?></td>
        </tr>
        <tr>
            <td colspan="4" style="font: normal 12pt arial;">
                <?php echo character_limiter($cartridge->org_name,25);?></td>
        </tr>
    </table>       
    <table><tr><td width="10"></td></tr></table>
<?php endforeach;?>

 <?php if($param=="from_master"):?>
    </a>
<?php endif;?> 