<div class="colaimpresionesdetalles view">
    <h2><?php echo __('Carta deudor enviada'); ?></h2>
    <fieldset>
        <b><?php echo __('Consorcio'); ?>: </b>
        <?= h($cartadeudore['Consorcio']['name']) ?>
        &nbsp;
        <br>
        <b><?php echo __('Propietario'); ?>: </b>
        <?= h($cartadeudore['Propietario']['name'] . ' - ' . $cartadeudore['Propietario']['unidad'] . " (" . $cartadeudore['Propietario']['code'] . ")") ?>
        &nbsp;
        <br>
        <b><?php echo __('Fecha envío'); ?>: </b>
        <?= $this->Time->format(__('d/m/Y H:i:s'), $cartadeudore['Cartadeudore']['created']) ?>
        &nbsp;
        <br>
        <b><?php echo __('Carta'); ?>: </b>
        <?= $cartadeudore['Cartadeudore']['carta'] ?>
        &nbsp;
    </fieldset>
</div>
<script>
    document.getElementById("print").style.display = "none";//oculto la impresora pedorra esa
</script>