
<script src="<?php echo URL; ?>js/operaciones/ges_jobs.js"></script>

<script type="text/javascript">


// ********************************************************
// * Aciones cuando la pagina ya esta cargada
// ********************************************************

$(window).load(function(){ 

  $("#ERROR").hide();

var filter = document.getElementById('select_filter').value;

load(filter);

});



/////////////////////////////////////////////////////////////////////////////////////////////////
</script>

<div class="page col-lg-12">

<!--INI DIV ERRO-->
<div id="ERROR" class="alert alert-danger"></div>
<!--INI DIV ERROR-->

<div  class="col-lg-12">
<!-- contenido -->
<h2>Gestion de Proyectos</h2>
<div class="title col-lg-12"></div>
<div class="separador col-lg-12"></div>

<input type="hidden" id='URL' value="<?php ECHO URL; ?>" />

  
<div class="separador col-lg-12"></div>

<div id='New_Job' class="col-lg-6" >
  
<fieldset>


  <input type="hidden" id='user' value="<?php echo $active_user_id; ?>" />
  <legend><h4>Crear Proyecto</h4></legend>
  <div class="col-lg-12">

    <div   class="col-lg-6"> 
        <label style="display:inline" > Job Id : </label>
        <input type="text" id='job_id' name='job_id' value="" max="20"  onkeyup="checkJobid();" />

    </div>

    <div  class="col-lg-6"></div>

      <div class="col-lg-6">
            <label style="display:inline" > Descripcion : </label>
            <input type="text" id='desc' name='desc' value="" max="100" onkeyup="checkDesc();"/>
            
      </div>

    <div  class="col-lg-6"></div>
    
   </div>

    
    <div  class="title col-lg-12"></div>
    <div  class="col-lg-6"></div>
    <div class="col-lg-6">
      <div class="col-lg-3"></div>
      <div class="col-lg-3">
       
       <input type="submit" onclick="create_job();" class="btn btn-primary  btn-sm btn-icon icon-right" value="Crear" />
      </div>
    </div>
            
</fieldset>

</div>

<div id='Filter' class="col-lg-6" >
  <div id='Filter' class="col-lg-3" >
  
  <fieldset>
    <legend><h4>Filtro</h4></legend>

    <select id="select_filter" onchange="load(this.value);">
  
      <option value="1" selected>Activos</option>
      <option value="0">Inactivos</option>
    </select>

  </fieldset>
  </div>
</div>



<!-- LISTA LOS JOBS. FILTROS PARA INACTIVOS Y ACTIVOS -->
<div  class="title col-lg-12"></div>
<div class="separador col-lg-12"></div> 

<fieldset>
<div class="col-lg-12"  > 
  <div id="table_job_div" ></div>
</div>  
</fieldset>  


</div>
</div>
<input type="hidden" id="req_no_jobid" value="" />




<!-- Modal : Modificacion de proyecto-->
<div id="jobModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h3 >Modificar Proyecto</h3>
      </div>

      <div class="col-lg-12 modal-body">
        
      <div id='prod'></div>

        <div class="col-lg-3" > 
             <label class="control-label">ID</label>
             <input  class="form-control" id="job_id_modal" name="job_id_modal"  readonly/>
             <input type="hidden" class="form-control" id="id_modal" name="id_modal"/>
        </div>
        
        <div class="col-lg-6" > 
             <label class="control-label">Descripcion: </label>
             <input  class="form-control" id="desc_modal" name="desc_modal" />
        </div>

        
        

<div class="col-lg-12" ></div>    
      </div>
      <div class="modal-footer">
        <button type="button" onclick="modify_job();" class="btn btn-primary" data-dismiss="modal">Modificar</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
      </div>
    </div>

  </div>
</div>

 <!-- FIN VENTANA -->

