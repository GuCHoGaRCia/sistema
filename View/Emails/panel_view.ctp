<div class="emails view">
    <h2><?php echo __('Email'); ?></h2>
    <fieldset>
        <b><?php echo __('Id'); ?>: </b>
        <?php echo h(__($email['Email']['id'])); ?>
        &nbsp;
        <br>
        <b><?php echo __('Client Id'); ?>: </b>
        <?php echo h(__($email['Email']['client_id'])); ?>
        &nbsp;
        <br>
        <b><?php echo __('Email'); ?>: </b>
        <?php echo h(__($email['Email']['email'])); ?>
        &nbsp;
        <br>
        <b><?php echo __('Asunto'); ?>: </b>
        <?php echo h(__($email['Email']['asunto'])); ?>
        &nbsp;
        <br>
        <b><?php echo __('Html'); ?>: </b>
        <?php echo $email['Email']['html'] ?>
        &nbsp;
        <br>
        <b><?php echo __('Created'); ?>: </b>
        <?php echo $this->Time->format(__('d/m/Y H:i:s'), $email['Email']['created']); ?>
        &nbsp;
        <br>
        <b><?php echo __('Modified'); ?>: </b>
        <?php echo $this->Time->format(__('d/m/Y H:i:s'), $email['Email']['modified']); ?>
        &nbsp;
        <br>
    </fieldset>
</div>
<?php echo '<br>' . $this->Html->link(__('Volver'), ['action' => 'index']); ?>