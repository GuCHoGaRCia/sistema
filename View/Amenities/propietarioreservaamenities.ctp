<div id="calendar">
    <?php
    if (isset($link)) {
        ?>
        <div class="resX" style="display:none">
            <div style="text-align:center;font-weight:bold">
                <?php
                if ($config['seleccionarquienrealizalimpieza'] === true) {
                    ?>
                    <br>
                    Defina qui&eacute;n se encargar&aacute; de la limpieza al finalizar su evento<br><br>
                    <input type="radio" name="limpieza" value="p"> Particular
                    <input type="radio" name="limpieza" value="e"> Encargado<br><br><br>
                    <?php
                }
                ?>
                Para confirmar su reserva para el dia <span class='leyendadia'></span><br> presione "Reservar"
            </div>
        </div>
        <div class="cancela" style="display:none">
            <div style="text-align:center;font-weight:bold">
                Para Cancelar su reserva del dia <span class='leyendadiac'></span><br> presione "Cancelar reserva"
            </div>
        </div>
        <?php
    } else {
        if (!empty($resul)) {//hubo algun error
            echo "<div class='error'>" . h($resul) . "</div>";
            echo "</div>"; //id="calendar"
            die;
        }
    }
    ?>
    <div class='leyendas'>
        <span class='reservadoporelpropietactual' style='width:230px;margin-right:10px'>Sus Turnos</span>&nbsp;&nbsp;
        <span class='reservado' style='width:230px;margin-right:10px'>Turno reservado por otros</span>&nbsp;&nbsp;
        <span class='noreservado' style='width:230px;margin-right:10px'>Turno disponible</span>&nbsp;&nbsp;
        <span class='noreservado gris' style='width:230px;margin-right:10px'>Turno no disponible</span>
    </div><br>
    <div class='info'>Para ver detalles de los turnos en su celular, mantenga presionado sobre el turno</div>
    <?php
    //title='Gris: turnos no disponibles \n\n Verde: turnos disponibles \n\n Rojo: turnos reservados \n\n Azul: turnos reservados por usted'
    //debug($amenity);die;
    //echo "<pre>";
    //debug(json_decode($reservas, true));
    //debug($resul);
    //debug($turnos);
    //debug($config);
    //debug($diaactual);
    //debug($diafinal);
    //debug($this->webroot);
    //debug($resul);
    $permitereservacondicional = $config['reservacondicional'] ? '1' : '0';
    if (isset($link)) {
        $url = /* $this->webroot ."/amenities/propietarioreservaamenities/$link/$pid/". */"/sistema/amenities/propietarioreservaamenities/$link/$pid/";
    } else {
        $url = /* $this->webroot ."/amenities/view/". */"/sistema/amenities/view/";
    }
    ?>
    <h4>Reservas - <?= h($amenity['Amenity']['nombre'] . " - " . ($name ?? '')) ?></h4>
