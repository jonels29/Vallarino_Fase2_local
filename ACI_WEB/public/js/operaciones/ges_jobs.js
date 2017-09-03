
//FUNCION PARA CARGAR LOS JOBS

function load(type){


URL = document.getElementById('URL').value;

  var datos= "url=ges_proyectos/load_jobs/"+type;
   
  var link= URL+"index.php";

  $('#table_job_div').html('<P>CARGANDO ...</P>');

    $.ajax({
      type: "GET",
      url: link,
      data: datos,
      success: function(res){
      
       $('#table_job_div').html(res);
       // alert(res);

        }
   });

}

//FUNCION PARA VALIDAR QUE EL CAMPO DESCRIPCION NO TENGA CARACTERES ESPECIALES

function checkDesc(){


  var x=document.getElementById('desc').value;


  var patt_slash = new RegExp("/");
  var slash = patt_slash.test( x );

    if (slash == true){

        document.getElementById('desc').value = x.slice(0,-1);

        alert("No se permite caracteres especiales en este campo");
    
        return false;
  }




  var patt_comilla = new RegExp("'");
  var comilla = patt_comilla.test( x );

    if (comilla  == true){

      document.getElementById('desc').value = x.slice(0,-1);

      alert("No se permite carecteres especiales en este campo");
    
      return false;
  }


  var patt_dat = new RegExp("#");
  var dat = patt_dat.test( x );

  if (dat == true){

    document.getElementById('desc').value = x.slice(0,-1);

    alert("No se permite carecteres especiales en este campo");
    
    return false;
  }

 if (x.length > 100) 
  {
    document.getElementById('desc').value =  x.slice(0,-1);
    alert("El campo de Descripcion admite un maximo de 100 caracteres");
    
    return false;
  }


}


//FUNCION PARA VALIDAR QUE EL CAMPO JOB ID NO TIENE CARACTERES ESPECIALES

function checkJobid(){


var x=document.getElementById('job_id').value;


var patt_slash = new RegExp("/");
var slash = patt_slash.test( x );

if (slash == true){

    document.getElementById('job_id').value = x.slice(0,-1);

    alert("No se permite carecteres especiales en este campo");
    
    return false;
  }




var patt_comilla = new RegExp("'");
var comilla = patt_comilla.test( x );

if (comilla  == true){

    document.getElementById('job_id').value = x.slice(0,-1);

    alert("No se permite carecteres especiales en este campo");
    
    return false;
  }


var patt_dat = new RegExp("#");
var dat = patt_dat.test( x );

if (dat == true){

    document.getElementById('job_id').value = x.slice(0,-1);

    alert("No se permite carecteres especiales en este campo");
    
    return false;
  }

 if (x.length > 20) 
  {
    document.getElementById('job_id').value =  x.slice(0,-1);
    alert("El campo de job Id admite un maximo de 20 caracteres");
    
    return false;
  }


}


//FUNCION PARA CREAR JOBS NUEVOS

function create_job(){


var id = document.getElementById('job_id').value;
var desc =  document.getElementById('desc').value;

if (id != '' && desc != '') {

URL = document.getElementById('URL').value;

var datos= "url=ges_proyectos/create_job/"+id+"/"+desc;
   
var link= URL+"index.php";

  $.ajax({
      type: "GET",
      url: link,
      data: datos,
      success: function(res){
      
        alert("El Proyecto fue agregado con exito");

          document.getElementById('job_id').value ='';
          document.getElementById('desc').value ='';

          location.reload(true);

        }
   });

}else{

  alert("Los campos Job id y descripcion deben estar llenos");

}
}


//FUNCION PARA ACTIVAR Y DESACTIVAR JOBS 

function act_job(id_table,action){


var msg;

if (action == 0) {

msg =  'El proyecto ha sido desactivado con exito!';

}else{

msg =  'El proyecto ha sido activado con exito!';

}


URL = document.getElementById('URL').value;

var datos= "url=ges_proyectos/act_jobs/"+id_table+"/"+action;
   
var link= URL+"index.php";


  $.ajax({
      type: "GET",
      url: link,
      data: datos,
      success: function(res){
      
       
       alert(msg);
       location.reload(true);

        }
   });


}

//FUNCION PARA SETAR LOS VALORES CUANDO EMERGE EL MODAL DE MODIFICACION

function set_modal_job(id_table,job_id,desc){

document.getElementById('id_modal').value = id_table;
document.getElementById('job_id_modal').value = job_id;
document.getElementById('desc_modal').value = desc;


}


//FUNCION PARA MODIFICAR EL JOB

function modify_job(){


var id_table = document.getElementById('id_modal').value;
var job_id = document.getElementById('job_id_modal').value;
var desc = document.getElementById('desc_modal').value;


URL = document.getElementById('URL').value;

var datos= "url=ges_proyectos/modify_job/"+id_table+"/"+job_id+"/"+desc;
   
var link= URL+"index.php";


if (desc =='') {

  alert('El campo descripcion debe estar lleno!');

}else{

var R = confirm('Desea modificar el Proyecto: '+job_id+' ?');

  if (R==true) {

  $.ajax({
      type: "GET",
      url: link,
      data: datos,
      success: function(res){
      
        alert('El Proyecto ha sido modificado con exito!');
        location.reload(true);

        }
   });

    } 
  }

}

// FUNCION PARA BORRAR LOS JOBS
function del_job(id){


URL = document.getElementById('URL').value;

var datos= "url=ges_proyectos/del_jobs/"+id;
   
var link= URL+"index.php";


var r = confirm('Este seguro de eliminar definitivamente el proyecto: '+id+' ?');

if (r==true) {

  $.ajax({
      type: "GET",
      url: link,
      data: datos,
      success: function(res){
      
       
       alert('El proyecto ha sido eliminado con exito!');
       location.reload(true);

        }
   });
  }
  
}