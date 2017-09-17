
// ********************************************************
// * Aciones cuando la pagina ya esta cargada
// ********************************************************
$(window).load(function(){


$('.loader').hide();
document.getElementById("allDocument").style.visibility = "visible";
});


// ********************************************************
// * Aciones cuando la pagina incia|
// ******************************************************** 
document.addEventListener('DOMContentLoaded', function() {



var LineArray = []; //array para los items de la cotizacion
   
$('.loader').show();


$("#pass_12").focus(function(){
//Elimina el valor de pass2 si se situa el foco a pass1 en el model de Edit user
   var passField = "";

  $('#pass2').val(passField);

   });


$('#pass_22').focusout(function(){
  var pass = $('#pass_12').val();
  var pass2 = $('#pass_22').val();
    if(pass != pass2){
      alert('Password no coninciden');
      }


     });

  
  //selectc con buscador 
  $(".select").select2({

  placeholder: 'Seleccione una opcion',
  allowClear: true,
  maximumSelectionSize: 1

  
  });  


}, false);




//FUNCION DE ERROR 
function MSG_ERROR(MSG,VAL){

  $('#ERROR').show();

  if(VAL == 1){
    
    $('#ERROR').append(MSG+'<br>');

  }else{

    $('#ERROR').html(MSG+'<br>');
  }

  
  
  $("html, body").animate({ scrollTop: 0 }, "slow");
  

  //return false;
}

function MSG_ERROR_RELEASE(){

 $('#ERROR').html('');

}


//FUNCION DE SPIN MOSTRAR Y APAGAR
function spin_show(){


        //TERMINA SPIN/////////////////////////////////////////////////////
        $('html,body').scrollTop(0);
        document.getElementById("allDocument").style.visibility = "hidden";
        $('.loader').show();
        ////////////////////////////////////////////////////////////////////


}


function spin_hide(){


        //TERMINA SPIN/////////////////////////////////////////////////////
        $('.loader').hide();
        document.getElementById("allDocument").style.visibility = "visible";
        ////////////////////////////////////////////////////////////////////

  
}


 function CLOSE_DIV(id){ 

 ID = '#'+id;
 
 $(ID).html('');

 }


 function get_OC(id){


var datos= "url=bridge_query/get_PO_details/"+id;  
var link = URL+"index.php";




  $.ajax({
      type: "GET",
      url: link,
      data: datos,
      success: function(res){
      
       $('#table2').html(res);

       // alert(res);

        }
   });


}


function agregar_pro(id,item,uni,maxqty,comp){


  var element = id+id;
  var idqty = id+"qty";

  
  //var qty = document.getElementById(idqty).value;

  if(!document.getElementById(element)){


    var arrayItem =LineArray.length;



     var line = '<tr id="'+element+'"><td class="text-center hidden-xs"><a href="#" onclick="javascript:  erase_item('+arrayItem+'); del_tr(this);"><i style="color:red;" class="fa fa-minus"></i></a>&nbsp&nbsp'+id+'</td><td>'+item+'</td><td class="text-center hidden-xs">'+uni+'</td><td class="text-right text-primary text-bold"><input onchange="javascript: changeline('+arrayItem+','+"'"+idqty+"'"+','+"'"+document.getElementById('no_order').value+"'"+','+"'"+uni+"'"+','+"'"+id+"'"+','+"'"+comp+"'"+');" type="number" id="'+idqty+'" name="'+idqty+'" min="1" max='+maxqty+' value="1"/></td></tr>';


 


    $( "#invoice" ).append( line );



      LineArray[LineArray.length] = "option=2&codigo_item="+id+"&uni="+uni+"&id_order="+document.getElementById('no_order').value+"&qty="+document.getElementById(idqty).value+"&id_company="+comp;

     // alert(LineArray);

    }else{

    alert("El Item ya ha sido agragado");
    }

}


function changeline(line,idqty,order,uni,iditem,comp){

var idqty = document.getElementById(idqty).value;


LineArray[line] = "option=2&codigo_item="+iditem+"&uni="+uni+"&id_order="+order+"&qty="+idqty +"&id_company="+comp;

}
     

function del_tr(remtr)
  {
      while((remtr.nodeName.toLowerCase())!='tr')
          remtr = remtr.parentNode;

      remtr.parentNode.removeChild(remtr);
  }
function del_id(id)
  {
          del_tr(document.getElementById(id));
  }


function erase_item(line){


  LineArray[line]='';

  //alert(LineArray);

}

