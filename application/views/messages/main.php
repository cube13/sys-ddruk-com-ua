<script src="/assets/bootstrap/js/bootstrap.min.js"></script>
<link href="/assets/bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">

<table class="table table-bordered table-condensed table-hover">
    <thead>
        <tr>
            <th width="150">
                Дата отправки
            </th>
            <th width="250">
                Тема
            </th>
            <th>
                Собщение
            </th>
            <th>
                От
            </th>
            <th width="50">
                
            </th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($mess->result() as $message):?>
        <?php if(!$mess->isread):?> 
            <tr class="info">
        <?php endif;?>
        <?php if($mess->isread):?> 
            <tr class="">
        <?php endif;?>
        
                <td width="150">
                <?php echo date('d.m.Y H:i',$message->add_date);?>
            </td>
            <td>
                <?php echo $message->subject;?>
            </td>
            <td>
                <?php echo $message->text;?>
            </td>
            <td>
                <?php echo $message->first_name.' '.$message->last_name;?>
            </td>
            <td width="200">
                <a href="/messages/answer/" title="Ответать">Ответ</a> | 
                <?php if(!$message->isread):?>
                <a href="/messages/mess_readed/<?php echo $message->id?>" title="Прочитано"><i class="icon-ok"></i></a>
                <?php else:?>
                <a href="/messages/hide_mess/<?php echo $message->id?>" title="Удалить"><i class="icon-remove"></i></a>
                <?php endif;?>
            </td>
            
        </tr>
        <?php endforeach;?>
    </tbody>
</table>