</div>
<script>
    function calendar(year, month, t, r) {
        try {
            var turnos = JSON.parse(t);
            var reservas = JSON.parse(r);
        } catch (err) {
            alert("No se pudieron obtener los turnos. Por vafor intente nuevamente");
            return false;
        }
        var meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
        $("#calendar").append("<h4><span id='anterior'><< </span>" + meses[month - 1] + " " + year + "<span id='siguiente'> >></span></h4>");
        var cantidadDias = new Date(year, month, 0).getDate();
        var divdia = "";
<?php
//diasemana inicio fin
// (int) 1 => array(
//     (int) 0 => array(
//         'id' => '4',// id de amenitiesturnos
//         'i' => '07:00:00',
//         'f' => '09:00:00'
//     ),
//     (int) 1 => array(
//         'id' => '35',
//         'i' => '10:30:00',
//         'f' => '10:30:00'
//     )
// ),
// dia del mes, propietario y idturno
//(int) 23 => array(
//    (int) 0 => array(
//        'p' => 'rakuzanska kasimiera - 1A (15)',
//        't' => '1'
//    )
//)
/* Amenity::getReservas(), $resul[$f]['p','t','h'], propietario, amenitiesturno_id,
 * habilitado (si la fecha del calendario es mayor igual a la actual, dejo reservar) 
  // el 1º esta vigente, los otros dos condicionales cancelados
  //		(int) 0 => array(
  //			'p' => 'Maria Juana Perez 4 - 2ºA (2)',
  //			't' => '20',
  //			'h' => (int) 1,
  //			'id' => '943',
  //			'c' => false,
  //			'm' => (int) 0,
  //			'f' => '30/11/-0001 00:00:00'
  //		),
  //		(int) 1 => array(
  //			'p' => 'garcia marcelo - 1ºA (1)',
  //			't' => '20',
  //			'h' => (int) 1,
  //			'id' => '942',
  //			'c' => true,
  //			'm' => (int) 0,
  //			'f' => '09/10/2019 12:00:18'
  //		),
  //		(int) 2 => array(
  //			'p' => 'garcia marcelo - 1ºA (1)',
  //			't' => '20',
  //			'h' => (int) 1,
  //			'id' => '942',
  //			'c' => true,
  //			'm' => (int) 0,
  //			'f' => '09/10/2019 12:31:12'
  //		)
 */
?>
        var sem = ['DOM', 'LUN', 'MAR', 'MIE', 'JUE', 'VIE', 'SAB'];
        var parts1 = '<?= $diaactual ?>'.split('-');
        var parts2 = '<?= $diafinal ?>'.split('-');
        var diaactual = new Date(parts1[0], parts1[1] - 1, parts1[2]);
        var diafinal = new Date(parts2[0], parts2[1] - 1, parts2[2]);
        var h = 0;
        for (i = 1; i <= cantidadDias; i++) {
            var actual = new Date(year, month - 1, i);
            var dia = actual.getDay() + 1;
            divdia = "<ul class='dia" + (actual.getTime() === diaactual.getTime() ? ' diaactual' : '') + "'>" + i + " (" + sem[dia - 1] + ")<hr>";
            h = (actual.getTime() < diaactual.getTime() || actual.getTime() > diafinal.getTime()) ? 0 : 1;
            jQuery.each(turnos, function (s, val) {
                if (parseInt(dia) == parseInt(s)) {<?php /* si es el dia actual */ ?>
                    jQuery.each(val, function (j, k) {
                        horarioturno = k['i'].substring(0, 5) + " a " + k['f'].substring(0, 5);
                        var clase = "";
                        var p = "";
                        var cantreservasdelturno = 0;
                        var claseyaestablecida = false;
                        var permitecancelarcondicionales = false;
                        jQuery.each(reservas, function (n, m) {
                            if (parseInt(n) === i) {<?php /* si la reserva es del dia actual */ ?>
                                jQuery.each(m, function (gg, hh) {
                                    if (hh['t'] === k['id']) {<?php /* si el id del turno reservado hh['t'] es del turno actual k['id'] */ ?>
                                        if (!hh['c']) {
                                            if (!claseyaestablecida) {
                                                clase = (hh['id'] == '<?= $pid ?? 0 ?>' ? 'reservadoporelpropietactual' : 'reservado');
                                                claseyaestablecida = true;
                                            }
                                        }
                                        p += hh['cr'] + "\n" + hh['p'];
                                        if (hh['c']) {
                                            p += "\n[ CANCELADO" + (hh['m'] === 1 ? " CON MULTA" : "") + ": " + hh['f'] + "]";
                                        } else {
                                            if (hh['id'] == '<?= $pid ?? 0 ?>') {
                                                permitecancelarcondicionales = true;
                                            }
                                            cantreservasdelturno++;
                                        }
                                        p += "\n\nCondicional\n ";
                                    }
                                });
                                p = p.substring(0, p.length - 15);
                                return;
                            }
                        });
                        clase = (clase === "" ? "noreservado" : clase);
                        divdia += "<span class='" + clase + ((h === 0) ? " gris" : '') + "'";
                        if (p !== "") {
                            divdia += " title='Reservado " + p + "' ";
                        } else if (clase === "reservadoporelpropietactual") {
                            divdia += " title='Su Turno Reservado' ";
                        } else {
                            divdia += ((h === 1) ? " title='Turno disponible para reservar' " : '');
                        }
<?php /* Si esta seteado $pid, viene del panel propietario, sino es el administrador desde Gestiones->amenities (no le permito reservar) */ ?>
                        divdia += (h === 1 && ('<?= $permitereservacondicional ?>' === '1' && cantreservasdelturno < 2 || '<?= $permitereservacondicional ?>' === '0' && cantreservasdelturno === 0 || clase === "reservadoporelpropietactual" || permitecancelarcondicionales) ? "<?= isset($pid) ? " onclick='reserva(this)' data-cancelar='\" + (permitecancelarcondicionales?1:0) +\"'  data-day='\" + actual.toLocaleDateString(\"es-ES\") +\"' data-tid='\" + k['id'] + \"'" : "" ?>>" : ">");
                        divdia += horarioturno + "</span>";
                    });
                }
            });
            divdia += "</ul>";
            $("#calendar").append(divdia);
        }

    }
