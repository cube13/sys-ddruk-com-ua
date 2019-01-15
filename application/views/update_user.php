   	<div id="infoMessage"><?php echo $message;?></div>
	
    <?php echo form_open("admin/update_user/".$userid."/1");?>
      
        <table>
            <tr><td width="100"><?php echo lang('form_create_user_name');?></td><td><?php echo form_input($first_name);?></td></tr>
            <tr><td><?php echo lang('form_create_user_sername');?></td><td><?php echo form_input($last_name);?></td></tr>
            <tr><td><?php echo lang('form_create_user_company');?></td><td><?php echo form_input($company);?></td></tr>
            <tr><td><?php echo lang('form_create_user_email');?></td><td><?php echo form_input($email);?></td></tr>
            <tr><td><?php echo lang('form_create_user_phone');?></td><td><?php echo form_input($phone);?></td></tr>
            <tr><td><?php echo lang('form_create_user_login');?></td><td><?php echo form_input($login);?></td></tr>
            
            <tr><td></td><td><?php echo form_submit('submit', lang('form_update_user_submit'));?></td></tr>
            
        </table>
      
    <?php echo form_close();?>
    <br/><br/>
    <?php
$table="";
    for ($month=1;$month<=date('m');$month++)
    {
        $date_start = mktime(0, 0, 0, $month, 1, date('Y'));
        $date_end = mktime(23, 59, 59, $month+1, 0, date('Y'));

        $inofc = $this->cartridge->get_count_cart_stage('inofc', $date_start, $date_end, $userid, 0, 0)->row();
        $apprv = $this->cartridge->get_count_cart_stage('apprv', $date_start, $date_end, $userid, 0, 0)->row();
        $topck = $this->cartridge->get_count_cart_stage('topck', $date_start, $date_end, $userid, 0, 0)->row();
        $inrfl = $this->cartridge->get_count_cart_stage('inrfl', $date_start, $date_end, $userid, 0, 0)->row();
        $todsp = $this->cartridge->get_count_cart_stage('todsp', $date_start, $date_end, $userid, 0, 0)->row();

        $table.='<tr>
            <td>'.$month.'</td>
            <td>'.$inofc->count.'</td>
            <td>'.$apprv->count.'</td>
            <td>'.$topck->count.'</td>
            <td>'.$todsp->count.'</td>
            <td>'.$inrfl->count.'</td>
            
        </tr>';
    }
    // work  cart on last month




    ?>

    <table class="table table-bordered table-striped">
        <thead>
        <tr>
            <td></td>
            <td>Привіз</td>
            <td>Апруви</td>
            <td>Запаковано</td>
            <td>Відвезено</td>
            <td>Заправленно</td>

        </tr>
        </thead>
        <tbody>
        <?php echo $table;?>
        </tbody>
    </table>


    