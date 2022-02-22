<?php
/**
 *
 *
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Errors
 * @since         CakePHP(tm) v 0.10.0.1076
 */
?>
<p class="error">
    <strong>&nbsp;&nbsp;&nbsp;</strong>
    <?php echo __('La acción solicitada es inexistente. Se generó y envió un registro de la acción realizada al administrador del sistema.'); ?>
</p>
<?php
if (Configure::read('debug') > 0):
	echo $this->element('exception_stack_trace');
endif;
?>
