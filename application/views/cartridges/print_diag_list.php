<script type="text/javascript" language="javascript">
         function prn() { i=setTimeout("window.print();",500); }</script>

<body onload="prn()">
    <div class="order_caption">Раскладка картриджей к заказу №<?php echo $order_id;?></div><br>
    <table style="font-size:15px;border: 1px solid black;" rules='all' cellpadding="3">
        <tr><td>№</td><td width="250">Название</td>
            <td width="150">Номер</td>
            <td width="50">Ячейка</td>
        <?php $num=1;?>
        
        <?php foreach ($cartridges as $cartridge):?>
            
        <tr><td style="color: black;"><?php echo $num++;?></td>
        <td style="color: black;"><?php echo $cartridge->cart_name;?></td>
        <td style="color: black;"><?php echo $cartridge->cart_num;?></td>
        <td><?php echo $cartridge->adres;?></td>
        
        
        </tr>
    <?php endforeach;?>
    
    </table>