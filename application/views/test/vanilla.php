<script>
    function fadeThis() {
        var s = document.getElementById('thing').style;
        s.opacity = 1;
        (
            function fade()
            {
                (s.opacity-=.01)<0?s.display="none":setTimeout(fade,40)
            })();
    }

</script>


<div id="thing" onclick="fadeThis();">
    Клацни сюди
</div>

<?php
/**
 * Created by PhpStorm.
 * User: cube
 * Date: 11.07.18
 * Time: 14:51
 */
