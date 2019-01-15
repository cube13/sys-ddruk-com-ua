<?php echo form_open("partners/edit/".$partner_id."/1");?>
<div class="row-fluid">
    <div class="span6">
<table width="100%" class="table table-bordered table-condensed" border="0">
    <tr><td><h4>Реквизиты</h4></td><td><?php echo form_submit('submit', lang('partners_submit_change'));?></td></tr>
    <tr><td width="150">Полное имя</td><td width="*"><?php echo form_input($full_name);?></td></tr>
    <tr><td><?php echo lang('partners_short_name')?> </td><td><?php echo form_input($short_name);?></td></tr>
    <tr><td><?php echo lang('partners_yur_adres')?></td><td><?php echo form_input($adres);?></td></tr>
    <tr><td><?php echo lang('partners_tel')?></td><td><?php echo form_input($tel);?></td></tr>
    <tr><td><?php echo lang('partners_direktor')?></td><td><?php echo form_input($direktor);?></td></tr>
    <tr><td><?php echo lang('partners_edrpou')?></td><td width="*"><?php echo form_input($edrpou);?></td></tr>
    <tr><td><?php echo lang('partners_ipn')?> </td><td><?php echo form_input($ipn);?></td></tr>
    <tr><td><?php echo lang('partners_svid_pdv')?></td><td><?php echo form_input($svid_pdv);?></td></tr>
    <tr><td><?php echo lang('partners_mfo')?></td><td><?php echo form_input($mfo);?></td></tr>
    <tr><td><?php echo lang('partners_bank_accnt')?> </td><td><?php echo form_input($bank_accnt);?></td></tr>
    <tr><td><?php echo lang('partners_discount')?></td><td><?php echo form_input($discount);?></td></tr>
    <tr><td>Способ оплаты</td><td>
            <?php
            echo form_radio($bnltov)."Б/н ТОВ ДД<br>";
            echo form_radio($bnltovitfs)."Б/н ТОВ ITFS<br>";
            echo form_radio($bnltovfsu)."Б/н ТОВ ФСУ<br>";
         echo form_radio($bnlfop)."Б/н ФОП<br>";
         echo form_radio($nal)."Нал";?></td></tr>
    <tr><td>Договор</td><td><?php echo form_input($contract);?></td></tr>
</table>
    </div>
    
    <div class="span6"><form action="/">
<table width="100%" class="table table-bordered table-condensed" border="0">
    <tr><td width="200"><h4>Контактные данные</h4></td>
        <td><?php echo form_submit('submit', lang('partners_submit_change'));?></td></tr>
    <?php foreach ($contacts->result() as $contact):?>
            <tr><td><?php echo lang($contact->type);?></td><td>
                <input type="text" class="span12" name="<?php echo $contact->id;?>" value="<?php echo $contact->value;?>"></td></tr>
            <?php endforeach;?>
</table></form>
<br>
        
    </div>
    
</div>
<?php //echo form_submit('submit', lang('partners_submit_change'));?>
<?php echo form_close();?>             