    <script>
    function tech_list()  
        {  
            $.ajax({
                 type: "GET",
                 url: "/techs/mega_table/",
                data: "",
                cache: false,  
                success: function(html){  
                    $("#tech_list").html(html);
                }  
            });
          }  
          
                    
    $(function()
    {
        tech_list();  
        setInterval('tech_list()',240000);
    }
);
    </script>
    <script src="/assets/bootstrap/js/bootstrap.min.js"></script>
 <link href="/assets/bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
   
    <div id="tech_list"></div>
