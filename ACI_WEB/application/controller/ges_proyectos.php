<?PHP

class ges_proyectos extends Controller
{

//SE CARGA LA VISTA DE JOBS

public function ges_proyecto(){
$res = $this->model->verify_session();

        if($res=='0'){
        

            // load views
            require APP . 'view/_templates/header.php';
            require APP . 'view/_templates/panel.php';
            require APP . 'view/operaciones/ges_jobs.php';
            require APP . 'view/_templates/footer.php';


        }
}

//FUNCION DE CREACION DE JOBS

public function create_job($id,$desc){

$this->model->verify_session();

if ($id != '' && $desc != '') {
	
	$val = array('ID_compania' => $this->model->id_compania,
				 'JobID' => $id,
				 'Description' => $desc,
				 'JobPhases' => 1,
				 'IsActive' => 1);

	$res = $this->model->insert('Jobs_Exp',$val);
	echo $res;


		}
}


//FUNCION QUE CARGA LA TABLA DE JOBS DE ACUERDO AL ESTATUS ACTIVO E INACTIVO

public function load_jobs($type){

	$this->model->verify_session();

	$sql = 'Select * from Jobs_Exp where ID_compania="'.$this->model->id_compania.'" and IsActive="'.$type.'" order by JobID asc';

	$jobs = $this->model->Query($sql);



$table = '<script type="text/javascript">

 jQuery(document).ready(function($)

  {

   var table = $("#table_job").dataTable({
   rowReorder: {
            selector: "td:nth-child(2)"
        },

      responsive: true,
      pageLength: 50,
      dom: "Bfrtip",
      bSort: false,
      select: false,

      info: false,
        buttons: [

          {

          extend: "excelHtml5",

          text: "Exportar",

          title: "Lista",

           
          exportOptions: {

                columns: ":visible",

                 format: {
                    header: function ( data ) {

                      var StrPos = data.indexOf("<div");

                        if (StrPos<=0){

                          
                          var ExpDataHeader = data;

                        }else{
                       
                          var ExpDataHeader = data.substr(0, StrPos); 

                        }
                       
                      return ExpDataHeader;
                      }
                    }
                 
                  }
                

          },

          {

          extend:  "colvis",

          text: "Seleccionar",

          columns: ":gt(0)"           

         },

         {

          extend: "colvisGroup",

          text: "Ninguno",

          show: [0],

          hide: [":gt(0)"]

          },

          {

            extend: "colvisGroup",

            text: "Todo",

            show: ["*"]

          }

          ]

   

    });


table.yadcf(
[
{column_number : 0,
 column_data_type: "html",
 html_data_type: "text" 
},
{column_number : 1},
{column_number : 2}
],
{cumulative_filtering: true}); 

});


  </script>


  <table id="table_job"  class="display nowrap table  table-condensed table-striped table-bordered" cellspacing="0" >

    <thead>
      <tr>
        <th width="10%">Job Id</th>
        <th width="20%">Descripcion</th>
        <th width="10%">Activo</th>
        <th width="10%">Accion</th>
        <th width="10%">Modificar</th>
        <th width="10%">Elminar</th>
      </tr>
    </thead>
   <tbody>';


   

  foreach ($jobs as $value) {

  		$act ='';
  		$isActive = '';
  		

      $value = json_decode($value);

      $job_id = "'".$value->{'JobID'}."'";
      $id_table = "'".$value->{'ID'}."'";
      $desc = "'".$value->{'Description'}."'";


      $isActive = $value->{'IsActive'};

      if ($isActive == 1) {
        
        $act = 'Yes';
		$act_deact = 'Desactivar';
        $act_deact_js = 0;
        $color_class = 'btn btn-sm btn-icon icon-left';

      }else{

        $act = 'No';
        $act_deact = 'Activar';
        $act_deact_js = 1;
        $color_class = 'btn btn-success btn-sm btn-icon icon-left';
      }

    
      $table.= '<tr>
            <td width="10%">'.$value->{'JobID'}.'</td>
            <td width="20%">'.$value->{'Description'}.'</td>
            <td width="10%">'.$act.'</td>
            <td width="10%" style="text-align:center"><input type="button" onclick="act_job('.$id_table.','.$act_deact_js.');" id="act_button" name="act_button"  class="'.$color_class.'" value="'.$act_deact.'"/></td>
            <td width="10%" style="text-align:center"><a title="modificar Item" data-toggle="modal" data-target="#jobModal"  href="javascript:void(0)" onclick="set_modal_job('.$id_table.','.$job_id.','.$desc.');"><input type="button" id="modify_button" name="modify_button"  class="btn btn-warning btn-sm btn-icon icon-left" value="Modificar"/></a></td>
            <td width="10%" style="text-align:center"><input type="button" onclick="del_job('.$job_id.');" id="act_button" name="act_button"  class="btn btn-danger btn-sm btn-icon icon-left" value="Elminar"/></td>
          </tr>';
  		}

  	$table .= '</tbody>
			   </table>';

	echo $table;


}


//FUNCION PARA ACTIVAR Y DESACTIVAR LOS JOBS

public function act_jobs($id_table,$action){

	$this->model->verify_session();


	$clause = "ID ='".$id_table."' and ID_compania='".$this->model->id_compania."'";
	$values = array('IsActive' => $action );

	$this->model->update('Jobs_Exp',$values,$clause);

}


//FUNCION PARA MODIFICAR LOS JOBS EXISTENTES

public function modify_job($id_table,$job_id,$desc){

	$this->model->verify_session();


	$clause = "JobID='".$job_id."' and ID_compania='".$this->model->id_compania."'";
	$values = array('Description' => $desc );

	$this->model->update('Jobs_Exp',$values,$clause);


}

//FUNCION PARA BORRAR LOS JOBS

public function del_jobs($id){

	$this->model->verify_session();


	$sql = "DELETE FROM Jobs_Exp WHERE JobID ='".$id."' and ID_compania='".$this->model->id_compania."'";

	$res = $this->model->Query($sql);

}


///////Corchete de la clase/////////
}

?>