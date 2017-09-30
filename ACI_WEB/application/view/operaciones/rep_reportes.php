<div class="page col-xs-12">

<div  class="col-xs-12">
<!-- contenido -->
<h2>Generar Reportes</h2>
<div class="title col-xs-12"></div>

<div class="col-xs-12">


<div class="col-lg-12">
  <fieldset>

   <div class="col-lg-3">
       <select  id="reportType" required>
           <option  selected disabled>Seleccione el reporte</option>
<!--       <option  value="InvXVen" >INV. STOCK x VENC.</option>
           <option  value="InvXStk" >INVENTARIO x STOCK</option>
           <option  value="ConList" >CONSIGNACIONES</option> -->
       <?php if($this->model->rol_campo=='1' || $this->model->rol_compras=='1' ){ ?>
           <option  value="ReqStat" >REQUISICIONES</option>
           <option  value="ReqUrg" >REQUISICIONES URGENTES</option>
           <option  value="ItemXReq" >ITEMS POR REQUISICION</option>
       <?php } 
           if($this->model->rol_compras=='1' ){
       ?>
           <option  value="PurOrd"  >ORDENES DE COMPRA</option>
        <?php  } ?>
       </select>

  </div>



<div id='par_filtro' class="collapse " >
<div class="col-lg-12"></div>
<fieldset>
  <div class="col-lg-5" >

     <label>Registros entre</label>
     <div class='col-lg-12'>
     <input class='numb' type="date" id="date1" name="name1"  value="<?php echo date('Y-m-d', strtotime('today - 30 days'));?>" /> -
     <input class='numb' type="date" id="date2" name="name2"  value="<?php echo date('Y-m-d'); ?>"/> 
     </div>
  
  </div>

  <div class="col-lg-3" >
    
     <label>Sortear</label>
     <div class='col-lg-12'>
     <select   id="sort" required>
           
           <option  value="ASC">Ascendente (A-Z)</option>
           <option  value="DESC" selected>Descendente (Z-A)</option>
         
    </select>
    </div>
   
  </div>

  <div class="col-lg-2" >
  <label>Limitar</label>
    <div class='col-lg-12'>
     
     <input class='numb' type="number" min="1" max="10000" id="limit" value="200" required/>
     <p class="help-block">Maximo de 10000 registros</p>
    </div>
  </div>

</fieldset>

</div>

<div class="separador col-lg-12"></div>

<div class="col-lg-4" >
   <div class="col-xs-6">
   <button class="btn btn-blue btn-sm"  data-toggle="collapse" data-target="#par_filtro" onclick="javascript:  $(this).find('i').toggleClass('fa-plus-circle fa-minus-circle');"><i  class='fa fa-plus-circle'></i> Filtros</button>
   </div>

   <div class="col-xs-6">
   <input type="submit" onclick="Filtrar();" class="btn btn-primary  btn-sm  btn-icon icon-right" value="Consultar" />
   </div>      
</div>

 
</fieldset> 
<div class="separador col-lg-12"></div>

<script type="text/javascript">
  
function Filtrar(){


var limit = $('#limit').val();
var sort =  $('#sort').val();
var type =  $('#reportType').val();
var date1 = $('#date1').val();
var date2 = $('#date2').val();



URL = document.getElementById('URL').value;

var datos= "url=bridge_query/get_report/"+type+"/"+sort+"/"+limit+"/"+date1+"/"+date2;
   
var link= URL+"index.php";

$('#table').html('<P>CARGANDO ...</P>');

  $.ajax({
      type: "GET",
      url: link,
      data: datos,
      success: function(res){
      
       $('#table').html(res);
       // alert(res);

        }
   });



}


</script>

<fieldset>
<div class="col-lg-12"  > 
  <div id="table" ></div>
</div>  
</fieldset>

        

</div>  




</div>
</div>
</div>