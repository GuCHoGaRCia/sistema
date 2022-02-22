<div class="gastosGenerales view">
    <h2><?php echo __('Gastos Generale'); ?></h2>
    <fieldset>
        <b><?php echo __('Consorcio'); ?>: </b>
        <?php echo $gastosGenerale['Consorcio']['name']; ?>
        &nbsp;
        <br>
        <b><?php echo __('Liquidación'); ?>: </b>
        <?php echo $gastosGenerale['Liquidation']['periodo']; ?>
        &nbsp;
        <br>
        <b><?php echo __('Descripción'); ?>: </b>
        <?php echo $gastosGenerale['GastosGenerale']['description']; ?>
        &nbsp;
        <br>
        <b><?php echo __('Importe'); ?>: </b>
        <?php echo h(__($gastosGenerale['GastosGeneraleDetalle']['amount'])); ?>
        &nbsp;
        <br>
        <b><?php echo __('Creado'); ?>: </b>
        <?php echo $this->Time->format(__('d/m/Y H:i:s'), $gastosGenerale['GastosGenerale']['created']); ?>
        &nbsp;
        <br>
        <b><?php echo __('Modificado'); ?>: </b>
        <?php echo $this->Time->format(__('d/m/Y H:i:s'), $gastosGenerale['GastosGenerale']['modified']); ?>
        &nbsp;
        <br>
    </fieldset>
</div>
<?php echo '<br>' . $this->Html->link(__('Volver'), array('action' => 'index')); ?>