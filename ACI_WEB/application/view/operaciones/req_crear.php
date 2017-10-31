<script type="text/javascript">


// ********************************************************
// * Aciones cuando la pagina ya esta cargada
// ********************************************************
$(window).load(function(){
   

$('#ERROR').hide();

   //lista jobs
     jobs();

    //lista phases
     

var table = $("#table_req_tb").dataTable({

      bSort: false,
      responsive: true,
      searching: false,
      paging:    false,
      info:      false,
      collapsed: false

 });


});





// Variables globales
URL = document.getElementById('URL').value;
link = URL+"index.php";

chk = '';
cantLineas = 500; //Setea la cantidad de lineas disponibles en la tabla de solicitud de items


JOBS = '';
PHASES = '';
//Variables globales

//datatables
/////////////////////////////////////////////////////////////////



/////////////////////////////////////////////////////////////////////////////////////////////////
function jobs(){


/*JOBS*/
 var datos= "url=bridge_query/get_JobList";


	$.ajax({
	  type: "GET",
	  url: link,
	  data: datos,
	  success: function(res){

		JOBS = res;


		$('#JOBID').append(JOBS);
        	    
	    if(res){
	    	    phase();
				}			

    		}
});
/*JOBS*/

}
/////////////////////////////////////////////////////////////////////////////////////////////////

function phase(){

/*PHASES*/
 var datos= "url=bridge_query/get_phaseList";

		$.ajax({
			  type: "GET",
			  url: link,
			  data: datos,
			  success: function(res){

				PHASES = res;
                            
				if(res){
				init(1); //llamo a construir tabla
				}

                            

                
             }
   });
/*PHASES*/

}

/////////////////////////////////////////////////////////////////////////////////////////////////

function init(chk)

{

var listitem = '';
var i = 1;
var datos= "url=bridge_query/get_ProductsCode/";
var reglon = '';



$.ajax({
      type: "GET",
      url: link,
      data: datos,
      success: function(res){

      
      listitem = res;

$('#table_req').html(''); //limpio la tabla 

function n(n){

    return n > 9 ? "" + n: "0" + n;

}

while(i <= cantLineas){

		   if(chk==1){ 
				reglon = '<td  width="10%" >'+n(i)+'</td>';  

			}else{

			reglon = '<td width="10%" >'+
						 '<div class="select-editable">'+
						    '<select id="sel'+i+'" >'+
						        '<option value=""></option>'
						         +listitem+
						    '</select>'+
						    '<input id="inp'+i+'" type="text" name="format" value="" />'+
						'</div>'+
						'</td>';

			 }     

			var line_table_req = '<tr>'+reglon+
		        '<td width="30%" class="rowtable_req"      id="DESC'+i+'" onkeyup="checkChar('+i+');" contenteditable></td>'+
			'<td width="15%" class="rowtable_req numb" id="QTY'+i+'"  onfocusout="checkInp('+i+');" contenteditable></td>'+
			'<td width="15%" class="rowtable_req"      id="UNI'+i+'"  onkeyup="checkuni('+i+');" contenteditable></td>'+
			'<td width="15%" class="rowtable_req"       ><select id="PHS'+i+'" ><option  value="-" selected>-</option>'+PHASES+'</select></td>'+
			'</tr>' ;

			 i++
			 $('#table_req').append(line_table_req); //limpio la tabla 
			}

       
      }
     });


}

function checkNOTA(){



var x=document.getElementById('nota').value;

var patt_slash = new RegExp("@");
var slash = patt_slash.test( x );

if (slash == true){

  	document.getElementById('nota').value = x.slice(0,-1);

    alert("No se permite carecteres especiales en este campo");
    
    return false;
  }




var patt_comilla = new RegExp("'");
var comilla = patt_comilla.test( x );

if (comilla  == true){

  	document.getElementById('nota').value = x.slice(0,-1);

    alert("No se permite carecteres especiales en este campo");
    
    return false;
  }

/*
var patt_dat = new RegExp("#");
var dat = patt_dat.test( x );

if (dat == true){

  	document.getElementById('nota').value = x.slice(0,-1);

    alert("No se permite carecteres especiales en este campo");''
    
    return false;
  }*/

 if (x.length > 1024) 
  {
  	document.getElementById('nota').value =  x.slice(0,-1);
    alert("El campo de Nota admite un maximo de 1024 caracteres");
    
    return false;
  }


}


