<script type="text/javascript" language="javascript">
         function prn() { i=setTimeout("window.print();",10); }</script>
<body onload='prn_()'>
    <div class="order_caption"><?php echo $title;?></div><br>
    <?php echo form_open('/cartridges/to_apprv/'.$order_id)?>
    <table style="font-size:15px;border: 1px solid black;" rules='all' cellpadding="3">
        <tr><td>№</td><td width="200">Название</td>
            <td width="70">Номер</td>
            <td width="60">Все ОК</td>
            <td width="50">Дефект</td>
            <td width="50">Ячейка</td>
        <?php $num=1;?>
        
        <?php foreach ($cartridges as $cartridge):?>
            <?php
        
            $hvdef_ch='';$nodef_ch='';
            if($cartridge->info=='hvdef') $hvdef_ch='checked';
            if($cartridge->info=='nodef') $nodef_ch='checked';
            $hvdef = array('name' => 'defect_'.$cartridge->cart_num,
                      'value' => 'hvdef',
			'checked' => $hvdef_ch
                	);
            $nodef = array('name' => 'defect_'.$cartridge->cart_num,
                      'value' => 'nodef',
			'checked' => $nodef_ch
                	);
        $defect=form_radio($hvdef);
        $nodefect=form_radio($nodef);
        
        if(!$cartridge->adres){$adres_param = array('name' => 'adres_'.$cartridge->cart_num,
			'id' => 'adres',
			'type' => 'text',
                        'size'=>'3',
                        'value' => ''
                	);$adres=form_input($adres_param);}
                        else{$adres=$cartridge->adres;}
            ?>
        <tr><td style="color: black;"><?php echo $num++;?></td>
        <td style="color: black;"><?php echo $cartridge->cart_name;?></td>
        <td style="color: black;"><?php echo $cartridge->cart_num;?></td>
        <td><?php echo $nodefect;?></td>
        <td><?php echo $defect;?></td>
        <td><?php echo $adres;?><div id="answer" font-size:16px;"></div></td>
        
        
        </tr>
    <?php endforeach;?>
    
    </table>
        <?php echo form_submit('submit','Отправить на согласование');?>
    <?php echo form_close();?>
    