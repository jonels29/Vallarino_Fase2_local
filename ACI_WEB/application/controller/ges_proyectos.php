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



public function list_job_modal($id){

$this->model->verify_session();

  $sql = 'SELECT * FROM Jobs_Exp where ID_compania="'.$this->model->id_compania.'" and IsActive="1" order by JobID asc';

  $jobs = $this->model->Query($sql);

  $sql_user = 'SELECT * FROM JOBS_USERS WHERE USER_ID ="'.$id.'" and ID_compania="'.$this->model->id_compania.'"';

  $jobs_user = $this->model->Query($sql_user);


$table = '<script type="text/javascript">

 jQuery(document).ready(function($)

  {

   var table = $("#table_job").dataTable({
   rowReorder: {
            selector: "td:nth-child(2)"
        },
      paging: false,
      responsive: true,
      pageLength: 5,
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
{column_number : 1}
],
{cumulative_filtering: true}); 

});


  </script>


  <table id="table_job"  class="display nowrap table  table-condensed table-striped table-bordered" cellspacing="0" >

    <thead>
      <tr>
        <th width="1%"></th>
        <th width="3%">Job Id</th>
        <th width="20%">Descripcion</th>
      </tr>
    </thead>
   <tbody>';

   $i = 1;

    foreach ($jobs as $value) {


        $value = json_decode($value);


        $check = '';
        $job_id = $value->{'JobID'};


            foreach ($jobs_user as $value_user) {


            $value_user = json_decode($value_user);

            $user_job_id = $value_user->{'JOB_ID'};

              if ($job_id == $user_job_id) {

                $check = 'checked="checked"';
                
              }


            }


          $table.= '<tr>
            <td width="1%" ><CENTER><input type="checkbox" name="'.$i.'" id="'.$i.'" value="'.$job_id.'" '.$check.' ></CENTER></td>
            <td width="3%" style="text-align:center">'.$value->{'JobID'}.'</td>
            <td width="20%">'.$value->{'Description'}.'</td>
          </tr>';

          $i++;

    }

    $table .= '</tbody>
         </table>';

  echo $table;

}

public function clear_assigment($userid){

$this->model->verify_session();

$del_sql = 'DELETE FROM JOBS_USERS WHERE ID_compania ="'.$this->model->id_compania.'" and USER_ID="'.$userid.'";';
$res = $this->model->Query($del_sql);

return 1;
}

public function set_assigment($userid){

$this->model->verify_session();

$del = $this->clear_assigment($userid);

if($del == 1){

$data = json_decode($_GET['Data']);



  foreach ($data as $key => $value) {
   

    if($value){

      list($jobid,$desc,$userid) = explode('@', $value );

        $values1 = array(
            'ID_compania' => $this->model->id_compania,
            'JOB_ID'=>$jobid,
            'DESCRIPTION'=>$desc,
            'USER_ID'=>$userid
             );


      $this->model->insert('JOBS_USERS',$values1);
      $this->CheckError();

    }
  }

  }

}


public function CheckError(){


  $CHK_ERROR =  $this->model->read_db_error();


  if ($CHK_ERROR!=''){ 

   
    die( "<script>  $(window).on('load', function () {   
                           $('#ErrorModal').modal('show');
                           $('#ErrorMsg').html('".$CHK_ERROR."');
                         }); 
          </script>");

  }

}

///////Corchete de la clase/////////
}

?>