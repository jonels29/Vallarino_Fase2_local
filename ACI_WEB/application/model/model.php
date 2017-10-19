<?php

error_reporting(1); 


class Model
{
    /**
     * @param object $db A PDO database connection
     */

     public  $active_user_id = null;
     public  $active_user_name  = null;
     public  $active_user_lastname  = null;
     public  $active_user_email  = null;
     public  $active_user_role  = null;
     public  $active_user_almacen  = null;
     public  $id_compania = null;
     public  $role_compras = null;
     public  $role_campo = null;




    function __construct($db,$dbname)
    {
        try {
           
           $this->db = $db;
           $this->dbname = $dbname;

        } catch (mysqli_connect_errno $e) {
            exit('No se pude realizar la conexion a la base de datos');
        }


    }
////////////////////////////////////////////////////////////////////////////////////////
/**
* test connetion BD
*/ 

    public function TestConexion(){

            $Mysql =  $this->db; 


            if (mysqli_connect_errno()) {
             
                $status ="Conexion Fallo: (" . mysqli_connect_errno() . ") " . mysqli_connect_error();

            }else{  

                $status="Conectado a Mysql";

            }

           
            return $status;

            }

    
////////////////////////////////////////////////////////////////////////////////////////
/**
* test connetion BD
*/ 

public function ConexionSage(){

$connected = $this->Query_value('CompanySession','isConnected','order by LAST_CHANGE DESC limit 1');


            

            if ($connected==0) {
             
                $status ="<img width='15px' src='img/Stop.png' /> No conectado a Sage";

            }else{  

                $status ="<img width='15px' src='img/Check.png' /> Conectado a ".$this->Query_value('CompanySession','CompanyNameSage50','order by LAST_CHANGE DESC  limit 1');

            }

          
            return $status;

            }


////////////////////////////////////////////////////////////////////////////////////////
/**
* test connetion BD 2
*/ 

public function ConSage(){


$con = $this->Query_value(' CompanySession ',' isConnected ',' order by LAST_CHANGE DESC limit 1 ');


 
        if ($con == 0 ) {
             
                $stat='0';

            }else{  

                $stat='1';

            }

          
            return $stat; 

}


////////////////////////////////////////////////////////////////////////////////////////
    /**
     * CONNECTION DB
     */
    public function connect($query){

      mysqli_set_charset($this->db, 'utf8' );
      
     
      $conn =  mysqli_query($this->db,$query);


      return $conn;
    }
////////////////////////////////////////////////////////////////////////////////////////
    /**
     * Query STATEMEN, DEVUELVE JSON
     */
        public function Query($query){
        //$this->verify_session();
            
        $ERROR = '';
       
        $i=0;

         $res = $this->connect($query);

        if($res=='0'){
         
          $ERROR['ERROR'] = date("Y-m-d h:i:sa").','.str_replace("'", " ", mysqli_error($this->db));

          file_put_contents("LOG_ERROR/TEMP_LOG.json",json_encode($ERROR),FILE_APPEND);

          file_put_contents("LOG_ERROR/ERROR_LOG.log",'/SAGEID-'.$this->id_compania.'/'.date("Y-m-d h:i:sa").'/'.$this->active_user_name.''.$this->active_user_lastname.'/'.mysqli_error($this->db).'/'.$query."\n",FILE_APPEND);

     //     die('<script>$(window).load(function(){ MSG_ERROR("'.mysqli_error($this->db).'",0); });</script>');

          
        }else{
             file_put_contents("LOG_ERROR/TEMP_LOG.json",''); //LIMPIO EL ARCHIVO

             $columns = mysqli_fetch_fields($res);
         

        
             while ($datos=  mysqli_fetch_assoc($res)) {
                 
                  foreach ($columns as $value) {
                    $currentField=$value->name;

                    $FIELD[$currentField]=$datos[$currentField];

                    $JSON[$i]=json_encode($FIELD);

                   
                 }
                 $i++;
               } 
               
      

        return  $JSON;


        }

        
$this->close();
}
////////////////////////////////////////////////////////////////////////////////////////
    /**
     * UPDATE STATEMEN
     */
    public function update($table,$columns,$clause){


    $whereSQL = '';
    if(!empty($clause))
    {
       
        if(substr(strtoupper(trim($clause)), 0, 5) != 'WHERE')
        {
           
            $whereSQL = " WHERE ".$clause;
        } else
        {
            $whereSQL = " ".trim($$clause);
        }
    }
    
    $query = "UPDATE ".$table." SET ";
   
    $sets = array();
    foreach($columns as $column => $value)
    {
         $sets[] = "`".$column."` = '".$value."'";
    }
    $query .= implode(', ', $sets);
    
    $query .= $whereSQL;

    
    $res = $this->Query($query);


    $this->close();
    return $res;

    }
////////////////////////////////////////////////////////////////////////////////////////
    /**
     * QUERY QUE DEVUELVE UN SOLO VALOR CONSULTADO
     */

function Query_value($table,$columns,$clause){

$query = 'SELECT '.$columns.' FROM '.$table.' '.$clause.';';

$res= $this->connect($query);
$columns= mysqli_fetch_fields($res);


     while ($datos=mysqli_fetch_assoc($res)) {
         
          foreach ($columns as $value) {
           
            $currentField=$value->name;

            $column_value=$datos[$currentField];

 
         }

       } 


return  $column_value;
//$this->close();


}
////////////////////////////////////////////////////////////////////////////////////////

////////////////////////////////////////////////////////////////////////////////////////
    /**
     * INSERT
     */

public function insert($table,$values){


$fields = array_keys($values);

$query= "INSERT INTO ".$table." (`".implode('`,`', $fields)."`) VALUES ('".implode("','", $values)."');";



$this->Query($query);


}

////////////////////////////////////////////////////////////////////////////////////////
    /**
     * CIERRA LA CONEXION DE BD
     */
    public function close(){

    return mysqli_close($this->db);

    }
////////////////////////////////////////////////////////////////////////////////////////



////////////////////////////////////////////////////////////////////////////////////////
//METODOS PARA GESTION DE LOGIN
////////////////////////////////////////////////////////////////////////////////////////
public function login_in($user,$pass,$temp_url){



$res = $this->Query("SELECT * FROM SAX_USER WHERE email='".$user."' AND pass='".$pass."' AND onoff='1';");

foreach ($res as $value) {

    $value = json_decode($value);

    $email= $value->{'email'};
    $id= $value->{'id'};
    $name= $value->{'name'};
    $lastname= $value->{'lastname'};
    $role=$value->{'role'};
    $pass=$value->{'pass'};

    $rol_compras=$value->{'role_purc'};
    $rol_campo  =$value->{'role_fiel'};
}


if($email==''){

 echo "<script> alert('Usuario o Password no son correctos.');</script>";
 

}else{


$columns= array('last_login' => $timestamp = date('Y-m-d G:i:s'));

$this->update('SAX_USER',$columns,'id='.$id);

session_start();


$_SESSION['ID_USER'] = $id;
$_SESSION['NAME'] = $name;
$_SESSION['LASTNAME'] = $lastname;
$_SESSION['EMAIL'] = $email;
$_SESSION['ROLE'] = $role;
$_SESSION['PASS'] = $pass;
$_SESSION['ALMACEN'] = $almacen;
$_SESSION['ROLE1'] = $rol_compras;
$_SESSION['ROLE2'] = $rol_campo;

/*$print_db_st = '';

$check_sage = $this->ConSage(); 

if($check_sage=='0'){

 $print_db_st = " alert('SageConnect no se encuentra activo o no esta debidamente conectado al sistema.'); ";

}*/

if($temp_url!=''){

$url = str_replace('@',  '/', $temp_url);

echo '<script> '.$print_db_st.' self.location="'.URL.'index.php?url='.$url.'"; </script>';


}else{

echo '<script> '.$print_db_st.'  self.location="'.URL.'index.php?url=home/index"; </script>';
   
}


} 
}


public function verify_session(){

        session_start();

       // session_destroy();

        if(!$_SESSION){

         
        //echo "<script>alert('Usuario no auntenticado');</script>";/"'.$_GET['url'].'"

        $temp_url = str_replace('/', '@', $_GET['url']);
  
        $res = '1';
        echo '<script>self.location ="index.php?url=login/index/'.$temp_url.'";</script>';

        
        }else{
   
        $res = '0';

        $this->set_login_parameters();
       }

     return $res;
    }

public function set_login_parameters(){

        $this->active_user_id = $_SESSION['ID_USER'];
        $this->active_user_name = $_SESSION['NAME'];
        $this->active_user_lastname = $_SESSION['LASTNAME'];
        $this->active_user_email = $_SESSION['EMAIL'];
        $this->active_user_role = $_SESSION['ROLE'] ;
        $this->active_user_almacen = $_SESSION['ALMACEN'];
        $this->id_compania = $this->Query_value('CompanySession','ID_compania','ORDER BY LAST_CHANGE DESC LIMIT 1');
        //$active_user_pass = $_SESSION['PASS'] ;
        $this->rol_compras = $_SESSION['ROLE1'];
        $this->rol_campo   = $_SESSION['ROLE2'];
        
    }


////////////////////////////////////////////////////////////////////////////////////////


////////////////////////////////////////////////////////////////////////////////////////
//METODOS PARA GESTION DE OPERACIONES
////////////////////////////////////////////////////////////////////////////////////////
public function Get_lote_list($itemid){

$query='SELECT
Prod_Lotes.no_lote, 
Prod_Lotes.fecha_ven, 
(select sum(qty) from status_location where status_location.lote = Prod_Lotes.no_lote 
and Prod_Lotes.ID_compania ="'.$this->id_compania.'" 
and status_location.ID_compania ="'.$this->id_compania.'") as lote_qty
from Prod_Lotes
where Prod_Lotes.ProductID ="'.$itemid.'" ;';

$list = $this->Query($query);

return $list;


}


public function fact_compras_list(){


$query='SELECT
Purchase_Header_Exp.PurchaseID, 
Purchase_Header_Exp.PurchaseNumber, 
Purchase_Header_Exp.VendorName, 
Purchase_Header_Exp.Date as fecha
from Purchase_Header_Exp
INNER join  Purchase_Detail_Exp on Purchase_Detail_Exp.PurchaseID = Purchase_Header_Exp.PurchaseID
WHERE Purchase_Detail_Exp.Item_id <> " " GROUP BY Purchase_Header_Exp.PurchaseID ';

$list = $this->Query($query);

return $list;
}


public function lote_loc_by_itemID($itemid){

$query ='SELECT * 
FROM status_location
INNER JOIN Prod_Lotes ON Prod_Lotes.no_lote = status_location.lote
WHERE Prod_Lotes.ProductID="'.$itemid.'"  GROUP BY status_location.ID';

$res = $this->Query($query);

return $res;
}

public function get_Purchaseitem($itemid){

$query ='SELECT
Products_Exp.ProductID,
Products_Exp.Description,
Products_Exp.QtyOnHand,
Products_Exp.UnitMeasure,
Products_Exp.Price1,
Products_Exp.id_compania
from Products_Exp
inner join Prod_Lotes on Prod_Lotes.ProductID=Products_Exp.ProductID
where  Products_Exp.ProductID="'.$itemid.'" ;';



$res = $this->Query($query);

return $res;
}

public function get_ProductsList(){


$query='SELECT 
Products_Exp.ProductID,
Products_Exp.Description,
Products_Exp.UnitMeasure,
Products_Exp.QtyOnHand,
Products_Exp.Price1,
Products_Exp.LastUnitCost
FROM Products_Exp 
inner join Prod_Lotes on Prod_Lotes.ProductID=Products_Exp.ProductID
WHERE Products_Exp.IsActive="1" AND  Products_Exp.QtyOnHand > 0 and Products_Exp.id_compania="'.$this->id_compania.'" and Prod_Lotes.ID_compania="'.$this->id_compania.'" group by Products_Exp.ProductID';


$res = $this->Query($query);

return $res;

}

public function get_ClientList(){

$query='SELECT * FROM Customers_Exp where  id_compania="'.$this->id_compania.'"';

$res = $this->Query($query);

return $res;

}

public function Get_SO_No(){

$order = $this->Query_value('SalesOrder_Header_Imp','SalesOrderNumber','where ID_compania="'.$this->id_compania.'" ORDER BY ID DESC LIMIT 1');

list($ACI , $NO_ORDER) = explode('-', $order);


$NO_ORDER = number_format((int)$NO_ORDER+1);
//$NO_ORDER = str_pad($NO_ORDER, 7 ,"0",STR_PAD_LEFT);

$NO_ORDER = 'ACI-'.$NO_ORDER;

if($NO_ORDER< '1'){

    $NO_ORDER=0;
    $NO_ORDER = 'ACI-'.$NO_ORDER;
   // $NO_ORDER = str_pad($NO_ORDER, 7 ,"0",STR_PAD_LEFT);

}



return $NO_ORDER; 
}


public function Get_Order_No(){

$order = $this->Query_value('Sales_Header_Imp','InvoiceNumber','where ID_compania="'.$this->id_compania.'" order by InvoiceNumber DESC LIMIT 1');

$NO_ORDER = number_format((int)$order+1);
$NO_ORDER = str_pad($NO_ORDER, 7 ,"0",STR_PAD_LEFT);


if($NO_ORDER< '1'){

    $NO_ORDER=0;
    $NO_ORDER = str_pad($NO_ORDER, 7 ,"0",STR_PAD_LEFT);

}


return $NO_ORDER; 
}



public function Get_Ref_No(){


$order = $this->Query_value('InventoryAdjust_Imp','Reference','where ID_compania="'.$this->id_compania.'" order by Reference DESC LIMIT 1');

$NO_ORDER = number_format((int)$order+1);
$NO_REF = str_pad($NO_ORDER, 7 ,"0",STR_PAD_LEFT);


if($NO_REF < '1'){

    $NO_REF=0;
    $NO_REF = str_pad($NO_REF, 7 ,"0",STR_PAD_LEFT);

}


return $NO_REF; 
}


public function Get_con_No(){


$order = $this->Query_value('CON_HEADER','refReg','where ID_compania="'.$this->id_compania.'" order by refReg DESC LIMIT 1');

$NO_ORDER = number_format((int)$order+1);
$NO_REF = str_pad($NO_ORDER, 7 ,"0",STR_PAD_LEFT);


if($NO_REF < '1'){

    $NO_REF=0;
    $NO_REF = str_pad($NO_REF, 7 ,"0",STR_PAD_LEFT);

}


return $NO_REF; 
}

public function Get_Req_No($jobid){

$order = $this->Query_value('REQ_HEADER','NO_REQ','where NO_REQ like "'.$jobid.'-%" and ID_compania="'.$this->id_compania.'" ORDER BY ID DESC LIMIT 1');

list($ACI , $NO_ORDER) = explode('-', $order);


$NO_ORDER = number_format((int)$NO_ORDER+1);
//$NO_ORDER = str_pad($NO_ORDER, 7 ,"0",STR_PAD_LEFT);

$NO_ORDER = $jobid.'-'.$NO_ORDER;

if($NO_ORDER< '1'){

    $NO_ORDER=0;
    $NO_ORDER =  $jobid.'-'.$NO_ORDER;
   

}


return $NO_ORDER; 
}



public function get_JobList(){

$jobs = $this->Query('Select * from Jobs_Exp where ID_compania="'.$this->id_compania.'" and IsActive="1" order by JobID asc'); 

return $jobs;

}

public function get_phaseList(){

$jobs = $this->Query('Select * from Job_Phases_Exp where ID_compania="'.$this->id_compania.'" and IsActive="1" order by PhaseID asc'); 

return $jobs;

}

public function get_costList(){

$jobs = $this->Query('Select * from Job_Cost_Codes_Exp where ID_compania="'.$this->id_compania.'" and IsActive="1"'); 

return $jobs;

}

public function Get_User_Info($id){

$user = $this->Query('Select * from SAX_USER where id='.$id); 

return $user;

}

public function Get_User_Name($id){

$USER = $this->Get_User_Info($id);
         
         foreach ($USER as $user ){

            $user = json_decode($user);

            $USERNAME = $user->{'name'}.' '.$user->{'lastname'};

         }

return $USERNAME;


}

public function Get_company_Info(){

$Company= $this->Query('Select * from company_info;'); 

return $Company;

}

public function Get_order_to_invoice($id){

$id_compania = $this->id_compania;

$ORDER= $this->Query('SELECT * FROM `SalesOrder_Header_Imp`
inner JOIN `SalesOrder_Detail_Imp` ON SalesOrder_Header_Imp.SalesOrderNumber = SalesOrder_Detail_Imp.SalesOrderNumber
inner JOIN `Customers_Exp` ON SalesOrder_Header_Imp.CustomerID = Customers_Exp.CustomerID
inner JOIN `SAX_USER` ON `SAX_USER`.`id` = SalesOrder_Header_Imp.user where SalesOrder_Header_Imp.SalesOrderNumber="'.$id.'" and 
SalesOrder_Detail_Imp.ID_compania="'.$id_compania.'" and SalesOrder_Header_Imp.ID_compania="'.$id_compania.'"
group by SalesOrder_Detail_Imp.ID order by SalesOrder_Detail_Imp.ID;'); 

return $ORDER;

}

public function Get_sales_to_invoice($id){

$ORDER= $this->Query('SELECT * FROM `Sales_Header_Imp`
inner JOIN `Sales_Detail_Imp` ON Sales_Header_Imp.InvoiceNumber = Sales_Detail_Imp.InvoiceNumber
inner JOIN `Customers_Exp` ON Sales_Header_Imp.CustomerID = Customers_Exp.CustomerID
inner JOIN `SAX_USER` ON `SAX_USER`.`id` = Sales_Header_Imp.user where Sales_Header_Imp.InvoiceNumber="'.$id.'" 
and  SalesOrder_Detail_Imp.ID_compania="'.$id_compania.'" and SalesOrder_Header_Imp.ID_compania="'.$id_compania.'"
group by Sales_Detail_Imp.ID order by Sales_Detail_Imp.ID;'); 

return $ORDER;

}

public function Get_sal_merc_to_invoice($id){

$ORDER= $this->Query("SELECT * FROM InventoryAdjust_Imp where Reference='".$id."'"); 

return $ORDER;

}

public function Get_sales_conf_Info(){

$saleinfo = $this->Query('SELECT * FROM sale_tax;');

return $saleinfo;

}

//ModifGPH
////////////////////////////////////////////////////
//QUERYS PARA REPORTES

public function get_InvXven($sort,$limit,$clause){

     $order = $this->Query('

         SELECT 
         a.name Almacen, 
         u.etiqueta Ubicacion, 
         l.no_lote Lote, 
         p.ProductID Producto, 
         p.Description Descripcion, 
         l.fecha_ven Vencimiento, 
         s.qty Cantidad
        from Products_Exp p
         inner join Prod_Lotes l  on p.ProductID = l.ProductID 
         inner join status_location s on p.ProductID = s.id_product and s.lote = l.no_lote
         inner join ubicaciones u  on s.route = u.id
         inner join almacenes a on u.id_almacen = a.id 

        '.$clause.' order by l.fecha_ven '.$sort.' limit '.$limit.';');



    return $order;

}


public function get_InvXStk($sort,$limit,$clause){

   $sql = 'SELECT 
         a.name Almacen, 
         u.etiqueta Ubicacion, 
         s.lote Lote, 
         p.ProductID Producto, 
         p.LastUnitCost,
         p.Description Descripcion, 
         s.qty Cantidad
        from Products_Exp p
         inner join status_location s on p.ProductID = s.id_product 
         inner join ubicaciones u  on s.route = u.id
         inner join almacenes a on u.id_almacen = a.id '.$clause.' order by a.name '.$sort.' limit '.$limit.';';

     $order = $this->Query($sql);


    return $order;

}


public function get_req_to_report($sort,$limit,$clause){

$sql='SELECT * FROM `REQ_HEADER` 
inner join REQ_DETAIL ON REQ_HEADER.NO_REQ = REQ_DETAIL.NO_REQ
'.$clause.' group by REQ_HEADER.NO_REQ order by ID '.$sort.' limit '.$limit.';';

$get_req = $this->Query($sql);


return $get_req;
}

public function get_req_to_report_urge($sort,$limit,$clause){

$sql='SELECT count(*) as cuenta, job FROM `REQ_HEADER` 
'.$clause.' group by job order by ID '.$sort.' limit '.$limit.';';

$get_req = $this->Query($sql);


return $get_req;
}



public function get_inv_qty_disp($sort,$limit,$clause){

$sql=' SELECT 
p.ProductID, 
p.Description, 
p.QtyOnHand, 
SUM( s.qty )  as LoteQty
FROM Products_Exp p
INNER JOIN status_location s ON s.id_product = p.ProductID AND s.ID_compania = p.id_compania
'.$clause.' GROUP BY p.ProductID order by p.ProductID '.$sort.' limit '.$limit.';';

$get_inv_qty = $this->Query($sql);


return $get_inv_qty;

}

////////////////////////////////////////////////////



////////////////////////////////////////////////////
//Req to print
public function get_req_to_print($id){


$sql='SELECT * FROM `REQ_HEADER` 
inner join REQ_DETAIL ON REQ_HEADER.NO_REQ = REQ_DETAIL.NO_REQ
WHERE 
REQ_HEADER.ID_compania="'.$this->id_compania.'" AND  
REQ_DETAIL.ID_compania="'.$this->id_compania.'" and 
REQ_HEADER.NO_REQ="'.$id.'" and 
REQ_DETAIL.NO_REQ="'.$id.'"
ORDER BY  `REQ_DETAIL`.`ITEM_UNIQUE_NO` ASC' ;

$req_info = $this->Query($sql);

return $req_info ;
}


////////////////////////////////////////////////////
//Orden de compras por id
public function get_items_by_OC($invoice){

$query ='SELECT * 
FROM PurOrdr_Header_Exp
INNER JOIN PurOrdr_Detail_Exp ON PurOrdr_Header_Exp.TransactionID = PurOrdr_Detail_Exp.TransactionID
WHERE PurOrdr_Header_Exp.ID_compania="'.$this->id_compania.'"
AND PurOrdr_Header_Exp.PurchaseOrderNumber ="'.$invoice.'"';

$res = $this->Query($query);


return $res;
}

//Orden de compras total
public function get_OC($sort,$limit,$clause){

$query ='SELECT * 
FROM PurOrdr_Header_Exp
INNER JOIN PurOrdr_Detail_Exp ON PurOrdr_Header_Exp.TransactionID = PurOrdr_Detail_Exp.TransactionID
'.$clause.' 
group by PurOrdr_Header_Exp.TransactionID 
Order by PurOrdr_Header_Exp.Date '.$sort.' limit '.$limit.';';



$res = $this->Query($query);


return $res;
}

////////////////////////////////////////////////////



////////////////////////////////////////////////////
//Consignacion

public function con_reg($refReg,$cont,$ID_compania){

$idReg = $this->Query_value('CON_HEADER','idReg','WHERE refReg = "'.$refReg.'" and ID_compania="'.$ID_compania.'";');

$regTra = $this->Query('SELECT id from reg_traslado where ID_compania="'.$ID_compania.'" ORDER BY LAST_CHANGE desc limit '.$cont.';');

   foreach ($regTra as $value) {
  
   $value = json_decode($value);

   $ID_REG_TRAS = $value->{'id'};

    $this->Query('INSERT INTO CON_REG_TRAS (idReg,idRegTras,ID_compania) values ("'.$idReg.'","'.$ID_REG_TRAS.'","'.$ID_compania.'");');

    }

}



public function get_con_to_report($sort,$limit,$clause){

$sql='SELECT      
                  CON_HEADER.date,
                  CON_HEADER.refReg as REF,
                  CON_HEADER.idJob  as JOB,
                  CON_HEADER.idPha as  PHASE,
                  CON_HEADER.idCost as COST,
                  CON_HEADER.nota as NOTA,
                  reg_traslado.id_almacen_ini,
                  reg_traslado.route_ini,
                  reg_traslado.id_almacen_des,
                  reg_traslado.route_des,
                  reg_traslado.id_user as USER,
                  reg_traslado.lote as LOTE,
                  reg_traslado.ProductID,
                  reg_traslado.qty as CANT
                  FROM CON_HEADER 
                  INNER JOIN CON_REG_TRAS ON CON_REG_TRAS.idReg = CON_HEADER.idReg 
                  INNER JOIN reg_traslado ON CON_REG_TRAS.idRegTras = reg_traslado.id 
                  '.$clause.' order by CON_HEADER.idReg '.$sort.' limit '.$limit.';';

$get_con = $this->Query($sql);


return $get_con;
}
////////////////////////////////////////////////////

public function read_db_error(){


    $string = file_get_contents("LOG_ERROR/TEMP_LOG.json");
    $json_a = json_decode($string, true);   
    $R_ERRORS = '';
    $R_ERRORS .= $json_a['ERROR']; 



    file_put_contents("LOG_ERROR/TEMP_LOG.json",''); //LIMPIO EL ARCHIVO

   $R_ERRORS = str_replace(',', '  ', $R_ERRORS);
   echo $R_ERRORS;
   return $R_ERRORS ;

}


public function send_mail($address,$subject,$title,$body){

$res = $this->verify_session();


echo $message_to_send ='<html>
<head>
<meta charset="UTF-8">
<title>'.$title.'</title>
</head>
<body>'.$body.'</body>
</html>';


require 'PHP_mailer/PHPMailerAutoload.php';
      
    $mail = new PHPMailer;

    $mail->IsSMTP(); // enable SMTP
    $mail->IsHTML(true);


$sql = "SELECT * FROM CONF_SMTP WHERE ID='1'";

$smtp= $this->Query($sql);

    foreach ($smtp as $smtp_val) {
        $smtp_val= json_decode($smtp_val);

        $mail->Host =     $smtp_val->{'HOSTNAME'};
        $mail->Port =     $smtp_val->{'PORT'};
        $mail->Username = $smtp_val->{'USERNAME'};
        $mail->Password = $smtp_val->{'PASSWORD'};
        $mail->SMTPAuth = $smtp_val->{'Auth'};
        $mail->SMTPSecure=$smtp_val->{'SMTPSecure'};
        $mail->SMTPDebug= $smtp_val->{'SMTPSDebug'};

        $mail->SetFrom($smtp_val->{'USERNAME'});
        $mail->SingleTo = true;

    }

    $mail->Body = $message_to_send;
    $mail->Subject = utf8_decode($subject);
    //$mail->AddAddress($email,$name.' '.$lastname);

    foreach ($address as $value) {


        list($email,$name,$lastname) = explode(';', $value);

        $mail->AddAddress($email, $name.' '.$lastname);

    }


if(!$mail->send()) {
 

   $alert .= 'Message could not be sent.';
   $alert .= 'Mailer Error: ' . $mail->ErrorInfo;


} else {

  ECHO '1';

}
}
//CORCHETE DE FIN DE LA CLASE
}
?>

