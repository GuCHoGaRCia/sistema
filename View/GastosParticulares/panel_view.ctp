<div class="gastosParticulares view">
    <h2><?php echo __('Gastos Particulares'); ?></h2>
    <fieldset>
        <b><?php echo __('Fecha'); ?>: </b>
        <?php echo $this->Time->format(__('d/m/Y'), $gastosParticulare['GastosParticulare']['date']); ?>
        &nbsp;
        <br>
        <b><?php echo __('Descripción'); ?>: </b>
        <?php echo $gastosParticulare['GastosParticulare']['description']; ?>
        &nbsp;
        <br>
        <b><?php echo __('Importe'); ?>: </b>$
        <?php echo h(__($gastosParticulare['GastosParticulare']['amount'])); ?>
        &nbsp;
        <br>
        <b><?php echo __('Número Factura'); ?>: </b>
        <?php echo h(__($gastosParticulare['GastosParticulare']['numero_factura'])); ?>
        &nbsp;
        <br>
        <b><?php echo __('Creado'); ?>: </b>
        <?php echo $this->Time->format(__('d/m/Y H:i:s'), $gastosParticulare['GastosParticulare']['created']); ?>
        &nbsp;
        <br>
        <b><?php echo __('Modificado'); ?>: </b>
        <?php echo $this->Time->format(__('d/m/Y H:i:s'), $gastosParticulare['GastosParticulare']['modified']); ?>
        &nbsp;
        <br>
    </fieldset>
</div>
<?php echo '<br>' . $this->Html->link(__('Cancelar'), array('action' => 'index'), array(), __('Desea cancelar?')); ?>