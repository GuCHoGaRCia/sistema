<?php
echo $this->Html->script(['i18n/es']);
?>
<div class="cobranzas form">
    <fieldset>
        <h2><?php echo __('Agregar Cobranza manual'); ?></h2>
        <?= $this->Form->input('consorcio_id', array('label' => __('Consorcio'), 'empty' => __('Seleccione Consorcio...'))) ?>
        <?= $this->Form->input('propietario_id', array('label' => __('Propietario'), 'empty' => __('Seleccione Propietario...'))) ?>
        <?php
        //        <!--select class="buscajax" style="width:400px">
        //            <option></option>
        //        </select-->
        //        <!--input type="text" value="" id="codigobarras" style="width:170px;margin-left:50px;" placeholder="Ingrese código barras..."/>
        //        <input type="submit" id="enviacodigobarras" value="Buscar..."/-->
        ?>
        <div id="formas" style="min-width:700px;"></div>
    </fieldset>
    <div id="contenido" style="width:auto;min-width:700px;height:auto"></div>
</div>
<?php echo '<br>' . $this->Html->link(__('Cancelar'), ['action' => 'index'], [], __('Desea cancelar?')); ?>
<script>
    var consorcio_id = <?= !empty($consorcio_id) ? $consorcio_id : 0 ?>;
    $(function () {
        $("#consorcio_id").select2({language: "es"});
        $("#propietario_id").select2({language: "es"});
    });
    $("#consorcio_id").on("select2:select", function (e) {
        recargar();
    });
    if (consorcio_id !== 0) {
        $("#consorcio_id").val(consorcio_id).change();
        $("#propietario_id option").remove();
        $("#propietario_id").hide();
        $("#formas").html('');
        getData($("#consorcio_id").val());
        $("#consorcio_id").prop('disabled', true);
    }
    /*$("#consorcio_id").change(function () {
     $("#propietario_id option").remove();
     $("#propietario_id").hide();
     $("#formas").html('');
     if ($("#consorcio_id").val() !== "") {
     getData($("#consorcio_id").val());
     $("#consorcio_id").prop('disabled', true);
     }
     });*/
    function recargar() {
        if ($("#consorcio_id").val() !== "") {
            window.location.href = "<?= $this->webroot ?>Cobranzas/add2/" + $("#consorcio_id").val();
        }
    }
    $("#propietario_id").change(function () {
        $("#formas").html('<br>Cargando datos, espere por favor... <img src="<?php echo $this->webroot; ?>img/loading.gif"/>');
        if ($("#propietario_id").val() !== "") {
            saldo($("#propietario_id").val());
            $("#propietario_id").prop('disabled', true);
        }
    });
    function getData(e) {
        $.ajax({type: "POST", url: "<?= $this->webroot ?>Propietarios/getPropietarios", cache: false, data: {q: e}}).done(function (msg) {
            if (msg) {
                var obj = jsonParseOrdered(msg);
                $("#propietario_id option").remove();
                $("#propietario_id").append($("<option></option>").attr("value", '').text("Seleccione Propietario..."));
                $.each(obj, function (j, val) {
                    $("#propietario_id").append($("<option></option>").attr("value", hhh(val["k"])).text(hhh(val["v"])));
                });
            }
        }).fail(function (jqXHR, textStatus) {
            if (jqXHR.status === 403) {
                alert("No se pudo obtener el dato. Verifique si se encuentra logueado en el sistema");
            } else {
                alert("No se pudo obtener el dato, intente nuevamente");
            }
        });
    }

<?php
/*

  $("#enviacodigobarras").click(function () {
  if ($("#codigobarras").val().length === 12 && /^\d+$/.test($("#codigobarras").val()) === true) {
  $.ajax({type: "POST", url: "<?= $this->webroot ?>SaldosCierres/getSaldosPropietario", cache: false, data: {c: $("#codigobarras").val()}}).done(function (msg) {
  $("#formas").html(msg);
  }).fail(function (jqXHR, textStatus) {
  if (jqXHR.status === 403) {
  alert("No se pudieron obtener los datos. Verifique que se encuentra logueado en el sistema");
  } else {
  alert("No se pudieron obtener los datos");
  }
  });
  } else {
  alert("El formato del código de barras es incorrecto, debe ser de 12 dígitos numéricos");
  }
  });
  $(".buscajax").select2({
  language: "es",
  placeholder: "Buscar por nombre, código, unidad o consorcio...",
  ajax: {
  url: "<?= $this->webroot ?>Propietarios/get",
  dataType: 'json',
  delay: 500,
  data: function (params) {
  return {
  q: params.term, <?php // search term               ?>
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
  saldo($(this).val());
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


 */
?>

    function saldo(id) {
        $.ajax({type: "POST", url: "<?= $this->webroot ?>SaldosCierres/getSaldosPropietario", cache: false, data: {p: id}}).done(function (msg) {
<?php /*  al ejegir otro propietario destruyo los dialog sino no funciona el javascript al hacer click en el cheque y no se manda el form completo (pasan cagadas raras) */ ?>
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
</style>