<?php
$aa = $as = $año;
if ($mes == 1) {
    $anterior = 12;
    $aa--;
} else {
    $anterior = $mes - 1;
}
if ($mes == 12) {
    $siguiente = 1;
    $as++;
} else {
    $siguiente = $mes + 1;
}
/* if ($año == 2019 && $anterior == 9 || $año < 2019) {
  $aa = 2019;
  $anterior = 10;
  } */
?>
    var veranteriormes = <?= ($año == 2019 && $anterior == 9 || $año < 2019) ? 'false' : 'true' ?>;
    var url = '<?= $url . $amenity['Amenity']['id'] ?>';
    $(function () {
        if (veranteriormes) {
            $("#anterior").on("click", function () {
                cerrar();
                $('#calendar').html('<div class=\"info\" style=\"width:200px;margin:0 auto\">Cargando...<img src=\""<?= $this->webroot ?>"img/loading.gif\"/></div>');
                $('#calendar').load(url + '<?= "/$aa/$anterior" ?>');
            });
        } else {
            $("#anterior").html('');
        }
        $("#siguiente").on("click", function () {
            cerrar();
            $('#calendar').html('<div class=\"info\" style=\"width:200px;margin:0 auto\">Cargando...<img src=\""<?= $this->webroot ?>"img/loading.gif\"/></div>');
            $('#calendar').load(url + '<?= "/$as/$siguiente" ?>');
        });
    });
    calendar(<?= $año ?>, <?= $mes ?>, '<?= $turnos ?>', '<?= $reservas ?>');
