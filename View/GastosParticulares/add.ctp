<div id="gastosgeneralesadd" class="gastosgeneralesadd">
    <h2><?php echo __('Agregar Gastos Particulares'); ?></h2>
    <?php
    echo $this->Form->create('GastosParticulare', ['class' => 'inline']);
    echo $this->Form->input('liquidation_id', ['label' => false, 'options' => $liquidations, 'type' => 'select',
        'selected' => isset($this->request->data['GastosParticulare']['liquidation_id']) ? $this->request->data['GastosParticulare']['liquidation_id'] : 0,
        'empty' => 'Seleccione una liquidación...']);
    echo $this->Form->end(__('Ver'));
    if (empty($coeficientes)) {
        echo "<div class='info'>Seleccione una liquidación a la cual cargar gastos particulares</div>";
    } else {
        
    }
    ?>
    <script>
        $(function () {
            $("#GastosParticulareLiquidationId").select2({language: "es", placeholder: "Seleccione una liquidación...", width: 600});
        });
    </script>
</div>