function checkChar(line){

var DESCID = 'DESC'+line;
var x=document.getElementById(DESCID).innerHTML;

var patt = new RegExp("@");
var val = patt.test( x );

console.log(val);

  if (val== true) 
  {

  	document.getElementById(DESCID).innerHTML = x.slice(0,-1);

   alert("No se permite carecteres especiales en este campo");
    
    return false;
  }



var patt_comilla = new RegExp("'");
var comilla = patt_comilla.test( x );

if (comilla == true){

  	document.getElementById(DESCID).innerHTML = x.slice(0,-1);

    alert("No se permite carecteres especiales en este campo");
    
    return false;
  }
 

  if (x.length > 255) 
  {
  	document.getElementById(DESCID).innerHTML =  x.slice(0,-1);
    alert("El campo de Descripcion admite un maximo de 255 caracteres");
    
    return false;
  }



}

function checkInp(line)
{


var QTYID = 'QTY'+line;

console.log(QTYID);

var x=document.getElementById(QTYID).innerHTML;

  if (isNaN(x)) 
  {
  	document.getElementById(QTYID).innerHTML = '';
    alert("La entrada de CANTIDAD debe ser solo en valores numericos");
    
    return false;
  }


  if (x.length > 18) 
  {
  	document.getElementById(QTYID).innerHTML  = x.slice(0,-1);
    alert("La entrada de CANTIDAD no debe ser mayor a 18 digitos");
    
    return false;
  }


}



function checkuni(line)
{

var UNIID = 'UNI'+line;
var x=document.getElementById(UNIID).innerHTML;

  if (x.length > 15) 
  {
  	document.getElementById(UNIID).innerHTML = x.slice(0,-1);
    alert("La entrada de UNIDAD no debe ser mayor a 15 caracteres");
    
    return false;
  }
}


/////////////////////////////////////////////////////////////////////////////////////////////////
</script>


<div class="page col-lg-12">

<!--INI DIV ERRO-->
<div id="ERROR" class="alert alert-danger"></div>
<!--INI DIV ERROR-->

<div  class="col-lg-12">
<!-- contenido -->
<h2>Requisiciones</h2>
<div class="title col-lg-12"></div>
<div class="separador col-lg-12"></div>

 <!-- FIN VENTANA -->

<input type="hidden" id='URL' value="<?php ECHO URL; ?>" />


	<div class="col-lg-6">
	   <button class="btn btn-blue btn-sm"  data-toggle="collapse" data-target="#Solicitud" onclick="javascript:  $(this).find('i').toggleClass('fa-minus-circle fa-plus-circle ');"><i  class='fa fa-minus-circle'></i> Detalle de solicitud</button>
	   <input type="submit" onclick="send_req_order();" class="btn btn-primary  btn-sm btn-icon icon-right" value="Procesar" />
	  
	</div>


<div class="separador col-lg-12"></div>

<div id='Solicitud' class="collapse in col-lg-10" >
	
<fieldset>
<input type="hidden" id='user' value="<?php echo $active_user_id; ?>" />

<div class="col-lg-12">

<div   class="col-lg-6"> 
<label style="display:inline" > Proyecto : </label>
<select id="JOBID" >
<option value="-" selected>-</option>
</select>
</div>

<div  class="col-lg-1"></div>

 <div   class="col-lg-5">
  <label style="display:inline" > Fecha Solicitud: </label>
  <input style="text-align: center;" class="input-control" name="date" id="date" value="<?php echo date("Y-m-d"); ?>" readonly/>
 </div>
<div  class="separador col-lg-12"></div>
  <div   class="col-lg-5">
  <label style="display:inline" > Fecha inicio actividad: </label>
  <input type='date' style="text-align: center;" class="input-control" name="date_ini" id="date_ini" min='<?php 
	$datetime = new DateTime('tomorrow');
	echo $datetime->format('Y-m-d');
	?>' value="" />
 </div>

</div>

        
 <div  class="title col-lg-12"></div>
		
	 <div class="col-lg-8">
         <fieldset>
       	
         	<div class="comment-text-area col-lg-12">
         		<strong>Nota: </strong><textarea class="textinput" onkeyup="checkNOTA();" rows="5" cols="70" id="nota" name="nota"></textarea>
        		
         	</div>
         </fieldset>
		</div> 
   
						



<div class=" col-lg-4">
<fieldset>
	
	
	<legend><h4>Opciones</h4></legend>

	<input type="CHECKBOX" name="urgent_chk" id="urgent_chk" value="0" />&nbsp<label>Requisicion Urgente</label><br>
	<input type="CHECKBOX" name="pay_chk" id="pay_chk" value="0" />&nbsp<label>Pago Adelantado</label>


	
</fieldset>
</div>

</fieldset>

</div>
<div class="separador col-lg-12"></div>		

<div class=" col-lg-12"> 

<fieldset class="table_req" >
<table id="table_req_tb" class="table table-striped table-condensed table-bordered " cellspacing="0">
	<thead>
		<tr >
			<th width="10%" >Renglon</th>
			<th width="35%" class="text-center">Descripcion</th>
			<th width="15%" class="text-center">Cantidad</th>
			<th width="15%" class="text-center">Unidad</th>
			<!-- <th width="15%" class="text-center">Proyecto</th> -->
			<th width="15%" class="text-center">Fase</th>
		</tr>
	</thead>
	<tbody id="table_req" >	

	</tbody>