<?php
if (isset($link)) {// solo para panel propietarios
    ?>
        var datosdeldia = '';
        function reserva(t) {
            datosdeldia = t;
            if ($(datosdeldia).hasClass('reservadoporelpropietactual') || (typeof $(datosdeldia).data('cancelar') !== "undefined" && $(datosdeldia).data('cancelar') === 1)) {
                $(".leyendadiac").html($(datosdeldia).data('day') + " de " + $(datosdeldia).html());
                $(".cancela").dialog('open');
            } else {
                $(".leyendadia").html($(datosdeldia).data('day') + " de " + $(datosdeldia).html());
                $("input:radio").prop("checked", false);
                $(".resX").dialog('open');
            }
        }
        ddd = $(".resX").dialog({
            autoOpen: false, width: 320,
            position: {at: "center center"},
            closeOnEscape: false,
            modal: true,
            dialogClass: "no-titlebar",
            buttons: {
                Reservar: function () {
    <?php
    if ($config['seleccionarquienrealizalimpieza'] === true) {//valido, si se habilita el check de limpieza, que seleccione algo
        ?>
                        if (!$("input[name='limpieza']:checked").val()) {
                            alert("Debe seleccionar quien realiza la limpieza");
                            return false;
                        }
                        var data = {l: '<?= $link ?>', pid: <?= $pid ?>, aid: <?= $amenity['Amenity']['id'] ?>, tid: $(datosdeldia).data('tid'), f: $(datosdeldia).data('day'), li: $("input[name='limpieza']:checked").val()};
        <?php
    } else {
        ?>
                        var data = {l: '<?= $link ?>', pid: <?= $pid ?>, aid: <?= $amenity['Amenity']['id'] ?>, tid: $(datosdeldia).data('tid'), f: $(datosdeldia).data('day')};
        <?php
    }
    ?>
                    envia(data, 'reservar');
                    ddd.dialog("close");
                },
                Cancelar: function () {
                    ddd.dialog("close");
                }
            },
        });
        cancel = $(".cancela").dialog({
            autoOpen: false, width: 320,
            position: {at: "center center"},
            closeOnEscape: false,
            modal: true,
            dialogClass: "no-titlebar",
            buttons: {
                "Cancelar Reserva": function () {
                    envia({l: '<?= $link ?>', pid: <?= $pid ?>, aid: <?= $amenity['Amenity']['id'] ?>, tid: $(datosdeldia).data('tid'), f: $(datosdeldia).data('day')}, 'cancelar');
                    cancel.dialog("close");
                },
                Cerrar: function () {
                    cancel.dialog("close");
                }
            }
        });
    <?php /* Si creo o cancelo una reserva, uso la misma funcion (cambia el data). Cierro los 2 dialogos */ ?>
        function envia(data, path) {
            $.ajax({type: "POST", url: "<?= $this->webroot ?>amenities/" + path, cache: false,
                data: data
            }).done(function (msg) {
                try {
                    var obj = JSON.parse(msg);
                    alert(obj.d);
                    $('#calendar').html('<div class=\"info\" style=\"width:200px;margin:0 auto\">Cargando...<img src=\""<?= $this->webroot ?>"img/loading.gif\"/></div>');
                    $('#calendar').load(url + '<?= "/$año/$mes" ?>');
                } catch (err) {
                    //
                }
            }).fail(function (jqXHR, textStatus) {
                if (jqXHR.status === 403) {
                    alert("No se pudo realizar la accion. Verifique que se encuentra logueado en el sistema");
                } else {
                    alert("No se pudo realizar la accion, intente nuevamente");
                }
            });
        }
    <?php
}
?>
    function cerrar() {
        try {
            ddd.dialog("close");
            ddd.dialog("destroy");
            cancel.dialog("close");
            cancel.dialog("destroy");
        } catch (err) {
            //
        }
    }
</script>
<style>
    .leyendas{text-align:center}
    #calendar{display:block}
    .dia{
        text-align:center;
        width:14%;
        min-width:135px;
        display:inline-block;
        border:1px solid black;
        padding:5px;
        height:auto;
        min-height:120px;
        margin:1px;
    }
    .diaactual{border:2px solid blue;background-color:#E6F1FB}
    .reservado,.noreservado,.reservadoporelpropietactual{float:left;width:100%;padding:0px 10px;color:white;margin-top:1.3px;border-radius:25px;cursor:pointer}
    .reservado{background-color:red}
    .reservadoporelpropietactual{background-color:blue}
    .reservado:hover{font-weight:700;background-color:orange;color:black}
    .noreservado{background-color:green}
    .noreservado:hover{font-weight:700;background-color:lightgreen;color:black}
    .noreservado.gris,.noreservado.gris:hover{background-color:grey !important;cursor:default}
    hr{margin:0;border:1px solid grey}
    #anterior,#siguiente{cursor:pointer}
    #siguiente{margin-left:30px}
    #anterior{margin-right:30px}
    #anterior:hover,#siguiente:hover{text-decoration:underline}
    .no-titlebar .ui-dialog-titlebar {display: none}
    .leyendadia{text-decoration:underline;white-space:nowrap;margin:15px}
    .ui-tooltip{font-size:12px !important}
</style>
