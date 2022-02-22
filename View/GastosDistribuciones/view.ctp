<div class="banco view">
    <h2><?php echo __('Distribución'); ?></h2>
    <fieldset>
        <b><?php echo __('Consorcio'); ?>: </b>
        <?php
        echo h(__($gastosDistribuciones['Consorcio']['name']));
        ?>
        &nbsp;
        <br>
        <b><?php echo __('Nombre'); ?>: </b>
        <?php
        echo h(__($gastosDistribuciones['GastosDistribucione']['nombre']));
        ?>
        &nbsp;
        <br>
        <b><?php echo __('Porcentajes'); ?>: </b>
        <br>
        <?php
        unset($gastosDistribuciones['GastosDistribucionesDetalle']['gastos_distribucione_id']);
        unset($gastosDistribuciones['GastosDistribucionesDetalle']['id']);
        unset($gastosDistribuciones['GastosDistribucionesDetalle']['coeficiente_id']);
        unset($gastosDistribuciones['GastosDistribucionesDetalle']['porcentaje']);
        foreach ($gastosDistribuciones['GastosDistribucionesDetalle'] as $k => $v) {
            ?>
            <?php echo $coeficientes[$v['coeficiente_id']] . ": <b>" . $v['porcentaje'] . "%</b>" ?>
            <br>
            <?php
        }
        ?>
    </fieldset>
</div>
<?php
echo '<br>' . $this->Html->link(__('Volver'), array('action' => 'index'));

//array(
//	'Consorcio' => array(
//		'name' => 'BELLA VISTA X'
//	),
//	'GastosDistribucione' => array(
//		'id' => '9',
//		'consorcio_id' => '7',
//		'nombre' => 'Distribucion AXX'
//	),
//	'GastosDistribucionesDetalle' => array(
//		'id' => '1',
//		'gastos_distribucione_id' => '9',
//		'coeficiente_id' => '21',
//		'porcentaje' => '25.00',
//		(int) 0 => array(
//			'id' => '1',
//			'gastos_distribucione_id' => '9',
//			'coeficiente_id' => '21',
//			'porcentaje' => '25.00'
//		),
//		(int) 1 => array(
//			'id' => '2',
//			'gastos_distribucione_id' => '9',
//			'coeficiente_id' => '22',
//			'porcentaje' => '75.00'
//		)
//	)
//)