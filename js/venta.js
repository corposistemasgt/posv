$(document).ready(function() {
    $("#resultados").load("../ajax/agregar_tmp.php");
    $("#resultados2").load("../ajax/carga_caja.php");
    $("#resultados3").load("../ajax/carga_resibido.php");
    $("#resultados4").load("../ajax/tipo_doc.php");
    $("#resultados5").load("../ajax/carga_num_trans.php");
    $("#datos_factura").load();
    $("#numComprobando").hide();
    $("#barcode").focus();
    $("#datos_traslados").hide();
    load(1);

    
}); 
function print_ticket(id_factura,origen,tipo) {
    if (id_factura) 
    {
        var open=0;
        if(origen=='1')
        {
            open=1;
        } 
        if ('Android'==isMobile())
        {    
            const objetoJSON = {factura: id_factura,open: open};
			console.log("corpoprint-ticket"+JSON.stringify(objetoJSON));
        }
        else
        {
            let url="";
            if(tipo==1)
            {
                url='../pdf/documentos/imprimir_factura_venta.php';
                
            }
            else
            {
                url='../pdf/documentos/imprimir_venta_edit.php';
            }
            $.ajax({
                url:  url,
                type: 'post',
                data: {
                    id_factura: id_factura
                },
                dataType: 'text',
                success: function(response) {
                    var mywindow = window.open('', 'Stock Management System', 'height=400,width=600');
                    mywindow.document.write('<html><head><title>Comprobante</title>');
                    mywindow.document.write('</head><body>');
                    mywindow.document.write(response);
                    mywindow.document.write('</body></html>');
                    mywindow.document.close(); // necessary for IE >= 10
                    mywindow.focus(); // necessary for IE >= 10
                    mywindow.print();
                    mywindow.close();
                } 
            }); 
        }
    } 
} 
function print_envio(id_factura,origen) {
    const checkbox = document.getElementById('precioss');
    let a=0;
    if (checkbox.checked) 
    {
        a=1;
    } 
    if (id_factura) 
    {
        var open=0;
        if(origen=='1')
        {
            open=1;
        }
        if ('Android'==isMobile())
        {    
            const objetoJSON = {factura: id_factura,open: open};
			console.log("corpoprint-ticket"+JSON.stringify(objetoJSON));
        }
        else
        {
            $.ajax({
                url: '../pdf/documentos/imprimir_envio.php',
                type: 'post',
                data: {
                    id_factura: id_factura,precios: a
                },
                dataType: 'text',
                success: function(response) {
                    var mywindow = window.open('', 'Stock Management System', 'height=400,width=600');
                    mywindow.document.write('<html><head><title>Envio</title>');
                    mywindow.document.write('</head><body>');
                    mywindow.document.write(response);
                    mywindow.document.write('</body></html>');
                    mywindow.document.close();
                    mywindow.focus();
                    mywindow.print();
                    mywindow.close();
                } 
            }); 
        }
    } 
} 
function generar_factura(id_factura,open) {
    console.log("corpoprint"+id_factura);
}
function load(page) {
    var q = $("#q").val();
    var id = $("#categoria").val();
    $("#loader").fadeIn('slow');
    $.ajax({
        url: '../ajax/productos_modal_ventas.php?action=ajax&page=' + page + '&q=' + q+ '&cate=' + id,
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
    //Inicia validacion
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
    //Fin validacion
    $.ajax({
        type: "POST",
        url: "../ajax/agregar_tmp_modalventas.php",
        data: "id=" + id + "&precio_venta=" + precio_venta + "&cantidad=" + cantidad + "&operacion=" + 2,
        beforeSend: function(objeto) {
            $("#resultados").html('<img src="../../img/ajax-loader.gif"> Cargando...');
        },
        success: function(datos) {
          //  console.log(datos);
            detalle(1,id,precio_venta,cantidad);
            $("#resultados").html(datos);
        }
    });
}
//CONTROLA EL FORMULARIO DEL CODIGO DE BARRA
$("#barcode_form").submit(function(event) {
    var id = $("#barcode").val();
    var cantidad = $("#barcode_qty").val();
    var id_sucursal = 0;
    //Inicia validacion
    if (isNaN(cantidad)) {
        $.Notification.notify('error','bottom center','NOTIFICACIÓN', 'LA CANTIDAD NO ES UN NUMERO, INTENTAR DE NUEVO')
        $("#barcode_qty").focus();
        return false;
    }
    //Fin validacion
    parametros = {
        'operacion':1,
        'id': id,
        'id_sucursal': id_sucursal,
        'cantidad': cantidad
    };
    $.ajax({
        type: "POST",
        url: "../ajax/agregar_tmp.php",
        data: parametros,
        beforeSend: function(objeto) {
            $("#resultados").html('<img src="../../img/ajax-loader.gif"> Cargando...');
        },
        success: function(datos) {
            console.log(datos);
            detalle(2,id,0,cantidad);
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
    detalle(3,id,0,0);
    $.ajax({
        type: "GET",
        url: "../ajax/agregar_tmp.php",
        data: "id=" + id,
        beforeSend: function(objeto) {
            
            $("#resultados").html('<img src="../../img/ajax-loader.gif"> Cargando...');
           
        },
        success: function(datos) {
            $("#resultados").html(datos);
        }
    });
}

function detalle(tipo,v1,v2,v3) {
   // if ('Android'==isMobile())					
    //{
        //console.log("d:"+tipo);
        var ids = document.getElementById('sesion').value;
        $.ajax({
            type: "GET",
            url: "../ajax/facturajson.php",
            data: "id=" + ids+"&v1=" + v1+"&v2=" + v2+"&v3=" + v3 +"&tipo=" + tipo,
            success: function(datos) {
                
                console.log("corpoprint-update"+datos);
            }
        });
  //  }

}

$("#datos_traslados").submit(function(event){

    //alert("Trasladar");
    //var id_sucursal = $("selec_sucursal").val();
    var parametros = $(this).serialize();
    //alert(parametros);
    $.ajax({
        type: "POST",
        url: "../ajax/trasladar_mercaderia.php",
        data: parametros,
        beforeSend: function(objeto) {
            $("#resultados_ajaxf").html('<img src="../../img/ajax-loader.gif"> Cargando...');
        },
        success: function(datos) {
            $("#resultados_ajaxf").html(datos);
            $('#guardar_factura').attr("disabled", false);
            //resetea el formulario
            //$('#modal_vuelto').modal('show');
            //$("#datos_factura")[0].reset(); //Recet al formilario de el cliente
            $("#datos_factura")[0].reset(); //Recet al formilario de el cliente
            $("#barcode_form")[0].reset(); // Recet al formulario de la fatura
            $("#resultados").load("../ajax/agregar_tmp.php"); // carga los datos nuevamente
            //$("#f_resultado").load("../ajax/incrementa_factura.php"); // carga la caja de incrementar la factura
            //$("#resultados2").load("../ajax/carga_caja.php"); // carga la caja total del dia
            $("#barcode").focus();
            load(1);
            //desaparecer la alerta
            $(".alert-success").delay(400).show(10, function() {
                $(this).delay(2000).hide(10, function() {
                    $(this).remove();
                });
            }); // /.alert
        }
    });
    event.preventDefault();
});

$("#datos_factura").submit(function(event) {
    $('#guardar_factura').attr("disabled", true);
    var id_cliente = $("#id_cliente").val();
    var resibido = $("#resibido").val();  
    var nombre_cliente = $("#nombre_cliente").val();
    //alert(nombre_cliente+"<- nombre cliente");
    if (isNaN(resibido)) {
        $.Notification.notify('error','bottom center','NOTIFICACIÓN', 'EL DATO NO ES VALIDO, INTENTAR DE NUEVO')
        $("#resibido").focus();
        return false;
    }
    var parametros = $(this).serialize();
    //parametros['valor'] = 5; agregar parametros a la variable parametros
    $.ajax({
        type: "POST",
        url: "../ajax/guardar_venta.php",
        data: parametros, 
        beforeSend: function(objeto) {
            $("#resultados_ajaxf").html('<img src="../../img/ajax-loader.gif"> Cargando...');
        },
        success: function(datos) {
            $("#resultados_ajaxf").html(datos);
            $('#guardar_factura').attr("disabled", false);
            //resetea el formulario
            //$('#modal_vuelto').modal('show');
            $("#datos_factura")[0].reset(); //Recet al formilario de el cliente
            $("#barcode_form")[0].reset(); // Recet al formulario de la fatura
            $("#resultados").load("../ajax/agregar_tmp.php"); // carga los datos nuevamente
            $("#f_resultado").load("../ajax/incrementa_factura.php"); // carga la caja de incrementar la factura
            $("#resultados2").load("../ajax/carga_caja.php"); // carga la caja total del dia
            $("#barcode").focus();
            load(1);
            //desaparecer la alerta
            $(".alert-success").delay(400).show(10, function() {
                $(this).delay(2000).hide(10, function() {
                    $(this).remove();
                });
            }); // /.alert
        }
    });
    event.preventDefault();
})
$("#guardar_cliente").submit(function(event) {
    $('#guardar_datos').attr("disabled", true);
    var parametros = $(this).serialize();
    $.ajax({
        type: "POST",
        url: "../ajax/nuevo_cliente.php",
        data: parametros,
        beforeSend: function(objeto) {
            $("#resultados_ajax").html('<img src="../../img/ajax-loader.gif"> Cargando...');
        },
        success: function(datos) {
            $("#resultados_ajax").html(datos);
            $('#guardar_datos').attr("disabled", false);
            //resetea el formulario
            $("#guardar_cliente")[0].reset();
            //desaparecer la alerta
            $(".alert-success").delay(400).show(10, function() {
                $(this).delay(2000).hide(10, function() {
                    $(this).remove();
                });
            }); // /.alert
            load(1);
        }
    });
    event.preventDefault();
})
$("#guardar_producto").submit(function(event) {
    $('#guardar_datos').attr("disabled", true);
    var parametros = $(this).serialize();
    $.ajax({
        type: "POST",
        url: "../ajax/nuevo_producto.php",
        data: parametros,
        beforeSend: function(objeto) {
            $("#resultados_ajax_productos").html('<img src="../../img/ajax-loader.gif"> Cargando...');
        },
        success: function(datos) {
            $("#resultados_ajax_productos").html(datos);
            $('#guardar_datos').attr("disabled", false);
            //resetea el formulario
            $("#guardar_producto")[0].reset();
            //desaparecer la alerta
            $(".alert-success").delay(400).show(10, function() {
                $(this).delay(2000).hide(10, function() {
                    $(this).remove();
                });
            }); // /.alert
            load(1);
        }
    });
    event.preventDefault();
})
$('#dataDelete').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget) // Botón que activó el modal
            var id = button.data('id') // Extraer la información de atributos de datos
            var modal = $(this)
            modal.find('#id_factura').val(id)
        })
        $("#eliminarDatos").submit(function(event) {
            var parametros = $(this).serialize();
            $.ajax({
                type: "POST",
                url: "../ajax/anular_factura.php",
                data: parametros,
                beforeSend: function(objeto) {
                    $(".datos_ajax_delete").html('<img src="../../img/ajax-loader.gif"> Cargando...');
                },
                success: function(datos) {
                    $(".datos_ajax_delete").html(datos);
                    $('#dataDelete').modal('hide');
                    load(1);
                    //desaparecer la alerta
                    window.setTimeout(function() {
                        $(".alert").fadeTo(200, 0).slideUp(200, function() {
                            $(this).remove();
                        });
                    }, 2000);
                }
            });
            event.preventDefault();
        });
$("#rnc").keydown( function(e) {
    if(e.which == 13) {
        e.preventDefault();
        buscar_nit();
    }
    });
function buscar_nit()
{
    var nit = $('#rnc').val();
    if(nit=='CF' ||nit=='cF' || nit=='Cf' ||nit=='cf')
    {
        $('#nombre_cliente').val("Consumidor Final");
        $('#rnc').val("CF");
    }
    else
    {
       
        $.ajax({
            type: 'GET',
            url: '../ajax/consultar_cliente.php',
            data: "nit=" + nit,// + "&param2=" + param2,
            beforeSend: function(objeto) {
                $(".datos_ajax_delete").html('<img src="../../img/ajax-loader.gif"> Cargando...');
            },
            success: function(data) {
                console.log(data);
                $(".datos_ajax_delete").html('');               
                const obj = JSON.parse(data);
                if(obj.resultado=="false")
                {
                    swal("Nit Invalido", "No se pudo localizar el numero de Nit", "error");
                    $('#nombre_cliente').val('');
                }
                else
                {	
                    $('#nombre_cliente').val('');
                    $('#nombre_cliente').val(obj.nombre);
                    $('#direccion_cliente').val('');
                    $('#rutaz').val('');
                    if(obj.direccion==undefined)
                    {
                        $('#direccion_cliente').val('Ciudad');
                    }else
                    {
                        $('#direccion_cliente').val(obj.direccion);
                    }
                    if(obj.correos!=undefined)
                    {
                        $('#correo_cliente').val(obj.correos);
                    }
                    if(obj.telefono!=undefined)
                    {
                        $('#telefono_cliente').val(obj.telefono);
                    }
                    if(obj.ruta!=undefined)
                    {
                        $('#rutaz').val(obj.ruta);
                    }
                }
            }
        });

    }
}


function imprimir_factura(id_factura,origen,idsucursal) {
    console.log("-"+id_factura+"-"+idsucursal);
    var open=0;
    if(origen=='1'){open=1;}
    $.ajax({
        url: '../certificar.php?id_factura=' + id_factura+'&idsucursal='+idsucursal, 
        type: 'GET',
        dataType: 'text',
        success: function(response) {
            console.log(response);
            const obj = JSON.parse(response);
            
            if(obj.resultado=="false")
            {
                swal("Error de Certificacion", "Notifica a Soporte Tecnico, el error es: "+atob(obj.descripcion), "error");
                console.log("falso");
            }
            else
            {
                if ('Android'==isMobile())
				{	
					generar_factura(id_factura,2);
                    enviar_documentos(response);
                    load(1);
				}	
				else		
                {
                    generar_factura(id_factura,2);
                    location.href =obj.link; 
                    enviar_documentos(response);
                    load(1);               
                }   
                     
            }					
        } 
    ,
        error:function(data,e){ swal("Error",data.responseText, "error");  }
    });

}
function enviar_documentos(response)
{ 
    var corrr=$('#corr').val();
    var tel=$('#tel').val();
    const obj = JSON.parse(response);
    $.ajax({
     
        url: '../enviardocumentos.php?datos=' +  btoa(response)+'&tel='+btoa(tel)+'&corr='+btoa(corrr)+'&receptor='+btoa(obj.receptor)+'&emisor='+btoa(obj.emisor)+
        '&nitemisor='+btoa(obj.nitemisor)+'&requestor='+btoa(obj.requestor)+'&guid='+btoa(obj.guid)+'&fecha='+btoa(obj.fecha)+'&tipo='+btoa(obj.tipo)+'&total='+btoa(obj.total)+
        '&link='+btoa(obj.link), 
        type: 'GET',
        dataType: 'text',
        success: function(response) {
            console.log(response);
          			
        } 
    });


}
function isMobile(){
    return (
        (navigator.userAgent.match(/Android/i)) ||
        (navigator.userAgent.match(/webOS/i)) ||
        (navigator.userAgent.match(/iPhone/i)) ||
        (navigator.userAgent.match(/iPod/i)) ||
        (navigator.userAgent.match(/iPad/i)) ||
        (navigator.userAgent.match(/BlackBerry/i))
    );
}