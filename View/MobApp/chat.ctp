<style>
    .fondogris{
        margin:0px;
        text-align:center;
    }
</style>
<?php
$ruta = (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' ? "http://localhost/" : "https://ceonline.com.ar/");

echo '<div class="fondogris">';
if (count($d['l']) == 0) {
    echo "<center><div class='info' style='margin-top:5px;width:99%'>No se encontraron Propiedades asociadas</div></center>";
} else {
    echo "<center><div class='info' style='margin-top:5px;width:99%'>Haga click en una Propiedad para enviar una Consulta</div></center>";
    ?>
    <style>
        #pri li{
            list-style-type:none;
            text-align:left;
        }
        #pri li ul li{
            padding-left:15px;
            font-size:20px;
        }
        .cont{
            width:99%;border:1px solid gray;padding:5px;margin:5px auto;
            border-radius:21px;
            -moz-border-radius:4px;
            -webkit-border-radius:4px;
            -moz-box-shadow:5px 5px 8px #CCC;
            -webkit-box-shadow:5px 5px 8px #CCC;
            box-shadow:5px 5px 8px #CCC;
            font-weight:bold !important;
            cursor:pointer;
        }
        .iom{
            font-size:22px;
            margin-right:15px;
        }
        .iom:hover,a:hover{
            text-decoration:underline !important;
        }
        .periodo{
            font-size:22px;
        }
        #centro{
            padding:0px;
        }
        .more{cursor:pointer;font-size:18px}
        .hide{display:hidden}
        hr{margin:3px !important}
        .androidFix {
            overflow:hidden !important;
            overflow-y:hidden !important;
            overflow-x:hidden !important;
        }
    </style>
    <?php
    //$d['h'] tiene los hash de los propietario_id
    foreach ($d['l'] as $pid => $consorcio) {
        $data = json_decode($consorcio[0]['Resumene']['data'], true);
        echo "<div class='cont' onclick='go(\"c2\",\"" . $d['h'][$pid] . "\")'>";
        echo "<span class='iom'><b>" . h($consorcio[0]['Client']['name']) . "<br>" . h($consorcio[0]['Consorcio']['name']);
        echo " - " . h($data['prop'][$pid]['unidad']) . "</b></span>";
        echo "</div>"; //cont
    }
}
echo "</div>";
?>
<script>
    var pr = 0;
    function p(obj) {
        if (obj !== '') {
            var x = "";
            try {
                for (j = 0; j < obj.length; j++) {
                    x = "<p style='background:" + (obj[j]['Consultaspropietario']['r'] ? '#f2e6ff;text-align:left' : '#f1fce8;text-align:right') + "' title='" + obj[j][0]['f'] + "'>" + obj[j]['Consultaspropietario']['m'] + "</p>" + x;
<?php /* $("<p style='background:" + (obj[j]['Consultaspropietario']['r'] ? '#f2e6ff;text-align:left' : '#f1fce8;text-align:right') + "' title='" + obj[j][0]['f'] + "'>" + obj[j]['Consultaspropietario']['m'] + "</p>").prependTo("#consultas"); */ ?>
                }
                if (x !== "") {
                    $("#consultas").html(x);
                    $("#consultas").addClass("androidFix").scrollTop($("#consultas")[0].scrollHeight).removeClass("androidFix");
                }
            } catch (err) {
            }
        }
    }
    function check() {
        $.ajax({
            type: "POST",
            beforeSend: function (g) {
                g.setRequestHeader("x-requested-with", "ar.com.ceonline.expensasceo");
            },
            url: "<?= $ruta ?>sistema/MobApp/gcp",
            data: {t: gT(), p: pr}
        }).done(function (msg) {
            var obj = JSON.parse(msg);
            if (obj.e === 0) {
                p(obj.d);
                setTimeout(function () {
                    check(pr);
                }, 20000);
            }
        });
    }

</script>
