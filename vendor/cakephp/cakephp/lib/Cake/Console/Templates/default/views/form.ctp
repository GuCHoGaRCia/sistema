<?php
/*
  modificado por esteban 14/07/14 para CEOnline
 */
$acciones = ['add' => 'Agregar', 'edit' => 'Editar', 'delete' => 'Eliminar', 'view' => 'Ver'];
?>
<div class="<?php echo $pluralVar; ?> form">
    <?php echo "<?php echo \$this->Form->create('{$modelClass}', ['class' => 'jquery-validation']); ?>\n"; ?>
    <fieldset>
        <h2><?php printf("<?php echo __('$acciones[$action] %s'); ?>", $singularHumanName); ?></h2>
        <?php
        echo "<?php \n";
        foreach ($fields as $field) {
            if (strpos($action, 'add') !== false && $field === $primaryKey) {
                continue;
            } elseif (!in_array($field, ['created', 'modified', 'updated'])) {
                echo "\techo \$this->JqueryValidation->input('{$field}', ['label' => __('" . ucwords($field) . "')]);\n";
            }
        }
        if (!empty($associations['hasAndBelongsToMany'])) {
            foreach ($associations['hasAndBelongsToMany'] as $assocName => $assocData) {
                echo "\techo \$this->JqueryValidation->input('{$assocName}');\n";
            }
        }
        echo "\t?>\n";
        ?>
    </fieldset>
    <?php
    echo "<?php echo \$this->Form->end(__('Guardar')); ?>\n";
    ?>
</div>
<?php
echo "<?php echo '<br>' . \$this->Html->link(__('Cancelar'), ['action' => 'index'], [], __('Desea cancelar?')); ?>";

/*
 * Agrego a los campos xx_id el select2() de jquery
 * Agrego .focus() a los form cuyo primer campo sea un input comun y no sea xx_id ni id
 */
echo "\n<script>\n";
echo "$(document).ready(function(){";

$first = true; // si es el 1º campo y no tiene _id (input normal), le hago focus
foreach ($fields as $field) {
    if (strpos($field, '_id') !== false) {
        // agrego los select2() a cada xx_id
        echo '$(\'#' . $modelClass . Inflector::Camelize($field) . '\').select2({language: \'es\'});';
    } else {
        if ($first && $field !== 'id') {
            echo '$(\'#' . $modelClass . Inflector::Camelize($field) . '\').focus();';
            $first = false;
        }
    }
}
echo "});";
echo "\n</script>";
