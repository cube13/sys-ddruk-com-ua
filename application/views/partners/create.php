<div id="infoMessage"><?php echo $message;?></div>
<?php echo form_open("partners/create");?>
    <table border=1>
            <tr><td>
            <table>
                <tr><td><?php echo lang('partners_full_name')?></td><td><?php echo form_input($full_name);?></td></tr>
                <tr><td><?php echo lang('partners_short_name')?> </td><td><?php echo form_input($short_name);?></td></tr>
                <tr><td><?php echo lang('partners_yur_adres')?></td><td><?php echo form_input($adres);?></td></tr>
        <tr><td><?php echo lang('partners_tel')?></td><td><?php echo form_input($tel);?>&nbsp;&nbsp;&nbsp;<?php echo lang('partners_fax')?>&nbsp;&nbsp;&nbsp;<?php echo form_input($fax);?></td></tr>
            </table>
            </td></tr> 
            <tr><td><b><?php echo lang('partners_details');?></b>
            <table>
            <tr><td><?php echo lang('partners_edrpou')?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo form_input($edrpou);?>
                &nbsp;&nbsp;&nbsp;<?php echo lang('partners_ipn')?> <?php echo form_input($ipn);?></td></tr>
             <tr><td><?php echo lang('partners_svid_pdv')?><?php echo form_input($svid_pdv);?>
                </td></tr>
        <tr><td><?php echo lang('partners_mfo')?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo form_input($mfo);?>
        &nbsp;&nbsp;<?php echo lang('partners_bank_accnt')?> <?php echo form_input($bank_accnt);?></td></tr>
        <tr><td><?php echo lang('partners_direktor')?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo form_input($direktor);?></td></tr>
        </table>
        </table>
        <?php echo form_submit('submit', lang('partners_submit_create'));?>
<?php echo form_close();?>             