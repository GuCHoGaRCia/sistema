<?php
echo $this->Html->script(['i18n/es']);
?>
<div class="cobranzas form">
    <fieldset>
        <h2><?php echo __('Consultar propietario'); ?></h2>
        <select class="buscajax" style="width:600px">
            <option></option>
        </select>
        <div id="formas"></div>
    </fieldset>
    <div id="contenido" style="width:auto;height:auto"></div>
</div>
<?php echo '<br>' . $this->Html->link(__('Cancelar'), ['action' => 'index'], [], __('Desea cancelar?')); ?>
<script>
    $(".buscajax").select2({
        language: "es",
        placeholder: "Buscar un Propietario por nombre, unidad o consorcio ...",
        ajax: {
            url: "<?= $this->webroot ?>Propietarios/buscarPropietario",
            dataType: 'json',
            delay: 500,
            data: function (params) {
                return {
                    q: params.term, <?php /* search term */ ?>
                    page: params.page
                };
            },
            processResults: function (data, page) {
                return {
                    results: data
                };
            },
            error: function (jqXHR, status, error) {
                if (jqXHR.status === 403) {
                    alert("No se pudieron obtener los datos. Verifique que se encuentra logueado en el sistema");
                } else {
                    alert("No se pudieron obtener los datos");
                }
            },
            cache: true
        },
        escapeMarkup: function (markup) {
            return markup;
        },
        minimumInputLength: 3,
        templateResult: formatRepo,
        templateSelection: formatRepoSelection
    });
    $(".buscajax").on("select2:select", function (e) {
        consultas($(this).val());
    });
    function formatRepo(repo) {
        if (repo.loading)
            return repo.text;
        var markup = '';
        if (repo.text) {
            markup += '<div>' + repo.text + '</div>';
        }
        return markup;
    }
    function formatRepoSelection(r) {
        return r.text;
    }

    function consultas(id) {
        $.ajax({type: "POST", url: "<?= $this->webroot ?>Consultaspropietarios/getConsultasPropietario", cache: false, data: {p: id}}).done(function (msg) {
<?php
// al ejegir otro propietario destruyo los dialog sino no funciona el javascript al hacer click en el cheque y no se manda el form completo (pasan cagadas raras)
?>
            if (typeof dialog !== "undefined") {
                dialog.dialog("destroy");
            }
            if (typeof dialog2 !== "undefined") {
                dialog2.dialog("destroy");
            }
            $("#formas").html(msg);
        });
    }
</script>
<style type="text/css">
    .prop{cursor:pointer; line-height: 5px; color:#444}
    /*.prop:hover{text-decoration:underline overline;background:#333}
    .select2-results__options li:hover{background:#444}*/
</style>