function send_order(){


var r = confirm("Desea procesar la orden?");


 if (r == true) {


       var date=document.getElementById('date').innerHTML;

       SalesOrderNumber=document.getElementById('no_order').value;

       var user_id=document.getElementById('user').value;

       var job_id=document.getElementById('jobid').value;

       var datos =  "option=1&id_job="+job_id+"&id_order="+SalesOrderNumber+"&user_id="+user_id;

       // alert(datos);

     if(job_id==''){

       alert("Debe indicar el JOB asociado a esta Orden de Salida");

     }else{

       if(LineArray!=''){

       //Envio detalles de la orden
        send_data(datos);


           $.each(LineArray, function(index,value) {


           send_data(value);//envio cada item de la cotizacion


           });

           var r = confirm("Desea imprimir la orden?");

          if (r == true) {


             printer(SalesOrderNumber);



           }else{

             msg(1);

           }




 }else{

   alert("Su lista de orden esta vacia, no será procesada");


    }

 }

}

}
function msg(r){

  if(r==1){ alert("La orden se ha enviado con exito");  

 location.reload(true); 
}else{  
 
 location.reload(true); 
   }



location.reload(true);

}

function send_data(data){

//alert(data);

   $.ajax({
          type: 'POST',
          url: 'form_query.php',
          data: data,
          dataType: 'html',
          success: function(res) {

            // alert(res);

            $("#prueba").html(res);

            }
        });

 }

function printer(order){


var datos= "id_item="+order+"&option=5";

$.ajax({
type: "POST",
url: "form_query.php",
data: datos,
success: function(res){


   print_data(res);

   }
});



function print_data(data){
//alert(data);

  

var logo = '<div style="background-color:white; width:100%; "><img src="images/logo.png" width="120px" /></div>';
var mywindow = window.open("", "","width=700, height=900, scrollbars=yes");

mywindow.document.write('<html><head><link rel="stylesheet" href="css/style_print.css"><title>Orden de salida de materiales</title></head><body style="background:white;" >');
mywindow.document.write('<div  style="margin-left:50px; margin-right:50px; " >');
mywindow.document.write(logo);
mywindow.document.write(data);
mywindow.document.write('</div></body></html>');
mywindow.document.close(); // necessary for IE >= 10
mywindow.focus(); // necessary for IE >= 10
//mywindow.print();
//mywindow.close();
msg(2);
   }
 }

function show_sales(URL,id){


     var datos= "url=bridge_query/get_salesorder_info/"+id;

      
       $.ajax({
         type: "GET",
         url: URL+'index.php',
         data: datos,
         success: function(res){

           //$("historial").hide();

           $("#info").html(res);

                 }
            });


 }

 function  show_invadj(URL,id,resp){


     var datos= "url=bridge_query/get_invadj_info/"+id+'/'+resp;

      
       $.ajax({
         type: "GET",
         url: URL+'index.php',
         data: datos,
         success: function(res){

           //$("historial").hide();

           $("#info").html(res);

                 }
            });


 }

function show_invoice(URL,id){


     var datos= "url=bridge_query/get_sales_info/"+id;

      
       $.ajax({
         type: "GET",
         url: URL+'index.php',
         data: datos,
         success: function(res){

           //$("historial").hide();

           $("#info").html(res);

                 }
            });


 }
function show_req(URL,id){


     var datos= "url=bridge_query/get_req_info/"+id;

      
       $.ajax({
         type: "GET",
         url: URL+'index.php',
         data: datos,
         success: function(res){

           //$("historial").hide();

           $("#info").html(res);

                 }
            });


 }

function show_req_urg(job){


URL = document.getElementById('URL').value;

var datos= "url=bridge_query/get_ReqJob/"+job;
   
var link= URL+"index.php";

$('#reqInfo').html('<P>CARGANDO ...</P>');

  $.ajax({
      type: "GET",
      url: link,
      data: datos,
      success: function(res){
      
       $('#reqInfo').html(res);
       // alert(res);

        }
   });
}

function close_req(id){ 

var reason = document.getElementById('req_reason_close').value;
var URL    = document.getElementById('URL').value;
var datos  = "url=bridge_query/set_reason_close/"+id+"/"+reason;

document.getElementById('info').value = 'Procesando cierre...';

r = confirm ('Esta seguro de  procesar el cierre definitivo de la requisicion "'+id+'" ?');

if(r==true){

   $.ajax({
         type: "GET",
         url: URL+'index.php',
         data: datos,
         success: function(res){

           alert('Se ha cerrado con exito la requisicion No. '+id);

           show_req(URL,id);

           
                 }
            });


}
    

 }


 function items(url,id){


     var datos= "url=bridge_query/get_items_by_invoice/"+id;

       
     var link= url+"index.php";

     
       $.ajax({
         type: "GET",
         url: link,
         data: datos,
         success: function(res){

          $("#tableInv").html(res);

                 }
            });


 }



function printer_sales(order){


var datos= "id_item="+order+"&option=12";

$.ajax({
type: "POST",
url: "form_query.php",
data: datos,
success: function(res){


   print_data_sales(res);

   }
});



function print_data_sales(data){
//alert(data);


var logo = '<div style="background-color:white; width:100%; "><img src="images/logo.png" width="120px" /></div>';
var mywindow = window.open("", "","width=700, height=900, scrollbars=yes");

mywindow.document.write('<html><head><link rel="stylesheet" href="css/style_print.css" /><title>Orden de venta</title></head><body style="background:white;" >');
mywindow.document.write('<div  style="margin-left:50px; margin-right:50px;" >');
mywindow.document.write(logo);
mywindow.document.write(data);
mywindow.document.write('</div></body></html>');
mywindow.document.close(); // necessary for IE >= 10
mywindow.focus(); // necessary for IE >= 10
mywindow.print();
mywindow.close();
msg(2);
   }
 }




