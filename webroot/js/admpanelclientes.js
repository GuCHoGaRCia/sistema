$(document).ready(function () {
    $("a.status").unbind("change");
    $(document).on("click", "a.status", function () {
        if ($.active > 0) {
            alert("Una petición anterior se encuentra todavía en proceso, espere un instante y vuelva a intentarlo...");
            return false;
        }
        var p = this.firstChild;
        var cual = p.src.match('1.png');
        $(p).attr({src: "/sistema/img/loading.gif", title: "Cargando..."});
        $.get(this.href + "?" + new Date().getTime()).fail(function (x, y) {
            alert("No se pudo realizar la accion, intente nuevamente");
            if (cual) {
                $(p).attr({src: "/sistema/img/1.png", title: "Habilitar"});
            } else {
                $(p).attr({src: "/sistema/img/0.png", title: "Deshabilitar"});
            }
        }).done(function (msg) {
            if (msg !== "1") {
                alert(msg);
                if (cual) {
                    $(p).attr({src: "/sistema/img/1.png", title: "Habilitar"});
                } else {
                    $(p).attr({src: "/sistema/img/0.png", title: "Deshabilitar"});
                }
            } else {
                if (cual) {
                    $(p).attr({src: "/sistema/img/0.png", title: "Habilitar"});
                } else {
                    $(p).attr({src: "/sistema/img/1.png", title: "Deshabilitar"});
                }
            }
        });
        return false;
    });
    $("#print").on("click", function () {
        window.print();
    });
    $("form input.submit").bind("dblclick", function (e) {
        e.preventDefault();
        return false;
    });
});
function enviaCola(id, reporte, model) {
    $.ajax({type: "POST", url: "/sistema/colaimpresiones/addCola", cache: false, data: {id: id, r: reporte, m: model}}).done(function (msg) {
		var obj = JSON.parse(msg);
        alert(msg[d]);
    }).fail(function (x, y) {
        if (x.status === 403) {
            alert("No se pudo realizar la accion. Verifique que se encuentra logueado en el sistema");
        } else {
            alert("No se pudo realizar la accion, intente nuevamente");
        }
    });
}
window.alert = function (outputMsg) {
    if (!outputMsg)
        return;
    var div = $('<div></div>');
    div.html(outputMsg).dialog({
        resizable: false,
        modal: true,
        buttons: {
            "Ok": function () {
                $(this).dialog("close");
            }
        },
    });
    div.siblings('.ui-dialog-titlebar').hide();
}
function jsonParseOrdered(o) {
    var obj1 = JSON.stringify(o);
    var obj = obj1.substring(4, obj1.length - 4).split('\\",\\"');
    var resul = {};
    for (var i = 0; i < obj.length; i++) {
        var x = obj[i].split('\\":\\"');
        var r = /\\u([\d\w]{4})/gi;
        y = x[1].replace(r, function (match, grp) {
            return String.fromCharCode(parseInt(grp, 16));
        });
        resul[i] = {"k": hhh(x[0]), "v": hhh(y.replace('\\', ''))};
    }
    return resul;
}
function hhh(val) {
    return $("<span>").text(val).html();
}
function dialogLoad(id, ruta) {
	$(id).load(ruta, function (a, s, x) {
		if (x.status === 403) {
			alert("No se pudo realizar la accion. Verifique que se encuentra logueado en el sistema");
		} 
	});
}