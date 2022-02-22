<?php
//  todas las etiquetas, etiquetas RC, propietarios con email, personalizado (tildar a mano)
$resumencuenta = $email = [];
$todas = $propietarios;
foreach ($propietarios as $prop) {
    $p = $prop['Propietario'];
    if ($p['imprime_resumen_cuenta'] === true) {
        $resumencuenta[$p['id']] = $p;
    }
    if ($p['email'] !== "") {
        $email[$p['id']] = $p;
    }
}
?>


<html>
    <head><title>Etiquetas</title>
        <script src="/sistema/js/jq.js"></script>
        <script>
            function change(cual) {
                $("div[id^='prop']").each(function () {
                    $(this).show('fast');
                    if (!$(this).hasClass(cual)) {
                        $(this).hide('fast');
                    }
                });
            }
        </script>
        <style>
            @page{
                margin: 1.6mm 0 0 0;
            }
            @media print{
                body{
                    margin-top:3mm !important;
                }
                img{
                    display:none;
                }
                .noimprimir{
                    display:none;
                }
            }
        </style>
    </head>
    <body>
        <p class="noimprimir">
            <a href="#" onclick="change('todas');">Todas</a>
            <a href="#" onclick="change('resumencuenta');">Resumenes</a>
            <a href="#" onclick="change('email');">Email</a>
        </p>
        <div class="cajas index" id="seccionaimprimir" style='text-align:left;padding-left:5px;margin-top:3mm !important;width:21cm;'>
            <?php
            $i = 0;
            $fila = 1;
            $style = "display:inline-block;width:7cm;height:32.7mm;";
            $style .= 'font-size:10px;font-family: "Courier New", Courier, "Lucida Sans Typewriter", "Lucida Typewriter", monospace;';
            foreach ($propietarios as $prop) {
                if ($i == 0) {
                    //echo "<div style='height:auto;border:1px solid red'>";
                }

                $p = $prop['Propietario'];

                // agrego etiquetas como class segun esté en las listas $resumencuenta,$email,$todas
                $etiquetas = 'todas';
                if (in_array($p['id'], array_keys($resumencuenta))) {
                    $etiquetas .= ' resumencuenta';
                }
                if (in_array($p['id'], array_keys($email))) {
                    $etiquetas .= ' email';
                }



                echo "<div id='prop" . $p['id'] . "' style='$style' class='$etiquetas'>";
                echo "<img src='/sistema/img/drop.png' style='position:relative;float:left' onclick='$(\"#prop" . $p['id'] . "\").hide()'/>";

                // detalle propietario
                echo "<p><br><b>" . h($p['name']) . "<br>" . h("(" . $p['code'] . ") " . $p['unidad']) . "</b><br>";
                echo "" . h($p['postal_address']) . "<br>" . h($p['postal_city']) . "</p>";

                // detalle remitente
                echo "<div style='text-align:center;'>" . h($_SESSION['Auth']['User']['Client']['name']) . "</div>"; //"<br>";
                //echo h($_SESSION['Auth']['User']['Client']['address']) . " - ";
                //echo h($_SESSION['Auth']['User']['Client']['city']);  //echo "</div>";                  

                echo "</div>";
                if ($i == 2) {
                    //echo "</div>";
                    $i = -1;
                }
                $i++;
            }
            ?>
        </div>
    </body>
</html>