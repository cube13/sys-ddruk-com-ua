<script src="/assets/bootstrap/js/bootstrap.min.js"></script>
<link href="/assets/bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">

<?php echo form_open("messages/add/0/0/usrmsg/");?>
<fieldset>
    <legend>Новое сообщение</legend>
    <label>Тема</label>
    <input type="text" placeholder="" name="subject" class="span6">
    <br>
    <label>Текст сообщения:</label>
    <textarea placeholder="" name="text" rows="10" class="span6"></textarea>
    <br>
    <div class="row">
        <div class="span3">
            <label>Кому</label>
    <select name="to_user_id">
    <?php foreach ($users->result() as $user):?>
  <option value="<?php echo $user->id;?>"><?php echo $user->first_name.' '.$user->last_name;?></option>
  <?php endforeach;?>
    </select>
        </div>
        <div class="span3">
            <label>Дата доставки</label>
            <input style="width:75px;" type="text" name="date" 
           value="<?php echo date('d.m.Y');?>" id="datepicker"/> 
        </div>
    </div>
    <br><button type="submit" class="btn btn-primary"><?php echo lang('menu_messages_send');?></button>
</fieldset>
<?php echo form_close();?>             