</table>
</fieldset>

</div>
</div>
</div>
<input type="hidden" id="req_no_jobid" value="" />



 <!-- FIN VENTANA -->
</div>
</div>


<script>
var falta = 1;
LineArray = [];
FaltaArray  = [];
URL = document.getElementById('URL').value;



function set_job(jobid){

document.getElementById('jobID_db').value = jobid;


}

function set_phase(phaseid){

document.getElementById('phaseID_db').value = phaseid;


}



CHK_VALIDATION = false;

function validacion(){

$('#ERROR').hide();

MSG_ERROR_RELEASE();

    //VALIDAR JOB
	JOBID = document.getElementById('JOBID').value;

	if (JOBID == '-'){

	 MSG_ERROR('Es obligatorio indicar el proyecto de referencia',1);
	 
	 CHK_VALIDATION = true;
	}

	 //VALIDAR NOTA
	NOTA =  $("#nota").val();
	console.log('nota '+NOTA);

	if (!NOTA){

	  MSG_ERROR('Es obligatorio completar el campo de Nota',1);
	 
	 CHK_VALIDATION = true;
	}

   //VALIDAR FECHA DE INICIO
   DATE = document.getElementById('date_ini').value;

   if (!DATE ){

	  MSG_ERROR('Es obligatorio completar el campo de fecha de inicio',1);
	 
	 CHK_VALIDATION = true;
	}

console.log(DATE);
console.log(isFutureDate(DATE));

	if (isFutureDate(DATE) == false ) {
	
	  MSG_ERROR('La fecha de inicio debe ser en el futuro',1);
		 
		 CHK_VALIDATION = true;
	}




}

function isFutureDate(idate){
var today = new Date().getTime(),
    idate = idate.split("-");


idate = new Date(idate[0], idate[1] , idate[2]).getTime();

return (today - idate) < 0 ? true : false;
}


function send_req_order(){


var flag = '';
var count= 0;
var arrLen = '';

validacion();
if(CHK_VALIDATION == true){ CHK_VALIDATION = false;  return;  }


flag = set_items(); //GUARDO ITEM EN ARRAY 

if(flag==1){  //SI HAY ITEMS EN LA LISTA

var r = confirm('Desea enviar esta requisicion ahora?');
    
if (r == true) { 

        spin_show();

        var link = URL+"index.php";


        if (document.getElementById('urgent_chk').checked) {

        	var set_urgent = 0;

        }else{

        	var set_urgent = 1;
        }

        if (document.getElementById('pay_chk').checked) {

        	var isPay = 0;

        }else{

        	var isPay = 1;

        }

        //REGITRO DE CABECERA
        function set_header(){

        	
	        var JOBID = document.getElementById('JOBID').value;
	        var nota  = document.getElementById('nota').value;
	        var date_ini = document.getElementById('date_ini').value;

	        var datos= JOBID+"@"+nota+"@"+set_urgent+"@"+date_ini+"@"+isPay; //LINK DEL METODO EN BRIDGE_QUERY
 



	     return   $.ajax({
					type: "GET",
					url: link,
					data: {url: 'bridge_query/set_req_header', Data : datos }, 
					success: function(res){
	                                 
	                                 Req_NO = res;

	                                 console.log(res);

	                                 $('#req_no_jobid').html(res);
									
					 }
				      });
	 }//FIN REGISTRO DE CABECERA

		
	$.when(set_header()).done(function(Req_NO){ //ESPERA QUE TERMINE LA INSERCION DE CABECERA

        // REGISTROS DE ITEMS 
		    $.ajax({
				 type: "GET",
				 url:  link,
				 data:  {url: 'bridge_query/set_req_items_test/'+Req_NO.trim() , Data : JSON.stringify(LineArray)}, 
				 success: function(res){
        		   
        		    console.log('RES:'+res);
					      
					if(res==1){//TERMINA EL LLAMADO AL METODO set_req_items SI ESTE DEVUELV UN '1', indica que ya no hay items en el array que procesar.
									
						send_mail(link,Req_NO,set_urgent,isPay);
					}

				   }
				});       
 
     });//FIN REGISTROS DE ITEMS

   }

}

if(flag==0){ 

 MSG_ERROR('Debe llenar la solicitud con almenos un item en la lista',0); 
}

//MANEJO DE ERRORES POR FAMPO FALTANTES EN LOS ITEMS
if(flag==2){ 

MSG_ERROR_RELEASE(); //LIMPIO DIV DE ERRORES

FaltaArray.forEach(ListFaltantes);


  function ListFaltantes(item,index){

      column = FIND_COLUMN_NAME(index);
      
      MSG_ERROR('No se indico valor en el Item: '+item+" / Campo :" +column, 1); 
      

  }

FaltaArray.length = ''; //LIMPIO ARRAY DE ERRORES

}

}
/////////////////////////////////////////////////////////////////////////////////////


