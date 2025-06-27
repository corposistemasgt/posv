$(document).ready(function() {
    $("#resultados").load("../ajax/agregar_tmpegreso.php");
    $("#datos_factura").load();
    $("#numComprobando").hide();
    $("#barcode").focus();
    $("#datos_traslados").hide();
    load(1);    
});
function load(page) {
    var q = $("#q").val();
    $("#loader").fadeIn('slow');
    $.ajax({
        url: '../ajax/productos_modal_egreso.php?action=ajax&page=' + page + '&q=' + q,
        beforeSend: function(objeto) { 
            $('#loader').html('<img src="../../img/ajax-loader.gif"> Cargando...');
        },
        success: function(data) {
            $(".outer_div").html(data).fadeIn('slow');
            $('#loader').html('');
        }
    })
}
function agregar(id) { 
    console.log("Aca se agrega");
    var precio_venta = document.getElementById('precio_venta_' + id).value;
    var cantidad = document.getElementById('cantidad_' + id).value;
    var idusuario = document.getElementById('selec_usuario').value;
    if (isNaN(cantidad)) {
        $.Notification.notify('error','bottom center','NOTIFICACIÓN', 'LA CANTIDAD NO ES UN NUMERO, INTENTAR DE NUEVO')
        document.getElementById('cantidad_' + id).focus();
        return false;
    }
    if (isNaN(precio_venta)) {
        $.Notification.notify('error','bottom center','NOTIFICACIÓN', 'EL PRECIO NO ES UN NUMERO, INTENTAR DE NUEVO')
        document.getElementById('precio_venta_' + id).focus();
        return false;
    }
    $.ajax({
        type: "POST",
        url: "../ajax/agregar_tmp_modalegreso.php",
        data: "id=" + id + "&precio_venta=" + precio_venta + "&cantidad=" + cantidad + "&idusuario=" + idusuario + "&operacion=" + 2,
        beforeSend: function(objeto) {
            $("#resultados").html('<img src="../../img/ajax-loader.gif"> Cargando...');
        },
        success: function(datos) {
            console.log(datos);
            $("#resultados").html(datos);
        }
    });
}
$("#barcode_form").submit(function(event) {
    var id = $("#barcode").val();
    var cantidad = $("#barcode_qty").val();
    var idusuario = document.getElementById('selec_usuario').value;
    var id_sucursal = 0;
    if (isNaN(cantidad)) {
        $.Notification.notify('error','bottom center','NOTIFICACIÓN', 'LA CANTIDAD NO ES UN NUMERO, INTENTAR DE NUEVO')
        $("#barcode_qty").focus();
        return false;
    }
    parametros = {
        'operacion':1,
        'id': id,
        'id_sucursal': id_sucursal,
        'idusuario': idusuario,
        'cantidad': cantidad
    };
    $.ajax({
        type: "POST",
        url: "../ajax/agregar_tmpegreso.php",
        data: parametros,
        beforeSend: function(objeto) {
            $("#resultados").html('<img src="../../img/ajax-loader.gif"> Cargando...');
        },
        success: function(datos) {
            $("#resultados").html(datos);
            $("#id").val("");
            $("#id").focus();
            $("#barcode").val("");
            $("#f_resultado").load("../ajax/incrementa_factura.php"); 
        }
    });
    event.preventDefault();
})
function eliminar(id) {
    $.ajax({
        type: "GET",
        url: "../ajax/agregar_tmpegreso.php",
        data: "id=" + id,
        beforeSend: function(objeto) {
            
            $("#resultados").html('<img src="../../img/ajax-loader.gif"> Cargando...');
           
        },
        success: function(datos) {
            $("#resultados").html(datos);
        }
    });
}
function cambio(){
    //url: "../ajax/agregar_tmpactualizar.php",
   
    var idusuario = document.getElementById('selec_usuario').value;
    console.log(idusuario);
    $.ajax({
        type: "POST", 
        url: "../ajax/agregar_tmpegreso.php",
        data: "idusuario="+idusuario,
        beforeSend: function(objeto) {         
            $("#resultados").html('<img src="../../img/ajax-loader.gif"> Cargando...');        
        },
        success: function(datos) {
            $("#resultados").html(datos);
        }
    });
}
function guardar()
{
    var idusuario = document.getElementById('selec_usuario').value;
    $.ajax({
        type: "GET",
        url: "../ajax/guardar_egreso.php",
        data: "idusuario="+idusuario,
        beforeSend: function(objeto) {         
            $("#resultados").html('<img src="../../img/ajax-loader.gif"> Cargando...');        
        },
        success: function() {
            cambio();
            swal("Exito", "Egreso registrado correctamente", "success");
        }
    });
}