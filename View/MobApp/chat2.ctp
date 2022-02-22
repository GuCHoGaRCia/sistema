&nbsp;&nbsp;<a class='axx' onclick="go('c');" style="cursor:pointer;font-size:18px">Consultas</a> >> <a class='axx' onclick="go('c2', '<?= $p ?>');" style="cursor:pointer;font-size:18px">Iniciar Consulta</a>
<?php
$ruta = (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' ? "http://localhost/" : "https://ceonline.com.ar/");
echo '<div class="fondogris">';
if (empty($d)) {
    echo "<center><div class='info' style='margin-top:5px;width:99%'>El Propietario no se encuentra habilitado para enviar Consultas</div></center>";
} else {
    $url = $_SERVER["REMOTE_ADDR"] == '::1' ? "localhost/sistema/" : "ceonline.com.ar/p/?";
    //$propietario = 0;
    foreach ($d['l'] as $pid => $consorcio) {
        //$propietario = $pid;
        $data = json_decode($consorcio[0]['Resumene']['data'], true);
        echo "<span class='iom'><b>" . h($consorcio[0]['Client']['name']) . "<br>" . h($consorcio[0]['Consorcio']['name']);
        echo " - " . h($data['prop'][$pid]['unidad']) . "</b></span>";
        echo "<div id='consultas'>&nbsp;</div>";
        echo "<div class='inline'>";
        echo $this->Form->input('m', ['label' => false, 'div' => false, 'id' => 'msg', 'placeholder' => 'Escriba su consulta...']);
        echo "<img src=\"i/s.png\" alt='' id='send' class='send'><img src=\"i/l.gif\" alt='' class='send' id='load' style='display:none'></div>"; //class inline mensaje y boton enviar
        echo "</div>"; //consultas
    }
}
echo "</div>";
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
    .iom{
        font-size:22px;
        margin-right:15px;
    }
    .fondogris{
        height:90%;
        text-align:center;
    }
    .send{width:30px;cursor:pointer}
    .content{height:70%;overflow:hidden}
    #consultas{position:relative;width:98%;height:75%;border:1px solid #ccc;border-radius:25px;padding:2px;margin:2px auto;font-size:24px;overflow:scroll;overflow-x:hidden}
    #msg{width:88%;border-radius:25px;line-height:30px;padding:0 15px;border:1px solid #ccc;margin-right:5px;margin-top:5px;margin-bottom:20px;outline:none;font-weight:bold;font-size:22px}
    ::placeholder {color:#ccc}
    #centro{padding:0px}
    .more{cursor:pointer;font-size:18px}
    .hide{display:hidden}
    hr{margin:3px !important}
    .axx{text-decoration:none;color:#000 !important}
    .androidFix {
        overflow:hidden !important;
        overflow-y:hidden !important;
        overflow-x:hidden !important;
    }
    #consultas p{
        color:black;border-radius:25px;padding:5px;padding-left:15px;padding-right:15px;line-height:26px;margin:1px
    }
</style>
<script>
    $("#send").on("click", function (e) {
        e.preventDefault();
        if ($("#msg").val() === "") {
            a('Escriba un mensaje primero...')
            return false;
        }
        $("#send").hide();
        $("#load").show();
        $.ajax({
            type: "POST",
            beforeSend: function (g) {
                g.setRequestHeader("x-requested-with", "ar.com.ceonline.expensasceo");
            },
            url: "<?= $ruta ?>sistema/MobApp/scp",
            data: {t: gT(), m: $("#msg").val(), p: '<?= $p ?>'}
        }).done(function (msg) {
            var obj = JSON.parse(msg);
            if (obj.e === 1) {
                a(obj.d);
            } else {
                p(obj.d);
                $("#msg").val('');
            }
        }).fail(function (x, t) {
            a("No se pudo realizar la accion, verifique su conexión e intente nuevamente");
        }).always(function () {
            $("#send").show();
            $("#load").hide();
        });

    });

    var c = '<?= $consultas ?>';
    $(function () {
        p(JSON.parse(c));<?php /* La primera vez hace parse de las consultas q ya tenga el Propietario */ ?>
    });
    pr = '<?= $p ?>';
    setTimeout(function () {
        check(pr);
    }, 20000);
</script>
