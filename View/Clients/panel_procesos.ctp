<div class="clients form">
    <h2>Ejecutar procesos generales</h2>
    <div class='info'>En esta secci&oacute;n se podr&aacute;n realizar procesos de Admin que permitan realizar, por ejemplo, c&aacute;lculos y modificaciones internas sin
        necesidad de desbloquear liquidaciones
    </div>
    <?php
    echo "<div class='info'>Actualizo el importe de los Pagos a Proveedor ya realizados, sumando facturas y pago a cuenta y restando notas de credito aplicadas y pagos a cuenta aplicados";
    echo "<div class='warning' style='width:160px'>" . $this->Html->link(__('Ejecutar proceso'), ['action' => 'actualizaImportesPagoProveedor'], [], __('Desea ejecutar el proceso? El mismo demora unos instantes')) . "</div>";
    echo "</div>";

    echo "<div class='info'>Actualizo los Saldos de Caja y Banco de Todos los Consorcios (Resumen Caja Banco)";
    echo "<div class='warning' style='width:160px'>" . $this->Html->link(__('Ejecutar proceso'), ['action' => 'saldosResumenCajaBanco'], [], __('Desea ejecutar el proceso? El mismo demora unos instantes')) . "</div>";
    echo "</div>";

    echo "<div class='info'>Para los Saldos Cierre de cada Propietario actualizo los valores de Cobranzas, Ajustes, Gastos generales, Gastos particulares, Inter&eacute;s Actual, Capital Anterior, Inter&eacute;s Anterior, Redondeo Anterior";
    echo "<div class='warning' style='width:160px'>" . $this->Html->link(__('Ejecutar proceso'), ['action' => 'actualizaSaldosCierres'], [], __('Desea ejecutar el proceso? El mismo demora unos instantes')) . "</div>";
    echo "</div>";

    echo "<div class='info'>Para las Liquidaciones, actualizo el Estado de disponibilidad.<br>";
    echo "<div class='warning' style='width:160px'>" . $this->Html->link(__('Ejecutar proceso'), ['action' => 'actualizaEstadoDisponibilidad'], [], __('Desea ejecutar el proceso? El mismo demora unos instantes')) . "</div>";
    echo "</div>";

    echo "<div class='info'>Actualizo el n&uacute;mero de recibo de las Cobranzas de cada Cliente de forma num&eacute;rica autoincremental de cero a n";
    echo "<div class='warning' style='width:160px'>" . $this->Html->link(__('Ejecutar proceso'), ['action' => 'actualizaNumeroReciboCobranza'], [], __('Desea ejecutar el proceso? El mismo demora unos instantes')) . "</div>";
    echo "</div>";

    echo "<div class='info'>Genera las formas de pago de los Propietarios (para el Panel de Propietario y que utilicen Informe de pagos) para cada cliente que no tenga actualmente";
    echo "<div class='warning' style='width:160px'>" . $this->Html->link(__('Ejecutar proceso'), ['action' => 'generarFormasdePago'], [], __('Desea ejecutar el proceso? El mismo demora unos instantes')) . "</div>";
    echo "</div>";

    echo "<div class='info'>Si se agregan consorcios automaticamente, usar esta funcion para crear las liquidaciones iniciales, los presupuestos y los saldos iniciales de los Propietarios";
    echo "<div class='warning' style='width:160px'>" . $this->Html->link(__('Ejecutar proceso'), ['controller' => 'Consorcios', 'action' => 'creaLiquidacionesIniciales'], [], __('Desea ejecutar el proceso? El mismo demora unos instantes')) . "</div>";
    echo "</div>";

    echo "<div class='info'>Genero los saldos iniciales de los tipos de liquidación para cada consorcio (aquellos q no hayan sido creados todavia)";
    echo "<div class='warning' style='width:160px'>" . $this->Html->link(__('Ejecutar proceso'), ['controller' => 'SaldosInicialesConsorcios', 'action' => 'generar'], [], __('Desea ejecutar el proceso? El mismo demora unos instantes')) . "</div>";
    echo "</div>";

    echo "<div class='info'>Actualiza los Saldos de los Proveedores de TODOS los Clientes (Facturas - Pagos)";
    echo "<div class='warning' style='width:160px'>" . $this->Html->link(__('Ejecutar proceso'), ['controller' => 'Proveedors', 'action' => 'actualizaSaldosProveedor'], [], __('Desea ejecutar el proceso? El mismo demora unos instantes')) . "</div>";
    echo "</div>";

    echo "<div class='info'>Cifra los nombres de los Adjuntos para m&aacute;s seguridad";
    echo "<div class='warning' style='width:160px'>" . $this->Html->link(__('Ejecutar proceso'), ['action' => 'cifrarURLAdjuntos'], [], __('Desea ejecutar el proceso? El mismo demora unos instantes')) . "</div>";
    echo "</div>";

    echo "<div class='info'>Actualizo el Saldo ACTUAL de TODAS las cuentas bancarias (utilizando el ultimo Saldocajabanco encontrado, o sea, el del dia anterior). Incluye los movimientos realizados hasta el momento";
    echo "<div class='warning' style='width:160px'>" . $this->Html->link(__('Ejecutar proceso'), ['action' => 'actualizaSaldoCuentasBancarias'], [], __('Desea ejecutar el proceso? El mismo demora unos instantes')) . "</div>";
    echo "</div>";

    echo "<div class='info'>Actualizo el Saldo ACTUAL de TODAS las Cajas (Ingresos menos Egresos)";
    echo "<div class='warning' style='width:160px'>" . $this->Html->link(__('Ejecutar proceso'), ['action' => 'actualizaSaldoCajas'], [], __('Desea ejecutar el proceso? El mismo demora unos instantes')) . "</div>";
    echo "</div>";

    echo "<div class='info'>Limpiar HTML de las Notas, Comunicaciones, Gastos Generales, Reparaciones y demás (script tag, javascript, etc)";
    echo "<div class='warning' style='width:160px'>" . $this->Html->link(__('Ejecutar proceso'), ['action' => 'processCleanHTML'], [], __('Desea ejecutar el proceso? El mismo demora unos instantes')) . "</div>";
    echo "</div>";

    echo '<br><br><br>' . $this->Html->link(__('Cancelar'), ['action' => 'index'], [], __('Desea cancelar?'));
    ?>
</div>