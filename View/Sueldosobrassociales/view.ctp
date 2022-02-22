<div class="sueldosobrassociales view">
    <h2><?php echo __('Sueldosobrassociale'); ?></h2>
    <fieldset>
        		<b><?php echo __('Id'); ?>: </b>
		<?php echo h(__($sueldosobrassociale['Sueldosobrassociale']['id'])); ?>
			&nbsp;
		<br>
		<b><?php echo __('Codigo'); ?>: </b>
		<?php echo h(__($sueldosobrassociale['Sueldosobrassociale']['codigo'])); ?>
			&nbsp;
		<br>
		<b><?php echo __('Nombre'); ?>: </b>
		<?php echo h(__($sueldosobrassociale['Sueldosobrassociale']['nombre'])); ?>
			&nbsp;
		<br>
		<b><?php echo __('Created'); ?>: </b>
		<?php echo $this->Time->format(__('d/m/Y H:i:s'), $sueldosobrassociale['Sueldosobrassociale']['created']); ?>
			&nbsp;
		<br>
		<b><?php echo __('Modified'); ?>: </b>
		<?php echo $this->Time->format(__('d/m/Y H:i:s'), $sueldosobrassociale['Sueldosobrassociale']['modified']); ?>
			&nbsp;
		<br>
    </fieldset>
</div>
<?php echo '<br>' . $this->Html->link(__('Volver'), array('action' => 'index')); ?>