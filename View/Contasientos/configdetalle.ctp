<?php
$opciones = ['0' => 'Debe', '1' => 'Haber'];
$c = json_decode($config, true);
//debug($c);
?>
<div id='seccionaimprimir'>
    <h4>Liquidaciones</h4>
    <?php
    echo $this->Form->create('Contasiento', ['class' => 'jquery-validation', 'multiple' => 'multiple', 'id' => 'configuracion']);
    echo $this->Form->input('consorcio_id', ['name' => "data[Contasiento][consorcio_id]", 'type' => 'hidden', 'value' => $consorcio_id]);
    ?>
    <table>
        <tr>
            <td class='cuentas' colspan="2">RUBROS GASTOS GENERALES</td>
            <td colspan='3'>
                <?php
                if (empty($rubros)) {
                    echo "<tr><td class='info' colspan=3>El Consorcio no posee Rubros de Gastos Generales configurados</td></tr>";
                }
                foreach ($rubros as $k => $v) {
                    echo "<tr><td>&nbsp;</td>";
                    echo "<td>&nbsp;&nbsp;" . h($v) . "</td>";
                    $selected = $c['liquidaciones']['rubros'][$k] ?? '';
                    echo "<td>" . $this->Form->input($k, ['label' => false, 'div' => false, 'name' => "data[Contasiento][liquidaciones][rubros][$k]", 'class' => 's2', 'options' => $contcuentas, 'selected' => $selected, 'empty' => '']) . "</td>";
                    echo "</tr>";
                }
                ?>
            </td>
        </tr>
        <tr>
            <td class='cuentas' colspan="2">Interes</td>
            <td>
                <?= $this->Form->input('liquidaciones_interes', ['label' => false, 'div' => false, 'name' => "data[Contasiento][liquidaciones][interes]", 'class' => 's2', 'options' => $contcuentas, 'type' => 'select', 'selected' => $c['liquidaciones']['interes'] ?? '', 'empty' => '']); ?>
            </td>
        </tr>
        <tr>
            <td class='cuentas' colspan="2">Cuentas gastos particulares</td>
            <td colspan='3'>
                <?php
                if (empty($cuentasgp)) {
                    echo "<tr><td class='info' colspan=3>El Consorcio no posee Cuentas de Gastos Particulares configuradas</td></tr>";
                }
                foreach ($cuentasgp as $k => $v) {
                    echo "<tr><td>&nbsp;</td>";
                    echo "<td>&nbsp;&nbsp;" . h($v) . "</td>";
                    $selected = $c['liquidaciones']['cuentasgp'][$k] ?? '';
                    echo "<td>" . $this->Form->input('liquidaciones_cuentasgp_' . $k, ['label' => false, 'div' => false, 'name' => "data[Contasiento][liquidaciones][cuentasgp][$k]", 'class' => 's2', 'options' => $contcuentas, 'selected' => $selected, 'empty' => '']) . "</td>";
                    echo "</tr>";
                }
                ?>
            </td>
        </tr>
        <tr>
            <td class='cuentas' colspan="2">Redondeo</td>
            <td>
                <?= $this->Form->input('liquidaciones_redondeo', ['label' => false, 'div' => false, 'name' => "data[Contasiento][liquidaciones][redondeo]", 'class' => 's2', 'options' => $contcuentas, 'type' => 'select', 'selected' => $c['liquidaciones']['redondeo'] ?? '', 'empty' => '']); ?>
            </td>
        </tr>
        <tr>
            <td class='cuentas' colspan="2">Cierre</td>
            <td>
                <?= $this->Form->input('liquidaciones_cierre', ['label' => false, 'div' => false, 'name' => "data[Contasiento][liquidaciones][cierre]", 'class' => 's2', 'options' => $contcuentas, 'type' => 'select', 'selected' => $c['liquidaciones']['cierre'] ?? '', 'empty' => '']); ?>
            </td>
        </tr>
    </table>
    <h4>Cobranzas</h4>
    <table>
        <tr>
            <td class='cuentas' colspan="2">Cobranzas</td>
            <td><?= $this->Form->input('cobranzas_cobranzas', ['label' => false, 'div' => false, 'name' => "data[Contasiento][cobranzas][cobranzas]", 'class' => 's2', 'options' => $contcuentas, 'type' => 'select', 'selected' => $c['cobranzas']['cobranzas'] ?? '', 'empty' => '']); ?></td>
        </tr>
        <tr>
            <td class='cuentas' colspan="2">Cierre</td>
            <td><?= $this->Form->input('cobranzas_cierre', ['label' => false, 'div' => false, 'name' => "data[Contasiento][cobranzas][cierre]", 'class' => 's2', 'options' => $contcuentas, 'type' => 'select', 'selected' => $c['cobranzas']['cierre'] ?? '', 'empty' => '']); ?></td>
        </tr>
    </table>
    <h4>Compras</h4>
    <table>
        <tr>
            <td class='cuentas' colspan="2">Facturas Proveedor</td>
            <td><?= $this->Form->input('compras_facturasproveedor', ['label' => false, 'div' => false, 'name' => "data[Contasiento][compras][facturasproveedor]", 'class' => 's2', 'options' => $contcuentas, 'type' => 'select', 'selected' => $c['compras']['facturasproveedor'] ?? '', 'empty' => '']); ?></td>
        </tr>
        <tr>
            <td class='cuentas' colspan="2">Cierre</td>
            <td><?= $this->Form->input('compras_cierre', ['label' => false, 'div' => false, 'name' => "data[Contasiento][compras][cierre]", 'class' => 's2', 'options' => $contcuentas, 'type' => 'select', 'selected' => $c['compras']['cierre'] ?? '', 'empty' => '']); ?></td>
        </tr>
    </table>
    <h4>Pagos</h4>
    <table>
        <tr>
            <td class='cuentas' colspan="2">Proveedores</td>
            <td><?= $this->Form->input('pagos_proveedores', ['label' => false, 'div' => false, 'name' => "data[Contasiento][pagos][proveedores]", 'class' => 's2', 'options' => $contcuentas, 'type' => 'select', 'selected' => $c['pagos']['proveedores'] ?? '', 'empty' => '']); ?></td>
        </tr>
        <tr>
            <td class='cuentas' colspan="2">Cierre</td>
            <td><?= $this->Form->input('pagos_cierre', ['label' => false, 'div' => false, 'name' => "data[Contasiento][pagos][cierre]", 'class' => 's2', 'options' => $contcuentas, 'type' => 'select', 'selected' => $c['pagos']['cierre'] ?? '', 'empty' => '']); ?></td>
        </tr>
    </table>
    <h4>Cajas</h4>
    <table>
        <tr>
            <td class='cuentas' colspan="2">Ingresos</td>
            <td><?= $this->Form->input('cajas_ing', ['label' => false, 'div' => false, 'name' => "data[Contasiento][cajas][ingresos]", 'class' => 's2', 'options' => $contcuentas, 'type' => 'select', 'selected' => $c['cajas']['ingresos'] ?? '', 'empty' => '']); ?></td>
        </tr>
        <tr>
            <td class='cuentas' colspan="2">Pagos</td>
            <td><?= $this->Form->input('cajas_pagos', ['label' => false, 'div' => false, 'name' => "data[Contasiento][cajas][pagos]", 'class' => 's2', 'options' => $contcuentas, 'type' => 'select', 'selected' => $c['cajas']['pagos'] ?? '', 'empty' => '']); ?></td>
        </tr>
        <tr>
            <td class='cuentas' colspan="2">Depositos Efectivo</td>
            <td><?= $this->Form->input('cajas_depositosefectivo', ['label' => false, 'div' => false, 'name' => "data[Contasiento][cajas][depositosefectivo]", 'class' => 's2', 'options' => $contcuentas, 'type' => 'select', 'selected' => $c['cajas']['depositosefectivo'] ?? '', 'empty' => '']); ?></td>
        </tr>
        <tr>
            <td class='cuentas' colspan="2">Depositos Cheques</td>
            <td><?= $this->Form->input('cajas_depositoscheques', ['label' => false, 'div' => false, 'name' => "data[Contasiento][cajas][depositoscheques]", 'class' => 's2', 'options' => $contcuentas, 'type' => 'select', 'selected' => $c['cajas']['depositoscheques'] ?? '', 'empty' => '']); ?></td>
        </tr>
        <tr>
            <td class='cuentas' colspan="2">Cierre</td>
            <td><?= $this->Form->input('cajas_cierre', ['label' => false, 'div' => false, 'name' => "data[Contasiento][cajas][cierre]", 'class' => 's2', 'options' => $contcuentas, 'type' => 'select', 'selected' => $c['cajas']['cierre'] ?? '', 'empty' => '']); ?></td>
        </tr>
    </table>
    <h4>Bancos</h4>
    <table>
        <tr>
            <td class='cuentas' colspan="2">Ingresos</td>
            <td><?= $this->Form->input('bancos_ing', ['label' => false, 'div' => false, 'name' => "data[Contasiento][bancos][ingresos]", 'class' => 's2', 'options' => $contcuentas, 'type' => 'select', 'selected' => $c['bancos']['ingresos'] ?? '', 'empty' => '']); ?></td>
        </tr>
        <tr>
            <td class='cuentas' colspan="2">Pagos</td>
            <td><?= $this->Form->input('bancos_pagos', ['label' => false, 'div' => false, 'name' => "data[Contasiento][bancos][pagos]", 'class' => 's2', 'options' => $contcuentas, 'type' => 'select', 'selected' => $c['bancos']['pagos'] ?? '', 'empty' => '']); ?></td>
        </tr>
        <tr>
            <td class='cuentas' colspan="2">Depositos Efectivo</td>
            <td><?= $this->Form->input('bancos_depositosefectivo', ['label' => false, 'div' => false, 'name' => "data[Contasiento][bancos][depositosefectivo]", 'class' => 's2', 'options' => $contcuentas, 'type' => 'select', 'selected' => $c['bancos']['depositosefectivo'] ?? '', 'empty' => '']); ?></td>
        </tr>
        <tr>
            <td class='cuentas' colspan="2">Depositos Cheques</td>
            <td><?= $this->Form->input('bancos_depositoscheques', ['label' => false, 'div' => false, 'name' => "data[Contasiento][bancos][depositoscheques]", 'class' => 's2', 'options' => $contcuentas, 'type' => 'select', 'selected' => $c['bancos']['depositoscheques'] ?? '', 'empty' => '']); ?></td>
        </tr>
        <tr>
            <td class='cuentas' colspan="2">Transferencias interbancarias</td>
            <td><?= $this->Form->input('bancos_transferenciasinterbancarias', ['label' => false, 'div' => false, 'name' => "data[Contasiento][bancos][transferenciasinterbancos]", 'class' => 's2', 'options' => $contcuentas, 'type' => 'select', 'selected' => $c['bancos']['transferenciasinterbancos'] ?? '', 'empty' => '']); ?></td>
        </tr>
        <tr>
            <td class='cuentas' colspan="2">CIERRE</td>
            <td colspan='3'>
                <?php
                if (empty($bancoscuentas)) {
                    echo "<tr><td class='info' colspan=3>El Consorcio no posee Cuentas bancarias configuradas</td></tr>";
                }
                foreach ($bancoscuentas as $k => $v) {
                    echo "<tr><td>&nbsp;</td>";
                    echo "<td>&nbsp;&nbsp;" . h($v) . "</td>";
                    echo "<td>" . $this->Form->input($k, ['label' => false, 'div' => false, 'name' => "data[Contasiento][bancos][cierre][$k]", 'class' => 's2', 'options' => $contcuentas, 'type' => 'select', 'selected' => $c['bancos']['cierre'][$k] ?? '', 'empty' => '']) . "</td>";
                    echo "</tr>";
                }
                ?>
            </td>
        </tr>
    </table>
    <center>
        <?php echo $this->Form->end(['label' => __('Guardar'), 'id' => 'guardar']); ?>
    </center>
</div>
<script>
    $(document).ready(function () {
        $(".s2").select2({language: "es", allowClear: true, placeholder: "Seleccione Cuenta..."});
        $(".s2").each(function (index) {
            if ($(this).val() === "") {
                $(this).siblings(".select2-container").css('border', '2px solid red');
            } else {
                $(this).siblings(".select2-container").css('border', 'none');
            }
        });
    });
    $("#guardar").click(function (e) {
        e.preventDefault();
        $("#guardar").prop('disabled', true);
        $.ajax({
            type: "POST",
            url: "<?= $this->webroot ?>Contasientos/config",
            data: $("#configuracion").serialize()
        }).done(function (msg) {
            try {
                var obj = JSON.parse(msg);
                if (obj.e === 1) {
                    alert(obj.d);
                    $("#guardar").prop('disabled', false);
                } else {
                    window.location.reload(false);
                }
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
    });
</script>
<style>
    .desc{display:inline-block}
    .contasientos{margin:0 auto}
    .cuentas{width:280px;border-bottom:1px solid gray;text-transform:uppercase}
    table{max-width:650px !important;min-width:650px !important}
    h4{width:650px;margin:0 auto;margin-top:40px}
</style>