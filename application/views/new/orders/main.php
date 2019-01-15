<?php
$userId=0;
if(!$this->ion_auth->is_admin()) $userId=$this->ion_auth->user()->row()->id;

?>
<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <header class="page-header">
                <div class="container-fluid" onclick="getOrders();">
                    <h2 class="no-margin-bottom">Мої замовлення &nbsp;&nbsp;&nbsp;
                        <small>
                            <button id="refresh_orders" type="button" class="btn btn-small" onclick="getOrders();">
                                <i id="icon-refresh" class="fa fa-refresh"></i>
                            </button>&nbsp;&nbsp;&nbsp;
                        <?php if($this->ion_auth->users()->row()->id==32 || $this->ion_auth->users()->row()->id==33|| $this->ion_auth->users()->row()->id==14|| $this->ion_auth->users()->row()->id==1) :?>
                        <?php echo anchor_popup('orders/quick_create', lang('menu_orders_create'),'class="btn btn-small"');?></h2>
                        <?php endif;?>
                    </small>
                </div>
            </header>
        </div>
    </div>
    <?php //if($this->ion_auth->users()->row()->id==32 || $this->ion_auth->users()->row()->id==33 || $this->ion_auth->users()->row()->id==1):?>
    <div class="nav-menu no-padding no-margin">
            <div class="btn-group btn-group-toggle" data-toggle="buttons">
                <label class="btn btn-outline-primary active" onclick="getOrders();">
                    <input type="radio" name="users" id="0" autocomplete="off" checked>Всі
                </label>
                <?php foreach ($managers as $id => $name):?>
                    <label class="btn btn-outline-primary" >
                        <input type="radio" name="users" id="<?php echo $id;?>" autocomplete="off" onchange="getOrders();">  <?php echo $name;?>
                    </label>
                <?php endforeach;?>
            </div>
    </div>
    <?php //endif;?>
    <br>
    <?php //if($this->ion_auth->users()->row()->id==1):?>
    <div class="btn-group btn-group-toggle" data-toggle="buttons">
        <label class="btn btn-outline-primary" >
            <input type="radio" name="filterstage" id="project" autocomplete="off" onchange="StageFilter();">Всі
        </label>
        <label class="btn btn-outline-primary active" >
            <input type="radio" name="filterstage" id="tofcdl" autocomplete="off" onchange="StageFilter();">В офіс
        </label>
        <label class="btn btn-outline-primary" >
            <input type="radio" name="filterstage" id="toclnt" autocomplete="off" onchange="StageFilter();">Клієнту
        </label>
        <label class="btn btn-outline-primary" >
            <input type="radio" name="filterstage" id="pprdoc" autocomplete="off" onchange="StageFilter();">Доки
        </label>
    </div>

    <?php //endif;?>
    <div class="row">
        <div class="col-lg-12" id="orders">

        </div>
    </div>
</div>

<script type="text/javascript" language="javascript">



    function get_orders(userId=0) {
        $("#refresh_orders").attr("disabled",true);
        $("#icon-refresh").addClass('fa-spin');
        $.ajax({
            url: "/orders/getorders/"+userId,
            success: function (html) {
                $("#orders").html(html);
                $("#refresh_orders").attr("disabled",false);
                $("#icon-refresh").removeClass('fa-spin');
                StageFilter();
            },
            error:  function(xhr, str){
                //alert('Виникла помилка. Перезавантажте сторінку.' + xhr.responseCode);
            }
        });
    }

    function getOrders() {
        id=$("input[type=radio][name=users]:checked" ).attr("id");
        get_orders(id);

      }

    function StageFilter() {
        stage=$("input[type=radio][name=filterstage]:checked" ).attr("id");
        $(".project" ).hide();
        $("." + stage ).show();
    }

    function SearchOrder(id)
    {
        $.ajax({
            url: "/orders/search_action/"+id,
            success: function (html) {
                $("#orders").html(html);
            },
            error:  function(xhr, str){
                //alert('Виникла помилка. Перезавантажте сторінку.' + xhr.responseCode);
            }
        });
    }


    $(function () {
        getOrders();
        StageFilter();
        setInterval("getOrders()",120000);
        $(".project").show();

    });



</script>