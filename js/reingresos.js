$(document).ready(function() {
   cambio();
    $("#datos_factura").load();
    $("#numComprobando").hide();
    $("#barcode").focus();
    $("#datos_traslados").hide();
    load(1);    
});
function load(page) {
   
}

function cambio(){
    var idusuario = document.getElementById('selec_usuario').value;
    console.log(idusuario);
    $.ajax({
        type: "GET",
        url: "../ajax/agregar_reingreso.php",
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
    console.log("entro al guardraireingerso");
    var idusuario = document.getElementById('selec_usuario').value;
    $.ajax({
        type: "GET",
        url: "../ajax/guardar_reingreso.php",
        data: "idusuario="+idusuario,
        beforeSend: function(objeto) {         
            $("#resultados").html('<img src="../../img/ajax-loader.gif"> Cargando...');        
        },
        success: function(datos) {
            console.log(datos);
            cambio();
            swal("Exito", "Egreso registrado correctamente", "success");
        }
    });
}