<div class="colaimpresionesdetalles view">
    <h2><?php echo __('Colaimpresionesdetalle'); ?></h2>
    <fieldset>
        		<b><?php echo __('Id'); ?>: </b>
		<?php echo h($colaimpresionesdetalle['Colaimpresionesdetalle']['id']); ?>
			&nbsp;
		<br>
		<b><?php echo __('Colaimpresione Id'); ?>: </b>
		<?php echo h($colaimpresionesdetalle['Colaimpresionesdetalle']['colaimpresione_id']); ?>
			&nbsp;
		<br>
		<b><?php echo __('Reporte'); ?>: </b>
		<?php echo h($colaimpresionesdetalle['Colaimpresionesdetalle']['reporte']); ?>
			&nbsp;
		<br>
		<b><?php echo __('Impreso'); ?>: </b>
		<?php echo h($colaimpresionesdetalle['Colaimpresionesdetalle']['impreso']); ?>
			&nbsp;
		<br>
		<b><?php echo __('Online'); ?>: </b>
		<?php echo h($colaimpresionesdetalle['Colaimpresionesdetalle']['online']); ?>
			&nbsp;
		<br>
    </fieldset>
</div>
<?php echo '<br>' . $this->Html->link(__('Volver'), ['action' => 'index']); ?>