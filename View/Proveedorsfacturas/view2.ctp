<div class="proveedorsfacturas view">
    <h2><?php echo __('Factura de proveedor'); ?></h2>
    <fieldset>
        <b><?php echo __('Consorcio - Liquidación'); ?>: </b>
        <?php echo h($proveedorsfactura['Consorcio']['name'] . " - " . $proveedorsfactura['Liquidation']['periodo']); ?>
        &nbsp;
        <br>
        <b><?php echo __('Concepto'); ?>: </b>
        <?php echo h($proveedorsfactura['Proveedorsfactura']['concepto']); ?>
        &nbsp;
        <br>
        <b><?php echo __('Fecha'); ?>: </b>
        <?php echo $this->Time->format(__('d/m/Y'), $proveedorsfactura['Proveedorsfactura']['fecha']); ?>
        &nbsp;
        <br>
        <b><?php echo __('Importe'); ?>: </b>
        <?php echo h(__($proveedorsfactura['Proveedorsfactura']['importe'])); ?>
        &nbsp;
        <br>
        <b><?php echo __('Número de factura'); ?>: </b>
        <?php echo h(__($proveedorsfactura['Proveedorsfactura']['numero'])); ?>
        &nbsp;
        <br>
        <b><?php echo __('Creado'); ?>: </b>
        <?php echo $this->Time->format(__('d/m/Y H:i:s'), $proveedorsfactura['Proveedorsfactura']['created']); ?>
        &nbsp;
        <br>
        <b><?php echo __('Modificado'); ?>: </b>
        <?php echo $this->Time->format(__('d/m/Y H:i:s'), $proveedorsfactura['Proveedorsfactura']['modified']); ?>
        &nbsp;
        <br>
    </fieldset>
</div>