// ********************************************************
// * USER MANAGMENT function
// ********************************************************

function ValidateEmail(email) {
   var expr = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
    return expr.test(email);
    };
  //Valido el match de los password
  $(document).ready(function(){
  $('#pass_2').focusout(function(){
    var pass = $('#pass_1').val();
    var pass2 = $('#pass_2').val();
      if(pass != pass2){
      alert('Password no coninciden');
      }

  });

  //Valido si la sintaxis de la direccion de correo es valido
   $('#mail').focusout(function(){
    if (!ValidateEmail($("#mail").val())) {
      alert("La direccion de correo no es correcta.");
      }

       });
    });

function edit(id){

var datos = 'option=1&id='+id;

var link = url+'/application/model/jquery.php';

$.ajax({
type: 'POST',
url:'index.php',
data: datos,
success: function(dat){
 alert(dat);

var user_info = JSON.parse(dat);


   document.getElementById("name2").value = user_info.name;
   document.getElementById("lastname2").value =user_info.lastname ;
   document.getElementById("email2").value = user_info.email;  
   document.getElementById("priv").value = user_info.role ;  
   document.getElementById("pass_12").value = user_info.pass ;  
   document.getElementById("pass_22").value = user_info.pass ;  
   document.getElementById("id_user").value = user_info.id;
   

}


});

}


function erase_user(URL){

var id=document.getElementById("user_2").value;
var name = document.getElementById("name2").value;
var lastname =  document.getElementById("lastname2").value;

var datos = 'url=bridge_query/erase_account/'+id;

var r = confirm('Este seguro de eliminar definitivamente la cuenta del usuario '+name+' '+lastname+' ?');

if(r==true){

$.ajax({
type: 'GET',
url:  URL+'index.php',
data: datos,
success: function(dat){

 alert('La cuenta se ha eliminado exitosamente.'); 

history.go(-1); 
return true;

}


});


}
  
}


// ********************************************************
// * location management
// ********************************************************


function FILTER(URL){

var stock = $('#item_by_stock').val();


var data = 'url=bridge_query/get_item_filter_by_stock/'+stock;

$('.loader').show();

document.getElementById("allDocument").style.visibility = "hidden";

    $.ajax({
    type:"GET",
    url: URL+'index.php',
    data: data,
    success: function(dat){


      $('#items_by_stock').html(dat);

      $('.loader').hide();
      document.getElementById("allDocument").style.visibility = "visible";

     } 

   });


}
  
function view_items(URL,ruta){

  $('.loader').show();
  document.getElementById("allDocument").style.visibility = "hidden";

  var data = 'url=bridge_query/get_item_filter_by_route/'+ruta;

    $.ajax({
    type:"GET",
    url: URL+'index.php',
    data: data,
    success: function(dat){

    
      
      $('#items_by_stock').html(dat);

      $('.loader').hide();
      document.getElementById("allDocument").style.visibility = "visible";

       

     } 

   });


}


  
function crear_almacen(){


$("#crear_alm_imp").show();
$("#bodega").hide();

}

function save_alm(URL){

var name = document.getElementById('almacen').value;

url = URL;


if(name!=''){

    var data = 'url=bridge_query/set_almacen/'+name.toUpperCase();

    

     $.ajax({
       type: 'GET',
       url: url+'index.php',
       data: data,
       success: function(res) {

    alert(res);

    location.reload(true); 

          }
     });

  }else{


  alert('El campo no debe estar vacio');

  }


$("#crear_alm_imp").hide();
$("#bodega").show();

}


function set_location(URL){


if(document.getElementById("stock").value==''){


alert('Debe seleccionar al menos el almacen');



} else {  

ALMACEN = document.getElementById("stock").value;

if(document.getElementById("stock_estand").value=='') { MUEBLE = '0'; } else {MUEBLE = mueble_ID; }
if(document.getElementById("stock_column").value=='') { COLUMNA = '0'; } else {COLUMNA=colum_ID; }
if(document.getElementById("stock_row").value==''){ FILA = '0'; } else { FILA=ROW_ID; }

var locationAL = 'A'+ALMACEN+'M'+MUEBLE+'C'+COLUMNA+'F'+FILA;

var datos ='url=bridge_query/set_location/'+ALMACEN+'/'+locationAL;

 $.ajax({
       type: 'GET',
       url: URL+'index.php',
       data: datos,
       dataType: 'html',
       success: function(res) {

    alert(res);

    location.reload(true); 

          }
     });

  }

 }


