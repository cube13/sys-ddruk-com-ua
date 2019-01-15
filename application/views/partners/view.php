<div class="order_caption">
    <?php echo anchor('/partners/edit/'.$partner->id,$partner->short_name);?>
</div>
<br/>
<div class="row-fluid">
    <div class="span6">
<table width="100%" class="table table-bordered table-condensed" border="0">
    <tr><td colspan="2"><h4>Реквизиты</h4></td></tr>
    <tr><td width="120">Полное имя</td><td width="*"><?php echo $partner->full_name;?></td></tr>
    <tr><td width="120">Короткое имя</td><td width="*"><?php echo $partner->short_name;?></td></tr>
    <tr><td width="120"><?php echo lang('partners_tel')?></td><td><?php echo $partner->tel;?></td></tr>
    <tr><td>Адрес юр.</td><td><?php echo $partner->adres;?></td></tr>
    <tr><td>Директор</td><td><?php echo $partner->direktor;?></td></tr>
    <tr><td>ЕДРПОУ</td><td width="*"><?php echo $partner->edrpou;?></td></tr>
    <tr><td>ИНН</td><td><?php echo $partner->ipn;?></td></tr>
    <tr><td>№ свидетельства НДС </td><td><?php echo $partner->svid_pdv;?></td></tr>
    <tr><td>МФО банка</td><td><?php echo $partner->mfo;?></td></tr>
    <tr><td>Р/с</td><td><?php echo $partner->bank_accnt;?></td></tr>
    <tr><td>Скидка</td><td><?php echo $partner->discount;?></td></tr>
    <tr><td>Способ оплаты</td><td><?php echo lang($partner->paymethod);?></td></tr>
    <tr><td>Договор</td><td><?php echo $partner->contract;?></td></tr>
    <tr><td>Баланс</td><td><?php echo $partner->balance;?></td></tr>
</table>
    </div>
    
    <div class="span6">
        <table width="100%" class="table table-bordered table-condensed" border="0">
            <tr><td colspan="3"><h4>Контактные данные</h4></td></tr>
            <?php foreach ($contacts->result() as $contact):?>
            
            <tr><td width="150"><?php echo lang($contact->type);?></td><td><?php echo $contact->value;?></td>
                <td width="15"><a href="/messages/del_org_contact/<?php echo $contact->id;?>"><i class="icon-remove"></i></a></td></tr>
            <?php endforeach;?>
           
            <tr><form action="/messages/add_org_contact/<?php echo $partner->id;?>" method="post" accept-charset="utf-8">
                <td>
                    <select style="width: 120px;" name="type">
                        <option value="contact-info">Доп.инфо</option>
                        <option value="contact-name">Имя</option>
                        <option value="contact-mob">Моб.тел.</option>
                        <option value="contact-tel">Тел.</option>
                        <option value="contact-fax">Факс</option>
                        <option value="contact-email">e-mail</option>
                        <option value="contact-adres">Адрес</option>
                    </select>
                </td><td colspan="2"><input class="span12" value="" type="text" name="text">

                </td></form>
            </tr>
    
        </table>
    </div>
    
</div>
      
<?php //if($this->ion_auth->is_admin() || $this->ion_auth->user()->row()->id==7|| $this->ion_auth->user()->row()->id==16):?>
<?php echo '<'.anchor('partners/view/'.$partner->id.'/orders','Заказы').'> '
        .'<'.anchor('partners/view/'.$partner->id.'/cartridges','Картриджи').'> '
        .'<'.anchor('partners/view/'.$partner->id.'/prices','Цены').'><br/><br/>';?>
<?php //endif; ?>

     