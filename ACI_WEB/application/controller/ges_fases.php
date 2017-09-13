<?PHP

class ges_fases extends Controller
{

//SE CARGA LA VISTA DE FASES

public function ges_fase(){
  $res = $this->model->verify_session();

        if($res=='0'){
        

            // load views
            require APP . 'view/_templates/header.php';
            require APP . 'view/_templates/panel.php';
            require APP . 'view/operaciones/ges_phase.php';
            require APP . 'view/_templates/footer.php';


        }
}

//FUNCION DE CREACION DE FASES

public function create_phase($id,$desc){

$this->model->verify_session();

if ($id != '' && $desc != '') {
	
	$val = array('ID_compania' => $this->model->id_compania,
				 'PhaseID' => $id,
				 'Description' => $desc,
				 'IsActive' => 1,
				 'JobCostCodes' => 0);

	$res = $this->model->insert('Job_Phases_Exp',$val);
	echo $res;


		}
}


//FUNCION QUE CARGA LA TABLA DE FASES DE ACUERDO AL ESTATUS ACTIVO E INACTIVO

public function load_phases($type){

	$this->model->verify_session();

	$sql = 'Select * from Job_Phases_Exp where ID_compania="'.$this->model->id_compania.'" and IsActive="'.$type.'" order by PhaseID asc';

	$jobs = $this->model->Query($sql);



$table = '<script type="text/javascript">

 jQuery(document).ready(function($)

  {

   var table = $("#table_phase").dataTable({
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


  <table id="table_phase"  class="display nowrap table  table-condensed table-striped table-bordered" cellspacing="0" >

    <thead>
      <tr>
        <th width="10%">Fase Id</th>
        <th width="20%">Descripcion</th>
        <th width="10%">Activo</th>
        <th width="5%">Accion</th>
        <th width="5%">Modificar</th>
        <th width="5%">Elminar</th>
      </tr>
    </thead>
   <tbody>';


   

  foreach ($jobs as $value) {

  		$act ='';
  		$isActive = '';
  		

      $value = json_decode($value);

      $phase_id = "'".$value->{'PhaseID'}."'";
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
            <td width="10%">'.$value->{'PhaseID'}.'</td>
            <td width="20%">'.$value->{'Description'}.'</td>
            <td width="10%" style="text-align:center">'.$act.'</td>
            <td width="5%" style="text-align:center"><input type="button" onclick="act_phase('.$phase_id.','.$act_deact_js.');" id="act_button" name="act_button"  class="'.$color_class.'" value="'.$act_deact.'"/></td>
            <td width="5%" style="text-align:center"><a title="modificar Item" data-toggle="modal" data-target="#PhaseModal"  href="javascript:void(0)" onclick="set_modal_phase('.$phase_id.','.$desc.');"><input type="button" id="modify_button" name="modify_button"  class="btn btn-warning btn-sm btn-icon icon-left" value="Modificar"/></a></td>
            <td width="5%" style="text-align:center"><input type="button" onclick="del_phase('.$phase_id.');" id="act_button" name="act_button"  class="btn btn-danger btn-sm btn-icon icon-left" value="Elminar"/></td>
          </tr>';
  		}

  	$table .= '</tbody>
			         </table>';

	echo $table;


}


//FUNCION PARA ACTIVAR Y DESACTIVAR LOS FASES

public function act_phase($phase_id,$action){

	$this->model->verify_session();


	$clause = "PhaseID ='".$phase_id."' and ID_compania='".$this->model->id_compania."'";
	$values = array('IsActive' => $action );

	$this->model->update('Job_Phases_Exp',$values,$clause);

}


//FUNCION PARA MODIFICAR LOS FASES EXISTENTES

public function modify_phase($job_id,$desc){

	$this->model->verify_session();


	$clause = "PhaseID='".$job_id."' and ID_compania='".$this->model->id_compania."'";
	$values = array('Description' => $desc );

	$this->model->update('Job_Phases_Exp',$values,$clause);


}

//FUNCION PARA BORRAR LOS FASES

public function del_phase($id){

	$this->model->verify_session();


	$sql = "DELETE FROM Job_Phases_Exp WHERE PhaseID ='".$id."' and ID_compania='".$this->model->id_compania."'";

	$res = $this->model->Query($sql);

}


///////Corchete de la clase/////////
}

?>