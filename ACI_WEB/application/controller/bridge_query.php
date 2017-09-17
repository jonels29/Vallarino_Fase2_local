<?php



class bridge_query extends Controller
{



public function SESSION(){

  $this->model->verify_session();


}

public function DEL_PO_TABLE($mode){

if($mode==1){

$SQL='DELETE FROM PurOrdr_Detail_Exp;';

$this->model->Query($SQL);

$SQL='DELETE FROM PurOrdr_Header_Exp;';

$this->model->Query($SQL);

}else{


$lastmoth = date("Y-m-d",strtotime("-1 month"));
$thismonth = date("Y-m-d");

$SQL = 'SELECT TransactionID 
          FROM PurOrdr_Header_Exp 
          WHERE Date  >= "'.$lastmoth.' 00:00:00" and Date <= "'.$thismonth.' 23:59:59" 
            and ID_compania="'.$this->model->id_compania.'" ;';

$tid = $this->model->Query($SQL);

foreach ($tid  as $value){

  $value = json_decode($value);


  $this->model->Query('DELETE FROM PurOrdr_Detail_Exp WHERE TransactionID="'.$value->{'TransactionID'}.'" and ID_compania="'.$this->model->id_compania.'";');

}



$SQL='DELETE FROM PurOrdr_Header_Exp WHERE Date  >= "'.$lastmoth.' 00:00:00" and Date <= "'.$thismonth.' 23:59:59" ;';

$this->model->Query($SQL);



}


}


public function get_items_by_invoice($invoice){


$query = 'SELECT
  Purchase_Header_Exp.PurchaseID, 
  Purchase_Header_Exp.PurchaseNumber, 
  Purchase_Header_Exp.VendorID,
  Products_Exp.ProductID,
  Products_Exp.Description,
  Products_Exp.UnitMeasure,
  Purchase_Detail_Exp.Quantity
from Purchase_Header_Exp
inner join  Purchase_Detail_Exp on Purchase_Header_Exp.PurchaseID = Purchase_Detail_Exp.PurchaseID
inner join  Products_Exp on Products_Exp.ProductID = Purchase_Detail_Exp.Item_id
 where isActive="1" and Purchase_Header_Exp.PurchaseID="'.$invoice.'"';


$lote_ven = $this->model->Query($query);

foreach ($lote_ven as $value) {

  $PROD_FACT= json_decode($value);
  
                      
  echo '<tr>
          <td >'.$PROD_FACT->{'ProductID'}.'</td>
          <td >'.$PROD_FACT->{'Description'}.'</td>
          <td class="numb" >'.number_format($PROD_FACT->{'Quantity'},0, ',', '.').'</td>
          <td ><a href="'.URL.'index.php?url=ges_inventario/inv_info/'.$PROD_FACT->{'ProductID'}.'" >Ver <i class="fa  fa-search"></i></a></td>
        </tr>';

  }


}

public function get_ProductsInfo($itemid){
$this->SESSION();

$sql= 'SELECT 
Products_Exp.ProductID,
Products_Exp.Description,
Products_Exp.UnitMeasure,
Products_Exp.QtyOnHand,
Products_Exp.Price1,
Products_Exp.LastUnitCost
FROM Products_Exp 
WHERE 
Products_Exp.IsActive="1" 
AND  Products_Exp.id_compania="'.$this->model->id_compania.'" 
AND  Products_Exp.ProductID ="'.$itemid.'" ';

$res = $this->model->Query($sql);


foreach ($res as  $value) {
    echo $value;
}
}

public function get_lote_selectlist($itemid){
$this->SESSION();


$query_lotes = 'SELECT lote 
FROM status_location 
where id_product="'.$itemid.'" and stock="1" and route="1" and onoff="1" and qty > 0 and ID_compania ="'.$this->model->id_compania.'"';

$query_almacen= 'SELECT almacenes.id, almacenes.name  
FROM almacenes 
inner join ubicaciones on ubicaciones.id_almacen = almacenes.id
where almacenes.onoff="1" GROUP BY almacenes.name ';

$url = "'".URL."'";
$itemid =  "'".$itemid."'";

$table='<fieldset><legend><h4>Agregar lote a nueva ubicacion</h4></legend>
<table class="dataTable">
<tr>
<th>No. Lote</th>
<th>Cantidad</th>
<th>Almacen</th>
<th>Ruta</th></tr>
<tr><td><select id="no_lote" class="form-control col-xs-4" onchange="set_qty('.$url.','.$itemid.',this.value);" >
<option selected disabled>Seleccionar lote</option>';
$res = $this->model->Query($query_lotes);

foreach ($res as $value) {
  $value = json_decode($value);
  $table.='<option value="'.$value->{'lote'}.'">'.$value->{'lote'}.'</option>';
  }

$table.='</select></td>
<td><input class="form-control col-xs-4" type="number" id="qty_new" name="qty_new" min="1" max="" required readonly="true"/></td>
<td><select onchange="routes(this.value);" class="form-control col-xs-4" id="almacen" name="almacen" readonly="true">
<option selected disabled>Seleccionar Almacen</option>';
$res = $this->model->Query($query_almacen);

foreach ($res as $value) {
  $value = json_decode($value);
  $table.='<option value="'.$value->{'id'}.'">'.$value->{'name'}.'</option>';
  }

$table.='</select></td>
<td><select class="form-control col-xs-4" id="routes" name="routes" readonly="true"></select></td>
<td><button onclick="add_location_route();"  class="btn btn-primary  btn-block text-left" type="submit" >Ubicar</button></td>
<td><button class="btn btn-warning btn-block text-left" onclick="javascript: location.reload();" >cancelar</button></td></tr>
</table>
</fieldset>';

echo $table;
}


public function get_lote_qty($lote,$itemid){


$this->SESSION();


$res = $this->model->Query_value('status_location','Floor(qty)','where stock="1" and route="1" and lote="'.$lote.'" and id_product="'.$itemid.'" and ID_compania ="'.$this->model->id_compania.'";');

echo $res;
return $res;
}


public function get_any_lote_qty($lote,$itemid,$ruta){


$this->SESSION();


$res = $this->model->Query_value('status_location','Floor(qty)','where route="'.$ruta.'" and lote="'.$lote.'" and id_product="'.$itemid.'" and ID_compania ="'.$this->model->id_compania.'";');

//echo $res;
return $res;
}

public function  get_almacen_selectlist(){



$query_almacen= 'SELECT almacenes.id, almacenes.name  
FROM almacenes 
inner join ubicaciones on ubicaciones.id_almacen = almacenes.id
where almacenes.onoff="1" GROUP BY almacenes.name ';

$select = '<option selected disabled>Seleccionar Almacen</option>';
$res = $this->model->Query($query_almacen);

foreach ($res as $value) {
  $value = json_decode($value);
  $select.='<option value="'.$value->{'id'}.'">'.$value->{'name'}.'</option>';
  }

$select.='</select>';
echo $select;

return $select;
}


public function  get_routes_by_almacenid($almacen){
$query= 'SELECT id , etiqueta  FROM ubicaciones where id_almacen="'.$almacen.'" and onoff="1"';

$res = $this->model->Query($query);

echo '<option selected disabled>Seleccionar Ruta</option>';
foreach ($res as $value) {
  $value = json_decode($value);
  echo '<option value="'.$value->{'id'}.'">'.$value->{'etiqueta'}.'</option>';
  }
}


public function set_lote_location($ruta_selected,$almacen_selected,$item_id,$lote,$qty){
$this->SESSION();

//UBICO LA CANTIDAD ACTUAL EN STATUS_LOCATION
$CURRENT_QTY = $this->get_lote_qty($lote,$item_id);



//ACTUALIZO LA CANTIDAD EN LA UBICCION DEFAULT
$QTY_TO_SET = $CURRENT_QTY - $qty;

$query= 'UPDATE status_location SET qty="'.$QTY_TO_SET.'" where id_product="'.$item_id.'" and stock="1" and route="1" and onoff="1" and ID_compania ="'.$this->model->id_compania.'"';

$res = $this->model->Query($query);



$id_verify = $this->model->Query_value('status_location','id','where lote="'.$lote.'" and stock="'.$almacen_selected.'" and route="'.$ruta_selected.'" and ID_compania ="'.$this->model->id_compania.'";');

if(!$id_verify){ 

//agregar nueva location
$val_to_insert = array(
  'lote' => $lote, 
  'stock' => $almacen_selected, 
  'qty' => $qty, 
  'route' => $ruta_selected,
  'id_product' => $item_id,
  'ID_compania' => $this->model->id_compania);

$res = $this->model->insert('status_location',$val_to_insert);


//registro de traslado Default a una nueva ubicacion
$value_traslate = array(
  'id_almacen_ini' => '1',
  'route_ini' => '1' ,
  'id_almacen_des' => $almacen_selected,
  'route_des' => $ruta_selected,
  'lote' => $lote,
  'qty' => $qty ,
  'ProductID' => $item_id ,
  'id_user' => $this->model->active_user_id
   );

$this->model->insert('reg_traslado',$value_traslate);


}else{

$old_qty = $this->model->Query_value('status_location','qty','where id="'.$id_verify.'";');

$qty_to_up = $old_qty  + $qty;

$query= 'UPDATE status_location SET qty="'.$qty_to_up.'"  where id="'.$id_verify.'";';
$this->model->Query($query);


//registro de traslado Default a una nueva ubicacion
$id_route_reg = $this->model->Query_value('status_location','route','where id="'.$id_verify.'";');
$id_alma_reg = $this->model->Query_value('ubicaciones','id_almacen','where id="'.$id_route_reg.'";');


$value_traslate = array(
  'id_almacen_ini' => $id_alma_reg,
  'route_ini' => $id_route_reg,
  'id_almacen_des' => $almacen_selected,
  'route_des' => $ruta_selected,
  'lote' => $lote,
  'qty' => $qty ,
  'ProductID' => $item_id ,
  'id_user' => $this->model->active_user_id
   );

$this->model->insert('reg_traslado',$value_traslate);



}

//$this->clear_lotacion_register();

}


public function update_lote_location($OrigenROUTE,$OrigenALMACEN,$status_location_id,$ruta,$almacen,$lote,$qty){

$this->SESSION();

//ID DE UBICACIONES DE ORIGEN
$ruta_src = $this->model->Query_value('ubicaciones','id',' where etiqueta="'.$OrigenROUTE.'"');
$almacen_src = $this->model->Query_value('ubicaciones','id_almacen',' where id="'.$ruta_src.'"');

//ID DEL USER QUE REALIZA EL TRASLADO
$id_user_active = $this->model->active_user_id;

//QTY ACTUAL
$CURRENT_QTY = $this->model->Query_value('status_location','qty','where id="'.$status_location_id.'";');

$NEW_QTY = $CURRENT_QTY - $qty;

//ACTUALIZA LA CANTIDAD RESTANTE EN LA UBICACION ACTUAL
$query= 'UPDATE status_location SET qty="'.$NEW_QTY.'" where id="'.$status_location_id.'";';
$this->model->Query($query);


//ID DEL PRODUCTO
$ProductID = $this->model->Query_value('status_location','id_product','where id="'.$status_location_id.'";');


  //VERIFICO SI EXISTE UN LOTE IGUAL EN LA UBICACION DESTINO
  $id_verify = $this->model->Query_value('status_location','id','where lote="'.$lote.'" and stock="'.$almacen.'" and route="'.$ruta.'" and  ID_compania ="'.$this->model->id_compania.'"');

  if(!$id_verify){ //SI NO EXISTE CREO LA NUEVA UBICACION PARA EL LOTE 

  //agregar nueva location
  $val_to_insert = array(
    'lote' => $lote, 
    'stock' => $almacen, 
    'qty' => $qty, 
    'route' => $ruta,
    'id_product' => $ProductID,
    'ID_compania' => $this->model->id_compania);

  $res = $this->model->insert('status_location',$val_to_insert);



  //registro de traslado Default a una nueva ubicacion
  $value_traslate = array(
    'id_almacen_ini' => $almacen_src,
    'route_ini' => $ruta_src,
    'id_almacen_des' => $almacen,
    'route_des' => $ruta,
    'lote' => $lote,
    'qty' => $qty ,
    'ProductID' => $ProductID,
    'id_user' => $this->model->active_user_id,
    'ID_compania' => $this->model->id_compania
     );

  $this->model->insert('reg_traslado',$value_traslate);


}else{//SI EXISTE LE SUMO LA NUEVA CANTIDAD

    //consulta qty actual en lla ubicacion destino apra ese lote
    $old_qty = $this->model->Query_value('status_location','qty','where id="'.$id_verify.'";');

    $qty_to_up = $old_qty  + $qty; //se suma

    //se actualiza
    $query= 'UPDATE status_location SET qty="'.$qty_to_up.'"  where id="'.$id_verify.'";';
    $this->model->Query($query);


    //registro de traslado
    $value_traslate = array(
      'id_almacen_ini' => $almacen_src,
      'route_ini' => $ruta_src ,
      'id_almacen_des' => $almacen,
      'route_des' => $ruta,
      'lote' => $lote,
      'qty' => $qty ,
      'ProductID' => $ProductID,
      'id_user' => $this->model->active_user_id,
      'ID_compania' => $this->model->id_compania
       );


    $this->model->insert('reg_traslado',$value_traslate);

    }


}

/*

public function clear_lotacion_register(){

$query= 'select status_location.id from status_location 
inner join sale_pendding on sale_pendding.status_location_id = status_location.id 
where sale_pendding.status_pendding ="0"';

$id = $this->model->Query($query);

foreach ($id as  $value) {
  $value  = json_decode($value);
 $id =  $value ->{'id'};
}

$query = 'delete from status_location where id="'.$id.'";';
$this->model->Query($query);

}*/

//modificacion rey 23/11
public function get_items_location_by_lote($itemid){


$this->SESSION();


$query = 'SELECT 
lote ,
id as id_location,
(select name from almacenes where almacenes.id=status_location.stock) as stock,
(select etiqueta from ubicaciones where ubicaciones.id=status_location.route) as route,
(select qty from Prod_Lotes where Prod_Lotes.no_lote=status_location.lote) as qty,
(select fecha_ven from Prod_Lotes where Prod_Lotes.no_lote=status_location.lote) as venc,
(select Price1 from Products_Exp where Products_Exp.ProductID=status_location.id_product) as Price,
(select Description from Products_Exp where Products_Exp.ProductID=status_location.id_product) as Descr,
(select UnitMeasure from Products_Exp where Products_Exp.ProductID=status_location.id_product) as UnitMeasure
FROM status_location 
where id_product="'.$itemid.'" and status_location.ID_compania ="'.$this->model->id_compania.'"';

$res = $this->model->Query($query);


echo '<script type="text/javascript">
    $("#modal_table").dataTable({
      
      pageLength: 5
              
            });
</script>

<table id="modal_table" widht="100%" class="table table-striped"  cellspacing="0">
            <thead>
              <tr>  
                
                <th width="20%" >Almacen</th>
                <th width="20%" >Ruta</th>
                <th width="25%" >Lote</th>
                <th width="5%" >Stock</th>
                <th width="10%" >Cant.</th>
                <th width="15%" >Venc.</th>
                <th width="5%" >Taxable</th>
              </tr>
            </thead><tbody>';


foreach ($res as $key => $value) {

$value = json_decode($value);

$PRICE =  $value->{'Price'};
$UNI =  $value->{'UnitMeasure'};
$NAME =  $value->{'Descr'};
$Lote =  $value->{'lote'};
$FECHA_VEN =  $value->{'venc'};
$STOCK =  $value->{'stock'};
$TAG_LOCATION =  $value->{'route'};
$ID_LOCATION = $value->{'id_location'};
$QTY = number_format($value->{'qty'},5, '.',',');



$ID_PRO="'".$itemid."'";
$PRICE="'".$PRICE."'";
$UNI_PRO="'".$UNI."'";
$NAME_PRO="'".$NAME."'";
$LOTE= "'".$Lote."'" ;
$VENC=  "'".str_replace('/','-', $FECHA_VEN)."'";
$RUTA= "'".$TAG_LOCATION."'";         
$QTYMAX =    "'".$value->{'qty'}."'"; 

$chk_val = $this->model->Query_value('Products_Exp','TaxType','WHERE ProductID="'.$itemid.'"');

if($chk_val=='1'){

$checked='checked disabled';
$chk_val='checked';

}else{

 $checked='disable'; 
 $chk_val='';
}

if($FECHA_VEN!='0000-00-00 00:00:00' and $FECHA_VEN!=null){
       $FECHA_VEN = date('Y-m-d',strtotime($FECHA_VEN));
     }else{
       $FECHA_VEN = '';
  }


if($QTY>=1){

  //<a href="javascript:void(0)" onclick="javascript: agregar_pro_sale_sale('.$ID_PRO.','.$NAME_PRO.','.$PRICE.','.$LOTE.','.$VENC.','.$RUTA.','.$QTYMAX.','.$UNI_PRO.');" ><i style="color:green" class="fa fa-plus"></i></a>

  $id_validar = "'".$Lote.$TAG_LOCATION."qty'";

//<td width="5%"><input type="checkbox" id="'.$Lote.$TAG_LOCATION.'" /></td>
  
 echo '<tr>
                  <td width="20%">'.$STOCK.'</td>
                  <td width="20%">'.$TAG_LOCATION.'</td>
                  <td width="25%">'.$Lote.'</td>
                  <td width="5%" class="numb" >'.$QTY.'</td>
                  <td width="10%"><input type="number" id="'.$Lote.$TAG_LOCATION.'qty" min="0.00001" max="'.$QTY.'" value="" onfocusout="valida_qty('.$QTYMAX.',this.value,'. $id_validar.');" /></td>
                  <td width="15%" >'.$FECHA_VEN.'</td>
                  <td width="5%" ><input type="checkbox"  id="'.$Lote.$TAG_LOCATION.'taxable"  value="'.$chk_val.'" '.$checked.' />
                  </td>
                 </tr>';

}           


}

echo '</tbody></table>';

}
//modificacion rey 23/11
public function get_Cust_info($custid){

$query = 'SELECT * FROM Customers_Exp WHERE ID="'.$custid.'";';

$res = $this->model->Query($query);

echo $res[0];

}




public function set_sales_order_header($ID_compania,$SalesOrderNumber,$CustomerID,$Subtotal,$TaxID,$Net_due,$user,$nopo,$pago,$licitacion,$observaciones,$entrega,$ordertax){


$SalesOrderNumber = trim(preg_replace('/000+/','',$SalesOrderNumber));



$values = array(
'ID_compania'=>$ID_compania,
'SalesOrderNumber'=>$SalesOrderNumber,
'CustomerID'=>$this->model->Query_value('Customers_Exp','CustomerID','Where ID="'.$CustomerID.'";'),
'CustomerName'=>$this->model->Query_value('Customers_Exp','Customer_Bill_Name','Where ID="'.$CustomerID.'";'),
'Subtotal'=>$Subtotal,
'TaxID'=>$TaxID,
'OrderTax' => $ordertax,
'Net_due'=>$Net_due,
'user'=>$user,
'date'=>date("Y-m-d"),
'saletax'=>$TaxID,
'CustomerPO' => $nopo,
'tipo_licitacion' => $licitacion,
'entrega' => $entrega,
'termino_pago' => $pago,
'observaciones' =>$observaciones,

 );


//echo var_dump($values);

$this->model->insert('SalesOrder_Header_Imp',$values);

}


public function set_sales_order_detail($index,$ID_compania,$itemid,$unit_price,$SalesOrderNumber,$qty,$Price,$lote,$ruta,$venc,$arrLen,$count){

$this->SESSION();

$id_compania= $this->model->id_compania;

$chk_cur_val =  $this->model->Query_value('FAC_DET_CONF','DIV_LINE','where ID_compania="'.$this->model->id_compania .'"');

if (!$chk_cur_val){

    if($venc!=''){
           $venc = date('Y-m-d',strtotime($venc));
         }else{
           $venc = '';
      }


    //IF ITEMS EXIST
    $clause='where Item_id="'.$itemid.'" and SalesOrderNumber="'.$SalesOrderNumber.'" and ID_compania="'.$id_compania.'";';
    $ID = $this->model->Query_value('SalesOrder_Detail_Imp','ID',$clause);



    if ($ID==''){

          $values1 = array(
          'ItemOrd' => $index,
          'ID_compania'=>$id_compania,
          'SalesOrderNumber'=>$SalesOrderNumber,
          'Item_id'=>$itemid,
          'Description'=>$this->model->Query_value('Products_Exp','Description','Where ProductID="'.$itemid.'";'),
          'Quantity'=>$qty,
          'Unit_Price'=>$unit_price,
          'Net_line'=>$Price,
          'Taxable'=>'1');

           $this->model->insert('SalesOrder_Detail_Imp',$values1); //set item line

            if ($venc!=''){

              $caduc =   'Vence :'.$venc.' ';

            }else{

              $caduc = '';

            }


          $Description = 'Lote :'.$lote.' '.$caduc.' Cant.:'.$qty;

         

          $values2 = array(
          'ItemOrd' => $index,
          'ID_compania'=>$id_compania,
          'SalesOrderNumber'=>$SalesOrderNumber,
          'Item_id'=>'',
          'Description'=>$Description,
          'Quantity'=>'0',
          'Unit_Price'=>'0',
          'Net_line'=>'0',
          'Taxable'=>'1');


          $this->model->insert('SalesOrder_Detail_Imp',$values2);//set lote line



    }else{

          $QUERY='SELECT Quantity, Unit_Price FROM SalesOrder_Detail_Imp where ID="'.$ID.'"';

          $QTY_PRI = $this->model->Query($QUERY);

                foreach ($QTY_PRI  AS $QTY_PRI) {

                $QTY_PRI = json_decode($QTY_PRI);

                $now_qty = $QTY_PRI->{'Quantity'}+$qty;
                $net_line= $QTY_PRI->{'Unit_Price'} * $now_qty;

                }


          $query= 'UPDATE SalesOrder_Detail_Imp SET Quantity="'.$now_qty.'" , Net_line="'.$net_line.'" where ID="'.$ID.'";';

          
          $this->model->Query($query);

          if ($venc!=''){

              $caduc =   'Vence :'.$venc.' ';
              
            }else{

              $caduc = '';

            }


          $Description = 'Lote :'.$lote.' '.$caduc.' Cant.:'.$qty;

          $values2 = array(
          'ItemOrd' => $index,
          'ID_compania'=>$id_compania,
          'SalesOrderNumber'=>$SalesOrderNumber,
          'Item_id'=>'',
          'Description'=>$Description,
          'Quantity'=>'0',
          'Unit_Price'=>'0',
          'Net_line'=>'0',
          'Taxable'=>'1');


          $this->model->insert('SalesOrder_Detail_Imp',$values2);//set lote line


    }

}else{

      $values1 = array(
      'ItemOrd' => $index,
      'ID_compania'=>$id_compania,
      'SalesOrderNumber'=>$SalesOrderNumber,
      'Item_id'=>$itemid,
      'Description'=>$this->model->Query_value('Products_Exp','Description','Where ProductID="'.$itemid.'";'),
      'Quantity'=>$qty,
      'Unit_Price'=>$unit_price,
      'Net_line'=>$Price,
      'Taxable'=>'1');

       $this->model->insert('SalesOrder_Detail_Imp',$values1); //set item line

        if ($venc!=''){

          $caduc =   'Vence :'.$venc.' ';

        }else{

          $caduc = '';

        }


      $Description = 'Lote :'.$lote.' '.$caduc.' Cant.:'.$qty;

     

      $values2 = array(
      'ItemOrd' => $index,
      'ID_compania'=>$id_compania,
      'SalesOrderNumber'=>$SalesOrderNumber,
      'Item_id'=>'',
      'Description'=>$Description,
      'Quantity'=>'0',
      'Unit_Price'=>'0',
      'Net_line'=>'0',
      'Taxable'=>'1');


      $this->model->insert('SalesOrder_Detail_Imp',$values2);//set lote line



}





$ruta = $this->model->Query_value('ubicaciones','id','Where etiqueta="'.$ruta.'";');

$values3 = array(
'SaleOrderId'=>$SalesOrderNumber,
'no_lote'=>$lote,
'ProductID'=>$itemid,
'qty'=>$qty,
'status_pendding' => '1',
'status_location_id' => $this->model->Query_value('status_location','id','Where id_product="'.$itemid.'" and lote="'.$lote.'" and route="'.$ruta.'" and ID_compania="'.$id_compania.'";'),
'ID_compania' => $id_compania
);


$this->model->insert('sale_pendding',$values3);

//UBICO LA CANTIDAD ACTUAL EN STATUS_LOCATION
$CURRENT_QTY = $this->get_any_lote_qty($lote,$itemid,$ruta);

//ACTUALIZO LA CANTIDAD EN LA UBICCION DEFAULT
$QTY_TO_SET = $CURRENT_QTY - $qty;

$query= 'UPDATE status_location SET qty="'.$QTY_TO_SET.'" where lote="'.$lote.'" and id_product="'.$itemid.'" and route="'.$ruta.'" and ID_compania="'.$id_compania.'"';


$res = $this->model->Query($query);

//$this->clear_lotacion_register();
if($count==$arrLen){ echo '1';}else{ echo '0';}
}




public function set_sal_merc($itemID,$orderID,$qty,$lote,$ruta,$fecha_ven,$Glacct,$reasonToAd,$count,$arrLen,$Jobinfo){

$this->SESSION();


$id_compania= $this->model->id_compania;

$id_user_active = $this->model->active_user_id;

//$Order = trim(preg_replace('/000+/','',$orderID));

list($JobDesc,$JobPhase,$JobCost) = explode(';', $Jobinfo);


$ruta = $this->model->Query_value('ubicaciones','id','Where etiqueta="'.$ruta.'";');
$LastUnitCost = $this->model->Query_value('Products_Exp','LastUnitCost','Where ProductID="'.$itemID.'" and id_compania="'.$id_compania.'";');

//echo 'LUC'.$LastUnitCost;

$UnitCost = $qty*$LastUnitCost;

$qty = (-1)*$qty;



$values = array(
'ItemID' => $itemID,
'ID_compania' => $this->model->id_compania,
'Reference' => $orderID,
'ReasonToAdjust' => $reasonToAd,
'Account' => $Glacct,
'Quantity' => $qty,
'Job_id_int' => $JobID,
'USER' => $id_user_active,
'JobID' => $JobDesc,
'JobPhaseID' =>  $JobPhase,
'JobCostCodeID' => $JobCost,
'UnitCost' => $UnitCost ,
'Date' => date('Y-m-d'),
'location_id' => $this->model->Query_value('status_location','id','where lote="'.$lote.'" and id_product="'.$itemID.'" and route="'.$ruta.'" and ID_compania="'.$this->model->id_compania.'"')
);


//echo var_dump($values);
$this->model->insert('InventoryAdjust_Imp',$values);

// ******************************************************************************************************************


//UBICO LA CANTIDAD ACTUAL EN STATUS_LOCATION
$CURRENT_QTY = $this->get_any_lote_qty($lote,$itemID,$ruta);

//ACTUALIZO LA CANTIDAD EN LA UBICCION DEFAULT
$QTY_TO_SET = $CURRENT_QTY + $qty; //(qty viene con signo negativo)

$query= 'UPDATE status_location SET qty="'.$QTY_TO_SET.'" where lote="'.$lote.'" and id_product="'.$itemID.'" and route="'.$ruta.'" and ID_compania="'.$this->model->id_compania.'"';


$res = $this->model->Query($query);

//echo $count.' '.$arrLen.'<br>';
//$this->clear_lotacion_register();
if($count==$arrLen){ echo '1';} else{ echo '0';}


}

public function get_invadj_info($id,$resp){


$this->SESSION();


$query ='SELECT * FROM `InventoryAdjust_Imp` 
inner JOIN `status_location` ON status_location.id = InventoryAdjust_Imp.location_id
inner JOIN `SAX_USER` ON `SAX_USER`.`id` = InventoryAdjust_Imp.USER where InventoryAdjust_Imp.Reference ="'.$id.'" and  
status_location.ID_compania="'.$this->model->id_compania.'" GROUP BY InventoryAdjust_Imp.Reference';



$ORDER_detail= $this->model->Query($query);


echo '<br/><br/><fieldset><legend>Detalle de Salida de Mercancia</legend><table class="table table-striped table-bordered" cellspacing="0"  ><tr>';

  foreach ($ORDER_detail as $datos) {
    $ORDER_detail = json_decode($datos);

$Proyecto= $this->model->Query_value('Jobs_Exp','JobID','where ID="'.$ORDER_detail->{'JobID'}.'"');

    echo "<th><strong>No. Referencia</strong></th><td class='InfsalesTd order'>".str_pad($ORDER_detail->{'Reference'}, 7 ,"0",STR_PAD_LEFT)."</td>
          <th><strong>Fecha</strong></th><td class='InfsalesTd'>".$ORDER_detail->{'Date'}."</td>
          <th><strong>Proyecto</strong></th><td class='InfsalesTd'>".$ORDER_detail->{'JobID'}.' / '.$ORDER_detail->{'JobPhaseID'}.' / '.$ORDER_detail->{'JobCostCodeID'}."</td>
          <th><strong>Descripcion</strong></th><td class='InfsalesTd'>".$ORDER_detail->{'ReasonToAdjust'}."</td>
          <th><strong>Responsable</strong></th><td class='InfsalesTd'>".$resp."</td>";

}



echo "</tr></table>";

$query ='SELECT * FROM `InventoryAdjust_Imp` 
inner JOIN `status_location` ON status_location.id = InventoryAdjust_Imp.location_id
inner JOIN `SAX_USER` ON `SAX_USER`.`id` = InventoryAdjust_Imp.USER where InventoryAdjust_Imp.Reference ="'.$id.'" and  
status_location.ID_compania="'.$this->model->id_compania.'"';


$ORDER= $this->model->Query($query);

echo '<table id="example-12" class="table table-striped table-bordered" cellspacing="0"  >
      <thead>
        <tr>
          <th>Codigo</th>
          <th>Descripcion</th>
          <th>Cantidad</th>
          <th>Unidad</th>
          <th>Estado Sinc.</th>
        </tr>
      </thead><tbody>';


foreach ($ORDER as $datos) {

    $ORDER = json_decode($datos);

    $id= "'".$ORDER_detail->{'SalesOrderNumber'}."'";

  if($ORDER->{'Error'}=='1') { 

   $status= "Error : ".$ORDER->{'ErrorPT'}. 'Se ha cancelado la Orden';
   $style="style='color:red;'"; 


 } else{

    if($ORDER->{'Enviado'}!="1"){

      $style="style='color:orange;'"; 
      $status='Por Procesar'; }else{ 

        $status= "Sincronizado el: ".$ORDER->{'Export_date'};
         $style="style='color:green;'";

       }   

    }

$quantity = number_format($ORDER->{'Quantity'},2,'.',',')*(-1);

$prod_desc = $this->model->Query_value('Products_Exp','Description','where ProductID="'.$ORDER->{'ItemID'}.'"');

$prod_measure= $this->model->Query_value('Products_Exp','UnitMeasure','where ProductID="'.$ORDER->{'ItemID'}.'"');

echo  "<tr>
          <td>".$ORDER->{'ItemID'}."</td>
          <td>".$prod_desc."</td>
          <td>".$quantity."</td>
          <td>".$prod_measure.'</td>
          <td '.$style.' >'.$status.'</td>
      </tr>';

  }

echo '</tbody></table><div style="float:right;" class="col-md-2">
<a href="'.URL.'index.php?url=ges_ventas/ges_print_SalMerc/'.$ORDER_detail->{'Reference'}.'"  class="btn btn-block btn-secondary btn-icon btn-icon-standalone btn-icon-standalone-right btn-single text-left">
   <img  class="icon" src="img/Printer.png" />
  <span>Imprimir</span>
</a>
</div></fieldset>';





}


public function get_salesorder_info($id){

$this->SESSION();
$id_compania= $this->model->id_compania;

$query ="SELECT * FROM `SalesOrder_Header_Imp`
inner JOIN `SalesOrder_Detail_Imp` ON SalesOrder_Header_Imp.SalesOrderNumber = SalesOrder_Detail_Imp.SalesOrderNumber
inner JOIN `SAX_USER` ON `SAX_USER`.`id` = SalesOrder_Header_Imp.user
inner JOIN Products_Exp ON Products_Exp.ProductID = SalesOrder_Detail_Imp.item_id
WHERE SalesOrder_Header_Imp.SalesOrderNumber='".$id."' and SalesOrder_Header_Imp.ID_compania='".$id_compania."'  GROUP BY SalesOrder_Detail_Imp.SalesOrderNumber ";



$ORDER_detail= $this->model->Query($query);


echo '<br/><br/><fieldset><legend>Detalle de Orden de venta</legend><table class="table table-striped table-bordered" cellspacing="0"  ><tr>';

  foreach ($ORDER_detail as $datos) {
    $ORDER_detail = json_decode($datos);


  if($ORDER_detail->{'Error'}=='1') { 

   $status= "Error : ".$ORDER_detail->{'ErrorPT'}. 'Se ha cancelado la Orden';
   $style="style='color:red;'"; 


 } else{

    if($ORDER_detail->{'Enviado'}!="1"){

      $style="style='color:orange;'"; 
      $status='Por Procesar'; }else{ 

        $status= "Sincronizado el: ".$ORDER_detail->{'Export_date'};
        $style="style='color:green;'";

       }   

    }

$aprobacion = $this->model->Query_value('SalesOrder_Header_Exp','Close_SO','Where SalesOrderNumber="'.$ORDER_detail->{'SalesOrderNumber'}.'" and  ID_compania="'.$this->model->id_compania.'" ');


//if($aprobacion==''){ $apro = 'En espera de envio'; $apro_style="style='color:orange; font-style:bold;'"; }

if($aprobacion=='0' || $aprobacion==''){ $apro = 'En espera de aprobacion';  $apro_style="style='color:orange; font-style:bold;'";  }

if($aprobacion=='1' ){ $apro = 'Aprobado'; $apro_style="style='color:green; font-style:bold;'"; }


    echo "<th><strong>No. Orden</strong></th><td class='InfsalesTd order'>".$ORDER_detail->{'SalesOrderNumber'}."</td>
          <th><strong>Fecha</strong></th><td class='InfsalesTd'>".$ORDER_detail->{'date'}."</td>
          <th><strong>Cliente</strong></th><td class='InfsalesTd'>".$ORDER_detail->{'CustomerName'}."</td>
          <th><strong>Total venta</strong></th><td class='InfsalesTd'>".$ORDER_detail->{'Net_due'}."</td>
          <th><strong>Vendedor</strong></th><td class='InfsalesTd'>".$ORDER_detail->{'name'}.' '.$ORDER_detail->{'lastname'}."</td>";

}


if($ORDER_detail->{'Error'}=='1') { 
$apro ='';

 }

echo "</tr></table>";

echo '<table  class="table table-striped table-bordered" cellspacing="0"  width="50%">
      <tr>
      <th><strong>Estado</strong></th><td '.$style.' class="InfsalesTd">'.$status.'</td>
      <th><strong>Aprobacion</strong></th><td   '.$apro_style.'  class="InfsalesTd" >'.$apro.'</td>
      </tr>
      </table>';

$query ="SELECT * FROM `SalesOrder_Detail_Imp`
WHERE SalesOrder_Detail_Imp.SalesOrderNumber='".$id."' and  SalesOrder_Detail_Imp.ID_compania='".$id_compania."' ORDER BY SalesOrder_Detail_Imp.ItemOrd ASC ;";


$ORDER= $this->model->Query($query);

echo '<table id="example-12" class="table table-striped table-bordered" cellspacing="0"  >
      <thead>
        <tr>
          <th>Codigo</th>
          <th>Descripcion</th>
          <th>Cantidad</th>
          <th>Precio Unit.</th>
          <th>Unidad</th>
        </tr>
      </thead><tbody>';


foreach ($ORDER as $datos) {

    $ORDER = json_decode($datos);

    $id= "'".$ORDER_detail->{'SalesOrderNumber'}."'";


echo  "<tr>
          <td>".$ORDER->{'Item_id'}."</td>
          <td>".$ORDER->{'Description'}."</td>
          <td>".number_format($ORDER->{'Quantity'},4,'.',',')."</td>
          <td>".number_format($ORDER->{'Unit_Price'},4,'.',',')."</td>
          <td>".$ORDER->{'UnitMeasure'}.'</td>
      </tr>';

  }

echo '</tbody></table><div style="float:right;" class="col-md-2">
<a href="'.URL.'index.php?url=ges_ventas/ges_print_salesorder/'.$ORDER_detail->{'SalesOrderNumber'}.'"  class="btn btn-block btn-secondary btn-icon btn-icon-standalone btn-icon-standalone-right btn-single text-left">
   <img  class="icon" src="img/Printer.png" />
  <span>Imprimir</span>
</a>
</div></fieldset>';


}

public function get_sales_info($id){


$this->SESSION();


$query ="SELECT * FROM `Sales_Header_Imp`
inner JOIN `Sales_Detail_Imp` ON Sales_Header_Imp.InvoiceNumber = Sales_Detail_Imp.InvoiceNumber
inner JOIN `SAX_USER` ON `SAX_USER`.`id` = Sales_Header_Imp.user
inner JOIN Products_Exp ON Products_Exp.ProductID = Sales_Detail_Imp.item_id
WHERE Sales_Header_Imp.InvoiceNumber='".$id."' GROUP BY Sales_Detail_Imp.InvoiceNumber";



$ORDER_detail= $this->model->Query($query);


echo '<br/><br/><fieldset><legend>Detalle de  venta</legend><table class="table table-striped table-bordered" cellspacing="0"  ><tr>';

  foreach ($ORDER_detail as $datos) {
    $ORDER_detail = json_decode($datos);


    echo "<th><strong>No. Orden</strong></th><td class='InfsalesTd order'>".str_pad($ORDER_detail->{'InvoiceNumber'}, 7 ,"0",STR_PAD_LEFT)."</td>
          <th><strong>Fecha</strong></th><td class='InfsalesTd'>".$ORDER_detail->{'date'}."</td>
          <th><strong>Cliente</strong></th><td class='InfsalesTd'>".$ORDER_detail->{'CustomerName'}."</td>
          <th><strong>Total venta</strong></th><td class='InfsalesTd'>".$ORDER_detail->{'Net_due'}."</td>
          <th><strong>Vendedor</strong></th><td class='InfsalesTd'>".$ORDER_detail->{'name'}.' '.$ORDER_detail->{'lastname'}."</td>";

}




echo "</tr></table>";

$query ="SELECT * FROM `Sales_Header_Imp`
inner JOIN `Sales_Detail_Imp` ON Sales_Header_Imp.InvoiceNumber = Sales_Detail_Imp.InvoiceNumber
inner JOIN `SAX_USER` ON `SAX_USER`.`id` = Sales_Header_Imp.user
WHERE Sales_Header_Imp.InvoiceNumber='".$id."';";


$ORDER= $this->model->Query($query);

echo '<table id="example-12" class="table table-striped table-bordered" cellspacing="0"  >
      <thead>
        <tr>
          <th>Codigo</th>
          <th>Descripcion</th>
          <th>Cantidad</th>
          <th>Precio Unit.</th>
          <th>Unidad</th>
          <th>Estado Sinc.</th>
        </tr>
      </thead><tbody>';


foreach ($ORDER as $datos) {

    $ORDER = json_decode($datos);

    $id= "'".$ORDER_detail->{'InvoiceNumber'}."'";

  if($ORDER->{'Error'}=='1') { 

   $status= "Error : ".$ORDER->{'ErrorPT'}. 'Se ha cancelado la Orden';
   $style="style='color:red;'"; 


 } else{

    if($ORDER->{'Enviado'}!="1"){

      $style="style='color:orange;'"; 
      $status='Por Procesar'; }else{ 

        $status= "Sincronizado el: ".$ORDER->{'Export_date'};
         $style="style='color:green;'";

       }   

    }

echo  "<tr>
          <td>".$ORDER->{'Item_id'}."</td>
          <td>".$ORDER->{'Description'}."</td>
          <td>".number_format($ORDER->{'Quantity'},4,'.',',')."</td>
          <td>".number_format($ORDER->{'Unit_Price'},4,'.',',')."</td>
          <td>".$ORDER->{'UnitMeasure'}.'</td>
          <td '.$style.' >'.$status.'</td>
      </tr>';

  }

echo '</tbody></table><div style="float:right;" class="col-md-2">
<a href="'.URL.'index.php?url=ges_ventas/ges_print_sales/'.$ORDER_detail->{'InvoiceNumber'}.'"  class="btn btn-block btn-secondary btn-icon btn-icon-standalone btn-icon-standalone-right btn-single text-left">
   <img  class="icon" src="img/Printer.png" />
  <span>Imprimir</span>
</a>
</div></fieldset>';


}



public function set_almacen($name){


$id = $this->model->Query('SELECT id from almacenes where name="'.$name.'" and onoff="1";');

foreach ($id as $value) {

$value = json_decode($value);

 $id = $value->{'id'};

}

if($id==''){ 

 $value = array('name' => $name);

 $this->model->insert('almacenes',$value);

 echo 'Se ha agregado el nuevo almacen';

}else{

    echo 'El nombre de almacen ya existe';

 }

}


public function set_location($id_almacen,$ruta){

$check= $this->model->Query('select * from ubicaciones where etiqueta="'.$ruta.'";');

foreach ($check as $value) {

 $value = json_decode($value);

 $id=$value->{'id'};
}

if($id==''){

$value = array(
  'id_almacen' => $id_almacen,
  'etiqueta' => $ruta
  );



$this->model->insert('ubicaciones',$value);


Echo 'La ubicacion '.$ruta.' se ha creado con exito';

}else{ 

Echo 'La ubicacion '.$ruta.' ya existe ';
}

}


public function get_item_filter_by_stock($id_stock){


$this->SESSION();

$query = '
SELECT *
FROM status_location 
inner join Products_Exp on status_location.id_product = Products_Exp.ProductID
where  stock="'.$id_stock.'" and Products_Exp.ID_compania ="'.$this->model->id_compania.'" ';



$prod = $this->model->Query($query);




$table.= '<script>
var table = $("#items_ubicaciones").dataTable({
      aLengthMenu: [
        [10, 25,50,-1], [10, 25, 50,"All"]
      ]
    });

table.yadcf([
{column_number : 0},
{column_number : 1},
{column_number : 2}
]); 
</script>

<table id="items_ubicaciones" class="table table-striped responsive">
            <thead>
              <tr>
                <th width="20%">Id</th>
                <th width="30%">Descripcion</th>
                <th width="20%">Lote</th>
                <th width="10%">Cant. Disp.</th>
                <th width="10%">Cant. Pend.</th>
                <th width="10%">Almacen</th>
                <th width="10%">Ruta</th></tr>
            </thead>
            <tbody> ';

foreach ($prod as $value) {

$prod = json_decode($value);


$Descr = $this->model->Query_value('Products_Exp','Description',' where ProductID="'.$prod->{'id_product'}.'" ');

$stock = $this->model->Query_value('almacenes','name',' where id="'.$prod->{'stock'}.'"');

$route = $this->model->Query_value('ubicaciones','etiqueta',' where id="'.$prod->{'route'}.'"');

$sale_pendding = $this->model->Query_value('sale_pendding','qty',' where status_pendding="1" and status_location_id="'.$prod->{'id'}.'" and no_lote="'.$prod->{'lote'}.'" and ID_compania="'.$this->model->id_compania.'" ');

if($sale_pendding>=1 || $prod->{'qty'}>=1){

$table.= '<tr><td>'.$prod->{'id_product'}.'</td><td>'.$Descr.'</td><td>'.$prod->{'lote'}.'</td><td>'.$prod->{'qty'}.'</td><td style="background-color:#F5A9A9;" >'.$sale_pendding.'</td><td>'.$stock.'</td><td>'.$route.'</td></tr>';

}



  }          


$table.='</tbody>
            </table>';



echo $table;
  

}


public function  get_item_filter_by_route($id_route){


$this->SESSION();


$query = '
SELECT *
FROM status_location 
inner join Products_Exp on status_location.id_product = Products_Exp.ProductID
where route="'.$id_route.'" and  Products_Exp.ID_compania ="'.$this->model->id_compania.'" ';


$prod = $this->model->Query($query);




$table.= '<script>
var table = $("#items_ubicaciones").dataTable({
      aLengthMenu: [
        [10, 25,50,-1], [10, 25, 50,"All"]
      ]
    });

table.yadcf([
{column_number : 0},
{column_number : 1},
{column_number : 2}
]); 
</script>

<table id="items_ubicaciones" class="table table-striped responsive">
            <thead>
              <tr>
                <th width="20%">Id</th>
                <th width="30%">Descripcion</th>
                <th width="20%">Lote</th>
                <th width="10%">Cant. Disp.</th>
                <th width="10%">Cant. Pend.</th>
                <th width="10%">Almacen</th>
                <th width="10%">Ruta</th></tr>
            </thead>
            <tbody> ';

foreach ($prod as $value) {

$prod = json_decode($value);


$Descr = $this->model->Query_value('Products_Exp','Description',' where ProductID="'.$prod->{'id_product'}.'" ');

$stock = $this->model->Query_value('almacenes','name',' where id="'.$prod->{'stock'}.'"');

$route = $this->model->Query_value('ubicaciones','etiqueta',' where id="'.$prod->{'route'}.'"');

$sale_pendding = $this->model->Query_value('sale_pendding','qty',' where status_pendding="1" and status_location_id="'.$prod->{'id'}.'" and no_lote="'.$prod->{'lote'}.'" and ID_compania="'.$this->model->id_compania.'"');

if($sale_pendding>=1 || $prod->{'qty'}>=1){

$table.= '<tr><td>'.$prod->{'id_product'}.'</td><td>'.$Descr.'</td><td>'.$prod->{'lote'}.'</td><td>'.$prod->{'qty'}.'</td><td style="background-color:#F5A9A9;" >'.$sale_pendding.'</td><td>'.$stock.'</td><td>'.$route.'</td></tr>';

}



  }          


$table.='</tbody>
            </table>';



echo $table;

  

}

public function erase_account($id){

$query='UPDATE SAX_USER SET onoff="0" where id="'.$id.'"';

$this->model->Query($query);


}

public function Get_SalesOrders($sort,$limit,$date1,$date2){


$this->SESSION();

$clause='';

$clause.= 'where SalesOrder_Header_Imp.ID_compania="'.$this->model->id_compania.'" ';



if($date1!=''){

   if($date2!=''){

      $clause.= 'and  date between "'.$date1.'" and "'.$date2.'" ';           
    }
   
   if($date2==''){ 

     $clause.= 'and date="'.$date1.'"';
   }
     
}



if($this->model->active_user_role=='admin'){

$query ='SELECT * FROM `SalesOrder_Header_Imp` 
inner JOIN `SalesOrder_Detail_Imp` ON SalesOrder_Header_Imp.SalesOrderNumber = SalesOrder_Detail_Imp.SalesOrderNumber
inner JOIN `SAX_USER` ON `SAX_USER`.`id` = SalesOrder_Header_Imp.user '.$clause.' GROUP BY SalesOrder_Header_Imp.SalesOrderNumber order by SalesOrder_Header_Imp.LAST_CHANGE '.$sort.' limit '.$limit ; }

if($this->active_user_role=='user'){

  if($clause!=''){ $clause.= 'and `SAX_USER`.`id`="'.$this->active_user_role_id.'"'; } else{ $clause.= ' Where `SAX_USER`.`id`="'.$this->active_user_role_id.'"'; }

$query='SELECT * FROM `SalesOrder_Header_Imp`
inner JOIN `SalesOrder_Detail_Imp` ON SalesOrder_Header_Imp.SalesOrderNumber = SalesOrder_Detail_Imp.SalesOrderNumber
inner JOIN `SAX_USER` ON `SAX_USER`.`id` = SalesOrder_Header_Imp.user '.$clause.' GROUP BY SalesOrder_Header_Imp.SalesOrderNumber  order by SalesOrder_Header_Imp.LAST_CHANGE '.$sort.' limit '.$limit;

}

       
$table.= '<script type="text/javascript">
  jQuery(document).ready(function($)
  {
   var table = $("#table_report").dataTable({
      bSort: false,
      
      aLengthMenu: [
        [5,10, 25,50,-1], [5,10, 25, 50,"All"]
      ]
    });

   
      
  });
  </script>
  <table id="table_report" class="tableReport table table-striped" cellspacing="0"  >
    <thead>
      <tr>
        <th>No. Orden</th>
        <th>Fecha</th>
        <th>Cliente</th>
        <th>Total Venta</th>
        <th>Procesado por:</th>
         <th>Estado</th>
        <th>Aprobacion</th>
        <th>Detalle</th>
      </tr>
    </thead>
    <tfoot>
      <tr>
        <th>No. Orden</th>
        <th>Fecha</th>
        <th>Cliente</th>
        <th>Total Venta</th>
        <th>Procesado por:</th>
        <th>Estado</th>
        <th>Aprobacion</th>
        <th>Detalle</th>
      </tr>
    </tfoot>';



$filter =  $this->model->Query($query);

$URL ='"'.URL.'"';

foreach ($filter as $datos) {

  $filter = json_decode($datos);


  $ID ='"'.$filter->{'SalesOrderNumber'}.'"';

   if($filter->{'Error'}==1) { 

     $status= "Error : ".$filter->{'ErrorPT'}. '  Cancelado';
     $style="style='color:red; font-style:bold;'"; 


   } else{

    if($filter->{'Enviado'}==0){

      $style="style='color:orange; font-style:bold;'"; 
      $status='No sincronizado';

       }else{ 

         $status= "Enviado";
         $style="style='color:green; font-style:bold;'";

       }   

    }


$aprobacion = $this->model->Query_value('SalesOrder_Header_Exp','Close_SO','Where SalesOrderNumber="'.$filter->{'SalesOrderNumber'}.'" and  ID_compania="'.$this->model->id_compania.'" ');


//if($aprobacion==''){ $apro = 'En espera de envio'; $apro_style="style='color:orange; font-style:bold;'"; }

if($aprobacion=='0' || $aprobacion==''){ $apro = 'En espera de aprobacion';  $apro_style="style='color:orange; font-style:bold;'";  }

if($aprobacion=='1' ){ $apro = 'Aprobado'; $apro_style="style='color:green; font-style:bold;'"; }



$user = $this->model->Get_User_Info($filter->{'user'}); 

foreach ($user as $value) {
$value = json_decode($value);
$name= $value->{'name'};
$lastname = $value->{'lastname'};
}


if($filter->{'Error'}!=1){$apr = $apro;}

$table.= "<tr>
    <td >".$filter->{'SalesOrderNumber'}."</td>
    <td >".$filter->{'date'}."</td>
    <td >".$filter->{'CustomerName'}.'</td>
    <td >'.$filter->{'Net_due'}.'</td>
    <td >'.$name.' '.$lastname.'</td>
    <td '.$style.'>'.$status.'</td>
    <td '.$apro_style.'>'.$apr."</td>
    <td ><a href='#' onclick='javascript: show_sales(".$URL.",".$ID."); ' ><i style='color:blue' class='fa fa-search'></i></a>   </td>
   </tr>";

$apr = '';
}

$table.= '</table>';

echo $table;

}

//*****************************************************************

//SECCION DE REPORTES



public function get_report($type,$sort,$limit,$date1,$date2){

$this->SESSION();

switch ($type) {

//CASE 1

case "InvXVen":
$table = '';
$clause='';


$clause.= 'where  s.qty > "0"  and l.fecha_ven > 0 and p.id_compania="'.$this->model->id_compania.' "';

if($date1!=''){
   if($date2!=''){
      $clause.= ' and  l.fecha_ven >= "'.$date1.'%" and l.fecha_ven <= "'.$date2.'%" ';           
    }
   if($date2==''){ 
     $clause.= ' and  l.fecha_ven like "'.$date1.'%" ';
   }
}

 //ModifGPH 

 $table.= '<script type="text/javascript">

 jQuery(document).ready(function($)

  {

   var table = $("#table_reportInvXVen").dataTable({


      responsive: true,

      dom: "Bfrtip",

      bSort: false,

      bPaginate: false,

      select:true,

        buttons: [

          {

          extend: "excelHtml5",

          text: "Exportar",

          title: "Reporte_STOCK_X_VEN",

           
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
{column_number : 0},
{column_number : 1},
{column_number : 2,
 column_data_type: "html",
 html_data_type: "text" 
},
{column_number : 3}
],
{cumulative_filtering: true}); 

});


  </script>



  <table id="table_reportInvXVen" class="table table-striped responsive" cellspacing="0">

    <thead>

      <tr>
        <th width="15%">Almacen</th>

        <th width="15%">Ubicacion</th>

        <th width="20%">Producto</th>

        <th width="15%">Lote</th>

        <th width="15%">Descripcion</th>

        <th width="5%">Cantidad</th>

        <th width="10%">Vence</th>

        <th width="5%">Dias</th>     

      </tr>

    </thead>

    <tbody>';



      $Item = $this->model->get_InvXven($sort,$limit,$clause);



      foreach ($Item as $datos) {

       $Item = json_decode($datos);

       $fechaVen = $Item->{'Vencimiento'};



       $fechaVen = strtotime($fechaVen);

       $fechaVen =  date( 'Y-m-d', $fechaVen);



       $dife = strtotime($fechaVen)-strtotime('now');



       $intervalo=intval($dife/60/60/24);



 

$style='bgcolor="red"'; 



       if ($intervalo<16) {



          $style='style="background-color:#F5A9A9;"';



        }elseif ($intervalo>=16 && $intervalo<=31) {



          $style='style="background-color:#F7D358;"';

     

        }else{ $style=''; }



$table.='<tr '.$style.'  >
              <td  >'.$Item->{'Almacen'}.'</td>
              <td  >'.$Item->{'Ubicacion'}.'</td>
              <td  ><a href="'.URL.'index.php?url=ges_inventario/inv_info/'.$Item->{'Producto'}.'" >'.$Item->{'Producto'}.'</a></td></td>
              <td  >'.$Item->{'Lote'}.'</td>
              <td  >'.$Item->{'Descripcion'}.'</td>
              <td class="numb" >'.$Item->{'Cantidad'}.'</td>
              <td  >'.$fechaVen.'</td>
              <td  >'.$intervalo.'</td>

           </tr>';
 

      }

   

$table.= '</tbody></table>'; 



break;



case "InvXStk":

$table = '';
$clause.= 'where  s.qty > "0"  and p.id_compania="'.$this->model->id_compania.' "';



 $table.= '<script type="text/javascript">

 jQuery(document).ready(function($)

  {

   var table = $("#table_reportInvXStk").dataTable({

         rowReorder: {
            selector: "td:nth-child(2)"
        },

      responsive: true,


      dom: "Bfrtip",
      bSort: false,
      select:true,
      
      bPaginate:false,

        buttons: [

          {

          extend: "excelHtml5",

          text: "Exportar",

          title: "Reporte_INVENTARIO_x_STOCK",

           
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
{column_number : 0},
{column_number : 1},
{column_number : 2},
{column_number : 3,
column_data_type: "html",
 html_data_type: "text"}
],
{cumulative_filtering: true}); 

});


  </script>



  <table id="table_reportInvXStk" class="table table-striped responsive" cellspacing="0">

    <thead>

      <tr>
        <th width="15%">Almacen</th>

        <th width="15%">Ubicacion</th>
        <th width="15%">Lote</th>
        <th width="15%">Producto</th>
        <th width="15%">Descripcion</th>
        
        <th width="5%">Cantidad</th>
        <th width="10%">Costo Unit.</th>
        <th width="10%">Costo Total</th>     

      </tr>

    </thead>

    <tbody>';



      $Item = $this->model->get_InvXStk($sort,$limit,$clause);



      foreach ($Item as $datos) {

       $Item = json_decode($datos);

$total = $Item->{'Cantidad'}*$Item->{'LastUnitCost'};


$table.='<tr '.$style.'  >
              <td  >'.$Item->{'Almacen'}.'</td>
              <td  >'.$Item->{'Ubicacion'}.'</td>
              <td  >'.$Item->{'Lote'}.'</td>
              <td  ><a href="'.URL.'index.php?url=ges_inventario/inv_info/'.$Item->{'Producto'}.'" >'.$Item->{'Producto'}.'</a></td>
              <td  >'.$Item->{'Descripcion'}.'</td>
              <td class="numb" >'.$Item->{'Cantidad'}.'</td>
              <td class="numb" >'.$Item->{'LastUnitCost'}.'</td>
              <td class="numb" >'.$total.'</td>

           </tr>';
 

      }

   

$table.= '</tbody></table>'; 

        break;

case "ReqStat":

$table = '';
$clause='';

$clause.= 'where REQ_HEADER.ID_compania="'.$this->model->id_compania.'" and REQ_DETAIL.ID_compania="'.$this->model->id_compania.'" ';

if($date1!=''){
   if($date2!=''){
      $clause.= ' and  DATE >= "'.$date1.'%" and DATE <= "'.$date2.'%" ';           
    }
   if($date2==''){ 
     $clause.= ' and  DATE like "'.$date1.'%" ';
   }
}




 $table.= '<script type="text/javascript">
 jQuery(document).ready(function($)
  {
   var table = $("#table_reportReqStat").dataTable({
      
      responsive: false,
      pageLength: 10,
      dom: "Bfrtip",
      bSort: false,
      select: false,
 
      info: false,
        buttons: [
          {
          extend: "excelHtml5",
          text: "Exportar",
          title: "Reporte_Estado_de_requisiciones",
           
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
{column_number : 3},
{column_number : 4}
],
{cumulative_filtering: true, 
filter_reset_button_text: false}); 
});

  </script>
   <table id="table_reportReqStat" class="display nowrap table table-condensed table-striped table-bordered" >
   
    <thead>
      <tr>
        
        <th width="10%">No. Ref.</th>
        <th width="10%">Fecha </th>
        <th width="45%">Descripcion</th>
        <th width="25%">Solicitado por:</th>
        <th width="10%">Estado</th>
        
      </tr>
    </thead>
    <tbody>';


$Item = $this->model->get_req_to_report($sort,$limit,$clause);



foreach ($Item as $datos) {

$Item = json_decode($datos);

$name = $this->model->Query_value('SAX_USER','name','Where ID="'.$Item->{'USER'}.'"');
$lastname =  $this->model->Query_value('SAX_USER','lastname','Where ID="'.$Item->{'USER'}.'"');

$status='';

$ID = '"'.$Item->{'NO_REQ'}.'"';

$URL = '"'.URL.'"';


//obtengo estatus de la requisicion
$status = $this->req_status($Item->{'NO_REQ'});

switch ($status) {

  case 'CERRADA':
     $style = 'style="background-color:#D8D8D8 ;"';//verder
    break;
  case 'FINALIZADO':
     $style = 'style="background-color:#BCF5A9;"';//verder
    break;
  case 'ORDENADO':
     $style = 'style="background-color:#F2F5A9;"';//AMARILLO
    break;
  case 'PARCIALMENTE ORDENADO':
     $style = 'style="background-color:#F3E2A9;"';//NARANJA
    break;
  case 'COTIZANDO':
     $style = 'style="background-color:#F7BE81;"';//NARANJA
    break; 
  case 'POR COTIZAR':
     $style = 'style="background-color:#F5A9A9;"';//ROJO
    break; 

}


$table.="<tr  >
              
              <td width='10%' ><a href='#' onclick='javascript: show_req(".$URL.",".$ID.");'>".$Item->{'NO_REQ'}."</a></td>
              <td width='10%' >".date('m/d/Y',strtotime($Item->{'DATE'}))."</td>
              <td width='45%' >".$Item->{'NOTA'}.'</td>
              <td width="25%" >'.$name.' '.$lastname.'</td>
              <td width="10%" '.$style.' >'.$status.'</td>
          </tr>';
 

      }

   

$table.= '</tbody></table> <div class="separador col-lg-12"></div><div id="info"></div>'; 

break;


//Reporte de requisiciones urgentes
case "ReqUrg":

$table = '';
$clause='';

$clause.= 'where REQ_HEADER.ID_compania="'.$this->model->id_compania.'" and REQ_HEADER.isUrgent="0"';

if($date1!=''){
   if($date2!=''){
      $clause.= ' and  DATE >= "'.$date1.'%" and DATE <= "'.$date2.'%" ';           
    }
   if($date2==''){ 
     $clause.= ' and  DATE like "'.$date1.'%" ';
   }
}




 $table.= '<script type="text/javascript">
 jQuery(document).ready(function($)
  {

   var table = $("#table_report").dataTable({
      
      responsive: false,
      pageLength: 10,
      bSort: false,
      select: false,
      info: false
    });
   

table.yadcf(
[ {column_number : 0} ],
  {cumulative_filtering: true, 
  filter_reset_button_text: false }); 
});

  </script>
  <div class="col-lg-4">
   <table id="table_report" class="display nowrap table table-condensed table-striped table-bordered" >
   
    <thead>
      <tr>
        
        <th width="10%">Proyecto</th>
        <th width="10%">Requisiciones Urgentes</th>
        
      </tr>
    </thead>
    <tbody>';




$Item = $this->model->get_req_to_report_urge($sort,$limit,$clause);


foreach ($Item as $datos) {

$Item = json_decode($datos);

$ID = '"'.$Item->{'job'}.'"';

$table.= "<tr >    
              <td width='10%' class='numb'  >".$Item->{'job'}."</a></td>
              <td width='10%' class='numb' ><a href='#' onclick='javascript: show_req_urg(".$ID.");'>".$Item->{'cuenta'}."</a></td>
          </tr>";
 

      }

   
$table.= '</tbody></table> </div><div class="separador col-lg-12"></div><div id="reqInfo"></div>'; 

break;

//Reporte  de consignaciones
case "ConList":

$this->SESSION();
$table = '';
$clause='';

 
$clause.= 'WHERE CON_HEADER.ID_compania="'.$this->model->id_compania.'"
                 and CON_REG_TRAS.ID_compania="'.$this->model->id_compania.'"  
                 and reg_traslado.ID_compania="'.$this->model->id_compania.'" ';

if($date1!=''){
   if($date2!=''){
      $clause.= ' and  CON_HEADER.date >= "'.$date1.'%" and CON_HEADER.date <= "'.$date2.'%" ';           
    }
   if($date2==''){ 
     $clause.= ' and  CON_HEADER.date like "'.$date1.'%" ';
   }
}

 //ModifGPH 

 $table.= '<script type="text/javascript">

 jQuery(document).ready(function($)

  {

   var table = $("#table_reportCON").dataTable({

         rowReorder: {
            selector: "td:nth-child(2)"
        },

      responsive: true,


      dom: "Bfrtip",
      bSort: false,
      select:true,
      scrollY: "200px",
      scrollCollapse: true,

        buttons: [

          {

          extend: "excelHtml5",

          text: "Exportar",

          title: "Reporte_CONSIGNACIONES",

           
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
{column_number : 0},
{column_number : 1,
 column_data_type: "html",
 html_data_type: "text" 
 },
{column_number : 2},
{column_number : 3},
{column_number : 4}
],
{cumulative_filtering: true}); 

});


  </script>



  <table id="table_reportCON" class="table table-striped responsive" cellspacing="0">

    <thead>

      <tr>
        <th width="10%">Fecha</th>
        <th width="10%">No. Ref.</th>
        <th width="10%">Responsable</th> 
        <th width="10%">Producto</th>
        <th width="10%">Lote</th>
        <th width="15%">Descripcion</th>
        <th width="10%">Almacen Origen</th>
        <th width="5%">Ruta</th>
        <th width="10%">Almacen Destino</th>
        <th width="5%">Ruta</th>
        <th width="5%">Cantidad</th>
      </tr>
    </thead>
   <tbody>';



$Item = $this->model->get_con_to_report($sort,$limit,$clause);


foreach ($Item as $datos) {

$Item = json_decode($datos);

$name = $this->model->Query_value('SAX_USER','name','Where ID="'.$Item->{'USER'}.'"');
$lastname =  $this->model->Query_value('SAX_USER','lastname','Where ID="'.$Item->{'USER'}.'"');


$ID = '"'.$Item->{'REF'}.'"';

$URL = '"'.URL.'"';



//RUTA ORIGEN
$route_src = $this->model->Query_value('ubicaciones','etiqueta',' where id="'.$Item->{'route_ini'}.'"');
$stock_src = $this->model->Query_value('almacenes','name',' where id="'.$Item->{'id_almacen_ini'}.'"');

//RUTA DESTINO
$route_des = $this->model->Query_value('ubicaciones','etiqueta',' where id="'.$Item->{'route_des'}.'"');
$stock_des = $this->model->Query_value('almacenes','name',' where id="'.$Item->{'id_almacen_des'}.'"');

$table.='<tr  >
              <td  >'.$Item->{'date'}."</td>
              <td  ><a href='#' onclick='javascript: show_con(".$URL.",".$ID.");'>".$Item->{'REF'}."</td>
              <td  >".$name.' '.$lastname."</td>
              <td  >".$Item->{'ProductID'}.'</td>
              <td  >'.$Item->{'LOTE'}."</td>
              <td  >".$Item->{'NOTA'}.'</td>
              <td style="background-color:#F3F781;" >'.$stock_src.'</td>
              <td style="background-color:#F3F781;" >'.$route_src.'</td>
              <td style="background-color:#BCDFA8;" >'.$stock_des.'</td>
              <td style="background-color:#BCDFA8;" >'.$route_des.'</td>
              <td  >'.$Item->{'CANT'}.'</td>

          </tr>';
 

      } 

   

$table.= '</tbody></table> <div class="separador col-lg-12"></div><div id="info"></div>'; 



        break;
//FIN Reporte  de consignaciones

//Reporte  ORDENES DE COMPRA
case "PurOrd":

$this->SESSION();
$table = '';
$clause='';

 
$clause.= 'WHERE PurOrdr_Header_Exp.ID_compania="'.$this->model->id_compania.'" AND PurOrdr_Header_Exp.PurchaseOrderNumber <> ""'; 

if($date1!=''){
   if($date2!=''){
      $clause.= ' and  Date >= "'.$date1.'%" and Date <= "'.$date2.'%" ';           
    }
   if($date2==''){ 
     $clause.= ' and  Date like "'.$date1.'%" ';
   }
}



 $table.= '<script type="text/javascript">

 jQuery(document).ready(function($)

  {

   var table = $("#table_reportPurOrd").dataTable({
   rowReorder: {
            selector: "td:nth-child(2)"
        },

      responsive: true,
      pageLength: 10,
      dom: "Bfrtip",
      bSort: false,
      select: false,

      info: false,
        buttons: [

          {

          extend: "excelHtml5",

          text: "Exportar",

          title: "Reporte_Ordenes_de_Compras",

           
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
{column_number : 2},
{column_number : 3},
{column_number : 4}
],
{cumulative_filtering: true}); 

});


  </script>


  <table id="table_reportPurOrd"  class="display nowrap table  table-condensed table-striped table-bordered" >

    <thead>
      <tr>
        <th width="10%">ID Ord. Compra</th>
        <th width="10%">Fecha</th>
        <th width="10%">Proveedor</th> 
        <th width="10%">Total</th>
        <th width="10%">Fecha de Entrega</th>
        <th width="10%">Asignado a</th>
        <th width="40%">Nota</th>
      </tr>
    </thead>
   <tbody>';

 

    $oc = $this->model->get_OC($sort,$limit,$clause);

    foreach ($oc as $value) {
     $value = json_decode($value);

     $date = strtotime($value->{'Date'});

     $date = date('m/d/Y',$date);


    $PO_NO = trim ($value->{'PurchaseOrderNumber'});
    $PO_NO = "'".$PO_NO."'";

     $table .= " <tr>
               <td  >".'<a href="javascript:void(0)" onclick="get_OC('.$PO_NO.')"><strong>'.$value->{'PurchaseOrderNumber'}.'</strong></a></td>
               <td  >'.$date.'</td>
               <td  >'.$value->{'VendorName'}.'</td>
               <td  class="numb">'.number_format($value->{'Total'},2).'</td>
               <td >'.$value->{'WorkflowStatusName'}.'</td>
               <td >'.$value->{'WorkflowAssignee'}.'</td>
               <td >'.$value->{'WorkflowNote'}.'</td>
               </tr>';

     
    }

   
      $table .= '</tbody></table>

            <div class="separador col-lg-12"></div>
            <div class="col-lg-12" > 
            <div id="table2"></div>
            </div>';

        break;

//Reporte  FACTURAS DE COMPRA
case "PurFact":

$this->SESSION();
$table = '';
$clause='';

 
$clause.= 'WHERE Purchase_Header_Imp.ID_compania="'.$this->model->id_compania.'"'; 

if($date1!=''){
   if($date2!=''){
      $clause.= ' and  Date >= "'.$date1.'%" and Date <= "'.$date2.'%" ';           
    }
   if($date2==''){ 
     $clause.= ' and  Date like "'.$date1.'%" ';
   }
}


 $table.= '<script type="text/javascript">

 jQuery(document).ready(function($)

  {

   var table = $("#table_reportPurFact").dataTable({
   rowReorder: {
            selector: "td:nth-child(2)"
        },

      responsive: true,
      pageLength: 50,
      dom: "Bfrtip",
      bSort: false,
      
      scrollY: "200px",
      scrollCollapse: true,

        buttons: [

          {

          extend: "excelHtml5",

          text: "Exportar",

          title: "Reporte_Facturas_de_Compras",

           
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
{column_number : 2},
{column_number : 3}
],
{cumulative_filtering: true}); 

});


  </script>



  <table id="table_reportPurFact" class="display nowrap table table-condensed table-striped table-bordered" cellspacing="0" >

    <thead>
      <tr>
        <th width="10%">Ref. </th>
        <th width="20%">Proveedor</th>
        <th width="10%">No. Factura</th>
        <th width="10%">Fecha</th>
        <th width="10%">Total</th>
        <th width="30%">Nota</th>
        <th width="10%">Estado</th>
      </tr>
    </thead>
   <tbody>';

  

    $fact = $this->model->Get_fact_header($sort,$limit,$clause);

    foreach ($fact as $value) {
     
    $value = json_decode($value);


    if($value->{'Error'}=='1') { 

     $status= "Error : ".$value->{'ErrorPT'};
     $style="style='color:red;'"; 

    } else{

      if($value->{'Enviado'}!="1"){

        $style="style='color:orange;'"; 
        $status='Por Procesar'; 

       }else{ 

          $status= "Sincronizado el: ".$value->{'Export_date'};
          $style="style='color:green;'";

         }   

      }

     

     $date = strtotime($value->{'Date'});
     $date = date('Y-m-d',$date);

     $REF =  str_pad($value->{'TransactionID'}, 9 ,"0",STR_PAD_LEFT);

     $ID = "'".$value->{'TransactionID'}."'";
     $URL = "'".URL."'";

     $table .= " <tr>
               <td >".'<a href="#"  onclick="javascript: show_fact('.$URL.','.$ID.');"  ><strong>'.$REF.'</strong></a>'.'</td>
               <td >'.$value->{'VendorName'}.'</td>
               <td >'.$value->{'PurchaseNumber'}.'</td>
               <td class="numb">'.date('m/d/Y',strtotime($value->{'Date'})).'</td>
               <td class="numb">'.number_format($value->{'Net_due'},2,'.',',').'</td>
               <td >'.$value->{'nota'}.'</td>
               
               <td '. $style.'>'.$status.'</td>
               </tr>';

     
    }

   
    $table .= '</tbody></table><div class="separador col-lg-12"></div><div id="info"></div>';

    break;
     

   /*  case "Compras":

         $query = ''; 

        break;*/



}



echo $table;





}



///////////////////////////////SALESS!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!


public function Get_Sales($sort,$limit,$date1,$date2){


$this->SESSION();


$clause='';

$clause.= 'where Sales_Header_Imp.ID_compania="'.$this->model->id_compania.'" ';



if($date1!=''){

   if($date2!=''){

      $clause.= ' and date between "'.$date1.'" and "'.$date2.'" ';           
    }
   
   if($date2==''){ 

     $clause.= ' and date="'.$date1.'"';
   }
     
}



if($this->model->active_user_role=='admin'){

$query ='SELECT * FROM `Sales_Header_Imp` 
inner JOIN `Sales_Detail_Imp` ON Sales_Header_Imp.InvoiceNumber = Sales_Detail_Imp.InvoiceNumber
inner JOIN `SAX_USER` ON `SAX_USER`.`id` = Sales_Header_Imp.user '.$clause.' GROUP BY Sales_Header_Imp.InvoiceNumber order by LAST_CHANGE '.$sort.' limit '.$limit ; }

if($this->active_user_role=='user'){

  if($clause!=''){ $clause.= 'and `SAX_USER`.`id`="'.$this->active_user_role_id.'"'; } else{ $clause.= ' Where `SAX_USER`.`id`="'.$this->active_user_role_id.'"'; }

$query='SELECT * FROM `Sales_Header_Imp`
inner JOIN `Sales_Detail_Imp` ON Sales_Header_Imp.InvoiceNumber = SalesOrder_Detail_Imp.InvoiceNumber
inner JOIN `SAX_USER` ON `SAX_USER`.`id` = Sales_Header_Imp.user '.$clause.' GROUP BY SalesOrder_Header_Imp.InvoiceNumber  order by LAST_CHANGE '.$sort.' limit '.$limit;

}

       
$table.= '<script type="text/javascript">
  jQuery(document).ready(function($)
  {
   var table = $("#table_report").dataTable({
      bSort: false,
      
      aLengthMenu: [
        [5,10, 25,50,-1], [5,10, 25, 50,"All"]
      ]
    });

table.yadcf([

{column_number : 2},
{column_number : 3},
{column_number : 4},
{column_number : 5},

]);
   
      
  });
  </script>
  <table id="table_report" class="tableReport table table-striped" cellspacing="0"  >
    <thead>
      <tr>
        <th>No. Orden</th>
        <th>Fecha</th>
        <th>Cliente</th>
        <th width="15%">Total Venta</th>
        <th>Procesado por:</th>
        <th>Estado</th>
        <th>Detalle</th>
      </tr>
    </thead>
    <tfoot>
      <tr>
        <th>No. Orden</th>
        <th>Fecha</th>
        <th>Cliente</th>
        <th>Total Venta</th>
        <th>Procesado por:</th>
        <th>Estado</th>
        <th>Detalle</th>
      </tr>
    </tfoot>';



$filter =  $this->model->Query($query);

$URL ='"'.URL.'"';

foreach ($filter as $datos) {

  $filter = json_decode($datos);


  $ID ='"'.$filter->{'InvoiceNumber'}.'"';

   if($filter->{'Error'}==1) { 

     $status= "Error : ".$filter->{'ErrorPT'}. 'Cancelado';
     $style="style='color:red; font-style:bold;'"; 


   } else{

    if($filter->{'Enviado'}==0){

      $style="style='color:orange; font-style:bold;'"; 
      $status='No sincronizado';

       }else{ 

         $status= "Enviado";
         $style="style='color:green; font-style:bold;'";

       }   

    }



$user = $this->model->Get_User_Info($filter->{'user'}); 

foreach ($user as $value) {
$value = json_decode($value);
$name= $value->{'name'};
$lastname = $value->{'lastname'};
}


$table.= "<tr>
    <td >".$filter->{'InvoiceNumber'}."</td>
    <td >".$filter->{'date'}."</td>
    <td >".$filter->{'CustomerName'}.'</td>
    <td width="15%">'.$filter->{'Net_due'}.'</td>
    <td >'.$name.' '.$lastname.'</td>
    <td '.$style.'>'.$status."</td>
    <td ><a href='#' onclick='javascript: show_invoice(".$URL.",".$ID."); ' ><i style='color:blue' class='fa fa-search'></i></a>   </td>
   </tr>";

}

$table.= '</table>';

echo $table;

}



/////////////////////////////////////////////////////////////////////////////////////////////////////////////
// SALIDA DE MERCANCIA

public function Get_SalMerc($sort,$limit,$date1,$date2){

$this->SESSION();



$clause='';

$clause.= 'where InventoryAdjust_Imp.ID_compania="'.$this->model->id_compania.'" ';



if($date1!=''){

   if($date2!=''){

      $clause.= ' and Date between "'.$date1.'" and "'.$date2.'" ';           
    }
   
   if($date2==''){ 

     $clause.= ' and Date ="'.$date1.'"';
   }
     
}



if($this->model->active_user_role=='admin'){

$query ='SELECT * FROM `InventoryAdjust_Imp` 
inner JOIN `status_location` ON status_location.id = InventoryAdjust_Imp.location_id
inner JOIN `SAX_USER` ON `SAX_USER`.`id` = InventoryAdjust_Imp.USER '.$clause.' 
and   status_location.ID_compania="'.$this->model->id_compania.'"
GROUP BY InventoryAdjust_Imp.Reference order by InventoryAdjust_Imp.Reference '.$sort.' limit '.$limit ;

 }

if($this->active_user_role=='user'){

  if($clause!=''){ $clause.= 'and `SAX_USER`.`id`="'.$this->active_user_role_id.'"'; } else{ $clause.= ' Where `SAX_USER`.`id`="'.$this->active_user_role_id.'"'; }

$query ='SELECT * FROM `InventoryAdjust_Imp` 
inner JOIN `status_location` ON status_location.id = InventoryAdjust_Imp.location_id
inner JOIN `SAX_USER` ON `SAX_USER`.`id` = InventoryAdjust_Imp.USER '.$clause.' 
and   status_location.ID_compania="'.$this->model->id_compania.'"
GROUP BY InventoryAdjust_Imp.Reference order by Date '.$sort.' limit '.$limit ;

}

       
$table.= '<script type="text/javascript">
  jQuery(document).ready(function($)
  {
   var table = $("#table_report").dataTable({
      bSort: false,
      
      aLengthMenu: [
        [5,10, 25,50,-1], [5,10, 25, 50,"All"]
      ]
    });

table.yadcf([

{column_number : 2},
{column_number : 3},
{column_number : 4},
{column_number : 5},

]);
   
      
  });
  </script>
  <table id="table_report" class="tableReport table table-striped" cellspacing="0"  >
    <thead>
      <tr>
        <th>No. Referencia</th>
        <th>Fecha</th>
        <th>Descripcion</th>
        <th>Proyecto</th>
        <th>Procesado por:</th>
        <th>Estado</th>
        <th width="5%">Detalle</th>
      </tr>
    </thead>
    <tfoot>
      <tr>
        <th>No. Referencia</th>
        <th>Fecha</th>
        <th>Descripcion</th>
        <th>Proyecto</th>
        <th>Procesado por:</th>
        <th>Estado</th>
        <th>Detalle</th>
      </tr>
    </tfoot>';



$filter =  $this->model->Query($query);

$URL ='"'.URL.'"';

foreach ($filter as $datos) {

  $filter = json_decode($datos);


  $ID ='"'.$filter->{'Reference'}.'"';

   if($filter->{'Error'}==1) { 

     $status= "Error : ".$filter->{'ErrorPT'}. 'Cancelado';
     $style="style='color:red; font-style:bold;'"; 


   } else{

    if($filter->{'Enviado'}==0){

      $style="style='color:orange; font-style:bold;'"; 
      $status='No sincronizado';

       }else{ 

         $status= "Enviado";
         $style="style='color:green; font-style:bold;'";

       }   

    }



$user = $this->model->Get_User_Info($filter->{'USER'}); 

foreach ($user as $value) {
$value = json_decode($value);
$name= $value->{'name'};
$lastname = $value->{'lastname'};
}

$resp = '"'.$name.' '.$lastname.'"';

$table.= "<tr>
    <td >".$filter->{'Reference'}."</td>
    <td >".$filter->{'Date'}."</td>
    <td >".$filter->{'ReasonToAdjust'}.'</td>
    <td >'.$filter->{'JobID'}.'</td>
    <td >'.$name.' '.$lastname.'</td>
    <td '.$style.'>'.$status."</td>
    <td ><a href='#' onclick='javascript: show_invadj(".$URL.",".$ID.",".$resp."); ' ><i style='color:blue' class='fa fa-search'></i></a>   </td>
   </tr>";

}

$table.= '</table>';

echo $table;


}





public function set_sales_header($ID_compania,$SalesOrderNumber,$CustomerID,$Subtotal,$TaxID,$Net_due,$user){


$this->SESSION();


$SalesOrderNumber = trim(preg_replace('/000+/','',$SalesOrderNumber));



$values = array(
'ID_compania'=>$ID_compania,
'InvoiceNumber'=>$SalesOrderNumber,
'CustomerID'=>$this->model->Query_value('Customers_Exp','CustomerID','Where ID="'.$CustomerID.'";'),
'CustomerName'=>$this->model->Query_value('Customers_Exp','Customer_Bill_Name','Where ID="'.$CustomerID.'";'),
'Subtotal'=>$Subtotal,
'TaxID'=>$TaxID,
'Net_due'=>$Net_due,
'user'=>$user,
'date'=>date("Y-m-d"),
'saletax'=>$this->model->Query_value('sale_tax','rate','Where taxid="'.$TaxID.'";')
);


//echo var_dump($values);

$this->model->insert('Sales_Header_Imp',$values);

}

public function set_sales_detail($ID_compania,$itemid,$unit_price,$SalesOrderNumber,$qty,$Price,$lote,$ruta,$venc,$arrLen,$count){


$SalesOrderNumber = trim(preg_replace('/000+/','',$SalesOrderNumber));

//IF ITEMS EXIST
$clause='where Item_id="'.$itemid.'" and InvoiceNumber="'.$SalesOrderNumber.'";';

$ID = $this->model->Query_value('Sales_Detail_Imp','ID',$clause);



if ($ID==''){


      $values1 = array(
      'ID_compania'=>$ID_compania,
      'invoiceNumber'=>$SalesOrderNumber,
      'Item_id'=>$itemid,
      'Description'=>$this->model->Query_value('Products_Exp','Description','Where ProductID="'.$itemid.'";'),
      'Quantity'=>$qty,
      'Unit_Price'=>$unit_price,
      'Net_line'=>$Price,
      'Taxable'=>'1');

      //echo  'insert';

      $this->model->insert('Sales_Detail_Imp',$values1); //set item line



}else{

$QUERY='SELECT Quantity, Unit_Price FROM Sales_Detail_Imp where ID="'.$ID.'";';

$QTY_PRI = $this->model->Query($QUERY);

foreach ($QTY_PRI  AS $QTY_PRI) {

$QTY_PRI = json_decode($QTY_PRI);

$now_qty = $QTY_PRI->{'Quantity'}+$qty;
$net_line= $QTY_PRI->{'Unit_Price'} * $now_qty;

}



 //echo  'qty: '.$now_qty;
 //echo  'netline: '.$net_line;

      $query= 'UPDATE Sales_Detail_Imp SET Quantity="'.$now_qty.'" , Net_line="'.$net_line.'" where ID="'.$ID.'";';

    //  echo  'query: '.$query;
      

      $this->model->Query($query);


}



$values2 = array(
'ID_compania'=>$ID_compania,
'InvoiceNumber'=>$SalesOrderNumber,
'Item_id'=>'',
'Description'=>'Lote : '.$lote.' / Venc. :'.$venc. '/ Ubicacion: '.$ruta,
'Quantity'=>'0',
'Unit_Price'=>'0',
'Net_line'=>'0',
'Taxable'=>'1');



$this->model->insert('Sales_Detail_Imp',$values2);//set lote line


$ruta = $this->model->Query_value('ubicaciones','id','Where etiqueta="'.$ruta.'";');


$values3 = array(
'SaleOrderId'=>$SalesOrderNumber,
'no_lote'=>$lote,
'ProductID'=>$itemid,
'qty'=>$qty,
'status_pendding' => '1',
'status_location_id' => $this->model->Query_value('status_location','id','Where id_product="'.$itemid.'" and lote="'.$lote.'" and route="'.$ruta.'" and ID_compania="'.$this->model->id_compania.'";')
);



$this->model->insert('sale_fact_pendding',$values3);



//UBICO LA CANTIDAD ACTUAL EN STATUS_LOCATION
$CURRENT_QTY = $this->get_any_lote_qty($lote,$itemid,$ruta);

//ACTUALIZO LA CANTIDAD EN LA UBICCION DEFAULT
$QTY_TO_SET = $CURRENT_QTY - $qty;

$query= 'UPDATE status_location SET qty="'.$QTY_TO_SET.'" where lote="'.$lote.'" and id_product="'.$itemid.'" and route="'.$ruta.'" and ID_compania="'.$this->model->id_compania.'"';

$res = $this->model->Query($query);

//$this->clear_lotacion_register();
if($count==$arrLen){ echo '1';}else{ echo '0';}
}



public function SET_NO_LOTE($item,$no_lote,$qty,$fecha){


$this->SESSION();


//Verifico No_lote si existe
$lote = $this->model->Query_value('Prod_Lotes','no_lote','Where no_lote="'.$no_lote.'" and ID_compania="'.$this->model->id_compania.'"');

if($lote==''){ 


//Actualizo la cantidad en el lote default
$now_qty = $this->model->Query_value('status_location','sum(qty)','Where lote="'.$item.'0000" and ID_compania="'.$this->model->id_compania.'"');

$qty_to_up = $now_qty - $qty;


$query= 'UPDATE status_location SET qty="'.$qty_to_up.'" where lote="'.$item.'0000" and route="1" and stock="1" and ID_compania="'.$this->model->id_compania.'"';
$res = $this->model->Query($query);




//Agrego nuevo lote
$value  = array(
  'ProductID' => $item ,
  'no_lote' => $no_lote ,
  'lote_qty' => '' ,
  'fecha_ven' => $fecha,
  'REG_KEY' => uniqid(),
  'ID_compania' => $this->model->id_compania );


$res = $this->model->insert('Prod_Lotes',$value);

//Agrego ubicacion de nuevo lote por default
$value  = array(
  'id_product' => $item ,
  'lote' => $no_lote ,
  'qty' => $qty ,
  'stock' => '1',
  'route' => '1',
  'ID_compania' => $this->model->id_compania );


$res = $this->model->insert('status_location',$value);



}else{


echo 'El No de Lote ya existe, por favor elija otro nombre';


}






}


public function erase_lote($no_lote,$qty){


$this->SESSION();



$item = $this->model->Query_value('Prod_Lotes','ProductID','Where no_lote="'.$no_lote.'" and ID_compania="'.$this->model->id_compania.'"');


//Actualizo la cantidad en el lote default
$now_qty = $this->model->Query_value('status_location','qty','Where lote="'.$item.'0000" and stock="1" and route="1" and ID_compania="'.$this->model->id_compania.'";');

$qty_to_up = $now_qty + $qty;

$query= 'UPDATE status_location SET qty="'.$qty_to_up.'" where lote="'.$item.'0000" and route="1" and stock="1" and ID_compania="'.$this->model->id_compania.'"';
$res = $this->model->Query($query);


$this->model->Query('DELETE FROM Prod_Lotes WHERE no_lote="'.$no_lote.'" and ID_compania="'.$this->model->id_compania.'"');
$this->model->Query('DELETE FROM status_location WHERE lote="'.$no_lote.'" and ID_compania="'.$this->model->id_compania.'"');

}


public function del_tax($id){


$this->model->Query('delete from sale_tax Where id="'.$id.'";');

}


public function get_ProductsCode(){

$this->SESSION();

$sql = 'SELECT ProductID FROM Products_Exp WHERE id_compania="'.$this->model->id_compania.'"';

$Codigos = $this->model->Query($sql);

foreach ($Codigos as $value) {

  $value = json_decode($value);
   
  $codes .= '<option value="'.$value->{'ProductID'}.'">'.$value->{'ProductID'}.'</option>';

 } 

echo $codes;

}


//REQUISICIONES//////////////////////////////////////////////////////////////////////////////////////////////////////////
public function set_req_header($JobID,$nota,$flag){
$this->SESSION();

$Req_NO = $this->model->Get_Req_No($JobID);

$value_to_set  = array( 
  'NO_REQ' => $Req_NO, 
  'job'  => $JobID, 
  'ID_compania' => $this->model->id_compania, 
  'NOTA' => $nota , 
  'USER' => $this->model->active_user_id, 
  'DATE' => date("Y-m-d"),
  'isUrgent' => $flag  
  );

$res = $this->model->insert('REQ_HEADER',$value_to_set);

ECHO $Req_NO;

}

public function set_req_items($ID,$DESC,$QTY,$UNIT,$JOB_ID,$JOB_DESC,$PHASE_ID,$PHASE_DESC,$COST_ID,$COST_DESC,$Req_NO,$COUNT,$ARRLENG){
$this->SESSION();

//$Req_NO = $this->model->Get_Req_No($JOB_ID);

$value_to_set  = array( 
  'ProductID' => $ID, 
  'DESCRIPCION' => $DESC,
  'CANTIDAD' => $QTY,  
  'UNIDAD' => $UNIT,  
  'JOB' => $JOB_ID,  
  'PHASE' => $PHASE_ID,    
  'NO_REQ' => $Req_NO, 
  'ITEM_UNIQUE_NO' => $ID.'@'.$Req_NO.'@'.$ITEMID.'@'.$this->model->id_compania,
  'ID_compania' => $this->model->id_compania
  );


$res = $this->model->insert('REQ_DETAIL',$value_to_set);

if($COUNT==$ARRLENG){ //SI LOS ITEMS PROCESADOS CONTABILIZADOS CON count ES IGUAL EL NUMERO DE LINEAS EN EL ARRAY (ARRLENG) entonces devuelve 0 para terminar el proceso de insesion de registros
  echo '1'; 
}else{ 
  echo '0';
} 

}

public function set_req_items_test($NO_REQ){
$this->SESSION();

$data = json_decode($_GET['Data']);

foreach ($data as $value) {


  list($null,$ITEMID, $DESC, $QTY, $UNIT, $JOB_ID, $PROY_DESC, $PHASE_ID, $PHASE_DESC ) = explode('@', $value );
   

  $value_to_set  = array( 
    'ProductID' => $ITEMID, 
    'DESCRIPCION' => $DESC,
    'CANTIDAD' => $QTY,  
    'UNIDAD' => $UNIT,  
    'JOB' => $JOB_ID,  
    'PHASE' => $PHASE_ID,    
    'NO_REQ' => $NO_REQ, 
    'ITEM_UNIQUE_NO' => $ITEMID.'@'.$NO_REQ.'@'.$ITEMID.'@'.$this->model->id_compania,
    'ID_compania' => $this->model->id_compania
    );

   //var_dump( $value_to_set);
  
   $res = $this->model->insert('REQ_DETAIL',$value_to_set);

}

echo '1';

}

public function get_req_status($id){
$this->SESSION();
$i=0;

$total_comprado = $this->model->Query_value('PurOrdr_Header_Exp','sum(PurOrdr_Detail_Exp.Quantity)','
                                              INNER JOIN PurOrdr_Detail_Exp ON PurOrdr_Header_Exp.TransactionID = PurOrdr_Detail_Exp.TransactionID
                                              INNER JOIN REQ_DETAIL ON REQ_DETAIL.NO_REQ = PurOrdr_Header_Exp.CustomerSO
                                              AND REQ_DETAIL.ProductID = PurOrdr_Detail_Exp.Item_id
                                              WHERE PurOrdr_Header_Exp.CustomerSO =  "'.$id.'"
                                              AND PurOrdr_Header_Exp.ID_compania =  "'.$this->model->id_compania.'"
                                              AND PurOrdr_Header_Exp.PurchaseOrderNumber <> ""');

/*repara el caso en el que en una OC se haya pedido mas de lo solicitado*/
$SQL = 'SELECT PurOrdr_Detail_Exp.Quantity, 
               PurOrdr_Detail_Exp.Item_id
          FROM PurOrdr_Header_Exp
          INNER JOIN PurOrdr_Detail_Exp ON PurOrdr_Header_Exp.TransactionID = PurOrdr_Detail_Exp.TransactionID
          INNER JOIN REQ_DETAIL ON REQ_DETAIL.NO_REQ = PurOrdr_Header_Exp.CustomerSO
          AND REQ_DETAIL.ProductID = PurOrdr_Detail_Exp.Item_id
          WHERE PurOrdr_Header_Exp.CustomerSO =  "'.$id.'"
          AND PurOrdr_Header_Exp.ID_compania =  "'.$this->model->id_compania.'"
          AND PurOrdr_Header_Exp.PurchaseOrderNumber <> ""';

$total_comprado_by_item = $this->model->Query($SQL);

foreach ($total_comprado_by_item  as $value) {
       
        $value = json_decode($value);

        $QTY_BY_ITEM = $this->model->Query_value('REQ_DETAIL','CANTIDAD ', 'WHERE NO_REQ="'.$id.'" 
                                                              AND ProductID="'.$value->{'Item_id'}.'" 
                                                              AND ID_compania =  "'.$this->model->id_compania.'";');
  
        $REST_BY_ITEM = $QTY_BY_ITEM - $value->{'Quantity'};

        if($REST_BY_ITEM < 0){

          $i += (-1)*$REST_BY_ITEM ;
        }

}



$total_comprado = $total_comprado - $i;



$total_REQ = $this->model->Query_value('REQ_DETAIL','sum(CANTIDAD)','WHERE ID_compania="'.$this->model->id_compania.'" and NO_REQ="'.$id.'"');

$total_restante = $total_REQ - $total_comprado; 


//ECHO '<BR>'.$total_restante;
return $total_restante;
}

public function get_req_ord($id){
$this->SESSION();


//saco estatus de REQUISICION
$sql_total = 'SELECT 
sum(PurOrdr_Detail_Exp.Quantity) as TOTAL_COMPRADO
FROM PurOrdr_Header_Exp
INNER JOIN PurOrdr_Detail_Exp ON PurOrdr_Header_Exp.TransactionID = PurOrdr_Detail_Exp.TransactionID
INNER JOIN REQ_DETAIL ON REQ_DETAIL.NO_REQ = PurOrdr_Header_Exp.CustomerSO
AND REQ_DETAIL.ProductID = PurOrdr_Detail_Exp.Item_id
WHERE PurOrdr_Header_Exp.CustomerSO =  "'.$id.'"
AND PurOrdr_Header_Exp.ID_compania =  "'.$this->model->id_compania.'"
AND PurOrdr_Header_Exp.PurchaseOrderNumber <> ""';

$sql_TOTAL_COMPRADO = $this->model->Query($sql_total);


foreach ($sql_TOTAL_COMPRADO as $value) {
  $value =  json_decode($value);

  $total_comprado = $value->{'TOTAL_COMPRADO'};

  }

return $total_comprado;
}


public function req_status($id){
$this->SESSION(); 
//////////////////////////////////////////////////ESTADO DEL PROCESO DE LA REQUISICION //////////////////////////////////////////////////////////

//STATUS INICIAL
$status = 'POR COTIZAR';

//CHECHO SI COTIZACION HA SIDO INICIADA 
$chk_quota = $this->model->Query_value('REQ_QUOTA','ID',' WHERE NO_REQ = "'.$id.'" AND
                                                                ID_compania =  "'.$this->model->id_compania.'"');

if($chk_quota){
    $status = 'COTIZANDO';
}


//CHECO ORDENES DE COMPRAS ASOCIADAS
$chk_po =$this->model->Query_value('PurOrdr_Header_Exp','PurOrdr_Header_Exp.TransactionID',' INNER JOIN PurOrdr_Detail_Exp ON PurOrdr_Header_Exp.TransactionID = PurOrdr_Detail_Exp.TransactionID
                                                                          INNER JOIN REQ_DETAIL ON REQ_DETAIL.NO_REQ = PurOrdr_Header_Exp.CustomerSO
                                                                          AND REQ_DETAIL.ProductID = PurOrdr_Detail_Exp.Item_id
                                                                          WHERE PurOrdr_Header_Exp.CustomerSO =  "'.$id.'"
                                                                          AND PurOrdr_Header_Exp.ID_compania =  "'.$this->model->id_compania.'"
                                                                          AND PurOrdr_Header_Exp.PurchaseOrderNumber <> ""');

/*$chk_po =$this->model->Query_value('PurOrdr_Header_Exp','TransactionID',' WHERE PurOrdr_Header_Exp.CustomerSO =  "'.$id.'"
                                                                          AND PurOrdr_Header_Exp.ID_compania =  "'.$this->model->id_compania.'"
                                                                          AND PurOrdr_Header_Exp.PurchaseOrderNumber <> ""');*/

if($chk_po){
    $status = 'PARCIALMENTE ORDENADO';
}


//CHECO TOTAL RESTANTE 
$total_restante = $this->get_req_status($id);

if($total_restante == 0 ){
    
    $status = 'ORDENADO';

    //TOTAL ORDENADO 
    $TOTAL_ORDENADO = $this->get_req_ord($id);

    //CHECO TOTAL RECIBIDO
    $totel_reciv = $this->model->Query_value('REQ_RECEPT','SUM(QTY)','WHERE  ID_compania="'.$this->model->id_compania.'" 
                                                                             AND NO_REQ="'.$id.'"');

    $rev_ord = $TOTAL_ORDENADO - $totel_reciv;

    if($TOTAL_ORDENADO > 0){

       if($rev_ord==0){
           $status = 'FINALIZADO';
       }

    }

}




//CHECO SI ESTA CERRADA FORZOSAMENTE
$chk_closed = $this->model->Query_value('REQ_HEADER','st_closed','WHERE  ID_compania="'.$this->model->id_compania.'" 
                                                                         AND NO_REQ="'.$id.'"');
if($chk_closed =='1'){

  $status = 'CERRADA';

}

return $status;

}


public function req_item_status($id,$item,$qty){

$this->SESSION(); 
//////////////////////////////////////////////////ESTADO DEL PROCESO DE LA REQUISICION //////////////////////////////////////////////////////////

//STATUS INICIAL
$status = 'POR COTIZAR';

//CHECHO SI COTIZACION HA SIDO INICIADA 
$chk_quota = $this->model->Query_value('REQ_QUOTA','ID',' WHERE NO_REQ = "'.$id.'" AND
                                                                ID_compania =  "'.$this->model->id_compania.'"');

if($chk_quota){
    $status = 'COTIZANDO';
}

//CHECO ORDENES DE COMPRAS ASOCIADAS
/*$chk_po =$this->model->Query_value('PurOrdr_Header_Exp','TransactionID',' WHERE PurOrdr_Header_Exp.CustomerSO =  "'.$id.'"
                                                                           AND PurOrdr_Header_Exp.ID_compania =  "'.$this->model->id_compania.'"');*/
 $sql_OC = 'SELECT 
  PurOrdr_Header_Exp.PurchaseOrderNumber,
  PurOrdr_Detail_Exp.Quantity
  FROM PurOrdr_Header_Exp
  INNER JOIN PurOrdr_Detail_Exp ON PurOrdr_Header_Exp.TransactionID = PurOrdr_Detail_Exp.TransactionID
  WHERE PurOrdr_Header_Exp.CustomerSO =  "'.$id.'"
  AND PurOrdr_Header_Exp.ID_compania =  "'.$this->model->id_compania.'"
  AND PurOrdr_Detail_Exp.Item_id = "'.$item.'"
  AND PurOrdr_Header_Exp.PurchaseOrderNumber <> ""';

  $INFO_OC = $this->model->Query($sql_OC);

  $QTY_TOTAL=0;

  foreach ($INFO_OC as $datos) {

      $INFO_OC = json_decode($datos);
      $QTY_TOTAL += $INFO_OC->{'Quantity'};

    }

if($INFO_OC){
    $status = 'PARCIALMENTE ORDENADO';
 }


//CHECO TOTAL RESTANTE 
//TOTEL RECIVIDO POR ITEM
$total_reciv = $this->model->Query_value('REQ_RECEPT','SUM(QTY)','WHERE  ID_compania="'.$this->model->id_compania.'" 
                                                                         AND NO_REQ="'.$id.'"
                                                                         AND ITEM="'.$item.'"');
//TOTAL ORDENADO POR ITEM
$TOTAL_ORDENADO = $QTY_TOTAL;

//TOTAL RESTANTE POR ORDENAR
$total_restante = $qty - $TOTAL_ORDENADO;


if($total_restante == 0 ){
    
    $status = 'ORDENADO';


    $rev_ord = $TOTAL_ORDENADO - $total_reciv;

    if($TOTAL_ORDENADO > 0){

       if($rev_ord==0){
           $status = 'FINALIZADO';
       }

    }

}elseif ($total_restante < 0) {
  

    $status = 'SOBREORDENADO';

     $rev_ord = $TOTAL_ORDENADO - $total_reciv;

    if($TOTAL_ORDENADO > 0){

       if($rev_ord==0){
           $status = 'FINALIZADO';
       }

    }


}


if ($total_reciv > 0 && $status <> 'FINALIZADO'){

  $status .= ' / RECEPCION PARCIAL';

}


//CHECO SI ESTA CERRADA FORZOSAMENTE
/*$chk_closed = $this->model->Query_value('REQ_HEADER','st_closed','WHERE  ID_compania="'.$this->model->id_compania.'" 
                                                                         AND NO_REQ="'.$id.'"');
if($chk_closed =='1'){

  $status = 'CERRADA';

}*/

return $status;



}


public function get_req_info($id){
$this->SESSION();


$ORDER_detail = $this->model->get_req_to_report('DESC','1','WHERE REQ_HEADER.ID_compania="'.$this->model->id_compania.'" AND  REQ_HEADER.ID_compania="'.$this->model->id_compania.'" and REQ_HEADER.NO_REQ="'.$id.'" and REQ_DETAIL.NO_REQ="'.$id.'"');

echo '<script>

var table = $("#table_info").dataTable({

       rowReorder: {
            selector: "td:nth-child(2)"
        },

      bSort: false,
      select:true,
      scrollX: "100%",
      scrollCollapse: true,
      responsive: false,
      searching: false,
      paging:    false,
      info:      false });


</script>';

echo '<br/><br/>
<button type="button" class="close" aria-label="Close" onclick="CLOSE_DIV('."'info'".');" >
          <span STYLE="color:red" aria-hidden="true">&times; </span> Cerrar
          </button>
<fieldset><legend>Detalle de Requisición</legend>
<table  class="display nowrap table table-striped table-bordered" cellspacing="0"  ><tbody>';

  foreach ($ORDER_detail as $datos) {
    $ORDER_detail = json_decode($datos);


$user = $this->model->Get_User_Info($ORDER_detail->{'USER'}); 

foreach ($user as $value) {
$value = json_decode($value);
$name= $value->{'name'};
$lastname = $value->{'lastname'};
}


//obtengo estatus de la requisicion
$status_gen = $this->req_status($id);

switch ($status_gen) {
  case 'CERRADA':
     $style = 'style="background-color:#D8D8D8 ;"';//verder
    break;
  case 'FINALIZADO':
     $style = 'style="background-color:#BCF5A9;"';//verder
    break;
  case 'ORDENADO':
     $style = 'style="background-color:#F2F5A9;"';//AMARILLO
    break;
  case 'PARCIALMENTE ORDENADO':
     $style = 'style="background-color:#F3E2A9;"';//NARANJA
    break;
  case 'COTIZANDO':
     $style = 'style="background-color:#F7BE81;"';//NARANJA
    break; 
  case 'POR COTIZAR':
     $style = 'style="background-color:#F5A9A9;"';//ROJO
    break; 

}




echo     "<tr><th style='text-align:left;' ><strong>No. Req</strong></th><td class='InfsalesTd order'>".$ORDER_detail->{'NO_REQ'}."</td><tr>
          <tr><th style='text-align:left;'><strong>Fecha</strong></th><td class='InfsalesTd'>".$ORDER_detail->{'DATE'}."</td><tr>
          <tr><th style='text-align:left;'><strong>Solicitado por:</strong></th><td class='InfsalesTd'>".$name.' '.$lastname.'</td><tr>
          <tr><th style="text-align:left;" ><strong>Estado</strong></th><td '.$style.' class="InfsalesTd">'.$status_gen.'</td><tr>';

}


echo "</tbody></table>";



$ORDER= $this->model->get_req_to_print($id);

echo '<table id="table_info" class="table table-striped table-bordered" cellspacing="0">
      <thead>
        <tr>
          <th width="5%">Renglon</th>
          <th width="10%">Descripcion</th>
          <th width="5%">Unidad</th>
          <th width="10%">Fase</th>
          <th width="5%">Cant. Requerida</th>
          <th width="5%">Cant. Ordenada</th>
          <th width="5%">Cant. Por Ordenar</th>
          <th width="5%">Cant. Recibida</th>
          <th width="20%">OC asociadas (Cant. Ordenada)</th>
          <th width="20%">Estado</th>
        </tr>
      </thead><tbody>';



foreach ($ORDER as $datos) {

$ORDER = json_decode($datos);


//Informacion de ORDEN DE COMPRA PARA ESTE PRODUCTO EN LA REQUISICION
  $sql_OC = 'SELECT 
  PurOrdr_Header_Exp.PurchaseOrderNumber,
  sum(PurOrdr_Detail_Exp.Quantity) as Quantity
  FROM PurOrdr_Header_Exp
  INNER JOIN PurOrdr_Detail_Exp ON PurOrdr_Header_Exp.TransactionID = PurOrdr_Detail_Exp.TransactionID
  WHERE PurOrdr_Header_Exp.CustomerSO =  "'.$ORDER_detail->{'NO_REQ'}.'"
  AND PurOrdr_Header_Exp.ID_compania =  "'.$this->model->id_compania.'"
  AND PurOrdr_Detail_Exp.Item_id = "'.$ORDER->{'ProductID'}.'"
  AND PurOrdr_Header_Exp.PurchaseOrderNumber <> ""';

  $INFO_OC = $this->model->Query($sql_OC);

  $QTY_TOTAL=0;

  unset($Qty_Comprada);

  foreach ($INFO_OC as $datos) {

      $INFO_OC = json_decode($datos);
      $Qty_Comprada[$INFO_OC->{'PurchaseOrderNumber'}] = $INFO_OC->{'Quantity'};
       $QTY_TOTAL += $INFO_OC->{'Quantity'};

    }


//INI STATUS POR ITEM/////////////////////////////////////////////////////////////////////////////////////////////
$status = $this->req_item_status($ORDER_detail->{'NO_REQ'},$ORDER->{'ProductID'},$ORDER->{'CANTIDAD'});


switch ($status) {
  case 'CERRADA':
     $style_row = 'style="background-color:#D8D8D8;"';//verder
    break;
  case 'FINALIZADO':
     $style_row = 'style="background-color:#BCF5A9;"';//verder
    break;
  case 'ORDENADO':
     $style_row = 'style="background-color:#F2F5A9;"';//AMARILLO
    break;
  case 'SOBREORDENADO':
     $style_row = 'style="background-color:#F2F5A9;"';//AMARILLO
    break;
  case 'ORDENADO / RECEPCION PARCIAL':
      $style_row = 'style="background-color:#F2F5A9;"';//AMARILLO
    break; 
  case 'PARCIALMENTE ORDENADO':
     $style_row = 'style="background-color:#F3E2A9;"';//NARANJA
    break;
  case 'PARCIALMENTE ORDENADO / RECEPCION PARCIAL':
      $style_row = 'style="background-color:#F3E2A9;"';//NARANJA
    break; 
  case 'COTIZANDO':
     $style_row= 'style="background-color:#F7BE81;"';//NARANJA
    break; 
  case 'POR COTIZAR':
     $style_row = 'style="background-color:#F5A9A9;"';//ROJO
    break; 


}


//Informacion de ORDEN DE COMPRA PARA ESTE PRODUCTO EN LA REQUISICION
$oc_list='';

foreach ($Qty_Comprada as $key => $value) {

//$oc_list .='<a href="'.URL.'index.php?url=ges_compras/orden_compras/'.$key.'" target="_blank" ><strong>'.$key.' ('.number_format($value,0,'.',',').')</strong></a><BR>';

$PO_NO = "'".$key."'";

$oc_list .='<a href="javascript:void(0)" onclick="get_OC('.$PO_NO.');"><strong>'.$key.' ('.number_format($value,0,'.',',').')</strong></a><BR>';

}


//cantidad recibida para este item
$qty_reciv = $this->model->Query_value('REQ_RECEPT','SUM(QTY)','WHERE  ID_compania="'.$this->model->id_compania.'" 
                                        AND NO_REQ="'.$ORDER_detail->{'NO_REQ'}.'" AND ITEM="'.$ORDER->{'ProductID'}.'"');

$QTY_FALTANTE = $ORDER->{'CANTIDAD'} - $QTY_TOTAL;

  echo  "<tr ".$style_row." >
            <td>".$ORDER->{'ProductID'}."</td>
            <td>".$ORDER->{'DESCRIPCION'}."</td>
            <td>".$ORDER->{'UNIDAD'}."</td>
            <td>".$ORDER->{'PHASE'}."</td>
            <td class='numb'>".number_format($ORDER->{'CANTIDAD'},2,'.',',')."</td>
            <td class='numb'>".number_format($QTY_TOTAL,2,'.',',')."</td>
            <td class='numb'>".number_format($QTY_FALTANTE,2,'.',',')."</td>
            <td class='numb'>".number_format($qty_reciv,2,'.',',')."</td>
            <td>".$oc_list."</td>
            <td>".$status."</td>
        </tr>";

  

  }

echo '</tbody></table>
<div style="float:right;" class="col-md-2">
<a href="'.URL.'index.php?url=ges_requisiciones/req_print/'.$ORDER->{'NO_REQ'}.'"  class="btn btn-block btn-secondary btn-icon btn-icon-standalone btn-icon-standalone-right btn-single text-left">
   <img  class="icon" src="img/Printer.png" />
  <span>Imprimir</span>
</a>
</div>';

if($ORDER_detail->{'st_closed'}=='0' && $status_gen !='FINALIZADO'){

if($this->model->rol_campo=='1'){ 
echo '<div style="float:right;" class="col-md-2">
<a href="'.URL.'index.php?url=ges_requisiciones/req_reception/'.$ORDER->{'NO_REQ'}.'"  class="btn btn-block btn-secondary btn-icon btn-icon-standalone btn-icon-standalone-right btn-single text-left">
   <img  class="icon" src="img/Box Down.png" />
  <span>Registrar entradas</span>
</a>
</div>';
}

if($this->model->rol_compras=='1' && $status_gen !='FINALIZADO'){ 

echo '<div style="float:right;" class="col-md-2">
        <a title="Cerrar Rerquisición" data-toggle="modal" data-target="#myModal" href="javascript:void(0)"  class="btn btn-block btn-secondary btn-icon btn-icon-standalone btn-icon-standalone-right btn-single text-left">
          <img  class="icon" src="img/Stop.png" />
          <span>Cerrar Requisición</span>
        </a>
      </div>';

}

if($this->model->rol_compras=='1'){ 

  if($status_gen =='POR COTIZAR'){

    echo '<div style="float:right;" class="col-md-2">
    <a href="'.URL.'index.php?url=bridge_query/set_req_quota/'.$ORDER->{'NO_REQ'}.'/'.$this->model->id_compania.'"  class="btn btn-block btn-secondary btn-icon btn-icon-standalone btn-icon-standalone-right btn-single text-left">
       <img  class="icon" src="img/Search.png" />
      <span>Iniciar Cotización</span>
    </a>
    </div>';
  }



}


}



echo '</fieldset>';



echo '</fieldset>

<div class=" separador col-lg-12"></div>

  <div class="col-lg-12">
    <fieldset>
    <legend>Historial de Movimientos</legend>
        <table class="table table-striped table-bordered" cellspacing="0" >
          <thead> 
            <th>Registro</th>
            <th>Fecha</th>
            <th>Usuario</th>            
          </thead>
          <tbody>';






$CREACION = $this->model->Query("SELECT DATE, USER FROM REQ_HEADER WHERE  ID_compania='".$this->model->id_compania."' 
                                                                              AND NO_REQ='".$ORDER_detail->{'NO_REQ'}."'");
        foreach ($CREACION as $value ){

        $value = json_decode($value);

        $date = strtotime($value->{'DATE'});
        $date = date('m/d/Y',$date );

        echo '<tr><td>Creación de Requisición</td><td class="numb" >'.$date.'</td><td>'.$this->model->Get_User_Name($value->{'USER'}).'</td></tr>';

        }

$QUOTA  = $this->model->Query("SELECT DATE, USER FROM REQ_QUOTA WHERE  ID_compania='".$this->model->id_compania."' 
                                                                         AND NO_REQ='".$ORDER_detail->{'NO_REQ'}."'");
        foreach ($QUOTA  as $value ){

        $value = json_decode($value);

        $date = strtotime($value->{'DATE'});
        $date = date('m/d/Y',$date );

        echo '<tr><td>Inicio de cotización</td><td class="numb" >'.$date.'</td><td>'.$this->model->Get_User_Name($value->{'USER'}).'</td></tr>';

        }

$TEMP_LOG[] = '';
$i=1;

$PO =  $this->model->Query('SELECT LAST_CHANGE, PurchaseOrderNumber FROM PurOrdr_Header_Exp WHERE  PurOrdr_Header_Exp.CustomerSO =  "'.$ORDER_detail->{'NO_REQ'}.'"
                                                                                        AND PurOrdr_Header_Exp.ID_compania =  "'.$this->model->id_compania.'"
                                                                                        AND PurOrdr_Header_Exp.PurchaseOrderNumber <> ""
                                                                                        ORDER BY PurOrdr_Header_Exp.LAST_CHANGE asc');

        foreach ($PO  as $value) {

        $value = json_decode($value);

        $date = strtotime($value->{'LAST_CHANGE'});

        $TEMP_LOG[$date.';PO'.$i] = 'Creación de PO en Peachtree ('.$value->{'PurchaseOrderNumber'}.');Usuario Peachtree'; 

       $i+=1;
        }

$RECEP = $this->model->Query("SELECT DATE, USER, QTY, ITEM FROM REQ_RECEPT WHERE  ID_compania='".$this->model->id_compania."' 
                                                                                  AND NO_REQ='".$ORDER_detail->{'NO_REQ'}."'
                                                                                  ORDER BY DATE ASC");
$i=1;
        foreach ($RECEP as $value ){

        $value = json_decode($value);

        $date = strtotime($value->{'DATE'});

        $TEMP_LOG[$date.';rep'.$i] = 'Recepción en almacen Item: '.$value->{'ITEM'}.' / Cant.'.$value->{'QTY'}.';'.$this->model->Get_User_Name($value->{'USER'}); 

        $i+=1;
        }

    ksort($TEMP_LOG);

    foreach($TEMP_LOG as $key => $value ){

    list($date,) = explode(';', $key);

    list($msg,$user) = explode(';', $value);

    $date = date('m/d/Y',$date);

    if($msg!=''){
     
     echo '<tr><td>'.$msg.'</td><td class="numb" >'.$date.'</td><td>'.$user.'</td></tr>';

    }

    

    }


if($status_gen=='FINALIZADO'){

$FIN = $this->model->Query_value('REQ_RECEPT','DATE',"WHERE  ID_compania='".$this->model->id_compania."' 
                                                                                  AND NO_REQ='".$ORDER_detail->{'NO_REQ'}."'
                                                                                  ORDER BY DATE DESC LIMIT 1");

 $date = strtotime($FIN);
 $date = date('m/d/Y',$date );


 echo '<tr><td>Proceso FINALIZADO </td><td class="numb" >'.$date.'</td><td>SISTEMA ACIWEB</td></tr>';


}

if($status_gen=='CERRADA'){

 $CERRADO= $this->model->Query_value('REQ_HEADER','LAST_CHANGE',"WHERE  ID_compania='".$this->model->id_compania."' 
                                                                                  AND NO_REQ='".$ORDER_detail->{'NO_REQ'}."'");
 $date = strtotime($CERRADO);
 $date = date('m/d/Y',$date );

 $MOTIVO= $this->model->Query_value('REQ_HEADER','desc_closed',"WHERE  ID_compania='".$this->model->id_compania."' 
                                                                       AND NO_REQ='".$ORDER_detail->{'NO_REQ'}."'");
 
 echo '<tr><td>CERRADO POR : '. $MOTIVO.'</td><td class="numb" >'.$date.'</td><td>SISTEMA ACIWEB</td></tr>';

}


 ECHO   '</tbody>
        </table>';

  
 ECHO  '</fieldset>
         </div>  
          <div class="separador col-lg-12"></div>
          <div id="table2" class="col-lg-12"></div>
          ';

//MODA PARA CIERRE FORZOSO DE LA REQUISICION
$id = "'".$id."'";
$MODAL = '
<!-- Modal -->
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h3 >Razón de Cierre</h3>
      </div>

      <div class="col-lg-12 modal-body">
      <!--ini Modal  body-->  
        <textarea class="textinput" rows="5" cols="70" id="req_reason_close" name="req_reason_close"></textarea>  
        <p class="help-block" >Indique la razón del cierre de la requisición</p>  
      <!--fin Modal  body-->
      </div>
      <div class="modal-footer">
        <button type="button" onclick="close_req('.$id.');" data-dismiss="modal" class="btn btn-primary" >Aceptar</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
      </div>
    </div>

  </div>
</div>



';

ECHO $MODAL;

}

public function set_reason_close($id,$reason){

$this->SESSION();

$table = 'REQ_HEADER';
$columns = array( 'st_closed'   => 1, 
                  'desc_closed' => $reason);

$clause = ' NO_REQ = "'.$id.'"
            AND ID_compania =  "'.$this->model->id_compania.'"';


$this->model->update($table,$columns,$clause);

}
  

////////////////////////////////////////////////////////////////////////////////////
//PROCESO DE ENVIO DE EMAIL (TEST)
public function send_test_mail($emailtest){

require 'PHP_mailer/PHPMailerAutoload.php';
$mail = new PHPMailer;

$mail->IsSMTP(); // enable SMTP
$mail->IsHTML(true);


$sql = "SELECT * FROM CONF_SMTP WHERE ID='1'";

$smtp= $this->model->Query($sql);

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

}



$mail->Subject = utf8_decode('Prueba de configurarión SMTP (ACI-WEB)');

$message_to_send ='<html>
<head>
<meta charset="UTF-8">
<title>Prueba de configurarión SMTP (ACI-WEB)</title>
</head>
<body>Este es un correo de prueba del sistema ACI-WEB de APCON Consulting, 
para certificar el funcionamiento de su configuracion SMTP.</body>
</html>';

$mail->Body = $message_to_send;

$mail->AddAddress($emailtest);



if(!$mail->send()) {
 

   $alert .= 'El correo no puede ser enviado.';
   $alert .= 'Error: ' . $mail->ErrorInfo;

   

} else {
  
  $alert = 'El correo de verificacion ha sido enviado';
}

echo $alert;

}


///////////////////////////////////////////////////////////////////////////////////////////////
//PROCESOD DE CONSIGNACION

public function set_consignacion_header($JobDesc,$PhaseDesc,$CostDesc,$reasonToAdj,$no_order){

$this->SESSION();

  $val_to_insert = array(
  'date'   => date('Y-m-d'),
  'idJob'  => $JobDesc, 
  'idPha'  => $PhaseDesc, 
  'idCost' => $CostDesc, 
  'nota'   => $reasonToAdj,
  'refReg' => $no_order,
  'ID_compania' => $this->model->id_compania);

  $this->model->insert('CON_HEADER',$val_to_insert);

}

public function set_con_reg_tras($idItem,$orderNum,$qty,$lote,$ruta,$caduc,$ruta_dest,$almacen_dest,$count,$arrLen){

$this->SESSION();


//RUTA ORIGEN
$route_src = $this->model->Query_value('ubicaciones','id',' where etiqueta="'.$ruta.'"');
$stock_src = $this->model->Query_value('ubicaciones','id_almacen',' where etiqueta="'.$ruta.'"');


//VERIFICO STATUS_LOCATION_ID ACTUAL
$clause = 'WHERE ID_compania="'.$this->model->id_compania.'"  and id_product="'.$idItem.'" and lote="'.$lote.'" and stock="'.$stock_src.'" and route="'.$route_src.'"';

$id_status_loc = $this->model->Query_value('status_location','id',$clause);


//ACTUALIZO LOCACION Y RREGISTRA EL MOVIMIENTO
$this->update_lote_location($ruta,'',$id_status_loc,$ruta_dest,$almacen_dest,$lote,$qty);


  if($count==$arrLen){

    $this->model->con_reg($orderNum,$arrLen,$this->model->id_compania);

    echo '0';

  }else{

    echo '1';

  }

}


public function get_con_info($id){
$this->SESSION();

$clause= 'WHERE CON_HEADER.ID_compania="'.$this->model->id_compania.'"
                 and CON_REG_TRAS.ID_compania="'.$this->model->id_compania.'"  
                 and reg_traslado.ID_compania="'.$this->model->id_compania.'"
                 and CON_HEADER.refReg="'.$id.'"';

$ORDER_detail = $this->model->get_con_to_report('DESC','1',$clause);

echo '<br/><br/><fieldset><legend>Detalle de consignación</legend><table class="table table-striped table-bordered" cellspacing="0"  ><tr>';

foreach ($ORDER_detail as $datos) {
    $ORDER_detail = json_decode($datos);


$user = $this->model->Get_User_Info($ORDER_detail->{'USER'}); 

foreach ($user as $value) {
$value = json_decode($value);
$name= $value->{'name'};
$lastname = $value->{'lastname'};
}



echo     "<th><strong>No. Req</strong></th><td class='InfsalesTd order'>".$ORDER_detail->{'REF'}."</td>
          <th><strong>Fecha</strong></th><td class='InfsalesTd'>".$ORDER_detail->{'date'}."</td>
          <th><strong>Responsable</strong></th><td class='InfsalesTd'>".$name.' '.$lastname."</td>
          <th><strong>Description</strong></th><td class='InfsalesTd'>".$ORDER_detail->{'NOTA'}."</td>";

}

echo "</tr></table>";


//$ORDER= $this->model->get_req_to_print($id);

echo '<table id="example-12" class="table table-striped table-bordered" cellspacing="0"  >
      <thead>
        <tr>
          <th width="10%">Producto</th>
          <th width="10%">Lote</th>
          <th width="10%">Almacen Origen</th>
          <th width="5%">Ruta</th>
          <th width="10%">Almacen Destino</th>
          <th width="5%">Ruta</th>
          <th width="5%">Cantidad</th>
        </tr>
      </thead><tbody>';


$ORDER = $this->model->get_con_to_report('DESC','100',$clause);

foreach ($ORDER as $datos) {

$ORDER = json_decode($datos);


//RUTA ORIGEN
$route_src = $this->model->Query_value('ubicaciones','etiqueta',' where id="'.$ORDER->{'route_ini'}.'"');
$stock_src = $this->model->Query_value('almacenes','name',' where id="'.$ORDER->{'id_almacen_ini'}.'"');

//RUTA DESTINO
$route_des = $this->model->Query_value('ubicaciones','etiqueta',' where id="'.$ORDER->{'route_des'}.'"');
$stock_des = $this->model->Query_value('almacenes','name',' where id="'.$ORDER->{'id_almacen_des'}.'"');

echo  '<tr  >
              <td  >'.$ORDER->{'ProductID'}.'</td>
              <td  >'.$ORDER->{'LOTE'}.'</td>
              <td style="background-color:#F3F781;" >'.$stock_src.'</td>
              <td style="background-color:#F3F781;" >'.$route_src.'</td>
              <td style="background-color:#BCDFA8;" >'.$stock_des.'</td>
              <td style="background-color:#BCDFA8;" >'.$route_des.'</td>
              <td  >'.$ORDER->{'CANT'}.'</td>

          </tr>';
 
  

  }

echo '</tbody></table><div style="float:right;" class="col-md-2">
<a href="'.URL.'index.php?url=ges_consignaciones/con_print/'.$ORDER->{'REF'}.'"  class="btn btn-block btn-secondary btn-icon btn-icon-standalone btn-icon-standalone-right btn-single text-left">
   <img  class="icon" src="img/Printer.png" />
  <span>Imprimir</span>
</a>
</div></fieldset>';



}


///////////////////////////////////////////////////////////////////////////////////////////////
//LISTA DE JOBS, FASES Y CENTRO DE COSTOS
public function get_JobList(){
$this->SESSION();

$jobs = $this->model->get_JobList(); 


foreach ($jobs as $value) {

 $value = json_decode($value);

  $list.= '<option value="'.$value->{'JobID'}.'" >'.$value->{'JobID'}.'-'.$value->{'Description'}.'</option>';

}

echo $list;


}


public function get_phaseList(){
$this->SESSION();

$phase = $this->model->get_phaseList();

if($phase!='0'){

foreach ($phase as $value) {

$value = json_decode($value);

  $list.= '<option value="'.$value->{'PhaseID'}.'" >'.$value->{'PhaseID'}.'</option>';

}


echo $list;

}else{

echo '0';

}



}

public function get_costList(){
$this->SESSION();

$cost = $this->model->get_costList();


if($cost!='0'){

foreach ($cost as $value) {

$value = json_decode($value);

  $list.= '<option value="'.$value->{'CostCodeID'}.'" >'.$value->{'Description'}.'</option>';

}


echo $list;


}else{

echo '0';

}


}


////////////////////////////////////////////////////////////////////////////////////////////////////////7
//PROCESO COMPRAS/RECIBO DE MERCANCIAS
public function set_fact_header($FACT_NO,$vendorID,$PO_ID,$nota,$total,$date){
$this->SESSION();

$date = date("Y-m-d", strtotime($date));

$VendorName = $this->model->Query_value('Vendors_Exp','Name', 'where VendorID="'.$vendorID.'" AND ID_compania="'.$this->model->id_compania.'"');
$CTA_CXP    = $this->model->Query_value('CTA_GL_CONF','CTA_CXP','WHERE ID_compania="'.$this->model->id_compania.'"');

$value_to_set  = array( 
  'TransactionID' => $FACT_NO,  
  'PurchaseNumber' => $PO_ID, 
  'ID_compania' => $this->model->id_compania,
  'VendorID' => $vendorID,
  'VendorName' => $VendorName,
  'AP_Account' => $CTA_CXP,
  'Net_due' => $total,
  'Subtotal' => $total,
  'nota' => $nota, 
  'USER' => $this->model->active_user_id, 
  'Date' => $date
  );

$res = $this->model->insert('Purchase_Header_Imp',$value_to_set);

echo $res;
}



public function set_fact_items($ITEM_ID,$DESC,$UnitMeasure,$QTY,$Unit_Price,$Net_line,$JOB_ID,$JOB_DESC,$PHASE_ID,$PHASE_DESC,$COST_ID,$COST_DESC,$FACT_NO,$COUNT,$ARRLENG,$lineType){
$this->SESSION();



//$gl_acnt = $this->model->Query_value('Products_Exp','GL_Sales_Acct','WHERE ProductID="'.$ITEM_ID.'" and ID_compania="'.$this->model->id_compania.'"');

if($JOB_ID == '-' ){
  $JOB_ID = '';
}

if ($PHASE_ID == '-' ) {
  $PHASE_ID = '';
}

if ($COST_ID == '-') {
 $COST_ID = '';
}


if($lineType=='1'){

  if($ITEM_ID=='ITBMS'){

  $gl_acnt = $this->model->Query_value('CTA_GL_CONF','CTA_TAX','WHERE ID_compania="'.$this->model->id_compania.'"');


  }else{

  $gl_acnt = $this->model->Query_value('CTA_GL_CONF','CTA_PUR','WHERE ID_compania="'.$this->model->id_compania.'"');

  $ITEM_ID = '';
  }

  

}else{

 $gl_acnt = $this->model->Query_value('Products_Exp','GL_Sales_Acct','WHERE ProductID="'.$ITEM_ID.'" and ID_compania="'.$this->model->id_compania.'"');


}


$value_to_set  = array( 
  'TransactionID' => $FACT_NO, 
  'Item_id' => $ITEM_ID,
  'Description' => $DESC,
  'Quantity' => $QTY,  
  'Unit_Price' => $Unit_Price,
  'Net_line'  => $Net_line,
  'GL_Acct' => $gl_acnt,
  'JobID' => $JOB_ID,  
  'JobPhaseID' => $PHASE_ID,  
  'JobCostCodeID' => $COST_ID, 
  'ID_compania' => $this->model->id_compania
  );



$res = $this->model->insert('Purchase_Detail_Imp',$value_to_set);



if($COUNT==$ARRLENG){ //SI LOS ITEMS PROCESADOS CONTABILIZADOS CON count ES IGUAL EL NUMERO DE LINEAS EN EL ARRAY (ARRLENG) entonces devuelve 0 para terminar el proceso de insesion de registros
  echo '1'; 
}else{ 
  echo '0';
} 

}



public function get_fact_by_id($id){

$this->SESSION();

$query ="SELECT * FROM `Purchase_Header_Imp`
inner JOIN `Purchase_Detail_Imp` ON Purchase_Detail_Imp.TransactionID = Purchase_Header_Imp.TransactionID
inner JOIN `SAX_USER` ON `SAX_USER`.`id` = Purchase_Header_Imp.USER
inner JOIN Products_Exp ON Products_Exp.ProductID =Purchase_Detail_Imp.item_id
WHERE Purchase_Header_Imp.TransactionID='".$id."' and 
      Purchase_Header_Imp.ID_compania='".$this->model->id_compania."'
GROUP BY Purchase_Header_Imp.TransactionID limit 1";



$fact_detail= $this->model->Query($query);

  
  

echo '<br/><br/><fieldset><div class="col-lg-6"><legend>Detalle de factura</legend><table class="table table-striped table-bordered" cellspacing="0"  >';


foreach ($fact_detail as $datos) {

   $fact_detail = json_decode($datos);

   if($fact_detail->{'Error'}=='1') { 

   $status= "Error : ".$fact_detail->{'ErrorPT'}. 'Se ha cancelado la Orden';
   $style="style='color:red;'"; 


 } else{

  

  if($fact_detail->{'Enviado'}!="1"){

      $style="style='color:orange;'"; 
      $status='Por Procesar'; 

     }else{ 

        $status= "Sincronizado el: ".$fact_detail->{'Export_date'};
        $style="style='color:green;'";

       }   

    }



echo "<tr><th style='text-align:left;'><strong>Ref.</strong></th><td class='InfsalesTd order'>".str_pad($fact_detail->{'TransactionID'}, 9 ,"0",STR_PAD_LEFT)."</td></tr>
      <tr><th style='text-align:left;'><strong>No. Factura</strong></th><td class='InfsalesTd'>".$fact_detail->{'PurchaseNumber'}."</td></tr>
      <tr><th style='text-align:left;'><strong>Fecha</strong></th><td class='InfsalesTd'>".$fact_detail->{'Date'}."</td></tr>
      <tr><th style='text-align:left;'><strong>Proveedor</strong></th><td class='InfsalesTd'>".$fact_detail->{'VendorName'}."</td></tr>
      <tr><th style='text-align:left;'><strong>Total Factura</strong></th><td class='InfsalesTd'>".number_format($fact_detail->{'Net_due'},2,'.',',')."</td></tr>
      <tr><th style='text-align:left;'><strong>Creado por:</strong></th><td class='InfsalesTd'>".$fact_detail->{'name'}.' '.$fact_detail->{'lastname'}."</td></tr>
      <tr><th style='text-align:left;'><strong>Estado:</strong></th><td class='InfsalesTd'  ".$style." >".$status."</td></tr>";

}
echo "</table></div>";




$query ="SELECT * FROM `Purchase_Header_Imp`
inner JOIN `Purchase_Detail_Imp` ON Purchase_Detail_Imp.TransactionID = Purchase_Header_Imp.TransactionID
WHERE Purchase_Header_Imp.TransactionID='".$id."'  and 
      Purchase_Header_Imp.ID_compania='".$this->model->id_compania."' and
      Purchase_Detail_Imp.ID_compania='".$this->model->id_compania."'
 order BY Purchase_Detail_Imp.ID ASC";





$fact_items= $this->model->Query($query);

echo '<table id="example-12" class="table table-striped table-bordered" cellspacing="0"  >
      <thead>
        <tr>
          <th>Cant.</th>
          <th>Item</th>
          <th>Unidad</th>
          <th>Descripcion</th>
          <th>Cta. GL</th>
          <th>Precio Unit.</th>
          <th>Total</th>
          <th>Job</th>
        </tr>
      </thead><tbody>';


foreach ($fact_items as $datos) {

   $fact_items = json_decode($datos);

    $id= "'".$fact_items->{'InvoiceNumber'}."'";


echo  "<tr>
          <td class='numb'>".number_format($fact_items->{'Quantity'},5,'.',',')."</td>
          <td>".$fact_items->{'Item_id'}."</td>
          <td>".$fact_items->{'UnitMeasure'}.'</td>
          <td>'.$fact_items->{'Description'}.'</td>
          <td class="numb">'.$fact_items->{'GL_Acct'}."</td>
          <td class='numb'>".number_format($fact_items->{'Unit_Price'},4,'.',',')."</td>
          <td class='numb'>".number_format($fact_items->{'Net_line'},4,'.',',').'</td>
          <td>'.$fact_items->{'JobID'}.'</td>
      </tr>';

  }

echo '</tbody></table><div style="float:right;" class="col-md-2">
<a href="'.URL.'index.php?url=ges_compras/print_fact/'.$fact_items->{'TransactionID'}.'"  class="btn btn-block btn-secondary btn-icon btn-icon-standalone btn-icon-standalone-right btn-single text-left">
   <img  class="icon" src="img/Printer.png" />
  <span>Imprimir</span>
</a>
</div></fieldset>';





}


public function set_req_quota($REQ_NO,$ID_compania){

$this->SESSION();

if($_SESSION){

$id_user = $this->model->active_user_id;

$VALID = $this->model->Query_value('REQ_QUOTA','NO_REQ','WHERE  NO_REQ ="'.$REQ_NO.'" 
                                                           AND  ID_compania="'.$ID_compania.'"');

if(!$VALID){

  $value = array( 'NO_REQ' => $REQ_NO , 
                  'USER' => $id_user, 
                  'ID_compania' => $ID_compania );

  $insert = $this->model->insert('REQ_QUOTA',$value);


  echo '<script>  alert("Cotizacion iniciada correctamente para la requisicion No. '.$REQ_NO.'");  

                 self.location="'.URL.'index.php?url=ges_reportes/rep_reportes"; 

       </script>';


}else{


  echo '<script>  alert("Para la requisicion No. '.$REQ_NO.' ya se ha iniciado la cotizacion");  
                 self.location="'.URL.'index.php?url=ges_reportes/rep_reportes"; 
        </script>';


}

}






}




public function get_PO_details($id){

$this->SESSION();
$oc = $this->model->get_items_by_OC($id);

$table.= '<button type="button" class="close" aria-label="Close" onclick="CLOSE_DIV('."'table2'".');" >
          <span STYLE="color:red" aria-hidden="true">&times; </span> Cerrar
          </button>

          <fieldset>
          
          <legend>Detalle de Orden de Compra</legend>



          <table   class="table table-striped table-bordered" cellspacing="0"  >
    <tbody>';
  
    $value = json_decode($oc[0]);

    $inv = "'".$value->{'PurchaseID'}."'";
    $url = "'".URL."'"; 


    $table.= "<tr><th style='text-align:left;' width='25%'>ID. Compra.</th><td >".$value->{'PurchaseOrderNumber'}.'</td></tr>
           <tr><th style="text-align:left;" width="25%">Fecha</th><td >'.$value->{'Date'}.'</td></tr>
           <tr><th style="text-align:left;" width="25%">Requisición</th><td >'.$value->{'CustomerSO'}.'</td></tr>
           <tr><th style="text-align:left;" width="25%">Proveedor</th><td >'.$value->{'VendorName'}.'</td></tr>
           <tr><th style="text-align:left;" width="10%">Estado</th> <td >'.$value->{'WorkflowStatusName'}.'</td></tr>
           <tr><th style="text-align:left;" width="10%">Asignado a</th> <td >'.$value->{'WorkflowAssignee'}.'</td></tr>
          <tr><th style="text-align:left;" width="30%">Nota</th><td >'.$value->{'WorkflowNote'}.'</td></tr>';
  
    $table.= '</tbody></table>

    <table id="Items" class="table table-striped" cellspacing="0"  >
    <thead>
      <tr>
        <th width="20%">Codigo Item</th>
        <th width="30%">Descripcion</th>
        <th width="10%">Cantidad</th>
        <th width="10%">Precio Uni.</th>
        <th width="10%">Total</th>
      </tr>
    </thead>
 
 <tbody >';
 
  foreach ($oc as $value) {

    $value = json_decode($value);

    $inv = "'".$value->{'PurchaseID'}."'";
    $url = "'".URL."'"; 

          $table.= "<tr>
            <td >".$value->{'Item_id'}.'</td>
            <td >'.$value->{'Description'}.'</td>
            <td class="numb" >'.number_format($value->{'Quantity'},2,'.',',').'</td>
            <td class="numb"  >'.number_format($value->{'Unit_Price'},2,'.',',').'</td>
            <td class="numb" >'.$value->{'NetLine'}.'</td>
          </tr>';

    }

     
  $table.='</tbody></table></fieldset>';


    echo $table;




}

public function get_reception($id){

$this->SESSION();

//IF EXSIST
$EXIST_ID = $this->model->Query_value('REQ_HEADER','NO_REQ','WHERE REQ_HEADER.ID_compania="'.$this->model->id_compania.'" and REQ_HEADER.NO_REQ="'.$id.'"');

if($EXIST_ID!=''){

$ORDER_detail = $this->model->get_req_to_report('DESC','1','WHERE REQ_HEADER.ID_compania="'.$this->model->id_compania.'" AND  REQ_HEADER.ID_compania="'.$this->model->id_compania.'" and REQ_HEADER.NO_REQ="'.$id.'" and REQ_DETAIL.NO_REQ="'.$id.'"');

echo '<script>

var table = $("#table_info").dataTable({

       rowReorder: {
            selector: "td:nth-child(2)"
        },

      bSort: false,
      select:true,
      scrollX: "100%",
      scrollCollapse: true,
      responsive: false,
      searching: false,
      paging:    false,
      info:      false
     });


</script>';

echo '<br/><br/><fieldset><legend>Detalle de Requisición</legend>
<table  class="display nowrap table table-striped table-bordered" cellspacing="0"  ><tbody>';

  foreach ($ORDER_detail as $datos) {
    $ORDER_detail = json_decode($datos);


$user = $this->model->Get_User_Info($ORDER_detail->{'USER'}); 

foreach ($user as $value) {
$value = json_decode($value);
$name= $value->{'name'};
$lastname = $value->{'lastname'};
}


//obtengo estatus de la requisicion
$status = $this->req_status($ORDER_detail->{'NO_REQ'});

switch ($status) {
  case 'CERRADA':
     $style = 'style="background-color:#D8D8D8 ;"';//verder
    break;
  case 'FINALIZADO':
     $style = 'style="background-color:#BCF5A9;"';//verder
    break;
  case 'ORDENADO':
     $style = 'style="background-color:#F2F5A9;"';//AMARILLO
    break;
  case 'PARCIALMENTE ORDENADO':
     $style = 'style="background-color:#F3E2A9;"';//NARANJA
    break;
  case 'COTIZANDO':
     $style = 'style="background-color:#F7BE81;"';//NARANJA
    break; 
  case 'POR COTIZAR':
     $style = 'style="background-color:#F5A9A9;"';//ROJO
    break; 

}


echo     "<tr><th style='text-align:left;' ><strong>No. Req</strong></th><td class='InfsalesTd order'>".$ORDER_detail->{'NO_REQ'}."</td><tr>
          <tr><th style='text-align:left;'><strong>Fecha</strong></th><td class='InfsalesTd'>".$ORDER_detail->{'DATE'}."</td><tr>
          <tr><th style='text-align:left;'><strong>Solicitado por:</strong></th><td class='InfsalesTd'>".$name.' '.$lastname.'</td><tr>
          <tr><th style="text-align:left;" ><strong>Estado</strong></th><td '.$style.' class="InfsalesTd">'.$status.'</td><tr>';

}


echo "</tbody></table>";



$ORDER= $this->model->get_req_to_print($id);

echo '<table id="table_info" class="table table-striped table-bordered" cellspacing="0"  >
      <thead>
        <tr>
          <th>Codigo</th>
          <th>Descripcion</th>
          <th>Unidad</th>
          <th>Cant. Requerida</th>
          <th>Cant. Ordenada</th>
          <th>Cant. Recibida</th>
          <th>OC. Asociadas</th>
          <th>Status</th>
          <th>Registar Recibido</th>
        </tr>
      </thead><tbody>';

$i=1;

foreach ($ORDER as $datos) {

$ORDER = json_decode($datos);



//Informacion de ORDEN DE COMPRA PARA ESTE PRODUCTO EN LA REQUISICION
    $sql_OC = 'SELECT 
    PurOrdr_Header_Exp.PurchaseOrderNumber,
    sum(PurOrdr_Detail_Exp.Quantity) as Quantity
    FROM PurOrdr_Header_Exp
    INNER JOIN PurOrdr_Detail_Exp ON PurOrdr_Header_Exp.TransactionID = PurOrdr_Detail_Exp.TransactionID
    WHERE PurOrdr_Header_Exp.CustomerSO =  "'.$ORDER_detail->{'NO_REQ'}.'"
    AND PurOrdr_Header_Exp.ID_compania =  "'.$this->model->id_compania.'"
    AND PurOrdr_Detail_Exp.Item_id = "'.$ORDER->{'ProductID'}.'"
    AND PurOrdr_Header_Exp.PurchaseOrderNumber <> " "';

    $INFO_OC = $this->model->Query($sql_OC);

    $QTY_TOTAL=0;

    unset($Qty_Comprada);

    foreach ($INFO_OC as $datos) {

        $INFO_OC = json_decode($datos);
        $Qty_Comprada[$INFO_OC->{'PurchaseOrderNumber'}] = $INFO_OC->{'Quantity'};
        $QTY_TOTAL += $INFO_OC->{'Quantity'};

      }

    $QTY_FALTANTE = $ORDER->{'CANTIDAD'}-$QTY_TOTAL;

    $oc_list='';

    foreach ($Qty_Comprada as $key => $value) {

    $PO_NO = "'".$key."'";

    $oc_list .='<a href="javascript:void(0)" onclick="get_OC('.$PO_NO.');"><strong>'.$key.' ('.number_format($value,0,'.',',').')</strong></a><BR>';
    }
//Informacion de ORDEN DE COMPRA PARA ESTE PRODUCTO EN LA REQUISICION




//INI STATUS POR ITEM/////////////////////////////////////////////////////////////////////////////////////////////
  $status = $this->req_item_status($ORDER_detail->{'NO_REQ'},$ORDER->{'ProductID'},$ORDER->{'CANTIDAD'});

  $VISIBLE = ' ';

switch ($status) {
  case 'CERRADA':
     $style_row = 'style="background-color:#D8D8D8;"';//verder
    break;

  case 'FINALIZADO':
     $style_row = 'style="background-color:#BCF5A9;"';//verder
    break;
  case 'ORDENADO':
     $style_row = 'style="background-color:#F2F5A9;"';//AMARILLO
    break;
  case 'SOBREORDENADO':
     $style_row = 'style="background-color:#F2F5A9;"';//AMARILLO
    break;
  case 'ORDENADO / RECEPCION PARCIAL':
      $style_row = 'style="background-color:#F2F5A9;"';//AMARILLO
    break; 
  case 'PARCIALMENTE ORDENADO':
     $style_row = 'style="background-color:#F3E2A9;"';//NARANJA
    break;
  case 'PARCIALMENTE ORDENADO / RECEPCION PARCIAL':
      $style_row = 'style="background-color:#F3E2A9;"';//NARANJA
    break; 
  case 'COTIZANDO':
     $style_row= 'style="background-color:#F7BE81;"';//NARANJA
    break; 
  case 'POR COTIZAR':
     $style_row = 'style="background-color:#F5A9A9;"';//ROJO
    break; 

  }
//FIN  STATUS POR ITEM/////////////////////////////////////////////////////////////////////////////////////////////


//BLOQUEA CAMPO D EENTRADA SI LO ORDENADO ES IGUAL A LO RECIBIDO
//TOTEL RECIVIDO POR ITEM
$total_reciv = $this->model->Query_value('REQ_RECEPT','SUM(QTY)','WHERE  ID_compania="'.$this->model->id_compania.'" 
                                                                         AND NO_REQ="'.$ORDER_detail->{'NO_REQ'}.'"
                                                                         AND ITEM="'.$ORDER->{'ProductID'}.'"');

if($QTY_TOTAL <= $total_reciv){ $VISIBLE = 'style="background-color : #D8D8D8;"  readonly';  }
//BLOQUEA CAMPO D EENTRADA SI LO ORDENADO ES IGUAL A LO RECIBIDO


echo  "<tr ".$style_row.">
            <td><label  id='ID".$i."'  >".$ORDER->{'ProductID'}."</label></td>
            <td>".$ORDER->{'DESCRIPCION'}."</td>
            <td class='numb' >".$ORDER->{'UNIDAD'}."</td>
            <td class='numb'>
            <label class='numb' id='QTYREQ".$ORDER->{'ProductID'}."' >".$ORDER->{'CANTIDAD'}."</label>
            </td>
            <td class='numb'>
            <label class='numb' id='QTYORD".$ORDER->{'ProductID'}."' >".$QTY_TOTAL."</label>             
            </td>
            <td class='numb'>
            <label class='numb' id='QTYRCV".$ORDER->{'ProductID'}."' >".$total_reciv."</label>
            </td>
            <td>".$oc_list."</td>
            <td>".$status."</td>
            <td width='5%' class='numb'>
            <input class='numb' id='rec".$ORDER->{'ProductID'}."'  type='number' min='0.01' step='0.1' value='' ".$VISIBLE." />
            </td></tr>";
$i+=1;
  }

echo '</tbody></table>

<div class=" separador col-lg-12"></div>

<div style="float:right;" class="col-md-2">
<input type="buttom" onclick="save();" class="btn btn-primary  btn-sm btn-icon icon-right" value="Guardar">
</div>
</fieldset>
<div class=" separador col-lg-12"></div>

  <div class="col-lg-12">
    <fieldset>
    <legend>Historial de recepción</legend>
        <table class="table table-striped table-bordered" cellspacing="0" >
          <thead> 
            <th># Item</th>
            <th>Cantidad Recibida</th>
            <th>Fecha Recepcion</th>
            <th>Registrado por:</th>
            
          </thead>
          <tbody>';

$SQL = 'SELECT * 
        FROM REQ_RECEPT
        WHERE  ID_compania="'.$this->model->id_compania.'" 
        AND NO_REQ="'.$ORDER_detail->{'NO_REQ'}.'"';

$RECEPT = $this->model->Query($SQL);

        foreach ($RECEPT  as $value) {
         
         $value = json_decode($value);

          $user = $this->model->Get_User_Info($value->{'USER'}); 

          foreach ($user as $USER) {
            $USER = json_decode($USER);
            $name= $USER->{'name'};
            $lastname = $USER->{'lastname'};
          }

        $date = strtotime($value->{'DATE'});
        $date = date('m/d/Y',$date);
   
 ECHO   '<tr><td>'.$value->{'ITEM'}.'</td>'.
        '<td class="numb" >'.number_format($value->{'QTY'},4,'.',',').'</td>'.
        '<td class="numb" >'.$date.'</td>'.
        '<td>'.$name.' '.$lastname.'</td></tr>';

        }

 ECHO   '</tbody>
        </table>';

  if(!$RECEPT){
    echo 'AUN NO EXISTEN REGISTROS DE RECEPCIÓN';

  }

    ECHO  '</fieldset>
           </div>
          <div class=" separador col-lg-12"></div>

            <div id="table2" class="col-lg-12"></div>


          ';


  }else{


     echo '<div id="ERROR" class="alert alert-danger">EL ID <strong>'.$id.'</strong> DE REQUISICIÓN NO EXISTE</div>';

  }

}


public function get_req_item_lines($id){
$this->SESSION();

$count = $this->model->Query_value('REQ_DETAIL','COUNT(*)', 'WHERE ID_compania="'.$this->model->id_compania.'" AND NO_REQ="'.$id.'"');

ECHO $count;
}





public function set_req_recept($ITEM,$QTY,$REQ_NO,$COUNT, $ARRGL){
$this->SESSION();

    $value_to_set  = array( 
      'NO_REQ' => $REQ_NO, 
      'ITEM' => $ITEM, 
      'QTY' => $QTY, 
      'USER' => $this->model->active_user_id, 
      'ID_compania' => $this->model->id_compania
      );


    $res = $this->model->insert('REQ_RECEPT',$value_to_set);

    if($COUNT==$ARRGL){ //SI LOS ITEMS PROCESADOS CONTABILIZADOS CON count ES IGUAL EL NUMERO DE LINEAS EN EL ARRAY (ARRLENG) entonces devuelve 0 para terminar el proceso de insesion de registros
      echo '1'; 
    }else{ 
      echo '0';
    } 
}



public function get_ReqJob($job){

$this->SESSION();

$table = '';
$clause='';

$clause.= 'where REQ_HEADER.ID_compania="'.$this->model->id_compania.'" and REQ_HEADER.isUrgent="0" and REQ_DETAIL.ID_compania="'.$this->model->id_compania.'" and REQ_HEADER.job="'.$job.'"';

if($date1!=''){
   if($date2!=''){
      $clause.= ' and  DATE >= "'.$date1.'%" and DATE <= "'.$date2.'%" ';           
    }
   if($date2==''){ 
     $clause.= ' and  DATE like "'.$date1.'%" ';
   }
}




 $table.= '<script type="text/javascript">
 jQuery(document).ready(function($)
  {
   var table = $("#table_reportReqUrg").dataTable({
      
      responsive: false,
      pageLength: 10,
      dom: "Bfrtip",
      bSort: false,
      select: false,
 
      info: false,
        buttons: [
          {
          extend: "excelHtml5",
          text: "Exportar",
          title: "Reporte_Estado_de_requisiciones",
           
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
{column_number : 3},
{column_number : 4}
],
{cumulative_filtering: true, 
filter_reset_button_text: false}); 
});

  </script>
   <table id="table_reportReqUrg" class="display nowrap table table-condensed table-striped table-bordered" >
   
    <thead>
      <tr>
        
        <th width="10%">No. Ref.</th>
        <th width="10%">Fecha </th>
        <th width="45%">Descripcion</th>
        <th width="25%">Solicitado por:</th>
        <th width="10%">Estado</th>
        
      </tr>
    </thead>
    <tbody>';


$Item = $this->model->get_req_to_report('DESC','10000',$clause);



foreach ($Item as $datos) {

$Item = json_decode($datos);

$name = $this->model->Query_value('SAX_USER','name','Where ID="'.$Item->{'USER'}.'"');
$lastname =  $this->model->Query_value('SAX_USER','lastname','Where ID="'.$Item->{'USER'}.'"');

$status='';

$ID = '"'.$Item->{'NO_REQ'}.'"';

$URL = '"'.URL.'"';


//obtengo estatus de la requisicion
$status = $this->req_status($Item->{'NO_REQ'});

switch ($status) {

  case 'CERRADA':
     $style = 'style="background-color:#D8D8D8 ;"';//verder
    break;
  case 'FINALIZADO':
     $style = 'style="background-color:#BCF5A9;"';//verder
    break;
  case 'ORDENADO':
     $style = 'style="background-color:#F2F5A9;"';//AMARILLO
    break;
  case 'PARCIALMENTE ORDENADO':
     $style = 'style="background-color:#F3E2A9;"';//NARANJA
    break;
  case 'COTIZANDO':
     $style = 'style="background-color:#F7BE81;"';//NARANJA
    break; 
  case 'POR COTIZAR':
     $style = 'style="background-color:#F5A9A9;"';//ROJO
    break; 

}


$table.="<tr  >
              
              <td width='10%' ><a href='#' onclick='javascript: show_req(".$URL.",".$ID.");'>".$Item->{'NO_REQ'}."</a></td>
              <td width='10%' >".date('m/d/Y',strtotime($Item->{'DATE'}))."</td>
              <td width='45%' >".$Item->{'NOTA'}.'</td>
              <td width="25%" >'.$name.' '.$lastname.'</td>
              <td width="10%" '.$style.' >'.$status.'</td>
          </tr>';
 

      }

   

$table.= '</tbody></table> <div class="separador col-lg-12"></div><div id="info"></div>'; 

echo $table;
}


}