function FIND_COLUMN_NAME(item){

	switch (item){

	  case 1: val ='Descripcion'; break;
	  case 2: val ='Cantidad'; break;
	  case 3: val ='Unidad'; break;
	  case 4: val ='Fase'; break;

	   
	}

return val;
							
}

function send_mail(link,Req_NO,flag_urgent,isPay){

 //ENVIO POR MAIL 
	var datos= "url=ges_requisiciones/req_mailing/"+Req_NO+"/"+flag_urgent+"/"+isPay; //LINK A LA PAGINA DE MAILING


	$.ajax({
		type: "GET",
		url: link,
		data: datos,
		success: function(res){
									      
			if(res==0){

			 alert('NO SE HA PODIDO ENVIAR LA NOTIFICACION DE ORDEN DE COMPRA.');
			 msg(link,Req_NO,isPay,flag_urgent);
			 
			}else{  
			
			 msg(link,Req_NO,isPay,flag_urgent);
			}

		}
	}); 
	//FIN ENVIO POR MAIL 


}		 			

//FUNCION PARA SOLICITAR IMPRESION DEL REPORTE
function msg(link,Req_NO,isPay,flag_urgent){

spin_hide();
   alert("La orden se ha enviado con exito");

	var R = confirm('Desea imprimir la orden de venta?');

	if(R==true){
         
         count = 1;
         LineArray.length='';
         window.open(link+'?url=ges_requisiciones/req_print/'+Req_NO+"/"+isPay+"/"+flag_urgent,'_self');
                 
    }else{

	    count = 1;
	    LineArray.length='';
            location.reload();

	}



}





//FUNCION PARA GUARDAR ITEMS EN ARRAY 
function set_items(){

LineArray.length=''; //limpio el array
FaltaArray.length='';

var flag = ''; 
var theTbl = document.getElementById('table_req'); //objeto de la tabla que contiene los datos de items
var line = '';

for(var i=0; i<cantLineas ;i++) //BLUCLE PARA LEER LINEA POR LINEA LA TABLA theTbl
{
	cell = '';
	y='';

    for(var j=0;j<theTbl.rows[i].cells.length; j++) //BLUCLE PARA LEER CELDA POR CELDA DE CADA LINEA
        {

              y=i+1;
    	  	  var selid = "sel"+y;
    	  	  var phsid = "PHS"+y;
 
    	  	      if(theTbl.rows[i].cells[1].innerHTML!=''){ //valido que la columna 1 (DESCRIPCION) sea diferente a vacio.	

                    //leer columnas de jobs
    	  	      	switch (j){

                       case 4:
                           cell += '@'+document.getElementById('JOBID').value+'@'+document.getElementById('JOBID').options[document.getElementById('JOBID').selectedIndex].text+'@'+document.getElementById(phsid).value+'@'+document.getElementById(phsid).options[document.getElementById(phsid).selectedIndex].text;
                            
 								//SI LA CELDA NO CONTIENE VALOR 
	                               if(document.getElementById(phsid).value == '-'){
	                                   
	                                  FaltaArray[j] = i+1;
	                               }

                              break;            


                       default: 

                             val= theTbl.rows[i].cells[j].innerHTML;

                             cell += '@'+val;//agrego el registo de las demas columnas

                               //SI LA CELDA NO CONTIENE VALOR 
                               if(val==''){
                                   
                                  FaltaArray[j] = i+1 ;
                               }

                             break;
                            }
                     //fin leer columnas de jobs
    	  	      	
    	  	      }
    	  	  

        }//FIN BLUCLE PARA LEER CELDA POR CELDA DE CADA LINEA


	    	if (theTbl.rows[i].cells[1].innerHTML!=''){

       
		       LineArray[i]=cell; 
		       console.log(LineArray);

			    
		     }



}//FIN BLUCLE PARA LEER LINEA POR LINEA DE LA TABLA 

 
//SETEA RETURN DE LA FUNCION, FLAG 1 Ó 0, SI ES 1 LA TABLA ESTA LLENA SI ES 0 LA TABLA ESTA VACIA.
if(FaltaArray.length == 0){

    if(LineArray.length >= 1){ 
      flag = 1; 
     }else{  
      flag = 0; 
     }

}else{
    
    LineArray.length = '';
    cell = '';
    flag = 2; //Alguna linea no tiene descripcion

}


return flag;
}



</script>


