<div class="content-inner">
    <!-- Page Header-->
    <header class="page-header">
        <div class="container-fluid">
            <h2 class="no-margin-bottom">Паналь задач</h2>
        </div>
    </header>
    <?php if($this->ion_auth->is_admin()):?>
        <div class="nav-menu no-padding no-margin">
            <div class="btn-group btn-group-toggle" data-toggle="buttons">
                <?php foreach ($couriers as $id => $name):?>
                    <label class="btn btn-outline-primary" >
                        <input type="radio" name="users" id="<?php echo $id;?>" autocomplete="off" onchange="userFilterA();">  <?php echo $name;?>
                    </label>
                <?php endforeach;?>

            </div>
    </div>
    <?php endif;?>

    <!-- Projects Section-->
    <section class="projects">
        <div class="container-fluid" onclick="get_stage('tofcdl','<?php echo $user_id;?>');"> Выезд к клиенту/Забрать у клиента &nbsp;
            <button id="refresh-tofcdl" type="button" class="btn btn-small" onclick="get_stage('tofcdl','<?php echo $user_id;?>');">
                <i id="icon-refresh-tofcdl" class="fa fa-refresh"></i>
            </button>&nbsp;&nbsp;&nbsp;</div>
        <div class="container-fluid">

            <div id="tofcdl" ></div>
        </div>
    </section>

    <!-- Projects Section-->
    <section class="projects">
        <div class="container-fluid" onclick="get_stage('toclnt','<?php echo $user_id;?>');">
                Доставки клиенту
            <button id="refresh-toclnt" type="button" class="btn btn-small" onclick="get_stage('toclnt','<?php echo $user_id;?>');">
                <i id="icon-refresh-toclnt" class="fa fa-refresh"></i>
            </button>&nbsp;&nbsp;&nbsp;
        </div>
        <div class="container-fluid">
            <div id="toclnt"></div>
        </div>
    </section>


    <!-- Page Footer-->
    <footer class="main-footer">
        <div class="container-fluid">
            <div class="row">

            </div>
        </div>
    </footer>


    <script type="text/javascript" language="javascript">

        $(function() {
            get_stage("tofcdl",'<?php echo $user_id;?>');
            get_stage("toclnt",'<?php echo $user_id;?>');

         //   setInterval(get_stage("tofcdl"), 120000);

         //   setInterval(get_stage("toclnt"), 120000);
        });

        function userFilter(userId)
        {
            get_stage('toclnt',userId);
            get_stage('tofcdl',userId);

        }

        function userFilterA()
        {
            userId=$( "input[type=radio][name=users]:checked" ).attr("id");
            get_stage('toclnt',userId);
            get_stage('tofcdl',userId);

        }

        function get_stage(stage,userId=0) {
            $("#refresh-"+stage).attr("disabled",true);
            $("#icon-refresh-"+stage).addClass('fa-spin');
            $.ajax({
                url: "/orders/stage_dynamic/"+stage+"/"+userId,
                success: function (html) {
                    $("#"+stage).html(html);
                    $("#refresh-"+stage).attr("disabled",false);
                    $("#icon-refresh-"+stage).removeClass('fa-spin');
                },
                error:  function(xhr, str){
                    //alert('Виникла помилка. Перезавантажте сторінку.' + xhr.responseCode);
                }

            });
        }

    </script>