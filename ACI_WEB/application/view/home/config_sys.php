 <!--INI DIV ERRO-->
<div id="ERROR" ></div>

<!--ERROR -->

<div id="ErrorModal" class="modal fade" role="dialog">

  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">

        <button type="button" onclick="javascript:history.go(-1);" class="close" data-dismiss="modal">&times;</button>
        <h3 >Error</h3>
      </div>

      <div class="col-lg-12 modal-body">

      <!--ini Modal  body-->  

            <div id='ErrorMsg'></div>

      <!--fin Modal  body-->

      </div>

      <div class="modal-footer">

        <button type="button" onclick="javascript:history.go(-1); return true;" data-dismiss="modal" class="btn btn-primary" >OK</button>

      </div>

    </div>

  </div>

</div>

<!--modal-->
<!--INI DIV ERROR-->


 <?php
 if (isset($_REQUEST['smtp'])) {

	
$value  = array(
'ID' => '1',
'HOSTNAME' => $_REQUEST['emailhost'],
'PORT'     => $_REQUEST['emailport'],
'USERNAME' => $_REQUEST['emailusername'],
'PASSWORD' => $_REQUEST['emailpass'],
'Auth' => 'true',
'SMTPSecure' => 'false',
'SMTPDebug' => '0');

$this->model->Query('DELETE from CONF_SMTP;');

$this->model->insert('CONF_SMTP',$value);
$this->CheckError();

unset($_REQUEST);

echo '<script> alert("Se ha actualizado con exito"); window.open("'.URL.'index.php?url=home/config_sys","_self");</script>';


}

 if(isset($_REQUEST['logo'])){

	$target_dir = "img/";

	$target_file = $target_dir . basename($_FILES["imageFile"]["name"]);
 
	$target_file;
	$uploadOk = 1;

	$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
	// Check if image file is a actual image or fake image

   if ($imageFileType=='jpg' || $imageFileType=='jpeg' ){ 

	      
	        $uploadOk = 1;


	 	   if (move_uploaded_file($_FILES["imageFile"]["tmp_name"], $target_file)) {
	 			

	 		rename("img/".$_FILES["imageFile"]["name"], "img/logo.jpg");
	        

	        echo '<script> alert("Se ha actualizado el logo con exito","ok"); 
	             window.open("'.URL.'index.php?url=home/config_sys","_self");</script>';

            } 


	    } else {

	    	
	        $uploadOk = 0;

	    }

    if ($uploadOk==0){   echo '<script>
	         alert("Se produjo un error al subir la imagen","ok"); window.open("'.URL.'index.php?url=home/config_sys","_self");</script>'; }

}

//GET LAST SYNC
$date_db =  $this->model->Query_value('PurOrdr_Header_Exp','LAST_CHANGE','order by LAST_CHANGE desc limit 1');	  

?>	




<div class="page col-xs-12">



<div  class="col-xs-12">
<!-- contenido -->
<h2>Configuracion del sistema</h2>
<div class="title col-xs-12"></div>
<div class="col-xs-1"></div>
<div class="col-xs-10">

<fieldset >
  <legend>Datos de generales</legend> 
<?php

if (isset($_REQUEST['comp'])) {
	
$value  = array(
'company_name' => $_POST['company'],
'email' => $_POST['email_contact'],
'address' => $_POST['address'],
'Tel' => $_POST['tel1'],
'Fax' => $_POST['tel2'] );


$this->model->update('company_info',$value,'Where id="1";');
$this->CheckError();

unset($_REQUEST);

echo '<script> alert("Se ha actualizado con exito"); window.open("'.URL.'index.php?url=home/config_sys","_self");</script>';


}




//LLAMO LOS VALORES ACTUALES DE LOS DATOS DE LA COMPAÑIA
$res= $this->model->Get_company_Info();
foreach ($res as $Comp_Info) {
	$Comp_Info = json_decode($Comp_Info);

	$name = $Comp_Info->{'company_name'};
	$email = $Comp_Info->{'email'};
	$address = $Comp_Info->{'address'};
	$tel= $Comp_Info->{'Tel'};
	$fax = $Comp_Info->{'Fax'};
}

?>
<form action="" role="form" class="form-horizontal" enctype="multipart/form-data" method="post" >

<input type="hidden" id="comp" name="comp" value="1" />

<div class="form-group">
<label class="col-sm-2 control-label" for="field-1">Compañia</label>

<div class="col-sm-10">
	<input type="text" class="form-control" id="company" name="company"  value="<?php echo $name; ?>"  /> 
</div>
</div>

<!--<div class="form-group">
<label class="col-sm-2 control-label" for="field-1">ID Compañia en SAGE</label>

<div class="col-sm-10">
<</div>
</div>-->

<div class="form-group">
<label class="col-sm-2 control-label" >Dirección</label>
<div class="col-sm-10">
<input type="text" class="form-control" id="address" name="address" value="<?php echo $address; ?>" />
</div>
</div>



<div class="form-group">
<label class="col-sm-2 control-label" >Email</label>
<div class="col-sm-10">
<input type="email" class="form-control" id="email_contact" name="email_contact" value="<?php echo $email; ?>" />
	<p class="help-block">Coloque el email de contacto de su compañia.</p>
	</div>
</div>



<div class="form-group">
	<label class="col-sm-3 control-label" >Teléfono</label>
<div class="input-group col-sm-2">
		<span class="input-group-addon">
			<i class="fa fa-phone"></i>
		</span>

<input type="text" class="form-control" id="tel1" name="tel1" value="<?php echo $tel; ?>" />

</div>


<label class="col-sm-3 control-label" >Fax</label>
	<div class="input-group col-sm-2">
	<span class="input-group-addon">
		<i class="fa fa-phone"></i>
	</span>
<input type="text" class="form-control" id="tel2" name="tel2" value="<?php echo $fax; ?>" />

	</div>
</div>
								
<div class="form-group col-lg-2">
<input type="submit"  value="Guardar" class="btn btn-primary  btn-block text-lef"/>
</div>
</form>
								

 </fieldset>
 <fieldset>
 	<legend>Logo</legend>

<form action="" role="form" class="form-horizontal" enctype="multipart/form-data" method="POST">

<div class="form-group">
<input type="hidden" id="logo" name="logo" value="1" />
	

    <img class="confLogo col-sm-2" src="img/logo.jpg" width="150" heigh="100" />

	<div class="col-sm-8">
		<input type="file" class="form-control" id="imageFile" name="imageFile">
			<p class="help-block">Formato de imagen permitido es jpg, tamaño maximo de 300k y dimensiones 150x150px</p>
	</div>
</div>
<div class="form-group col-lg-2">
<input type="submit"  value="Cargar imagen" class="btn btn-primary  btn-block text-lef" name="submit" />
</div>
 </form>


 </fieldset>

<?php

$sql = "SELECT * FROM CONF_SMTP WHERE ID='1'";

$smtp= $this->model->Query($sql);

foreach ($smtp as $smtp_val) {
  $smtp_val= json_decode($smtp_val);

  $hostname       = $smtp_val->{'HOSTNAME'};
  $emailport      = $smtp_val->{'PORT'};
  $emailusername  = $smtp_val->{'USERNAME'};
  $emailpass      = $smtp_val->{'PASSWORD'};


}

?>

<fieldset>
  <legend>Configuracion SMTP</legend>
<form action="" role="form" class="form-horizontal" enctype="multipart/form-data" method="POST">
<input type="hidden" id="smtp" name="smtp" value="1" />

<div class="form-group">
<label class="col-sm-2 control-label" >Host</label>
<div class="col-sm-8">
<input class="form-control" id="emailhost" name="emailhost" type="text" maxlength="64" value="<?php echo $hostname; ?>" required>
</div>
</div>


<div class="form-group">
<label class="col-sm-2 control-label" >Puerto</label>
<div class="col-sm-8">
<input  class="form-control" id="emailport" name="emailport" type="text" value="<?php echo $emailport; ?>" required>
	</div>
</div>

<div class="form-group">
<label class="col-sm-2 control-label" >Usuario</label>
<div class="col-sm-8">
<input class="form-control" id="emailusername" name="emailusername" type="text" maxlength="64" value="<?php echo $emailusername; ?>" required>
	</div>
</div>

<div class="form-group">
<label class="col-sm-2 control-label" >Contraseña</label>
<div class="col-sm-8">
<input class="form-control" name="emailpass" id="emailpass" type="password" maxlength="64" value="<?php echo $emailpass; ?>" required>
	</div>
</div>

<div style='float:right;' class="col-sm-2">
<input type="submit"  value="Guardar" class="btn btn-primary  btn-block text-lef"  />
</div>

</form>

<script type="text/javascript">
	
function send_test(){

URL       = document.getElementById('URL').value;
var email = document.getElementById('emailtest').value;

var datos= "url=bridge_query/send_test_mail/"+email;
   
var link= +"index.php";

$('#notificacion').html('<P>Enviando...</P>');

  $.ajax({
      type: "GET",
      url: link,
      data: datos,
      success: function(res){
      
       $('#notificacion').html(res);
       // alert(res);

        }
   });

}

</script>


<div class="separador col-lg-12"></div>

<div class="form-group">
<div class="col-sm-3">
<input type='button' onclick="javascript: send_test(); return false;" class="btn btn-default  btn-block text-lef" id="testmail" name="testmail"  value='Enviar email de prueba' />
</div>
<div class="col-sm-7">
<input class="form-control" name="emailtest" id="emailtest" type="email"  value="">
	</div>
<div class="col-sm-12" id='notificacion'></div>
</div>


</fieldset>




 <fieldset>
 	<legend>Datos de ventas</legend>


<?php

if (isset($_REQUEST['add'])) {
	
$value  = array(
'taxid' => $_POST['idtax'],
'rate' => $_POST['porc'],
 );


$this->model->INSERT('sale_tax',$value,'Where id="1";');

echo '<script> alert("El nuevo Tax se ha agregado con exito","ok"); window.open("'.URL.'index.php?url=home/config_sys","_self");</script>';


}

?>





<form action="" role="form" class="form-horizontal" enctype="multipart/form-data" method="POST">
<input type="hidden" id="sale" name="sale" value="1" />


<fieldset>
<legend><h4>Tax ID</h4></legend>
<div class="form-group">
<div class="col-lg-12"></div>

<?php
//LLAMO LOS VALORES ACTUALES DE LOS DATOS DE LA COMPAÑIA
$saleRes= $this->model->Get_sales_conf_Info();

foreach ($saleRes as $sale) {
	$sale = json_decode($sale);

	$tax =  $sale->{'taxid'};
	$porc = $sale->{'rate'};

	$table .= '<div class="col-lg-1"></div>
	            <div class="col-lg-4">
	             <input type="text" class="form-control"  value="'.$tax.'" disabled/> 
               </div>
               <div class="col-lg-4">
	             <input type="text" class="form-control"  value="'.$porc.'" disabled/> 
               </div>
               <div class="col-lg-2">
	             <input type="button" onclick="del_tax('.$sale->{'id'}.');" value="Borrar" class="btn btn-primary  btn-block text-lef"  />
               </div><div class="col-lg-12"></div>';
}

echo $table;
?>

<script type="text/javascript">
	
function del_tax(id){


URL = document.getElementById('URL').value;

var datos= "url=bridge_query/del_tax/"+id;
var link= URL+"index.php";

  $.ajax({
      type: "GET",
      url: link,
      data: datos,
      success: function(res){

		 alert("Se ha eliminado el tax seleccionado","ok"); 
		 window.open("index.php?url=home/config_sys","_self");

		}
   });


}


function del_po_tbl(mode){


URL = document.getElementById('URL').value;

var datos= "url=bridge_query/DEL_PO_TABLE/"+mode;
var link= URL+"index.php";

r = confirm('Esta seguro de realizar esta acción?');

if(r==true){

  $.ajax({
      type: "GET",
      url: link,
      data: datos,
      success: function(res){

      console.log(res);

              if(res.trim()==''){

		alert('LAS TABLAS SE HAN BORRADO CORRECTAMENTE');
                window.open("index.php?url=home/config_sys","_self");

		  }else{

                alert(res);
                window.open("index.php?url=home/config_sys","_self");

		  }

		}
   });

}




}

</script>




<div class="separador col-lg-12"></div>

<div class="col-lg-1"></div>
<div class="col-lg-4">
	<input type="text" class="form-control" id="idtax" name="idtax" required/> 
	<p class="help-block">ID del TAX que esta configurado en SAGE 50</p>
</div>

<div class="col-lg-4">
	<input type="text" class="form-control" id="porc" name="porc"  placeholder="0.00" required/> 
	<p class="help-block">% RATE o porcentaje del TAX que esta configurado en SAGE 50</p>
</div>

<div class="col-lg-2">
<input type="submit"  value="Agregar" class="btn btn-primary  btn-block text-lef" id="add" name="add"  />
</div>


</div>


</fieldset>

</form>


 </fieldset>


 <fieldset>
 	<legend>Base de datos</legend>

<div class="col-lg-12">
 <div class="col-lg-10">
	 Esta opcion borra las tablas referentes a los registros de Ordenes de Compras sincronizadas desde Peachtree 
	 <p class='help-block'>La ultima sincronizacion fue: <?php 




    $date = strtotime($this->model->GetLocalTime($date_db));
    $dateInLocal = date('d/M/Y g:i a',$date);


    if(!$date_db){

     echo '<i style="font-weight:bold; color:red; font-size:12; ">Tablas NO han sincronizado aún</i>';

    }else{

     echo '<i style="font-weight:bold; color:green; font-size:12;">'.$dateInLocal.'</i>';

    }


	 ?></p>
 </div>
 <div class="col-lg-2">
 <input type="submit"  onclick='del_po_tbl(1);' value="Limpiar toda la tabla" class="btn btn-danger  btn-block text-lef" id="poreset" name="preset"  />
  <div class="separador col-lg-12"></div>
 <input type="submit"  onclick='del_po_tbl(2);' value="Limpiar ultimo mes" class="btn btn-warning  btn-block text-lef" id="poreset" name="preset"  />
 </div>


</div>

</fieldset>

</div>	
</div>
</div>

