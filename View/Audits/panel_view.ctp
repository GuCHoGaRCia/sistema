<div class="panel panel-default" id='seccionaimprimir'>
    <div class="panel-heading">
        <?php echo __('Auditoría detallada del movimiento'); ?>
    </div>
    <div class="panel-body">
        <fieldset>
            <b><?= __('Cliente') . ": " ?></b>
            <?= h($clientes[$audits['Audit']['client_id']] ?? '--') ?>
            <br>
            <b><?= __('Sección') . ": " ?></b>
            <?= h($audits['Audit']['model']) ?>
            <br>
            <b><?= __('Acción') . ": " ?></b>
            <?= h($audits['Audit']['event']) ?>
            <br>
            <b><?= __('ID Registro') . ": " ?></b>
            <?= h($audits['Audit']['entity_id']) ?>
            <br>
            <b><?= __('Usuario') . ": " ?></b>
            <?= h($audits['Audit']['description']) ?>
            <br>
            <b><?= __('Fecha movimiento') . ": " ?></b>
            <?= $this->Time->format(__('d/m/Y H:i:s'), $audits['Audit']['created']) ?>
            <br>
            <?php
            if (!empty($audits['AuditDelta'])) {
                foreach ($audits['AuditDelta'] as $auditDeltas) {
                    ?>
                    <div class="inline" style="font-family:courier"><b><?= __('Campo') . ": " ?></b>
                        <?= h(str_pad($auditDeltas['property_name'], 25, ".", STR_PAD_RIGHT)) ?><br>
                        <div style='max-width:45%;position:relative;top:0'><h4 style='color:red'><?= __('Anterior') . ": " ?></h4>
                            <?= $auditDeltas['old_value'] ?></div>
                        <div style='max-width:45%;position:relative;top:0'><h4 style='color:red'><?= __('Nuevo') . ": " ?></h4>
                            <?= $auditDeltas['new_value'] ?></div>
                        <br>
                    </div>
                    <hr>
                    <?php
                }
            }
            ?>
        </fieldset>
    </div>
</div>