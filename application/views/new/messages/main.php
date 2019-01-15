
<div class="container">
    <div class="row">
        <div class="col">
            <div class="alert alert-primary">
                <h4 class="alert-heading" onclick="viewTasks();">Мої взавдання
                <button type="button" class="btn btn-large btn-link" data-toggle="modal" data-target="#addTask" data-whatever="@mdo"><i class="fa fa-plus"></i></button>
                </h4>
            </div>
        </div>
    </div>
    <div class="row" id="tasks">

    </div>


<div class="modal fade" id="addTask" tabindex="-1" role="dialog" aria-labelledby="addTask" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addTask">Нова задача</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="javascript:void(null)"  name="form" id="addTaskForm"
                      novalidate="novalidate" enctype="multipart/form-data" method="post" accept-charset="utf-8">
                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label">Тема:</label>
                        <input type="text" class="form-control" id="subject" name="subject">
                    </div>
                    <div class="form-group">
                        <label for="message-text" class="col-form-label">Опис:</label>
                        <textarea class="form-control" id="text" name="text"></textarea>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрити</button>
                <input id="createButton" type="submit" name="submit" value="Створити" class="btn btn-primary btn-sm" onclick="addTask();">
            </div>
        </div>
    </div>
</div>



<script type="text/javascript" language="javascript">
    function addTask() {
        $('#tasks').fadeOut('fast');
        $("#createButton").attr("disabled",true);
        var msg   = $('#addTaskForm').serialize();
        $.ajax({
            type: 'POST',
            url: "/messages/addTask/",
            data: msg,
            success: function(data) {
                viewTasks();
                $('#subject').val('');
                $('#text').val('');
                $('#addTask').modal('hide');
            },
            error:  function(xhr, str){
                alert('Возникла ошибка: ' + xhr.responseCode);
            }
        });

        $("#createButton").attr("disabled",false);
    }

    function viewTasks() {
        $.ajax({
            type: 'GET',
            url: "/messages/viewTasks/",
            success: function(data) {
                $('#tasks').html(data);
                $('#tasks').fadeIn('slow');
            },
            error:  function(xhr, str){
                alert('Виникла помилка. Перезавантажте сторінку.' + xhr.responseCode);
            }
        });
    }

    function taskDone(taskId) {
        $('#'+taskId).attr('disabled',true);
        $.ajax(
            {
                type: 'GET',
                url: "/messages/taskDone/" + taskId,
                success: function () {
                    $('#'+taskId).fadeOut('fast');
                }
            }
        )
    }

    function taskRefresh(taskId) {
        $('#'+taskId).attr('disabled',true);
        $.ajax(
            {
                type: 'GET',
                url: "/messages/taskRefresh/" + taskId,
                success: function () {
                    $('#'+taskId).fadeOut('slow');
                    viewTasks();
                }
            }
        )
    }

    setInterval(function(){
        $("#tasks").load(viewTasks());
    }, 30000);
</script>
