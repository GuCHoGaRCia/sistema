<div class="resumenes view">
    <h2><?php echo __('Resumene'); ?></h2>
    <dl>
        		<dt><?php echo __('Id'); ?></dt>
		<dd>
			<?php echo h($resumene['Resumene']['id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Liquidation'); ?></dt>
		<dd>
			<?php echo $this->Html->link($resumene['Liquidation']['name'], array('controller' => 'Liquidations', 'action' => 'view', $resumene['Liquidation']['id'])); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Data'); ?></dt>
		<dd>
			<?php echo h($resumene['Resumene']['data']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Creado'); ?></dt>
		<dd>
			<?php echo h($resumene['Resumene']['created']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Modificado'); ?></dt>
		<dd>
			<?php echo h($resumene['Resumene']['modified']); ?>
			&nbsp;
		</dd>
    </dl>
</div>
<div class="actions">
    <h3><?php echo __('Actions'); ?></h3>
    <ul>
        		<li><?php echo $this->Html->link(__('Edit Resumene'), array('action' => 'edit', $resumene['Resumene']['id'])); ?> </li>
		<li><?php echo $this->Form->postLink(__('Delete Resumene'), array('action' => 'delete', $resumene['Resumene']['id']), array(), __('Are you sure you want to delete # %s?', $resumene['Resumene']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Resumenes'), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Resumene'), array('action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Liquidations'), array('controller' => 'Liquidations', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Liquidation'), array('controller' => 'Liquidations', 'action' => 'add')); ?> </li>
    </ul>
</div>
