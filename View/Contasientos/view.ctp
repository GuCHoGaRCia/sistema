<div class="contasientosx" style="text-align:center">
    <?php
    if (!empty($contasiento)) {
        //debug($contasiento);
        ?>
        <fieldset>
            <h3>
                <?php echo __('DETALLE ASIENTO') . " N&ordm;" . h($contasiento[0]['Contasiento']['numero']); ?>
            </h3>
            <?php
            $debe = $haber = 0;
            $i = 0;

            echo "<div class='inline'>";
            echo $this->Form->input('', ['label' => __('Consorcio'), 'disabled' => 'disabled', 'style' => 'width:550px;text-align:left', 'value' => h($consorcios[$contasiento[0]['Contasiento']['consorcio_id']])]);
            echo $this->Form->input('', ['label' => __('Fecha'), 'disabled' => 'disabled', 'style' => 'width:100px', 'value' => $this->Time->format(__('d/m/Y'), $contasiento[0]['Contasiento']['fecha'])]);
            echo "</div>";
            foreach ($contasiento as $k => $v) {
                echo "<li class='inline' style='list-style-type:decimal-leading-zero'>";
                echo $this->Form->input('', ['label' => $i == 0 ? __('Cuenta') : false, 'disabled' => 'disabled', 'class' => 's2', 'style' => 'width:200px', 'type' => 'text', 'value' => h(strtoupper($contcuentas[$v['Contasiento']['contcuenta_id']]))]);
                echo $this->Form->input('', ['label' => $i == 0 ? __('Descripción') : false, 'disabled' => 'disabled', 'style' => 'width:280px', 'type' => 'text', 'value' => h($v['Contasiento']['descripcion']) ?? '']);
                echo $this->Form->input('', ['label' => $i == 0 ? __('Debe') : false, 'disabled' => 'disabled', 'style' => 'width:80px;text-align:right', 'type' => 'text', 'value' => $this->Functions->money($v['Contasiento']['debe'])]);
                echo $this->Form->input('', ['label' => $i == 0 ? __('Haber') : false, 'disabled' => 'disabled', 'style' => 'width:80px;text-align:right', 'type' => 'text', 'value' => $this->Functions->money($v['Contasiento']['haber'])]);
                echo "</li>";
                $debe += $v['Contasiento']['debe'];
                $haber += $v['Contasiento']['haber'];
                $i++;
            }
            echo "<div class='inline' style='margin-top:10px'>";
            echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<div style='width:490px;font-weight:bold'></div>";
            echo $this->Form->input('', ['label' => false, 'type' => 'text', 'disabled' => 'disabled', 'style' => 'width:80px;text-align:right', 'disabled' => 'disabled', 'value' => $this->Functions->money($debe)]);
            echo $this->Form->input('', ['label' => false, 'type' => 'text', 'disabled' => 'disabled', 'style' => 'width:80px;text-align:right', 'disabled' => 'disabled', 'value' => $this->Functions->money($haber)]);
            echo "</div>";
            ?>
        </fieldset>
        <?php
    }
    ?>
</div>
<style>
    .contasientosx{font-size:10px}
</style>