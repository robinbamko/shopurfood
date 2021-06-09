<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 
 * Mobile APP Related functions
 * @author Teamtweaks
 * @Modified Author - vishnu,devaraj
 */

class Mobile_Vehicle extends MY_Controller {  
/*Loading wanted Model and Form validations, Model class, Google map Api key loading from config*/
  function __construct(){
    parent::__construct();
    $this->load->helper(array('cookie','date','form','email'));
    $this->load->library(array('encrypt','form_validation','resizeimage', 'image_lib', 'upload'));  
    $this->load->model('mobile_model');
    $this->output->set_content_type('application/json');
    $this->google_map_api = $this->config->item('google_map_api');
    $this->paypal_curency="USD";
    $this->paypal_symbol="$";
    define("API_LOGINID",$this->config->item('payment_2'));
    define("StripeDetails",$this->config->item('payment_1'));
    $defaultLanguage = $this->db->select('lang_code')->where('default_lang',"Default")->get(LANGUAGES)->row()->lang_code;
    $selectedLanguage = $this->input->post('lang_code');
    ($selectedLanguage != '') ? $selectedLanguage = $selectedLanguage : $selectedLanguage = $defaultLanguage;
    $filePath = APPPATH . "language/" . $selectedLanguage . "/" . $selectedLanguage . "_lang.php";
    if ($selectedLanguage != '') {
        if (!(is_file($filePath))) {
            $this->lang->load($defaultLanguage, $defaultLanguage);
        } else {
            $this->lang->load($selectedLanguage, $selectedLanguage);
        }
    } else {
        $this->lang->load($defaultLanguage, $defaultLanguage);
    }

    if ($this->lang->line('parm_missing') != ''){  $this->parm_missing = stripslashes($this->lang->line('parm_missing')); } else{ $this->parm_missing = "Parameters missing!"; }
    if ($this->lang->line('add_succ') != ''){  $this->add_succ = stripslashes($this->lang->line('add_succ')); } else{ $this->add_succ = "Added successfully"; }
    if ($this->lang->line('updt_succ') != ''){  $this->updt_succ = stripslashes($this->lang->line('updt_succ')); } else{ $this->updt_succ = "Updated successfully"; }
     if ($this->lang->line('fild') != ''){  $this->fild = stripslashes($this->lang->line('fild')); } else{ $this->fild = "Failed!"; }
    if ($this->lang->line('succ') != ''){  $this->succ = stripslashes($this->lang->line('succ')); } else { $this->no_rstnd_found = "Success"; }
    if ($this->lang->line('dta_found') != ''){  $this->dta_found = stripslashes($this->lang->line('dta_found')); } else{ $this->dta_found = "Data available"; }
    if ($this->lang->line('veh_added') != ''){  $this->veh_added = stripslashes($this->lang->line('veh_added')); } else{ $this->veh_added = "Vehicle added"; }
    if ($this->lang->line('sign_bfr_listing') != ''){  $this->sign_bfr_listing = stripslashes($this->lang->line('sign_bfr_listing')); } else{ $this->sign_bfr_listing = "Please sign in before listing your rental!"; }
    if ($this->lang->line('no_drvr_data_avail') != ''){  $this->no_drvr_data_avail = stripslashes($this->lang->line('no_drvr_data_avail')); } else{ $this->no_drvr_data_avail = "No driver data available"; }
    if ($this->lang->line('drvr_exist') != ''){  $this->drvr_exist = stripslashes($this->lang->line('drvr_exist')); } else{ $this->drvr_exist = "Driver already exist"; }
    if ($this->lang->line('veh_avail') != ''){  $this->veh_avail = stripslashes($this->lang->line('veh_avail')); } else{ $this->veh_avail = "Vehicle available"; }
    if ($this->lang->line('no_veh_avail') != ''){  $this->no_veh_avail = stripslashes($this->lang->line('no_veh_avail')); } else{ $this->no_veh_avail = "No vehicle available"; }
    if ($this->lang->line('no_drvr_data_avail') != ''){  $this->no_drvr_data_avail = stripslashes($this->lang->line('no_drvr_data_avail')); } else{ $this->no_drvr_data_avail = "No Driver data available!"; }
    if ($this->lang->line('amt_dtls_rtr') != ''){  $this->amt_dtls_rtr = stripslashes($this->lang->line('amt_dtls_rtr')); } else{ $this->amt_dtls_rtr = "Amount details retrieved"; }
    if ($this->lang->line('enqy_failed') != ''){  $this->enqy_failed = stripslashes($this->lang->line('enqy_failed')); } else{ $this->enqy_failed = "Enquiry failed"; }
    if ($this->lang->line('drvr_exist') != ''){  $this->drvr_exist = stripslashes($this->lang->line('drvr_exist')); } else{ $this->drvr_exist = "Driver already exist"; }
    if ($this->lang->line('sts_not_avail') != ''){  $this->sts_not_avail = stripslashes($this->lang->line('sts_not_avail')); } else{ $this->sts_not_avail = "Sorry This Status Is Not Available"; }
    if ($this->lang->line('no_list_avail') != ''){  $this->no_list_avail = stripslashes($this->lang->line('no_list_avail')); } else{ $this->no_list_avail = "No Listing available"; }
    if ($this->lang->line('list_avail') != ''){  $this->list_avail = stripslashes($this->lang->line('list_avail')); } else{ $this->list_avail = "Listing available"; }
    if ($this->lang->line('succ_cancelled') != ''){  $this->succ_cancelled = stripslashes($this->lang->line('succ_cancelled')); } else{ $this->succ_cancelled = "Successfully cancelled"; }
    if ($this->lang->line('succ_paid') != ''){  $this->succ_paid = stripslashes($this->lang->line('succ_paid')); } else{ $this->succ_paid = "paid Successfully"; }
    if ($this->lang->line('payt_failed') != ''){  $this->payt_failed = stripslashes($this->lang->line('payt_failed')); } else{ $this->payt_failed = "Payment failed"; }
    if ($this->lang->line('nt_vlid_img') != ''){  $this->nt_vlid_img = stripslashes($this->lang->line('nt_vlid_img')); } else{ $this->nt_vlid_img = "Not valid image type"; }
    if ($this->lang->line('title_exist') != ''){  $this->title_exist = stripslashes($this->lang->line('title_exist')); } else{ $this->title_exist = "Title Exist!"; }
    if ($this->lang->line('alrdy_exist') != ''){  $this->alrdy_exist = stripslashes($this->lang->line('alrdy_exist')); } else{ $this->title_exist = "Already Exist!"; }
    if ($this->lang->line('no_dta_found') != ''){  $this->no_dta_found = stripslashes($this->lang->line('no_dta_found')); } else{ $this->no_dta_found = "No data found"; }
    if ($this->lang->line('no_trps_avail') != ''){  $this->no_trps_avail = stripslashes($this->lang->line('no_trps_avail')); } else{ $this->no_trps_avail = "No trip available"; }
    if ($this->lang->line('trps_avail') != ''){  $this->trps_avail = stripslashes($this->lang->line('trps_avail')); } else{ $this->title_exist = "Trips available"; }
    if ($this->lang->line('enqy_succ') != ''){  $this->enqy_succ = stripslashes($this->lang->line('enqy_succ')); } else{ $this->enqy_succ = "Enquiry Success"; }
    if ($this->lang->line('enqy_failed') != ''){  $this->enqy_failed = stripslashes($this->lang->line('enqy_failed')); } else{ $this->enqy_failed = "Enquiry Failed"; }
    if ($this->lang->line('addr_updt') != ''){  $this->addr_updt = stripslashes($this->lang->line('addr_updt')); } else{ $this->addr_updt = "address updated"; }
    if ($this->lang->line('dlted_succ') != ''){  $this->dlted_succ = stripslashes($this->lang->line('dlted_succ')); } else{ $this->dlted_succ = "Successfully delete"; }
    if ($this->lang->line('price_updt') != ''){  $this->price_updt = stripslashes($this->lang->line('price_updt')); } else{ $this->dlted_succ = "Price Updated"; }
    if ($this->lang->line('rejct') != ''){  $this->rejct = stripslashes($this->lang->line('rejct')); } else{ $this->rejct = "Rejected"; }
    if ($this->lang->line('dlt_succ') != ''){  $this->dlt_succ = stripslashes($this->lang->line('dlt_succ')); } else{ $this->dlt_succ = "Successfully deleted"; }
    if ($this->lang->line('list_succ_paid') != ''){  $this->list_succ_paid = stripslashes($this->lang->line('list_succ_paid')); } else{ $this->list_succ_paid = "Successfully paid"; }
    if ($this->lang->line('wish_ritrved') != ''){  $this->wish_ritrved = stripslashes($this->lang->line('wish_ritrved')); } else{ $this->wish_ritrved = "Data retrieved"; }
    if ($this->lang->line('alrdy_bkd') != ''){  $this->alrdy_bkd = stripslashes($this->lang->line('alrdy_bkd')); } else{ $this->alrdy_bkd = "Data retrieved"; }
    if ($this->lang->line('succ') != ''){  $this->succ = stripslashes($this->lang->line('succ')); } else{ $this->succ = "Data retrieved"; }
  }



  public function seo_friendly_url($string, $wordLimit = 0)
  {
      $separator = '-';
      if ($wordLimit != 0) {
          $wordArr = explode(' ', $string);
          $string = implode(' ', array_slice($wordArr, 0, $wordLimit));
      }
      $quoteSeparator = preg_quote($separator, '#');
      $trans = array(
          '&.+?;' => '',
          '[^\w\d _-]' => '',
          '\s+' => $separator,
          '(' . $quoteSeparator . ')+' => $separator
      );

      $string = strip_tags($string);
      foreach ($trans as $key => $val) {
          $string = preg_replace('#' . $key . '#i' . (UTF8_ENABLED ? 'u' : ''), $val, $string);
      }
      $string = strtolower($string);
      return trim(trim($string, $separator));
  }


  public function car_list_values() 
  {
    $language_code = $_POST['lang_code'];
    $vehicle_type  = $_POST['base_id'];
    $currency_code = $_POST['currency_code'];

    $vehicles_basic_dtls = $this->get_vehicle_basic_dtls($vehicle_type,$language_code);

    $json_encode = json_encode(array("status" => 1,"message" => $this->dta_found,"make" =>$vehicles_basic_dtls['make_details'],"model"=>$vehicles_basic_dtls['model_details'],"type"=>$vehicles_basic_dtls['type_details'],"attribute"=>$vehicles_basic_dtls['attribute'],"property_attributes" => $vehicles_basic_dtls['property_attributes'],"currency" => $vehicles_basic_dtls['currency']),JSON_PRETTY_PRINT);

    echo $json_encode; exit();

  }

  public function get_vehicle_basic_dtls($vehicle_type = '',$language_code = '')
  {
    $car = array();
    $car_model = array();
    $currencyvalueArr = array();
    $attribute = array();
    $car_type = array();

    /*Currency Details*/
    $select_qry = "select id,currency_symbols,currency_type,currency_rate from fc_currency where status='Active' and currency_symbols !='' and currency_type !=''";

    $currency_values = $this->mobile_model->ExecuteQuery($select_qry);
    $currencyvalueArr = array();
    if($currency_values->num_rows() >0) 
    {
      foreach($currency_values->result() as $cur_value) 
      {
      $currencyvalueArr[] = array("id" =>$cur_value->id,"country_symbols"=>$cur_value->currency_symbols,"currency_type"=>$cur_value->currency_type);
      }
    } 
    /*******/

    /*Make Details*/
    $conditions = array('status'=>'Active','vehicle_type'=>$vehicle_type);
    $car_space = $this->mobile_model->get_all_details(MAKE_MASTER, $conditions);

    if(count($car_space)>0) 
    {
      foreach($car_space->result() as $pro) 
      {
        $carvalueArr = array();

        if($language_code == 'en')
        {
          $rentalNameField=$pro->make_name;
        }
        else
        {
          $titleNameField='make_name_ph';
          if($pro->$titleNameField == '') 
          { 
            $rentalNameField=$pro->make_name;
          }
          else
          {
            $rentalNameField=$pro->$titleNameField;
          }
        }
        $carvalueArr[] = array("child_id" =>$pro->id,"child_name"=>$rentalNameField,"parent_name"=>"Make");
        $car[]  = array("option_name"=>"Make","options"=>$carvalueArr);
      }
    }
    /********/

    /*car Models*/
    $conditions = array('status'=>'Active','vehicle_type'=>$vehicle_type);
    $car_modal_space = $this->mobile_model->get_all_details(MODEL_MASTER, $conditions);

    if(count($car_modal_space)>0) 
    {
      foreach($car_modal_space->result() as $modalpro) 
      {
        $carvalueArr = array();
        if($language_code == 'en')
        {
          $modelNameField=$modalpro->model_name;
        }
        else
        {
          $titleNameField='model_name_ph';
          if($modalpro->$titleNameField == '') 
          { 
            $modelNameField=$modalpro->model_name;
          }
          else
          {
            $modelNameField=$modalpro->$titleNameField;
          }
        }

        $carvalueArr[] = array("child_id" =>$modalpro->id,"child_name"=>$modelNameField,"parent_name"=>"Model");
        $car_model[]  = array("option_name"=>"Model","options"=>$carvalueArr);
      }
    }
    /******/

    /*Car types*/
    $conditions = array('status'=>'Active','vehicle_type'=>$vehicle_type);
    $car_type_space = $this->mobile_model->get_all_details(TYPE_MASTER, $conditions);

    if(count($car_type_space)>0) 
    {
      foreach($car_type_space->result() as $typepro) 
      {
        $carvalueArr = array();

        if($language_code == 'en')
        {
           $modelNameField=$typepro->type_name;
        }
        else
        {
          $titleNameField='type_name_ph';
          if($typepro->$titleNameField == '')
          { 
            $modelNameField=$typepro->type_name;
          }
          else{
             $modelNameField=$typepro->$titleNameField;
          }
        }  

        $carvalueArr[] = array("child_id" =>$typepro->id,"child_name"=>$modelNameField,"parent_name"=>"Type");
        $car_type[]  = array("option_name"=>"Type","options"=>$carvalueArr);
      }
    }

    $roombedVal=array();
    $roombedVal1=array();

    $select_qry = "select * from fc_listings where id=1";
    $list_values = $this->mobile_model->ExecuteQuery($select_qry);

    if($list_values->num_rows()>0)
    {
      foreach($list_values->result() as   $list){
        $roombedVal[] =json_decode($list->listing_values);
        $roombedVal1[] =json_decode($list->rooms_bed);
      }
    }

    $select_qrys = "select * from fc_listing_types WHERE status='Active' AND rental_type = '".$vehicle_type."'";
    $listing_values = $this->mobile_model->ExecuteQuery($select_qrys);

    $property_attributes = array();

    if($listing_values->num_rows()>0)
    {
      foreach($listing_values->result() as $listing_parent)
      {
        if($language_code == 'en')
        {
            $field_name=$listing_parent->name;
            $field_labelname  = $listing_parent->labelname;
        }
        else
        {
            $name_Field='name_ph';
            if($listing_parent->$name_Field == '') { 
                $field_name=$listing_parent->name;
            }
            else{
                $field_name=$listing_parent->$name_Field;
            }

            $labelname_Field='labelname_ph';
            if($listing_parent->$labelname_Field == '') { 
                $field_labelname=$listing_parent->labelname;
            }
            else{
                $field_labelname=$listing_parent->$labelname_Field;
            }
        }

        $listing_id = $listing_parent->id;
        $listing_name = $field_name;
        $listing_type = $listing_parent->type;
        $listing_labelname = $field_labelname;
        $listing_use_propertyattribute = (($listing_parent->don_not_delete==0)?true:false); 

        $select_qryy = "select * from fc_listing_child where parent_id=".$listing_id." and status=0 order by child_name ASC";

        $list_valuesy = $this->mobile_model->ExecuteQuery($select_qryy);
        $property_child_attributes = array();

        if($list_valuesy->num_rows()>0)
        {
          if($listing_type=="option") 
          {
            foreach($list_valuesy->result() as $listing_child)
            {      
              $listing_child_id = $listing_child->id;
              $listing_child_name = $listing_child->child_name;
              $property_child_attributes[] = array("attribute_child_id"=>intval($listing_child_id),"attribute_parent_name"=>$listing_name,"attribute_child_value"=>$listing_child_name);
            }
          }
        }

        if($listing_type=="option" && $list_valuesy->num_rows()==0) 
        {
        }
        else
        {
          $property_attributes[] = array("attribute_id"=>intval($listing_id),"attribute_type"=>$listing_type,"attribute_name"=>$listing_name,"attribute_label"=>$listing_labelname,"use_property_attribute"=>$listing_use_propertyattribute,"attribute_value"=>$property_child_attributes);
        }
      }
    }

    $parent_select_qry = "select id,attribute_name,attribute_name_ph,status from fc_attribute where status='Active' AND rental_type = '".$vehicle_type."'";
    $parent_list_values = $this->mobile_model->ExecuteQuery($parent_select_qry);

    if($parent_list_values->num_rows()>0) 
    {
      foreach($parent_list_values->result() as $parent_value) 
      {
        $select_qrys = "select fc_list_values.id,fc_attribute.rental_type,list_value,list_id,fc_attribute.id as attr_id,attribute_name,image from fc_list_values left join fc_attribute  on fc_attribute.id = fc_list_values.list_id where fc_list_values.status='Active' and fc_attribute.rental_type = ".$vehicle_type;

        $list_values = $this->mobile_model->ExecuteQuery($select_qrys);

        if($list_values->num_rows()>0) 
        {
          $listvalueArr = array();

          if ($language_code == 'en')
          {
            $parent_attribute_name  = $parent_value->attribute_name;
          }
          else
          {
            $attribute_name_parent  = 'attribute_name_ph';
            if($parent_value->$attribute_name_parent == '') { 
                $parent_attribute_name=$parent_value->attribute_name;
            }
            else{
                $parent_attribute_name=$parent_value->$attribute_name_parent;
            }
          }

          foreach($list_values->result() as $list_value) 
          {
            if($parent_value->id == $list_value->list_id) 
            {
              if($language_code == 'en')
              {
                  $field_list_value=$list_value->list_value;
                  $field_attribute_name  = $list_value->attribute_name;
              }
              else
              {
                  $list_value_Field='list_value_ph';
                  if($list_value->$list_value_Field == '') { 
                      $field_list_value=$list_value->list_value;
                  }
                  else{
                      $field_list_value=$list_value->$list_value_Field;
                  }

                  $attribute_name_field  = 'attribute_name_ph';
                  if($list_value->$attribute_name_field == '') { 
                      $field_attribute_name=$list_value->attribute_name;
                  }
                  else{
                      $field_attribute_name=$list_value->$attribute_name_field;
                  }
              }

              $listvalueArr[] = array("child_id" =>$list_value->id,"child_name"=>$field_list_value,"child_image"=>base_url()."images/attribute/".$list_value->image,"parent_name"=>$field_attribute_name,"parent_id"=>$list_value->attr_id);
            }
          }
          $attribute[]  = array("option_id"=>$parent_value->id,"option_name"=>$parent_attribute_name,"options"=>$listvalueArr);
        } 
      }
    }

    return array("make_details"=>$car,"model_details"=>$car_model,"type_details"=>$car_type,"attribute"=>$attribute,"property_attributes" => $property_attributes,"currency" =>$currencyvalueArr);

  }


  public function home_vehicle_info()
  {
    $CityDetails = $this->mobile_model->Featured_city();
    $response= array();
    $currency_val = array();

    if($CityDetails->num_rows()>0){
      foreach ($CityDetails->result() as $result){  
        $name=str_replace(' ','+',$result->name);
        $response[] = array("name" => trim(stripslashes($result->name)), "citythumb" => $result->citythumb,"image_url" => base_url().'images/city/'.trim(stripslashes($result->citythumb)), "property_url" => 'property?city='.$name);
      }
    }   

    $currency_symbol_query='SELECT * FROM '.CURRENCY.' where status = "Active"';
    $currency_symbol=$this->mobile_model->ExecuteQuery($currency_symbol_query);

    if($currency_symbol->num_rows() > 0)
    { 
      foreach($currency_symbol->result() as $cur){
      $currency_rate =0;

      $currency_val[] = array('id'=>intval($cur->id),'currency_symbol'=>$cur->currency_symbols,'currency_code'=>$cur->currency_type,"currency_value"=>0);
      }
    }

    if(($currency_symbol->num_rows() == 0) && ($CityDetails->num_rows() ==0) )
    { 
      $response_json = array('status'=>0,'message'=>$this->no_dta_found,'Home_page_details'=>$response,'currency_list'=>$currency_val);
    } else {
      $response_json = array('status'=>1,'message'=>$this->dta_found,'Home_page_details'=>$response,'currency_list'=>$currency_val);
    }   
    echo json_encode($response_json);
  }

  public function mobile_add_car_basicinfo()
  {
    $base_id = $this->input->post('base_id');
    $language_code = $this->input->post('lang_code');
    $user_id = $this->input->post('user_id');
    $vehicle_make = $this->input->post('vehicle_make');
    $vehicle_model = $this->input->post('vehicle_model');
    $vehicle_type = $this->input->post('vehicle_type');
    $vehicle_year = $this->input->post('vehicle_year');
    $address = $this->input->post('city');

    if($base_id == "" || $user_id == "" || $vehicle_make == "" || $vehicle_model == "" || $vehicle_type == "" || $vehicle_year == "" || $address == "")
    {
      $response_json = array('status'=>0,'message'=>$this->parm_missing);
      echo json_encode($response_json);
      exit();
    }

    if ($user_id=='')
    {
      $json_encode = json_encode(array('status'=>0,'message'=>$this->sign_bfr_listing));
      echo $json_encode;
    }
    else
    {
      $condition = array('id'=>$user_id,'status'=>'Active');
      $checkUser = $this->mobile_model->get_all_details(USERS,$condition);
      $cityArr = explode(',',$this->input->post('city'));

      if($checkUser->num_rows() == 1)
      {
        $data = array('make_id'=>$this->input->post('vehicle_make'),
                 'model_id'=>$this->input->post('vehicle_model'),
                 'type_id'=>$this->input->post('vehicle_type'),
                 'vehicle_type'=>$this->input->post('base_id'),
                 'year' => $vehicle_year,
                 'user_id'=>$user_id,
                 'instant_book'=>'Yes',
                 'request_to_book'=>'No',
                 'status'=>'UnPublish',
                );

        $this->mobile_model->simple_insert(VEHICLE,$data);
        $getInsertId=$this->mobile_model->get_last_insert_id(); 

        $lat = 0.00;
        $lang = 0.00;
        $address1 = urlencode($address);
        $google_map_api = $this->config->item('google_developer_key');
        $json = file_get_contents("https://maps.google.com/maps/api/geocode/json?address=$address1&sensor=false&key=$google_map_api");

        $json = json_decode($json); 
        $street = $city = $state = $country = $zip = "";
        $street = $json->{'results'}[0]->{'address_components'}[0]->{'long_name'};
        $city = $json->{'results'}[0]->{'address_components'}[1]->{'long_name'};
        $state = $json->{'results'}[0]->{'address_components'}[2]->{'long_name'};
        $country = $json->{'results'}[0]->{'address_components'}[3]->{'long_name'};
        $zip = $json->{'results'}[0]->{'address_components'}[4]->{'long_name'};
        $lat = $json->{'results'}[0]->{'geometry'}->{'location'}->{'lat'};
        $lang = $json->{'results'}[0]->{'geometry'}->{'location'}->{'lng'};

        $dataArr = array('vehicle_type'=>$this->input->post('base_id'),'vehicle_id' => $getInsertId, 'address' => $address, 'street' => $street, 'city' => $city, 'state' => $state, 'country' => $country, 'lat' => $lat, 'lang' => $lang);

        $this->mobile_model->simple_insert(VEHICLE_ADDRSS,$dataArr);
        $inputArr3=array();
        $inputArr3 = array('vehicle_id' =>$getInsertId,'vehicle_type'=>$this->input->post('base_id'));

        $this->mobile_model->simple_insert(VEHICLE_BOOKING,$inputArr3);
        $inputArr4=array();
        $inputArr4 = array('id' =>$getInsertId,'vehicle_type'=>$this->input->post('vehicle_type'));

        $this->mobile_model->simple_insert(VEHICLE_SCHEDULE,$inputArr4);
        $this->mobile_model->update_details(USERS,array('group'=>'Seller'),array('id'=>$user_id));

      }
      else
      {
        $json_encode = json_encode(array('status'=>0,'message'=>$this->rgstr_bfr_listing));
        echo $json_encode; 
        exit();
      }
    }

    $condition = array('p.id' => $getInsertId);
    $restaurant_detail = $this->mobile_model->get_all_veh_details($condition);

    if($restaurant_detail->num_rows() == 1)
    {
      $vehicles_basic_dtls = $this->get_vehicle_basic_dtls($vehicle_type,$language_code);
      $result = $this->get_vehicle_completed_steps($getInsertId,$vehicle_type,$language_code);

      $json_encode = json_encode(array("status" => 1,"message" => $this->veh_added,"vehicle_id"=>$getInsertId,"base_id"=>$vehicle_type,"result"=>$result['result_array'],"make" =>$vehicles_basic_dtls['make_details'],"model"=>$vehicles_basic_dtls['model_details'],"type"=>$vehicles_basic_dtls['type_details'],"attribute"=>$vehicles_basic_dtls['attribute'],"property_attributes" => $vehicles_basic_dtls['property_attributes'],"currency" => $vehicles_basic_dtls['currency']),JSON_PRETTY_PRINT);
      echo $json_encode; 
      exit();

    }
    else
    {
      $json_encode = json_encode(array("status" => 0,"message" => $this->no_dta_found,"vehicle_id"=>""),JSON_PRETTY_PRINT);
      echo $json_encode; exit();
    }

  }

  public function get_model()
  {
    $rental_type = $this->input->post ('base_id');
    $make_id = $this->input->post ('make_id');
    $language_code = $this->input->post ('lang_code');
    $model_lists = array();

    if($rental_type == "" || $make_id == "" || $language_code == "")
    { 
      $json_encode = json_encode(array("status" => 0,"message" => $this->parm_missing),JSON_PRETTY_PRINT);
      echo $json_encode; exit();
    }

    $models_list = $this->mobile_model->get_all_details(MODEL_MASTER, array('vehicle_type'=>$rental_type,'make_id'=>$make_id,'status'=>'Active')); 
    if($language_code =='en') { $field = 'model_name'; } else { $field='model_name_ph'; }
    if($models_list->num_rows() > 0 )
    {
      foreach($models_list->result() as $models)
      {
        $model_lists[] = array("model_id"=>$models->id,"model_type"=>$models->$field);
      }
      $json_encode = json_encode(array("status" => 1,"message" => $this->dta_found,"model"=>$model_lists),JSON_PRETTY_PRINT);
      echo $json_encode; exit();
    }
    else
    {
      $json_encode = json_encode(array("status" => 0,"message" => $this->no_dta_found),JSON_PRETTY_PRINT);
      echo $json_encode; exit();
    }

  }


  public function get_vehicle_completed_steps($vehicle_id = '',$vehicle_type = '',$language_code = '')
  {
    $instant_payment = $this->mobile_model->get_all_details(MODULES_MASTER, array('module_name' => 'payment_option'));
    $photos = array();
    $sometime_arr = array();

    $where1 = array('p.id'=>$vehicle_id);  

    $this->db->select('p.user_id,p.mileage,p.registration_number,p.driver_type,p.driver_id,p.vehicle_type,p.make_id,p.id,p.accommodates,p.model_id,p.type_id,p.booking_type,p.min_hour,p.min_days,p.min_hour_price,p.year,p.min_hour_exprice,p.day_price,p.weekly_price,p.monthly_price,p.currency,p.status,p.calendar_checked,pa.address,p.description,p.description_ph,p.veh_title,p.veh_title_ph,p.request_to_book,p.instant_book,p.other_things,p.other_things_ph,p.car_rules,p.car_rules_ph,p.important_info,p.important_info_ph,p.terms_condition,p.terms_condition_ph,p.list_name,p.listings,pa.address,pa.country,pa.state,pa.city,pa.street,pa.zip,pa.lat,pa.lang,p.cancellation_policy,p.cancellation_percentage,p.cancel_description,p.cancel_description_ph,p.security_deposit,p.meta_title,p.meta_keyword,p.meta_description,p.meta_title_ph,p.meta_keyword_ph,p.meta_description_ph,pb.title,pb.title_ph,pb.price,pb.price_type,pb.short_description,pb.short_description_ph');

    $this->db->from(VEHICLE.' as p');
    $this->db->join(VEHICLE_ADDRSS.' as pa',"pa.vehicle_id=p.id","LEFT");
    $this->db->join(VEHICLE_ADDITIONAL_PRICE.' as pb',"pb.vehicle_id=p.id","LEFT");
    $this->db->where($where1);
    $rental_details = $this->db->get(); 

    $completed_steps = 0; 
    $step_1_status=array('step_completed'=>false);
    $step_2_status=array('step_completed'=>false);
    $step_3_status=array('step_completed'=>false);
    $step_4_status=array('step_completed'=>false);
    $step_5_status=array('step_completed'=>false);
    $step_6_status=array('step_completed'=>false);
    $step_7_status=array('step_completed'=>false);
    $step_8_status=array('step_completed'=>false);
    $step_9_status=array('step_completed'=>false);
    $step_10_status=array('step_completed'=>false);
    $step_11_status=array('step_completed'=>false);
    $step_12_status=array('step_completed'=>false);

    foreach($rental_details->result() as $data)
    {
      if($data->address !='' && $data->make_id !='' && $data->model_id !='' && $data->type_id !='')
      {
        $step_1_status=array('step_completed'=>true);
        $completed_steps = $completed_steps + 1;          
      }

      $step_1_data = array("vehicle_id"=>intval($data->id),"base_id"=>$data->vehicle_type,"city"=>$data->address,"make_id"=>$data->make_id,"model_id"=>$data->model_id,"type_id"=>$data->type_id,"year"=>$data->year,"status"=>$data->status);

      $step1 = array_merge($step_1_status, $step_1_data);


      if($data->lat != '' && $data->lang != '')
      {
        $step_2_status=array('step_completed'=>true);
        $completed_steps = $completed_steps + 1;    
      }

      $step_2_data = array("vehicle_id"=>intval($data->id),"base_id"=>$data->vehicle_type,"address"=>$data->address,"country"=>$data->country,"state"=>$data->state,"city"=>$data->city,"street"=>$data->street,"zip"=>$data->zip,"lat"=>$data->lat,"long"=>$data->lang);

      $step2 = array_merge($step_2_status, $step_2_data);

      if($data->currency != '' && $data->booking_type != '')
      {
        $step_3_status=array('step_completed'=>true);
        $completed_steps = $completed_steps + 1;          
      }

      $saved_type = $data->booking_type; 
      $saved_booking_type = explode(",",$saved_type); 

      $daily_checked = false;
      $hourly_checked = false;
      $min_hours = '';
      $min_hour_price = '';
      $price_per_hour = '';
      $min_days = '';
      $price_per_day = '';
      $weekly_price = '';
      $monthly_price = '';

      if($data->min_hour == 0 || $data->min_hour == '')
      {
        $min_hours = '';
      }
      else
      {
        $min_hours = $data->min_hour;
      }

      if($data->min_hour_price == 0 || $data->min_hour_price == '')
      {
        $min_hour_price = '';
      }
      else
      {
        $min_hour_price = $data->min_hour_price;
      }

      if($data->min_hour_exprice == 0 || $data->min_hour_exprice == '')
      {
        $price_per_hour = '';
      }
      else
      {
        $price_per_hour = $data->min_hour_exprice;
      }

      if($data->min_days == 0 || $data->min_days == '')
      {
        $min_days = '';
      }
      else
      {
        $min_days = $data->min_days;
      }

      if($data->day_price == 0 || $data->day_price == '')
      {
        $price_per_day = '';
      }
      else
      {
        $price_per_day = $data->day_price;
      }

      if($data->weekly_price == 0 || $data->weekly_price == '')
      {
        $weekly_price = '';
      }
      else
      {
        $weekly_price = $data->weekly_price;
      }

      if($data->monthly_price == 0 || $data->monthly_price == '')
      {
        $monthly_price = '';
      }
      else
      {
        $monthly_price = $data->monthly_price;
      }

      if(in_array('1', $saved_booking_type) && !in_array('2', $saved_booking_type))
      { 
          $daily_checked = true;
      }
      elseif (!in_array('1', $saved_booking_type) && in_array('2', $saved_booking_type)) 
      {
          $hourly_checked = true; 
      }
      elseif (in_array('1', $saved_booking_type) && in_array('2', $saved_booking_type)) 
      {
          $daily_checked = true;
          $hourly_checked = true; 
      }
      else
      { 
          $daily_checked = false;
          $hourly_checked = false;
      }

      $step_3_data = array("vehicle_id"=>intval($data->id),"base_id"=>$data->vehicle_type,"currency_code"=>$data->currency,"daily_booking_status"=>$daily_checked,"hourly_booking_status"=>$hourly_checked,"min_hours"=>$min_hours,"min_hour_price"=>floatval($min_hour_price),"price_per_hour"=>floatval($price_per_hour),"min_days"=>$min_days,"price_per_day"=>floatval($price_per_day),"weekly_price"=>floatval($weekly_price),"monthly_price"=>floatval($monthly_price));

      $step3 = array_merge($step_3_status, $step_3_data);

      if($data->calendar_checked != '')
      {
        $step_4_status=array('step_completed'=>true);
        $completed_steps = $completed_steps + 1;    
      }
      
      /* schedule starts here */
      $query = $this->db->query("SELECT * FROM `fc_vehicle_bookings_dates` WHERE vehicle_id=".$data->id." AND tot_checked_in <> ''");
      $rows_res = $query->result_array(); 
   
      $dateeee=array();

      if(!empty($rows_res)) 
      {
        $datee = '';
        $stime = '';
        $etime = '';
        $timee = '';
        $wholedt = '';
        $dayArr1 = array();
        $dayArr2 = array();

        foreach($rows_res  as $date)
        {
          $start=$date['tot_checked_in'];
          $startDate=date('Y-m-d',strtotime($start));
          $startTime=date('H:i',strtotime($start)); 
          $end=$date['tot_checked_out'];
          $endDate=date('Y-m-d',strtotime($end));
          $endTime=date('H:i',strtotime($end));
          $state = $date['id_state'];

          $datesbetween = $this->createRange($startDate, $endDate, $format = 'Y-m-d');
          
          foreach($datesbetween as $day)
          {
            if(count($datesbetween) == 1 ){ 
              $datee=$day;
              $timee= $startTime.'' . '-' . ''.$endTime;
            }
            else
            {
              if ($day==$startDate){
                $datee = $day;
                $timee= $startTime.'' .  '-' . ''.'23:00' ;
              }else if ($day==$endDate){
                $datee =$day;
                $timee='00:00'.'' .  '-' . ''.$endTime ;
              }
              else{
                $datee = $day;  
                $timee='00:00'.'' .  '-' . ''.'23:00';
              } 
            } 

            /*$value=array();*/

            if(in_array($datee, array_column($dateeee, 'date')) && in_array($timee, array_column($dateeee, 'time'))) 
            {
              $key = array_search($datee, array_column($dateeee, 'date'));
              $value=$timee;
              $dateeee[$key]=array('date'=>$datee,'time'=>$value,"state"=>$state);
            }
            else
            {
              $value=$timee;
              $dateeee[]=array('date'=>$datee,'time'=>$value,"state"=>$state);
            }
          } 
        }
      }
      /* schedule ends here */

      $step_4_data = array("vehicle_id"=>intval($data->id),"base_id"=>$data->vehicle_type,"booked_Dates"=>$dateeee,"daily_booking_status"=>$daily_checked,"hourly_booking_status"=>$hourly_checked,);

      $step4 = array_merge($step_4_status, $step_4_data);

      if($data->veh_title !='')
      {
        $step_5_status=array('step_completed'=>true);
        $completed_steps = $completed_steps + 1;          
      }

      $veh_title_en = '';
      $veh_desc_en = '';
      $veh_title_ph = '';
      $veh_desc_ph = '';

      if($data->veh_title == ''){  $veh_title_en = ''; }else{  $veh_title_en = $data->veh_title; }

      if($data->veh_title_ph == ''){ $veh_title_ph = ''; }else{ $veh_title_ph = $data->veh_title_ph; }

      if($data->description == ''){ $veh_desc_en = ''; }else{ $veh_desc_en = $data->description; }

      if($data->description_ph == ''){  $veh_desc_ph = ''; }else{ $veh_desc_ph = $data->description_ph; }

      $step_5_data = array("vehicle_id"=>intval($data->id),"base_id"=>$data->vehicle_type,"title_en"=>$veh_title_en,"title_fil"=>$veh_title_ph,"description"=>$veh_desc_en,"description_fil"=>$veh_desc_ph);

      $step5 = array_merge($step_5_status, $step_5_data);

      if($data->important_info !='')
      {
        $step_6_status=array('step_completed'=>true);
        $completed_steps = $completed_steps + 1;          
      }

      $veh_import_info_en = '';
      $veh_import_info_ph = '';
      $veh_terms_en = '';
      $veh_terms_ph = '';
      $veh_car_rules_en = '';
      $veh_car_rules_ph = '';
      $veh_other_en = '';
      $veh_other_ph = '';

      if($data->important_info == ''){  $veh_import_info_en = ''; }else{  $veh_import_info_en = $data->important_info; }

      if($data->important_info_ph == ''){  $veh_import_info_ph = ''; }else{  $veh_import_info_ph = $data->important_info_ph; }

      if($data->terms_condition == ''){  $veh_terms_en = ''; }else{  $veh_terms_en = $data->terms_condition; }

      if($data->terms_condition_ph == ''){  $veh_terms_ph = ''; }else{  $veh_terms_ph = $data->terms_condition_ph; }

      if($data->car_rules == ''){  $veh_car_rules_en = ''; }else{  $veh_car_rules_en = $data->car_rules; }

      if($data->car_rules_ph == ''){  $veh_car_rules_ph = ''; }else{  $veh_car_rules_ph = $data->car_rules_ph; }

      if($data->other_things == ''){  $veh_other_en = ''; }else{  $veh_other_en = $data->other_things; }

      if($data->other_things_ph == ''){  $veh_other_ph = ''; }else{  $veh_other_ph = $data->other_things_ph; }

      $step_6_data = array("vehicle_id"=>intval($data->id),"base_id"=>$data->vehicle_type,"important_info"=>$veh_import_info_en,"important_info_ph"=>$veh_import_info_ph,"terms_condition"=>$veh_terms_en,"terms_condition_ph"=>$veh_terms_ph,"car_rules"=>$veh_car_rules_en,"car_rules_ph"=>$veh_car_rules_ph,"things_to_note"=>$veh_other_en,"things_to_note_ph"=>$veh_other_ph);

      $step6 = array_merge($step_6_status, $step_6_data);

      $photos = array();

      $veh_photo=$this->mobile_model->get_selected_fields_records('id,image',VEHICLE_PHOTOS,' where vehicle_id='.$data->id);

      if($veh_photo->num_rows()>0)
      {
        $step_7_status=array('step_completed'=>true);
        $completed_steps = $completed_steps + 1;    
      }

      foreach ($veh_photo->result() as $value) {
        $photos[] =array("image_id"=>intval($value->id),"photos"=>base_url().'images/vehicles/'.$value->image); 
      }

      $step_7_data = array("vehicle_id"=>intval($data->id),"base_id"=>$data->vehicle_type,"photos"=>$photos);
      $step7 = array_merge($step_7_status, $step_7_data);

      if($data->list_name !='')
      {
        $step_8_status=array('step_completed'=>true);
        $completed_steps = $completed_steps + 1;          
      }

      $step_8_data = array("vehicle_id"=>intval($data->id),"base_id"=>$data->vehicle_type,"amenities_id"=>$data->list_name);
      $step8 = array_merge($step_8_status, $step_8_data);

      /*Not mandatory*/
      $step_9_status=array('step_completed'=>true);
      $completed_steps = $completed_steps + 1;

      $additional_dtls = array();
      $additional_price_list = $this->mobile_model->get_all_details(VEHICLE_ADDITIONAL_PRICE, array('vehicle_id' => $data->id));

      if ($additional_price_list->num_rows() > 0) 
      {
        foreach ($additional_price_list->result() as $row) 
        {
          /*if($language_code == 'en')
          {
            $rentalNameField=$row->title;
            $descField=$row->short_description;
          }
          else
          {
            $titleNameField='title_ph';
            $descNameField='short_description_ph';

            if($row->$titleNameField == '') 
            { 
              $rentalNameField=$row->title;
            }
            else
            {
              $rentalNameField=$row->$titleNameField;
            }

            if($row->$descNameField == '') 
            { 
              $descField=$row->short_description;
            }
            else
            {
              $descField=$row->$descNameField;
            }
          }*/
          if($row->title_ph != ""){$rentalNameField_ph=$row->title_ph;}else{$rentalNameField_ph="";}
          if($row->title_ph != ""){$descField_ph=$row->short_description_ph;}else{$descField_ph="";}

          $additional_dtls[] =array("addtnl_id"=>$row->id,"title"=>$row->title,"title_ph"=>$rentalNameField_ph,"max_limit"=>$row->max_limit,"price"=>floatval($row->price),"price_type"=>$row->price_type,"description"=>$row->short_description,"description_ph"=>$descField_ph); 
        }
      }

      $attributes = array();
      $step_9_data = array("vehicle_id"=>intval($data->id),"base_id"=>$data->vehicle_type,"additional_price"=>$additional_dtls);
      $step9 = array_merge($step_9_status, $step_9_data);

      if($data->listings !='')
      {
        $step_10_status=array('step_completed'=>true);
        $completed_steps = $completed_steps + 1; 
        $attributes[] = json_decode($data->listings); 
      }

      $step_10_data = array("vehicle_id"=>intval($data->id),"base_id"=>$data->vehicle_type,"list_id"=>$attributes);
      $step10 = array_merge($step_10_status, $step_10_data);

      $self_drive_status = false;
      $with_drive_status = false;

      if($data->registration_number !='')
      {
        $step_11_status=array('step_completed'=>true);
        $completed_steps = $completed_steps + 1;          
      }

      if(in_array('self_drive',explode(",",$data->driver_type))) 
      {
        $self_drive_status = true;
      }

      if(in_array('with_drive',explode(",",$data->driver_type))) 
      {
        $with_drive_status = true;
      }

      $driverDetails = array();

      $driverdtls = $this->mobile_model->get_all_details(DRIVER_MASTER, array('host_id'=>$data->user_id));

      foreach($driverdtls->result() as $driverDetail)
      {
        $driverDetails[] = array("driver_id"=>$driverDetail->id,"driver_name"=>$driverDetail->driver_name,"driver_age"=>$driverDetail->age,"driver_email"=>$driverDetail->email,"driver_insurance_number"=>$driverDetail->insurance_num,"driver_license_number"=>$driverDetail->license_num,"license_expiry_year"=>$driverDetail->license_expiry_year,"license_expiry_month"=>$driverDetail->license_expiry_month,"phone_number"=>$driverDetail->contact_num);
      }

      $step_11_data = array("vehicle_id"=>intval($data->id),"base_id"=>$data->vehicle_type,"mileage"=>$data->mileage,"registration_number"=>$data->registration_number,"self_drive_status"=>$self_drive_status,"with_drive_status"=>$with_drive_status,"driver_details"=>$driverDetails);
      $step11 = array_merge($step_11_status, $step_11_data);

      if($data->cancellation_policy !='')
      {
        $step_12_status=array('step_completed'=>true);
        $completed_steps = $completed_steps + 1;          
      }

      if($data->meta_title_ph == ""){ $meta_title_ph = ""; }else{ $meta_title_ph = $data->meta_title_ph; }

      if($data->meta_keyword_ph == ""){ $meta_keyword_ph = ""; }else{ $meta_keyword_ph = $data->meta_keyword_ph; }

      if($data->meta_description_ph == ""){ $meta_description_ph = ""; }else{ $meta_description_ph = $data->meta_description_ph; }

      $step_12_data = array("vehicle_id"=>intval($data->id),"base_id"=>$data->vehicle_type,"cancellation_policy"=>$data->cancellation_policy,"return_amount"=>$data->cancellation_percentage,"cancel_description"=>$data->cancel_description,"cancel_description_fil"=>$data->cancel_description_ph,"security_deposit"=>$data->security_deposit,"meta_title"=>$data->meta_title,"meta_title_fil"=>$meta_title_ph,"meta_keyword"=>$data->meta_keyword,"meta_keyword_fil"=>$meta_keyword_ph,"meta_description"=>$data->meta_description,"meta_description_fil"=>$meta_description_ph);

      $step12 = array_merge($step_12_status, $step_12_data);

    }

    $result_arr[] = array("step1"=>$step1,"step2"=>$step2,"step3"=>$step3,"step4"=>$step4,"step5"=>$step5,"step6"=>$step6,"step7"=>$step7,"step8"=>$step8,"step9"=>$step9,"step10"=>$step10,"step11"=>$step11,"step12"=>$step12);

    return array("result_array"=>$result_arr);

  }

  //end of car step1

  public function mobile_add_car_address()
  {
    $vehicle_type = $this->input->post('base_id');
    $user_id = $this->input->post('user_id');
    $vehicle_currency =$this->input->post('currency_code');
    $vehicle_id =$this->input->post('vehicle_id');
    $language_code = $this->input->post('lang_code');
    $lat = $this->input->post('latitude');
    $lang = $this->input->post('longitude');
    $addr = $this->input->post('address_location');
    $prd_id = array('vehicle_id' => $vehicle_id);

    if($addr == '' || $vehicle_id == '' || $vehicle_type == '')
    {
      $json_encode = json_encode(array("status" => 0,"message" => $this->parm_missing),JSON_PRETTY_PRINT);
      echo $json_encode; exit();
    }
    
    if ($lat == "" || $lang == "") {
      $newAddress = '';
      if ($this->input->post('address_location') != '') $newAddress .= ',' . $this->input->post('address_location');
      if ($this->input->post('city') != '') $newAddress .= ',' . $this->input->post('city');
      if ($this->input->post('state') != '') $newAddress .= ',' . $this->input->post('state');
      if ($this->input->post('country') != '') $newAddress .= ',' . $this->input->post('country');
      if ($this->input->post('post_code') != '') $newAddress .= ',' . $this->input->post('post_code');
      $address = str_replace(" ", "+", $newAddress);
      $google_map_api = $this->config->item('google_developer_key');
      $bing_map_api = $this->config->item('bing_developer_key');
      $address_details = $this->get_address_bound($address, $google_map_api, $bing_map_api);
      $lat = $address_details['lat'];
      $lang = $address_details['long'];
    }

    $dataArr = array('address' => $this->input->post('address_location'), 'country' => $this->input->post('country'), 'state' => $this->input->post('state'), 'city' => $this->input->post('city'), 'street' => $this->input->post('street'), 'zip' => $this->input->post('post_code'), 'lat' => $lat, 'lang' => $lang);

    $data = array_merge($dataArr, $prd_id);

    $veh_Detail = $this->mobile_model->get_all_details(VEHICLE_ADDRSS, array('vehicle_id' => $vehicle_id));

    if ($veh_Detail->num_rows() > 0) 
    {
        $this->mobile_model->update_details(VEHICLE_ADDRSS, $dataArr, array('vehicle_id' => $vehicle_id));
    } else {
        $this->mobile_model->simple_insert(VEHICLE_ADDRSS, $data);
    }

    $vehicle_Detail = $this->mobile_model->get_all_details(VEHICLE, array('id' => $vehicle_id));

    if($vehicle_Detail->num_rows() == 1)
    {
      $vehicles_basic_dtls = $this->get_vehicle_basic_dtls($vehicle_type,$language_code);
      $result = $this->get_vehicle_completed_steps($vehicle_id,$vehicle_type,$language_code);

      $json_encode = json_encode(array("status" => 1,"message" => $this->addr_updt,"vehicle_id"=>intval($vehicle_id),"base_id"=>$vehicle_type,"result"=>$result['result_array'],"make" =>$vehicles_basic_dtls['make_details'],"model"=>$vehicles_basic_dtls['model_details'],"type"=>$vehicles_basic_dtls['type_details'],"attribute"=>$vehicles_basic_dtls['attribute'],"property_attributes" => $vehicles_basic_dtls['property_attributes'],"currency" => $vehicles_basic_dtls['currency']),JSON_PRETTY_PRINT);
      echo $json_encode; 
      exit();

    }
    else
    {
      $json_encode = json_encode(array("status" => 0,"message" => $this->no_dta_found,"vehicle_id"=>""),JSON_PRETTY_PRINT);
      echo $json_encode; exit();
    }

  }

  public function mobile_add_car_price()
  {
    $base_id = $this->input->post('base_id');
    $user_id = $this->input->post('user_id');
    $vehicle_id = $this->input->post('vehicle_id');
    $book_type = $this->input->post('booking_type'); 

    if($book_type == '' || $vehicle_id == '')
    {
      $json_encode = json_encode(array("status" => 0,"message" => $this->parm_missing),JSON_PRETTY_PRINT);
      echo $json_encode; exit();
    }

    $book_type = explode(',', $book_type);

    if(in_array('1', $book_type) && !in_array('2', $book_type))
    { 
        $min_hour = '0';
        $min_hour_price = '0.00';
        $min_hour_exprice = '0.00';
        $min_days = $this->input->post('min_days');
        $day_price = $this->input->post('day_price');
        $weekly_price = $this->input->post('weekly_price');
        $monthly_price = $this->input->post('monthly_price');
    }
    elseif (!in_array('1', $book_type) && in_array('2', $book_type))
    { 
        $min_hour = $this->input->post('min_hour');
        $min_hour_price = $this->input->post('min_hour_price');
        $min_hour_exprice = $this->input->post('min_hour_exprice');
        $min_days = '0';
        $day_price = '0.00';
        $weekly_price = '0.00';
        $monthly_price = '0.00';
    }
    elseif (in_array('1', $book_type) && in_array('2', $book_type))
    { 
        $min_hour = $this->input->post('min_hour');
        $min_hour_price = $this->input->post('min_hour_price');
        $min_hour_exprice = $this->input->post('min_hour_exprice');
        $min_days = $this->input->post('min_days');
        $day_price = $this->input->post('day_price');
        $weekly_price = $this->input->post('weekly_price');
        $monthly_price = $this->input->post('monthly_price');
    }
    else
    {
        $min_hour = $this->input->post('min_hour');
        $min_hour_price = $this->input->post('min_hour_price');
        $min_hour_exprice = $this->input->post('min_hour_exprice');
        $min_days = $this->input->post('min_days');
        $day_price = $this->input->post('day_price');
        $weekly_price = $this->input->post('weekly_price');
        $monthly_price = $this->input->post('monthly_price');
    }

    $data = array('currency' => $this->input->post('currency'), 'min_hour' => $min_hour,'min_hour_price' => $min_hour_price,'min_hour_exprice'=> $min_hour_exprice,'min_days' => $min_days,'day_price' => $day_price, 'weekly_price' => $weekly_price, 'monthly_price' => $monthly_price, 'booking_type' => $this->input->post('booking_type'));           

    $this->mobile_model->update_details(VEHICLE,$data,array('id'=>$vehicle_id));

    $vehicle_Detail = $this->mobile_model->get_all_details(VEHICLE, array('id' => $vehicle_id));

    if($vehicle_Detail->num_rows() == 1)
    {
      $vehicles_basic_dtls = $this->get_vehicle_basic_dtls($base_id,$language_code);
      $result = $this->get_vehicle_completed_steps($vehicle_id,$base_id,$language_code);

      $json_encode = json_encode(array("status" => 1,"message" => $this->price_updt,"vehicle_id"=>intval($vehicle_id),"base_id"=>$base_id,"result"=>$result['result_array'],"make" =>$vehicles_basic_dtls['make_details'],"model"=>$vehicles_basic_dtls['model_details'],"type"=>$vehicles_basic_dtls['type_details'],"attribute"=>$vehicles_basic_dtls['attribute'],"property_attributes" => $vehicles_basic_dtls['property_attributes'],"currency" => $vehicles_basic_dtls['currency']),JSON_PRETTY_PRINT);
      echo $json_encode; 
      exit();
    }
    else
    {
      $json_encode = json_encode(array("status" => 0,"message" => $this->no_dta_found,"vehicle_id"=>""),JSON_PRETTY_PRINT);
      echo $json_encode; exit();
    }

  }

  
  public function get_booked_veh_hours()
  {
    $productid = $_POST["vehicle_id"];
    $productstartdate = $_POST["startdate"];
    $productenddate = $_POST["enddate"];

    if($productid == '' || $productstartdate == '' || $productenddate == '')
    {
      $json_encode = json_encode(array("status" => 0,"message"=>$this->parm_missing),JSON_PRETTY_PRINT);
      echo $json_encode; exit();
    }

    $disable_time = array();
    $productvalues=$this->mobile_model->get_all_details(VEHICLE_BOOKING_DATES,array('vehicle_id'=>$productid));
    $productvalues_decode=json_decode($productvalues->result()[0]->data,TRUE);

    $datesbetween=$this->createRange(date('Y-m-d', strtotime($productstartdate)), date('Y-m-d', strtotime($productenddate)), $format = 'Y-m-d');

    foreach($productvalues_decode as $pp=>$kk) 
    {
      foreach($datesbetween as $dtsbet) 
      {
        if(trim($dtsbet)==trim($pp)) 
        {
          if($kk['00:00']!='') $pric00m=$kk['00:00']; else $pric00m="";
          if($kk['01:00']!='') $pric01m=$kk['01:00']; else $pric01m="";
          if($kk['02:00']!='') $pric02m=$kk['02:00']; else $pric02m="";
          if($kk['03:00']!='') $pric03m=$kk['03:00']; else $pric03m="";
          if($kk['04:00']!='') $pric04m=$kk['04:00']; else $pric04m="";
          if($kk['05:00']!='') $pric05m=$kk['05:00']; else $pric05m="";
          if($kk['06:00']!='') $pric06m=$kk['06:00']; else $pric06m="";
          if($kk['07:00']!='') $pric07m=$kk['07:00']; else $pric07m="";
          if($kk['08:00']!='') $pric08m=$kk['08:00']; else $pric08m="";
          if($kk['09:00']!='') $pric09m=$kk['09:00']; else $pric09m="";
          if($kk['10:00']!='') $pric10m=$kk['10:00']; else $pric10m="";
          if($kk['11:00']!='') $pric11m=$kk['11:00']; else $pric11m="";
          if($kk['12:00']!='') $pric12m=$kk['12:00']; else $pric12m="";
          if($kk['13:00']!='') $pric13m=$kk['13:00']; else $pric13m="";
          if($kk['14:00']!='') $pric14m=$kk['14:00']; else $pric14m="";
          if($kk['15:00']!='') $pric15m=$kk['15:00']; else $pric15m="";
          if($kk['16:00']!='') $pric16m=$kk['16:00']; else $pric16m="";
          if($kk['17:00']!='') $pric17m=$kk['17:00']; else $pric17m="";
          if($kk['18:00']!='') $pric18m=$kk['18:00']; else $pric18m="";
          if($kk['19:00']!='') $pric19m=$kk['19:00']; else $pric19m="";
          if($kk['20:00']!='') $pric20m=$kk['20:00']; else $pric20m="";
          if($kk['21:00']!='') $pric21m=$kk['21:00']; else $pric21m="";
          if($kk['22:00']!='') $pric22m=$kk['22:00']; else $pric22m="";
          if($kk['23:00']!='') $pric23m=$kk['23:00']; else $pric23m="";
          
          $string1=array($pric00m, $pric01m,$pric02m,$pric03m,$pric04m,$pric05m,$pric06m,$pric07m,$pric08m,$pric09m,$pric10m,$pric11m,$pric12m,$pric13m,$pric14m,$pric15m,$pric16m,$pric17m,$pric18m,$pric19m,$pric20m,$pric21m,$pric22m,$pric23m);
        }
      }
    }

    $manage_list=1; 
    $disable_time=$this->disable_date_time($productid,date('Y-m-d', strtotime($productstartdate)),$manage_list,$productvalues->result()[0]->vehicle_type);

    $json_encode = json_encode(array("status" => 1,"unavailable_time"=>$disable_time),JSON_PRETTY_PRINT);
    echo $json_encode; exit();

  }

  public function createRange($start, $end, $format = 'Y-m-d') {
    $start  = new DateTime($start);
    $end    = new DateTime($end);
    $invert = $start > $end;

    $dates = array();
    $dates[] = $start->format($format);
    while ($start != $end) {
      $start->modify(($invert ? '-' : '+') . '1 day');
      $dates[] = $start->format($format);
    }
    return $dates;
  }

  /*daily-for unavailable
{"2019-01-18":{"available":"0","bind":0,"info":"","notes":"","price":"","promo":"","00:00":{"price":"","time_status":"available"},"01:00":{"price":"","time_status":"available"},"02:00":{"price":"","time_status":"available"},"03:00":{"price":"","time_status":"available"},"04:00":{"price":"","time_status":"available"},"05:00":{"price":"","time_status":"available"},"06:00":{"price":"","time_status":"available"},"07:00":{"price":"","time_status":"available"},"08:00":{"price":"","time_status":"available"},"09:00":{"price":"","time_status":"available"},"10:00":{"price":"","time_status":"available"},"11:00":{"price":"","time_status":"available"},"12:00":{"price":"","time_status":"available"},"13:00":{"price":"","time_status":"available"},"14:00":{"price":"","time_status":"available"},"15:00":{"price":"","time_status":"available"},"16:00":{"price":"","time_status":"available"},"17:00":{"price":"","time_status":"available"},"18:00":{"price":"","time_status":"available"},"19:00":{"price":"","time_status":"available"},"20:00":{"price":"","time_status":"available"},"21:00":{"price":"","time_status":"available"},"22:00":{"price":"","time_status":"available"},"23:00":{"price":"","time_status":"available"}}}
*/
/*daily-for booked
{"2019-01-11":{"available":"2","bind":0,"info":"","notes":"","price":"","promo":"","00:00":{"price":"","time_status":"available"},"01:00":{"price":"","time_status":"available"},"02:00":{"price":"","time_status":"available"},"03:00":{"price":"","time_status":"available"},"04:00":{"price":"","time_status":"available"},"05:00":{"price":"","time_status":"available"},"06:00":{"price":"","time_status":"available"},"07:00":{"price":"","time_status":"available"},"08:00":{"price":"","time_status":"available"},"09:00":{"price":"","time_status":"available"},"10:00":{"price":"","time_status":"available"},"11:00":{"price":"","time_status":"available"},"12:00":{"price":"","time_status":"available"},"13:00":{"price":"","time_status":"available"},"14:00":{"price":"","time_status":"available"},"15:00":{"price":"","time_status":"available"},"16:00":{"price":"","time_status":"available"},"17:00":{"price":"","time_status":"available"},"18:00":{"price":"","time_status":"available"},"19:00":{"price":"","time_status":"available"},"20:00":{"price":"","time_status":"available"},"21:00":{"price":"","time_status":"available"},"22:00":{"price":"","time_status":"available"},"23:00":{"price":"","time_status":"available"}}}
*/
/*daily-for available
{"2019-01-11":{"available":"1","bind":0,"info":"","notes":"","price":"","promo":"","00:00":{"price":"","time_status":"available"},"01:00":{"price":"","time_status":"available"},"02:00":{"price":"","time_status":"available"},"03:00":{"price":"","time_status":"available"},"04:00":{"price":"","time_status":"available"},"05:00":{"price":"","time_status":"available"},"06:00":{"price":"","time_status":"available"},"07:00":{"price":"","time_status":"available"},"08:00":{"price":"","time_status":"available"},"09:00":{"price":"","time_status":"available"},"10:00":{"price":"","time_status":"available"},"11:00":{"price":"","time_status":"available"},"12:00":{"price":"","time_status":"available"},"13:00":{"price":"","time_status":"available"},"14:00":{"price":"","time_status":"available"},"15:00":{"price":"","time_status":"available"},"16:00":{"price":"","time_status":"available"},"17:00":{"price":"","time_status":"available"},"18:00":{"price":"","time_status":"available"},"19:00":{"price":"","time_status":"available"},"20:00":{"price":"","time_status":"available"},"21:00":{"price":"","time_status":"available"},"22:00":{"price":"","time_status":"available"},"23:00":{"price":"","time_status":"available"}}}
*/
/*hourly-for unavailable
{"2019-01-18":{"available":"0","bind":0,"info":"","notes":"","price":"","promo":"","00:00":{"price":"","time_status":"unavailable"},"01:00":{"price":"","time_status":"unavailable"},"02:00":{"price":"","time_status":"unavailable"},"03:00":{"price":"","time_status":"unavailable"},"04:00":{"price":"","time_status":"unavailable"},"05:00":{"price":"","time_status":"unavailable"},"06:00":{"price":"","time_status":"unavailable"},"07:00":{"price":"","time_status":"unavailable"},"08:00":{"price":"","time_status":"unavailable"},"09:00":{"price":"","time_status":"unavailable"},"10:00":{"price":"","time_status":"unavailable"},"11:00":{"price":"","time_status":"available"},"12:00":{"price":"","time_status":"available"},"13:00":{"price":"","time_status":"available"},"14:00":{"price":"","time_status":"available"},"15:00":{"price":"","time_status":"available"},"16:00":{"price":"","time_status":"available"},"17:00":{"price":"","time_status":"available"},"18:00":{"price":"","time_status":"available"},"19:00":{"price":"","time_status":"available"},"20:00":{"price":"","time_status":"available"},"21:00":{"price":"","time_status":"available"},"22:00":{"price":"","time_status":"available"},"23:00":{"price":"","time_status":"available"}}}
*/
/*hourly-for booked
{"2019-01-18":{"available":"2","bind":0,"info":"","notes":"","price":"","promo":"","00:00":{"price":"","time_status":"booked"},"01:00":{"price":"","time_status":"booked"},"02:00":{"price":"","time_status":"booked"},"03:00":{"price":"","time_status":"booked"},"04:00":{"price":"","time_status":"booked"},"05:00":{"price":"","time_status":"booked"},"06:00":{"price":"","time_status":"booked"},"07:00":{"price":"","time_status":"booked"},"08:00":{"price":"","time_status":"booked"},"09:00":{"price":"","time_status":"booked"},"10:00":{"price":"","time_status":"booked"},"11:00":{"price":"","time_status":"available"},"12:00":{"price":"","time_status":"available"},"13:00":{"price":"","time_status":"available"},"14:00":{"price":"","time_status":"available"},"15:00":{"price":"","time_status":"available"},"16:00":{"price":"","time_status":"available"},"17:00":{"price":"","time_status":"available"},"18:00":{"price":"","time_status":"available"},"19:00":{"price":"","time_status":"available"},"20:00":{"price":"","time_status":"available"},"21:00":{"price":"","time_status":"available"},"22:00":{"price":"","time_status":"available"},"23:00":{"price":"","time_status":"available"}}}
*/
/*hourly-for available
{"2019-01-18":{"available":"1","bind":0,"info":"","notes":"","price":"","promo":"","00:00":{"price":"","time_status":"available"},"01:00":{"price":"","time_status":"available"},"02:00":{"price":"","time_status":"available"},"03:00":{"price":"","time_status":"available"},"04:00":{"price":"","time_status":"available"},"05:00":{"price":"","time_status":"available"},"06:00":{"price":"","time_status":"available"},"07:00":{"price":"","time_status":"available"},"08:00":{"price":"","time_status":"available"},"09:00":{"price":"","time_status":"available"},"10:00":{"price":"","time_status":"available"},"11:00":{"price":"","time_status":"available"},"12:00":{"price":"","time_status":"available"},"13:00":{"price":"","time_status":"available"},"14:00":{"price":"","time_status":"available"},"15:00":{"price":"","time_status":"available"},"16:00":{"price":"","time_status":"available"},"17:00":{"price":"","time_status":"available"},"18:00":{"price":"","time_status":"available"},"19:00":{"price":"","time_status":"available"},"20:00":{"price":"","time_status":"available"},"21:00":{"price":"","time_status":"available"},"22:00":{"price":"","time_status":"available"},"23:00":{"price":"","time_status":"available"}}}
*/
  public function mobile_add_car_calendar()
  {
    $vehicle_type = $this->input->post('base_id');
    $language_code = $this->input->post('lang_code');
    $propId = $this->input->post('vehicle_id');
    $user_id = $this->input->post('user_id');
    $blocking_type = $this->input->post('blocking_type');
    $values = json_decode($this->input->post('schedule'));

    if($vehicle_type == '' || $language_code == '' || $propId == '' || $user_id == '' || $blocking_type == '' || $values == '')
    {
      $json_encode = json_encode(array("status" => 0,"message" => $this->parm_missing),JSON_PRETTY_PRINT);
      echo $json_encode; exit();
    }

    $dat_arr = get_object_vars($values); 
    if($blocking_type=='daily')
    {
      foreach($dat_arr as $key=>$val)
      {
        $dateIs=$key;
        $insertDate = date('Y-m-d H:i:s',strtotime($dateIs));
        $availalbeStatus = $val->available;
        $checkedOut = date('Y-m-d 23:00:00',strtotime($insertDate));
        $tot = $insertDate.'-'.$checkedOut;
        if($availalbeStatus == 0 || $availalbeStatus == 2)
        {
          if($availalbeStatus == 0) { $id_state = '4'; } if($availalbeStatus == 2) { $id_state = '1'; } 
          $query = "INSERT INTO  `fc_vehicle_bookings_dates` (  `vehicle_type`,`vehicle_id` ,  `the_date` , `id_state` ,`id_item`,`checked_in`,`checked_out`,`tot_checked_in`,`tot_checked_out`,`tot_time`,`added_by`,`added_userid` ) VALUES (".$vehicle_type.",".$propId.", '".$key."',".$id_state.",1,'00:00','23:00','".$insertDate."','".$checkedOut."','".$tot."','host',".$user_id.")";
          /*mysqli_query($conn, $query);*/
          $this->mobile_model->ExecuteQuery($query);
        }
        else
        {
          $query = "delete from `fc_vehicle_bookings_dates` where `vehicle_id`=".$propId." and `the_date`='".$key."' AND (id_state = '1' OR id_state = '4') AND id_booking = '0'";
          $this->mobile_model->ExecuteQuery($query);
        }
      }
    }
    else
    {
      $startTime = '';
      foreach($dat_arr as $key=>$val)
      {
        $dateIs=$key;
        $availalbeStatus = $val->available;
        if($availalbeStatus == 0 || $availalbeStatus == 2)
        {
          if($availalbeStatus == 0) { $id_state = '4'; } if($availalbeStatus == 2) { $id_state = '1'; } 
          foreach($val as $key1=>$res)
          {
            if($key1!='available' && $key1!='bind' && $key1!='info' && $key1!='notes' && $key1!='price' && $key1!='promo' && $key1!='status')
            {
              if($res->time_status=='unavailable' || $res->time_status=='booked')
              {
                if($startTime=='')
                {
                  $startTime = $key1;
                }
                $endTime = $key1;
              }
            }
          }

          $insertDate = $dateIs." ".$startTime;
          $insertDate = date('Y-m-d H:i:s',strtotime($insertDate));
          $checkedOut = $dateIs." ".$endTime;
          $checkedOut = date('Y-m-d H:i:s',strtotime($checkedOut));
          $tot = $insertDate.'-'.$checkedOut;
          $query = "INSERT INTO  `fc_vehicle_bookings_dates` (  `vehicle_type`,`vehicle_id` ,  `the_date` , `id_state` ,`id_item`,`checked_in`,`checked_out`,`tot_checked_in`,`tot_checked_out`,`tot_time`,`added_by`,`added_userid` ) VALUES (".$vehicle_type.",".$propId.", '".$key."',".$id_state.",1,'".$startTime."','".$endTime."','".$insertDate."','".$checkedOut."','".$tot."','host',".$user_id.")";
          $this->mobile_model->ExecuteQuery($query);
        }
        else
        {
          $query = "delete from `fc_vehicle_bookings_dates` where `vehicle_id`=".$propId." and `the_date`='".$key."' AND (id_state = '1' OR id_state = '4') AND id_booking = '0'";
          $this->mobile_model->ExecuteQuery($query);
        }
      }
    }

    $vehicle_Detail = $this->mobile_model->get_all_details(VEHICLE, array('id' => $propId));

    if($vehicle_Detail->num_rows() == 1)
    {
      $vehicles_basic_dtls = $this->get_vehicle_basic_dtls($vehicle_type,$language_code);
      $result = $this->get_vehicle_completed_steps($propId,$vehicle_type,$language_code);

      $json_encode = json_encode(array("status" => 1,"message" => $this->succ,"vehicle_id"=>intval($propId),"base_id"=>$vehicle_type,"result"=>$result['result_array'],"make" =>$vehicles_basic_dtls['make_details'],"model"=>$vehicles_basic_dtls['model_details'],"type"=>$vehicles_basic_dtls['type_details'],"attribute"=>$vehicles_basic_dtls['attribute'],"property_attributes" => $vehicles_basic_dtls['property_attributes'],"currency" => $vehicles_basic_dtls['currency']),JSON_PRETTY_PRINT);
      echo $json_encode; 
      exit();

    }
    else
    {
      $json_encode = json_encode(array("status" => 0,"message" => $this->no_dta_found,"vehicle_id"=>""),JSON_PRETTY_PRINT);
      echo $json_encode; exit();
    }

  }


  public function mobile_add_car_overview()
  {
    $base_id = $this->input->post('base_id');
    $language_code = $this->input->post('lang_code');
    $vehicle_id = $this->input->post('vehicle_id');

    $request_to_book = "No";
    $instant_pay = "yes";
    $SeoUrl = $this->input->post('veh_title');
    $description = $this->input->post('description');

    if($base_id == '' || $vehicle_id == '' || $SeoUrl == '' || $description == '')
    {
      $json_encode = json_encode(array("status" => 0,"message" => $this->parm_missing),JSON_PRETTY_PRINT);
      echo $json_encode; exit();
    }

    $SeoUrl = $this->seo_friendly_url($SeoUrl);

    $data_to_update = array('veh_title' => $this->input->post('veh_title'),'veh_title_ph' => $this->input->post('veh_title_ph'), 'seourl' => $SeoUrl, 'description' => $this->input->post('description'),'description_ph' => $this->input->post('description_ph'), 'request_to_book' => $request_to_book, 'instant_book' => $instant_pay);

    $condition_to_insert = array('id' => $vehicle_id);
    $this->db->where($condition_to_insert);
    $this->db->update('fc_vehicle', $data_to_update);

    $vehicle_Detail = $this->mobile_model->get_all_details(VEHICLE, array('id' => $vehicle_id));

    if($vehicle_Detail->num_rows() == 1)
    {
      $vehicles_basic_dtls = $this->get_vehicle_basic_dtls($base_id,$language_code);
      $result = $this->get_vehicle_completed_steps($vehicle_id,$base_id,$language_code);

      $json_encode = json_encode(array("status" => 1,"message" => $this->add_succ,"vehicle_id"=>intval($vehicle_id),"base_id"=>$base_id,"result"=>$result['result_array'],"make" =>$vehicles_basic_dtls['make_details'],"model"=>$vehicles_basic_dtls['model_details'],"type"=>$vehicles_basic_dtls['type_details'],"attribute"=>$vehicles_basic_dtls['attribute'],"property_attributes" => $vehicles_basic_dtls['property_attributes'],"currency" => $vehicles_basic_dtls['currency']),JSON_PRETTY_PRINT);
      echo $json_encode; 
      exit();

    }
    else
    {
      $json_encode = json_encode(array("status" => 0,"message" => $this->no_dta_found,"vehicle_id"=>""),JSON_PRETTY_PRINT);
      echo $json_encode; exit();
    }

  }


  public function mobile_add_car_adddetails()
  {
    $base_id = $this->input->post('base_id');
    $language_code = $this->input->post('lang_code');
    $vehicle_id = $this->input->post('vehicle_id');

    if($base_id == '' || $language_code == '' || $vehicle_id == '')
    {
      $json_encode = json_encode(array("status" => 0,"message" => $this->parm_missing),JSON_PRETTY_PRINT);
      echo $json_encode; exit();
    }

    $data = array('important_info' => $this->input->post('important_info'), 'important_info_ph' => $this->input->post('important_info_ph'), 'terms_condition' => $this->input->post('terms_condition'), 'terms_condition_ph' => $this->input->post('terms_condition_ph'), 'car_rules' => $this->input->post('car_rules'), 'car_rules_ph' => $this->input->post('car_rules_ph'),'other_things' => $this->input->post('other_things'), 'other_things_ph' => $this->input->post('other_things_ph'));

    $this->mobile_model->update_details(VEHICLE,$data,array('id'=>$vehicle_id));

    $vehicle_Detail = $this->mobile_model->get_all_details(VEHICLE, array('id' => $vehicle_id));

    if($vehicle_Detail->num_rows() == 1)
    {
      $vehicles_basic_dtls = $this->get_vehicle_basic_dtls($base_id,$language_code);
      $result = $this->get_vehicle_completed_steps($vehicle_id,$base_id,$language_code);

      $json_encode = json_encode(array("status" => 1,"message" => $this->add_succ,"vehicle_id"=>intval($vehicle_id),"base_id"=>$base_id,"result"=>$result['result_array'],"make" =>$vehicles_basic_dtls['make_details'],"model"=>$vehicles_basic_dtls['model_details'],"type"=>$vehicles_basic_dtls['type_details'],"attribute"=>$vehicles_basic_dtls['attribute'],"property_attributes" => $vehicles_basic_dtls['property_attributes'],"currency" => $vehicles_basic_dtls['currency']),JSON_PRETTY_PRINT);
      echo $json_encode; 
      exit();

    }
    else
    {
      $json_encode = json_encode(array("status" => 0,"message" => $this->no_dta_found,"vehicle_id"=>""),JSON_PRETTY_PRINT);
      echo $json_encode; exit();
    }       

  }

  public function mobile_add_car_photo()
  {
    $base_id = $this->input->post('base_id');
    $language_code = $this->input->post('lang_code');
    $vehicle_id =$this->input->post('vehicle_id');
    $device_type =$this->input->post('device_type');

    if($base_id == '' || $vehicle_id == '' || $device_type == '')
    {
      $json_encode = json_encode(array("status" => 0,"message" => $this->parm_missing),JSON_PRETTY_PRINT);
      echo $json_encode; exit();
    }

    /*if($_POST['photos'] != '') 
    {*/
    if($_POST['device_type'] == 'IOS')
    {
      $Image_name = $_FILES['photos']['name'];
      if ($Image_name!='')
      {
        $config['overwrite'] = FALSE;
        $config['allowed_types'] = 'jpg|jpeg|gif|png';
        $config['upload_path'] = 'images/vehicles/'; /*print_r($config['upload_path']); exit();*/
        $this->upload->initialize($config);
        if ($this->upload->do_upload('photos')){
            $imgDetails = $this->upload->data();

            $data = array('mobile_image' => $imgDetails['file_name'],'vehicle_type'=>$base_id,'image'=>$imgDetails['file_name'],'vehicle_id'=>$vehicle_id);
            $this->mobile_model->simple_insert(VEHICLE_PHOTOS,$data);
        }
        else
        {
          $json_encode = json_encode(array("status"=>0,"message" => $this->fild,"error"=>$this->upload->display_errors()),JSON_PRETTY_PRINT);
          echo $json_encode;
          exit();
        }
      }
      else
      {
        $json_encode = json_encode(array("status"=>0,"message" => $this->fild,"error"=>$this->nt_vlid_img),JSON_PRETTY_PRINT);
        echo $json_encode;
        exit();
      }
    }
    else
    { 
      /*$arr_image = explode(',',$_POST['photos']); 
      if (!empty($arr_image)) {
        $i=0;
        foreach($arr_image as $img){
          $image_name= time().$i.".jpg";
          $ifp = fopen("images/vehicles/".$image_name, "wb" ); 
          fwrite( $ifp, base64_decode( $img) ); 
          fclose( $ifp );         

       $data = array('mobile_image' => $image_name,'vehicle_type'=>$base_id,'image'=>$image_name,'vehicle_id'=>$vehicle_id);

      $this->mobile_model->simple_insert(VEHICLE_PHOTOS,$data);
          $i++;
        }
      }*/
      $decoded = base64_decode($_POST['photos']); 
      $img_handler = imagecreatefromstring($decoded);       
      if ($img_handler !== false) 
      {
        $fileName = date('Ymdhis').".png"; 
        $location_path = 'images/vehicles/'.$fileName; 
        /*header('Content-Type: image/png');*/              
        if(imagepng($img_handler, $location_path, 0, NULL))
        { 
          $data = array('mobile_image' => $fileName,'vehicle_type'=>$base_id,'image'=>$fileName,'vehicle_id'=>$vehicle_id);
          $this->mobile_model->simple_insert(VEHICLE_PHOTOS,$data);
          imagedestroy($img_handler);
        }          
      }
    } 
    /*}*/

    /*$this->mobile_model->update_details(VEHICLE,$data,array('id'=>$vehicle_id));*/

    $vehicle_Detail = $this->mobile_model->get_all_details(VEHICLE, array('id' => $vehicle_id));

    if($vehicle_Detail->num_rows() == 1)
    {
      $vehicles_basic_dtls = $this->get_vehicle_basic_dtls($base_id,$language_code);
      $result = $this->get_vehicle_completed_steps($vehicle_id,$base_id,$language_code);

      $json_encode = json_encode(array("status" => 1,"message" => $this->add_succ,"vehicle_id"=>intval($vehicle_id),"base_id"=>$base_id,"result"=>$result['result_array'],"make" =>$vehicles_basic_dtls['make_details'],"model"=>$vehicles_basic_dtls['model_details'],"type"=>$vehicles_basic_dtls['type_details'],"attribute"=>$vehicles_basic_dtls['attribute'],"property_attributes" => $vehicles_basic_dtls['property_attributes'],"currency" => $vehicles_basic_dtls['currency']),JSON_PRETTY_PRINT);
      echo $json_encode; 
      exit();

    }
    else
    {
      $json_encode = json_encode(array("status" => 0,"message" => $this->no_dta_found,"vehicle_id"=>""),JSON_PRETTY_PRINT);
      echo $json_encode; exit();
    }   

  }    

  public function vehicle_image_delete()
  {
    $phtid   = $this->input->post('image_id');
    $vehicle_id   = $this->input->post('vehicle_id');
    $rest_type = $this->input->post('base_id');
    $userId  = $this->input->post ('user_id');
    $language_code = $this->input->post('lang_code');
    $currency_code = $this->input->post('currency_code');

    if($phtid == "" || $vehicle_id == "" || $rest_type == "" || $language_code == "" || $currency_code == "")
    {
      $json_encode = json_encode(array("status" => 0,"message" => $this->parm_missing),JSON_PRETTY_PRINT);
      echo $json_encode; exit();
    }

    $condition =array('id'=>$phtid);
    $photo_details = $this->db->select('id,image')->from(VEHICLE_PHOTOS)->where('id',$phtid)->get();

    foreach($photo_details->result() as $image_name)
    {        
      $gambar= $image_name->image;
      unlink("images/vehicles/".$gambar);
      unlink("images/vehicles/mobile/".$gambar);   
    }
    $this->mobile_model->commonDelete(VEHICLE_PHOTOS,array('id' => $phtid));

    $vehicle_Detail = $this->mobile_model->get_all_details(VEHICLE, array('id' => $vehicle_id));

    if($vehicle_Detail->num_rows() == 1)
    {
      $vehicles_basic_dtls = $this->get_vehicle_basic_dtls($base_id,$language_code);
      $result = $this->get_vehicle_completed_steps($vehicle_id,$base_id,$language_code);

      $json_encode = json_encode(array("status" => 1,"message" => $this->dlted_succ,"vehicle_id"=>intval($vehicle_id),"base_id"=>$base_id,"result"=>$result['result_array'],"make" =>$vehicles_basic_dtls['make_details'],"model"=>$vehicles_basic_dtls['model_details'],"type"=>$vehicles_basic_dtls['type_details'],"attribute"=>$vehicles_basic_dtls['attribute'],"property_attributes" => $vehicles_basic_dtls['property_attributes'],"currency" => $vehicles_basic_dtls['currency']),JSON_PRETTY_PRINT);
      echo $json_encode; 
      exit();

    }
    else
    {
      $json_encode = json_encode(array("status" => 0,"message" => $this->no_dta_found,"vehicle_id"=>""),JSON_PRETTY_PRINT);
      echo $json_encode; exit();
    }   

  }


  public function mobile_add_car_amenties()
  {
    $base_id = $this->input->post('base_id');
    $language_code = $this->input->post('lang_code');
    $vehicle_id =$this->input->post('vehicle_id');
    $list_name =$this->input->post('list_name');

    if($base_id == '' || $vehicle_id == '' || $list_name == '')
    {
      $json_encode = json_encode(array("status" => 0,"message" => $this->parm_missing),JSON_PRETTY_PRINT);
      echo $json_encode; exit();
    }
    
    $data = array('list_name' => $list_name);

    $this->mobile_model->update_details(VEHICLE,$data,array('id'=>$vehicle_id));

    $vehicle_Detail = $this->mobile_model->get_all_details(VEHICLE, array('id' => $vehicle_id));

    if($vehicle_Detail->num_rows() == 1)
    {
      $vehicles_basic_dtls = $this->get_vehicle_basic_dtls($base_id,$language_code);
      $result = $this->get_vehicle_completed_steps($vehicle_id,$base_id,$language_code);

      $json_encode = json_encode(array("status" => 1,"message" => $this->add_succ,"vehicle_id"=>intval($vehicle_id),"base_id"=>$base_id,"result"=>$result['result_array'],"make" =>$vehicles_basic_dtls['make_details'],"model"=>$vehicles_basic_dtls['model_details'],"type"=>$vehicles_basic_dtls['type_details'],"attribute"=>$vehicles_basic_dtls['attribute'],"property_attributes" => $vehicles_basic_dtls['property_attributes'],"currency" => $vehicles_basic_dtls['currency']),JSON_PRETTY_PRINT);
      echo $json_encode; 
      exit();

    }
    else
    {
      $json_encode = json_encode(array("status" => 0,"message" => $this->no_dta_found,"vehicle_id"=>""),JSON_PRETTY_PRINT);
      echo $json_encode; exit();
    }    
   
  }


  public function mobile_add_car_addprice()
  { 
    $vehicle_type = $this->input->post('base_id');
    $language_code = $this->input->post('lang_code');
    $vehicle_id =$this->input->post('vehicle_id');
    $title = $this->input->post('title');
    $title_ph = $this->input->post('title_ph');
    $max_limit = $this->input->post('max_limit');
    $price = $this->input->post('price');
    $price_type = $this->input->post('price_type');
    $short_description = str_replace("'", "`", $this->input->post('short_description'));
    $short_description_ph = str_replace("'", "`", $this->input->post('short_description_ph'));

    if($vehicle_type == '' || $language_code == '' || $vehicle_id == '' || $title == '' || $max_limit == '' || $price == '' || $price_type == '' || $short_description == '')
    {
      $json_encode = json_encode(array("status" => 0,"message" => $this->parm_missing),JSON_PRETTY_PRINT);
      echo $json_encode; exit();
    }

    $checkDatesExist = $this->mobile_model->get_all_details(VEHICLE_ADDITIONAL_PRICE, array('vehicle_type' => $vehicle_type,'vehicle_id' => $vehicle_id,'title' => $title,'max_limit' => $max_limit,'price' => $price));

    if ($checkDatesExist->num_rows() > 0) {
        $json_encode = json_encode(array("status" => 0,"message" => $this->alrdy_exist),JSON_PRETTY_PRINT);
        echo $json_encode; exit();
    } else {
        $dataArr = array('vehicle_type' => $vehicle_type, 'vehicle_id' => $vehicle_id, 'title' => $title,'title_ph'=>$title_ph, 'max_limit' => $max_limit, 'price' => $price, 'price_type' => $price_type, 'short_description' => $short_description, 'short_description_ph' => $short_description_ph, 'createdAt' => date('Y-m-d H:i:s'), 'status' => 'Active');
        $this->db->insert(VEHICLE_ADDITIONAL_PRICE, $dataArr);
       
        $vehicle_Detail = $this->mobile_model->get_all_details(VEHICLE, array('id' => $vehicle_id));

        if($vehicle_Detail->num_rows() == 1)
        {
          $vehicles_basic_dtls = $this->get_vehicle_basic_dtls($vehicle_type,$language_code);
          $result = $this->get_vehicle_completed_steps($vehicle_id,$vehicle_type,$language_code);

          $json_encode = json_encode(array("status" => 1,"message" => $this->add_succ,"vehicle_id"=>intval($vehicle_id),"base_id"=>$vehicle_type,"result"=>$result['result_array'],"make" =>$vehicles_basic_dtls['make_details'],"model"=>$vehicles_basic_dtls['model_details'],"type"=>$vehicles_basic_dtls['type_details'],"attribute"=>$vehicles_basic_dtls['attribute'],"property_attributes" => $vehicles_basic_dtls['property_attributes'],"currency" => $vehicles_basic_dtls['currency']),JSON_PRETTY_PRINT);
          echo $json_encode; 
          exit();
        }
        else
        {
          $json_encode = json_encode(array("status" => 0,"message" => $this->no_dta_found,"vehicle_id"=>""),JSON_PRETTY_PRINT);
          echo $json_encode; exit();
        }    
    }
  }

  public function show_car_addprice()
  {
    $vehicle_id = $this->input->post('vehicle_id');
    $currency_code = $this->input->post('currency_code');
    $language_code = $this->input->post('lang_code');

    if($vehicle_id == "" || $currency_code == "" || $language_code == "")
    {
      $json_encode = json_encode(array("status"=>0,"message"=>$this->parm_missing));
      echo $json_encode;
      exit();
    }

    $additional_dtls = array();
    $additional_price_list = $this->mobile_model->get_all_details(VEHICLE_ADDITIONAL_PRICE, array('vehicle_id' => $vehicle_id));

    if ($additional_price_list->num_rows() > 0) 
    {
      foreach ($additional_price_list->result() as $row) 
      {
        /*if($language_code == 'en')
        {
          $rentalNameField=$row->title;
          $descField=$row->short_description;
        }
        else
        {
          $titleNameField='title_ph';
          $descNameField='short_description_ph';

          if($row->$titleNameField == '') 
          { 
            $rentalNameField=$row->title;
          }
          else
          {
            $rentalNameField=$row->$titleNameField;
          }

          if($row->$descNameField == '') 
          { 
            $descField=$row->short_description;
          }
          else
          {
            $descField=$row->$descNameField;
          }
        }*/
        if($row->title_ph != ""){ $rentalNameField=$row->title_ph; }else{ $rentalNameField=""; }
        if($row->short_description_ph != ""){ $descField=$row->short_description_ph; }else{ $descField=""; }
        $additional_dtls[] =array("addtnl_id"=>$row->id,"title"=>$row->title,"title_ph"=>$rentalNameField,"max_limit"=>$row->max_limit,"price"=>floatval($row->price),"price_type"=>$row->price_type,"description"=>$row->short_description,"description_ph"=>$descField); 
      }

      $json_encode = json_encode(array("status"=>1,"message"=>$this->succ,"additional_dtls"=>$additional_dtls),JSON_PRETTY_PRINT);
      echo $json_encode;
      exit();
    }
    else
    {
      $json_encode = json_encode(array("status"=>0,"message"=>$this->no_dta_found),JSON_PRETTY_PRINT);
      echo $json_encode;
      exit();
    }


  }
  public function updateAdditionalPrice()
  {
    $id = $this->input->post('addPrice_id');
    $title = $this->input->post('title');
    $title_ph = $this->input->post('title_ph');
    $max_limit = $this->input->post('max_limit');
    $price = $this->input->post('price');
    $price_type = $this->input->post('price_type');
    $short_description = str_replace("'", "`", $this->input->post('short_description'));
    $short_description_ph = str_replace("'", "`", $this->input->post('short_description_ph'));

    if($id == "" || $title == "" || $max_limit == "" || $price == "" || $price_type == "" || $short_description == "")
    {
      $json_encode = json_encode(array("status"=>0,"message"=>$this->parm_missing));
      echo $json_encode;
      exit();
    }

    $dataArr = array('title' => $title,'title_ph'=>$title_ph, 'max_limit' => $max_limit, 'price' => $price, 'price_type' => $price_type, 'short_description' => $short_description, 'short_description_ph' => $short_description_ph, 'modifiedAt' => date('Y-m-d H:i:s'), 'status' => 'Active');
    $this->mobile_model->update_details(VEHICLE_ADDITIONAL_PRICE, $dataArr, array('id'=>$id));
    $json_encode = json_encode(array("status"=>1,"message"=>$this->updt_succ));
    echo $json_encode;
    exit();
  }



  public function delete_additional_price()
  { 
    $id = $this->input->post('addPrice_id');
    if($id == "")
    {
      $json_encode = json_encode(array("status"=>0,"message"=>$this->parm_missing));
      echo $json_encode;
      exit(); 
    }

    $this->db->where('id', $id);
    $this->db->delete(VEHICLE_ADDITIONAL_PRICE);
    $json_encode = json_encode(array("status"=>1,"message"=>$this->dlt_succ));
    echo $json_encode;
    exit();      
  }

  public function mobile_add_car_listings()
  {
    $base_id = $this->input->post('base_id');
    $user_id = $this->input->post('user_id');
    $language_code = $this->input->post('lang_code');
    $vehicle_id = $this->input->post('vehicle_id');
    $list_name = $this->input->post('attribute');
   
    if ($user_id=='' || $vehicle_id == '' || $base_id == '' || $list_name == '')
    {
      $json_encode = json_encode(array('status'=>0,'message'=>$this->parm_missing));
      echo $json_encode;
    }

    $attr_values = $this->input->post('attribute');
    $attribute = json_decode($this->input->post('attribute'),true);

    if($attr_values !="") 
    {
      foreach($attribute as $attributeTableName => $attributeTablevalue )
      {
        $select_qrys = "select * from fc_listing_types WHERE status='Active' and id=".$attributeTableName;
        $listing_values = $this->mobile_model->ExecuteQuery($select_qrys);

        foreach($listing_values->result() as $listname) 
        {
          if($listname->name == "accommodates") {
          $this->mobile_model->update_details(VEHICLE,array('accommodates'=>$attributeTablevalue),array('id' => $vehicle_id));
          }
        }
      }
    }

    $FinalsValues= array('listings'=>$attr_values);

    $this->mobile_model->update_details(VEHICLE, $FinalsValues, array('id' => $vehicle_id));

    $vehicle_Detail = $this->mobile_model->get_all_details(VEHICLE, array('id' => $vehicle_id));

    if($vehicle_Detail->num_rows() == 1)
    {
      $vehicles_basic_dtls = $this->get_vehicle_basic_dtls($base_id,$language_code);
      $result = $this->get_vehicle_completed_steps($vehicle_id,$base_id,$language_code);

      $json_encode = json_encode(array("status" => 1,"message" => $this->add_succ,"vehicle_id"=>intval($vehicle_id),"base_id"=>$base_id,"result"=>$result['result_array'],"make" =>$vehicles_basic_dtls['make_details'],"model"=>$vehicles_basic_dtls['model_details'],"type"=>$vehicles_basic_dtls['type_details'],"attribute"=>$vehicles_basic_dtls['attribute'],"property_attributes" => $vehicles_basic_dtls['property_attributes'],"currency" => $vehicles_basic_dtls['currency']),JSON_PRETTY_PRINT);
      echo $json_encode; 
      exit();

    }
    else
    {
      $json_encode = json_encode(array("status" => 0,"message" => $this->no_dta_found,"vehicle_id"=>""),JSON_PRETTY_PRINT);
      echo $json_encode; exit();
    }            
  }


  public function mobile_add_car_driver()
  {
    $base_id = $this->input->post('base_id');
    $host_id = $this->input->post('user_id');
    $language_code = $this->input->post('lang_code');
    $vehicle_id = $this->input->post('vehicle_id');
    $driver_type = $this->input->post('driver_type');
    $reg_num = $this->input->post('reg_number');
    $milg = $this->input->post('current_mileage');
    $drive_methods = explode(',', $driver_type);

    if($base_id == '' || $language_code == '' || $vehicle_id == '' || $driver_type == '' || $reg_num == '' || $milg == '')
    {
      $json_encode = json_encode(array('status'=>0,'message'=>$this->parm_missing));
      echo $json_encode;
      exit();
    }

    if(in_array('with_drive',$drive_methods)) 
    {
        $driver_id = $this->input->post('driver_id');
        if($driver_id=='0')
        {
            $driverNameExistorNot = $this->mobile_model->get_all_details(DRIVER_MASTER, array('host_id'=>$host_id,'driver_name'=>$this->input->post('driver_name')));
            if($driverNameExistorNot->num_rows() > 0 )
            {
                $json_encode = json_encode(array('status'=>0,'message'=>$this->drvr_exist));
                echo $json_encode;
                exit();
            }
            else
            {
                $dataArr = array(   'host_id'=>$host_id,
                                    'driver_name'=>$this->input->post('driver_name'),
                                    'age' => $this->input->post('age'),
                                    'contact_num' => $this->input->post('contact_num'),
                                    'email' => $this->input->post('email'),
                                    'insurance_num' => $this->input->post('insurance_num'),
                                    'license_num' => $this->input->post('license_num'),
                                    'license_expiry_month' => $this->input->post('license_expiry_month'),
                                    'license_expiry_year' => $this->input->post('license_expiry_year')
                                );
                $this->mobile_model->simple_insert(DRIVER_MASTER, $dataArr);
                $driver_id = $this->db->insert_id();
                
                $dataArr = array('mileage' => $this->input->post('current_mileage'),
                        'registration_number' => $this->input->post('reg_number'),  
                        'driver_type' => $this->input->post('driver_type'),    
                        'driver_id' => $driver_id
                        );

                $this->mobile_model->update_details(VEHICLE, $dataArr, array('id'=>$vehicle_id));
                
                $vehicle_Detail = $this->mobile_model->get_all_details(VEHICLE, array('id' => $vehicle_id));

                if($vehicle_Detail->num_rows() == 1)
                {
                  $vehicles_basic_dtls = $this->get_vehicle_basic_dtls($base_id,$language_code);
                  $result = $this->get_vehicle_completed_steps($vehicle_id,$base_id,$language_code);

                  $json_encode = json_encode(array("status" => 1,"message" => $this->add_succ,"vehicle_id"=>$vehicle_id,"base_id"=>$base_id,"result"=>$result['result_array'],"make" =>$vehicles_basic_dtls['make_details'],"model"=>$vehicles_basic_dtls['model_details'],"type"=>$vehicles_basic_dtls['type_details'],"attribute"=>$vehicles_basic_dtls['attribute'],"property_attributes" => $vehicles_basic_dtls['property_attributes'],"currency" => $vehicles_basic_dtls['currency']),JSON_PRETTY_PRINT);
                  echo $json_encode; 
                  exit();

                }
                else
                {
                  $json_encode = json_encode(array("status" => 0,"message" => $this->no_dta_found,"vehicle_id"=>""),JSON_PRETTY_PRINT);
                  echo $json_encode; exit();
                }     
            }
        }
        else
        {
            $dataArr = array('mileage' => $this->input->post('current_mileage'),
                            'registration_number' => $this->input->post('reg_number'),  
                            'driver_type' => $this->input->post('driver_type'),    
                            'driver_id' => $driver_id
                            );
            $this->mobile_model->update_details(VEHICLE, $dataArr, array('id'=>$vehicle_id));                    
            
            $vehicle_Detail = $this->mobile_model->get_all_details(VEHICLE, array('id' => $vehicle_id));

            if($vehicle_Detail->num_rows() == 1)
            {
              $vehicles_basic_dtls = $this->get_vehicle_basic_dtls($base_id,$language_code);
              $result = $this->get_vehicle_completed_steps($vehicle_id,$base_id,$language_code);

              $json_encode = json_encode(array("status" => 1,"message" => $this->add_succ,"vehicle_id"=>intval($vehicle_id),"base_id"=>$base_id,"result"=>$result['result_array'],"make" =>$vehicles_basic_dtls['make_details'],"model"=>$vehicles_basic_dtls['model_details'],"type"=>$vehicles_basic_dtls['type_details'],"attribute"=>$vehicles_basic_dtls['attribute'],"property_attributes" => $vehicles_basic_dtls['property_attributes'],"currency" => $vehicles_basic_dtls['currency']),JSON_PRETTY_PRINT);
              echo $json_encode; 
              exit();

            }
            else
            {
              $json_encode = json_encode(array("status" => 0,"message" => $this->no_dta_found,"vehicle_id"=>""),JSON_PRETTY_PRINT);
              echo $json_encode; exit();
            }
        }
    }
    else
    {
        $dataArr = array('mileage' => $this->input->post('current_mileage'),
                        'registration_number' => $this->input->post('reg_number'),  
                        'driver_type' => $this->input->post('driver_type'),    
                        'driver_id' => 0
                        );

        $this->mobile_model->update_details(VEHICLE, $dataArr, array('id'=>$vehicle_id));
        
        $vehicle_Detail = $this->mobile_model->get_all_details(VEHICLE, array('id' => $vehicle_id));

        if($vehicle_Detail->num_rows() == 1)
        {
          $vehicles_basic_dtls = $this->get_vehicle_basic_dtls($base_id,$language_code);
          $result = $this->get_vehicle_completed_steps($vehicle_id,$base_id,$language_code);

          $json_encode = json_encode(array("status" => 1,"message" => $this->add_succ,"vehicle_id"=>intval($vehicle_id),"base_id"=>$base_id,"result"=>$result['result_array'],"make" =>$vehicles_basic_dtls['make_details'],"model"=>$vehicles_basic_dtls['model_details'],"type"=>$vehicles_basic_dtls['type_details'],"attribute"=>$vehicles_basic_dtls['attribute'],"property_attributes" => $vehicles_basic_dtls['property_attributes'],"currency" => $vehicles_basic_dtls['currency']),JSON_PRETTY_PRINT);
          echo $json_encode; 
          exit();

        }
        else
        {
          $json_encode = json_encode(array("status" => 0,"message" => $this->no_dta_found,"vehicle_id"=>""),JSON_PRETTY_PRINT);
          echo $json_encode; exit();
        }
    }
  }

  public function mobile_add_car_cancel()
  {
   /* $json_encode = json_encode(array('status'=>0,'message'=>$_POST));
    echo $json_encode;
    exit();
*/    $base_id = $this->input->post('base_id');
    $user_id = $this->input->post('user_id');
    $language_code =$this->input->post('lang_code');
    $vehicle_id =$this->input->post('vehicle_id');
    $cancel_policy = $this->input->post('cancel_policy'); 
    $can_description = $this->input->post('can_description'); 
    $can_description_ph = $this->input->post('can_description_ph'); 
    $security_deposit = $this->input->post('security_deposit');
    $meta_title = $this->input->post('meta_title');
    $meta_title_ph = $this->input->post('meta_title_ph');
    $meta_keword = $this->input->post('meta_keyword');
    $meta_keyword_ph = $this->input->post('meta_keyword_ph');
    $meta_description = $this->input->post('meta_description');
    $meta_description_ph = $this->input->post('meta_description_ph');

    if($base_id == '' || $language_code == '' || $vehicle_id == '' || $cancel_policy == '')
    {
      $json_encode = json_encode(array('status'=>0,'message'=>$this->parm_missing));
      echo $json_encode;
      exit();
    }

    if ($cancel_policy == 'Strict') 
    {
      $cancel_percentage = 100; 
    } 
    elseif ($cancel_policy == 'Moderate') 
    {
      $cancel_percentage = 50; 
    }
    else 
    {
      $cancel_percentage = $this->input->post('cancel_percentage');
    }

    $condition = array('id' => $vehicle_id);

    $data = array(
        'cancellation_policy' => $cancel_policy,
        'cancel_description' => $can_description,
        'cancel_description_ph' => $can_description_ph,
        'cancellation_percentage' => $cancel_percentage,
        'security_deposit' => $security_deposit,
        'meta_title' => $meta_title,
        'meta_title_ph' => $meta_title_ph,
        'meta_keyword' => $meta_keword,
        'meta_keyword_ph' => $meta_keyword_ph,
        'meta_description' => $meta_description,
        'meta_description_ph' => $meta_description_ph
    );

    $this->mobile_model->update_details(VEHICLE, $data, $condition); 

    $vehicle_Detail = $this->mobile_model->get_all_details(VEHICLE, array('id' => $vehicle_id));

    if($vehicle_Detail->num_rows() == 1)
    {
      $vehicles_basic_dtls = $this->get_vehicle_basic_dtls($base_id,$language_code);
      $result = $this->get_vehicle_completed_steps($vehicle_id,$base_id,$language_code);

      $json_encode = json_encode(array("status" => 1,"message" => $this->add_succ,"vehicle_id"=>intval($vehicle_id),"base_id"=>$base_id,"result"=>$result['result_array'],"make" =>$vehicles_basic_dtls['make_details'],"model"=>$vehicles_basic_dtls['model_details'],"type"=>$vehicles_basic_dtls['type_details'],"attribute"=>$vehicles_basic_dtls['attribute'],"property_attributes" => $vehicles_basic_dtls['property_attributes'],"currency" => $vehicles_basic_dtls['currency']),JSON_PRETTY_PRINT);
      echo $json_encode; 
      exit();

    }
    else
    {
      $json_encode = json_encode(array("status" => 0,"message" => $this->no_dta_found,"vehicle_id"=>""),JSON_PRETTY_PRINT);
      echo $json_encode; exit();
    } 
  }


  public function vehicle_edit() 
  {
    $vehicle_type = $this->input->post('base_id');
    $vehicle_id = intval($this->input->post('vehicle_id'));
    $language_code = $this->input->post('lang_code');

    if($vehicle_type == '' || $vehicle_id == '' || $language_code == '')
    {
      $json_encode = json_encode(array("status" => 0,"message" => $this->parm_missing),JSON_PRETTY_PRINT);
      echo $json_encode; exit();
    }

    $vehicle_Detail = $this->mobile_model->get_all_details(VEHICLE, array('id' => $vehicle_id));

    if($vehicle_Detail->num_rows() == 1)
    {
      $vehicles_basic_dtls = $this->get_vehicle_basic_dtls($vehicle_type,$language_code);
      $result = $this->get_vehicle_completed_steps($vehicle_id,$vehicle_type,$language_code);

      $json_encode = json_encode(array("status" => 1,"message" => $this->add_succ,"vehicle_id"=>intval($vehicle_id),"base_id"=>$vehicle_type,"result"=>$result['result_array'],"make" =>$vehicles_basic_dtls['make_details'],"model"=>$vehicles_basic_dtls['model_details'],"type"=>$vehicles_basic_dtls['type_details'],"attribute"=>$vehicles_basic_dtls['attribute'],"property_attributes" => $vehicles_basic_dtls['property_attributes'],"currency" => $vehicles_basic_dtls['currency']),JSON_PRETTY_PRINT);
      echo $json_encode; 
      exit();

    }
    else
    {
      $json_encode = json_encode(array("status" => 0,"message" => $this->no_dta_found,"vehicle_id"=>""),JSON_PRETTY_PRINT);
      echo $json_encode; exit();
    } 

  }


//vehicle_detail_page

public function vehicle_rental_detail() {


    $base_id = $_POST['base_id'];

    $email = $_POST['email'];

    $vehicle_id = $_POST['vehicle_id'];

    $user_id = $_POST['user_id'];

    $language_code = $_POST['lang_code'];

    $userDetails = $this->mobile_model->get_all_details(USERS, array('email'=>$email));

    $userId = $userDetails->row()->id; 

    $sometime_arr = array();

    $fav = 0;

    if($user_id !="" || $user_id !=0) 

    {

      $select_qrys = "select fc_lists.id from fc_lists where  find_in_set(".$vehicle_id.",vehicle_id) and user_id = ".$user_id;

      $checkFavorite = $this->mobile_model->ExecuteQuery($select_qrys);

      if($checkFavorite->num_rows() > 0) $fav = 1;

      else $fav = 0;

    }



      $where1 = array('p.id'=>$vehicle_id); 

      $where2 = array('vehicle_id'=>$vehicle_id); 



      

      $this->db->select('p.user_id,p.mileage,p.registration_number,p.driver_type,p.driver_id,p.vehicle_type,p.make_id,p.id,p.accommodates,p.model_id,p.type_id,p.booking_type,p.min_hour,p.min_days,p.min_hour_price,p.year,p.min_hour_exprice,p.day_price,p.weekly_price,p.monthly_price,p.currency,p.status,p.calendar_checked,pa.address,p.description,p.description_ph,p.veh_title,p.veh_title_ph,p.request_to_book,p.instant_book,p.important_info,p.important_info_ph,p.terms_condition,p.terms_condition_ph,p.car_rules,p.car_rules_ph,p.other_things,p.other_things_ph,p.car_rules,p.car_rules_ph,p.list_name,p.listings,pa.address,pa.country,pa.state,pa.city,pa.street,pa.zip,pa.lat,pa.lang,p.cancellation_policy,p.cancellation_percentage,p.cancel_description,p.cancel_description_ph,p.security_deposit,
        p.meta_title,p.meta_keyword,p.meta_description,p.meta_title_ph,p.meta_keyword_ph,p.meta_description_ph,pa.lat as latitude,pa.lang as longitude, u.description as description1, u.phone_no, rq.id, rq.checkin_date, rq.checkout_date, u.user_name, u.group, u.s_phone_no, u.about, u.loginUserType, u.email as RenterEmail, u.image as thumbnail,pa.country, pa.state, pa.city, pa.zip, pa.address, u.is_verified, u.id_verified, u.ph_verified, u.created, u.facebook, u.google, u.address, u.about,p.security_deposit,p.currency,rq.booking_status');
      $this->db->from(VEHICLE.' as p');
      $this->db->join(VEHICLE_ADDRSS.' as pa',"pa.vehicle_id=p.id","LEFT");
      $this->db->join(USERS.' as u',"u.id=p.user_id","LEFT");
      $this->db->join(VEHICLE_ENQUIRY.' as rq',"rq.vehicle_id=p.id","LEFT");
      $this->db->where($where1);
      $this->db->group_by('rq.id');
      $rental_details = $this->db->get();
      //echo '<pre>';print_r($rental_details->result());die;

      $this->db->select('image');

      $this->db->from(VEHICLE_PHOTOS);

      $this->db->where($where2);

      $photoDetails = $this->db->get();



      //booked dates

      $BookedArr = array();

    $booked_Dates = $this->db->select('checkin_date,checkout_date')->where(array('vehicle_id' => $vehicle_id, 'booking_status' => 'Booked','cancelled' => 'No'))->get(VEHICLE_ENQUIRY);

    if ($booked_Dates->num_rows() > 0) {

      foreach ($booked_Dates->result() as $Dates) {

        $current = strtotime($Dates->checkin_date);

        $last = strtotime($Dates->checkout_date);

        while ($current <= $last) {

          $BookedArr[] = "'" . date('m/d/Y', $current) . "'";

          $current = strtotime('+1 day', $current);

        }

      }

    }



   

      if($rental_details->num_rows() > 0) {

        $prodimgArr = array();

        $proddetailArr = array();

        $check = array();       

        $list_info = array();

        $producttitle="";

        $productdesc="";

        $productprice="";

        $min_hour_price="";

        $day_price="";

        $weekly_price="";

        $monthly_price="";

        $userimg="";

        $loginUserType="";

        $hostname="";

        $accommodates="";

        $bedroom="";

        $beds="";

        $bathrooms="";

        $country="";

        $state="";

        $city="";

        $post_code="";

        $address="";

        $latitude="";

        $longitude="";

        $minimum_stay="";

        $cancellation="";

        $house_rules="";

        $terms="";

        $import_info="";

        $other_things_to_note="";

        $list_name="";

        $rental_id="";

        $host_id="";

        $host_email="";

        $user_currency="";

        $user_about="";

        $price_perweek="";

        $price_permonth="";

        $email_verified="";

        $ph_verified="";

        $id_verified="";

        $listarr = array();

        $datefrom = "";

        $dateto = "";

        $home_type = "";

        $member_since = "";

        $facebook = "";

        $google = "";

        $userAddress = "";

        $hostabout = "";

        $min_hours = "";

        $min_days = "";

        

        foreach($photoDetails->result() as $rental_detail){ 

          if($rental_detail->image != ''){

          $p_img = explode('.',$rental_detail->image);  



          $suffix = strrchr($rental_detail->image, "."); 

          $pos = strpos  ( $rental_detail->image, $suffix); 

          $name = substr_replace ($rental_detail->image, "", $pos); 

         // echo $suffix . "<br><br>". $name;



          $pro_img = $name.''.$suffix; 

          

          $proImage = base_url().'images/vehicles/'.$pro_img;

          }else{

            $proImage = base_url().'images/rental/dummyProductImage.jpg';

          }

          $prodimgArr[] = array("vehicle_image" =>$proImage);

        }

       

     //  print_r($rental_details->result());exit();

        foreach($rental_details->result() as $rental_detail){ 

          if($rental_detail->checkin_date != '' && $rental_detail->booking_status =="Booked"){

            $checkin = $rental_detail->checkin_date;

            $checkout = $rental_detail->checkout_date;

          }else{

            // echo "string";exit();

            $checkin = '';

            $checkout = '';

          }

          

          $producttitle = $rental_detail->veh_title;



          $productdesc = $rental_detail->description;



          $booking_type = $rental_detail->booking_type;



          $driver_id = $rental_detail->driver_id;

          $Driver_details = array();

          if($driver_id != 0){

        $driverDetails = $this->mobile_model->get_all_details(DRIVER_MASTER, array('id'=>$driver_id));



        if(count($driverDetails)>0) {

      foreach($driverDetails->result() as $typepro) {

        $drivervalueArr = array();

        $Driver_details[] = array("host_id"=>$typepro->id,"driver_name"=>$typepro->driver_name,"age"=>$typepro->age,"contact_num"=>$typepro->contact_num,"email"=>$typepro->email,"insurance_num"=>$typepro->insurance_num,"license_num"=>$typepro->license_num,"license_expiry_year"=>$typepro->license_expiry_year,"license_expiry_month"=>$typepro->license_expiry_month);
        
        }
      }

          }

          $book_type = explode(',', $rental_detail->booking_type);

          //print_r($rental_detail->booking_type);exit();

          $day_price = $rental_detail->day_price;

          $min_hour_price = $rental_detail->min_hour_price;

          $weekly_price = $rental_detail->weekly_price;

          $monthly_price = $rental_detail->monthly_price;





           if(in_array('1', $book_type) && !in_array('2', $book_type))

            { 

                $veh_price = $rental_detail->day_price; 

            }

            elseif (!in_array('1', $book_type) && in_array('2', $book_type))

            { 

              $veh_price = $rental_detail->min_hour_price;

            }

            // if (in_array('1', $book_type) && in_array('2', $book_type))

            else

            { 

                 $veh_price = $rental_detail->day_price;

            }

      $userCurrencySymbol = $this->db->select('currency_symbols')->where('currency_type',$_POST['currency_code'])->get(CURRENCY)->row()->currency_symbols;

      if($rental_detail->currency != $_POST['currency_code'])

      {



        $day_price = currency_conversion($rental_detail->currency,$_POST['currency_code'], $day_price);

        $weekly_price = currency_conversion($rental_detail->currency,$_POST['currency_code'], $weekly_price);

        $monthly_price = currency_conversion($rental_detail->currency,$_POST['currency_code'], $monthly_price);

        $min_hour_price = currency_conversion($rental_detail->currency,$_POST['currency_code'], $min_hour_price);

      }

      $this->data['productPriceDetails'] = $this->mobile_model->get_all_details(VEHICLE,array('id'=>$vehicle_id));

      

          if(($checkin != "" && $checkout != "") || ($checkin != null&& $checkout != null)) {

            $check[] = array("checkin" =>$checkin,'checkout'=>$checkout);

          }

          $hostname = $rental_detail->user_name;

          if($rental_detail->thumbnail != ''){

            $userimg = base_url().'images/users/'.$rental_detail->thumbnail;

          }else{

            $userimg = base_url().'images/users/profile.png';

          }

          if($rental_detail->loginUserType != ''){

            $loginUserType = $rental_detail->loginUserType;

          }else{

            $loginUserType = '';

          }



           if(is_int($rental_detail->make_id) || ($rental_detail->make_id!=0)) {

            $home_type_sql=$this->mobile_model->get_all_details(MAKE_MASTER,array("id"=>$rental_detail->make_id));

            // print_r($rental_detail->make_id);exit();

      $home_type_varfinal = trim($home_type_sql->row()->make_name);

    //  echo $home_type_varfinal;exit();

          } else {

            $home_type_varfinal = $rental_detail->make_id;

          }



          $home_type = $home_type_varfinal;



        if(is_int($rental_detail->model_id) || ($rental_detail->model_id!=0)) 

        {

          $room_type_sql=$this->mobile_model->get_all_details(MODEL_MASTER,array("id"=>$rental_detail->model_id));

          $room_type_varfinal =trim($room_type_sql->row()->model_name);

        }

        else

        {

          $room_type_varfinal = $rental_detail->model_id;

        }



         if(is_int($rental_detail->type_id) || ($rental_detail->type_id!=0)) 

        {

          $type_id_sql=$this->mobile_model->get_all_details(TYPE_MASTER,array("id"=>$rental_detail->type_id));



          $type_id_sql_varfinal =trim($type_id_sql->row()->type_name);

        }

        else

        {

          $type_id_sql_varfinal = $rental_detail->type_id;

        }

         

          $listing_json = $rental_detail->listings;

          $listing_decode = json_decode($listing_json);

     /*print_r($listing_json); die();*/

    

    if(count($listing_decode)>0)

    {

       $list_info = array();

       $list_info[] = array("label" => "Vehicle Make","value" => $home_type);

       $list_info[] = array("label" => "Vehicle Model","value" => $room_type_varfinal);

       $list_info[] = array("label" => "Vehicle Type","value" => $type_id_sql_varfinal);

       //print_r($list_info);exit();

          foreach($listing_decode as $lkey=>$lvalues)

          {



            

            $listinginformation = $this->mobile_model->get_all_details ( LISTING_TYPES, array('id'=>$lkey) );

         

            if(trim($lkey)==trim($listinginformation->row()->id)) 

            {        

              $listingchild = $this->mobile_model->get_all_details ( LISTING_CHILD, array('id'=>$lvalues) ); 

              if ($listinginformation->row()->type == 'option')

              {

                $list_info[] = array("label" => $listinginformation->row()->labelname,"value" => $listingchild->row()->child_name);



              }



              else

              {

                $list_info[] = array("label" => $listinginformation->row()->labelname,"value" => $lvalues);



              }

              

              if($listinginformation->row()->name=="Doors"){

              $doors = ($listingchild->num_rows()>0)?$listingchild->row()->child_name:'';

             //echo $doors;exit();

            }



              

              if($listinginformation->row()->name=="Fuel_Type") {

              $fuel_type = $listingchild->row()->child_name;

              //echo $fuel_type;exit();

              }

              if($listinginformation->row()->name=="Airbags") {

              $Airbags = $listingchild->row()->child_name;

              }

              if($listinginformation->row()->name=="Boot_space_Capacity") {

              $Boot_space_Capacity = $listingchild->row()->child_name;

              }

              if($listinginformation->row()->name=="accommodates") {

              $Guest_Capacity = $listingchild->row()->child_name;

              }

    if($listinginformation->row()->name =="Transmission_type") {

              $Transmission_type = $listingchild->row()->child_name;

             // echo "$Transmission_type";exit();

              }



            }               

          }   

        //  print_r($list_info);exit();        

      }



      else {

      $Doors = '';      

      $fuel_type = '';      

      $airbags = '';      

      $Boot_space_Capacity='';  

      $Guest_Capacity='';  

      $Transmission_type='';   



      } 

          $accommodates = $this->db->select('child_name')->from(LISTING_CHILD)->where('id',$rental_detail->accommodates)->get()->row()->child_name;

       

          if($rental_detail->country != ''){

          $country = $rental_detail->country;

          }else{

          $country = '';

          }

          if($rental_detail->state != ''){

          $state = $rental_detail->state;

          }else{

          $state = '';

          }

          if($rental_detail->city != ''){

          $city = $rental_detail->city;

          }else{

          $city = '';

          }

          

          if($rental_detail->zip != '') {

          $post_code = $rental_detail->zip;

          }else{

          $post_code ='';

          }

          

          if($rental_detail->address !='') {

          $address = $rental_detail->address;

          }else{

          $address ='';

          }

          

          if($rental_detail->latitude !='') {

          $latitude = $rental_detail->latitude;

          }else{

          $latitude = '';

          }

          

          if($rental_detail->longitude !='') {

          $longitude = $rental_detail->longitude;

          }

          else{

          $longitude ='';

          }         

          if($rental_detail->cancellation_policy !='') {

          $cancellation = $rental_detail->cancellation_policy;

          }else{

          $cancellation = '';

          }

          if($rental_detail->min_days !='') {

          $min_days = $rental_detail->min_days;

          }else{

          $min_days = '';

          }

          if($rental_detail->min_hour !='') {

          $min_hours = $rental_detail->min_hour;

          }else{

          $min_hours = '';

          }
          

          if($rental_detail->security_deposit != '') {

      if($rental_detail->currency != $_POST['currency_code'])

      {

        $security_deposit = currency_conversion($rental_detail->currency,$_POST['currency_code'], $rental_detail->security_deposit);

        //$security_deposit = floatval($rental_detail->security_deposit);

      }

      else{

        $security_deposit = floatval($rental_detail->security_deposit);

      }     

          }else{

      $security_deposit ='0';

          }

          

          $rental_id = $_POST['vehicle_id'];

          $host_id = $rental_detail->user_id;

          

          

          if($rental_detail->about != '') {

          $user_about = $rental_detail->about;

          }else{

          $user_about ='';

          }

          

          if($rental_detail->price_perweek != '') {

          $price_perweek = $rental_detail->price_perweek;

          }else{

          $price_perweek = '';

          }

          

          if($rental_detail->day_price != '') {

          $price_permonth = $rental_detail->day_price;

          }else{

          $price_permonth = '';

          }
                  

          $hostemail = $this->mobile_model->get_all_details ( USERS, array('id'=>$host_id) );

          $host_email = $hostemail->row()->email;

          if($language_code == 'en')
          {
               $field_title=$rental_detail->veh_title;
               $field_desc=$rental_detail->description;
               $import_info_val=strip_tags($rental_detail->important_info);
               $terms_val=strip_tags($rental_detail->terms_condition);
               $house_rules_val=strip_tags($rental_detail->car_rules);
               $other_things_to_note_val=strip_tags($rental_detail->other_things);
          }
          else
          {
              $titleNameField='veh_title_ph';
              if($rental_detail->$titleNameField=='') {
                  $field_title=strip_tags($rental_detail->veh_title);
              }
              else{
                  $field_title=strip_tags($rental_detail->$titleNameField);
              }

              $titlefield_desc='description_ph';
              if($rental_detail->$titlefield_desc=='') {
                  $field_desc=strip_tags($rental_detail->description);
              }
              else{
                  $field_desc=strip_tags($rental_detail->$titlefield_desc);
              }

              $titleimport_info='important_info_ph';
              if($rental_detail->$titleimport_info=='') {
                  $import_info_val=strip_tags($rental_detail->description);
              }
              else{
                  $import_info_val=strip_tags($rental_detail->$titleimport_info);
              }

              $titleterms='terms_condition_ph';
              if($rental_detail->$titleterms=='') {
                  $terms_val=strip_tags($rental_detail->terms_condition);
              }
              else{
                  $terms_val=strip_tags($rental_detail->$titleterms);
              }

              $titlerules='car_rules_ph';
              if($rental_detail->$titlerules=='') {
                  $house_rules_val=strip_tags($rental_detail->car_rules);
              }
              else{
                  $house_rules_val=strip_tags($rental_detail->$titlerules);
              }

              $titleother='other_things_ph';
              if($rental_detail->$titleother=='') {
                  $other_things_to_note_val=strip_tags($rental_detail->other_things);
              }
              else{
                  $other_things_to_note_val=strip_tags($rental_detail->$titleother);
              }
          }

          /*if($rental_detail->$house_rules == '')
          {
            $house_rules_val = '';
          }
          else
          {
            $house_rules_val = $rental_detail->$house_rules;
          }

          if($rental_detail->$terms == '')
          {
            $terms_val = '';
          }
          else
          {
            $terms_val = $this->mob_readmoreContent($rental_detail->$terms);
          }

          if($rental_detail->$import_info == '')
          {
            $import_info_val = '';
          }
          else
          {
            $import_info_val = $this->mob_readmoreContent($rental_detail->$import_info);
          }

          if($rental_detail->$other_things_to_note == '')
          {
            $other_things_to_note_val = '';
          }
          else
          {
            $other_things_to_note_val = $this->mob_readmoreContent($rental_detail->$other_things_to_note);
          }*/
      

          if($rental_detail->list_name == ''){

          $list_name = '';

          }

          else

          {

            $list_name = $rental_detail->list_name;

          } 

          $email_verified=$rental_detail->is_verified;

          $ph_verified=$rental_detail->ph_verified;

          $id_verified=$rental_detail->id_verified;

          $member_since=$rental_detail->created;

          $facebook=$rental_detail->facebook;

          $google=$rental_detail->google;

          $userAddress=$rental_detail->address;

        } 

        $list = $rental_details->row()->list_name;

        $list_value = explode(',',$list);

        for($i=0;$i<count($list_value);$i++) {

          if($list_value[$i] !="") {

          $list_detail = $this->mobile_model->get_all_details (LIST_VALUES, array('id'=>$list_value[$i]) );

          

          if($list_detail != '') { $list_name = $list_detail->row()->list_value; } else { $list_name=""; } 

          if($list_detail != '') { $list_img = $list_detail->row()->image; }  else { $list_img=""; }

          if($list_img != '') { $list_img = base_url().'images/attribute/'.$list_detail->row()->image;   } else {  $list_img=""; }

          $listarr[] = array('list_name'=>$list_name,'list_image'=>$list_img);

          }

        }

        $this->data['productPriceDetails']->row()->booking_type;

        if($this->data['productPriceDetails']->row()->vehicle_type == 4){

if($this->data['productPriceDetails']->row()->booking_type == 1){

  $service_tax_query='SELECT * FROM '.COMMISSION.' WHERE seo_tag="car-booking-daily" AND status="Active"';

}

else if($this->data['productPriceDetails']->row()->booking_type == 2){



  $service_tax_query='SELECT * FROM '.COMMISSION.' WHERE seo_tag="car-booking-hourly" AND status="Active"';

}

else {

  $service_tax_query='SELECT * FROM '.COMMISSION.' WHERE seo_tag="car-booking-daily" AND status="Active"';

}

}

        else{

if($this->data['productPriceDetails']->row()->booking_type == 1){

  $service_tax_query='SELECT * FROM '.COMMISSION.' WHERE seo_tag="van-booking-daily" AND status="Active"';

}

else if($this->data['productPriceDetails']->row()->booking_type == 2){



  $service_tax_query='SELECT * FROM '.COMMISSION.' WHERE seo_tag="van-booking-hourly" AND status="Active"';

}

else {

  $service_tax_query='SELECT * FROM '.COMMISSION.' WHERE seo_tag="van-booking-daily" AND status="Active"';

}

}



        



        $service_taxs = $this->mobile_model->ExecuteQuery($service_tax_query);

      // print_r($this->data['productPriceDetails']->row()->booking_type);exit();

        $services =array();

    if(($service_taxs->num_rows())>0) {

      $service_type = $service_taxs->row()->promotion_type;

      $service_fee = $service_taxs->row()->commission_percentage;

      $services = array("service_type"=>$service_type,"service_value"=>floatval($service_fee));

    } else {

      $service_type = "";

      $service_fee = "";

      $services = array("service_type"=>$service_type,"service_value"=>"");

    }

    /* schedule starts here */
      $query = $this->db->query("SELECT * FROM `fc_vehicle_bookings_dates` WHERE vehicle_id=".$rental_id." AND tot_checked_in <> ''");
      $rows_res = $query->result_array(); 
   
      $dateeee=array();

      if(!empty($rows_res)) 
      {
        $datee = '';
        $stime = '';
        $etime = '';
        $timee = '';
        $wholedt = '';
        $dayArr1 = array();
        $dayArr2 = array();

        foreach($rows_res  as $date)
        {
          $start=$date['tot_checked_in'];
          $startDate=date('Y-m-d',strtotime($start));
          $startTime=date('H:i',strtotime($start)); 
          $end=$date['tot_checked_out'];
          $endDate=date('Y-m-d',strtotime($end));
          $endTime=date('H:i',strtotime($end));
          $state = $date['id_state'];

          $datesbetween = $this->createRange($startDate, $endDate, $format = 'Y-m-d');
          
          foreach($datesbetween as $day)
          {
            if(count($datesbetween) == 1 ){ 
              $datee=$day;
              $timee= $startTime.'' . '-' . ''.$endTime;
            }
            else
            {
              if ($day==$startDate){
                $datee = $day;
                $timee= $startTime.'' .  '-' . ''.'23:00' ;
              }else if ($day==$endDate){
                $datee =$day;
                $timee='00:00'.'' .  '-' . ''.$endTime ;
              }
              else{
                $datee = $day;  
                $timee='00:00'.'' .  '-' . ''.'23:00';
              } 
            } 

          if(in_array($datee, array_column($sometime_arr, 'date')) && in_array($timee, array_column($sometime_arr, 'time'))) 
          {
            $key = array_search($datee, array_column($dateeee, 'date'));
            $value=$timee;
            $sometime_arr[$key]=array('date'=>$datee,'time'=>$value/*,"state"=>$state*/);
          }
          else
          {
            $value=$timee;
            $sometime_arr[]=array('date'=>$datee,'time'=>$value/*,"state"=>$state*/);
          }
        } 
      }
    }
      /* schedule ends here */       

      

        $condition = array('currency_type'=>$rental_detail->currency);

        $currency_details = $this->mobile_model->get_all_details (CURRENCY, $condition );

        $user_currency = $currency_details->row()->currency_symbols;

    

    $userCurrencySymbol = $this->db->select('currency_symbols')->where('currency_type',$_POST['currency_code'])->get(CURRENCY)->row()->currency_symbols;

    if($rental_detail->currency != $_POST['currency_code'])

    {

      $currency_price = currency_conversion($rental_detail->currency,$_POST['currency_code'], $veh_price);

    }

    else

    {

      $currency_price = $veh_price;

    }

     $reviewTotals = $this->mobile_model->get_review_tot($vehicle_id);



        $star_avg = $reviewTotals->row()->tot_tot * 20;

        $reviewData = $this->mobile_model->get_review($vehicle_id);

        $reviewTotal = $reviewData->num_rows();

        $property_review = array();



        if($reviewData->num_rows()>0){

          foreach($reviewData->result() as $review){ 

            if($review->image == '') {

              $img_url = base_url().'images/users/profile.png';

            }  else {

              $img_url = base_url().'images/users/'.$review->image;

            } 

            $review_date = date('F Y',strtotime($review->dateAdded));

            if($review->firstname !="")$review_name = $review->firstname; else $review_name="";

            if($review->review !="")$review_comments = $review->review; else $review_comments="";

            

            $property_review[] = array("user_name"=>$review_name,"review"=>nl2br($review_comments),"star_rating"=>intval($review->total_review),"review_date"=>$review_date,"user_image"=>$img_url); 

 

          }

        

        }



        $json_encode = json_encode(array("status"=>1,"message"=>$this->veh_avail,/*"Booked_dates" => $BookedArr,*/"vehicle_image" => $prodimgArr,/*"check"=>$check,*/"defaultvehicletitle"=>(string)$producttitle,"vehicledesc"=>$productdesc,"driverDetails"=>$Driver_details,"booking_type"=>$booking_type,"min_hour_price"=>floatval($min_hour_price),"day_price"=>floatval($day_price),"weekly_price"=>floatval($weekly_price),"monthly_price"=>floatval($monthly_price),"hostimg"=>$userimg,"loginUserType"=>$loginUserType,"hostname"=>$hostname,"accommodates"=>$accommodates,"Doors"=>$doors ,"fuel_type"=>$fuel_type,"is_favourite"=>$fav,"Airbags"=>$Airbags,"Guest_Capacity"=>$Guest_Capacity,"Transmission_type"=>$Transmission_type,"country"=>$country,"state"=>$state,"city"=>$city,"post_code"=>$post_code,"address"=>$address,"latitude"=>$latitude,"longitude"=>$longitude,"cancellation"=>$cancellation,"car_rules"=>$house_rules_val,"other_things_to_note"=>$other_things_to_note_val,"terms_condition"=>$terms_val,"important_info"=>$import_info_val,"list_name"=>$list_name,'vehicle_id'=>intval($vehicle_id),'host_id'=>intval($host_id),'host_email'=>$host_email,'user_currency'=>$userCurrencySymbol,'user_about'=>$user_about,'hr_price'=>floatval($price_perweek),'day_price'=>floatval($price_permonth),'url'=>base_url(),'vehicle_make'=>$home_type,'vehicle_model'=>$room_type_varfinal,'vehicle_type'=>$type_id_sql_varfinal,'email_verified'=>$email_verified, 'ph_verified'=>$ph_verified, 'id_verified'=>$id_verified, 'member_since'=>$member_since, 'facebook'=>$facebook, 'google'=>$google, 'userAddress'=>$userAddress, 'hostabout'=>$hostabout,"property_currency_code"=>$currency_details->row()->currency_type,"property_currency_symbol"=>$currency_details->row()->currency_symbols,'list_details'=>$listarr,'security_deposit'=>$security_deposit,"services"=>$services,"total_review_count"=>intval($reviewTotal),"star_rating"=>floatval($star_avg),"property_reviews"=>$property_review,"booked_dates"=>$sometime_arr,"listing_info"=>$list_info,"min_days"=>$min_days,"min_hours"=>$min_hours));

        

      } else {

        $json_encode = json_encode(array("status"=>0,"message"=>$this->no_veh_avail));

        

      }

    echo $json_encode;

    }

    function mob_readmoreContent($content)
    {
      $paragraphAfter = 1;
      $content = explode("<p>",$content);
      return $content[1];
    }

    //add review

    public function vehicle_mobile_add_review()
    {

    // echo "string";exit();

    $base_id = $this->input->post ( 'base_id' );

    $user_id = $this->input->post ( 'user_id' );

    $vehicle_id = $this->input->post ( 'vehicle_id' );

    $bookingno = $this->input->post ( 'bookingno' );

    $review = $this->input->post ( 'review' );

    $total_review = $this->input->post ( 'star_rating' );



    if($user_id=="" || $user_id==0  || $vehicle_id =="" || $vehicle_id ==0 ||  $bookingno =="" ||  $review =="") {

      echo json_encode(array("status"=>0,"message"=>$this->parm_missing));

      exit;

    } 
    else 
    {

      $this->db->select('*');

      $this->db->from(USERS.' as u');

      $this->db->join(VEHICLE.' as p' , 'p.user)id = u.id');



      $userDetails = $this->db->query('select u.email,u.id from '.USERS.' as u where u.id ="'.$user_id.'"');

      if($userDetails->num_rows() >0) {

        foreach($userDetails->result() as $u) {

          $user_id = $u->id;  

          $email = $u->email; 

        }

      } else {

        echo json_encode(array("status"=>1,"message"=>$this->no_dta_found));

        exit;

      }

      $renter_Details = $this->db->query('select p.user_id from '.VEHICLE.' as p where p.id ="'.$vehicle_id.'"');

      /*print_r($renter_Details->result()->user_id); die;*/

      if($renter_Details->num_rows() >0) {

        foreach($renter_Details->result() as $r) {

          $host_id = $r->user_id;  

        }

        $dataArr = array( 'vehicle_type' => $base_id,'review'=>$review, 'status'=>'Inactive', 'vehicle_id'=>$vehicle_id, 'user_id'=>$host_id, 'reviewer_id'=>$user_id, 'email'=>$email, 'bookingno'=>$bookingno, 'total_review'=>$total_review);

        $insertquery = $this->mobile_model->veh_add_review($dataArr);

        $review_id = $this->db->insert_id();

        echo json_encode(array("status"=>1,"message"=>$this->succ,"review_id"=>intval($review_id)));

        exit;

      } else {

        echo json_encode(array("status"=>1,"message"=>$this->no_dta_found));

        exit;

      }



    }

  }

   public function vehicle_show_review()

  {

    $user_id = $this->input->post ( 'user_id' );

    $booking_no = $this->input->post ( 'booking_no' );

    if($user_id =="" || $booking_no =="") {

      echo json_encode(array("status"=>0,"message"=>$this->parm_missing));

      exit;

    }


    $review_all = $this->mobile_model->get_vehicle_trip_review($booking_no,$user_id); 

    $your_review = array();



    if($review_all->num_rows()>0)

    {

        foreach($review_all->result() as $review) 

        {

          if($review->image == '') {

            $img_url = base_url().'images/users/profile.png';

          }else{

            $img_url = base_url().'images/users/'.$review->image;

          } 



        $review_date = date('d-m-Y',strtotime($review->dateAdded));

        $your_review[] = array("name"=>$review->firstname,"review"=>$review->review,"review_date"=>$review_date,"star_rating"=>intval($review->total_review),"email"=>$review->email,"user_image"=>$img_url);         

      }

      echo json_encode(array("status"=>1,"message"=>$this->succ,"your_review"=>$your_review),JSON_PRETTY_PRINT);

      exit;

    } 

    else 

    {

      echo json_encode(array("status"=>1,"message"=>$this->no_dta_found,"your_review"=>$your_review),JSON_PRETTY_PRINT);

      exit;

    }      

  }



  public function vehicle_mobile_add_dispute()

  {

    $user_id = $this->input->post ( 'user_id' );

    $vehicle_id = $this->input->post ( 'vehicle_id' );

    $bookingno = $this->input->post ( 'bookingno' );

    $message = $this->input->post ( 'message' );

    $base_id = $this->input->post ( 'base_id' );


    if($user_id=="" || $user_id==0  || $vehicle_id =="" || $vehicle_id ==0 ||  $bookingno =="" ||  $message =="") {

      echo json_encode(array("status"=>0,"message"=>$this->parm_missing));

      exit;

    } else {

      $this->db->select('*');

      $this->db->from(USERS.' as u');

      /*$this->db->join(PRODUCT.' as p' , 'p.user)id = u.id');*/

      $this->db->join(VEHICLE.' as p' , 'p.user_id = u.id');



      $userDetails = $this->db->query('select u.email,u.id from '.USERS.' as u where u.id ="'.$user_id.'"');



      if($userDetails->num_rows() >0) {

        foreach($userDetails->result() as $u) {

          $user_id = $u->id;  

          $email = $u->email; 

        }

      } else {

        echo json_encode(array("status"=>1,"message"=>$this->no_dta_found));

        exit;

      }

      $renter_Details = $this->db->query('select p.user_id from '.VEHICLE.' as p where p.id ="'.$vehicle_id.'"');

      //echo $this->db->last_query();die;



      if($renter_Details->num_rows() >0) {

        foreach($renter_Details->result() as $r) {

          $disputer_id = $r->user_id;  

        }

        $dataArr = array('vehicle_id'=>$vehicle_id,

            'vehicle_type'=>$base_id,

            'message'=>$message,

            'user_id'=>$disputer_id,

            'booking_no'=>$bookingno,

            'email'=>$email,

            'disputer_id'=>$user_id

            );

        $insertquery = $this->mobile_model->veh_add_dispute($dataArr);

        $dispute_id = $this->db->insert_id();

        echo json_encode(array("status"=>1,"message"=>$this->succ,"dispute_id"=>intval($dispute_id)));

        exit;

      } else {

        echo json_encode(array("status"=>1,"message"=>$this->no_dta_found));

        exit;

      }



    }

  }

  

  public function vehicle_mobile_message()
  {
    $vehicle_type = $this->input->post('base_id');
    $vehicle_id = $this->input->post('vehicle_id');
    $bookingno = $this->input->post('bookingno');
    $recevier_id = $this->input->post('receiver_id');
    $senderId = $this->input->post('senderId');
    $message = $this->input->post('message');
    $language_code = $this->input->post('lang_code');

    if($vehicle_type == "" || $vehicle_id == ""  || $bookingno =="" || $recevier_id == "" ||  $message =="") {
      echo json_encode(array("status"=>0,"message"=>$this->parm_missing));
      exit;
    }

    if($vehicle_type=='4') { $subject = 'Car'; } else { $subject='Van'; } 
    $now = time();
    $dataArr = array('rental_type'=>$vehicle_type, 'productId' => $this->input->post('vehicle_id'), 'bookingNo' => $this->input->post('bookingno'), 'senderId' => $this->input->post('senderId'), 'receiverId' => $this->input->post('receiver_id'), 'subject' => $subject.' Booking Request : ' . $this->input->post('bookingno'), 'message' => $this->input->post('message'));

    $this->user_model->simple_insert(MED_MESSAGE, $dataArr);

    echo json_encode(array("status"=>1,"message"=>$this->succ));
    exit;
  }



    public function add_driver_review()

    {
  

        if ($_POST['vehicle_id'] != '') 

        {

          $user_id = $_POST['user_id'];

          $driver_id = $this->db->query('select u.driver_id from '.VEHICLE.' as u where u.id ="'.$_POST['vehicle_id'].'"');



      if($driver_id->num_rows() >0) {

        foreach($driver_id->result() as $u) {

          $driver_id_rev = $u->driver_id;  

        }

      } else {

        echo json_encode(array("status"=>1,"message"=>$this->no_drvr_data_avail));

        exit;

      }

             $total_review = $_POST['driver_total_review'] > 1 ? $_POST['driver_total_review'] : 1;

            $date = date('Y-m-d H:i:s');

            $dataArr = array('vehicle_type'=>$_POST['base_id'], 'review' => $_POST['review'], 'status' => 'Inactive', 'vehicle_id' => $_POST['vehicle_id'], 'user_id' => $_POST['user_id'], 'reviewer_id' => $driver_id_rev ,'user_email' => $_POST['email'], 'bookingno' => $_POST['bookingno'], 'driver_total_review' => $total_review, 'dateAdded'=>$date);

           // print_r($dataArr); exit();

            $this->mobile_model->add_driver_review($dataArr);

            $driver_review_id = $this->db->insert_id();

             echo json_encode(array("status"=>1,"message"=>$this->succ,"driver_review_id"=>intval($driver_review_id)));

        exit;

        }
        else {

        echo json_encode(array("status"=>1,"message"=>$this->parm_missing));

        exit;

      }

       

    }

    public function show_driver_review()

  {

    $user_id = $this->input->post ( 'user_id' );

    $booking_no = $this->input->post ( 'booking_no' );

    if($user_id =="" || $booking_no =="") {

      echo json_encode(array("status"=>0,"message"=>$this->parm_missing));

      exit;

    }


    $review_all = $this->mobile_model->get_driver_trip_review($booking_no,$user_id); 

    $your_review = array();



    if($review_all->num_rows()>0)

    {

        foreach($review_all->result() as $review) 

        {

          if($review->image == '') {

            $img_url = base_url().'images/users/profile.png';

          }else{

            $img_url = base_url().'images/users/'.$review->image;

          } 



        $review_date = date('d-m-Y',strtotime($review->dateAdded));

        $your_review[] = array("name"=>$review->firstname,"review"=>$review->review,"review_date"=>$review_date,"star_rating"=>intval($review->driver_total_review),"email"=>$review->user_email,"user_image"=>$img_url);         

      }

      echo json_encode(array("status"=>1,"message"=>$this->succ,"your_review"=>$your_review),JSON_PRETTY_PRINT);

      exit;

    } 

    else 

    {

      echo json_encode(array("status"=>1,"message"=>$this->no_dta_found,"your_review"=>$your_review),JSON_PRETTY_PRINT);

      exit;

    }      

  }

  public function mobile_vehicle_cancel()
  {

    $vehicle_type = $this->input->post('base_id');    

    $prd_id = $this->input->post('vehicle_id');

    $bookingNo = $this->input->post('bookingno');

    $cancel_percentage = $this->input->post('cancel_percentage');

    $user_id = $this->input->post('user_id');

    $disputer_id = $this->input->post('disputer_id');

    $message = $this->input->post('message');

    $email = $this->input->post('email');

   

    if($disputer_id != "" && $user_id != "" && $prd_id != "" && $bookingNo != "" &&  $message != "" && $vehicle_type != "")

    {     

        $excludeArr = array();

        $dataArr = array('vehicle_type'=>$vehicle_type, 'vehicle_id' => $prd_id, 'cancellation_percentage' => $cancel_percentage, 'message' => $message, 'user_id' => $user_id, 'booking_no' => $bookingNo, 'email' => $email, 'disputer_id' => $disputer_id, 'cancel_status' => 1);



        /* Mail to Host Start*/

        $newsid = '56';

        $template_values = $this->mobile_model->get_newsletter_template_details($newsid);

        if ($template_values['sender_name'] == '' && $template_values['sender_email'] == '') {

            $sender_email = $this->data['siteContactMail'];

            $sender_name = $this->data['siteTitle'];

        } else {

            $sender_name = $template_values['sender_name'];

            $sender_email = $template_values['sender_email'];

        }

        $condition = array('id' => $disputer_id);

        $hostDetails = $this->mobile_model->get_all_details(USERS, $condition);

        $uid = $hostDetails->row()->id;

        $hostname = $hostDetails->row()->user_name;

        $host_email = $hostDetails->row()->email;

        $condition = array('id' => $user_id);

        $custDetails = $this->mobile_model->get_all_details(USERS, $condition);

        $cust_name = $custDetails->row()->user_name;

        $condition = array('id' => $prd_id);

        $prdDetails = $this->mobile_model->get_all_details(VEHICLE, $condition);

        $veh_title = $prdDetails->row()->veh_title;

        $reason = $this->input->post('message');

        $booking_no = $bookingNo;

        $email_values = array('from_mail_id' => $sender_email, 'to_mail_id' => $host_email, 'subject_message' => $template_values ['news_subject'], 'body_messages' => $message);

        $reg = array('logo' => $this->data['logo'],'host_name' => $hostname, 'cust_name' => $cust_name, 'veh_title' => $veh_title, 'reason' => $reason, 'booking_no' => $booking_no);

        $message = $this->load->view('newsletter/ToHostCancelBooking' . $newsid . '.php', $reg, TRUE);

       

        $this->load->library('email', $config);

        $this->email->from($email_values['from_mail_id'], $sender_name);

        $this->email->to($email_values['to_mail_id']);

        $this->email->subject($email_values['subject_message']);

        $this->email->set_mailtype("html");

        $this->email->message($message);

        try {

            $this->email->send();

            $returnStr ['msg'] = 'Successfully registered';

            $returnStr ['success'] = '1';

        } catch (Exception $e) {

            /*echo $e->getMessage();*/

        }



        $this->mobile_model->vehicle_add_cancellation($dataArr);


        $UpdateArr=array('cancelled'=>'No');

        $Condition=array('vehicle_id'=>$prd_id,

                 'user_id'=>$user_id,

                 'Bookingno'=>$bookingNo);

        $this->mobile_model->update_details(VEHICLE_ENQUIRY,$UpdateArr,$Condition);



        $getEnquiryDet=$this->mobile_model->get_all_details(VEHICLE_ENQUIRY,array('Bookingno'=>$bookingNo));

        $TheSubTot=$getEnquiryDet->row()->subTotal;

        $SecDeposit=$getEnquiryDet->row()->secDeposit;

        $CancelPercentage=$getEnquiryDet->row()->cancel_percentage;

        $CancelPercentAmt=$TheSubTot/100*$CancelPercentage;

        $cancel_amount_toGuest=$TheSubTot-$CancelPercentAmt;

        

        if($getEnquiryDet->row()->cancel_percentage!="100"){ //For Moderate,Flexible

          $CancelAmountWithSecDeposit=$cancel_amount_toGuest+$SecDeposit;

        }else{  //For Strict

          $CancelAmountWithSecDeposit=$SecDeposit;

        }



        $UpdateCommissionArr=array('paid_cancel_amount'=>$CancelAmountWithSecDeposit);

        $ConditionCommission=array('booking_no'=>$bookingNo);

        $this->mobile_model->update_details(COMMISSION_TRACKING,$UpdateCommissionArr,$ConditionCommission);



        echo json_encode(array("status"=>200,"message"=>$this->succ/*,"dispute_id"=>intval($dispute_id)*/),JSON_PRETTY_PRINT);

        exit;

    } 

    else 

    {

      echo json_encode(array("status"=>400,"message"=>$this->parm_missing),JSON_PRETTY_PRINT);

      exit;

    }

  }

  public function veh_cancellation_accept()
  {

    $disputeId = $this->input->post('cancel_id');

    $booking_no = $this->input->post('booking_no');

    if($disputeId == "")
    {
      echo json_encode(array("status"=>0,"message"=>$this->parm_missing),JSON_PRETTY_PRINT);
      exit;
    }

    $condition = array('id' => $disputeId);

    $disputeData = $this->mobile_model->get_all_details(VEHICLE_DIPSUTE, $condition);

    $data = array('status' => 'Accept');

    $this->mobile_model->update_details(VEHICLE_DIPSUTE, $data, $condition);

    $getBookedDate = $this->mobile_model->ExecuteQuery("select DATE(checkin_date) as checkinDate ,DATE(checkout_date) as checkoutDate from " . VEHICLE_ENQUIRY . " where Bookingno='" . $booking_no . "'")->row();

    //NEED TO DELETE THE BOOKING DateS

    $up_Q = "delete from fc_vehicle_bookings_dates WHERE tot_checked_in='" . $getBookedDate->checkinDate. "' AND tot_checked_out='".$getBookedDate->checkoutDate."' AND  vehicle_id=" . $disputeData->row()->vehicle_id;

        $this->mobile_model->ExecuteQuery($up_Q);

        $UpdateArr = array('cancelled' => 'Yes');

        $Condition = array('vehicle_id' => $disputeData->row()->vehicle_id, 'user_id' => $disputeData->row()->user_id, 'Bookingno' => $disputeData->row()->booking_no);

        $this->mobile_model->update_details(VEHICLE_ENQUIRY, $UpdateArr, $Condition);

    

        $getEnquiryDet = $this->mobile_model->get_all_details(VEHICLE_ENQUIRY, array('Bookingno' => $disputeData->row()->booking_no));

        $TheSubTot = $getEnquiryDet->row()->subTotal;

        $CancelPercentage = $getEnquiryDet->row()->cancel_percentage;

        $CancelAmount = $TheSubTot / 100 * $CancelPercentage;

        $UpdateCommissionArr = array('paid_cancel_amount' => $CancelAmount);

        $ConditionCommission = array('booking_no' => $disputeData->row()->booking_no);

        $this->mobile_model->update_details(COMMISSION_TRACKING, $UpdateCommissionArr, $ConditionCommission);

        $condition = array('id' => $disputeData->row()->disputer_id);

        $hostDetails = $this->mobile_model->get_all_details(USERS, $condition);

        $uid = $hostDetails->row()->id;

        $hostname = $hostDetails->row()->user_name;

        $host_email = $hostDetails->row()->email;

        $condition = array('id' => $disputeData->row()->user_id);

        $custDetails = $this->mobile_model->get_all_details(USERS, $condition);

        $cust_name = $custDetails->row()->user_name;

        $cust_email = $custDetails->row()->email;

        $newsid = '57';

        $template_values = $this->mobile_model->get_newsletter_template_details($newsid);

        if ($template_values['sender_name'] == '' && $template_values['sender_email'] == '') {

            $sender_email = $this->data['siteContactMail'];

            $sender_name = $this->data['siteTitle'];

        } else {

            $sender_name = $template_values['sender_name'];

            $sender_email = $template_values['sender_email'];

        }

        $email_values = array('mail_type' => 'html', 'from_mail_id' => $sender_email,  'to_mail_id' => $cust_email, 'subject_message' => $template_values ['news_subject'],);

        $reg = array('name' => 'Accepted', 'host_name' => $hostname, 'cus_name' => $cust_name,'logo' => $this->data['logo']);

        $message = $this->load->view('newsletter/ToGuestAcceptRejection' . $newsid . '.php', $reg, TRUE);

        $this->load->library('email');

        $this->email->set_mailtype($email_values['mail_type']);

        $this->email->from($email_values['from_mail_id'], $sender_name);

        $this->email->to($email_values['to_mail_id']);

        $this->email->subject($email_values['subject_message']);

        $this->email->message($message);

        try {

            $this->email->send();

            if ($this->lang->line('mail_send_success') != '') {

                $message = stripslashes($this->lang->line('mail_send_success'));

            } else {

                $message = "mail send success";

            }

            $this->setErrorMessage('success', $message);

        } catch (Exception $e) {

            /*echo $e->getMessage();*/

        }

    echo json_encode(array('status'=>1,'message'=>$this->succ_cancelled));
    exit();
  }

  function veh_cancellation_reject()
  {

      $disputeId = $this->input->post('cancel_id');

      $booking_no = $this->input->post('booking_no');

      if($disputeId == "")
      {
        echo json_encode(array("status"=>0,"message"=>$this->parm_missing),JSON_PRETTY_PRINT);
        exit;
      }

      $condition = array('id' => $disputeId);

      $data = array('status' => 'Reject');

      $ok = $this->mobile_model->update_details(VEHICLE_DIPSUTE, $data, $condition);

      /* Mail to Guest Start*/

      $newsid = '58';

      $template_values = $this->mobile_model->get_newsletter_template_details($newsid);

      if ($template_values['sender_name'] == '' && $template_values['sender_email'] == '') {

          $sender_email = $this->data['siteContactMail'];

          $sender_name = $this->data['siteTitle'];

      } else {

          $sender_name = $template_values['sender_name'];

          $sender_email = $template_values['sender_email'];

      }

      $getdisputeDetails = $this->mobile_model->get_all_details(VEHICLE_DIPSUTE, $condition);

      if($getdisputeDetails->row()->rental_type == 4){ $prdt_type = 'Car'; }

      if($getdisputeDetails->row()->rental_type == 5){ $prdt_type = 'Van'; }

      $condition = array('id' => $getdisputeDetails->row()->disputer_id);

      $hostDetails = $this->mobile_model->get_all_details(USERS, $condition);

      $uid = $hostDetails->row()->id;

      $hostname = $hostDetails->row()->user_name;

      $host_email = $hostDetails->row()->email;

      $condition = array('id' => $getdisputeDetails->row()->user_id);

      $custDetails = $this->mobile_model->get_all_details(USERS, $condition);

      $cust_name = $custDetails->row()->user_name;

      $email = $custDetails->row()->email;

      $condition = array('id' => $getdisputeDetails->row()->vehicle_id);

      $prdDetails = $this->mobile_model->get_all_details(VEHICLE, $condition);

      $prd_title = $prdDetails->row()->veh_title;

      $email_values = array('from_mail_id' => $sender_email, //'from_mail_id'=>'kailashkumar.r@pofitec.com',

          'to_mail_id' => $email, //'to_mail_id'=> 'preetha@pofitec.com',

          'subject_message' => $template_values ['news_subject'], 'body_messages' => $message);

      $reg = array('prdt_type' => $prdt_type,'host_name' => $hostname, 'cust_name' => $cust_name, 'prd_title' => $prd_title,'logo' => $this->data['logo']);

      $message = $this->load->view('newsletter/ToGuestRejectCancelBooking' . $newsid . '.php', $reg, TRUE);

      //send mail

      $this->load->library('email', $config);

      $this->email->from($email_values['from_mail_id'], $sender_name);

      $this->email->to($email_values['to_mail_id']);

      $this->email->subject($email_values['subject_message']);

      $this->email->set_mailtype("html");

      $this->email->message($message);

      try {

          $this->email->send();

          $returnStr ['msg'] = 'Successfully registered';

          $returnStr ['success'] = '1';

      } catch (Exception $e) {

          echo $e->getMessage();

      }

    echo json_encode(array('status'=>1,'message'=>$this->rejct));
    exit();

  }

  /*booking amount calculate*/
  public function vehicles_amountcalculate()
  {
    $checkIn = $this->input->post('checkIn');
    $checkOut = $this->input->post('checkOut');
    $booking_type = $this->input->post('booking_type');
    $id = $this->input->post('vehicle_id'); 
    $currency_result = $this->input->post('currency_code');

    if($checkIn == "" || $checkOut == "" || $booking_type == "" || $id == "" || $currency_result == "")
    {
      echo json_encode(array("status"=>400,"message"=>$this->parm_missing),JSON_PRETTY_PRINT);
      exit;
    }

    /*price details*/
    $priceDet = $this->db->where('id', $id)->get(VEHICLE)->row();
    $minhourPrice = $priceDet->min_hour_price;
    $hourlyPrice = $priceDet->min_hour_exprice;
    $dayPrice = $priceDet->day_price;
    $weeklyPrice = $priceDet->weekly_price;
    $monthlyPrice = $priceDet->monthly_price;
    $currencyCode = $priceDet->currency;
    $vehicle_type = $priceDet->vehicle_type;

    $NoOfDays = $this->getDatesFromRange(date('Y-m-d', strtotime($checkIn)), date('Y-m-d', strtotime($checkOut)));
    $stop_date = date('Y-m-d', strtotime($checkOut . ' +1 day'));

    if($booking_type=='daily')
    {
      $datetime1 = new DateTime(date('d M Y',strtotime($checkIn)));
      $datetime2 = new DateTime(date('d M Y',strtotime($stop_date)));
      $interval = $datetime1->diff($datetime2);
      
      $years = $interval->format('%y');
      $months = $interval->format('%m');
      $weeks = 0;
      $days = $interval->format('%d');
      if($years > 0)
      {
        $months +=$years*12;
      }
      if($days >= 7)
      {
        $weeks += (int)($days/7);
        $days = $days-($weeks*7);
      }
      $numberOfDays = '';
      if($months > 0 )
      {
        $numberOfDays .= $months;
        if ($this->lang->line('months_text') != '') { $months_text = stripslashes($this->lang->line('months_text')); } else { $months_text = 'Months'; }
        if ($this->lang->line('month_text') != '') { $month_text = stripslashes($this->lang->line('month_text')); } else { $months_text = 'Month'; }
        if($months > 1) { $numberOfDays .= ' '.$months_text.', '; } else { $numberOfDays .= ' '.$month_text.', '; }
      } 
      if($weeks > 0 )
      {
        
        $numberOfDays .= $weeks;
        if ($this->lang->line('weeks_text') != '') { $weeks_text = stripslashes($this->lang->line('weeks_text')); } else { $weeks_text = 'Weeks'; }
        if ($this->lang->line('week_text') != '') { $week_text = stripslashes($this->lang->line('week_text')); } else { $week_text = 'Week'; }
        if($weeks > 1) { $numberOfDays .= ' '.$weeks_text.', '; } else { $numberOfDays .= ' '.$week_text.', '; }
      }
      if($days > 0 )
      {
        
        $numberOfDays .= $days;
        if ($this->lang->line('days_text') != '') { $days_text = stripslashes($this->lang->line('days_text')); } else { $days_text = 'Days'; }
        if ($this->lang->line('day_text') != '') { $day_text = stripslashes($this->lang->line('day_text')); } else { $day_text = 'Day'; }
        if($days > 1) { $numberOfDays .= ' '.$days_text; } else { $numberOfDays .= ' '.$day_text; }
      }

      $this->data['no_of_months']=$months;
      $this->data['no_of_weeks']=$weeks;
      $this->data['no_of_days']=$days;
      
      $subTotMonthPrice = $subTotWeekPrice = $subTotDayPrice = $subTotHourPrice = 0;
      if($months > 0)
      {
        $subTotMonthPrice = $months*$monthlyPrice;
      }
      if($weeks > 0)
      {
        $subTotWeekPrice = $weeks*$weeklyPrice;
      }
      if($days > 0)
      {
        $subTotDayPrice = $days*$dayPrice;
      }
      $DateCalCul = $subTotMonthPrice+$subTotWeekPrice+$subTotDayPrice;
      
      if($vehicle_type == 4) { $seo_tag = 'car-booking-daily'; }
      if($vehicle_type == 5) { $seo_tag = 'van-booking-daily'; }
    }

    $service_tax_query = 'SELECT * FROM ' . COMMISSION . ' WHERE seo_tag="'.$seo_tag.'" AND status="Active"';
    $service_tax = $this->mobile_model->ExecuteQuery($service_tax_query);

    if ($service_tax->num_rows() == 0) {
        $this->data['taxValue'] = '0.00';
        $this->data['taxString'] = '0.00';
    } else {
        $this->data['commissionType'] = $service_tax->row()->promotion_type;
        $this->data['commissionValue'] = $service_tax->row()->commission_percentage;
        if ($service_tax->row()->promotion_type == 'flat') {
            $basecurrencyCode = $this->db->where('default_currency', 'Yes')->get(CURRENCY)->row();
            $currency_code = $basecurrencyCode->currency_type;
    
            if ($currency_code != $currencyCode) {  
      $rate = changeCurrency($currency_code,$currencyCode,$service_tax->row()->commission_percentage);
      } else {
                $rate = $service_tax->row()->commission_percentage;
            }
    
    if ($currency_code != $currency_result) {  
      $rateDisplay = changeCurrency($currencyCode,$currency_result,$service_tax->row()->commission_percentage);
            } else {
                $rateDisplay = $service_tax->row()->commission_percentage;
            }
    
            $this->data['taxValue'] = $rate; //for saving in DB in Prd Currency
            $this->data['taxString'] = $rateDisplay; //for displaying in siteCur
        } else {
            $finalTax = ($service_tax->row()->commission_percentage * $DateCalCul) / 100;
            $this->data['taxValue'] = $finalTax;
            $this->data['taxString'] = $finalTax;
            $currencyCode = $this->db->where('id', $id)->get(VEHICLE)->row()->currency;
            if ($currencyCode != $currency_result) {
      $this->data['taxString'] = changeCurrency($currencyCode,$currency_result,$this->data['taxString']);
            } elseif ($currencyCode == $currency_result) {
                $this->data['taxString'] = $finalTax;
            }
        }
    }

    $this->data['total_nights'] = $numberOfDays;
    $this->data['total_days']=count($NoOfDays);

    if ($currency_code != $currency_result) {  
      $this->data['subTotal'] = changeCurrency($currencyCode,$currency_result,$DateCalCul);
    } else {
      $this->data['subTotal'] = $DateCalCul;
    }

    $currencyCode = $this->db->where('id', $id)->get(VEHICLE)->row()->currency;
    $this->data['currencycd'] = $this->db->where('id', $id)->get(VEHICLE)->row()->currency;
    $securityDepositestart = $this->db->where('id', $id)->get(VEHICLE)->row()->security_deposit;

    if ($currencyCode != $this->session->userdata('currency_type')) {
    $securityDeposite_string = changeCurrency($currencyCode,$this->session->userdata('currency_type'),$securityDepositestart);
    } elseif ($currencyCode == $this->session->userdata('currency_type')) {
        $securityDeposite_string = $securityDepositestart;
    }
    if ($currencyCode != $this->session->userdata('currency_type')) {
    $this->data['total_value'] = changeCurrency($currencyCode,$this->session->userdata('currency_type'),$DateCalCul);
    } elseif ($currencyCode == $this->session->userdata('currency_type')) {
        $this->data['total_value'] = $DateCalCul;
    }

    $basecurrencyCode = $this->db->where('default_currency', 'Yes')->get(CURRENCY)->row();
    $currency_code = $basecurrencyCode->currency_type;
     
    if ($service_tax->row()->promotion_type == 'flat') {
     $serviceFeeInPrdCurrency=changeCurrency($currency_code,$currencyCode,$service_tax->row()->commission_percentage);
    }else{
      $serviceFeeInPrdCurrency=$service_tax->row()->commission_percentage * $DateCalCul / 100;
    }

    $this->data['net_total_value'] = ($this->data['total_value']) + ($this->data['taxString']) + ($securityDeposite_string);

    if ($currencyCode != $this->session->userdata('currency_type')) {
      $net_total_string = $this->data['net_total_value'];
    } elseif ($currencyCode == $this->session->userdata('currency_type')) {
        $net_total_string = $this->data['net_total_value'];
    }

    echo json_encode(array("status"=>200,"message"=>$this->amt_dtls_rtr,"booking_days"=>$this->data['total_nights'],"total_amount"=>floatval($this->data['total_value']),"service_fee"=>floatval($this->data['taxString']),"security_deposit"=>floatval($securityDeposite_string),"total"=>floatval($net_total_string)),JSON_PRETTY_PRINT);
    exit();
  }

   /* Book vehicle */

  public function vehicle_mobile_host_request()
  {
    $vehicle_type = $this->input->post('base_id');
    $booking_type = $this->input->post('booking_type');
    $checkin = $this->input->post('checkin');
    $checkout = $this->input->post('checkout');
    $prd_id = $this->input->post ('vehicle_id');
    $user_currencyCode = $this->input->post('currency_code');
    $NoofGuest = $this->input->post('NoofGuest');

    $driver_details = new stdClass();
    
    if($booking_type == "" || $checkin == "" || $checkout == "" || $prd_id == "" || $NoofGuest == "")
    {
      echo json_encode(array("status"=>400,"message"=>$this->parm_missing));
      exit();
    }

    if($booking_type=='daily')
    {
      $checkin=date('Y-m-d 00:00:00', strtotime($this->input->post('checkin')));
      $checkout=date('Y-m-d 23:00:00', strtotime($this->input->post('checkout')));
      $NoOfDays = $this->getDatesFromRange(date('Y-m-d', strtotime($checkin)), date('Y-m-d', strtotime($checkout)));
      $dateCheck = $this->mobile_model->get_all_details(VEHICLE_BOOKING_DATES, array('vehicle_id' => $prd_id));
      $returnStr['status_code'] = "";
      if ($dateCheck->num_rows() > 0) {
        foreach ($dateCheck->result() as $dateCheckStr) {
          if (in_array($dateCheckStr->the_date, $NoOfDays)) {            
            echo json_encode(array("status"=>0,"message"=>$this->alrdy_bkd));
            exit();
          }
        }
      }
    }
    else
    {
      $prd_id = $this->input->post('vehicle_id');
      $checkin=date('Y-m-d H:i:s', strtotime($this->input->post('checkin')));
      $checkout=date('Y-m-d H:i:s', strtotime($this->input->post('checkout')));
      $split_date=explode(" ",$checkin);
      $checkedInn=$split_date[0];
      $split_date2=explode(" ",$checkout);
      $checkedOutt=$split_date2[0];
      $betweendays=$this->db->select('*')->from(VEHICLE_BOOKING_DATES)->where('(tot_checked_in BETWEEN "'. $checkin. '" and "'. $checkout.'" OR tot_checked_out BETWEEN "'. $checkin. '" and "'. $checkout.'")')->where('vehicle_id = '. $prd_id)->get();
      $num=$betweendays->num_rows();
      if ($num > 0 )
      {
        echo json_encode(array("status"=>0,"message"=>$this->alrdy_bkd));
        exit();
      }
      else{
        $returnStr ['status_code'] = "";
      }
    }

    if ($returnStr ['status_code'] == "") 
    {
      $currencyPerUnitSeller = 1;
      $getLastBookingNo= $this->db->select('Bookingno')->order_by('id','DESC')->get(VEHICLE_ENQUIRY)->row()->Bookingno;
      if($getLastBookingNo=='')
      {
        $val = 1500000;
      }
      else
      {
        $val = $getLastBookingNo;
        $val = str_replace('VE','',$val);
        $val = $val+1;
      }

      $veh_details = $this->mobile_model->get_all_details(VEHICLE, array('id' => $prd_id));
      $last_cron_id = $this->db->select('*')->from('fc_currency_cron')->order_by('curren_id','desc')->limit(1)->get();

      $bookingno = "VE" . $val;
      $dataArr = array(
        'vehicle_type' => $vehicle_type,
        'user_id' => $this->input->post('user_id'),
        'vehicle_id' => $this->input->post('vehicle_id'),
        'checkin_date' => $checkin,
        'checkout_date' => $checkout,
        'NoofGuest' => $this->input->post('NoofGuest'),
        'renter_id' => $veh_details->row()->user_id,
        'booking_type' => $this->input->post('booking_type'),
        'numofdates' => $this->input->post('numofdates'),
        'no_of_hours' => $this->input->post('no_of_hours'),
        'no_of_months' => $this->input->post('no_of_months'),
        'no_of_weeks' => $this->input->post('no_of_weeks'),
        'no_of_days' => $this->input->post('no_of_days'),
        'caltophone' => 0,
        'totalAmt' => $this->input->post('totalAmt'),
        'cancel_percentage' => $veh_details->row()->cancellation_percentage,
        'currencycode' => $veh_details->row()->currency,
        'secDeposit' => $this->input->post('secDeposit'),
        'serviceFee' => $this->input->post('serviceFee'),
        'subTotal' => $this->input->post('subTotal'),
        'user_currencyCode' => $user_currencyCode,
        'walletAmount' => 0.00,
        'currencyPerUnitSeller' => $currencyPerUnitSeller,
        'dateAdded' => date('Y-m-d h:i:s'),
        'Bookingno' => $bookingno,
        'choosed_option' => 'book_now',
        'currency_cron_id'=>$last_cron_id->row()->curren_id
        );

      $booking_status = array('booking_status' => 'Enquiry');
      $dataArr1 = array_merge($dataArr, $booking_status);
      $this->db->insert(VEHICLE_ENQUIRY, $dataArr1);

      $insertid = $this->db->insert_id();

      $bkd_details = array();
      $addtnl_details = array();
      $slf_sts = False;
      $drv_sts = False;


      $productList = $this->mobile_model->view_veh_details_booking(' where p.id="' . $prd_id . '" and rq.id="' . $insertid . '" group by p.id order by p.createdAt desc limit 0,1');

      if($productList->num_rows() > 0)
      {  
        foreach ($productList->result() as $key) 
        {
          if($language_code == 'en')
          {
            $pdtNameField = 'veh_title';
          }
          else
          {
            $pdtNameField = 'veh_title_ph';
          }

          if($key->booking_type=='daily')
          {
            $bk_dt = date('d', strtotime($key->checkin_date)).' '.date('M', strtotime($key->checkin_date)).' '.date('Y', strtotime($key->checkin_date)).'- '.date('d', strtotime($key->checkout_date)).' '.date('M', strtotime($key->checkout_date)).' '.date('Y', strtotime($key->checkout_date));

            $numberOfDays = '';
            $months = $key->no_of_months;
            $weeks = $key->no_of_weeks;
            $days = $key->no_of_days;

            if($months > 0 )
            {
              $numberOfDays .= $months; 
              if ($this->lang->line('months_text') != '') { $months_text = stripslashes($this->lang->line('months_text')); } else { $months_text = 'Months'; }
              if ($this->lang->line('month_text') != '') { $month_text = stripslashes($this->lang->line('month_text')); } else { $months_text = 'Month'; }
              if($months > 1) { $numberOfDays .= ' '.$months_text.', '; } else { $numberOfDays .= ' '.$month_text.', '; }
            } 
            if($weeks > 0 )
            {
              $numberOfDays .= $weeks;
              if ($this->lang->line('weeks_text') != '') { $weeks_text = stripslashes($this->lang->line('weeks_text')); } else { $weeks_text = 'Weeks'; }
              if ($this->lang->line('week_text') != '') { $week_text = stripslashes($this->lang->line('week_text')); } else { $week_text = 'Week'; }
              if($weeks > 1) { $numberOfDays .= ' '.$weeks_text.', '; } else { $numberOfDays .= ' '.$week_text.', '; }
            }
            if($days > 0 )
            {
              $numberOfDays .= $days;
              if ($this->lang->line('days_text') != '') { $days_text = stripslashes($this->lang->line('days_text')); } else { $days_text = 'Days'; }
              if ($this->lang->line('day_text') != '') { $day_text = stripslashes($this->lang->line('day_text')); } else { $day_text = 'Day'; }
              if($days > 1) { $numberOfDays .= ' '.$days_text; } else { $numberOfDays .= ' '.$day_text; }
            }

          }
          else
          {
            $bk_dt = date('d', strtotime($key->checkin_date)).' '.date('M', strtotime($key->checkin_date)).' '.date('Y', strtotime($key->checkin_date)).' '.date('H:i',strtotime($key->checkin_date)).'- '.date('d', strtotime($key->checkout_date)).' '.date('M', strtotime($key->checkout_date)).' '.date('Y', strtotime($key->checkout_date)).' '.date('H:i',strtotime($key->checkout_date));

            $numberOfhrs = $key->no_of_hours;
            if ($total_hours == 1 || $total_hours == 0.5) {
              if($this->lang->line('hour_text') != '') { $hours_text = " ".stripslashes($this->lang->line('hour_text')); } else $hours_text = "hour" ;
            } else {
              if($this->lang->line('hours_text') != '') { $hours_text = " ".stripslashes($this->lang->line('hours_text')); } else $hours_text = "hours" ;
            }  
            if ($this->lang->line('Booking_for') != '') {  $hours_text = " ".stripslashes($this->lang->line('Booking_for'));  } else {  $hours_text =  "Booking for";  } 
            $numberOfDays = $total_hours . ' '.$hours_text;
          }

          if ($key->user_currencycode != $user_currencyCode) {
            $GrandtotalAmt = currency_conversion($key->user_currencycode, $user_currencyCode, $key->totalAmt);
            $serviceFee = currency_conversion($key->user_currencycode, $user_currencyCode, $key->serviceFee);
            $secDeposit = currency_conversion($key->user_currencycode, $user_currencyCode, $key->secDeposit);
          } else {
            $GrandtotalAmt = $key->totalAmt;
            $serviceFee = $key->serviceFee;
            $secDeposit = $key->secDeposit;
          }

          $img_src = 'dummyProductImage.jpg';
          if ($key->image != "" && file_exists('./images/vehicles/' . $key->image)) {
            $img_src = base_url().'images/vehicles/'.$key->image;
          }

          if ($key->driver_type == 'self_drive') {
            $slf_sts = True;
          } elseif($key->driver_type == 'with_drive') {
            $drv_sts = True;
          }
          else
          {
            $slf_sts = True;
            $drv_sts = True;
          }

          if($key->driver_id != 0)
          {
            $driver_Detail = $this->mobile_model->get_all_details(DRIVER_MASTER, array('id' => $key->driver_id));
            foreach ($driver_Detail->result() as $ke) {
              $driver_details = array("id"=>$ke->id,"name"=>$ke->driver_name,"age"=>$ke->age,"email"=>$ke->email,"insurance_num"=>$ke->insurance_num,"license_num"=>$ke->license_num);
            }
          }

          $bkd_details = array("vehicle_title"=>$key->$pdtNameField,"location"=>$key->address,"Noofdates"=>$key->numofdates,"Noofhours"=>$key->no_of_hours,"NoofGuest"=>$key->NoofGuest,"booked_date"=>$bk_dt,"booking_type"=>$key->booking_type,"booking_duration"=>$numberOfDays,"vehicle_currency"=>$key->vehicle_currency,"total_amount"=>$GrandtotalAmt,"security_depos"=>$secDeposit,"serviceFee"=>$serviceFee,"veh_image"=>$img_src,"self_drive_status"=>$slf_sts,"with_driver_status"=>$drv_sts,"driver_Details"=>$driver_details,"currency_cron_id"=>$key->currency_cron_id);
        }


        if($productList->row()->booking_type == 'hourly')
        {
          $additional_price_list = $this->mobile_model->get_all_details(VEHICLE_ADDITIONAL_PRICE, array('vehicle_id' => $prd_id,'price_type<>'=>'per day'));
        }
        else
        {
          $additional_price_list = $this->mobile_model->get_all_details(VEHICLE_ADDITIONAL_PRICE, array('vehicle_id' => $prd_id));
        }

        if($additional_price_list->num_rows() > 0)
        {
          foreach ($additional_price_list->result() as $addPrice1) 
          {
            if($language_code == 'en')
            {
              $productTitle1=$addPrice1->title;
              $desc1=$addPrice1->short_description;
            }
            else
            {
              $titleNameField1='title_ph';
              $descph1='short_description_ph';
              if($addPrice1->$titleNameField1=='') { $productTitle1=$addPrice1->title; } else{ $productTitle1=$addPrice1->$titleNameField1;  }

              if($addPrice1->$descph1=='') { $desc1=$addPrice1->title; } else{ $desc1=$addPrice1->$descph1;  }
            }

            if ($productList->row()->vehicle_currency != $user_currencyCode) {
              $additional_price = currency_conversion($productList->row()->vehicle_currency, $user_currencyCode, $addPrice1->price);
            } else {
              $additional_price = $addPrice1->price;
            }

            $pdt_title1 = ($productTitle1 != "") ? ucfirst($productTitle1) : '---';
            $addtnl_details[] = array("title"=>$pdt_title1,"desc"=>$desc1,"id"=>$addPrice1->id,"item_count"=>$addPrice1->max_limit,"amount"=>intval($additional_price),"price_type"=>$addPrice1->price_type);
          }
        }

        echo json_encode(array("status"=>1,"message"=>$this->succ,"vehicle_id"=>$prd_id,"enquiry_id"=>$insertid,"bookingno"=>$bookingno,"booking_details"=>$bkd_details,"additional_details"=>$addtnl_details),JSON_PRETTY_PRINT);
        exit();

      }
      else
      {
        echo json_encode(array("status"=>0,"message"=>$this->no_dta_found),JSON_PRETTY_PRINT);
        exit();
      }

    }
    else
    {
      echo json_encode(array("status"=>0,"message"=>$this->enqy_failed));
      exit();
    }

  }

  public function vehicle_request_confirm()
  {
    $vehicle_type = $_POST['base_id'];
    $language_code = $_POST['lang_code'];
    $vehicle_id = $_POST['vehicle_id'];
    $enquiry_id = $_POST['enquiry_id'];
    $currency_code = $_POST['currency_code'];
    

    if($vehicle_id == "" || $enquiry_id == "" || $language_code == "" || $currency_code == "")
    {
      echo json_encode(array("status"=>400,"message"=>$this->parm_missing));
      exit();
    }

    $productList = $this->mobile_model->view_veh_details_booking(' where p.id="' . $vehicle_id . '" and rq.id="' . $enquiry_id . '" group by p.id order by p.createdAt desc limit 0,1');

    if($productList->num_rows() > 0)
    {  
      foreach ($productList->result() as $key) 
      {
        if($language_code == 'en')
        {
          $pdtNameField = 'veh_title';
        }
        else
        {
          $pdtNameField = 'veh_title_ph';
        }

        if($key->booking_type=='daily')
        {
          $bk_dt = date('d', strtotime($key->checkin_date)).' '.date('M', strtotime($key->checkin_date)).' '.date('Y', strtotime($key->checkin_date)).'- '.date('d', strtotime($key->checkout_date)).' '.date('M', strtotime($key->checkout_date)).' '.date('Y', strtotime($key->checkout_date));

          $numberOfDays = '';
          $months = $key->no_of_months;
          $weeks = $key->no_of_weeks;
          $days = $key->no_of_days;

          if($months > 0 )
          {
            $numberOfDays .= $months; 
            if ($this->lang->line('months_text') != '') { $months_text = stripslashes($this->lang->line('months_text')); } else { $months_text = 'Months'; }
            if ($this->lang->line('month_text') != '') { $month_text = stripslashes($this->lang->line('month_text')); } else { $months_text = 'Month'; }
            if($months > 1) { $numberOfDays .= ' '.$months_text.', '; } else { $numberOfDays .= ' '.$month_text.', '; }
          } 
          if($weeks > 0 )
          {
            $numberOfDays .= $weeks;
            if ($this->lang->line('weeks_text') != '') { $weeks_text = stripslashes($this->lang->line('weeks_text')); } else { $weeks_text = 'Weeks'; }
            if ($this->lang->line('week_text') != '') { $week_text = stripslashes($this->lang->line('week_text')); } else { $week_text = 'Week'; }
            if($weeks > 1) { $numberOfDays .= ' '.$weeks_text.', '; } else { $numberOfDays .= ' '.$week_text.', '; }
          }
          if($days > 0 )
          {
            $numberOfDays .= $days;
            if ($this->lang->line('days_text') != '') { $days_text = stripslashes($this->lang->line('days_text')); } else { $days_text = 'Days'; }
            if ($this->lang->line('day_text') != '') { $day_text = stripslashes($this->lang->line('day_text')); } else { $day_text = 'Day'; }
            if($days > 1) { $numberOfDays .= ' '.$days_text; } else { $numberOfDays .= ' '.$day_text; }
          }

        }
        else
        {
          $bk_dt = date('d', strtotime($key->checkin_date)).' '.date('M', strtotime($key->checkin_date)).' '.date('Y', strtotime($key->checkin_date)).' '.date('H:i',strtotime($key->checkin_date)).'- '.date('d', strtotime($key->checkout_date)).' '.date('M', strtotime($key->checkout_date)).' '.date('Y', strtotime($key->checkout_date)).' '.date('H:i',strtotime($key->checkout_date));

          $numberOfhrs = $key->no_of_hours;
          if ($total_hours == 1 || $total_hours == 0.5) {
            if($this->lang->line('hour_text') != '') { $hours_text = " ".stripslashes($this->lang->line('hour_text')); } else $hours_text = "hour" ;
          } else {
            if($this->lang->line('hours_text') != '') { $hours_text = " ".stripslashes($this->lang->line('hours_text')); } else $hours_text = "hours" ;
          }  
          if ($this->lang->line('Booking_for') != '') {  echo stripslashes($this->lang->line('Booking_for'));  } else { echo "Booking for";  } 
          $numberOfDays = $total_hours . ' '.$hours_text;
        }

        if ($key->user_currencycode != $currency_code) {
          $GrandtotalAmt = currency_conversion($key->user_currencycode, $currency_code, $key->totalAmt);
          $serviceFee = currency_conversion($key->user_currencycode, $currency_code, $key->serviceFee);
          $secDeposit = currency_conversion($key->user_currencycode, $currency_code, $key->secDeposit);
        } else {
          $GrandtotalAmt = $key->totalAmt;
          $serviceFee = $key->serviceFee;
          $secDeposit = $key->secDeposit;
        }

        $img_src = 'dummyProductImage.jpg';
        if ($key->image != "" && file_exists('./images/vehicles/' . $key->image)) {
          $img_src = base_url().'images/vehicles/'.$key->image;
        }

        if ($key->driver_type == 'self_drive') {
          $slf_sts = True;
        } elseif($key->driver_type == 'with_drive') {
          $drv_sts = True;
        }
        else
        {
          $slf_sts = True;
          $drv_sts = True;
        }

        if($key->driver_id != 0)
        {
          $driver_Detail = $this->mobile_model->get_all_details(DRIVER_MASTER, array('id' => $key->driver_id));
          foreach ($driver_Detail->result() as $ke) {
            $driver_details = array("id"=>$ke->id,"name"=>$ke->driver_name,"age"=>$ke->age,"email"=>$ke->email,"insurance_num"=>$ke->insurance_num,"license_num"=>$ke->license_num);
          }
        }

        $bkd_details = array("enquiryid"=>$enquiry_id,"vehicle_title"=>$key->$pdtNameField,"location"=>$key->address,"Noofdates"=>$key->numofdates,"NoofGuest"=>$key->NoofGuest,"booked_date"=>$bk_dt,"booking_type"=>$key->booking_type,"booking_duration"=>$numberOfDays,"vehicle_currency"=>$key->vehicle_currency,"total_amount"=>$GrandtotalAmt,"security_depos"=>$secDeposit,"serviceFee"=>$serviceFee,"veh_image"=>$img_src,"self_drive_status"=>$slf_sts,"with_driver_status"=>$drv_sts,"driver_Details"=>$driver_details,"currency_cron_id"=>$key->currency_cron_id);
      }


      if($productList->row()->booking_type=='hourly')
      {
        $additional_price_list = $this->mobile_model->get_all_details(VEHICLE_ADDITIONAL_PRICE, array('vehicle_id' => $vehicle_id,'price_type<>'=>'per day'));
      }
      else
      {
        $additional_price_list = $this->mobile_model->get_all_details(VEHICLE_ADDITIONAL_PRICE, array('vehicle_id' => $vehicle_id));
      }

      if($additional_price_list->num_rows() > 0)
      {
        foreach ($additional_price_list->result() as $addPrice1) 
        {
          if($language_code == 'en')
          {
            $productTitle1=$addPrice1->title;
          }
          else
          {
            $titleNameField1='title_ph';
            if($addPrice1->$titleNameField1=='') { $productTitle1=$addPrice1->title; } else{ $productTitle1=$addPrice1->$titleNameField1;  }
          }

          if ($productList->row()->vehicle_currency != $currency_code) {
            $additional_price = currency_conversion($productList->row()->vehicle_currency, $currency_code, $addPrice1->price);
          } else {
            $additional_price = $addPrice1->price;
          }

          $pdt_title1 = ($productTitle1 != "") ? ucfirst($productTitle1) : '---';
          $addtnl_details[] = array("title"=>$pdt_title1,"id"=>$addPrice1->id,"item_count"=>$addPrice1->max_limit,"amount"=>$additional_price,"price_type"=>$addPrice1->price_type);
        }
      }

      echo json_encode(array("status"=>1,"message"=>$this->succ,"booking_details"=>$bkd_details,"additional_details"=>$addtnl_details),JSON_PRETTY_PRINT);
      exit();

    }
    else
    {
      echo json_encode(array("status"=>0,"message"=>$this->no_dta_found),JSON_PRETTY_PRINT);
      exit();
    }

  }

  public function vehicle_booking()
  {
    /*Array ( [vehicle_type] => 4 [booking_type] => daily [user_currencycode] => USD [vehicle_currency] => USD [ids] => Array ( [0] => 9 [1] => 13 ) [price] => Array ( [9] => 200.00 [13] => 200.00 ) [addi_price_hid] => Array ( [9] => 200.00 [13] => 200.00 ) [price_type] => Array ( [9] => per person [13] => per person ) [quant] => Array ( [9] => 1 [13] => 2 ) [message] => gh [grandAdditionalPriceTot] => [rightGrandTot] => 284.00 [rightNoofDates] => 4 [rightNoofGuest] => 1 [driver_id] => 1 [Bookingno] => VE1500192 )*/
   /* $ids = $this->input->post('ids');
    $quant = $this->input->post('quant'); 
    $price = $this->input->post('addi_price');
    $price_type = $this->input->post('price_type');*/
    $rightGrandTot = $this->input->post('GrandTot');
    $rightNoofGuest = $this->input->post('NoofGuest');
    $rightNoofDates = $this->input->post('NoofDates');
    $booking_type = $this->input->post('booking_type');
    $vehicle_currency = $this->input->post('vehicle_currency'); 
    $currency_code = $this->input->post('currency_code'); 
    $user_id = $this->input->post('user_id');
    $enquiry_id = $this->input->post('enquiry_id'); 
    $currency_cron_id = $this->input->post('currency_cron_id');
    $additional_item =  json_decode($this->input->post('additional_item'));
    $grandTot = $subtotal = 0;
    $addiPriceCountArray=array();
    $addiPriceArray=array();  

    if($booking_type == "" || $enquiry_id == "" || $user_id == "")
    {
      echo json_encode(array('status'=>0,'message'=>$this->parm_missing));
      exit;
    }

    foreach ($additional_item as $key => $value) {
     if($key == "Additional_Id"){ $ids =  $value; }
     if($key == "Quantity"){ $quant =  $value; }
     if($key == "Additional_Price"){ $price =  $value; }
     if($key == "Prie_Type"){ $price_type =  $value; }
    }
/*print_r($ids); print_r($quant); print_r($price); print_r($price_type); exit();*/
    foreach($ids as $addiPriceId)
    {
      if($quant[$addiPriceId] > 0 )
      {
        array_push($addiPriceCountArray,$addiPriceId);
        if ($vehicle_currency != $currency_code)
        {
          $addi_price = currency_conversion($vehicle_currency, $currency_code, $price[$addiPriceId]);

        } else {
          $addi_price = $price[$addiPriceId];
        }

        $addi_price_type = $price_type[$addiPriceId];
        if($addi_price_type=='per person')
        {
          $addPriceSubTot_val = $rightNoofGuest*$quant[$addiPriceId]*$addi_price;
        }
        elseif($addi_price_type=='per hire')
        {
          $addPriceSubTot_val = $quant[$addiPriceId]*$addi_price;
        }
        elseif($addi_price_type=='per day')
        {
          $addPriceSubTot_val = $quant[$addiPriceId]*$rightNoofDates*$addi_price;
        }

        $subtotal += $addPriceSubTot_val;
        $rightGrandTot +=$addPriceSubTot_val;
        $addiPriceArray[$addiPriceId]=array('qty'=>$quant[$addiPriceId],'price'=>$addi_price,'price_type'=>$addi_price_type,'subTotal'=>$addPriceSubTot_val);
      }
    }

    if(count($addiPriceCountArray) > 0)
    {
      $additional_sub_total = $subtotal;
      $additional_prices=json_encode($addiPriceArray);
    }
    else
    {
      $additional_sub_total = 0;
      $additional_prices='';
    }

    if($this->input->post('self_drive')=='1')
    {

      $self_drive = 1;
      $dataArr = array('added_by_user'=>'1',
              'user_id'=> $user_id,
              'driver_name'=>$this->input->post('driver_name'),
              'age' => $this->input->post('age'),
              'contact_num' => $this->input->post('contact_num'),
              'email' => $this->input->post('email'),
              'insurance_num' => $this->input->post('insurance_num'),
              'license_num' => $this->input->post('license_num'),
              'license_expiry_month' => $this->input->post('license_expiry_month'),
              'license_expiry_year' => $this->input->post('license_expiry_year')
            );

      $this->mobile_model->simple_insert(DRIVER_MASTER, $dataArr);
      $driver_id = $this->db->insert_id();
    }
    else
    {
      $self_drive = 0;
      $driver_id=$this->input->post('driver_id');
    }

    $bookingDetails = $this->mobile_model->get_all_details(VEHICLE_ENQUIRY, array('Bookingno' => $this->input->post('Bookingno')));
    $vehicle_type=$this->input->post('base_id');
    $message = $this->input->post('message');
    if($vehicle_type==4) { $subj='Car'; } else { $subj='Van'; } 
    $links = ''.base_url().'vehicle_trips/upcoming/'.$vehicle_type.'';

    $dataArr = array('rental_type'=>$vehicle_type, 'productId' => $bookingDetails->row()->vehicle_id, 'bookingNo' => $bookingDetails->row()->Bookingno, 'senderId' => $bookingDetails->row()->user_id, 'receiverId' => $bookingDetails->row()->renter_id, 'subject' => $subj.' Booking Request : ' . $bookingDetails->row()->Bookingno, 'message' => $message, 'currencycode' => $bookingDetails->row()->currencycode);
    $this->mobile_model->simple_insert(MED_MESSAGE, $dataArr);

    $this->mobile_model->update_details(VEHICLE_ENQUIRY, array('additional_sub_total'=>$additional_sub_total,'additional_prices'=>$additional_prices,'self_drive'=>$self_drive,'driver_id'=>$driver_id,'totalAmt'=>$rightGrandTot,'booking_status' => 'Pending','approval'=>'Accept', 'caltophone' => $this->input->post('phone_no')), array('user_id' => $user_id, 'id' => $enquiry_id));

    $condition=" WHERE id=".$bookingDetails->row()->user_id;
    $TheGuest=$this->mobile_model->get_selected_fields_records("firstname,lastname,email",USERS,$condition);

    
    $conditionProp=" WHERE id=".$bookingDetails->row()->vehicle_id;
    $TheProperty=$this->mobile_model->get_selected_fields_records("veh_title,veh_title_ph",VEHICLE,$conditionProp);

    /* Mail function start */


    $newsid = '23';
    $template_values = $this->user_model->get_newsletter_template_details($newsid);

    if ($template_values ['sender_name'] == '' && $template_values ['sender_email'] == '') {
        $sender_email = $this->config->item('site_contact_mail');
        $sender_name = $this->config->item('email_title');
    } else {
        $sender_name = $template_values ['sender_name'];
        $sender_email = $template_values ['sender_email'];
    }

    $email_values = array('mail_type' => 'html', 'from_mail_id' => $sender_email, 'mail_name' => $sender_name, 'to_mail_id' => $TheGuest->row()->email, 'subject_message' => $template_values ['news_subject'], 'body_messages' => $message);

    $Approval_info = array('email_title' => $sender_name, 'logo' => $this->data ['logo'], 'links' => $links,'rental_type' => $vehicle_type, 'travelername' => $TheGuest->row()->firstname . "  " . $TheGuest->row()->lastname, 'propertyname' => $TheProperty->row()->veh_title);

    $message = $this->load->view('newsletter/Host Approve Reservation' . $newsid . '.php', $Approval_info, TRUE);
    //send mail
    $this->load->library('email');
    $this->email->from($email_values['from_mail_id'], $sender_name);
    $this->email->to($email_values['to_mail_id']);
    $this->email->subject($email_values['subject_message']);
    $this->email->set_mailtype("html");
    $this->email->message($message);
    try {
        $this->email->send();
    } catch (Exception $e) {
        //echo $e->getMessage();
    }

    $dataArr = array('rental_type'=>$vehicle_type,'productId' => $bookingDetails->row()->vehicle_id, 'senderId' => $bookingDetails->row()->renter_id, 'receiverId' => $bookingDetails->row()->user_id, 'bookingNo' => $bookingDetails->row()->Bookingno, 'subject' => $subj.' Booking Request : ' . $bookingDetails->row()->Bookingno, 'message' => 'Accepted', 'point' => '1', 'status' => 'Accept');

    $this->db->insert(MED_MESSAGE, $dataArr);
    $this->db->where('bookingNo', $bookingDetails->row()->Bookingno);
    $this->db->update(MED_MESSAGE, array('status' => 'Accept'));

    $newdata = array('approval' => 'Accept');
    $condition = array('Bookingno' => $bookingDetails->row()->Bookingno);
    $this->mobile_model->update_details(VEHICLE_ENQUIRY, $newdata, $condition);
    $bookingDetails = $this->mobile_model->get_all_details(VEHICLE_ENQUIRY, $condition);

    if($currency_code != 'USD')
    {
        $payable_amounts = currency_conversion($currency_code,'USD',$rightGrandTot,$currency_cron_id); 
    }
    else
    {
       $payable_amounts = $rightGrandTot;
    }

    $jsonReturn = array("status"=>1,"message"=>$this->succ,"enquiryid"=>$enquiry_id,"payable_paypal_currency"=>$this->paypal_curency,"payable_paypal_symbol"=>$this->paypal_symbol,"payable_paypal_total" => $payable_amounts);
        echo json_encode($jsonReturn,JSON_PRETTY_PRINT);
    exit;


  /* Mail Function End */

  }


  public function vehicles_list()
  {

    $vehicle_type = $_POST['base_id'];

    $language_code = $_POST['lang_code'];

    $currency_code = $_POST['currency_code']; 

    $user_id = $_POST['user_id'];

    $getted_city = $_POST['city'];

    $type_of_booking = $_POST['type_of_booking'];

    $checkIn = $_POST['checkIn'];

    $checkOut = $_POST['checkOut'];

    $checkIn_time = $_POST['checkIn_time'];

    $checkOut_time = $_POST['checkOut_time'];

    $guests = $_POST['guests'];

    $vehicle_id = $_POST['vehicle_id'];

    $vehicleType = $_POST['base_id'];

    $listvalues = $_POST['listvalues'];

    $pricemin = $_POST['price_min'];

    $pricemax = $_POST['price_max'];   

    $page=intval($_POST['page_Id']);

    

    $driver_type = $this->input->post('driver_type');



    $priceDet = $this->db->where('id', $vehicle_id)->get(VEHICLE)->row();

    $minhourPrice = $priceDet->min_hour_price;

    $hourlyPrice = $priceDet->min_hour_exprice;

    $dayPrice = $priceDet->day_price;



    // echo $dayPrice;exit();

    $weeklyPrice = $priceDet->weekly_price;

    $monthlyPrice = $priceDet->monthly_price;



    $min_hour = $priceDet->min_hour;

    $min_hour_price = $priceDet->min_hour_price;

    $min_hour_exprice = $priceDet->min_hour_exprice;

    



  



    $booking_type = $this->input->post('booking_type');

    $json_encode = json_encode(array("status"=>1,"message"=>$this->veh_avail),JSON_PRETTY_PRINT);

  //     echo $json_encode;

  //     exit();

        $returnStr ['status_code'] = 1;

    $last_cron_id = $this->db->select('*')->from('fc_currency_cron')->order_by('curren_id','desc')->limit(1)->get();

   

   





      if($booking_type=='daily')

    {



       $DiffHrs = $this->input->post('DiffHrs');

      $datetime1 = new DateTime(date('d M Y',strtotime($checkIn)));

      $datetime2 = new DateTime(date('d M Y',strtotime($checkOut)));

      $interval = $datetime1->diff($datetime2);



  $NoOfDays = $this->getDatesFromRange(date('Y-m-d', strtotime($checkIn)), date('Y-m-d', strtotime($checkOut)));



       $dateCheck = $this->mobile_model->get_all_details(VEHICLE_BOOKING_DATES, array('vehicle_id' => $vehicle_id));

   

      if ($dateCheck->num_rows() > 0) {



        foreach ($dateCheck->result() as $dateCheckStr) {

       

          if (in_array($dateCheckStr->the_date, $NoOfDays)) {

            

            $json_encode = json_encode(array("status"=>0,"message"=>$this->veh_avail),JSON_PRETTY_PRINT);

            echo $json_encode;

            exit();

          }

        }

      }

   

      $years = $interval->format('%y');

      $months = $interval->format('%m');

      $weeks = 0;

      $days = $interval->format('%d');

      if($years > 0)

      {

        $months +=$years*12;

      }

      if($days >= 7)

      {

        $weeks += (int)($days/7);

        $days = $days-($weeks*7);

      }

      $numberOfDays = '';



      if($months > 0 )

      {

        $numberOfDays .= $months;

        if ($this->lang->line('months_text') != '') { $months_text = stripslashes($this->lang->line('months_text')); } else { $months_text = 'Months'; }

        if ($this->lang->line('month_text') != '') { $month_text = stripslashes($this->lang->line('month_text')); } else { $months_text = 'Month'; }

        if($months > 1) { $numberOfDays .= ' '.$months_text.', '; } else { $numberOfDays .= ' '.$month_text.', '; }

      } 

      if($weeks > 0 )

      {

        

        $numberOfDays .= $weeks;

        if ($this->lang->line('weeks_text') != '') { $weeks_text = stripslashes($this->lang->line('weeks_text')); } else { $weeks_text = 'Weeks'; }

        if ($this->lang->line('week_text') != '') { $week_text = stripslashes($this->lang->line('week_text')); } else { $week_text = 'Week'; }

        if($weeks > 1) { $numberOfDays .= ' '.$weeks_text.', '; } else { $numberOfDays .= ' '.$week_text.', '; }

      }

      if($days > 0 )

      {

        

        $numberOfDays .= $days;

        if ($this->lang->line('days_text') != '') { $days_text = stripslashes($this->lang->line('days_text')); } else { $days_text = 'Days'; }

        if ($this->lang->line('day_text') != '') { $day_text = stripslashes($this->lang->line('day_text')); } else { $day_text = 'Day'; }

        if($days > 1) { $numberOfDays .= ' '.$days_text; } else { $numberOfDays .= ' '.$day_text; }

      }

      $this->data['no_of_months']=$months;

      $this->data['no_of_weeks']=$weeks;

      $this->data['no_of_days']=$days;



      $days=$days+1;

     





      

      $subTotMonthPrice = $subTotWeekPrice = $subTotDayPrice = $subTotHourPrice = 0;

      if($months > 0)

      {

        $subTotMonthPrice = $months*$monthlyPrice;

      }

      if($weeks > 0)

      {

        $subTotWeekPrice = $weeks*$weeklyPrice;

      }

      if($days > 0)

      {

        $subTotDayPrice = $days*$dayPrice;

      }

      $DateCalCul = $subTotMonthPrice+$subTotWeekPrice+$subTotDayPrice;



     

      

      if($vehicle_type == 4) { $seo_tag = 'car-booking-daily'; }

      if($vehicle_type == 5) { $seo_tag = 'van-booking-daily'; }





       $service_tax_query = 'SELECT * FROM ' . COMMISSION . ' WHERE seo_tag="'.$seo_tag.'" AND status="Active"';

        $service_tax = $this->mobile_model->ExecuteQuery($service_tax_query);

       // print_r($service_tax->result());exit();

        if ($service_tax->num_rows() == 0) {

            $taxValue = '0.00';

            $taxString = '0.00';

        } else {

            $commissionType = $service_tax->row()->promotion_type;

            $commissionValue = $service_tax->row()->commission_percentage;

            if ($service_tax->row()->promotion_type == 'flat') {

                $basecurrencyCode = $this->db->where('default_currency', 'Yes')->get(CURRENCY)->row();

                $currency_code = $basecurrencyCode->currency_type;

        

                if ($currency_code != $currencyCode) {  

          $rate = changeCurrency($currency_code,$currencyCode,$service_tax->row()->commission_percentage);

          } else {

                    $rate = $service_tax->row()->commission_percentage;

                }

        

        if ($currency_code != $currency_code) {  

          $rateDisplay = changeCurrency($currencyCode,$currency_code,$service_tax->row()->commission_percentage);

                } else {

                    $rateDisplay = $service_tax->row()->commission_percentage;

                }

        

                $taxValue = $rate; //for saving in DB in Prd Currency

                $taxString = $rateDisplay; //for displaying in siteCur

            } else {

                $finalTax = ($service_tax->row()->commission_percentage * $DateCalCul) / 100;

                $taxValue = $finalTax;

                $taxString = $finalTax;

                $currencyCode = $this->db->where('id',$vehicle_id)->get(VEHICLE)->row()->currency;

                if ($currencyCode != $currency_code) {

          $taxString = changeCurrency($currencyCode,$currency_code,$taxString);

                } elseif ($currencyCode == $currency_code) {

                    $taxString = $finalTax;

                }

            }

        }

       

    //echo $this->data['taxString']; exit;

        $this->data['total_nights'] = $numberOfDays;

    $this->data['total_days']=count($NoOfDays);

        $this->data['product_id'] = $vehicle_id;





    if ($currency_code != $currency_code) {  

      $this->data['subTotal'] = changeCurrency($currencyCode,$currency_code,$DateCalCul);

    } else {

      $this->data['subTotal'] = $DateCalCul;

    }



        $currencyCode = $this->db->where('id', $vehicle_id)->get(VEHICLE)->row()->currency;

        $curren_code= $this->db->where('id', $vehicle_id)->get(VEHICLE)->row()->currency;

        $securityDepositestart = $this->db->where('id', $vehicle_id)->get(VEHICLE)->row()->security_deposit;

        $pay_option = $this->mobile_model->get_all_details(VEHICLE, array('id' => $vehicle_id));



        if(count($pay_option)>0) {

      foreach($pay_option->result() as $pro) {

        

          $carvalueArr = array();

          $pay_option  = $pro->instant_book;

          $cancel_percentage_is = $pro->cancellation_percentage;



        }

      }

       

        $instant_pay = $this->mobile_model->get_all_details(MODULES_MASTER, array('module_name' => 'payment_option')); 

          if(count($instant_pay)>0) {

      foreach($instant_pay->result() as $pro) {

        

          

          $instant_pay  = $pro->status;



        }

      }

              

        if ($currencyCode != $currency_code) {

      $securityDeposite_string = changeCurrency($currencyCode,$currency_code,$securityDepositestart);

        } elseif ($currencyCode == $currency_code) {

            $securityDeposite_string = $securityDepositestart;

        }

        if ($currencyCode != $currency_code) {

      $total_value = changeCurrency($currencyCode,$currency_code,$DateCalCul);

        } elseif ($currencyCode == $currency_code) {

            $total_value = $DateCalCul;

        }

     $basecurrencyCode = $this->db->where('default_currency', 'Yes')->get(CURRENCY)->row();

         $currency_code = $basecurrencyCode->currency_type;

     

      if ($service_tax->row()->promotion_type == 'flat') {

         $serviceFeeInPrdCurrency=changeCurrency($currency_code,$currencyCode,$service_tax->row()->commission_percentage);

       }else{

          $serviceFeeInPrdCurrency=$service_tax->row()->commission_percentage * $DateCalCul / 100;

       }

      $net_total_value = ($total_value) + ($taxString) + ($securityDeposite_string);



        if ($currencyCode != $currency_code) {

      $net_total_string = $net_total_value;

        } elseif ($currencyCode == $currency_code) {

            $net_total_string = $net_total_value;

        }

    $returnStr ['text'] = "can";

    $checkIn=date('Y-m-d H:i:s', strtotime($checkIn));

      $checkOut=date('Y-m-d H:i:s', strtotime($checkOut));



    }



     else

    {

      $DiffHrs=$this->input->post('DiffHrs');

      $days = 0;

      $months = 0;

      $weeks = 0;

 



     

      // else{

      //   $returnStr ['status_code'] = '';

      // }



    $startDate=date('Y-m-d H:i',strtotime($checkIn_time));



    $startDate_ref=date('Y-m-d',strtotime($start));



    $time_ref_start=date('H:i',strtotime($start));

    //echo $startDate.'/'.$startDate_ref.'/'.$time_ref_start.'<br>';

    // $split_dateTo = explode(' GMT',$checkOut_time);

    // $end=trim($split_dateTo[0]);



    $endDate=date('Y-m-d H:i',strtotime("+60 minutes" . $end));



    $endDate_ref=date('Y-m-d',strtotime($checkOut_time));





    $time_ref_end=date('H:i',strtotime($end));

    $data_start=$this->disable_date_time($vehicle_id,$startDate_ref,2);

    $data_end=$this->disable_date_time($vehicle_id,$endDate_ref,2);





     $betweendays=$this->db->select('*')->from(VEHICLE_BOOKING_DATES)->where('(tot_checked_in BETWEEN "'. $startDate_ref. '" and "'. $endDate_ref.'" OR tot_checked_out BETWEEN "'. $startDate_ref. '" and "'. $endDate_ref.'")')->where('vehicle_id = '. $vehicle_id)->get();

      $num=$betweendays->num_rows();

      // if ($num > 0 )

      // {

      //   $json_encode = json_encode(array("status"=>0,"message"=>"Already Booked"),JSON_PRETTY_PRINT);

      //       echo $json_encode;

      //       exit();

      // }

// echo "string";exit();

    // echo $startDate_ref;

    // echo $endDate_ref;exit();

    $CalendarDateArr = $this->getDatesFromRange($startDate_ref,$endDate_ref);





    $dateval = implode(',', $CalendarDateArr);



     $checkin=date('Y-m-d H:i:s', strtotime($checkIn_time));

    $checkout=date('Y-m-d H:i:s', strtotime($checkOut_time));



 //echo $checkin.'<br>'.$checkout;exit();

    $checkIn = $checkin;

    $checkOut = $checkout;

       

    $array_exist=array();

    $data_arr=json_decode($data_end);

    $data_arr_end=$data_arr->avail_dates;

    $unavail_dates=$data_arr->unavail_dates;



    //print_r($unavail_dates); echo '<hr>'; print_r($data_arr_end); exit;



    if($startDate_ref==$endDate_ref){

      foreach($data_arr_end as $key=>$val){

        foreach($val as $time ){

          if(is_array($time)){

            foreach($time as $time_val){

              $time_limit=explode('-', $time_val);

              //print_r($time_limit);

              if(isset($time_limit[0]))

              $time_start = str_replace('.', ':', $time_limit[0]);



              if(isset($time_limit[1]))

              $time_end = str_replace('.', ':', $time_limit[1]);



              $time_ref_end=str_replace('.', ':', $time_ref_end);

              $time_ref_start=str_replace('.', ':', $time_ref_start);



              if((($time_ref_start <= $time_start) && ($time_start <= $time_ref_end)) || (($time_ref_start <= $time_end) && ($time_end <= $time_ref_end))){

                $array_exist[]=1;

                break;

              }

            }

          }

        }

      }



    }else{



      //multiple days

      if(!empty($unavail_dates)){

        $datesbetween=$this->createRange($startDate_ref, $endDate_ref, $format = 'Y-m-d');

        $common_fields=array_intersect($datesbetween, $unavail_dates);

        if(!empty($common_fields)){

          $array_exist[]=1;

        }

      }

    }



    if(count($array_exist)>0){

       $json_encode = json_encode(array("message" => $this->sts_not_avail,"base_id" => $vehicle_type),JSON_PRETTY_PRINT);

      echo $json_encode;

      exit();

    }



   



    $begin = new DateTime($startDate);

    $end = new DateTime($endDate);

    $daterange = new DatePeriod($begin, new DateInterval('PT60M'), $end); 



    $productvalues=$this->mobile_model->get_all_details(VEHICLE_SCHEDULE,array('id'=>$vehicle_id,'vehicle_type'=>$vehicle_type));

    $productvalues_decode=json_decode($productvalues->result()[0]->data,TRUE); //as Array



    $i=0;

    $Price=0;

    $DateCalCul=0;

    foreach($productvalues_decode as $pp=>$kk) {

      

      foreach($daterange as $date){

        

        $dt=$date->format("Y-m-d");

        $time=$date->format("H:i");

        foreach($kk as $key=>$val){

          if($dt==$pp ){

            if($time==$key){

              if($i!=0){

                if($val['price']!='' && $val['price']!='0' && $val['price']!='0.00'){

                  $Price=$Price+$val['price'];

                }else{

                  $Price=$Price+$PricePost;

                }

              }

              $i=$i+1;

            }

          }

        }

      }

    }



    if($Price==0){

      $PriceN=$PricePost;

    }else{

      $PriceN=$Price;

    } 





    $CalendarDateArr = explode(',',$dateval);









    foreach($CalendarDateArr as $CalendarDateRow){

      $result[] =  trim($CalendarDateRow);

    }

    $this->data['ScheduleDatePrice'] = $this->mobile_model->get_all_details(VEHICLE_SCHEDULE,array('id'=>$vehicle_id,'vehicle_type'=>$vehicle_type));





      

    if($DiffHrs <= $min_hour)

    {

      $DateCalCul = $min_hour_price;

    }

    else

    {

      $excessHours = $DiffHrs-$min_hour;

      $excessHourAmt = $excessHours*$min_hour_exprice;

      $minhour_price = $min_hour_price;

      $DateCalCul = $excessHourAmt+$minhour_price;

      

    }



 

$pay_option = $this->mobile_model->get_all_details(VEHICLE, array('id' => $vehicle_id));



        if(count($pay_option)>0) {

      foreach($pay_option->result() as $pro) {

        

          $carvalueArr = array();

          $pay_option  = $pro->instant_book;

          $cancel_percentage_is = $pro->cancellation_percentage;



        }

      }



    $currencyCode = $this->db->where('id',$vehicle_id)->get(VEHICLE)->row()->currency;

    if($currencyCode != $currency_code)

    {

      $converted_total_value = convertCurrency($currencyCode,$currency_code,$DateCalCul);

    }

    elseif($currencyCode == $currency_code){

      $converted_total_value = $DateCalCul;

    }

    if($vehicle_type == 4){ $seo_tag= 'car-booking-hourly'; }

    if($vehicle_type == 5){ $seo_tag= 'van-booking-hourly'; }

    $service_tax_query='SELECT * FROM '.COMMISSION.' WHERE seo_tag="'.$seo_tag.'" AND status="Active"';

    $service_tax=$this->mobile_model->ExecuteQuery($service_tax_query);

    if($service_tax->num_rows() == 0)

    {

      $taxValue = '0.00';

      $taxString = 'No Tax';

    }

    else 

    {

      $commissionType = $service_tax->row()->promotion_type;

      $commissionValue = $service_tax->row()->commission_percentage;

      if($service_tax->row()->promotion_type=='flat')

      {

        $currencyCode     = $this->db->where('id',$vehicle_id)->get(VEHICLE)->row()->currency;

        $currInto_result = $this->db->where('currency_type',$currencyCode)->get(CURRENCY)->row();



        $rate = $service_tax->row()->commission_percentage * $currInto_result->currency_rate;

        $taxValue = $rate;

        $taxString = $rate;

        $currencyCode = $this->db->where('id',$vehicle_id)->get(VEHICLE)->row()->currency;

        if($currencyCode != $currency_code)

        {

          $taxString = convertCurrency($currencyCode,$currency_code,$taxString);

        }

        elseif($currencyCode == $currency_code){

          $taxString= $rate;

        }



      }

      else

      {    

        $finalTax = ( $service_tax->row()->commission_percentage * $converted_total_value)/100;



        $taxValue = $finalTax;

        $taxString = $finalTax;



        $currencyCode = $this->db->where('id',$vehicle_id)->get(VEHICLE)->row()->currency;



        if($currencyCode != $currency_code)

        {

          $taxString1 = convertCurrency($currencyCode,$currency_code,$taxString);

        }

        elseif($currencyCode == $currency_code){

          $taxString = $finalTax;

        }



      }

    }



      



    $this->data['total_nights'] = count($result);

    $this->data['total_hours'] = $DiffHrs;

    $this->data['product_id'] = $vehicle_id;

    $this->data['subTotal'] = $converted_total_value;



     



    $currencyCode = $this->db->where('id',$vehicle_id)->get(VEHICLE)->row()->currency;

    $currencycd = $this->db->where('id',$vehicle_id)->get(VEHICLE)->row()->currency;



    $securityDepositestart = $this->db->where('id',$vehicle_id)->get(VEHICLE)->row()->security_deposit;



    if($currencyCode != $currency_code)

    {

      $securityDeposite = convertCurrency($currencyCode,$currency_code,$securityDepositestart);    

    }

    elseif($currencyCode == $currency_code)

    {

      $securityDeposite= $securityDepositestart;

    }



    if($currencyCode != $currency_code)

    {

      $total_value = convertCurrency($currencyCode,$currency_code,$DateCalCul);

    }

    elseif($currencyCode == $currency_code){

      $total_value = $DateCalCul;

    }



    



    $net_total_value = $total_value + $taxString + $securityDeposite;

    if($currencyCode != $currency_code)

    {

      $net_total_string = convertCurrency($currencyCode,$currency_code,$net_total_value);

      // $net_total_string = $net_total_value;

    }

    elseif($currencyCode == $currency_code){

      $net_total_string = $net_total_value;

    }

    }

    

    

       

     //   if ($returnStr ['status_code'] != 10) {

          

      //$user_currencyCode = trim($this->input->post('user_currencyCode'));

      $currencyPerUnitSeller = 1;

      $getLastBookingNo= $this->db->select('Bookingno')->order_by('id','DESC')->get(VEHICLE_ENQUIRY)->row()->Bookingno;

      $renter= $this->db->select('user_id')->get(VEHICLE)->row()->user_id;

      if($getLastBookingNo=='')

      {

        $val = 1500000;

      }

      else

      {

        $val = $getLastBookingNo;

        $val = str_replace('VE','',$val);

        $val = $val+1;

      }

      if($driver_type == 'self_drive'){

        $self_drive =0;



        $dataArr = array('added_by_user'=>'1',

              'user_id'=> $user_id,

              'driver_name'=>$this->input->post('driver_name'),

              'age' => $this->input->post('age'),

              'contact_num' => $this->input->post('contact_num'),

              'email' => $this->input->post('email'),

              'insurance_num' => $this->input->post('insurance_num'),

              'license_num' => $this->input->post('license_num'),

              'license_expiry_month' => $this->input->post('license_expiry_month'),

              'license_expiry_year' => $this->input->post('license_expiry_year')

            );



      $this->mobile_model->simple_insert(DRIVER_MASTER, $dataArr);

      $drive_id_is = $this->db->insert_id();

        //echo $drive_id_is;exit();

      }

      else{

        $self_drive =1;

        $priceDet = $this->db->where('id', $vehicle_id)->get(VEHICLE)->row();

        $drive_id_is = $priceDet->driver_id;

      }





      

      $bookingno = "VE" . $val;

            $dataArr = array(

        'vehicle_type' => $vehicle_type,

        'user_id' => $user_id,

        'vehicle_id' => $vehicle_id,

                'checkin_date' => $checkIn,

                'checkout_date' => $checkOut,

        'NoofGuest' => $guests,

        'renter_id' => $renter,

        'booking_type' => $booking_type,

                //'Enquiry' => 0,

                'numofdates' => $days,

                'no_of_hours' => $DiffHrs,

                'no_of_months' => $months,

                'no_of_weeks' => $weeks,

                'no_of_days' => $days,

                'caltophone' => 0,

                //'enquiry_timezone' => 0, 

                'totalAmt' => $net_total_value,

                 'self_drive' => $self_drive,

                 'driver_id' => $drive_id_is,

                'cancel_percentage' => $cancel_percentage_is,

                'currencycode' => $currency_code,

                'secDeposit' => $securityDepositestart,

                'serviceFee' => $taxValue,

                'subTotal' => $DateCalCul,

                'user_currencyCode' => $currency_code,

                'walletAmount' => 0,

                'currencyPerUnitSeller' => $currencyPerUnitSeller,

                //'unitPerCurrencyUser' => $unitPerCurrencyUser,

                'dateAdded' => date('Y-m-d h:i:s'),

        'Bookingno' => $bookingno,

                'choosed_option' => $this->input->post('choosed_option'),

        'currency_cron_id'=>$last_cron_id->row()->curren_id

            );



//print_r($dataArr);exit();

             // $json_encode = json_encode(array("status"=>1,"message" => "Data Available","base_id"=> $vehicle_type,"no_of_months"=>$months,"no_of_weeks"=>$weeks,"no_of_days"=>$days,"commissionType"=>$commissionType,"commissionValue"=>$commissionValue,"taxValue"=>$taxValue,"taxString"=>$taxString,"total_nights"=>$numberOfDays,"total_days"=>count($NoOfDays),"product_id"=>$id,"subTotal"=>$DateCalCul,"currencycd"=>$curren_code,"securityDeposite" => $securityDepositestart,"pay_option" => $pay_option,"instant_pay" => $instant_pay,"securityDeposite_string" => $securityDeposite_string,"net_total_value"=>$net_total_value,"requestType"=>$requestType),JSON_PRETTY_PRINT);

            $booking_status = array('booking_status' => 'Pending','approval' => 'Accept');

            $dataArr1 = array_merge($dataArr, $booking_status);



           

            $this->db->insert(VEHICLE_ENQUIRY, $dataArr1);









            $insertid = $this->db->insert_id();







            //message



             $bookingDetails = $this->mobile_model->get_all_details(VEHICLE_ENQUIRY, array('Bookingno' => $bookingno));

   

        $message = $this->input->post('message');

    if($vehicle_type==4) { $subj='Car'; } else { $subj='Van'; } 

    $links = ''.base_url().'vehicle_trips/upcoming/'.$vehicle_type.'';

        $dataArr = array('rental_type'=>$vehicle_type, 'productId' => $bookingDetails->row()->vehicle_id, 'bookingNo' => $bookingDetails->row()->Bookingno, 'senderId' => $bookingDetails->row()->user_id, 'receiverId' => $bookingDetails->row()->renter_id, 'subject' => $subj.' Booking Request : ' . $bookingDetails->row()->Bookingno, 'message' => $message, 'currencycode' => $bookingDetails->row()->currencycode);

        $this->mobile_model->simple_insert(MED_MESSAGE, $dataArr);

       

    

    $condition=" WHERE id=".$bookingDetails->row()->user_id;

    $TheGuest=$this->mobile_model->get_selected_fields_records("firstname,lastname,email",USERS,$condition);

    

    $conditionProp=" WHERE id=".$bookingDetails->row()->vehicle_id;

    $TheProperty=$this->mobile_model->get_selected_fields_records("veh_title,veh_title_ph",VEHICLE,$conditionProp);

    

        /* Mail function start */



     $newsid = '23';

            $template_values = $this->user_model->get_newsletter_template_details($newsid);

            if ($template_values ['sender_name'] == '' && $template_values ['sender_email'] == '') {

                $sender_email = $this->config->item('site_contact_mail');

                $sender_name = $this->config->item('email_title');

            } else {

                $sender_name = $template_values ['sender_name'];

                $sender_email = $template_values ['sender_email'];

            }

      

            $email_values = array('mail_type' => 'html', 'from_mail_id' => $sender_email, 'mail_name' => $sender_name, 'to_mail_id' => $TheGuest->row()->email, 'subject_message' => $template_values ['news_subject'], 'body_messages' => $message);

            $Approval_info = array('email_title' => $sender_name, 'logo' => $this->data ['logo'], 'links' => $links,'rental_type' => $vehicle_type, 'travelername' => $TheGuest->row()->firstname . "  " . $TheGuest->row()->lastname, 'propertyname' => $TheProperty->row()->veh_title);

            $message = $this->load->view('newsletter/Host Approve Reservation' . $newsid . '.php', $Approval_info, TRUE);

            //send mail

            $this->load->library('email');

            $this->email->from($email_values['from_mail_id'], $sender_name);

            $this->email->to($email_values['to_mail_id']);

            $this->email->subject($email_values['subject_message']);

            $this->email->set_mailtype("html");

            $this->email->message($message);

            try {

                $this->email->send();

            } catch (Exception $e) {

                echo $e->getMessage();

            }

    

        /* Mail Function End */



        $dataArr = array('rental_type'=>$vehicle_type,'productId' => $bookingDetails->row()->vehicle_id, 'senderId' => $bookingDetails->row()->renter_id, 'receiverId' => $bookingDetails->row()->user_id, 'bookingNo' => $bookingDetails->row()->Bookingno, 'subject' => $subj.' Booking Request : ' . $bookingDetails->row()->Bookingno, 'message' => 'Accepted', 'point' => '1', 'status' => 'Accept');

        $this->db->insert(MED_MESSAGE, $dataArr);

        $this->db->where('bookingNo', $bookingDetails->row()->Bookingno);

        $this->db->update(MED_MESSAGE, array('status' => 'Accept'));

        // $newdata = array('approval' => 'Accept');

        // $condition = array('Bookingno' => $bookingDetails->row()->Bookingno);

        // $this->user_model->update_details(VEHICLE_ENQUIRY, $newdata, $condition);

        // $bookingDetails = $this->user_model->get_all_details(VEHICLE_ENQUIRY, $condition);

        // $enqId = $bookingDetails->row()->id;

           



           //end of message

           // $this->session->set_userdata('EnquiryId', $insertid);

            

      //  }

       // echo json_encode($returnStr);







      $car = array();

      $conditions = array('status'=>'Active','vehicle_type'=>$_POST['base_id']);

      $car_space = $this->mobile_model->get_all_details(MAKE_MASTER, $conditions);

      if(count($car_space)>0) {

        foreach($car_space->result() as $pro) {      

          $carvalueArr = array();   

          if($language_code == 'en')

          {

              $rentalNameField=$pro->make_name;

          }

          else

          {

              $titleNameField='make_name_ph';

              if($pro->$titleNameField == '') { 

                  $rentalNameField=$pro->make_name;

              }

              else{

                  $rentalNameField=$pro->$titleNameField;

              }

          }    

          $carvalueArr[] = array("child_id" =>$pro->id,"child_name"=>$rentalNameField,"parent_name"=>"Make","parent_id"=>$pro->vehicle_type);        

          $car[]  = array("base_id"=>$pro->vehicle_type,"option_name"=>"Make","options"=>$carvalueArr);

        }

      }



      $car_model = array();

      $conditions = array('status'=>'Active','vehicle_type'=>$_POST['base_id']);

      $car_modal_space = $this->mobile_model->get_all_details(MODEL_MASTER, $conditions);

      if(count($car_modal_space)>0) {

        foreach($car_modal_space->result() as $modalpro) {          

            $carvalueArr = array();  

            if($language_code == 'en')

            {

                $modelNameField=$modalpro->model_name;

            }

            else

            {

                $titleNameField='model_name_ph';

                if($modalpro->$titleNameField == '') { 

                    $modelNameField=$modalpro->model_name;

                }

                else{

                    $modelNameField=$modalpro->$titleNameField;

                }

            }             

            $carvalueArr[] = array("child_id" =>$modalpro->id,"child_name"=>$modelNameField,"parent_name"=>"Model","parent_id"=>$modalpro->vehicle_type);            

            $car_model[]  = array("base_id"=>$modalpro->vehicle_type,"option_name"=>"Model","options"=>$carvalueArr);

        }

      }



      $car_type = array();

      $conditions = array('status'=>'Active','vehicle_type'=>$_POST['base_id']);

      $car_type_space = $this->mobile_model->get_all_details(TYPE_MASTER, $conditions);

      if(count($car_type_space)>0) {

        foreach($car_type_space->result() as $typepro) {          

            $carvalueArr = array();

            if($language_code == 'en')

            {

                $typeNameField=$typepro->type_name;

            }

            else

            {

                $titleNameField='type_name_ph';

                if($typepro->$titleNameField == '') { 

                    $typeNameField=$typepro->type_name;

                }

                else{

                    $typeNameField=$typepro->$titleNameField;

                }

            }            

            $carvalueArr[] = array("child_id" =>$typepro->id,"child_name"=>$typeNameField,"parent_name"=>"Type","parent_id"=>$typepro->vehicle_type);                          

            $car_type[]  = array("base_id"=>$modalpro->vehicle_type,"option_name"=>"Type","options"=>$carvalueArr);

          }

      }



        //driver

     

           $host_id = $user_id;

      $driverDetails_arr = $this->mobile_model->get_all_details(DRIVER_MASTER, array('host_id'=>$host_id));



//$conditions = array('status'=>'Active','vehicle_type'=>$_POST['base_id']);

    //$driverDetails_arr = $this->mobile_model->get_all_details(TYPE_MASTER, $conditions);

    if(count($driverDetails_arr)>0) {

      foreach($driverDetails_arr->result() as $typepro) {

        

          $drivervalueArr = array();

         //if($_POST['lang_code'] == 'en'){

              $drivervalueArr[] = array("child_id" =>$typepro->id,"child_name"=>$typepro->type_name,"host_id"=>$typepro->host_id,"driver_name"=>$typepro->driver_name,"age "=>$typepro->age,"contact_num"=>$typepro->contact_num,"email"=>$typepro->email,"insurance_num"=>$typepro->insurance_num,"license_num"=>$typepro->license_num,"license_expiry_year"=>$typepro->license_expiry_year,"license_expiry_month"=>$typepro->license_expiry_month,"parent_name"=>"Drivers","parent_id"=>$typepro->vehicle_type);

            

          

          $Driver_details[]  = array("base_id"=>$modalpro->vehicle_type,"option_name"=>"Drivers","options"=>$drivervalueArr);

        }

      }



      $attribute = array();

      $parent_select_qry = "select id,attribute_name,attribute_name_ph,status from fc_attribute where status='Active' AND rental_type = '".$vehicle_type."'";

      $parent_list_values = $this->mobile_model->ExecuteQuery($parent_select_qry);



      /* Features of amenties,extras ,wifi and so on */

      if($parent_list_values->num_rows()>0) 

      {

        foreach($parent_list_values->result() as $parent_value) 

        {

          $select_qrys = "select fc_list_values.id,list_value,list_value_ph,list_id,fc_attribute.id as attr_id,attribute_name,attribute_name_ph,image from fc_list_values left join fc_attribute  on fc_attribute.id = fc_list_values.list_id where fc_list_values.status='Active' and fc_list_values.rental_type = '".$vehicle_type."' and list_id = ".$parent_value->id;

          $list_values = $this->mobile_model->ExecuteQuery($select_qrys);

          if($list_values->num_rows()>0) 

          {

            $listvalueArr = array();



            if ($language_code == 'en')

            {

              $parent_attribute_name  = $parent_value->attribute_name;

            }

            else

            {

              $attribute_name_parent  = 'attribute_name_ph';

              if($parent_value->$attribute_name_parent == '') { 

                  $parent_attribute_name=$parent_value->attribute_name;

              }

              else{

                  $parent_attribute_name=$parent_value->$attribute_name_parent;

              }

            }



            foreach($list_values->result() as $list_value) 

            {

              if($parent_value->id == $list_value->list_id) 

              {

                if($language_code == 'en')

                {

                    $field_list_value=$list_value->list_value;

                    $field_attribute_name  = $list_value->attribute_name;

                }

                else

                {

                    $list_value_Field='list_value_ph';

                    if($list_value->$list_value_Field == '') { 

                        $field_list_value=$list_value->list_value;

                    }

                    else{

                        $field_list_value=$list_value->$list_value_Field;

                    }



                    $attribute_name_field  = 'attribute_name_ph';

                    if($list_value->$attribute_name_field == '') { 

                        $field_attribute_name=$list_value->attribute_name;

                    }

                    else{

                        $field_attribute_name=$list_value->$attribute_name_field;

                    }

                }



                $listvalueArr[] = array("child_id" =>$list_value->id,"child_name"=>$field_list_value,"child_image"=>base_url()."images/attribute/".$list_value->image,"parent_name"=>$field_attribute_name,"parent_id"=>$list_value->attr_id);

              }

            }

            $attribute[]  = array("option_id"=>$parent_value->id,"option_name"=>$parent_attribute_name,"options"=>$listvalueArr);

          } 

        }

      }



      $roombedVal=array();

      $roombedVal1=array();

      $select_qrys = "select * from fc_listing_types WHERE status='Active' and rental_type = '".$vehicle_type."'";

      $listing_values = $this->mobile_model->ExecuteQuery($select_qrys);

      $property_attributes = array();

      if($listing_values->num_rows()>0)

      {

        foreach($listing_values->result() as $listing_parent)

        {

          if($language_code == 'en')

          {

              $field_name=$listing_parent->name;

              $field_labelname  = $listing_parent->labelname;

          }

          else

          {

              $name_Field='name_ph';

              if($listing_parent->$name_Field == '') { 

                  $field_name=$listing_parent->name;

              }

              else{

                  $field_name=$listing_parent->$name_Field;

              }



              $labelname_Field='labelname_ph';

              if($listing_parent->$labelname_Field == '') { 

                  $field_labelname=$listing_parent->labelname;

              }

              else{

                  $field_labelname=$listing_parent->$labelname_Field;

              }

          }



          $listing_id = $listing_parent->id;

          $listing_name = $field_name;

          $listing_type = $listing_parent->type;

          $listing_labelname = $field_labelname;

          $listing_useproperty_attribute = (($listing_parent->name=='accommodates')?true:false); 

    

          $select_qryy = "select * from fc_listing_child where parent_id=".$listing_id." and status=0 and rental_type = '".$vehicle_type."' order by child_name ASC";

          $list_valuesy = $this->mobile_model->ExecuteQuery($select_qryy);

    

          $property_child_attributes = array();

      

          if($list_valuesy->num_rows()>0)

          {

            if($listing_type=="option") 

            {

              foreach($list_valuesy->result() as $listing_child)

              {

        

                $listing_child_id = $listing_child->id;

                $listing_child_name = $listing_child->child_name;

              

                $property_child_attributes[] = array("attribute_child_id"=>intval($listing_child_id),"attribute_parent_name"=>$listing_name,"attribute_child_value"=>$listing_child_name);

              }

            }

          }



          if($listing_type=="option" && $list_valuesy->num_rows()==0) {

          }

        

        }

      }



      $json_encode = json_encode(array("status"=>1,"message"=>$this->succ_bkd,"EnquiryId" => $insertid,"Cars"=>$car,"Models"=>$car_model,"Driver_details" => $Driver_details, "Types"=>$car_type, "attribute"=>$attribute,"other_attributes" => $roombedVal),JSON_PRETTY_PRINT);

      echo $json_encode;

      exit();

    

    

  }



  



  public function ajaxdateCalculate()

    {

   

    $id=$this->input->post('vehicle_id');

    $vehicle_type=$this->input->post('base_id');

    $booking_type = $this->input->post('booking_type'); 

    $priceDet = $this->db->where('id', $id)->get(VEHICLE)->row();

  

    $minhourPrice = $priceDet->min_hour_price;

    $hourlyPrice = $priceDet->min_hour_exprice;

    $dayPrice = $priceDet->day_price;

    $weeklyPrice = $priceDet->weekly_price;

    $monthlyPrice = $priceDet->monthly_price;

    $currencyCode = $priceDet->currency;

   

   // $vehicle_type = $priceDet->vehicle_type;

    

        $currency_result = $this->input->post('currency_code');

        $checkIn = $this->input->post('checkIn');

        $checkOut = $this->input->post('checkOut');

    $NoOfDays = $this->getDatesFromRange(date('Y-m-d', strtotime($checkIn)), date('Y-m-d', strtotime($checkOut)));
    

    $stop_date = date('Y-m-d', strtotime($checkOut . ' +1 day'));

    if($booking_type=='daily')

    {

      $datetime1 = new DateTime(date('d M Y',strtotime($checkIn)));

      $datetime2 = new DateTime(date('d M Y',strtotime($stop_date)));

      $interval = $datetime1->diff($datetime2);   

      $years = $interval->format('%y');

      $months = $interval->format('%m');

      $weeks = 0;

      $days = $interval->format('%d');

      if($years > 0)
      {

        $months +=$years*12;

      }

      if($days >= 7)
      {

        $weeks += (int)($days/7);

        $days = $days-($weeks*7);

      }

      $numberOfDays = '';



      if($months > 0 )
      {

        $numberOfDays .= $months;

        if ($this->lang->line('months_text') != '') { $months_text = stripslashes($this->lang->line('months_text')); } else { $months_text = 'Months'; }

        if ($this->lang->line('month_text') != '') { $month_text = stripslashes($this->lang->line('month_text')); } else { $months_text = 'Month'; }

        if($months > 1) { $numberOfDays .= ' '.$months_text.', '; } else { $numberOfDays .= ' '.$month_text.', '; }

      } 

      if($weeks > 0 )
      {

        

        $numberOfDays .= $weeks;

        if ($this->lang->line('weeks_text') != '') { $weeks_text = stripslashes($this->lang->line('weeks_text')); } else { $weeks_text = 'Weeks'; }

        if ($this->lang->line('week_text') != '') { $week_text = stripslashes($this->lang->line('week_text')); } else { $week_text = 'Week'; }

        if($weeks > 1) { $numberOfDays .= ' '.$weeks_text.', '; } else { $numberOfDays .= ' '.$week_text.', '; }

      }

      if($days > 0 )
      {

        $numberOfDays .= $days;

        if ($this->lang->line('days_text') != '') { $days_text = stripslashes($this->lang->line('days_text')); } else { $days_text = 'Days'; }

        if ($this->lang->line('day_text') != '') { $day_text = stripslashes($this->lang->line('day_text')); } else { $day_text = 'Day'; }

        if($days > 1) { $numberOfDays .= ' '.$days_text; } else { $numberOfDays .= ' '.$day_text; }

      }

      $this->data['no_of_months']=$months;

      $this->data['no_of_weeks']=$weeks;

      $this->data['no_of_days']=$days;





      

      $subTotMonthPrice = $subTotWeekPrice = $subTotDayPrice = $subTotHourPrice = 0;

      if($months > 0)

      {

        $subTotMonthPrice = $months*$monthlyPrice;

      }

      if($weeks > 0)

      {

        $subTotWeekPrice = $weeks*$weeklyPrice;

      }

      if($days > 0)

      {

        $subTotDayPrice = $days*$dayPrice;

      }

      $DateCalCul = $subTotMonthPrice+$subTotWeekPrice+$subTotDayPrice;

      

      if($vehicle_type == 4) { $seo_tag = 'car-booking-daily'; }

      if($vehicle_type == 5) { $seo_tag = 'van-booking-daily'; }

    }

    

    

        $service_tax_query = 'SELECT * FROM ' . COMMISSION . ' WHERE seo_tag="'.$seo_tag.'" AND status="Active"';

        $service_tax = $this->mobile_model->ExecuteQuery($service_tax_query);

       // print_r($service_tax->result());exit();

        if ($service_tax->num_rows() == 0) {

            $taxValue = '0.00';

            $taxString = '0.00';

        } else {

            $commissionType = $service_tax->row()->promotion_type;

            $commissionValue = $service_tax->row()->commission_percentage;

            if ($service_tax->row()->promotion_type == 'flat') {

                $basecurrencyCode = $this->db->where('default_currency', 'Yes')->get(CURRENCY)->row();

                $currency_code = $basecurrencyCode->currency_type;

        

                if ($currency_code != $currencyCode) {  

          $rate = changeCurrency($currency_code,$currencyCode,$service_tax->row()->commission_percentage);

          } else {

                    $rate = $service_tax->row()->commission_percentage;

                }

        

        if ($currency_code != $currency_result) {  

          $rateDisplay = changeCurrency($currencyCode,$currency_result,$service_tax->row()->commission_percentage);

                } else {

                    $rateDisplay = $service_tax->row()->commission_percentage;

                }

        

                $taxValue = $rate; //for saving in DB in Prd Currency

                $taxString = $rateDisplay; //for displaying in siteCur

            } else {

                $finalTax = ($service_tax->row()->commission_percentage * $DateCalCul) / 100;

                $taxValue = $finalTax;

                $taxString = $finalTax;

                $currencyCode = $this->db->where('id', $id)->get(VEHICLE)->row()->currency;

                if ($currencyCode != $currency_result) {

          $taxString = changeCurrency($currencyCode,$currency_result,$taxString);

                } elseif ($currencyCode == $currency_result) {

                    $taxString = $finalTax;

                }

            }

        }

       

    //echo $this->data['taxString']; exit;

        $this->data['total_nights'] = $numberOfDays;

    $this->data['total_days']=count($NoOfDays);

        $this->data['product_id'] = $id;





    if ($currency_code != $currency_result) {  

      $this->data['subTotal'] = changeCurrency($currencyCode,$currency_result,$DateCalCul);

    } else {

      $this->data['subTotal'] = $DateCalCul;

    }



        $currencyCode = $this->db->where('id', $id)->get(VEHICLE)->row()->currency;

        $curren_code= $this->db->where('id', $id)->get(VEHICLE)->row()->currency;

        $securityDepositestart = $this->db->where('id', $id)->get(VEHICLE)->row()->security_deposit;

        $pay_option = $this->mobile_model->get_all_details(VEHICLE, array('id' => $id));



        if(count($pay_option)>0) {

      foreach($pay_option->result() as $pro) {

        

          $carvalueArr = array();

          $pay_option  = $pro->instant_book;



        }

      }

       

        $instant_pay = $this->mobile_model->get_all_details(MODULES_MASTER, array('module_name' => 'payment_option')); 

          if(count($instant_pay)>0) {

      foreach($instant_pay->result() as $pro) {

        

          

          $instant_pay  = $pro->status;



        }

      }

              

        if ($currencyCode != $currency_result) {

      $securityDeposite_string = changeCurrency($currencyCode,$currency_result,$securityDepositestart);

        } elseif ($currencyCode == $currency_result) {

            $securityDeposite_string = $securityDepositestart;

        }

        if ($currencyCode != $currency_result) {

      $total_value = changeCurrency($currencyCode,$currency_result,$DateCalCul);

        } elseif ($currencyCode == $currency_result) {

            $total_value = $DateCalCul;

        }

     $basecurrencyCode = $this->db->where('default_currency', 'Yes')->get(CURRENCY)->row();

         $currency_code = $basecurrencyCode->currency_type;

     

      if ($service_tax->row()->promotion_type == 'flat') {

         $serviceFeeInPrdCurrency=changeCurrency($currency_code,$currencyCode,$service_tax->row()->commission_percentage);

       }else{

          $serviceFeeInPrdCurrency=$service_tax->row()->commission_percentage * $DateCalCul / 100;

       }

      $net_total_value = ($total_value) + ($taxString) + ($securityDeposite_string);



        if ($currencyCode != $currency_result) {

      $net_total_string = $net_total_value;

        } elseif ($currencyCode == $currency_result) {

            $net_total_string = $net_total_value;

        }

        $requestType = 'booking_request';

    $currentCurrency = $this->mobile_model->get_all_details(CURRENCY, array('currency_type' => $currency_result));
    $currency_symbols = $currentCurrency->row()->currency_symbols;

    $json_encode = json_encode(array("status"=>1,"message" =>$this->dta_found,"base_id"=> $vehicle_type,"no_of_months"=>$months,"no_of_weeks"=>$weeks,"no_of_days"=>$days,"no_of_hours"=>0,"total_days"=>count($NoOfDays),"service_fee"=>$taxString,"vehicle_id"=>$id,"subTotal"=>$DateCalCul,"currency_code"=>$curren_code,"secDeposite" => $securityDeposite_string,"total_amount"=>$net_total_value,"currency_symbols"=>$currency_symbols),JSON_PRETTY_PRINT);

    echo $json_encode;

  }



function ajaxHourCalculate(){

   $currency_result = $this->input->post('currency_code');

    $id=$this->input->post('vehicle_id');

    $vehicle_type=$this->input->post('base_id');

    $vehicle_det = $this->mobile_model->get_all_details(VEHICLE,array('id'=>$id,'vehicle_type'=>$vehicle_type));

    $min_hour = $vehicle_det->row()->min_hour;

    $min_hour_price = $vehicle_det->row()->min_hour_price;

    $min_hour_exprice = $vehicle_det->row()->min_hour_exprice;

    $DiffHrs=$this->input->post('DiffHrs');

    $datefrom=$this->input->post('checkIn');

    $dateto=$this->input->post('checkOut');
    

    $split_dateFrom = explode(' GMT',$datefrom);

    $start=trim($split_dateFrom[0]);

    $startDate=date('Y-m-d H:i',strtotime($start));

    $startDate_ref=date('Y-m-d',strtotime($start));

    $time_ref_start=date('H:i',strtotime($start));

    //echo $startDate.'/'.$startDate_ref.'/'.$time_ref_start.'<br>';

    $split_dateTo = explode(' GMT',$dateto);

    $end=trim($split_dateTo[0]);

    $endDate=date('Y-m-d H:i',strtotime("+60 minutes" . $end));

    $endDate_ref=date('Y-m-d',strtotime($end));

    $time_ref_end=date('H:i',strtotime($end));

    $data_start=$this->disable_date_time($id,$startDate_ref,2);

    $data_end=$this->disable_date_time($id,$endDate_ref,2);

    $CalendarDateArr = $this->getDatesFromRange($startDate_ref,$endDate_ref);

    $dateval = implode(',', $CalendarDateArr);

    $array_exist=array();

    $data_arr=json_decode($data_end);

    $data_arr_end=$data_arr->avail_dates;

    $unavail_dates=$data_arr->unavail_dates;

    if($startDate_ref==$endDate_ref){

      foreach($data_arr_end as $key=>$val){

        foreach($val as $time ){

          if(is_array($time)){

            foreach($time as $time_val){

              $time_limit=explode('-', $time_val);

              //print_r($time_limit);

              if(isset($time_limit[0]))

              $time_start = str_replace('.', ':', $time_limit[0]);



              if(isset($time_limit[1]))

              $time_end = str_replace('.', ':', $time_limit[1]);



              $time_ref_end=str_replace('.', ':', $time_ref_end);

              $time_ref_start=str_replace('.', ':', $time_ref_start);



              if((($time_ref_start <= $time_start) && ($time_start <= $time_ref_end)) || (($time_ref_start <= $time_end) && ($time_end <= $time_ref_end))){

                $array_exist[]=1;

                break;

              }

            }

          }

        }

      }



    }else{

      //multiple days

      if(!empty($unavail_dates)){

        $datesbetween=$this->createRange($startDate_ref, $endDate_ref, $format = 'Y-m-d');

        $common_fields=array_intersect($datesbetween, $unavail_dates);

        if(!empty($common_fields)){

          $array_exist[]=1;

        }

      }

    }



    if(count($array_exist)>0){

       $json_encode = json_encode(array("message" => $this->this_slot_ntavail,"base_id" => $vehicle_type),JSON_PRETTY_PRINT);

      echo $json_encode;

      exit();

    }



   



    $begin = new DateTime($startDate);

    $end = new DateTime($endDate);

    $daterange = new DatePeriod($begin, new DateInterval('PT60M'), $end); 



    $productvalues=$this->mobile_model->get_all_details(VEHICLE_SCHEDULE,array('id'=>$id,'vehicle_type'=>$vehicle_type));

    $productvalues_decode=json_decode($productvalues->result()[0]->data,TRUE); //as Array



    $i=0;

    $Price=0;

    $DateCalCul=0;

    foreach($productvalues_decode as $pp=>$kk) {

      

      foreach($daterange as $date){

        

        $dt=$date->format("Y-m-d");

        $time=$date->format("H:i");

        foreach($kk as $key=>$val){

          if($dt==$pp ){

            if($time==$key){

              if($i!=0){

                if($val['price']!='' && $val['price']!='0' && $val['price']!='0.00'){

                  $Price=$Price+$val['price'];

                }else{

                  $Price=$Price+$PricePost;

                }

              }

              $i=$i+1;

            }

          }

        }

      }

    }



    if($Price==0){

      $PriceN=$PricePost;

    }else{

      $PriceN=$Price;

    } 





    $CalendarDateArr = explode(',',$dateval);









    foreach($CalendarDateArr as $CalendarDateRow){

      $result[] =  trim($CalendarDateRow);

    }

    $this->data['ScheduleDatePrice'] = $this->mobile_model->get_all_details(VEHICLE_SCHEDULE,array('id'=>$id,'vehicle_type'=>$vehicle_type));





      

    if($DiffHrs <= $min_hour)

    {

      $DateCalCul = $min_hour_price;

    }

    else

    {

      $excessHours = $DiffHrs-$min_hour;

      $excessHourAmt = $excessHours*$min_hour_exprice;

      $minhour_price = $min_hour_price;

      $DateCalCul = $excessHourAmt+$minhour_price;

      

    }



 





    $currencyCode = $this->db->where('id',$id)->get(VEHICLE)->row()->currency;

    if($currencyCode != $currency_result)

    {

      $converted_total_value = convertCurrency($currencyCode,$currency_result,$DateCalCul);

    }

    elseif($currencyCode == $currency_result){

      $converted_total_value = $DateCalCul;

    }

    if($vehicle_type == 4){ $seo_tag= 'car-booking-hourly'; }

    if($vehicle_type == 5){ $seo_tag= 'van-booking-hourly'; }

    $service_tax_query='SELECT * FROM '.COMMISSION.' WHERE seo_tag="'.$seo_tag.'" AND status="Active"';

    $service_tax=$this->mobile_model->ExecuteQuery($service_tax_query);

    if($service_tax->num_rows() == 0)

    {

      $taxValue = '0.00';

      $taxString = 'No Tax';

    }

    else 

    {

      $commissionType = $service_tax->row()->promotion_type;

      $commissionValue = $service_tax->row()->commission_percentage;

      if($service_tax->row()->promotion_type=='flat')

      {

        $currencyCode     = $this->db->where('id',$id)->get(VEHICLE)->row()->currency;

        $currInto_result = $this->db->where('currency_type',$currencyCode)->get(CURRENCY)->row();



        $rate = $service_tax->row()->commission_percentage * $currInto_result->currency_rate;

        $taxValue = $rate;

        $taxString = $rate;

        $currencyCode = $this->db->where('id',$id)->get(VEHICLE)->row()->currency;

        if($currencyCode != $currency_result)

        {

          $taxString = convertCurrency($currencyCode,$currency_result,$taxString);

        }

        elseif($currencyCode == $currency_result){

          $taxString= $rate;

        }



      }

      else

      {    

        $finalTax = ( $service_tax->row()->commission_percentage * $converted_total_value)/100;



        $taxValue = $finalTax;

        $taxString = $finalTax;



        $currencyCode = $this->db->where('id',$id)->get(VEHICLE)->row()->currency;



        if($currencyCode != $currency_result)

        {

          $taxString1 = convertCurrency($currencyCode,$currency_result,$taxString);

        }

        elseif($currencyCode == $currency_result){

          $taxString = $finalTax;

        }



      }

    }



      



    $this->data['total_nights'] = count($result);

    $this->data['total_hours'] = $DiffHrs;

    $this->data['product_id'] = $id;

    $this->data['subTotal'] = $converted_total_value;

    $currencyCode = $this->db->where('id',$id)->get(VEHICLE)->row()->currency;


    $securityDepositestart = $this->db->where('id',$id)->get(VEHICLE)->row()->security_deposit;



    if($currencyCode != $currency_result)

    {

      $securityDeposite = convertCurrency($currencyCode,$currency_result,$securityDepositestart);    

    }

    elseif($currencyCode == $currency_result)

    {

      $securityDeposite= $securityDepositestart;

    }



    if($currencyCode != $currency_result)

    {

      $total_value = convertCurrency($currencyCode,$currency_result,$DateCalCul);

    }

    elseif($currencyCode == $currency_result){

      $total_value = $DateCalCul;

    }



    



    $net_total_value = $total_value + $taxString + $securityDeposite;

    /*if($currencyCode != $currency_result)

    {

      $net_total_string = convertCurrency($currencyCode,$currency_result,$net_total_value);


    }

    elseif($currencyCode == $currency_result){

      $net_total_string = $net_total_value;

    }*/

    $currentCurrency = $this->mobile_model->get_all_details(CURRENCY, array('currency_type' => $currency_result));
    $currency_symbols = $currentCurrency->row()->currency_symbols;

    $requestType = 'booking_request';

    $json_encode = json_encode(array("status"=>1,"message" => $this->dta_found,"base_id"=> $vehicle_type,"service_fee"=>$taxString,"no_of_months"=>"","no_of_weeks"=>"","no_of_hours"=>$DiffHrs,"total_days"=>count($result),"vehicle_id"=>$id,"subTotal"=>$converted_total_value,"currency_code"=>$currency_result,"secDeposite" => $securityDeposite,"total_amount"=> $net_total_value,"currency_symbols"=>$currency_symbols),JSON_PRETTY_PRINT);

    echo $json_encode;

  exit();

  }

  

    function getDatesFromRange($start, $end)

    {

        $dates = array($start);

        while (end($dates) < $end) {

            $dates [] = date('Y-m-d', strtotime(end($dates) . ' +1 day'));

        }

        return $dates;

    }



    public function disable_date_time($id='',$date='',$manage_list='',$vehicle_type=''){

   

    if($id==''){

      $id=$this->input->post('id');

      $date=$this->input->post('startdate');

    }

    if($vehicle_type=='')

    {

      $vehicle_type = $this->input->post('base_id');

    }
/*echo $id.'-'.$date.'-'.$manage_list.'-'.$vehicle_type; exit();*/
    $dateeee=array();

    if($date==''){

      $condition=array('vehicle_id'=>$id,'vehicle_type'=>$vehicle_type,'checked_in<>'=>'');

    }else{

      $condition=array('vehicle_id'=>$id,'the_date'=>$date,'vehicle_type'=>$vehicle_type,'checked_in<>'=>'');

    }



    $posted_date=$date;

    //echo 'posted date'.$posted_date; exit;

    $ff=array(); 

    /*GET UNAVAILABLE DATES */

    //DAILY BOOKING

    $myCondition = array_merge($condition,array('checked_in'=>'00:00','checked_out'=>'23:00'));

    $unAvail_daily=$this->mobile_model->get_all_details(VEHICLE_BOOKING_DATES,$myCondition);

    $unavailArray = array();

    $unAvailDate = array();

    if($unAvail_daily->num_rows() > 0 )

    {

      foreach($unAvail_daily->result() as $unavail_date)

      {

        $unavailArray[] =date('Y-n-d',strtotime($unavail_date->the_date));

        $unAvailDate[] =$unavail_date->id;

      }

    }

    //GET HOURLY BOOKING DATES

    $unAvailDates=array();

    $query = $this->db->select('the_date');

    if(count($unAvailDate) > 0 )

    {

      $this->db->where_not_in('id', $unAvailDate);

    }

    $this->db->where('vehicle_id', $id);

    $this->db->where('vehicle_type', $vehicle_type);

    $this->db->where('checked_in<>', '');

    $this->db->group_by('the_date');

    $datesQuery = $this->db->get(VEHICLE_BOOKING_DATES);

    if($datesQuery->num_rows() > 0)

    {

      foreach($datesQuery->result() as $dates)

      {

        if(!in_array($unAvailDates,$dates->the_date))

        {

          $unAvailDates[] =date('Y-m-d',strtotime($dates->the_date));

        }

      }

    }

    //CHECK HOURLY BOOKING DATE IS FILLED WITH 23 HOURS. IF FILLED APPEND THE DATE

    if(count($unAvailDates) > 0 )

    {

      foreach($unAvailDates as $unavaildateeach)

      {

        $timeQuery = $this->db->query("SELECT SUM(TIMESTAMPDIFF(HOUR, b.`tot_checked_in`, b.`tot_checked_out`)) AS `HOURS`  FROM ".VEHICLE_BOOKING_DATES." b where vehicle_id='".$id."' AND  the_date='".$unavaildateeach."' GROUP BY vehicle_id");

        if($timeQuery->num_rows() > 0 )

        {

          foreach($timeQuery->result() as $tq)

          {

            if($tq->HOURS >= 23)

            {

              array_push($unavailArray,date('Y-n-d',strtotime($unavaildateeach)));

            }

          }

        }

      }

    }     

    /*EOF GET UNAVAILABLE DATES */ 

    $res=$this->mobile_model->get_all_details(VEHICLE_BOOKING_DATES,$condition);

  

    if($res->num_rows() > 0) 
    {

      $datee = '';

      $stime = '';

      $etime = '';

      $timee = '';

      $wholedt = '';

      $dayArr1=array();

      $dayArr2=array();

      $id_state = array();

      foreach($res->result()  as $date)
      {

        $id_state[] = $date->id_state;

        $start=$date->tot_checked_in;

        $startDate=date('Y-m-d',strtotime($start));

        $startTime=date('H:i',strtotime($start));

        $end=$date->tot_checked_out;

        $endDate=date('Y-m-d',strtotime($end));

        $endTime=date('H:i',strtotime($end));

        $datesbetween=$this->createRange($startDate, $endDate, $format = 'Y-m-d');

        /*foreach($datesbetween as $day)
        {

          if($posted_date!='')
          {

            if($day==$posted_date)
            {

              if(count($datesbetween) == 1 )
              { 

                $datee= '"'.$day.'"' ;

                $timee= $startTime.'' . '-' . ''.$endTime;

              }else{

                if ($day==$startDate){



                  $datee = '"'.$day.'"' ;

                  $timee= $startTime.'' .  '-' . ''.'23:00' ;

                }else if ($day==$endDate){

                  $datee = '"'.$day.'"' ;

                  $timee='00:00'.'' .  '-' . ''.$endTime ;

                }

                else{

                  $datee = '"'.$day.'"' ; 

                  $timee='00:00'.'' .  '-' . ''.'23:00';

                } 

              } 

            }

          }else{

            if(count($datesbetween) == 1 ){ 

              $datee= '"'.$day.'"' ;

              $timee= $startTime.'' . '-' . ''.$endTime;

            }else{

              if ($day==$startDate){

                $datee = '"'.$day.'"' ;

                $timee= $startTime.'' .  '-' . ''.'23:00' ;

              }else if ($day==$endDate){

                $datee = '"'.$day.'"' ;

                $timee='00:00'.'' .  '-' . ''.$endTime ;

              }

              else{

                $datee = '"'.$day.'"' ; 

                $timee='00:00'.'' .  '-' . ''.'23:00';

              } 

            } 

          }


          if($datee!='' && $timee!=''){

            $value=array();

            if(in_array($datee, array_column($dateeee, 'date'))) { // search value in the array

              $key = array_search($datee, array_column($dateeee, 'date'));

              $value = $dateeee[$key]['time'];

              if(!in_array($timee, $value))

              $value[]=$timee;

              $dateeee[$key]=array('date'=>$datee,'time'=>$value,"state"=>$id_state);  

            }else{

              $dateeee[]=array('date'=>$datee,'time'=>array($timee),"state"=>$id_state);

            }
          }
        }*/

        foreach($datesbetween as $day)
        {

          if($posted_date!='')
          {

            if($day==$posted_date)
            {

              if(count($datesbetween) == 1 )
              { 

                $datee= ''.$day.'' ;

                $timee= $startTime.'' . '-' . ''.$endTime;

              }else{

                if ($day==$startDate){



                  $datee = ''.$day.'' ;

                  $timee= $startTime.'' .  '-' . ''.'23:00' ;

                }else if ($day==$endDate){

                  $datee = ''.$day.'' ;

                  $timee='00:00'.'' .  '-' . ''.$endTime ;

                }

                else{

                  $datee = ''.$day.'' ; 

                  $timee='00:00'.'' .  '-' . ''.'23:00';

                } 

              } 

            }

          }else{

            if(count($datesbetween) == 1 ){ 

              $datee= ''.$day.'' ;

              $timee= $startTime.'' . '-' . ''.$endTime;

            }else{

              if ($day==$startDate){

                $datee = ''.$day.'' ;

                $timee= $startTime.'' .  '-' . ''.'23:00' ;

              }else if ($day==$endDate){

                $datee = ''.$day.'' ;

                $timee='00:00'.'' .  '-' . ''.$endTime ;

              }

              else{

                $datee = ''.$day.'' ; 

                $timee='00:00'.'' .  '-' . ''.'23:00';

              } 

            } 

          }


          if($datee!='' && $timee!=''){

            $value=array();

            if(in_array($datee, array_column($dateeee, 'date'))) { // search value in the array

              $key = array_search($datee, array_column($dateeee, 'date'));

              $value = $dateeee[$key]['time'];

              if(!in_array($timee, $value))

              $value[]=$timee;

              $dateeee[$key]=array('date'=>$datee,'time'=>$value,"state"=>$id_state);  

            }else{

              $dateeee[]=array('date'=>$datee,'time'=>array($timee),"state"=>$id_state);

            }
          }
        }
      }
    }

    if($manage_list==1){

      return(json_encode($dateeee));

    }else if($manage_list==2){

      if(!empty($ff)){

        $value=array();

        foreach($ff as $va){



          if(in_array($va['date'], array_column($dateeee, 'date'))) {

            $key = array_search($va['date'], array_column($dateeee, 'date'));

            $value = $dateeee[$key]['time'];//

            //echo $value;

            $two_time=array_merge($value,$va['time']);

            //$value=$va['time'];

            $dateeee[$key]=array('date'=>$va['date'],'time'=>$two_time);  

          }else{

            $dateeee[]=array('date'=>$va['date'],'time'=>$va['time']);

          }

        }

      }

      $date_and_time=array('avail_dates'=>$dateeee,'unavail_dates'=>$unavailArray);

      return json_encode($date_and_time);



    }else{

      if(!empty($ff)){

        $value=array();

        foreach($ff as $va){



          if(in_array($va['date'], array_column($dateeee, 'date'))) { // search value in the array

            $key = array_search($va['date'], array_column($dateeee, 'date'));

            $value = $dateeee[$key]['time'];//

            //echo $value;

            $two_time=array_merge($value,$va['time']);

            //$value=$va['time'];

            $dateeee[$key]=array('date'=>$va['date'],'time'=>$two_time);  

          }else{

            $dateeee[]=array('date'=>$va['date'],'time'=>$va['time']);

          }

        }

      }

      $date_and_time=array('avail_dates'=>$dateeee,'unavail_dates'=>$unavailArray);

      echo json_encode($date_and_time);

    } 

    

  }







    public function PaymentCredit()

  {

    //echo "string";exit();

    /*print_r($this->input->post()); exit;Array ( [vehicle_type] => 4 [booking_type] => hourly [total_price] => 4.09 [currencycode] => INR [booking_rental_id] => 16 [enquiryid] => 42 [creditvalue] => authorize [user_currencycode] => USD [currency_cron_id] => 129 [cardType] => Visa [cardNumber] => 4242424242424242 [CCExpMonth] => 12 [CCExpYear] => 2018 [creditCardIdentifier] => 123 ) */



      $vehicle_id=$this->input->post('vehicle_id');

    $vehicle_type=$this->input->post('base_id');

    $booking_type = $this->input->post('booking_type'); 

     $user_id = $this->input->post('user_id');

$couponUsedAmount = $walletUsedAmount = '0.00';



    $priceDet = $this->db->where('id', $vehicle_id)->get(VEHICLE)->row();

    $enqryDet = $this->db->where('vehicle_id', $vehicle_id)->get(VEHICLE_ENQUIRY)->row();

    

   

    $booking_type = $this->input->post('booking_type');

    $condition = array('id' => $user_id);

    $userDetails = $this->mobile_model->get_all_details(USERS, $condition);

    $currency_code = $this->input->post('currency_code');

    $user_currencycode = $priceDet->currency;

    if ($this->input->post('creditvalue') == 'authorize') {

      $Auth_Details = unserialize(API_LOGINID);

      $Auth_Setting_Details = unserialize($Auth_Details['settings']);

      error_reporting(-1);

      define("AUTHORIZENET_API_LOGIN_ID", $Auth_Setting_Details['merchantcode']);

      define("AUTHORIZENET_TRANSACTION_KEY", $Auth_Setting_Details['merchantkey']);

      define("API_MODE", $Auth_Setting_Details['mode']);

      if (API_MODE == 'sandbox') {

        define("AUTHORIZENET_SANDBOX", true);

      } else {

        define("AUTHORIZENET_SANDBOX", false);

      }

      define("TEST_REQUEST", "FALSE");

      require_once './authorize/autoload.php';

      $transaction = new AuthorizeNetAIM;

      $transaction->setSandbox(AUTHORIZENET_SANDBOX);

      $payable_amount = $this->input->post('total_price');

      if($this->input->post('currency_code') != 'USD'){

      $payable_amount = currency_conversion($this->input->post('currency_code'), 'USD', $this->input->post('total_price'),$enqryDet->currency_cron_id);

      }

     // echo $payable_amount;

      $transaction->setFields(array('amount' => $payable_amount, 'card_num' => $this->input->post('cardnumber'), 'exp_date' => $this->input->post('cc_exp_month') . '/' . $this->input->post('cc_exp_year'), 'first_name' => $userDetails->row()->firstname, 'last_name' => $userDetails->row()->lastname, 'address' => $this->input->post('address'), 'city' => $this->input->post('city'), 'state' => $this->input->post('state'), 'country' => $userDetails->row()->country, 'phone' => $userDetails->row()->phone_no, 'email' => $userDetails->row()->email, 'card_code' => $this->input->post('credit_card_identifier')));

      $response = $transaction->authorizeAndCapture();

     /*print_r($response->response_reason_text);exit();*/

      if ($response->approved != '') {

       

        $product = $this->mobile_model->get_all_details(VEHICLE, array('id' => $vehicle_id));

        if($booking_type=='hourly') { $indtotal = $product->row()->day_price; } else { $indtotal = $product->row()->min_hour_price;  }

        $totalAmnt = $this->input->post('total_price');

        $enquiryid = $this->input->post('enquiryid');

        $loginUserId = $user_id;

        if ($this->input->post('randomNo') != '') {

          $this -> db -> where('dealCodeNumber', $this->input->post('randomNo'));

          $this -> db -> where('user_id', $loginUserId);

          $this -> db -> delete(VEHICLE_PAYMENT);

          

          $dealCodeNumber = $this->input->post('randomNo');

        } else {

          $dealCodeNumber = mt_rand();  

        }

        $insertIds = array();

        $now = date("Y-m-d H:i:s");

        $paymentArr = array('vehicle_type'=>$vehicle_type, 'vehicle_id' => $vehicle_id, 'sell_id' => $product->row()->user_id, 'price' => $totalAmnt, 'indtotal' => $indtotal, 'sumtotal' => $totalAmnt, 'user_id' => $loginUserId, 'created' => $now, 'dealCodeNumber' => $dealCodeNumber, 'status' => 'Paid', 'shipping_status' => 'Pending', 'total' => $totalAmnt, 'EnquiryId' => $enquiryid, 'inserttime' => NOW(), 'currency_code' => $user_currencycode);

       

        $this->mobile_model->simple_insert(VEHICLE_PAYMENT, $paymentArr);

        $insertIds[] = $this->db->insert_id();

        $paymtdata = array('randomNo' => $dealCodeNumber, 'randomIds' => $insertIds);

        //$this->session->set_userdata($paymtdata, $currency_code);

        $this->mobile_model->edit_rentalbooking(array('booking_status' => 'Booked'), array('id' => $enquiryid));

        $lastFeatureInsertId = $dealCodeNumber;

        

        // redirect('vehicle_order/success/' . $loginUserId . '/' . $dealCodeNumber . '/' . $response->transaction_id);

        //invoicedata



        

                // if ($this->uri->segment(5) == '') {

                //     $transId = $_REQUEST['txn_id'];

                    

                // } else {

                //     $transId = $this->uri->segment(5);

                //     $Pray_Email = '';

                // }

$Pray_Email = $userDetails->row()->email;

                    $transId = $response->transaction_id;

                $UserNo = $user_id;

                $DealCodeNo = $dealCodeNumber;



                $EnquiryUpdate = $this->mobile_model->get_all_details(VEHICLE_PAYMENT, array('dealCodeNumber' => $DealCodeNo));

                $eprd_id = $EnquiryUpdate->row()->vehicle_id;

        $vehicle_type = $this->db->select('vehicle_type')->where('id',$eprd_id)->get(VEHICLE)->row()->vehicle_type;

        $this->data['rental_type']=$vehicle_type;

                $eInq_id = $EnquiryUpdate->row()->EnquiryId;

                $totalAmt = $EnquiryUpdate->row()->total;

                $this->data['paid_amount'] = $totalAmt;

                $couponses = $this->session->userdata('coupon_strip');

                $coupon = explode('-', $couponses);

        

                if ($coupon['0'] != '') {

                    $data = array('is_coupon_used' => 'Yes', 'discount_type' => $coupon['4'], 'coupon_code' => $coupon['0'], 'discount' => $coupon['2'], 'dval' => $coupon['1'], 'total_amt' => $coupon['9']  //from prdcurrency amount

                    );

                    //$this->session->unset_userdata('coupon_strip');

                    $this->session->unset_userdata(array('coupon_strip'));

                    $this->mobile_model->update_details(VEHICLE_PAYMENT, $data, array('dealCodeNumber' => $DealCodeNo));

                    $data = array('code' => trim($coupon['0']));

                    $couponi = $this->mobile_model->get_all_details(COUPONCARDS, $data);

                    $data = array('purchase_count' => $couponi->row()->purchase_count + 1, 'card_status' => 'redeemed');

                    $this->mobile_model->update_details(COUPONCARDS, $data, array('id' => $couponi->row()->id));

                }

                

                $this->data['invoicedata'] = $this->mobile_model->get_all_details(VEHICLE_ENQUIRY, array('id' => $eInq_id));

                $condition1 = array('user_id' => $UserNo, 'vehicle_id' => $eprd_id, 'id' => $eInq_id);

                $dataArr1 = array('booking_status' => 'Booked');

                $this->mobile_model->update_details(VEHICLE_ENQUIRY, $dataArr1, $condition1); // need to uncomment

//$this->data['Confirmation'] = $this->mobile_model->PaymentSuccess($user_id, $DealCodeNo, $transId, $Pray_Email);

                $SelBookingQty = $this->mobile_model->get_all_details(VEHICLE_ENQUIRY, array('id' => $eInq_id));

                $productId = $SelBookingQty->row()->vehicle_id;

                $arrival = $SelBookingQty->row()->checkin_date;

                $depature = $SelBookingQty->row()->checkout_date;

                $dates = $this->getDatesFromRange($arrival, $depature);

                $i = 1;

        $split_checkedin=explode(" ",$arrival);

        $checked_in=$split_checkedin[1];

        $split_checkedintime=explode(":",$checked_in);

        $checked_in=$split_checkedintime[0].':'.$split_checkedintime[1];

        

        $split_checkedout=explode(" ",$depature);

        $checked_out=$split_checkedout[1];

        $split_checkedouttime=explode(":",$checked_out);

        $checked_out=$split_checkedouttime[0].':'.$split_checkedouttime[1];

    

                $dateMinus1 = count($dates) - 1;

                foreach ($dates as $date) {

                    if ($i <= $dateMinus1) {

                        $BookingArr = $this->mobile_model->get_all_details(VEHICLE_BOOKING_DATES, array('vehicle_id' => $productId, 'id_state' => 1, 'id_item' => 1, 'the_date' => $date));

                        if ($BookingArr->num_rows() > 0) {

                        } else {

                            $dataArr = array(

                      'vehicle_type'=>$vehicle_type, 

                      'vehicle_id' => $productId, 

                      'id_state' => 1, 

                      'id_item' => 1, 

                      'the_date' => $date,

                      'id_booking'=>$eInq_id,

                      'checked_in' => $checked_in,

                      'checked_out' => $checked_out,

                      'tot_checked_in' => $arrival,

                      'tot_checked_out' => $depature,

                      'tot_time' => $arrival. ' - ' . $depature

                      );

                            $this->mobile_model->simple_insert(VEHICLE_BOOKING_DATES, $dataArr);

                        }

                    }

                    $i++;

                }

        //echo 'product Id'.$productId.'<br>';

                $DateArr = $this->mobile_model->get_all_details(VEHICLE_BOOKING_DATES, array('vehicle_id' => $productId));

        

                $dateDispalyRowCount = 0;

                $dateArrVAl = '';

        if($DateArr->num_rows() > 0){

          $dateArrVAl .='{';

          

          foreach($DateArr->result() as $dateDispalyRow){

            $time_details=$this->db->select('checked_in,checked_out')->where(array('the_date'=>$dateDispalyRow->the_date,'vehicle_id'=>$productId))->get(VEHICLE_BOOKING_DATES);

          

            if($time_details->num_rows() > 0)

            {

              $final_date_time_result="";

              foreach($time_details->result() as $date_time)

              {

                $checked_in=$date_time->checked_in;

                $checked_out=$date_time->checked_out;

                $final_date_time_result .= $checked_in.'-'.$checked_out.' ';

              }

              if($dateDispalyRowCount==0){

                $dateArrVAl .='"'.$dateDispalyRow->the_date.'":{"available":"1","bind":0,"info":"","notes":"","price":"'.$payable_amount.'","promo":"","status":"booked","checkedtime":"'.$final_date_time_result.'","prd_id":"'.$dateDispalyRow->vehicle_id.'"}';

              }else{

                $dateArrVAl .=',"'.$dateDispalyRow->the_date.'":{"available":"1","bind":0,"info":"","notes":"","price":"'.$payable_amount.'","promo":"","status":"booked","checkedtime":"'.$final_date_time_result.'","prd_id":"'.$dateDispalyRow->vehicle_id.'"}';

              }

              $final_date_time_result="";

              //$dateArrVAl="";

            }

            else

            {

              if($dateDispalyRowCount==0){

                $dateArrVAl .='"'.$dateDispalyRow->the_date.'":{"available":"1","bind":0,"info":"","notes":"","price":"'.$payable_amount.'","promo":"","status":"booked","checkedtime":"'.$dateDispalyRow->checked_in. '-'.$dateDispalyRow->checked_out.'","prd_id":"'.$dateDispalyRow->vehicle_id.'"}';

              }else{

                $dateArrVAl .=',"'.$dateDispalyRow->the_date.'":{"available":"1","bind":0,"info":"","notes":"","price":"'.$payable_amount.'","promo":"","status":"booked","checkedtime":"'.$dateDispalyRow->checked_in. '-'.$dateDispalyRow->checked_out.'","prd_id":"'.$dateDispalyRow->vehicle_id.'"}';

              }

            }

            



            $dateDispalyRowCount=$dateDispalyRowCount+1;

            

          }

          $dateArrVAl .='}';

        }

               

                $inputArr4 = array('id' => $productId, 'data' => trim($dateArrVAl));

                $this->mobile_model->update_details(VEHICLE_SCHEDULE, $inputArr4, array('id' => $productId));

        if($vehicle_type == 4) { $seo_tag = 'car-listing'; }

        if($vehicle_type == 5) { $seo_tag = 'van-listing'; }

                $condition = array('status' => 'Active', 'seo_tag' => $seo_tag);

                $service_tax_host = $this->mobile_model->get_all_details(COMMISSION, $condition);

                $host_tax_type = $service_tax_host->row()->promotion_type;

                $host_tax_value = $service_tax_host->row()->commission_percentage;

        if($booking_type == 'daily')

        {

          if($vehicle_type == 4) { $seo_tag = 'car-booking-daily'; }

          if($vehicle_type == 5) { $seo_tag = 'van-booking-daily'; }

        }

        else

        {

          if($vehicle_type == 4) { $seo_tag = 'car-booking-hourly'; }

          if($vehicle_type == 5) { $seo_tag = 'van-booking-hourly'; }

        }

                $condition = array('status' => 'Active', 'seo_tag' => $seo_tag);

                $service_tax_guest = $this->mobile_model->get_all_details(COMMISSION, $condition);

                $guest_tax_type = $service_tax_guest->row()->promotion_type;

                $guest_tax_value = $service_tax_guest->row()->commission_percentage;

                $orderDetails = $this->mobile_model->get_all_details(VEHICLE_ENQUIRY, array('id' => $eInq_id));

                $userDetails = $this->mobile_model->get_all_details(USERS, array('id' => $orderDetails->row()->renter_id));

                $guest_fee = $orderDetails->row()->serviceFee;

                $netAmount = $orderDetails->row()->totalAmt - $guest_fee;

                $host_fee = 0;

                $payable_amount = $netAmount - $host_fee;

        //echo $userDetails->row()->rep_code; exit;

                if ($userDetails->row()->rep_code != '') 

                {

                    $condition = array('status' => 'Active', 'seo_tag' => 'host-accept-rep');

                    $service_tax_guest = $this->mobile_model->get_all_details(COMMISSION, $condition);

                    $rep_host = $service_tax_guest->row()->promotion_type;

                    $rep_guest = $service_tax_guest->row()->commission_percentage;

                    $condition = array('status' => 'Active', 'seo_tag' => 'guest-accept-rep');

                    $service_tax_guest = $this->mobile_model->get_all_details(COMMISSION, $condition);

                    $rep_host_rep = $service_tax_guest->row()->promotion_type;

                    $rep_guest_rep = $service_tax_guest->row()->commission_percentage;

                    if ($rep_host == 'flat') {

                        $rep_fees = $rep_host;

                    } else {

                        $rep_all = $rep_guest / 100;

                        $rep_fees = ($netAmount * $host_tax_value) / 100;

                    }

                    $rep_fee = $netAmount * $rep_all;

                    $data2 = array('rental_type'=>$vehicle_type,'host_email' => $userDetails->row()->email, 'booking_no' => $orderDetails->row()->Bookingno, 'total_amount' => $orderDetails->row()->totalAmt, 'guest_fee' => $guest_fee, 'host_fee' => $rep_fees, 'rep_fee' => $rep_fee, 'code' => $userDetails->row()->rep_code, 'payable_amount' => $rep_fee);

          

                    $chkQry = $this->mobile_model->get_all_details(COMMISSION_REP_TRACKING, array('booking_no' => $orderDetails->row()->Bookingno,'rental_type'=>$vehicle_type));

                    if ($chkQry->num_rows() == 0) $this->mobile_model->simple_insert(COMMISSION_REP_TRACKING, $data2); else

                        $this->mobile_model->update_details(COMMISSION_REP_TRACKING, $data2, array('booking_no' => $orderDetails->row()->Bookingno,'rental_type'=>$vehicle_type));

                    $data1 = array('rental_type'=>$vehicle_type,'host_email' => $userDetails->row()->email, 'booking_no' => $orderDetails->row()->Bookingno, 'total_amount' => $EnquiryUpdate->row()->total, 'guest_fee' => $guest_fee, 'host_fee' => $host_fee, 'cancel_percentage' => $orderDetails->row()->cancel_percentage, 'subtotal' => $orderDetails->row()->subTotal, 'payable_amount' => $payable_amount, 'booking_walletUse' => $walletUsedAmount //malar - wallet amount used on payment

                    );

                    $chkQry = $this->mobile_model->get_all_details(COMMISSION_TRACKING, array('booking_no' => $orderDetails->row()->Bookingno,'rental_type'=>$vehicle_type));

                    if ($chkQry->num_rows() == 0) $this->mobile_model->simple_insert(COMMISSION_TRACKING, $data1); else

                        $this->mobile_model->update_details(COMMISSION_TRACKING, $data1, array('booking_no' => $orderDetails->row()->Bookingno,'rental_type'=>$vehicle_type));

            

            

                } else {

          

          

                    $data1 = array('rental_type'=>$vehicle_type, 'host_email' => $userDetails->row()->email, 'booking_no' => $orderDetails->row()->Bookingno, 'total_amount' => $orderDetails->row()->totalAmt, 'guest_fee' => $guest_fee, 'host_fee' => $host_fee, 'cancel_percentage' => $orderDetails->row()->cancel_percentage, 'subtotal' => $orderDetails->row()->subTotal, 'payable_amount' => $payable_amount, 'booking_walletUse' => $walletUsedAmount //malar - wallet amount used on payment

                    );

                    $chkQry = $this->mobile_model->get_all_details(COMMISSION_TRACKING, array('booking_no' => $orderDetails->row()->Bookingno,'rental_type'=>$vehicle_type));

          //echo $chkQry->num_rows().'<br>';print_r($data1); exit;

                    if ($chkQry->num_rows() == 0) $this->mobile_model->simple_insert(COMMISSION_TRACKING, $data1); else

                        $this->mobile_model->update_details(COMMISSION_TRACKING, $data1, array('booking_no' => $orderDetails->row()->Bookingno,'rental_type'=>$vehicle_type));

            

                }

        

      

                $this->booking_conform_mail($DealCodeNo);

              

         $json_encode = json_encode(array("status"=>1,"message" => $this->succ_paid,"base_id"=> $vehicle_type),JSON_PRETTY_PRINT);



      echo $json_encode;

exit();



      } else {

        $json_encode = json_encode(array("status"=>1,"message" => $this->payt_failed,"base_id"=> $vehicle_type),JSON_PRETTY_PRINT);



      echo $json_encode;

exit();

      }

    }

  }



  public function UserPaymentCreditStripe()

  {



     $vehicle_id=$this->input->post('vehicle_id');

    $vehicle_type=$this->input->post('base_id');

     

     $user_id = $this->input->post('user_id');

    $couponUsedAmount = $walletUsedAmount = '0.00';



    $priceDet = $this->db->where('id', $vehicle_id)->get(VEHICLE)->row();

    $enqryDet = $this->db->where('vehicle_id', $vehicle_id)->get(VEHICLE_ENQUIRY)->row();

    

   

    $booking_type = $this->input->post('booking_type');

    

    $condition = array('id' => $user_id);

    $userDetails = $this->mobile_model->get_all_details(USERS, $condition);

    $loginUserId = $user_id;

   

    //$tax = $this->input->post('tax');

    $currencyCode = $this->input->post('currency_code');

    $user_currencycode = $priceDet->currency;

    $currency_cron_id = $enqryDet->currency_cron_id;

    $enquiryid = $this->input->post('enquiryid');

    $product = $this->mobile_model->get_all_details(VEHICLE, array('id' => $vehicle_id));

    $seller = $this->mobile_model->get_all_details(USERS, array('id' => $product->row()->user_id));

    //$dealcode = $this->db->insert_id();

    $lastFeatureInsertId = $this->session->userdata('randomNo');

    //echo "dealCodeBefore insert";print_r($lastFeatureInsertId); 

    $userDetails = $this->mobile_model->get_all_details(USERS, $condition);

    

    $excludeArr = array('authorize_mode', 'authorize_id', 'authorize_key', 'creditvalue', 'shipping_id', 'cardType', 'email', 'cardNumber', 'CCExpDay', 'CCExpMnth', 'creditCardIdentifier', 'total_price', 'CreditSubmit');

    $condition = array('id' => $loginUserId);

    $dataArr = array('user_id' => $loginUserId, 'full_name' => $userDetails->row()->firstname . ' ' . $userDetails->row()->lastname, 'address1' => $this->input->post('address'), 'address2' => $this->input->post('address2'), 'city' => $this->input->post('city'), 'state' => $this->input->post('state'), 'country' => $this->input->post('country'), 'postal_code' => $this->input->post('postal_code'), 'phone' => $this->input->post('phone_no'));

    $StripDetVal = unserialize(StripeDetails);

    $StripeVals = unserialize($StripDetVal['settings']);

    require_once('./stripe/lib/Stripe.php');

    $secret_key = $StripeVals['secret_key'];

    $publishable_key = $StripeVals['publishable_key'];

    $stripe = array("secret_key" => $secret_key, "publishable_key" => $publishable_key);

    Stripe::setApiKey($stripe['secret_key']);

    //$token = $this->input->post('stripeToken');







   //$token = $this->input->post('stripeToken');

       //$token = 'tok_18rNhZGSJl4rw2Zd3itBZgF5';

   $stripeToken = Stripe_Token::create(

     array(

       "card" => array(

         "name" => $userDetails->row()->firstname,

         "number" => $this->input->post('cardnumber'),

         "exp_month" => $this->input->post('cc_exp_month'),

         "exp_year" => $this->input->post('cc_exp_year'),

         "cvc" => $this->input->post('credit_card_identifier')

       )

     )

   );



  $token = $stripeToken['id'];  

    //$amounts = currencyConvertToUSD($product_id, $values['amount']) * 100;

    $amounts = currency_conversion($user_currencycode, 'USD', $this->input->post('total_price'),$currency_cron_id);

    //echo $amounts.'|'.$user_currencycode.'|'.$this->input->post('total_price')."|".$currency_cron_id.'|'.$this->data['currencyType']; exit;

    //35.99|PHP|1885.26|33

    try {

      $customer = Stripe_Customer::create(array("card" => $token, "description" => "Product Purhcase for " . $this->config->item('email_title'), "email" => $this->input->post('email')));

      Stripe_Charge::create(array("amount"   => ($amounts*100), # amount in cents, again

                    "currency" => 'USD', "customer" => $customer->id));

     // $product_id = $this->input->post('booking_rental_id');

      $product = $this->mobile_model->get_all_details(VEHICLE, array('id' => $vehicle_id));

      $seller = $this->mobile_model->get_all_details(USERS, array('id' => $product->row()->user_id));

      $totalAmount = $this->input->post('total_price');

      //$totalAmount = currency_conversion($user_currencycode, 'USD', $this->input->post('total_price'),$currency_cron_id);

      if ($this->session->userdata('randomNo') != '') {

        $delete = 'delete from ' . VEHICLE_PAYMENT . ' where dealCodeNumber = "' . $this->session->userdata('randomNo') . '" and user_id = "' . $loginUserId . '" ';

        $this->mobile_model->ExecuteQuery($delete, 'delete');

        $dealCodeNumber = $this->session->userdata('randomNo');

      } else {

        $dealCodeNumber = mt_rand();

      }

      $insertIds = array();

      $now = date("Y-m-d H:i:s");

      $paymentArr = array('vehicle_type' => $vehicle_type,

                'vehicle_id' => $vehicle_id, 

                'sell_id' => $product->row()->user_id, 

                'price' => $totalAmount,   //totAmt in rentalEnquiry

                'indtotal'   => $product->row()->day_price,

                'sumtotal' => $totalAmount,  //totAmt in rentalEnquiry

                'user_id'    => $loginUserId, //price in product Tbl

                'created'    => $now, 

                'dealCodeNumber' => $dealCodeNumber,

                'status' => 'paid', 

                'shipping_status' => 'Pending',

                'total' => $totalAmount,  //totAmt in rentalEnquiry

                'EnquiryId'  => $enquiryid, 

                'inserttime' => NOW(), 

                'currency_code' => $user_currencycode);

      $this->mobile_model->simple_insert(VEHICLE_PAYMENT, $paymentArr);

  

      $insertIds[] = $this->db->insert_id();

      $paymtdata = array('randomNo' => $dealCodeNumber, 'randomIds' => $insertIds, 'EnquiryId' => $enquiryid);

      $this->session->set_userdata($paymtdata);

      $this->mobile_model->edit_rentalbooking(array('booking_status' => 'Booked'), array('id' => $this->session->userdata('EnquiryId')));

      $lastFeatureInsertId = $dealCodeNumber;



     // redirect('vehicle_order/success/' . $loginUserId . '/' . $lastFeatureInsertId . '/' . $token);





$Pray_Email = $userDetails->row()->email;

                    $transId = $response->transaction_id;

                $UserNo = $user_id;

                $DealCodeNo = $dealCodeNumber;



                $EnquiryUpdate = $this->mobile_model->get_all_details(VEHICLE_PAYMENT, array('dealCodeNumber' => $DealCodeNo));

                $eprd_id = $EnquiryUpdate->row()->vehicle_id;

        $vehicle_type = $this->db->select('vehicle_type')->where('id',$eprd_id)->get(VEHICLE)->row()->vehicle_type;

        

                $eInq_id = $EnquiryUpdate->row()->EnquiryId;

                $totalAmt = $EnquiryUpdate->row()->total;

               

                $this->data['invoicedata'] = $this->mobile_model->get_all_details(VEHICLE_ENQUIRY, array('id' => $eInq_id));

                $condition1 = array('id' => $eInq_id);

                $dataArr1 = array('booking_status' => 'Booked');

                $this->mobile_model->update_details(VEHICLE_ENQUIRY, $dataArr1, $condition1); 


                $SelBookingQty = $this->mobile_model->get_all_details(VEHICLE_ENQUIRY, array('id' => $eInq_id));

                $productId = $SelBookingQty->row()->vehicle_id;

                $arrival = $SelBookingQty->row()->checkin_date;

                $depature = $SelBookingQty->row()->checkout_date;

                $dates = $this->getDatesFromRange($arrival, $depature);

                $i = 1;

        $split_checkedin=explode(" ",$arrival);

        $checked_in=$split_checkedin[1];

        $split_checkedintime=explode(":",$checked_in);

        $checked_in=$split_checkedintime[0].':'.$split_checkedintime[1];

        

        $split_checkedout=explode(" ",$depature);

        $checked_out=$split_checkedout[1];

        $split_checkedouttime=explode(":",$checked_out);

        $checked_out=$split_checkedouttime[0].':'.$split_checkedouttime[1];

    

                $dateMinus1 = count($dates) - 1;

                foreach ($dates as $date) {

                    if ($i <= $dateMinus1) {

                        $BookingArr = $this->mobile_model->get_all_details(VEHICLE_BOOKING_DATES, array('vehicle_id' => $productId, 'id_state' => 1, 'id_item' => 1, 'the_date' => $date));

                        if ($BookingArr->num_rows() > 0) {

                        } else {

                            $dataArr = array(

                      'vehicle_type'=>$vehicle_type, 

                      'vehicle_id' => $productId, 

                      'id_state' => 1, 

                      'id_item' => 1, 

                      'the_date' => $date,

                      'id_booking'=>$eInq_id,

                      'checked_in' => $checked_in,

                      'checked_out' => $checked_out,

                      'tot_checked_in' => $arrival,

                      'tot_checked_out' => $depature,

                      'tot_time' => $arrival. ' - ' . $depature

                      );

                            $this->mobile_model->simple_insert(VEHICLE_BOOKING_DATES, $dataArr);

                        }

                    }

                    $i++;

                }

        //echo 'product Id'.$productId.'<br>';

                $DateArr = $this->mobile_model->get_all_details(VEHICLE_BOOKING_DATES, array('vehicle_id' => $productId));

        

                $dateDispalyRowCount = 0;

                $dateArrVAl = '';

        if($DateArr->num_rows() > 0){

          $dateArrVAl .='{';

          

          foreach($DateArr->result() as $dateDispalyRow){

            $time_details=$this->db->select('checked_in,checked_out')->where(array('the_date'=>$dateDispalyRow->the_date,'vehicle_id'=>$productId))->get(VEHICLE_BOOKING_DATES);

          

            if($time_details->num_rows() > 0)

            {

              $final_date_time_result="";

              foreach($time_details->result() as $date_time)

              {

                $checked_in=$date_time->checked_in;

                $checked_out=$date_time->checked_out;

                $final_date_time_result .= $checked_in.'-'.$checked_out.' ';

              }

              if($dateDispalyRowCount==0){

                $dateArrVAl .='"'.$dateDispalyRow->the_date.'":{"available":"1","bind":0,"info":"","notes":"","price":"'.$payable_amount.'","promo":"","status":"booked","checkedtime":"'.$final_date_time_result.'","prd_id":"'.$dateDispalyRow->vehicle_id.'"}';

              }else{

                $dateArrVAl .=',"'.$dateDispalyRow->the_date.'":{"available":"1","bind":0,"info":"","notes":"","price":"'.$payable_amount.'","promo":"","status":"booked","checkedtime":"'.$final_date_time_result.'","prd_id":"'.$dateDispalyRow->vehicle_id.'"}';

              }

              $final_date_time_result="";

              //$dateArrVAl="";

            }

            else

            {

              if($dateDispalyRowCount==0){

                $dateArrVAl .='"'.$dateDispalyRow->the_date.'":{"available":"1","bind":0,"info":"","notes":"","price":"'.$payable_amount.'","promo":"","status":"booked","checkedtime":"'.$dateDispalyRow->checked_in. '-'.$dateDispalyRow->checked_out.'","prd_id":"'.$dateDispalyRow->vehicle_id.'"}';

              }else{

                $dateArrVAl .=',"'.$dateDispalyRow->the_date.'":{"available":"1","bind":0,"info":"","notes":"","price":"'.$payable_amount.'","promo":"","status":"booked","checkedtime":"'.$dateDispalyRow->checked_in. '-'.$dateDispalyRow->checked_out.'","prd_id":"'.$dateDispalyRow->vehicle_id.'"}';

              }

            }

            



            $dateDispalyRowCount=$dateDispalyRowCount+1;

            

          }

          $dateArrVAl .='}';

        }

               

                $inputArr4 = array('id' => $productId, 'data' => trim($dateArrVAl));

                $this->mobile_model->update_details(VEHICLE_SCHEDULE, $inputArr4, array('id' => $productId));

        if($vehicle_type == 4) { $seo_tag = 'car-listing'; }

        if($vehicle_type == 5) { $seo_tag = 'van-listing'; }

                $condition = array('status' => 'Active', 'seo_tag' => $seo_tag);

                $service_tax_host = $this->mobile_model->get_all_details(COMMISSION, $condition);

                $host_tax_type = $service_tax_host->row()->promotion_type;

                $host_tax_value = $service_tax_host->row()->commission_percentage;

        if($booking_type == 'daily')

        {

          if($vehicle_type == 4) { $seo_tag = 'car-booking-daily'; }

          if($vehicle_type == 5) { $seo_tag = 'van-booking-daily'; }

        }

        else

        {

          if($vehicle_type == 4) { $seo_tag = 'car-booking-hourly'; }

          if($vehicle_type == 5) { $seo_tag = 'van-booking-hourly'; }

        }

                $condition = array('status' => 'Active', 'seo_tag' => $seo_tag);

                $service_tax_guest = $this->mobile_model->get_all_details(COMMISSION, $condition);

                $guest_tax_type = $service_tax_guest->row()->promotion_type;

                $guest_tax_value = $service_tax_guest->row()->commission_percentage;

                $orderDetails = $this->mobile_model->get_all_details(VEHICLE_ENQUIRY, array('id' => $eInq_id));

                $userDetails = $this->mobile_model->get_all_details(USERS, array('id' => $orderDetails->row()->renter_id));

                $guest_fee = $orderDetails->row()->serviceFee;

                $netAmount = $orderDetails->row()->totalAmt - $guest_fee;

                if ($host_tax_type == 'flat') {

                    $host_fee = 0;

                } else {

                    $host_fee = 0;

                }

                $payable_amount = $netAmount - $host_fee;

        //echo $userDetails->row()->rep_code; exit;

                if ($userDetails->row()->rep_code != '') 

                {

                    $condition = array('status' => 'Active', 'seo_tag' => 'host-accept-rep');

                    $service_tax_guest = $this->mobile_model->get_all_details(COMMISSION, $condition);

                    $rep_host = $service_tax_guest->row()->promotion_type;

                    $rep_guest = $service_tax_guest->row()->commission_percentage;

                    $condition = array('status' => 'Active', 'seo_tag' => 'guest-accept-rep');

                    $service_tax_guest = $this->mobile_model->get_all_details(COMMISSION, $condition);

                    $rep_host_rep = $service_tax_guest->row()->promotion_type;

                    $rep_guest_rep = $service_tax_guest->row()->commission_percentage;

                    if ($rep_host == 'flat') {

                        $rep_fees = $rep_host;

                    } else {

                        $rep_all = $rep_guest / 100;

                        $rep_fees = ($netAmount * $host_tax_value) / 100;

                    }

                    $rep_fee = $netAmount * $rep_all;

                    $data2 = array('rental_type'=>$vehicle_type,'host_email' => $userDetails->row()->email, 'booking_no' => $orderDetails->row()->Bookingno, 'total_amount' => $orderDetails->row()->totalAmt, 'guest_fee' => $guest_fee, 'host_fee' => $rep_fees, 'rep_fee' => $rep_fee, 'code' => $userDetails->row()->rep_code, 'payable_amount' => $rep_fee);

          

                    $chkQry = $this->mobile_model->get_all_details(COMMISSION_REP_TRACKING, array('booking_no' => $orderDetails->row()->Bookingno,'rental_type'=>$vehicle_type));

                    if ($chkQry->num_rows() == 0) $this->mobile_model->simple_insert(COMMISSION_REP_TRACKING, $data2); else

                        $this->mobile_model->update_details(COMMISSION_REP_TRACKING, $data2, array('booking_no' => $orderDetails->row()->Bookingno,'rental_type'=>$vehicle_type));

                    $data1 = array('rental_type'=>$vehicle_type,'host_email' => $userDetails->row()->email, 'booking_no' => $orderDetails->row()->Bookingno, 'total_amount' => $EnquiryUpdate->row()->total, 'guest_fee' => $guest_fee, 'host_fee' => $host_fee, 'cancel_percentage' => $orderDetails->row()->cancel_percentage, 'subtotal' => $orderDetails->row()->subTotal, 'payable_amount' => $payable_amount, 'booking_walletUse' => $walletUsedAmount //malar - wallet amount used on payment

                    );

                    $chkQry = $this->mobile_model->get_all_details(COMMISSION_TRACKING, array('booking_no' => $orderDetails->row()->Bookingno,'rental_type'=>$vehicle_type));

                    if ($chkQry->num_rows() == 0) $this->mobile_model->simple_insert(COMMISSION_TRACKING, $data1); else

                        $this->mobile_model->update_details(COMMISSION_TRACKING, $data1, array('booking_no' => $orderDetails->row()->Bookingno,'rental_type'=>$vehicle_type));

            

            

                } else {

          

          

                    $data1 = array('rental_type'=>$vehicle_type, 'host_email' => $userDetails->row()->email, 'booking_no' => $orderDetails->row()->Bookingno, 'total_amount' => $orderDetails->row()->totalAmt, 'guest_fee' => $guest_fee, 'host_fee' => $host_fee, 'cancel_percentage' => $orderDetails->row()->cancel_percentage, 'subtotal' => $orderDetails->row()->subTotal, 'payable_amount' => $payable_amount, 'booking_walletUse' => $walletUsedAmount //malar - wallet amount used on payment

                    );

                    $chkQry = $this->mobile_model->get_all_details(COMMISSION_TRACKING, array('booking_no' => $orderDetails->row()->Bookingno,'rental_type'=>$vehicle_type));

          //echo $chkQry->num_rows().'<br>';print_r($data1); exit;

                    if ($chkQry->num_rows() == 0) $this->mobile_model->simple_insert(COMMISSION_TRACKING, $data1); else

                        $this->mobile_model->update_details(COMMISSION_TRACKING, $data1, array('booking_no' => $orderDetails->row()->Bookingno,'rental_type'=>$vehicle_type));

            

                }

        

      

                $this->booking_conform_mail($DealCodeNo);





$json_encode = json_encode(array("status"=>1,"message" => $this->succ_paid,"base_id"=> $vehicle_type),JSON_PRETTY_PRINT);



      echo $json_encode;

exit();





    } catch (Exception $e) {

      $error = $e->getMessage();

       $json_encode = json_encode(array("status"=>0,"message" => $error,"base_id"=> $vehicle_type),JSON_PRETTY_PRINT);



      echo $json_encode;

exit();

      

    }

  }

  public function vehicle_paypal_payment()
  {
    $transId = $this->input->post('txn_id');
    $Pray_Email = $this->input->post('payer_email');
    $payment_gross = $this->input->post('payment_gross');
    $currencySymbol = $this->input->post('currency_symbol');
    $currencyCode = $this->input->post('currency_code');
    $enquiryid = $this->input->post('enquiryid');

    if($enquiryid == "")
    {
      $json_encode = json_encode(array("status"=>0, "message" => $this->parm_missing),JSON_PRETTY_PRINT);
      echo $json_encode;
      exit();
    }

    $couponUsedAmount = $walletUsedAmount = '0.00';

    $enqryDet = $this->db->where('id', $enquiryid)->get(VEHICLE_ENQUIRY)->row();  

    if(count($enqryDet) == 0)
    {
      echo json_encode(array('status'=>0,'message'=>$this->no_dta_found));
      exit();
    }
    else
    {
      $vehDet = $this->db->where('id', $enqryDet->vehicle_id)->get(VEHICLE)->row();

      $loginUserId = $enqryDet->user_id; 
      $user_currencycode = $vehDet->currency;
      $currency_cron_id = $enqryDet->currency_cron_id;

      $seller = $this->mobile_model->get_all_details(USERS, array('id' => $vehDet->user_id));

      $now = date("Y-m-d H:i:s");

      if($enqryDet->booking_type == 'daily')
      {
        $indtotal =  $vehDet->day_price;
      }
      else
      {
        $indtotal =  $vehDet->min_hour_price;
      }

      $dealCodeNumber = mt_rand();

      $paymentArr = array('vehicle_type'=>$vehDet->vehicle_type, 'vehicle_id' => $enqryDet->vehicle_id, 'price' => $enqryDet->totalAmt, 'indtotal' => $indtotal, 'sumtotal' => $enqryDet->totalAmt, 'user_id' => $loginUserId, 'sell_id' => $vehDet->user_id, 'created' => $now, 'dealCodeNumber' => $dealCodeNumber, 'status' => 'Paid', 'shipping_status' => 'Pending', 'total' => $enqryDet->totalAmt, 'EnquiryId' => $enquiryid, 'inserttime' => NOW(), 'currency_code' => $enqryDet->user_currencycode,"payment_type"=>"paypal","paypal_transaction_id"=>$transId,"payer_email"=>$Pray_Email);

      $this->mobile_model->simple_insert(VEHICLE_PAYMENT, $paymentArr);

      $condition1 = array('id' => $enquiryid);
      $dataArr1 = array('booking_status' => 'Booked');
      $this->mobile_model->update_details(VEHICLE_ENQUIRY, $dataArr1, $condition1);

      $EnquiryUpdate = $this->mobile_model->get_all_details(VEHICLE_PAYMENT, array('dealCodeNumber' => $dealCodeNumber));

      $SelBookingQty = $this->mobile_model->get_all_details(VEHICLE_ENQUIRY, array('id' => $enquiryid));

      $productId = $SelBookingQty->row()->vehicle_id;

      $arrival = $SelBookingQty->row()->checkin_date;

      $depature = $SelBookingQty->row()->checkout_date;

      $dates = $this->getDatesFromRange($arrival, $depature);

      $i = 1;

      $split_checkedin=explode(" ",$arrival);

      $checked_in=$split_checkedin[1];

      $split_checkedintime=explode(":",$checked_in);

      $checked_in=$split_checkedintime[0].':'.$split_checkedintime[1];

      

      $split_checkedout=explode(" ",$depature);

      $checked_out=$split_checkedout[1];

      $split_checkedouttime=explode(":",$checked_out);

      $checked_out=$split_checkedouttime[0].':'.$split_checkedouttime[1];

      $dateMinus1 = count($dates) - 1;

      foreach ($dates as $date) {

          if ($i <= $dateMinus1) 
          {

            $BookingArr = $this->mobile_model->get_all_details(VEHICLE_BOOKING_DATES, array('vehicle_id' => $productId, 'id_state' => 1, 'id_item' => 1, 'the_date' => $date));

              if ($BookingArr->num_rows() > 0) {

              } else {

                  $dataArr = array(

            'vehicle_type'=>$vehDet->vehicle_type, 

            'vehicle_id' => $productId, 

            'id_state' => 1, 

            'id_item' => 1, 

            'the_date' => $date,

            'id_booking'=>$enquiryid,

            'checked_in' => $checked_in,

            'checked_out' => $checked_out,

            'tot_checked_in' => $arrival,

            'tot_checked_out' => $depature,

            'tot_time' => $arrival. ' - ' . $depature

            );

            $this->mobile_model->simple_insert(VEHICLE_BOOKING_DATES, $dataArr);

              }

          }

          $i++;

      }

      $DateArr = $this->mobile_model->get_all_details(VEHICLE_BOOKING_DATES, array('vehicle_id' => $enqryDet->vehicle_id));
      $dateDispalyRowCount = 0;
      $dateArrVAl = '';

      if($DateArr->num_rows() > 0){
          $dateArrVAl .='{';
          
          foreach($DateArr->result() as $dateDispalyRow){
            $time_details=$this->db->select('checked_in,checked_out')->where(array('the_date'=>$dateDispalyRow->the_date,'vehicle_id'=>$productId))->get(VEHICLE_BOOKING_DATES);
            //echo $this->db->last_query().'<br>';
            //print_r($time_details->result()); echo '<br>';
            if($time_details->num_rows() > 0)
            {
              $final_date_time_result="";
              foreach($time_details->result() as $date_time)
              {
                $checked_in=$date_time->checked_in;
                $checked_out=$date_time->checked_out;
                $final_date_time_result .= $checked_in.'-'.$checked_out.' ';
              }
              if($dateDispalyRowCount==0){
                $dateArrVAl .='"'.$dateDispalyRow->the_date.'":{"available":"1","bind":0,"info":"","notes":"","price":"'.$price.'","promo":"","status":"booked","checkedtime":"'.$final_date_time_result.'","prd_id":"'.$dateDispalyRow->vehicle_id.'"}';
              }else{
                $dateArrVAl .=',"'.$dateDispalyRow->the_date.'":{"available":"1","bind":0,"info":"","notes":"","price":"'.$price.'","promo":"","status":"booked","checkedtime":"'.$final_date_time_result.'","prd_id":"'.$dateDispalyRow->vehicle_id.'"}';
              }
              $final_date_time_result="";
              //$dateArrVAl="";
            }
            else
            {
              if($dateDispalyRowCount==0){
                $dateArrVAl .='"'.$dateDispalyRow->the_date.'":{"available":"1","bind":0,"info":"","notes":"","price":"'.$price.'","promo":"","status":"booked","checkedtime":"'.$dateDispalyRow->checked_in. '-'.$dateDispalyRow->checked_out.'","prd_id":"'.$dateDispalyRow->vehicle_id.'"}';
              }else{
                $dateArrVAl .=',"'.$dateDispalyRow->the_date.'":{"available":"1","bind":0,"info":"","notes":"","price":"'.$price.'","promo":"","status":"booked","checkedtime":"'.$dateDispalyRow->checked_in. '-'.$dateDispalyRow->checked_out.'","prd_id":"'.$dateDispalyRow->vehicle_id.'"}';
              }
            }
            

            $dateDispalyRowCount=$dateDispalyRowCount+1;
            
          }
          $dateArrVAl .='}';
        }


        $inputArr4 = array('id' => $productId, 'data' => trim($dateArrVAl));
                $this->product_model->update_details(VEHICLE_SCHEDULE, $inputArr4, array('id' => $productId));
        $vehicle_type = $vehDet->vehicle_type;
        if($vehicle_type == 4) { $seo_tag = 'car-listing'; }
        if($vehicle_type == 5) { $seo_tag = 'van-listing'; }
        $condition = array('status' => 'Active', 'seo_tag' => $seo_tag);
        $service_tax_host = $this->mobile_model->get_all_details(COMMISSION, $condition);
        $this->data['host_tax_type'] = $service_tax_host->row()->promotion_type;
        $this->data['host_tax_value'] = $service_tax_host->row()->commission_percentage;
        if($SelBookingQty->row()->booking_type=='daily')
        {
          if($vehicle_type == 4) { $seo_tag = 'car-booking-daily'; }
          if($vehicle_type == 5) { $seo_tag = 'van-booking-daily'; }
        }
        else
        {
          if($vehicle_type == 4) { $seo_tag = 'car-booking-hourly'; }
          if($vehicle_type == 5) { $seo_tag = 'van-booking-hourly'; }
        }

        $condition = array('status' => 'Active', 'seo_tag' => $seo_tag);
        $service_tax_guest = $this->mobile_model->get_all_details(COMMISSION, $condition);
        $this->data['guest_tax_type'] = $service_tax_guest->row()->promotion_type;
        $this->data['guest_tax_value'] = $service_tax_guest->row()->commission_percentage;
        $orderDetails = $this->mobile_model->get_all_details(VEHICLE_ENQUIRY, array('id' => $enquiryid));
        $userDetails = $this->mobile_model->get_all_details(USERS, array('id' => $orderDetails->row()->renter_id));
        $guest_fee = $orderDetails->row()->serviceFee;
        $netAmount = $orderDetails->row()->totalAmt - $guest_fee;
        if ($this->data['host_tax_type'] == 'flat') {
            $host_fee = 0;
        } else {
            $host_fee = 0;
        }
        $payable_amount = $netAmount - $host_fee;

        if ($userDetails->row()->rep_code != '') 
        {
            $condition = array('status' => 'Active', 'seo_tag' => 'host-accept-rep');
            $service_tax_guest = $this->mobile_model->get_all_details(COMMISSION, $condition);
            $this->data['rep_host'] = $service_tax_guest->row()->promotion_type;
            $this->data['rep_guest'] = $service_tax_guest->row()->commission_percentage;
            $condition = array('status' => 'Active', 'seo_tag' => 'guest-accept-rep');
            $service_tax_guest = $this->mobile_model->get_all_details(COMMISSION, $condition);
            $this->data['rep_host_rep'] = $service_tax_guest->row()->promotion_type;
            $this->data['rep_guest_rep'] = $service_tax_guest->row()->commission_percentage;
            if ($this->data['rep_host'] == 'flat') {
                $rep_fees = $this->data['rep_host'];
            } else {
                $rep_all = $this->data['rep_guest'] / 100;
                $rep_fees = ($netAmount * $this->data['host_tax_value']) / 100;
            }
            $rep_fee = $netAmount * $rep_all;
            $data2 = array('rental_type'=>$vehicle_type,'host_email' => $userDetails->row()->email, 'booking_no' => $orderDetails->row()->Bookingno, 'total_amount' => $orderDetails->row()->totalAmt, 'guest_fee' => $guest_fee, 'host_fee' => $rep_fees, 'rep_fee' => $rep_fee, 'code' => $userDetails->row()->rep_code, 'payable_amount' => $rep_fee);
  
            $chkQry = $this->mobile_model->get_all_details(COMMISSION_REP_TRACKING, array('booking_no' => $orderDetails->row()->Bookingno,'rental_type'=>$vehicle_type));
            if ($chkQry->num_rows() == 0) $this->mobile_model->simple_insert(COMMISSION_REP_TRACKING, $data2); else
                $this->mobile_model->update_details(COMMISSION_REP_TRACKING, $data2, array('booking_no' => $orderDetails->row()->Bookingno,'rental_type'=>$vehicle_type));
            $data1 = array('rental_type'=>$vehicle_type,'host_email' => $userDetails->row()->email, 'booking_no' => $orderDetails->row()->Bookingno, 'total_amount' => $EnquiryUpdate->row()->total, 'guest_fee' => $guest_fee, 'host_fee' => $host_fee, 'cancel_percentage' => $orderDetails->row()->cancel_percentage, 'subtotal' => $orderDetails->row()->subTotal, 'payable_amount' => $payable_amount, 'booking_walletUse' => $walletUsedAmount //malar - wallet amount used on payment
            );
            $chkQry = $this->mobile_model->get_all_details(COMMISSION_TRACKING, array('booking_no' => $orderDetails->row()->Bookingno,'rental_type'=>$vehicle_type));
            if ($chkQry->num_rows() == 0) $this->mobile_model->simple_insert(COMMISSION_TRACKING, $data1); else
                $this->mobile_model->update_details(COMMISSION_TRACKING, $data1, array('booking_no' => $orderDetails->row()->Bookingno,'rental_type'=>$vehicle_type));
    
    
        } else {
  
  //$payable_amount = $orderDetails->row()->totalAmt - $guest_fee;
            $data1 = array('rental_type'=>$vehicle_type, 'host_email' => $userDetails->row()->email, 'booking_no' => $orderDetails->row()->Bookingno, 'total_amount' => $orderDetails->row()->totalAmt, 'guest_fee' => $guest_fee, 'host_fee' => $host_fee, 'cancel_percentage' => $orderDetails->row()->cancel_percentage, 'subtotal' => $orderDetails->row()->subTotal, 'payable_amount' => $payable_amount, 'booking_walletUse' => $walletUsedAmount //malar - wallet amount used on payment
            );
            $chkQry = $this->mobile_model->get_all_details(COMMISSION_TRACKING, array('booking_no' => $orderDetails->row()->Bookingno,'rental_type'=>$vehicle_type));
  //echo $chkQry->num_rows().'<br>';print_r($data1); exit;
            if ($chkQry->num_rows() == 0) $this->mobile_model->simple_insert(COMMISSION_TRACKING, $data1); else
                $this->mobile_model->update_details(COMMISSION_TRACKING, $data1, array('booking_no' => $orderDetails->row()->Bookingno,'rental_type'=>$vehicle_type));
    
        }
        $this->booking_conform_mail($DealCodeNo);
        $response[] = array("currencycode" =>$currencyCode,"total_price" =>floatval($orderDetails->row()->totalAmt),"booking_no"=>$orderDetails->row()->Bookingno);
        $json_encode = json_encode(array("status"=>1,"message"=>"Success! Payment Made Successfully","payment_success"=>$response));
        echo $json_encode;
        exit;

    }

  }

  public function vehicle_paymaya_payment()
  {
    $transId = $this->input->post('txn_id');
    $Pray_Email = $this->input->post('payer_email');
    $payment_gross = $this->input->post('payment_gross');
    $currencySymbol = $this->input->post('currency_symbol');
    $currencyCode = $this->input->post('currency_code');
    $enquiryid = $this->input->post('enquiryid');

    if($enquiryid == "")
    {
      $json_encode = json_encode(array("status"=>0, "message" => $this->parm_missing),JSON_PRETTY_PRINT);
      echo $json_encode;
      exit();
    }

    $couponUsedAmount = $walletUsedAmount = '0.00';

    $enqryDet = $this->db->where('id', $enquiryid)->get(VEHICLE_ENQUIRY)->row();  
    if(count($enqryDet) == 0)
    {
      echo json_encode(array('status'=>0,'message'=>$this->no_dta_found));
      exit();
    }
    else
    {
      $vehDet = $this->db->where('id', $enqryDet->vehicle_id)->get(VEHICLE)->row();

      $loginUserId = $enqryDet->user_id; 
      $user_currencycode = $vehDet->currency;
      $currency_cron_id = $enqryDet->currency_cron_id;

      $seller = $this->mobile_model->get_all_details(USERS, array('id' => $vehDet->user_id));

      $now = date("Y-m-d H:i:s");

      if($enqryDet->booking_type == 'daily')
      {
        $indtotal =  $vehDet->day_price;
      }
      else
      {
        $indtotal =  $vehDet->min_hour_price;
      }

      $dealCodeNumber = mt_rand();

      $paymentArr = array('vehicle_type'=>$vehDet->vehicle_type, 'vehicle_id' => $enqryDet->vehicle_id, 'price' => $enqryDet->totalAmt, 'indtotal' => $indtotal, 'sumtotal' => $enqryDet->totalAmt, 'user_id' => $loginUserId, 'sell_id' => $vehDet->user_id, 'created' => $now, 'dealCodeNumber' => $dealCodeNumber, 'status' => 'Paid', 'shipping_status' => 'Pending', 'total' => $enqryDet->totalAmt, 'EnquiryId' => $enquiryid, 'inserttime' => NOW(), 'currency_code' => $enqryDet->user_currencycode,"payment_type"=>"paymaya","payer_email"=>$Pray_Email);

      $this->mobile_model->simple_insert(VEHICLE_PAYMENT, $paymentArr);

      $condition1 = array('id' => $enquiryid);
      $dataArr1 = array('booking_status' => 'Booked');
      $this->mobile_model->update_details(VEHICLE_ENQUIRY, $dataArr1, $condition1);

      $EnquiryUpdate = $this->mobile_model->get_all_details(VEHICLE_PAYMENT, array('dealCodeNumber' => $dealCodeNumber));

      $SelBookingQty = $this->mobile_model->get_all_details(VEHICLE_ENQUIRY, array('id' => $enquiryid));

      $productId = $SelBookingQty->row()->vehicle_id;

      $arrival = $SelBookingQty->row()->checkin_date;

      $depature = $SelBookingQty->row()->checkout_date;

      $dates = $this->getDatesFromRange($arrival, $depature);

      $i = 1;

      $split_checkedin=explode(" ",$arrival);

      $checked_in=$split_checkedin[1];

      $split_checkedintime=explode(":",$checked_in);

      $checked_in=$split_checkedintime[0].':'.$split_checkedintime[1];

      

      $split_checkedout=explode(" ",$depature);

      $checked_out=$split_checkedout[1];

      $split_checkedouttime=explode(":",$checked_out);

      $checked_out=$split_checkedouttime[0].':'.$split_checkedouttime[1];

      $dateMinus1 = count($dates) - 1;

      foreach ($dates as $date) {

          if ($i <= $dateMinus1) 
          {

            $BookingArr = $this->mobile_model->get_all_details(VEHICLE_BOOKING_DATES, array('vehicle_id' => $productId, 'id_state' => 1, 'id_item' => 1, 'the_date' => $date));

              if ($BookingArr->num_rows() > 0) {

              } else {

                  $dataArr = array(

            'vehicle_type'=>$vehDet->vehicle_type, 

            'vehicle_id' => $productId, 

            'id_state' => 1, 

            'id_item' => 1, 

            'the_date' => $date,

            'id_booking'=>$enquiryid,

            'checked_in' => $checked_in,

            'checked_out' => $checked_out,

            'tot_checked_in' => $arrival,

            'tot_checked_out' => $depature,

            'tot_time' => $arrival. ' - ' . $depature

            );

            $this->mobile_model->simple_insert(VEHICLE_BOOKING_DATES, $dataArr);

              }

          }

          $i++;

      }

      $DateArr = $this->mobile_model->get_all_details(VEHICLE_BOOKING_DATES, array('vehicle_id' => $enqryDet->vehicle_id));
      $dateDispalyRowCount = 0;
      $dateArrVAl = '';

      if($DateArr->num_rows() > 0){
          $dateArrVAl .='{';
          
          foreach($DateArr->result() as $dateDispalyRow){
            $time_details=$this->db->select('checked_in,checked_out')->where(array('the_date'=>$dateDispalyRow->the_date,'vehicle_id'=>$productId))->get(VEHICLE_BOOKING_DATES);
            //echo $this->db->last_query().'<br>';
            //print_r($time_details->result()); echo '<br>';
            if($time_details->num_rows() > 0)
            {
              $final_date_time_result="";
              foreach($time_details->result() as $date_time)
              {
                $checked_in=$date_time->checked_in;
                $checked_out=$date_time->checked_out;
                $final_date_time_result .= $checked_in.'-'.$checked_out.' ';
              }
              if($dateDispalyRowCount==0){
                $dateArrVAl .='"'.$dateDispalyRow->the_date.'":{"available":"1","bind":0,"info":"","notes":"","price":"'.$price.'","promo":"","status":"booked","checkedtime":"'.$final_date_time_result.'","prd_id":"'.$dateDispalyRow->vehicle_id.'"}';
              }else{
                $dateArrVAl .=',"'.$dateDispalyRow->the_date.'":{"available":"1","bind":0,"info":"","notes":"","price":"'.$price.'","promo":"","status":"booked","checkedtime":"'.$final_date_time_result.'","prd_id":"'.$dateDispalyRow->vehicle_id.'"}';
              }
              $final_date_time_result="";
              //$dateArrVAl="";
            }
            else
            {
              if($dateDispalyRowCount==0){
                $dateArrVAl .='"'.$dateDispalyRow->the_date.'":{"available":"1","bind":0,"info":"","notes":"","price":"'.$price.'","promo":"","status":"booked","checkedtime":"'.$dateDispalyRow->checked_in. '-'.$dateDispalyRow->checked_out.'","prd_id":"'.$dateDispalyRow->vehicle_id.'"}';
              }else{
                $dateArrVAl .=',"'.$dateDispalyRow->the_date.'":{"available":"1","bind":0,"info":"","notes":"","price":"'.$price.'","promo":"","status":"booked","checkedtime":"'.$dateDispalyRow->checked_in. '-'.$dateDispalyRow->checked_out.'","prd_id":"'.$dateDispalyRow->vehicle_id.'"}';
              }
            }
            

            $dateDispalyRowCount=$dateDispalyRowCount+1;
            
          }
          $dateArrVAl .='}';
        }


        $inputArr4 = array('id' => $productId, 'data' => trim($dateArrVAl));
                $this->product_model->update_details(VEHICLE_SCHEDULE, $inputArr4, array('id' => $productId));
        $vehicle_type = $vehDet->vehicle_type;
        if($vehicle_type == 4) { $seo_tag = 'car-listing'; }
        if($vehicle_type == 5) { $seo_tag = 'van-listing'; }
        $condition = array('status' => 'Active', 'seo_tag' => $seo_tag);
        $service_tax_host = $this->mobile_model->get_all_details(COMMISSION, $condition);
        $this->data['host_tax_type'] = $service_tax_host->row()->promotion_type;
        $this->data['host_tax_value'] = $service_tax_host->row()->commission_percentage;
        if($SelBookingQty->row()->booking_type=='daily')
        {
          if($vehicle_type == 4) { $seo_tag = 'car-booking-daily'; }
          if($vehicle_type == 5) { $seo_tag = 'van-booking-daily'; }
        }
        else
        {
          if($vehicle_type == 4) { $seo_tag = 'car-booking-hourly'; }
          if($vehicle_type == 5) { $seo_tag = 'van-booking-hourly'; }
        }

        $condition = array('status' => 'Active', 'seo_tag' => $seo_tag);
        $service_tax_guest = $this->mobile_model->get_all_details(COMMISSION, $condition);
        $this->data['guest_tax_type'] = $service_tax_guest->row()->promotion_type;
        $this->data['guest_tax_value'] = $service_tax_guest->row()->commission_percentage;
        $orderDetails = $this->mobile_model->get_all_details(VEHICLE_ENQUIRY, array('id' => $enquiryid));
        $userDetails = $this->mobile_model->get_all_details(USERS, array('id' => $orderDetails->row()->renter_id));
        $guest_fee = $orderDetails->row()->serviceFee;
        $netAmount = $orderDetails->row()->totalAmt - $guest_fee;
        if ($this->data['host_tax_type'] == 'flat') {
            $host_fee = 0;
        } else {
            $host_fee = 0;
        }
        $payable_amount = $netAmount - $host_fee;

        if ($userDetails->row()->rep_code != '') 
        {
            $condition = array('status' => 'Active', 'seo_tag' => 'host-accept-rep');
            $service_tax_guest = $this->mobile_model->get_all_details(COMMISSION, $condition);
            $this->data['rep_host'] = $service_tax_guest->row()->promotion_type;
            $this->data['rep_guest'] = $service_tax_guest->row()->commission_percentage;
            $condition = array('status' => 'Active', 'seo_tag' => 'guest-accept-rep');
            $service_tax_guest = $this->mobile_model->get_all_details(COMMISSION, $condition);
            $this->data['rep_host_rep'] = $service_tax_guest->row()->promotion_type;
            $this->data['rep_guest_rep'] = $service_tax_guest->row()->commission_percentage;
            if ($this->data['rep_host'] == 'flat') {
                $rep_fees = $this->data['rep_host'];
            } else {
                $rep_all = $this->data['rep_guest'] / 100;
                $rep_fees = ($netAmount * $this->data['host_tax_value']) / 100;
            }
            $rep_fee = $netAmount * $rep_all;
            $data2 = array('rental_type'=>$vehicle_type,'host_email' => $userDetails->row()->email, 'booking_no' => $orderDetails->row()->Bookingno, 'total_amount' => $orderDetails->row()->totalAmt, 'guest_fee' => $guest_fee, 'host_fee' => $rep_fees, 'rep_fee' => $rep_fee, 'code' => $userDetails->row()->rep_code, 'payable_amount' => $rep_fee);
  
            $chkQry = $this->mobile_model->get_all_details(COMMISSION_REP_TRACKING, array('booking_no' => $orderDetails->row()->Bookingno,'rental_type'=>$vehicle_type));
            if ($chkQry->num_rows() == 0) $this->mobile_model->simple_insert(COMMISSION_REP_TRACKING, $data2); else
                $this->mobile_model->update_details(COMMISSION_REP_TRACKING, $data2, array('booking_no' => $orderDetails->row()->Bookingno,'rental_type'=>$vehicle_type));
            $data1 = array('rental_type'=>$vehicle_type,'host_email' => $userDetails->row()->email, 'booking_no' => $orderDetails->row()->Bookingno, 'total_amount' => $EnquiryUpdate->row()->total, 'guest_fee' => $guest_fee, 'host_fee' => $host_fee, 'cancel_percentage' => $orderDetails->row()->cancel_percentage, 'subtotal' => $orderDetails->row()->subTotal, 'payable_amount' => $payable_amount, 'booking_walletUse' => $walletUsedAmount //malar - wallet amount used on payment
            );
            $chkQry = $this->mobile_model->get_all_details(COMMISSION_TRACKING, array('booking_no' => $orderDetails->row()->Bookingno,'rental_type'=>$vehicle_type));
            if ($chkQry->num_rows() == 0) $this->mobile_model->simple_insert(COMMISSION_TRACKING, $data1); else
                $this->mobile_model->update_details(COMMISSION_TRACKING, $data1, array('booking_no' => $orderDetails->row()->Bookingno,'rental_type'=>$vehicle_type));
    
    
        } else {
  
  //$payable_amount = $orderDetails->row()->totalAmt - $guest_fee;
            $data1 = array('rental_type'=>$vehicle_type, 'host_email' => $userDetails->row()->email, 'booking_no' => $orderDetails->row()->Bookingno, 'total_amount' => $orderDetails->row()->totalAmt, 'guest_fee' => $guest_fee, 'host_fee' => $host_fee, 'cancel_percentage' => $orderDetails->row()->cancel_percentage, 'subtotal' => $orderDetails->row()->subTotal, 'payable_amount' => $payable_amount, 'booking_walletUse' => $walletUsedAmount //malar - wallet amount used on payment
            );
            $chkQry = $this->mobile_model->get_all_details(COMMISSION_TRACKING, array('booking_no' => $orderDetails->row()->Bookingno,'rental_type'=>$vehicle_type));
  //echo $chkQry->num_rows().'<br>';print_r($data1); exit;
            if ($chkQry->num_rows() == 0) $this->mobile_model->simple_insert(COMMISSION_TRACKING, $data1); else
                $this->mobile_model->update_details(COMMISSION_TRACKING, $data1, array('booking_no' => $orderDetails->row()->Bookingno,'rental_type'=>$vehicle_type));
    
        }
        $this->booking_conform_mail($DealCodeNo);
        $response[] = array("currencycode" =>$currencyCode,"total_price" =>floatval($orderDetails->row()->totalAmt),"booking_no"=>$orderDetails->row()->Bookingno);
        $json_encode = json_encode(array("status"=>1,"message"=>"Success! Payment Made Successfully","payment_success"=>$response));
        echo $json_encode;
        exit;

    }

  }


  public function booking_conform_mail($paymentid)

        {

      

      //echo $paymentid;

            $PaymentSuccess = $this->mobile_model->get_all_details(VEHICLE_PAYMENT, array('dealCodeNumber' => $paymentid));

      

            $Renter_details = $this->mobile_model->get_all_details(USERS, array('id' => $PaymentSuccess->row()->sell_id));



            $user_details = $this->mobile_model->get_all_details(USERS, array('id' => $PaymentSuccess->row()->user_id));

            $Rental_details = $this->mobile_model->get_all_details(VEHICLE, array('id' => $PaymentSuccess->row()->vehicle_id));           

            

            $Contact_details = $this->mobile_model->get_all_details(VEHICLE_ENQUIRY, array('id' => $PaymentSuccess->row()->EnquiryId));

            if($Contact_details->row()->additional_sub_total != '')

            {

                $aditional_amount = $Contact_details->row()->additional_sub_total;

            }

            else

            {

                $aditional_amount = 0;

            }



            $RentalPhoto = $this->mobile_model->get_all_details(VEHICLE_PHOTOS, array('vehicle_id' => $PaymentSuccess->row()->vehicle_id));

            $total = $Contact_details->row()->totalAmt - $Contact_details->row()->serviceFee;

            /*---------------email to user---------------------------*/

            $newsid = '29';

            $template_values = $this->mobile_model->get_newsletter_template_details($newsid);

            $subject = 'From: ' . $this->config->item('email_title') . ' - ' . $template_values['news_subject'];

            $proImages = base_url() . 'images/vehicles/' . $RentalPhoto->row()->image;

            $links = ''.base_url().'vehicles/detail/'.$Rental_details->row()->seourl.'';

            $chkIn = date('d-m-y', strtotime($Contact_details->row()->checkin_date));

            $chkOut = date('d-m-y', strtotime($Contact_details->row()->checkout_date));

            $user_image = $user_details->row()->image;

            $user_type = $user_details->row()->loginUserType;

            $user_types = $user_type;

            if ($user_image != '') {

                if ($user_types == 'google') {

                    $user_image = $user_image;

                } elseif ($user_types != '') {

                    $user_image = base_url() . 'images/users/' . $user_image;

                }

            } else {

                $user_image = base_url() . 'images/users/profile.png';

            }

            $renter_image = $Renter_details->row()->image;

            $renter_type = $Renter_details->row()->loginUserType;

            if ($renter_image != '') {

                if ($renter_type == 'google') {

                    $renter_image = $renter_image;

                } elseif ($renter_type != '') {

                    $renter_image = base_url() . 'images/users/' . $renter_image;

                }

            } else {

                $renter_image = base_url() . 'images/users/profile.png';

            }

            /***************************************currency****************************/

      $productCurrency = $Rental_details->row()->currency;

      $currency = $Rental_details->row()->currency;

      

      $guest_currency_type = $this->session->userdata('currency_type');

      $guestCurrencySymbol = $currency_symbol = $this->session->userdata('currency_s');

            $saved_type = $Rental_details->row()->booking_type; 

            $saved_booking_type = explode(",",$saved_type); 

            if(in_array('1', $saved_booking_type) && !in_array('2', $saved_booking_type))

            { 

                $singleNight = $Rental_details->row()->day_price;

            }

            elseif (!in_array('1', $saved_booking_type) && in_array('2', $saved_booking_type)) 

            {

                $singleNight = $Rental_details->row()->min_hour_exprice; 

            }

            elseif (in_array('1', $saved_booking_type) && in_array('2', $saved_booking_type)) 

            {

                $singleNight = $Rental_details->row()->day_price;

            }

            else

            { 

                $singleNight = $Rental_details->row()->day_price;

            }

      

            if($Contact_details->row()->booking_type == 'daily')

            {

                $noofnights = $Contact_details->row()->no_of_days; 

            }

      else

            {

                $noofnights = $Contact_details->row()->no_of_hours;

            }

      $subTot = $singleNight*$noofnights;

      $servicefee = $Contact_details->row()->serviceFee;

      $secDeposit = $Rental_details->row()->security_deposit;

      

      //BRL!=USD // all need to go as USD

      if($productCurrency!=$guest_currency_type)

      {

        $guestSingleNight = currency_conversion($productCurrency,$guest_currency_type, $singleNight);

        $guestSubTot = currency_conversion($productCurrency,$guest_currency_type, $subTot);

        $guestSecDep = currency_conversion($productCurrency,$guest_currency_type,$secDeposit);

        

      }

      else

      {

        $guestSingleNight = $singleNight;

        $guestSubTot = $subTot;

        $guestSecDep = $secDeposit;

        

      }

      if($guest_currency_type!=$Contact_details->row()->user_currencycode)

      {

        $guestServiceFee = currency_conversion($Contact_details->row()->user_currencycode,$guest_currency_type, $servicefee);

      }

      else

      {

        $guestServiceFee = $servicefee;

      }

      $guestNetTot = $guestSubTot+$guestServiceFee+$guestSecDep;

      

      

      $admin = $this->mobile_model->get_all_details(ADMIN, array('admin_type' => 'super'));

      $data = $admin->row();

      $admin_currencyCode = trim($data->admin_currencyCode);

      $adminCurrencySymbol = $this->db->select('currency_symbols')->from('fc_currency')->where('currency_type = ', $admin_currencyCode)->get()->row()->currency_symbols;

      //echo $admin_currencyCode.'|'.$productCurrency;

      if($admin_currencyCode!=$productCurrency)

      {

        $adminSingleNight = currency_conversion($productCurrency,$admin_currencyCode, $singleNight);

        $adminSubTot = currency_conversion($productCurrency,$admin_currencyCode, $subTot);

        $adminSecDep = currency_conversion($productCurrency,$admin_currencyCode,$secDeposit);

        

      }

      else

      {

        $adminSingleNight = $singleNight;

        $adminSubTot = $subTot;

        $adminSecDep = $secDeposit;

        

      }

      if($admin_currencyCode!=$Contact_details->row()->user_currencycode)

      {

        $adminServiceFee = currency_conversion($Contact_details->row()->user_currencycode, $admin_currencyCode, $servicefee);

      }

      else

      {

        $adminServiceFee = $servicefee;

      }

      $adminNetTot = $adminSubTot+$adminServiceFee+$adminSecDep;

      

      /*HOST*/

      $hostCurrencyType = $currency;

      $hostCurrencySymbol = $this->db->select('currency_symbols')->from('fc_currency')->where('currency_type = ', $currency)->get()->row()->currency_symbols;

      $hostSingleNight = $singleNight;



      $hostSubTot = $subTot;

      $hostSecDep = $secDeposit;

      if($hostCurrencyType!=$Contact_details->row()->user_currencycode)

      {

        $hostServiceFee = ceil(currency_conversion($Contact_details->row()->user_currencycode, $hostCurrencyType, $servicefee));

      }

      else

      {

        $hostServiceFee = $servicefee;

      }

      $hostNetTot = $hostSubTot+$hostServiceFee+$hostSecDep;

      $booking_type = 'Days';

            /***************************************currency****************************/

            $adminnewstemplateArr = array('email_title' => $this->config->item('email_title'), 'logo' => $this->data['logo'], 'first_name' => $user_details->row()->firstname, 'last_name' => $user_details->row()->lastname, 'NoofGuest' => $Contact_details->row()->NoofGuest, 'numofdates' => $Contact_details->row()->numofdates, 'booking_status' => $Contact_details->row()->booking_status, 'user_email' => $user_details->row()->email, 'ph_no' => $Renter_details->row()->phone_no, /*'Enquiry' => $Contact_details->row()->Enquiry,*/ 'checkin' => $chkIn, 'checkout' => $chkOut, /*'currency_price' => $currency_price,*/ 'currency' => $Rental_details->row()->currency, /*'securityDeposite' => $securityDeposite,*/ 'amount' => $total, 'netamount' => $Contact_details->row()->totalAmt, 'days' => $noofnights, /*'currency_serviceFee' => $currency_serviceFee,*/ 'renter_id' => $PaymentSuccess->row()->sell_id, /*'prd_id' => $PaymentSuccess->row()->vehicle_id,*/ 'renter_fname' => $Renter_details->row()->firstname, 'renter_lname' => $Renter_details->row()->lastname, 'owner_email' => $Renter_details->row()->email, 'owner_phone' => $Renter_details->row()->phone_no, 'rental_name' => $Rental_details->row()->veh_title, 'meta_title' => $this->config->item('email_title'), 'rental_image' => $proImages, 'renter_image' => $renter_image, 'user_image' => $user_image,'guestSingleNight'=>$guestSingleNight,'guestSubTot'=>$guestSubTot,'guestServiceFee'=>$guestServiceFee,'guestSecDep'=>$guestSecDep,'guestNetTot'=>$guestNetTot,'guest_currency_type'=>$guest_currency_type,'guestCurrencySymbol'=>$guestCurrencySymbol,'adminSingleNight'=>$adminSingleNight,'adminSubTot'=>$adminSubTot,'adminServiceFee'=>$adminServiceFee,'adminSecDep'=>$adminSecDep,'adminNetTot'=>$adminNetTot,'admin_currencyCode'=>$admin_currencyCode,'adminCurrencySymbol'=>$adminCurrencySymbol,'hostSingleNight'=>$hostSingleNight,'hostSubTot'=>$hostSubTot,'hostSecDep'=>$hostSecDep,'hostServiceFee'=>$hostServiceFee,'hostNetTot'=>$hostNetTot,'hostCurrencyType'=>$hostCurrencyType,'hostCurrencySymbol'=>$hostCurrencySymbol,'aditional_amount' => $aditional_amount,'booking_type'=> $booking_type,'links'=>$links);


            extract($adminnewstemplateArr);

            if ($template_values['sender_name'] == '' && $template_values['sender_email'] == '') {

                $sender_email = $this->data['siteContactMail'];

                $sender_name = $this->data['siteTitle'];

            } else {

                $sender_name = $template_values['sender_name'];

                $sender_email = $template_values['sender_email'];

            }

            $this->session->set_userdata('ContacterEmail', $user_details->row()->email);

            /* Mail function starts  */

            $reg = $adminnewstemplateArr;

            $this->load->library('email');

            for ($i = 1; $i <= 3; $i++) {

                if ($i == 1) {

                    $message = $this->load->view('newsletter/Usermailbooking' . $newsid . '.php', $reg, TRUE); // users

                    $email_values = array('mail_type' => 'html', 'from_mail_id' => $sender_email, 'mail_name' => $sender_name, 'to_mail_id' => $user_details->row()->email, 'subject_message' => $template_values['news_subject'], 'body_messages' => $message);

                    $this->email->from($email_values['from_mail_id'], $sender_name);

                    $this->email->to($email_values['to_mail_id']);

                    $this->email->subject($email_values['subject_message']);

                    $this->email->set_mailtype("html");

                    $this->email->message($message);

                    try {

                        $this->email->send();

                        $returnStr ['msg'] = 'Success';

                        $returnStr ['success'] = '1';

                    } catch (Exception $e) {

                        echo $e->getMessage();

                    }

                } elseif ($i == 2) {

                    $newsid = '22';

                    $message1 = $this->load->view('newsletter/Adminmailbooking' . $newsid . '.php', $reg, TRUE); //admin

                    $email_values = array('mail_type' => 'html', 'from_mail_id' => $sender_email, 'mail_name' => $sender_name, 'to_mail_id' => $sender_email, 'subject_message' => $template_values['news_subject'], 'body_messages' => $message1);

                    $this->email->from($email_values['from_mail_id'], $sender_name);

                    $this->email->to($email_values['to_mail_id']);

                    $this->email->subject($email_values['subject_message']);

                    $this->email->set_mailtype("html");

                    $this->email->message($message1);

                    try {

                        $this->email->send();

                        $returnStr ['msg'] = 'Success';

                        $returnStr ['success'] = '1';

                    } catch (Exception $e) {

                        echo $e->getMessage();

                    }

                } elseif ($i == 3) {

                    $newsid = '34';

                    $message2 = $this->load->view('newsletter/Host Mail Booking' . $newsid . '.php', $reg, TRUE); //Host

                    $email_values = array('mail_type' => 'html', 'from_mail_id' => $sender_email, 'mail_name' => $sender_name, 'to_mail_id' => $Renter_details->row()->email, 'subject_message' => $template_values['news_subject'], 'body_messages' => $message2);

                    $this->email->from($email_values['from_mail_id'], $sender_name);

                    $this->email->to($email_values['to_mail_id']);

                    $this->email->subject($email_values['subject_message']);

                    $this->email->set_mailtype("html");

                    $this->email->message($message2);

                    try {

                        $this->email->send();

                        $returnStr ['msg'] = 'Success';

                        $returnStr ['success'] = '1';

                    } catch (Exception $e) {

                        echo $e->getMessage();

                    }

                }

            }

        }




  public function mobile_vehicle_your_trips()

  {

    $guestEmail = $_POST['email']; 

    $rental_type = $_POST['base_id']; 

    $language_code = $_POST['lang_code'];    

    $user_currencyCode = $_POST['currency_code'];

    $my_trips = array();

    $keyword = "";

    if ($_POST) {

      $keyword = $this->input->post ('veh_title');

    }

    $this->data['guestDetails'] = $this->mobile_model->get_all_details(USERS,array('email'=>$guestEmail));

    $userId = $this->data['guestDetails']->row()->id; 



    $trip_details = $this->mobile_model->booked_vehicle_rental_trip($userId, $rental_type, $keyword);

 

    if($trip_details->num_rows()>0)

    {  

      foreach($trip_details->result() as $trip)

      {

        if($language_code == 'en')

        {

          $field_prdttitle = $trip->veh_title;

        }

        else

        {

          $NameField_f='veh_title_ph';

          if($trip->$NameField_f=='') 

          { 

              $field_prdttitle=$trip->veh_title;

          }

          else{

              $field_prdttitle=$trip->$NameField_f;

          }

        }



        if($trip->product_image != '')

        {

          $p_img = explode('.',$trip->product_image); 

          $suffix = strrchr($trip->product_image, "."); 

          $pos = strpos  ( $trip->product_image  , $suffix); 

          $name = substr_replace ($trip->product_image, "", $pos); 

          $pro_img = $name.''.$suffix;          

          $proImage = base_url().'images/vehicles/'.$pro_img;

        }

        else

        {

          $proImage = base_url().'images/vehicles/dummyProductImage.jpg';

        }



        if($trip->firstname != ''){

          $host_name = $trip->firstname;

        } else {

          $host_name ="";

        }



        if(($trip->checkout_date) > date("Y-m-d H:i:s")) {

          $is_previous = false;

        } else if(($trip->checkout) <= date("Y-m-d H:i:s")) {

          $is_previous = true;

        }



        $extend_trips_status = false;

        if($trip->booking_status == 'Booked')

        {

          if($trip->veh_booking_type == "2" || $trip->veh_booking_type == "1,2")

          {

            $extend_trips_status = true;

          }

        }



        $condition = array('currency_type'=>$trip->currency);

        $property_currency_details = $this->mobile_model->get_all_details ( CURRENCY, $condition );

        $property_currency_symbol = $property_currency_details->row()->currency_symbols;

        $property_currency_code = $property_currency_details->row()->currency_type; 



        $conditionrq = array('currency_type'=>$trip->currencycode);

        $paid_currency_details = $this->mobile_model->get_all_details ( CURRENCY, $conditionrq );

        $paid_currency_symbol = $paid_currency_details->row()->currency_symbols;



        $condition_new = array('currency_type' => $user_currencyCode);

        $user_currency_details = $this->mobile_model->get_all_details ( CURRENCY, $condition_new );

        $user_currency_symbol = $user_currency_details->row()->currency_symbols;

        

        $cur_date = date("Y-m-d H:i:s");

        $secDeposit = floatval($trip->secDeposit);

        $total = $trip->subTotal + $trip->secDeposit +$trip->serviceFee;



        $reviewData = $this->mobile_model->get_vehicle_review($trip->bookingno,$userId); 

        if($reviewData->num_rows()==0){

          $is_review =false;

        } else {

          $is_review =true;

        }



        $dispute_show_status = false;

        $cancel_show_status = false;

        $is_dispute = false;

        $is_canceled = false;

        $driver_review_show_status = false;

        $driver_review_status = false;

        $cancelled_by_host = false;



        if($trip->driver_id != 0)

        {

          $driver_review = $this->mobile_model->get_all_details(DRIVER_REVIEW, array('bookingno' => $trip->bookingno,'reviewer_id'=>$trip->driver_id));



          $driver_review_count = $driver_review->num_rows(); 

          if($driver_review_count > 0)

          {

            $driver_review_show_status = true;

            $driver_review_status = true;

          }

          else

          {

            $driver_review_show_status = true;

            $driver_review_status = false;

          }

        }

        else

        {

          $driver_review_show_status = false;

          $driver_review_status = false;

        }



        $host_dis_details = $this->mobile_model->get_all_details(VEHICLE_DIPSUTE, array('user_id' => $trip->user_id, 'vehicle_id' => $trip->product_id, 'booking_no' => $trip->bookingno,'disputer_id'=>$trip->user_id,'dispute_by'=>'Host'));



        if ($host_dis_details->num_rows() > 0) 

        {

          $cancelled_by_host = true;

        }

        else

        {

          $cancelled_by_host = false;

        }



        if($cancelled_by_host == false)

        {

          $dis_details = $this->mobile_model->get_all_details(VEHICLE_DIPSUTE, array('user_id' => $trip->user_id, 'vehicle_id' => $trip->product_id, 'booking_no' => $trip->bookingno));

          

          $time_val = date('Y-m-d');

          $check_in = date("Y-m-d", strtotime($trip->checkin_date));

          $check_out = date("Y-m-d", strtotime($trip->checkout_date));

          $admin = $this->mobile_model->getAdminSettings(ADMIN_SETTINGS);

          $dipute_day = $admin->row()->dispute_days;

          $after_day = strtotime("+" . $dipute_day . "days", strtotime($check_in));

          $out_date = date('Y-m-d', $after_day);

          $service_type = $this->uri->segment(3,0);

          if($service_type == 4){ $hidedays = 'cancel_hide_days_car'; }

          if($service_type == 5){ $hidedays = 'cancel_hide_days_van'; }

          

          $hideCancelDay = $this->config->item($hidedays);

          $totlessDays = $hideCancelDay + 1;

         

          $minus_checkin = strtotime("-" . $totlessDays . "days", strtotime($check_in));

          $checkinBeforeDay = date('Y-m-d', $minus_checkin); 



          if ($dis_details->num_rows() == 0) 

          {

            if (($time_val) <= $out_date) 

            {     //after checked in

              if (($time_val) >= $check_in) 

              {

                $dispute_show_status = true;

                $is_dispute = false;

              }

              else

              {

                if ($time_val <= $checkinBeforeDay) 

                {

                  if ($trip->walletAmount != '0.00' || $trip->is_coupon_used == 'Yes')

                  {

                     

                  }

                  else

                  {

                    $cancel_show_status = true;

                    $is_canceled = false;

                  }

                }

                else

                {

                  //happy stay

                }

              }

            }

            else

            {

              if ($time_val <= $checkinBeforeDay) 

              {

                $cancel_show_status = true;

                $is_canceled = false;

              }

              else

              {

                //happy stay

              }

            }

          }

          else

          {

            if ($dis_details->row()->cancel_status == 1) 

            {

              /*canceled*/ 

              $cancel_show_status = true;

              $is_canceled = true;

            }

            elseif ($dis_details->row()->cancel_status == 0) 

            {

              /*disputed*/

              $dispute_show_status = true;

              $is_dispute = true;

            }            

          }

        }



        $bkng_type = $trip->veh_booking_type; 

        $vehicle_booking_type = explode(",", $bkng_type); 

        if(in_array('1',$vehicle_booking_type) && !in_array('2',$vehicle_booking_type))

        {

          $veh_price = $trip->day_price;

        }

        elseif (!in_array('1',$vehicle_booking_type) && in_array('2',$vehicle_booking_type)) 

        {

          $veh_price = $trip->min_hour_exprice;

        }

        else

        {

          $veh_price = $trip->day_price;

        }



        if($user_currencyCode!=$trip->currency)

        {

          $prop_price = currency_conversion($trip->currency,$user_currencyCode,$veh_price,$trip->currency_cron_id);

        }

        else

        {

          $prop_price = $veh_price;

        }



        if($user_currencyCode!=$trip->user_currencycode)

        {

          $total = currency_conversion($trip->user_currencycode,$user_currencyCode,$trip->totalAmt,$trip->currency_cron_id);

          $secDeposit = currency_conversion($trip->user_currencycode,$user_currencyCode,$trip->secDeposit,$trip->currency_cron_id);

          $serviceFee = currency_conversion($trip->user_currencycode,$user_currencyCode,$trip->serviceFee,$trip->currency_cron_id);

          $sub_total = currency_conversion($trip->user_currencycode,$user_currencyCode,$trip->subTotal,$trip->currency_cron_id);

          $additional_price = currency_conversion($trip->user_currencycode,$user_currencyCode,$trip->additional_sub_total,$trip->currency_cron_id);

        }

        else

        {

          $total = $trip->totalAmt;          

          $secDeposit = $trip->secDeposit;

          $serviceFee = $trip->serviceFee;

          $sub_total = $trip->subTotal;

          $additional_price = $trip->additional_sub_total;

        }

        if($this->paypal_curency!=$user_currencyCode)

        {

          $payable_paypal_total = currency_conversion($trip->user_currencycode,$this->paypal_curency,$trip->totalAmt,$trip->currency_cron_id);

        }

        else

        {

          $payable_paypal_total = $trip->totalAmt;

        }



        $additonal_listings = array();

        $additionalPriceDet = $trip->additional_prices;

        $additionalPriceArr = json_decode($additionalPriceDet,true);

        if(count($additionalPriceArr) > 0 )

        {

          foreach ($additionalPriceArr as $key => $addiprice) 

          {

            $getDet = $this->db->where('id',$key)->get(VEHICLE_ADDITIONAL_PRICE)->row();

            if($language_code == 'en')

            {

              $productTitle=$getDet->title;

            }

            else

            {

              $titleNameField='title_ph';

              if($getDet->$titleNameField=='') {

                $productTitle=$getDet->title;

              }

              else{

                $productTitle=$getDet->$titleNameField;

              }

            }

            $additonal_listings[] = array("title"=>$productTitle,"unit_price"=>$addiprice['price'],"price_type"=>$addiprice['price_type'],"subTotal"=>$addiprice['subTotal']);

          }

        }



        $my_trips[] = array("id"=>intval($trip->id),"vehicle_title"=>$field_prdttitle,"vehicle_price"=>floatval($prop_price),"vehicle_currency_code"=>$property_currency_code,"vehicle_currency_symbol"=>$property_currency_symbol,"bookedon"=>$trip->dateAdded,"bookingno"=>$trip->bookingno,"booking_status"=>$trip->booking_status,"booking_type"=>$trip->booking_type,"approval_status"=>$trip->approval,"checkin"=>$trip->checkin_date,"checkout"=>$trip->checkout_date,"numofdates"=>intval($trip->no_of_days),"numofhours"=>intval($trip->no_of_hours),"address"=>$trip->prd_address,"country"=>$trip->country_name,"state"=>$trip->state_name,"city"=>$trip->city_name,"vehicle_id"=>intval($trip->product_id),"host_name"=>$host_name,"service_fee"=>floatval($serviceFee),"sub_total"=>floatval($sub_total),"security_deposit"=>floatval($secDeposit),"NoofGuest"=>intval($trip->NoofGuest),"cancellation_policy"=>$trip->cancellation_policy,"cancel_percentage"=>$trip->cancellation_percentage,"total"=>floatval($total),"additional_sub_total"=>floatval($additional_price),"paid_currency_code"=>$trip->currencycode,"paid_currency_symbol"=>$paid_currency_symbol,"payable_paypal_currency"=>$this->paypal_curency,"payable_paypal_symbol"=>$this->paypal_symbol,"payable_paypal_total"=>$payable_paypal_total,"vehicle_image"=>$proImage,"is_previous"=>$is_previous,"extend_trips_status"=> $extend_trips_status,"driver_review_show_status"=>$driver_review_show_status,"driver_review_status"=>$driver_review_status,"is_review"=>$is_review,"cancelled_by_host"=>$cancelled_by_host,"cancel_show_status"=>$cancel_show_status,"is_canceled"=>$is_canceled,"dispute_show_status"=>$dispute_show_status,"is_dispute"=>$is_dispute,"guest_id"=>intval($trip->GestId),"guest_email"=>$guestEmail,"host_id"=>intval($trip->renter_id),"additional_listings"=>$additonal_listings,"user_currency_code"=>$user_currencyCode,"user_currency_symbol"=>$user_currency_symbol,"currency_cron_id"=>$trip->currency_cron_id);

      }

    }

    if($trip_details->num_rows() == 0){

      $response = array("status"=>1,"message"=>$this->no_trps_avail,"mytrips"=>$my_trips);

    } else {

      $response = array("status"=>1,"message"=>$this->trps_avail,"mytrips"=>$my_trips);

    }    

    echo json_encode($response,JSON_PRETTY_PRINT);

  }



  /* GET USER ACCOUNT */

  public function user_vehicle_account_details()

  {

    $userid = $_POST['userid'];  

    $rental_type = $_POST['base_id'];

    $language_code = $_POST['lang_code'];      

    $user_currencyCode = $_POST['currency_code'];



    $user_currencySymb = $this->db->select('currency_symbols')->where('currency_type',$_POST['currency_code'])->get(CURRENCY)->row()->currency_symbols;



    if($userid =="") {

      echo json_encode(array('status'=>0,'message'=>$this->parm_missing));

      exit;

    }



    $userDetails = $this->db->query('select * from '.USERS.' where `id`="'.$userid.'"');

    $user_details = array();

    $user_list = array();



    if($userDetails->num_rows() >0) 

    {

      foreach($userDetails->result() as $u) 

      {

        if($u->image != ''){

          $userimg = base_url().'images/users/'.$u->image;

        }else{

          $userimg = base_url().'images/users/profile.png';

        }

        if($u->is_verified=='Yes'){ $is_verified=true; } elseif($u->is_verified =='No'){ $is_verified=false; }else{ $is_verified=""; }

        if($u->id_verified=='Yes'){ $id_verified=true; } elseif($u->id_verified =='No'){ $id_verified=false; }else{ $id_verified=""; }

        if($u->ph_verified=='Yes'){ $ph_verified=true; } elseif($u->ph_verified =='No'){ $ph_verified=false; }else{ $ph_verified=""; }

        if($u->is_brand=='yes'){ $is_brand=true; } elseif($u->is_brand =='no'){ $is_brand=false; }else{ $is_brand=""; }

        if($u->display_lists=='Yes'){ $display_lists=true; } elseif($u->display_lists =='No'){ $display_lists=false; }else{ $display_lists=""; }

        if($u->social_recommend=='yes'){ $social_recommend=true; } elseif($u->social_recommend =='no'){ $social_recommend=false; }else{ $social_recommend=""; }

        if($u->search_by_profile=='yes'){ $search_by_profile=true; } elseif($u->search_by_profile =='no'){ $search_by_profile=false; }else{ $search_by_profile=""; }

        if($u->subscriber=='Yes'){ $subscriber=true; } elseif($u->subscriber =='No'){ $subscriber=false; }else{ $subscriber=""; }



        $user_list[] = array("id"=>intval($u->id),"loginUserType"=>$u->loginUserType,"facebook_id"=>$u->f_id,"google_id"=>$u->google_id,"linkedin_id"=>$u->linkedin_id,"group"=>$u->group,"email"=>$u->email,"status"=>$u->status,"is_verified"=>$is_verified,"id_verified"=>$id_verified,"ph_verified"=>$ph_verified,"is_brand"=>$is_brand,"created"=>$u->created,"last_login_date"=>$u->last_login_date,"last_logout_date"=>$u->last_logout_date,"firstname"=>$u->firstname,"lastname"=>$u->lastname,"description"=>$u->description,"gender"=>$u->gender,"dob_date"=>$u->dob_date,"dob_month"=>$u->dob_month,"dob_year"=>$u->dob_year,"country"=>intval($u->country),"phone_no"=>$u->phone_no,"where_you_live"=>$u->s_city,"request_status"=>$u->request_status,"verify_code"=>$u->verify_code,"feature_product"=>$u->feature_product,"followers_count"=>$u->followers_count,"following_count"=>$u->following_count,"language"=>$u->language,"visibility"=>$u->visibility,"display_lists"=>$display_lists,"email_notifications"=>$u->email_notifications,"notifications"=>$u->notifications,"updates"=>$u->updates,"package_status"=>$u->package_status,"expired_date"=> $u->expired_date,"social_recommend"=>$social_recommend,"search_by_profile"=>$u->search_by_profile,"languages_known"=>$u->languages_known,"accname"=>$u->accname,"accno"=>$u->accno,"bankname"=>$u->bankname,"subscriber"=>$subscriber,"login_hit"=>$u->login_hit,"user_image"=>$userimg);



        $payout[] = array("accname"=>$u->accname,"accno"=>$u->accno,"bankname"=>$u->bankname);

        $notify[] = array("reservation_request"=>$u->notifications,"email_notifications"=>$u->email_notifications);

        $privacy[] = array("search_by_profile"=>$search_by_profile,"social_recommend"=>$social_recommend);

      }

    }



    /* Transaction History starts here */

    $emailQry = $this->mobile_model->get_all_details(USERS, array('id' => $userid));

    $email = $emailQry->row()->email;

    $future_transaction = $this->mobile_model->get_vehicle_future_transaction($email,$rental_type);



    $completed_transaction = $this->mobile_model->get_vehicle_completed_transaction($email,$rental_type);



    $fut_trans = array();

    $comp_trans = array();

    if($completed_transaction->num_rows() >0) 

    {

      foreach($completed_transaction->result() as $comp) 

      {

        $amountToHost = $comp->payable_amount-$comp->paid_cancel_amount;

        if($comp->currency_cron_id==0) { $currencyCronId='';} else { $currencyCronId=$comp->currency_cron_id;}  

        if($comp->user_currencycode != $user_currencyCode) 

        {

          $transAmount = currency_conversion($comp->user_currencycode, $user_currencyCode,  $amountToHost,$currencyCronId);

        }

        else

        {

          $transAmount = $amountToHost;

        }

        $comp_trans[] = array("dateadded"=>date('M d, Y',strtotime($comp->dateAdded)),"transaction_method"=>"Via Bank","transaction_id"=>$comp->transaction_id,"amount"=>floatval($transAmount),"currency_code"=>$user_currencyCode,"currency_symbol"=>$user_currencySymb);

      }

    }



    if($future_transaction->num_rows() >0) 

    {

      foreach($future_transaction->result() as $fut) 

      {

        if($language_code=='en')

        {

           $productTitle=$fut->veh_title;

        }

        else

        {

           $titleNameField='veh_title_ph';

           if($fut->$titleNameField=='') {

               $productTitle=$fut->veh_title;

           }

           else{

               $productTitle=$fut->$titleNameField;

           }

        }



        if($fut->currency_cron_id=='' || $fut->currency_cron_id==0)  { $currencyCronId=''; } else { $currencyCronId=$fut->currency_cron_id;}

        $saved_booking_type=explode(",",$fut->booking_type);

        if(in_array('1', $saved_booking_type) && !in_array('2', $saved_booking_type))

        { 

          $price = $row->day_price;

        }

        elseif (!in_array('1', $saved_booking_type) && in_array('2', $saved_booking_type)) 

        {

          $price = $row->min_hour_exprice;

        }

        else

        { 

          $price = $row->day_price;

        }

        if($fut->user_currencycode != $user_currencyCode)  

        {

          $propPrice  = currency_conversion($fut->user_currencycode,$user_currencyCode,$fut->price,$currencyCronId);

          $propTotAmt = currency_conversion($fut->user_currencycode,$user_currencyCode,$fut->totalAmt,$currencyCronId);

          $propSerFee = currency_conversion($fut->user_currencycode,$user_currencyCode,$fut->guest_fee,$currencyCronId);

          $propHostFee= currency_conversion($fut->user_currencycode,$user_currencyCode,$fut->host_fee,$currencyCronId);

          $propPayable= currency_conversion($fut->user_currencycode,$user_currencyCode,$fut->payable_amount,$currencyCronId);

          $propsecDepst= currency_conversion($fut->user_currencycode,$user_currencyCode,$fut->secDeposit,$currencyCronId);

          $propsubtotal= currency_conversion($fut->user_currencycode,$user_currencyCode,$fut->subTotal,$currencyCronId);

        }

        else

        {

          $propPrice  = $fut->price;

          $propTotAmt = $fut->totalAmt;

          $propSerFee = $fut->guest_fee;

          $propHostFee= $fut->host_fee;

          $propPayable= $fut->payable_amount;

          $propsecDepst= $fut->secDeposit;

          $propsubtotal= $fut->subTotal;

        }



        $fut_trans[] = array("dateadded"=>date('M d, Y',strtotime($fut->dateAdded)),"firstname"=>$fut->firstname,"vehicle_title"=>$productTitle,"vehicle_price"=>floatval($propPrice),"bookingno"=>$fut->Bookingno,"totalAmt"=>floatval($propTotAmt),"service_fee"=>floatval($propSerFee),"host_fee"=>floatval($propHostFee),"security_deposit"=>floatval($propsecDepst),"sub_total"=>floatval($propsubtotal),"payable_amount"=>floatval($propPayable),"currency_code"=>$user_currencyCode,"currency_symbol"=>$user_currencySymb);

      }

    }

    /* Country List starts here */

    $country_list = array();

    $country_query='SELECT id,name FROM '.LOCATIONS.' WHERE status="Active" order by name';

    $active_countries = $this->mobile_model->ExecuteQuery($country_query);

    if($active_countries->num_rows() >0) {

      foreach($active_countries->result() as $cntry) {

        $country_list[] = array("id"=>intval($cntry->id),"country_name"=>$cntry->name);

      }

    } 

    /*  Country List ends here */   

    echo json_encode(array('status'=>1,'message'=>$this->usr_dtls_avail,'accountinfo'=>$user_list,"notifications"=>$notify,"payout_perference"=>$payout,"privacy"=>$privacy,"completed_transaction"=>$comp_trans,"future_transaction"=>$fut_trans,"country_list"=>$country_list),JSON_PRETTY_PRINT);

  }



  public function get_wishlist_contents($user_id = '',$language_code = '',$currency_code = '')

  {

      $condition1 = array("id"=>$user_id);

      $userDetails = $this->mobile_model->get_all_details(USERS,$condition1); 

      $WishListCat = $this->mobile_model->get_list_details_wishlist($user_id);

      $wishlist = array();

      $wishlist = array();

      $img = base_url().'images/site/empty-wishlist.jpg';

      if($WishListCat->num_rows() > 0)

      {

        foreach($WishListCat->result() as $wlist)

        {   /*properties*/

            $hotels = array();

            $offices = array();

            $resorts = array();

            $cars = array();

            $vans = array();

            $restaurants = array();

            $img = base_url().'images/site/empty-wishlist.jpg';

            if($wlist->product_id !='')

            {

              $products=explode(',',$wlist->product_id);

              $productsNotEmy=array_filter($products);

              $CountProduct1=count($productsNotEmy);

              if($CountProduct1 > 0)

              {

                $CountProduct = $this->mobile_model->get_all_details(PRODUCT,array('id'=>end($productsNotEmy)))->num_rows(); 

              }

             

              if($CountProduct > 0)

              { 

                $ProductsImg = $this->mobile_model->get_all_details(PRODUCT_PHOTOS,array('product_id'=>end($productsNotEmy))); 

                if($ProductsImg->row()->product_image!='')

                {

                  $img = base_url().'images/rental/'.$ProductsImg->row()->product_image;

                }

                else

                {

                  $img = base_url().'images/rental/dummyProductImage.jpg';

                }

              } 

              else 

              {

                $img = base_url().'images/empty-wishlist.jpg';

              }

            

              if (count ( $productsNotEmy ) > 0) 

              {

                $product_details = $this->mobile_model->get_product_details_wishlist_one_category ( $productsNotEmy );

                

                if(count($product_details)>0) 

                {

                  

                  foreach($product_details->result() as $p) 

                  {

                    if($p->rental_type == 1)

                    {

                      $wishlist_image  = $this->mobile_model->get_wishlistphoto ( $p->id );

                      $wish_img = array();

                      if(count($wishlist_image)>0) 

                      {

                        foreach($wishlist_image->result() as $product_image) 

                        {

                          $prd_img  ="";

                          if($product_image->product_image !=""){

                            if(strpos($product_image->product_image, 's3.amazonaws.com') > 1) 

                            {

                            $prd_img = $product_image->product_image;

                            } 

                            else  

                            {

                              $prd_img = base_url()."images/rental/".$product_image->product_image;

                            }

                          }

                          $wish_img[] = array("property_image"=>$prd_img);

                        }

                      }

                      $condition = array('currency_type'=>$p->currency);

                      $property_currency_details = $this->mobile_model->get_all_details ( CURRENCY, $condition );

                      $property_currency_symbol = $property_currency_details->row()->currency_symbols;

                      if($userDetails->row()->image !='')

                      {

                        $user_img = base_url().'images/users/'.$userDetails->row()->image;

                      }

                      else

                      {

                        $user_img = base_url().'images/users/user-thumb.png';

                      }

                      $select_prd = "select user_id from fc_product where id='".$p->id."'";

                      $prd_ty = $this->mobile_model->ExecuteQuery($select_prd);

                      foreach($prd_ty->result() as $RW)

                      {

                        $hostId = $RW->user_id;

                      }

                      $user_Currency_code = $currency_code;

                      $propertyCurrency = $p->currency;

                      if($user_Currency_code!=$propertyCurrency)

                      {

                        $propertyPrice = currency_conversion($propertyCurrency,$user_Currency_code,$p->price);

                        $propertyCurCod= $user_Currency_code;

                        $propertyCurSym= $this->db->select('currency_symbols')->where('currency_type',$propertyCurCod)->get(CURRENCY)->row()->currency_symbols;

                      }

                      else

                      {

                        $propertyPrice = $p->price;

                        $propertyCurCod= $p->currency;

                        $propertyCurSym= $property_currency_symbol;

                      }

                      if($language_code == 'en')

                      {

                          $field_title = $p->product_title;

                      }

                      else

                      {

                          $name_Field='product_title_ph';

                          if($p->$name_Field == '') { 

                              $field_title=$p->product_title;

                          }

                          else{

                              $field_title=$p->$name_Field;

                          }

                      }

                      $hotels[] = array("property_id"=>intval($p->id),"property_title"=>

                      $field_title,"property_address"=>$p->address,"room_type"=>$p->room_type,"bedrooms"=>$p->bedrooms,"bathrooms"=>$p->bathrooms,"notes_id"=>intval($p->nid),"notes_desc"=>strip_tags($p->notes),"property_price"=>floatval($propertyPrice),"property_currency_code"=>$propertyCurCod,"property_currency_symbol"=>$propertyCurSym,"host_id"=>intval($hostId),"user_image"=>$user_img,"property_images"=>$wish_img);

                    }



                    if($p->rental_type == 2)

                    {

                      $wishlist_image  = $this->mobile_model->get_wishlistphoto ( $p->id );

                      $wish_img = array();

                      if(count($wishlist_image)>0) 

                      {

                        foreach($wishlist_image->result() as $product_image) 

                        {

                          $prd_img  ="";

                          if($product_image->product_image !=""){

                            if(strpos($product_image->product_image, 's3.amazonaws.com') > 1) 

                            {

                            $prd_img = $product_image->product_image;

                            } 

                            else  

                            {

                              $prd_img = base_url()."images/rental/".$product_image->product_image;

                            }

                          }

                          $wish_img[] = array("property_image"=>$prd_img);

                        }

                      }

                      $condition = array('currency_type'=>$p->currency);

                      $property_currency_details = $this->mobile_model->get_all_details ( CURRENCY, $condition );

                      $property_currency_symbol = $property_currency_details->row()->currency_symbols;

                      if($userDetails->row()->image !='')

                      {

                        $user_img = base_url().'images/users/'.$userDetails->row()->image;

                      }

                      else

                      {

                        $user_img = base_url().'images/users/user-thumb.png';

                      }

                      $select_prd = "select user_id from fc_product where id='".$p->id."'";

                      $prd_ty = $this->mobile_model->ExecuteQuery($select_prd);

                      foreach($prd_ty->result() as $RW)

                      {

                        $hostId = $RW->user_id;

                      }

                      $user_Currency_code = $currency_code;

                      $propertyCurrency = $p->currency;

                      if($user_Currency_code!=$propertyCurrency)

                      {

                        $propertyPrice = currency_conversion($propertyCurrency,$user_Currency_code,$p->price);

                        $propertyCurCod= $user_Currency_code;

                        $propertyCurSym= $this->db->select('currency_symbols')->where('currency_type',$propertyCurCod)->get(CURRENCY)->row()->currency_symbols;

                      }

                      else

                      {

                        $propertyPrice = $p->price;

                        $propertyCurCod= $p->currency;

                        $propertyCurSym= $property_currency_symbol;

                      }

                      if($language_code == 'en')

                      {

                          $field_title = $p->product_title;

                      }

                      else

                      {

                          $name_Field='product_title_ph';

                          if($p->$name_Field == '') { 

                              $field_title=$p->product_title;

                          }

                          else{

                              $field_title=$p->$name_Field;

                          }

                      }

                      $offices[] = array("property_id"=>intval($p->id),"property_title"=>

                      $field_title,"property_address"=>$p->address,"room_type"=>$p->room_type,"bedrooms"=>$p->bedrooms,"bathrooms"=>$p->bathrooms,"notes_id"=>intval($p->nid),"notes_desc"=>strip_tags($p->notes),"property_price"=>floatval($propertyPrice),"property_currency_code"=>$propertyCurCod,"property_currency_symbol"=>$propertyCurSym,"host_id"=>intval($hostId),"user_image"=>$user_img,"property_images"=>$wish_img);

                    }



                    if($p->rental_type == 3)

                    {

                      $wishlist_image  = $this->mobile_model->get_wishlistphoto ( $p->id );

                      $wish_img = array();

                      if(count($wishlist_image)>0) 

                      {

                        foreach($wishlist_image->result() as $product_image) 

                        {

                          $prd_img  ="";

                          if($product_image->product_image !=""){

                            if(strpos($product_image->product_image, 's3.amazonaws.com') > 1) 

                            {

                            $prd_img = $product_image->product_image;

                            } 

                            else  

                            {

                              $prd_img = base_url()."images/rental/".$product_image->product_image;

                            }

                          }

                          $wish_img[] = array("property_image"=>$prd_img);

                        }

                      }

                      $condition = array('currency_type'=>$p->currency);

                      $property_currency_details = $this->mobile_model->get_all_details ( CURRENCY, $condition );

                      $property_currency_symbol = $property_currency_details->row()->currency_symbols;

                      if($userDetails->row()->image !='')

                      {

                        $user_img = base_url().'images/users/'.$userDetails->row()->image;

                      }

                      else

                      {

                        $user_img = base_url().'images/users/user-thumb.png';

                      }

                      $select_prd = "select user_id from fc_product where id='".$p->id."'";

                      $prd_ty = $this->mobile_model->ExecuteQuery($select_prd);

                      foreach($prd_ty->result() as $RW)

                      {

                        $hostId = $RW->user_id;

                      }

                      $user_Currency_code = $currency_code;

                      $propertyCurrency = $p->currency;

                      if($user_Currency_code!=$propertyCurrency)

                      {

                        $propertyPrice = currency_conversion($propertyCurrency,$user_Currency_code,$p->price);

                        $propertyCurCod= $user_Currency_code;

                        $propertyCurSym= $this->db->select('currency_symbols')->where('currency_type',$propertyCurCod)->get(CURRENCY)->row()->currency_symbols;

                      }

                      else

                      {

                        $propertyPrice = $p->price;

                        $propertyCurCod= $p->currency;

                        $propertyCurSym= $property_currency_symbol;

                      }

                      if($language_code == 'en')

                      {

                          $field_title = $p->product_title;

                      }

                      else

                      {

                          $name_Field='product_title_ph';

                          if($p->$name_Field == '') { 

                              $field_title=$p->product_title;

                          }

                          else{

                              $field_title=$p->$name_Field;

                          }

                      }

                      $resorts[] = array("property_id"=>intval($p->id),"property_title"=>

                      $field_title,"property_address"=>$p->address,"room_type"=>$p->room_type,"bedrooms"=>$p->bedrooms,"bathrooms"=>$p->bathrooms,"notes_id"=>intval($p->nid),"notes_desc"=>strip_tags($p->notes),"property_price"=>floatval($propertyPrice),"property_currency_code"=>$propertyCurCod,"property_currency_symbol"=>$propertyCurSym,"host_id"=>intval($hostId),"user_image"=>$user_img,"property_images"=>$wish_img);

                    }

                    

                  }

                }

              }

            }



            /*restaurant*/

            if($wlist->rest_id !='')

            {

              $restaurant=explode(',',$wlist->rest_id); 

              $restaurantNotEmy=array_filter($restaurant);

              $Countrestaurant1=count($restaurantNotEmy);

              if($Countrestaurant1 > 0)

              {

                $CountProduct = $this->mobile_model->get_all_details(RESTAURANT,array('id'=>end($restaurantNotEmy)))->num_rows(); 

              }

           

              if($CountProduct > 0)

              { 

                $restImg = $this->mobile_model->get_all_details(VEHICLE_PHOTOS,array('vehicle_id'=>end($restaurantNotEmy))); 

                if($restImg->row()->product_image!='')

                {

                  $img = base_url().'images/restaurant/'.$restImg->row()->image;

                }

                else

                {

                  $img = base_url().'images/restaurant/dummyProductImage.jpg';

                }

              } 

              else 

              {

                $img = base_url().'images/empty-wishlist.jpg';

              }

            

              if (count ( $restaurantNotEmy ) > 0) 

              {

                $restaurant_details = $this->mobile_model->get_restaurant_details_wishlist_one_category ( $restaurantNotEmy );

                

                if(count($restaurant_details)>0) 

                {

                  $restaurants = array();

                  foreach($restaurant_details->result() as $p) 

                  {

                    $wishlist_restaurantimage  = $this->mobile_model->get_wishlistrestaurantphoto ( $p->id );

                    $wish_rest_img = array();

                    if(count($wishlist_restaurantimage)>0) 

                    {

                      foreach($wishlist_restaurantimage->result() as $product_image) 

                      {

                        $rest_img  ="";

                        if($product_image->image !=""){

                          if(strpos($product_image->image, 's3.amazonaws.com') > 1) 

                          {

                          $rest_img = $product_image->image;

                          } 

                          else  

                          {

                            $rest_img = base_url()."images/restaurant/".$product_image->image;

                          }

                        }

                        $wish_rest_img[] = array("property_image"=>$rest_img);

                      }

                    }

                    $condition = array('currency_type'=>$p->currency);

                    $restaurant_currency_details = $this->mobile_model->get_all_details ( CURRENCY, $condition );

                    $restaurant_currency_symbol = $restaurant_currency_details->row()->currency_symbols;

                    if($userDetails->row()->image !='')

                    {

                      $user_img = base_url().'images/users/'.$userDetails->row()->image;

                    }

                    else

                    {

                      $user_img = base_url().'images/users/user-thumb.png';

                    }

                    $select_prd = "select user_id from fc_product where id='".$p->id."'";

                    $prd_ty = $this->mobile_model->ExecuteQuery($select_prd);

                    foreach($prd_ty->result() as $RW)

                    {

                      $hostId = $RW->user_id;

                    }

                    $user_Currency_code = $currency_code;

                    $propertyCurrency = $p->currency;                   



                    if($user_Currency_code!=$propertyCurrency)

                    {

                      $restaurantPrice = currency_conversion($propertyCurrency,$user_Currency_code,$p->price_per_person);

                      $restaurantCurCod= $user_Currency_code;

                      $restaurantCurSym= $this->db->select('currency_symbols')->where('currency_type',$vehicleCurCod)->get(CURRENCY)->row()->currency_symbols;

                    }

                    else

                    {

                      $restaurantPrice = $p->price_per_person;

                      $restaurantCurCod= $p->currency;

                      $restaurantCurSym= $property_currency_symbol;

                    }

                    if($language_code == 'en')

                    {

                      $field_veh_title = 'rest_title';

                    }

                    else

                    {

                      $field_veh_title = 'rest_title_ph';

                    } 



                    $restaurants[] = array("restaurant_id"=>intval($p->id),"restaurant_title"=>

                    $p->$field_veh_title,"restaurant_address"=>$p->address,"notes_id"=>intval($p->nid),"notes_desc"=>strip_tags($p->notes),"restaurant_price"=>floatval($restaurantPrice),"restaurant_currency_code"=>$restaurantCurCod,"restaurant_currency_symbol"=>$restaurantCurSym,"host_id"=>intval($hostId),"user_image"=>$user_img,"restaurant_images"=>$wish_rest_img);

                  }

                }

              }

            }



            /*vehicles*/

            if($wlist->vehicle_id !='')

            {

              $vehicles=explode(',',$wlist->vehicle_id); 

              $vehiclesNotEmy=array_filter($vehicles);

              $Countvehicle1=count($vehiclesNotEmy);

              if($Countvehicle1 > 0)

              {

                $CountProduct = $this->mobile_model->get_all_details(VEHICLE,array('id'=>end($vehiclesNotEmy)))->num_rows(); 

              }

             

              if($CountProduct > 0)

              { 

                $ProductsImg = $this->mobile_model->get_all_details(VEHICLE_PHOTOS,array('vehicle_id'=>end($vehiclesNotEmy))); 

                if($ProductsImg->row()->product_image!='')

                {

                  $img = base_url().'images/vehicles/'.$ProductsImg->row()->product_image;

                }

                else

                {

                  $img = base_url().'images/vehicles/dummyProductImage.jpg';

                }

              } 

              else 

              {

                $img = base_url().'images/empty-wishlist.jpg';

              }

            

              if (count ( $vehiclesNotEmy ) > 0) 

              {

                $vehicle_details = $this->mobile_model->get_vehicle_details_wishlist_one_category ( $vehiclesNotEmy );

                

                if(count($vehicle_details)>0) 

                {

                  /*$vehicle = array();*/

                  foreach($vehicle_details->result() as $p) 

                  {

                    if($p->vehicle_type == 4)

                    {

                      $wishlist_vehicleimage  = $this->mobile_model->get_wishlistvehiclephoto ( $p->id );

                      $wish_veh_img = array();

                      if(count($wishlist_vehicleimage)>0) 

                      {

                        foreach($wishlist_vehicleimage->result() as $product_image) 

                        {

                          $veh_img  ="";

                          if($product_image->image !=""){

                            if(strpos($product_image->image, 's3.amazonaws.com') > 1) 

                            {

                            $veh_img = $product_image->image;

                            } 

                            else  

                            {

                              $veh_img = base_url()."images/vehicles/".$product_image->image;

                            }

                          }

                          $wish_veh_img[] = array("property_image"=>$veh_img);

                        }

                      }

                      $condition = array('currency_type'=>$p->currency);

                      $vehicle_currency_details = $this->mobile_model->get_all_details ( CURRENCY, $condition );

                      $vehicle_currency_symbol = $vehicle_currency_details->row()->currency_symbols;

                      if($userDetails->row()->image !='')

                      {

                        $user_img = base_url().'images/users/'.$userDetails->row()->image;

                      }

                      else

                      {

                        $user_img = base_url().'images/users/user-thumb.png';

                      }

                      $select_prd = "select user_id from fc_product where id='".$p->id."'";

                      $prd_ty = $this->mobile_model->ExecuteQuery($select_prd);

                      foreach($prd_ty->result() as $RW)

                      {

                        $hostId = $RW->user_id;

                      }

                      $user_Currency_code = $currency_code;

                      $propertyCurrency = $p->currency;

                      $saved_booking_type=explode(",",$p->booking_type);

                      if(in_array('1', $saved_booking_type) && !in_array('2', $saved_booking_type))

                      { 

                        $price = $p->day_price;

                      }

                      elseif (!in_array('1', $saved_booking_type) && in_array('2', $saved_booking_type)) 

                      {

                        $price = $p->min_hour_exprice;

                      }

                      elseif (in_array('1', $saved_booking_type) && in_array('2', $saved_booking_type)) 

                      {

                        $price = $p->day_price;

                      }

                      else

                      { 

                        $price = $p->day_price;

                      }



                      if($user_Currency_code!=$propertyCurrency)

                      {

                        $vehiclePrice = currency_conversion($propertyCurrency,$user_Currency_code,$price);

                        $vehicleCurCod= $user_Currency_code;

                        $vehicleCurSym= $this->db->select('currency_symbols')->where('currency_type',$vehicleCurCod)->get(CURRENCY)->row()->currency_symbols;

                      }

                      else

                      {

                        $vehiclePrice = $price;

                        $vehicleCurCod= $p->currency;

                        $vehicleCurSym= $property_currency_symbol;

                      }

                      if($language_code == 'en')

                      {

                          $field_veh_title = $p->veh_title;

                      }

                      else

                      {

                          $name_veh_Field='veh_title_ph';

                          if($p->$name_veh_Field == '') { 

                              $field_veh_title=$p->veh_title;

                          }

                          else{

                              $field_veh_title=$p->$name_veh_Field;

                          }

                      }

                      

                      $cars[] = array("vehicle_id"=>intval($p->id),"vehicle_title"=>

                      $field_veh_title,"vehicle_address"=>$p->address,"notes_id"=>intval($p->nid),"notes_desc"=>strip_tags($p->notes),"vehicle_price"=>floatval($vehiclePrice),"vehicle_currency_code"=>$vehicleCurCod,"vehicle_currency_symbol"=>$vehicleCurSym,"host_id"=>intval($hostId),"user_image"=>$user_img,"vehicle_images"=>$wish_veh_img);

                    }



                    if($p->vehicle_type == 5)

                    {

                      $wishlist_vehicleimage  = $this->mobile_model->get_wishlistvehiclephoto ( $p->id );

                      $wish_veh_img = array();

                      if(count($wishlist_vehicleimage)>0) 

                      {

                        foreach($wishlist_vehicleimage->result() as $product_image) 

                        {

                          $veh_img  ="";

                          if($product_image->image !=""){

                            if(strpos($product_image->image, 's3.amazonaws.com') > 1) 

                            {

                            $veh_img = $product_image->image;

                            } 

                            else  

                            {

                              $veh_img = base_url()."images/vehicles/".$product_image->image;

                            }

                          }

                          $wish_veh_img[] = array("property_image"=>$veh_img);

                        }

                      }

                      $condition = array('currency_type'=>$p->currency);

                      $vehicle_currency_details = $this->mobile_model->get_all_details ( CURRENCY, $condition );

                      $vehicle_currency_symbol = $vehicle_currency_details->row()->currency_symbols;

                      if($userDetails->row()->image !='')

                      {

                        $user_img = base_url().'images/users/'.$userDetails->row()->image;

                      }

                      else

                      {

                        $user_img = base_url().'images/users/user-thumb.png';

                      }

                      $select_prd = "select user_id from fc_product where id='".$p->id."'";

                      $prd_ty = $this->mobile_model->ExecuteQuery($select_prd);

                      foreach($prd_ty->result() as $RW)

                      {

                        $hostId = $RW->user_id;

                      }

                      $user_Currency_code = $currency_code;

                      $propertyCurrency = $p->currency;

                      $saved_booking_type=explode(",",$p->booking_type);

                      if(in_array('1', $saved_booking_type) && !in_array('2', $saved_booking_type))

                      { 

                        $price = $p->day_price;

                      }

                      elseif (!in_array('1', $saved_booking_type) && in_array('2', $saved_booking_type)) 

                      {

                        $price = $p->min_hour_exprice;

                      }

                      elseif (in_array('1', $saved_booking_type) && in_array('2', $saved_booking_type)) 

                      {

                        $price = $p->day_price;

                      }

                      else

                      { 

                        $price = $p->day_price;

                      }



                      if($user_Currency_code!=$propertyCurrency)

                      {

                        $vehiclePrice = currency_conversion($propertyCurrency,$user_Currency_code,$price);

                        $vehicleCurCod= $user_Currency_code;

                        $vehicleCurSym= $this->db->select('currency_symbols')->where('currency_type',$vehicleCurCod)->get(CURRENCY)->row()->currency_symbols;

                      }

                      else

                      {

                        $vehiclePrice = $price;

                        $vehicleCurCod= $p->currency;

                        $vehicleCurSym= $property_currency_symbol;

                      }

                      if($language_code == 'en')

                      {

                          $field_veh_title = $p->veh_title;

                      }

                      else

                      {

                          $name_veh_Field='veh_title_ph';

                          if($p->$name_veh_Field == '') { 

                              $field_veh_title=$p->veh_title;

                          }

                          else{

                              $field_veh_title=$p->$name_veh_Field;

                          }

                      }



                      $vans[] = array("vehicle_id"=>intval($p->id),"vehicle_title"=>

                      $field_veh_title,"vehicle_address"=>$p->address,"notes_id"=>intval($p->nid),"notes_desc"=>strip_tags($p->notes),"vehicle_price"=>floatval($vehiclePrice),"vehicle_currency_code"=>$vehicleCurCod,"vehicle_currency_symbol"=>$vehicleCurSym,"host_id"=>intval($hostId),"user_image"=>$user_img,"vehicle_images"=>$wish_veh_img);

                    }

                  }

                }

              }

            }



          /*experiences*/

            

            $experience = array();

            if($wlist->experience_id !='')

            {

              $experiences=explode(',', $wlist->experience_id);

              $experienceNotEmy=array_filter($experiences);

              $CountProduct1=count($experienceNotEmy);

              

              if (count ( $experienceNotEmy ) > 0) 

              {

                $experience_details = $this->mobile_model->get_experience_details_wishlist_one_category ( $experienceNotEmy );              

                if(count($experience_details)>0) 

                {

                  

                  foreach($experience_details->result() as $p) 

                  {

                    $wishlist_image  = $this->mobile_model->get_wishlist_experience_photo( $p->experience_id );

                    $wish_imgs = array();

                    if(count($wishlist_image)>0) 

                    {

                      foreach($wishlist_image->result() as $product_image) 

                      {

                        $exp_img  ="";

                        if($product_image->product_image !="")

                        {

                          if(strpos($product_image->product_image, 's3.amazonaws.com') > 1) 

                          {

                          $exp_img = $product_image->product_image;

                          } 

                          else  

                          {

                            $exp_img = base_url()."images/experience/".$product_image->product_image;

                          }

                        }

                        $wish_imgs[] = array("experience_image"=>$exp_img);

                      }

                    }



                    $condition = array('currency_type'=>$p->currency);

                    $experience_currency_details = $this->mobile_model->get_all_details ( CURRENCY, $condition );

                    $experience_currency_symbol = $experience_currency_details->row()->currency_symbols;

                    if($userDetails->row()->image !='')

                    {

                      $user_img = base_url().'images/users/'.$userDetails->row()->image;

                    }

                    else

                    {

                      $user_img = base_url().'images/users/user-thumb.png';

                    }

                    $select_exp = "select user_id from fc_experiences where experience_id='".$experience_id."'";

                    $prd_ty = $this->mobile_model->ExecuteQuery($select_exp);

                    

                    $experience[] = array("experience_id"=>intval($p->experience_id),"experience_title"=>

                    $p->experience_title,"experience_address"=>$p->address,"notes_desc"=>strip_tags($p->review_content),"experience_price"=>floatval($p->price),"experience_currency_symbol"=>$experience_currency_symbol,"host_id"=>intval($user_id),"user_image"=>$user_img,"expeience_images"=>$wish_imgs);

                  }

                }                

              }

            }

            else

            {

              $experience = array();

            }



          $wishlist[] = array("wishlist_id"=>intval($wlist->id),"wishlist_title"=>$wlist->name,"wishlist_image"=>$img,"hotel_details"=>$hotels,"office_details"=>$offices,"resort_details"=>$resorts,"cars_details"=>$cars,"vans_details"=>$vans,"restaurant_details"=>$restaurants,"experience_details"=>$experience);     

                

        }

        $json_encode = json_encode(array("status"=>1,"message"=>$this->wish_ritrved,"wishlist"=>$wishlist),JSON_PRETTY_PRINT);    

      } 

      else 

      {

        $json_encode = json_encode(array("status"=>1,"message"=>$this->dta_found,"wishlist"=>$wishlist),JSON_PRETTY_PRINT);

      }

      return array("response" => $json_encode);

  }



  public function create_vehicle_wishlist()

  {

    $rental_type = $this->input->post('base_id');

    $userid = $this->input->post ( 'userid' );

    $wishlist_title = $this->input->post ( 'wishlist_title' );

    $vehicle_id = $this->input->post ( 'vehicle_id' );

    $language_code = $this->input->post('lang_code');

    $notes = $this->input->post('notes');

    $currency_code = $this->input->post('currency_code');

    if($userid =="" || $wishlist_title =="")

    {

      echo json_encode(array("status"=>0,"message"=>$this->parm_missing));

      exit;

    } 

    if($userid == 0)

    {

      echo json_encode(array("status"=>0,"message"=>$this->parm_missing));

      exit;

    } 

    else 

    {
      /*checking already exist*/
      $this->data ['WishListCat'] = $this->mobile_model->check_users_lists($userid, $wishlist_title);

      if ($this->data ['WishListCat']->num_rows() > 0) {
        echo json_encode(array("status"=>0,"message"=>$this->title_exist));
        exit;
      }

      $data = $this->mobile_model->add_wishlist_category(array(

        'rental_type' => $rental_type,

          'user_id' => $userid,

          'name' => ucfirst($wishlist_title),

          'vehicle_id'=>$vehicle_id

      ));



      $data = $this->mobile_model->add_wishlist_notes(array(

        'rental_type' => $rental_type,

          'user_id' => $userid,

          'notes' => $notes,

          'vehicle_id'=>$vehicle_id

      ));

    

      $result = $this->get_wishlist_contents($userid,$language_code,$currency_code);

      print_r($result['response']); exit();

    }

  }



  /* ADD Property to  WISHLIST */

  public function mobile_add_wishlist_vehicle()

  {

    $wishlist_id = $this->input->post ( 'wishlist_id' );

    $user_id = $this->input->post ( 'user_id' );

    $vehicle_id = $this->input->post ( 'vehicle_id' );

    $currency_code = $this->input->post('currency_code');

    $language_code = $this->input->post ( 'lang_code' );

    $rental_type = $this->input->post ( 'base_id' );

    $notes = $this->input->post ( 'notes' );

    if($wishlist_id =="" || $user_id=="" || $user_id==0 || $wishlist_id ==0 || $vehicle_id =="" || $vehicle_id ==0) 

    {

      echo json_encode(array("status"=>0,"message"=>$this->parm_missing));

      exit;

    } 

    else 

    {

      $wishlist = array();

      $select_qrys = "select fc_lists.id from fc_lists where id = ".$wishlist_id." and user_id = ".$user_id;

      $list_values = $this->mobile_model->ExecuteQuery($select_qrys);



      if($list_values->num_rows()>0) 

      {

        $update_wishlist_details = $this->mobile_model->update_wishlist_vehicle($vehicle_id,$user_id,$wishlist_id);



        $data = $this->mobile_model->add_wishlist_notes(array(

        'rental_type' => $rental_type,

          'user_id' => $user_id,

          'notes' => $notes,

          'vehicle_id'=>$vehicle_id

        ));

        

        $result = $this->get_wishlist_contents($user_id,$language_code,$currency_code);

        print_r($result['response']); exit();    

      } 

      else

      {

        echo json_encode(array("status"=>0,"message"=>$this->no_dta_found,"wishlist"=>$wishlist));

        exit;

      }

    }

  }



  public function mobile_remove_wishlist_vehicle()

  {

    $wishlist_id = $this->input->post ('wishlist_id');

    $user_id = $this->input->post ('user_id');

    $vehicle_id = $this->input->post ('vehicle_id');

    $language_code = $this->input->post ('lang_code');

    $currency_code = $this->input->post('currency_code');

    if($user_id == "" || $user_id == 0  || $vehicle_id == "" || $vehicle_id == 0) 

    {

      echo json_encode(array("status"=>0,"message"=>$this->parm_missing));

      exit;

    } 

    else 

    {

      $wishlist = array();

      $select_qrys = "select fc_lists.id from fc_lists where  find_in_set(".$vehicle_id.",vehicle_id) and user_id = ".$user_id;

      $list_values = $this->mobile_model->ExecuteQuery($select_qrys);

      

      if($list_values->num_rows()>0) 

      {

        $update_wishlist_details = $this->mobile_model->remove_wishlist_vehicle($vehicle_id,$user_id,$wishlist_id);

        

        $result = $this->get_wishlist_contents($user_id,$language_code,$currency_code);

        print_r($result['response']); exit();

        

      } 

      else 

      {

        echo json_encode(array("status"=>1,"message"=>$this->no_dta_found,"wishlist"=>$wishlist),JSON_PRETTY_PRINT);

        exit;

      }

    }

  }



  /*function getDatesFromRange($start, $end)

  {

      $dates = array($start);

      while (end($dates) < $end) {

          $dates [] = date('Y-m-d', strtotime(end($dates) . ' +1 day'));

      }

      return $dates;

  }*/



  public function vehicle_listview()

  {

    $id = $_POST['userid'];

    $vehicle_type = $_POST['base_id'];

    $language_code = $_POST['lang_code'];

    $user_currencyCode = $_POST['currency_code'];

    if($id == "" || $vehicle_type == "") {

      $json_encode = json_encode(array("status"=>0,"message"=>$this->parm_missing),JSON_PRETTY_PRINT);

      echo $json_encode; 

      exit;

    }

    $check_paypal_enabled = $this->db->select('id')->where('gateway_name','Paypal IPN')->where('status','Enable')->get(PAYMENT_GATEWAY)->num_rows(); 
    if($check_paypal_enabled == 1){ $paypal_status = true; }else{ $paypal_status = false; }
    $check_stripe_enabled = $this->db->select('id')->where('gateway_name','Stripe')->where('status','Enable')->get(PAYMENT_GATEWAY)->num_rows();
    if($check_stripe_enabled == 1){ $stripe_status = true; }else{ $stripe_status = false; }
    $check_credit_enabled = $this->db->select('id')->where('gateway_name','Credit Card')->where('status','Enable')->get(PAYMENT_GATEWAY)->num_rows();
    if($check_credit_enabled == 1){ $creditcard_status = true; }else{ $creditcard_status = false; }
    $check_paymaya_enabled = $this->db->select('id')->where('gateway_name','PayMaya')->where('status','Enable')->get(PAYMENT_GATEWAY)->num_rows();
    if($check_paymaya_enabled == 1){ $paymaya_status = true; }else{ $paymaya_status = false; }

    if($vehicle_type == 4) { $seotag = 'car-listing'; }

    if($vehicle_type == 5) { $seotag = 'van-listing'; }

    $hosting_commission='SELECT * FROM '.COMMISSION.' WHERE seo_tag="'.$seotag.'"';

    $hosting_commission_status=$this->mobile_model->ExecuteQuery($hosting_commission);


    $vehiclearr = array();

    $search_res1 = $this->mobile_model->get_vehicle_dashboard_list($id, $vehicle_type);

    if($search_res1->num_rows() != 0) 

    {

      foreach($search_res1->result() as $res1)

      {

        $total_steps = 11;

        if ($res1->make_id != "") { $total_steps--; }

        if ($res1->latitude != "") { $total_steps--; }

        if ($res1->booking_type != "") { $total_steps--;  }

        if ($res1->calendar_checked != "") { $total_steps--;  }

        if ($res1->veh_title != "") { $total_steps--;  }

        if ($res1->important_info != "" ||  $res1->terms_condition != ""  ||  $res1->car_rules != ""  ||  $res1->other_things != "" )

        {

          $total_steps--;

        }

        if ($res1->image != "") {  $total_steps--;  } 

        if ($res1->list_name != "") { $total_steps--; }                                       

        if ($res1->listings != "") { $total_steps--; }

        if ($res1->driver_type != "") { $total_steps--;  }

        if ($res1->cancellation_policy != "") { $total_steps--; }



        $payable = false;

        $paid_status = false;

        $calendar_status = false;

      

        $check_stripe_enabled = $this->db->select('id')->where('gateway_name','Stripe')->where('status','Enable')->get(PAYMENT_GATEWAY)->num_rows();

        $check_paypal_enabled = $this->db->select('id')->where('gateway_name','Paypal IPN')->where('status','Enable')->get(PAYMENT_GATEWAY)->num_rows();

        $pmtGateways=$check_stripe_enabled+$check_paypal_enabled;



        if($res1->status == 'publish' )

        {

          $calendar_status = true;

        }



        if($total_steps != 0 )

        {

          $listing_status = $total_steps.' steps to list';

        }

        else

        {

          if($res1->payment_status == 'paid')

          {

            $paid_status = true;

            /*$payment_status = "Amount paid";*/

          }

          else

          {

            if($pmtGateways == 0 )

            {

              //Disabled

            }

            else

            {

              if($hosting_commission_status->row()->status == 'Inactive')

              {

                //no commission

              }

              else

              {

                if($res1->payment_status == 'paid')

                {

                  $paid_status = true;

                  /*$payment_status = "Amount paid";*/

                }

                else

                {

                  $payable = true;

                }

              }

            }

          }

        }



        if($total_steps != 0 ){

          $listing_status = $total_steps.' steps to list';

        } 

        else 

        {

          if($res1->status == 'publish' && $total_steps == 0){

            $listing_status = 'Listed';

          } elseif($res1->status == 'unpublish' && $total_steps == 0 && $hosting_commission_status->row()->status == 'Inactive'){

            $listing_status = 'Pending';

          } elseif($res1->status == 'unpublish' && $total_steps == 0 && $hosting_commission_status->row()->status == 'Active' && $res1->payment_status == 'paid'){

            $listing_status = 'Paid';

          } elseif($res1->status == 'unpublish' && $total_steps == 0 && $hosting_commission_status->row()->status == 'Active') {

            $listing_status = 'Pay';

          }

        } 



        if($res1->image != '')

        {

          $p_img = explode('.',$res1->image);

          $suffix = strrchr($res1->image, "."); 

          $pos = strpos  ( $res1->image  , $suffix); 

          $name = substr_replace ($res1->image, "", $pos); 

          $pro_img = $name.''.$suffix;           

          $proImage = base_url().'images/vehicles/'.$pro_img;

        }

        else

        {

          $proImage = base_url().'images/vehicles/dummyProductImage.jpg';

        }



        if($language_code == 'en')

        {

          $productTitle=$res1->veh_title;

        }

        else

        {

          $titleNameField='veh_title_ph';

          if($res1->$titleNameField=='') {

            $productTitle=$res1->veh_title;

          }

          else{

            $productTitle=$res1->$titleNameField;

          }

        }
        
        $saved_booking_type=explode(",",$res1->booking_type);
        if(in_array('1', $saved_booking_type) && !in_array('2', $saved_booking_type))
        { 
          $veh_price = $res1->day_price;
        }
        elseif (!in_array('1', $saved_booking_type) && in_array('2', $saved_booking_type)) 
        {
          $veh_price = $res1->min_hour_exprice;
        }
        elseif (in_array('1', $saved_booking_type) && in_array('2', $saved_booking_type)) 
        {             
          /*day price*/
          $veh_price = $res1->day_price;
          /*****/
        }
        else
        { 
          $veh_price = $res1->day_price;
        }


        $prop_curSymb = $this->mobile_model->get_all_details(CURRENCY, array('currency_type'=>$res1->currency))->row()->currency_symbols;
        
        $admin = $this->mobile_model->get_all_details(ADMIN, array('admin_type' => 'super'));
        $data = $admin->row();
        $admin_currencyCode = trim($data->admin_currencyCode);

        if($user_currencyCode != $admin_currencyCode)
        {
          $app_hosting_price = currency_conversion($admin_currencyCode,$user_currencyCode, $hosting_commission_status->row()->commission_percentage);
        }
        else
        {
          $app_hosting_price = $hosting_commission_status->row()->commission_percentage;
        }
        

        $vehiclearr[] = array('base_id'=>$res1->vehicle_type,'remaining_steps'=>$total_steps,'vehicle_image'=>$proImage,'vehicle_title'=>$productTitle,'vehicle_price'=>$veh_price,'commission_type'=>$hosting_commission_status->row()->promotion_type,'commission'=>$hosting_commission_status->row()->commission_percentage,'hosting_price'=>$hosting_commission_status->row()->commission_percentage,'app_hosting_price'=>$app_hosting_price,'vehicle_status'=>$listing_status,'vehicle_id'=>intval($res1->id),'paid_status'=>$paid_status,'payable'=>$payable,'created_date'=>$res1->createdAt,'host_id'=>$res1->user_id,'vehicle_currency_code'=>$res1->currency,'vehicle_currency_symbol'=>$prop_curSymb,'calendar_status'=>$calendar_status);

      } 

    }



    /*my reservation*/

    $prop_curcode = $user_currencyCode;

    $prop_curSymb = $this->mobile_model->get_all_details(CURRENCY, array('currency_type'=>$prop_curcode))->row()->currency_symbols;  

    $my_reservation =array();

    $reservationDetails = $this->mobile_model->future_vehicle_bookings($id, $vehicle_type);

    if($reservationDetails->num_rows()>0)

    {

      foreach($reservationDetails->result() as $trip)

      {

        if($trip->currency_cron_id=='' || $trip->currency_cron_id==0){ $currencyCronId=''; }else{ $currencyCronId=$trip->currency_cron_id; }

        $paymentstatus = $this->mobile_model->get_all_details(VEHICLE_PAYMENT,array('Enquiryid'=>$trip->EnqId));

        $chkval = $paymentstatus->num_rows();

        $host_cancellation_show_status = false;

        $host_cancelled = false;

        if($chkval==1) 

        { 

          $payment_status = "Paid";

          $host_cancellation_show_status = true;

          $check_guest_cancelled = $this->mobile_model->get_all_details(VEHICLE_DIPSUTE, array('user_id' => $trip->GestId, 'vehicle_id' => $trip->product_id, 'booking_no' => $trip->Bookingno));

          if ($check_guest_cancelled->num_rows() > 0) 

          {

            $host_cancelled = true;

          }

        }

        else {

          $payment_status = "Pending";

        }



        if($trip->approval=='Pending') 

        {

          $approval_status = "Approval Pending";

        }

        else 

        {

          if($trip->approval == 'Accept'){

            $approval_status = "Accepted";

          } else {

            $approval_status = "Declined";

          }

        }



        if($trip->image != '')

        {

          if($trip->loginUserType == 'google')

          {

            $userImage = base_url().'images/users/'.$trip->image;

          }elseif($trip->image == '' ){ 

            $userImage = base_url().'images/users/profile.png';

          } else { 

            $userImage = base_url().'images/users/'.$trip->image;

          }

        }

        else

        {

          $userImage = base_url().'images/users/profile.png';

        }



        if($trip->firstname != ''){

          $host_name = $trip->firstname;

        } else {

          $host_name ="";

        }



        if($trip->booking_type == 'hourly')

        {

          $book_date = date('M d, Y H:i', strtotime($trip->checkin_date)) . " - " . date('M d, Y H:i', strtotime($trip->checkout_date));

        }

        else

        {

          $book_date =  date('M d', strtotime($trip->checkin_date)) . " - " . date('M d, Y', strtotime($trip->checkout_date));

        }   



        $secDeposit = floatval($trip->secDeposit);

        $total = $trip->subTotal + $trip->secDeposit +$trip->serviceFee+$trip->additional_sub_total;

        $paid_currency_symbol = $this->mobile_model->get_all_details(CURRENCY, array('currency_type'=>$trip->user_currencycode))->row()->currency_symbols;

        $bkng_type = $trip->vehicle_booking_type; 

        $vehicle_booking_type = explode(",", $bkng_type); 

        if(in_array('1',$vehicle_booking_type) && !in_array('2',$vehicle_booking_type))

        {

          $veh_price = $trip->min_hour_exprice;

        }

        elseif (!in_array('1',$vehicle_booking_type) && in_array('2',$vehicle_booking_type)) 

        {

          $veh_price = $trip->day_price;

        }

        else

        {

          $veh_price = $trip->day_price;

        }

        if($user_currencyCode!=$trip->user_currencycode)

        {

          $prop_price   = currency_conversion($trip->user_currencycode,$user_currencyCode, $veh_price,$currencyCronId);

          $prop_servicefee= currency_conversion($trip->user_currencycode,$user_currencyCode, $trip->serviceFee,$currencyCronId);

          $prop_subTotal  = currency_conversion($trip->user_currencycode,$user_currencyCode, $trip->subTotal,$currencyCronId);

          $security_depos = currency_conversion($trip->user_currencycode,$user_currencyCode, $trip->secDeposit,$currencyCronId);

          $prop_grandtotal= currency_conversion($trip->user_currencycode,$user_currencyCode, $total,$currencyCronId);

          $additional_amount = currency_conversion($trip->user_currencycode,$user_currencyCode, $trip->additional_sub_total,$currencyCronId);

        }

        else

        {

          $prop_price   = $veh_price;

          $prop_servicefee= $trip->serviceFee;

          $prop_subTotal  = $trip->subTotal;

          $security_depos = $trip->secDeposit;

          $prop_grandtotal= $total;

          $additional_amount = $trip->additional_sub_total;

        }



        if($language_code == 'en')

        {

          $field_prdttitle = $trip->veh_title;

        }

        else

        {

          $prdttitle_field = 'veh_title_ph';

          if($trip->$prdttitle_field == '') { 

                $field_prdttitle = $trip->veh_title;

            }

            else{

                $field_prdttitle = $trip->$prdttitle_field;

            }

        }



        $my_reservation[] = array("id"=>$trip->EnqId,"vehicle_title"=>$field_prdttitle,"vehicle_price"=>floatval($prop_price),"vehicle_currency_code"=>$prop_curcode,"vehicle_currency_symbol"=>$prop_curSymb,"booking_dates"=>$book_date,"checkin"=>$trip->checkin_date,"checkout"=>$trip->checkout_date,"num_of_days"=>$trip->no_of_days,"num_of_hours"=>$trip->no_of_hours,"address"=>$trip->address,"country"=>$trip->country_name,"state"=>$trip->state_name,"city"=>$trip->city_name,"post_code"=>$trip->post_code,"vehicle_id"=>$trip->product_id,"service_fee"=>floatval($prop_servicefee),"sub_total"=>floatval($prop_subTotal),"security_deposit"=>floatval($security_depos),"additional_price"=>$additional_amount,"NoofGuest"=>$trip->NoofGuest,"cancellation_policy"=>$trip->cancellation_policy,"cancellation_percentage"=>$trip->cancellation_percentage,"total"=>floatval($prop_grandtotal),"payment_status"=>$payment_status,"approval_status"=>$approval_status,"paid_currency_code"=>$trip->user_currencycode,"paid_currency_symbol"=>$paid_currency_symbol,"user_name"=>$host_name,"bookingno"=>$trip->Bookingno,"loginUserType"=>$trip->loginUserType,"user_image"=>$userImage,"host_cancellation_show_status"=>$host_cancellation_show_status,"host_cancelled"=>$host_cancelled);

      }

    }

    if($search_res1->num_rows() == 0 && $reservationDetails->num_rows() ==0) 

    {      

      $vehiclearr = array();

      $json_encode = json_encode(array("status"=>1,"message"=>$this->no_list_avail,'stripe_status'=>$stripe_status,'creditcard_status'=>$creditcard_status,'paypal_status'=>$paypal_status,'paymaya_status'=>$paymaya_status,"vehicle_listing"=>$vehiclearr,"my_reservation"=>$my_reservation),JSON_PRETTY_PRINT);      

    } 

    else

    {

      $json_encode = json_encode(array("status"=>1,"message"=>$this->list_avail,'stripe_status'=>$stripe_status,'creditcard_status'=>$creditcard_status,'paypal_status'=>$paypal_status,'paymaya_status'=>$paymaya_status,"vehicle_listing"=>$vehiclearr,"my_reservation"=>$my_reservation),JSON_PRETTY_PRINT);

    }

    echo $json_encode;

  }



  public function mobile_hostcancellation()

  {

    $vehicle_type = $this->input->post('base_id');

    $language_code = $this->input->post('lang_code');

    $currency_code = $this->input->post('currency_code');

    $vehicle_id = $this->input->post('vehicle_id');

    $cancel_percentage = $this->input->post('cancellation_percentage');

    $bookingNo = $this->input->post('bookingNo');

    $email = $this->input->post('email');

    $user_id = $this->input->post('user_id');

    $disputer_id = $this->input->post('disputer_id');



    if($vehicle_id != "" && $cancel_percentage != "" && $bookingNo != "" && $email != "" && $user_id != "" && $disputer_id != "" && $vehicle_type != "")

    {



      $excludeArr = array('trip_url', 'dispute_message', 'bookingNo');

      $dataArr = array('vehicle_type'=>$vehicle_type, 'vehicle_id' => $vehicle_id, 'cancellation_percentage' => $cancel_percentage, 'message' => $this->input->post('message'), 'user_id' => $user_id, 'booking_no' => $bookingNo, 'email' => $email, 'disputer_id' => $disputer_id, 'cancel_status' => 1,'dispute_by'=>'Host','status'=>'Accept');



      /* Mail to Host Start*/

      $newsid = '72';

      $template_values = $this->mobile_model->get_newsletter_template_details($newsid);

      if ($template_values['sender_name'] == '' && $template_values['sender_email'] == '') {

          $sender_email = $this->data['siteContactMail'];

          $sender_name = $this->data['siteTitle'];

      } else {

          $sender_name = $template_values['sender_name'];

          $sender_email = $template_values['sender_email'];

      }

      //HOST DETAILS

      $condition = array('id' => $disputer_id);

      $hostDetails = $this->mobile_model->get_all_details(USERS, $condition);

      $uid = $hostDetails->row()->id;

      $hostname = $hostDetails->row()->user_name;

      $host_email = $hostDetails->row()->email;

      

      //GUEST DETAILS

      $getEnquiryDet = $this->mobile_model->get_all_details(VEHICLE_ENQUIRY, array('Bookingno' => $bookingNo))->row();

      $guest_id = $getEnquiryDet->user_id;

      $EnquiryId=$getEnquiryDet->id;

      $checkInDate = $getEnquiryDet->checkin_date;

      $checkOutDate = $getEnquiryDet->checkout_date;

      $Enquser_id=$getEnquiryDet->user_id;

      $Enqsell_id=$getEnquiryDet->renter_id;

      $Enqvehicle_id=$getEnquiryDet->vehicle_id;

      

      $condition = array('id' => $guest_id);

      $custDetails = $this->mobile_model->get_all_details(USERS, $condition);

      $cust_name = $custDetails->row()->user_name;

      $cust_email = $custDetails->row()->email;



      //email_title

      $condition = array('id' => $vehicle_id);

      $prdDetails = $this->mobile_model->get_all_details(VEHICLE, $condition);

      $prd_title = $prdDetails->row()->veh_title;

      $reason = $this->input->post('message');

      $booking_no = $bookingNo;

      $email_values = array('from_mail_id' => $sender_email, 'to_mail_id' => $cust_email, 'subject_message' => $template_values ['news_subject'], 'body_messages' => $message);

      $reg = array('logo' => $this->data['logo'],'host_name' => $hostname, 'cust_name' => $cust_name, 'prd_title' => $prd_title, 'reason' => $reason, 'booking_no' => $booking_no);

      $message = $this->load->view('newsletter/ToGuestCancelBooking' . $newsid . '.php', $reg, TRUE);

      /*echo $message;*/

      $this->load->library('email', $config);

      $this->email->from($email_values['from_mail_id'], $sender_name);

      $this->email->to($email_values['to_mail_id']);

      $this->email->subject($email_values['subject_message']);

      $this->email->set_mailtype("html");

      $this->email->message($message);

      try {

          $this->email->send();

          $returnStr ['msg'] = 'Successfully registered';

          $returnStr ['success'] = '1';

      } catch (Exception $e) {

          echo $e->getMessage();

      }

      /* Mail to Host End*/



      /* MAIL TO ADMIN */ 

      

      $newsid='73'; 

      $template_values=$this->mobile_model->get_newsletter_template_details($newsid);

      if($template_values['sender_name']=='' && $template_values['sender_email']=='')

      {

        $sender_email=$this->data['siteContactMail'];

        $sender_name=$this->data['siteTitle'];

      }

      else

      {

        $sender_name=$template_values['sender_name'];

        $sender_email=$template_values['sender_email'];

      } 

      

      $reg = array('logo' => $this->data['logo'],'host_name' => $hostname, 'guest_name' => $cust_name, 'prd_title' => $prd_title, 'reason' => $reason, 'booking_no' => $booking_no);

          $message = $this->load->view('newsletter/ToAdminCancelBooking' . $newsid . '.php', $reg, TRUE);

      /*echo $message;*/

      $this->load->library('email'); 

      $this->email->set_mailtype($email_values['mail_type']);

      $this->email->from($email_values['from_mail_id'], $sender_name);

      $this->email->to($sender_email);

      $this->email->subject($email_values['subject_message']);

      $this->email->message($message); 

      try {

            $this->email->send();

            $returnStr ['msg'] = 'Successfully registered';

            $returnStr ['success'] = '1';

          } catch (Exception $e) {

              echo $e->getMessage();

          }

      /* EOF MAIL TO ADMIN */ 



      $this->mobile_model->simple_insert(VEHICLE_DIPSUTE,$dataArr);



      $UpdateArr=array('cancelled'=>'Yes');

      $Condition=array('vehicle_id'=>$vehicle_id,'Bookingno'=>$bookingNo);

      $this->mobile_model->update_details(VEHICLE_ENQUIRY,$UpdateArr,$Condition);



      $up_Q =  "delete from fc_vehicle_bookings_dates WHERE tot_checked_in='" . $checkInDate. "' AND tot_checked_out='".$checkOutDate."' AND  vehicle_id=".$vehicle_id;

      $this->mobile_model->ExecuteQuery($up_Q);



      $get_paid_amount = $this->mobile_model->get_all_details(VEHICLE_PAYMENT,array('EnquiryId'=>$EnquiryId,'user_id'=>$Enquser_id,'sell_id'=>$Enqsell_id,'vehicle_id'=>$Enqvehicle_id));

      $paidAmount = $get_paid_amount->row()->price;



      $UpdateCommissionArr=array('paid_cancel_amount'=>$paidAmount,'dispute_by'=>'Host','disputer_id'=>$disputer_id);

      $ConditionCommission=array('booking_no'=>$bookingNo);

      $this->mobile_model->update_details(COMMISSION_TRACKING,$UpdateCommissionArr,$ConditionCommission);



      $json_encode = json_encode(array("status"=>200,"message" => $this->succ_cancelled),JSON_PRETTY_PRINT);

      echo $json_encode;



    }

    else

    {

      $json_encode = json_encode(array("status"=>0,"message" => $this->parm_missing),JSON_PRETTY_PRINT);

      echo $json_encode;

    }

  }



  public function get_hostcancellation()

  {

    $user_id = $this->input->post ('user_id');

    $vehicle_id = $this->input->post ('vehicle_id');

    $booking_no = $this->input->post ('booking_no');

    $language_code = $this->input->post ('lang_code');



    if($user_id =="" || $booking_no =="") 

    {

      echo json_encode(array("status"=>0,"message"=>$this->parm_missing));

      exit;

    } 

    else

    {

      $dispute_all = $this->mobile_model->get_veh_host_cancellation($user_id,$vehicle_id,$booking_no);  

      $your_cancellation = array();

      if($dispute_all->num_rows()>0)

      {

          foreach($dispute_all->result() as $dispute) 

          {

            if($dispute->image == '') 

            {

              $img_url = base_url().'images/vehicle/dummyProductImage.png';

            }

            else 

            {

              $img_url = base_url().'images/vehicle/'.$dispute->image;

            } 



            $dispute_date = date('d-m-Y',strtotime($dispute->created_date));

            if($$language_code == 'en')

           {

               $productTitle=$dispute->veh_title;

           }

           else

           {

               $titleNameField='veh_title_ph';

               if($dispute->$titleNameField=='') {

                   $productTitle=$dispute->veh_title;

               }

               else{

                   $productTitle=$dispute->$titleNameField;

               }

           }

            $your_cancellation[] = array("vehicle_title"=>$productTitle,"dispute"=>$dispute->message,"cancelled_date"=>$dispute_date,"vehicle_image"=>$img_url,"address"=>$dispute->city);           

        }

        echo json_encode(array("status"=>1,"message"=>$this->succ,"your_cancellation"=>$your_cancellation),JSON_PRETTY_PRINT);

        exit;

      } 

      else

      {

        echo json_encode(array("status"=>0,"message"=>$this->no_dta_found,"your_cancellation"=>$your_cancellation),JSON_PRETTY_PRINT);

        exit;

      }

    }

  }



  //devaraj_oct5

   public function Hosting_Paymentstripe()
  {

    $product_id = $this->input->post('vehicle_id');

    $host_id = $this->input->post('user_id');

    $vehicle_type = $this->input->post('base_id');



    $host_payment = $this->mobile_model->get_all_details(VEHICLE_HOSTPAYMENT, array('vehicle_id' => $product_id, 'host_id' => $host_id));

    if ($host_payment->num_rows() > 0) 

    {

        $delete_failed_payment = 'DELETE FROM ' . VEHICLE_HOSTPAYMENT . ' WHERE vehicle_id=' . $product_id . ' AND host_id=' . $host_id;

        $this->mobile_model->ExecuteQuery($delete_failed_payment);

    }

    $loginUserId = $host_id;

    $admin = $this->mobile_model->get_all_details(ADMIN, array('admin_type' => 'super'));

    $data = $admin->row();

    $admin_currencyCode = trim($data->admin_currencyCode);

    $getPrdDetails = $this->mobile_model->get_all_details(VEHICLE, array('id' => $product_id));

    $theCurrency = $getPrdDetails->row()->currency;



    if ($theCurrency != $admin_currencyCode) 

    {

        $unit_price = convertCurrency($admin_currencyCode, $theCurrency, 1);

    } 

    else

    {

        $unit_price = 1;

    }



    $paymentArr = array('vehicle_type'=>$vehicle_type,'vehicle_id' => $product_id, 'amount' => $this->input->post('productPrice'), 'host_id' => $loginUserId, 'payment_status' => 'Pending', 'payment_type' => $this->input->post('payment_method'), 'currency_code' => $admin_currencyCode, 'currency_code_host' => $theCurrency,'commission' => $this->input->post('commission'), 'commission_type' => $this->input->post('commission_type'), 'hosting_price' => $this->input->post('commission'));



    $this->mobile_model->simple_insert(VEHICLE_HOSTPAYMENT, $paymentArr);

    $totalAmount = $this->input->post('commission');



    define("StripeDetails", $this->config->item('payment_1'));

    $StripDetVal = unserialize(StripeDetails);

    $StripeVals = unserialize($StripDetVal['settings']);

    require_once('./stripe/lib/Stripe.php');

    $secret_key = $StripeVals['secret_key'];

    $publishable_key = $StripeVals['publishable_key'];

    $stripe = array("secret_key" => $secret_key, "publishable_key" => $publishable_key);

    Stripe::setApiKey($stripe['secret_key']);



    $stripeToken = Stripe_Token::create(

      array(

        "card" => array(

          "name" => '',

          "number" => $this->input->post('cardnumber'),

          "exp_month" => $this->input->post('CCExpMonth'),

          "exp_year" => $this->input->post('CCExpYear'),

          "cvc" => $this->input->post('credit_card_identifier')

        )

      )

    );



    $token = $stripeToken['id'];  

    $amounts = $totalAmount * 100;



    try 

    {



      $customer = Stripe_Customer::create(array("card" => $token, "description" => "Product Purhcase for " . $this->config->item('email_title'), "email" => $this->input->post('email')));



      Stripe_Charge::create(array("amount" => $amounts, # amount in cents, again



          "currency" => $this->data['currencyType'], "customer" => $customer->id));



      $hostPrice= $totalAmount;

      $bookingId = 'EN' . time();

      $this->data['payment_gross'] = $hostPrice;

      $this->data['bookingId'] = $bookingId; 

       $this->data['rental_type'] = $vehicle_type;         

      $dataArr = array('payment_status' => 'paid','bookingId'=>$this->data['bookingId'] );

      $condition = array('vehicle_type' => $vehicle_type,'vehicle_id' => $product_id);

      $this->mobile_model->update_details(VEHICLE_HOSTPAYMENT, $dataArr, $condition);

      //MAIL TO HOST AND ADMIN

      $this->hostPayment_mail($bookingId);

      $json_encode = json_encode(array("status"=>1,"message"=>$this->succ_paid));

      echo $json_encode;



    } catch (Exception $e) {

        $error = $e->getMessage();

        $json_encode = json_encode(array("status"=>0,"message"=>$this->payt_failed,"error" => $error));

        echo $json_encode;

    }

  }



  public function hostPayment_mail($bookingId)

    {

        /* Mail Function starts */

        $this->data['paymentdetail'] = $this->mobile_model->view_vehicle_byBookingId($bookingId);



        /*$this->data['paymentdetail'] = $this->product_model->view_vehiclepmtDet_byBookingId($bookingId);*/



        $hostemail = $this->data['paymentdetail']->row()->email;

        $hostname = $this->data['paymentdetail']->row()->firstname;

        $prdname = $this->data['paymentdetail']->row()->prd_name;

        $amount = $this->data['paymentdetail']->row()->hosting_price;

        $created = $this->data['paymentdetail']->row()->created;

        $type_id = $this->data['paymentdetail']->row()->vehicle_type;

        if($type_id == 4){ $product_type =  'Car'; }else{ $product_type =  'Van'; }

        $dateAndTime = $created;

        $cdata = '';

        $ctime = '';

        $newsid = '26';

        $template_values = $this->mobile_model->get_newsletter_template_details($newsid);

        $adminnewstemplateArr = array('email_title' => $this->config->item('email_title'), 'logo' => $this->data['logo'], 'hostname' => $hostname, 'prdname' => $prdname, 'amount' => $amount, 'product_type' => $product_type, 'currency_s' => $this->session->userdata('currency_s'), 'currency_type' => $this->session->userdata('currency_type'));

        extract($adminnewstemplateArr);

        //echo "<pre>"; print_r($adminnewstemplateArr);

        if ($template_values ['sender_name'] == '' && $template_values ['sender_email'] == '') {

            $sender_email = $this->config->item('site_contact_mail');

            $sender_name = $this->config->item('email_title');

        } else {

            $sender_name = $template_values ['sender_name'];

            $sender_email = $template_values ['sender_email'];

        }

        $this->load->library('email');

        for ($i = 1; $i <= 3; $i++) {

            if ($i == 1) {

                $to_host['to_host'] = 1;

                $to_admin['to_admin'] = 0;

                $reg = array_merge($adminnewstemplateArr, $to_host, $to_admin);

                $message = $this->load->view('newsletter/Property Host Payment Success Host' . $newsid . '.php', $reg, TRUE); //Listing pay from host and mail to host

                $email_values = array('mail_type' => 'html', 'from_mail_id' => $sender_email, 'mail_name' => $sender_name, 'to_mail_id' => $hostemail, 'cc_mail_id' => $template_values ['sender_email'], 'subject_message' => $template_values['news_subject'], 'body_messages' => $message);

                $this->email->from($email_values['from_mail_id'], $sender_name);

                $this->email->to($email_values['to_mail_id']);

                $this->email->subject($email_values['subject_message']);

                $this->email->set_mailtype("html");

                $this->email->message($message);

                try {

                    $this->email->send();

                    //echo "success1";

                    $returnStr ['msg'] = 'Success';

                    $returnStr ['success'] = '1';

                } catch (Exception $e) {

                    //echo $e->getMessage();

                }

            } elseif ($i == 2) {

                $newsid = '27';

                $to_host['to_host'] = 0;

                $to_admin['to_admin'] = 1;

                $reg = array_merge($adminnewstemplateArr, $to_host, $to_admin);

                $message1 = $this->load->view('newsletter/Property Host Payment Success Admin' . $newsid . '.php', $reg, TRUE); //Listing pay from host to mail admin

                $email_values = array('mail_type' => 'html', 'from_mail_id' => $sender_email, 'mail_name' => $sender_name, 'to_mail_id' => $sender_email, 'subject_message' => $template_values['news_subject'], 'body_messages' => $message1);

                $this->email->from($email_values['from_mail_id'], $sender_name);

                $this->email->to($email_values['to_mail_id']);

                $this->email->subject($email_values['subject_message']);

                $this->email->set_mailtype("html");

                $this->email->message($message1);

                try {

                    $this->email->send();

                    //echo "success2";

                    $returnStr ['msg'] = 'Success';

                    $returnStr ['success'] = '1';

                } catch (Exception $e) {

                    //echo $e->getMessage();

                }

            }

        }

        /* Mail Function ends */

    }



  public function Hosting_Paymentpaypal()

  {

    $transId = $this->input->post('txn_id');

    $Pray_Email = $this->input->post('payer_email');

    $payment_gross = $this->input->post('payment_gross');

    $currencySymbol = $this->input->post('currency_symbol');

    $currencyCode = $this->input->post('currency_code');

    $property_id = $this->input->post('vehicle_id');
    

    $condition_user = array('id'=>$property_id);

    $productdetail = $this->mobile_model->get_all_details(VEHICLE, $condition_user);

    $renter_host_id = $productdetail->row()->user_id;

    $rental_type = $productdetail->row()->vehicle_type;



    $condition = array('currency_type'=>$currencyCode);

    $currency_details = $this->mobile_model->get_all_details(CURRENCY, $condition);

    $currency_symbol = $currency_details->row()->currency_symbols;

    $default_currency_code = $currency_details->row()->currency_type;



    if($default_currency_code != $currencyCode)

    {

      $payment_gross1 = convertCurrency($currencyCode,$default_currency_code,$payment_gross);

    }

    else

    {

      $payment_gross1 = $payment_gross;

    }



    $condition12 = array('vehicle_id'=>$property_id);

    $hostpayment_details = $this->mobile_model->get_all_details(VEHICLE_HOSTPAYMENT, $condition12);

    $bookingId = 'EN'.time();

   

    if($hostpayment_details->num_rows()>0) 

    {

      $admin = $this->user_model->get_all_details(ADMIN, array('admin_type' => 'super'));

      $data = $admin->row();

      $admin_currencyCode = trim($data->admin_currencyCode);

          

      $dataArr = array('vehicle_type'=>$rental_type,'vehicle_id'=>$property_id,'paypal_txn_id' => $transId,'paypal_email' => $Pray_Email,'payment_status'=>'paid','host_id'=>$renter_host_id,'amount'=>$payment_gross1,'payment_type'=>'Paypal','currency_code' => $admin_currencyCode,'currency_code_host' => $currencyCode,/*'unitPerCurrencyHost' => $unit_price,*/'commission'=>$payment_gross1,'commission_type'=>'flat','hosting_price'=>$payment_gross1,'bookingid'=>$bookingId);

      

      $condition=array('vehicle_id'=>$property_id); 

      

      $this->mobile_model->update_details(VEHICLE_HOSTPAYMENT,$dataArr,$condition);

     /* $dataArrPrd=array('status'=>'Publish');

      $condition_product_query=array('id'=>$property_id); 

      $this->mobile_model->update_details(VEHICLE,$dataArrPrd,$condition_product_query);*/

    }

    else

    {

      $admin = $this->mobile_model->get_all_details(ADMIN, array('admin_type' => 'super'));

      $data = $admin->row();

      $admin_currencyCode = trim($data->admin_currencyCode);

            

      $dataArr = array('vehicle_type'=>$rental_type,'vehicle_id'=>$property_id,'paypal_txn_id' => $transId,'paypal_email' => $Pray_Email,'payment_status'=>'paid','host_id'=>$renter_host_id,'amount'=>$payment_gross1,'payment_type'=>'Paypal','currency_code' => $admin_currencyCode,'currency_code_host' => $currencyCode,/*'unitPerCurrencyHost' => $unit_price,*/'commission'=>$payment_gross1,'commission_type'=>'flat','hosting_price'=>$payment_gross1,'bookingId'=>$bookingId);

 

      $this->mobile_model->simple_insert(VEHICLE_HOSTPAYMENT,$dataArr);

      /*$dataArrPrd=array('status'=>'Publish');

      $condition_product_query=array('id'=>$property_id); 

      $this->mobile_model->update_details(VEHICLE,$dataArrPrd,$condition_product_query);*/

    }

    $this->hostPayment_mail($transId);

    $response[] = array("currencycode" =>$currencyCode,"total_price" =>floatval($payment_gross),"booking_no"=>$bookingId);

    $json_encode = json_encode(array("status"=>1,"message"=>$this->list_succ_paid,"payment_success"=>$response));

    

    echo $json_encode;

    exit;

  }

  public function Hosting_Paymentpaymaya()

  {

    $transId = $this->input->post('txn_id');

    $Pray_Email = $this->input->post('payer_email');

    $payment_gross = $this->input->post('payment_gross');

    $currencySymbol = $this->input->post('currency_symbol');

    $currencyCode = $this->input->post('currency_code');

    $property_id = $this->input->post('vehicle_id');

    

    $condition_user = array('id'=>$property_id);

    $productdetail = $this->mobile_model->get_all_details(VEHICLE, $condition_user);

    $renter_host_id = $productdetail->row()->user_id;

    $rental_type = $productdetail->row()->vehicle_type;



    $condition = array('currency_type'=>$currencyCode);

    $currency_details = $this->mobile_model->get_all_details(CURRENCY, $condition);

    $currency_symbol = $currency_details->row()->currency_symbols;

    $default_currency_code = $currency_details->row()->currency_type;



    if($default_currency_code != $currencyCode)

    {

      $payment_gross1 = convertCurrency($currencyCode,$default_currency_code,$payment_gross);

    }

    else

    {

      $payment_gross1 = $payment_gross;

    }



    $condition12 = array('vehicle_id'=>$property_id);

    $hostpayment_details = $this->mobile_model->get_all_details(VEHICLE_HOSTPAYMENT, $condition12);

    $bookingId = 'EN'.time();

   

    if($hostpayment_details->num_rows()>0) 

    {

      $admin = $this->user_model->get_all_details(ADMIN, array('admin_type' => 'super'));

      $data = $admin->row();

      $admin_currencyCode = trim($data->admin_currencyCode);

          

      $dataArr = array('vehicle_type'=>$rental_type,'vehicle_id'=>$property_id,'txn_id' => $transId,'payment_status'=>'paid','host_id'=>$renter_host_id,'amount'=>$payment_gross1,'payment_type'=>'Paymaya','currency_code' => $admin_currencyCode,'currency_code_host' => $currencyCode,/*'unitPerCurrencyHost' => $unit_price,*/'commission'=>$payment_gross1,'commission_type'=>'flat','hosting_price'=>$payment_gross1,'bookingid'=>$bookingId);

      

      $condition=array('vehicle_id'=>$property_id); 

      

      $this->mobile_model->update_details(VEHICLE_HOSTPAYMENT,$dataArr,$condition);

      /*$dataArrPrd=array('status'=>'Publish');

      $condition_product_query=array('id'=>$property_id); 

      $this->mobile_model->update_details(VEHICLE,$dataArrPrd,$condition_product_query);*/

    }

    else

    {

      $admin = $this->mobile_model->get_all_details(ADMIN, array('admin_type' => 'super'));

      $data = $admin->row();

      $admin_currencyCode = trim($data->admin_currencyCode);

            

      $dataArr = array('vehicle_type'=>$rental_type,'vehicle_id'=>$property_id,'txn_id' => $transId,'payment_status'=>'paid','host_id'=>$renter_host_id,'amount'=>$payment_gross1,'payment_type'=>'Paymaya','currency_code' => $admin_currencyCode,'currency_code_host' => $currencyCode,/*'unitPerCurrencyHost' => $unit_price,*/'commission'=>$payment_gross1,'commission_type'=>'flat','hosting_price'=>$payment_gross1,'bookingId'=>$bookingId);

 

      $this->mobile_model->simple_insert(VEHICLE_HOSTPAYMENT,$dataArr);

      /*$dataArrPrd=array('status'=>'Publish');

      $condition_product_query=array('id'=>$property_id); 

      $this->mobile_model->update_details(VEHICLE,$dataArrPrd,$condition_product_query);*/

    }

    $this->hostPayment_mail($transId);

    $response[] = array("currencycode" =>$currencyCode,"total_price" =>floatval($payment_gross),"booking_no"=>$bookingId);

    $json_encode = json_encode(array("status"=>1,"message"=>$this->list_succ_paid,"payment_success"=>$response));

    

    echo $json_encode;

    exit;

  }


public function vehicle_search()
{
    $parent_select_qry = "select id,attribute_name,attribute_name_ph,status from fc_attribute where status='Active'";
    $parent_list_values = $this->mobile_model->ExecuteQuery($parent_select_qry);
    
    $rental_type = $_POST['base_id'];
    $language_code = $_POST['lang_code'];
    $user_id = $_POST['user_id'];
    $getted_city = $this->input->post('city');
    $chkin = $this->input->post('checkIn');
    $chkot = $this->input->post('checkOut');

    if($getted_city == "")
    {
      $json_encode = json_encode(array("status"=>0,"message"=>$this->parm_missing));
      echo $json_encode; 
      exit();
    }

    if ($rental_type == 4) { 
      $parent_id = '77';
    }

    if ($rental_type == 5) {
      $parent_id = '66';
    }

    $attribute = array();
    $car_make = array();
    $car_model = array();
    $car_type = array();
    $listing_data = array();
    $vehicle_lists = array();

    $conditions = array('status'=>'Active','vehicle_type'=>$_POST['base_id']);
    $car_space = $this->mobile_model->get_all_details(MAKE_MASTER, $conditions);

    if(count($car_space)>0) 
    {
      $carvalueArr = array();
      foreach($car_space->result() as $pro) 
      {
        if($language_code == 'en')
        {
          $makeNameField=$pro->make_name;
        }
        else
        {
          $titlemakeField='make_name_ph';
          if($pro->$titlemakeField == '') { 
               $makeNameField=$pro->make_name;
          }
          else{
               $makeNameField=$pro->$titlemakeField;
          }
        }
        $carvalueArr[] = array("child_id" =>$pro->id,"child_name"=>$makeNameField,"parent_name"=>"Make"); 
      }
      $car_make[]  = array("option_name"=>"Make","options"=>$carvalueArr);
    }

    $conditions = array('status'=>'Active','vehicle_type'=>$_POST['base_id']);
    $car_modal_space = $this->mobile_model->get_all_details(MODEL_MASTER, $conditions);
    if(count($car_modal_space)>0) 
    {
      $carvalueArrmdl = array();
      foreach($car_modal_space->result() as $modalpro) 
      {       
        if($language_code == 'en')
        {
           $modelNameField=$modalpro->model_name;
        }
        else
        {
           $titleNameField='model_name_ph';
           if($modalpro->$titleNameField == '') 
           { 
              $modelNameField=$modalpro->model_name;
           }
           else
           {
              $modelNameField=$modalpro->$titleNameField;
           }
        }  
              
        $carvalueArrmdl[] = array("child_id" =>$modalpro->id,"child_name"=>$modelNameField,"parent_name"=>"Model");
      }
      $car_model[]  = array("option_name"=>"Model","options"=>$carvalueArrmdl);
    }

    $conditions = array('status'=>'Active','vehicle_type'=>$_POST['base_id']);
    $car_type_space = $this->mobile_model->get_all_details(TYPE_MASTER, $conditions);

    if(count($car_type_space)>0) 
    {
      $carvalueArrtyp = array();
      foreach($car_type_space->result() as $typepro) 
      {
        
        if($language_code == 'en')
        {
          $modelNameField=$typepro->type_name;
        }
        else
        {
          $titleNameField='type_name_ph';
          if($typepro->$titleNameField == '') { 
              $modelNameField=$typepro->$titleNameField;
          }
          else{
              $modelNameField=$typepro->$titleNameField;
          }
        }  
        
        $carvalueArrtyp[] = array("child_id" =>$typepro->id,"child_name"=>$modelNameField,"parent_name"=>"Type");    
      }
      $car_type[]  = array("option_name"=>"Type","options"=>$carvalueArrtyp);
    }
    
    /* Features of amenties,extras ,wifi and so on */
    if($parent_list_values->num_rows()>0) 
    {
      foreach($parent_list_values->result() as $parent_value) 
      {
        $select_qrys = "select fc_list_values.id,list_value,list_value_ph,list_id,fc_attribute.id as attr_id,attribute_name,attribute_name_ph,image from fc_list_values left join fc_attribute  on fc_attribute.id = fc_list_values.list_id where fc_list_values.status='Active' and fc_list_values.rental_type = '".$rental_type."' and list_id = ".$parent_value->id;
        $list_values = $this->mobile_model->ExecuteQuery($select_qrys);
        if($list_values->num_rows()>0) 
        {
          if($language_code == 'en')
          {
              $parent_attribute_name  = $parent_value->attribute_name;
          }
          else
          {
              $attribute_name_parent  = 'attribute_name_ph';
              if($parent_value->$attribute_name_parent == '') { 
                  $parent_attribute_name=$parent_value->attribute_name;
              }
              else{
                  $parent_attribute_name=$parent_value->$attribute_name_parent;
              }
          }
          $listvalueArr = array();
          foreach($list_values->result() as $list_value) 
          {
            
            if($parent_value->id == $list_value->list_id) 
            {
              if($language_code == 'en')
              {
                  $field_list_value=$list_value->list_value;
                  $field_attribute_name  = $list_value->attribute_name;
              }
              else
              {
                  $list_value_Field='list_value_ph';
                  if($list_value->$list_value_Field == '') { 
                      $field_list_value=$list_value->list_value;
                  }
                  else{
                      $field_list_value=$list_value->$list_value_Field;
                  }

                  $attribute_name_field  = 'attribute_name_ph';
                  if($list_value->$attribute_name_field == '') { 
                      $field_attribute_name=$list_value->attribute_name;
                  }
                  else{
                      $field_attribute_name=$list_value->$attribute_name_field;
                  }
              }

              $listvalueArr[] = array("child_id" =>$list_value->id,"child_name"=>$field_list_value,"child_image"=>base_url()."images/attribute/".$list_value->image,"parent_name"=>$field_attribute_name,"parent_id"=>$list_value->attr_id);
            }
          }
          $attribute[]  = array("option_id"=>$parent_value->id,"option_name"=>$parent_attribute_name,"options"=>$listvalueArr);
        } 
      }
    }

    $room_type_query = "";
    $property_query = "";
    $find_in_set_categories = "";
    $category_query = "";
    $restrict_product_id_query = "";
    $price_array = array();
    $session_currency = $this->input->post('currency_code');
    $google_map_api = $this->config->item('google_developer_key');
    $bing_map_api = $this->config->item('bing_developer_key'); 
    $type_of_booking = $this->input->post('type_of_booking');
    $pricemin = $this->input->post('f_p_min');
    $pricemax = $this->input->post('f_p_max');

    if($this->input->post('type_of_booking')=='daily')
    {
      $datefrom = $this->input->post('checkIn').' 00:00:00';
      $dateto = $this->input->post('checkOut').' 23:00:00';
      $booking_type_qry =  " AND FIND_IN_SET('1', p.booking_type) ";
    }
    elseif($this->input->post('type_of_booking')=='hourly')
    {
      $datefrom = $this->input->post('checkIn').' '.date('H:i:s',strtotime($this->input->post('checkIn_time')));
      $dateto = $this->input->post('checkOut').' '.date('H:i:s',strtotime($this->input->post('checkOut_time')));
      $booking_type_qry =  " AND FIND_IN_SET('2', p.booking_type) ";
    }
    else
    {
      $datefrom = $this->input->post('checkin').' 00:00:00';
      $dateto = $this->input->post('checkout').' 23:00:00';
    }

    $page = $this->input->post('page_number');
    if (!$page) {
      $page = 0;
    }

    $searchPerPage = /*$this->config->item('site_pagination_per_page')*/10;
    $guests = $this->input->post('guests');
    $vehicleType = $this->input->post('vehicleType');
    $vehicleMake = $this->input->post('vehicleMake');
    $listvalue = $this->input->post('listvalue');
       
    
    /* Getting address data */
    $address = str_replace(' ', '+', $getted_city);
    $address_details = $this->get_address_bound($address, $google_map_api, $bing_map_api);
    
    $minLat = ($address_details['minLat']!="")?$address_details['minLat']:"0.00000";
    $lat = ($address_details['lat']!="")?$address_details['lat']:"0.00000";
    $long = ($address_details['long']!="")?$address_details['long']:"0.00000";
    $minLong = ($address_details['minLong']!="")?$address_details['minLong']:"0.00000";
    $maxLat = ($address_details['maxLat']!="")?$address_details['maxLat']:"0.00000";
    $maxLong = ($address_details['maxLong']!="")?$address_details['maxLong']:"0.00000";
    if(($minLat == 0.00000 && $maxLat == 0.00000) || ($minLong == 0.00000 && $maxLong == 0.00000))
    {
      $json_encode = json_encode(array("status"=>0,"message"=>"address not found"),JSON_PRETTY_PRINT);
      echo $json_encode; exit();
    }
    else
    {
      $whereLat = 'AND (pa.lat BETWEEN "' . $minLat . '" AND "' . $maxLat . '" ) AND (pa.lang BETWEEN "' . $minLong . '" AND "' . $maxLong . '" )';
    }
    /* close- Getting address data */

   
    if (($datefrom != '') && ($dateto != '')) 
    {
      $DateStart = date("Y-m-d H:i:s", strtotime($datefrom));
      $DateEnd = date("Y-m-d H:i:s", strtotime($dateto));
      $this->db->select('vehicle_id');
      $this->db->from(VEHICLE_BOOKING_DATES);
      $this->db->where('tot_checked_in <=', $DateStart);
      $this->db->where('tot_checked_out >=', $DateEnd);
      $this->db->group_by('vehicle_id');
      $restrick_booking_query = $this->db->get();

      $product_restrick_id = "";
      if ($restrick_booking_query->num_rows() != 0) 
      {
        $restrick_booking_result = $restrick_booking_query->result();
        foreach ($restrick_booking_result as $restrick_data) {
          $product_restrick_id .= "'" . $restrick_data->vehicle_id . "',";
        }
        $product_restrick_id .= '}';
        
        $restrict_product_id = str_replace(',}', '', $product_restrick_id);
        $restrict_product_id_query = " AND p.id NOT IN(" . $restrict_product_id . ")";
      }
    } 

    if ($guests != '' && $guests != '0') {

      if (strpos($guests, "+") != '') {

        $guests = str_replace('+', '', $guests);

        $guest_query = " AND (lc.child_name =" . $guests ." AND lc.parent_id = " . $parent_id .") ";

      } else {

        $guest_query = " AND (lc.child_name >=" . $guests ." AND lc.parent_id = " . $parent_id .")";

      }

    } else {

      $guest_query = " AND (lc.child_name >='1' AND lc.parent_id = " . $parent_id .")";

    }

    if ($pricemax != '' && $pricemin != '') {
      $price_min = $pricemin;
      $price_max = $pricemax + 5;
    } else {
      $price_min = 0;
      $price_max = 50000000;
    }

    $vehicleTypes = explode(',',$vehicleType); 
    if ($vehicleTypes) 
    {
      $room_values_count = 0;
      $room_checked_id = "";
      foreach ($vehicleTypes as $room_checked_values) {
        if ($room_checked_values != '') {
          $room_values_count = 1;
          $room_checked_id .= "'" . trim($room_checked_values) . "',";
        }
      }
      $room_checked_id .= "}";
      $room_check_id = str_replace(",}", "", $room_checked_id);
      if ($room_values_count == 1) $room_type_query = " and p.type_id IN (" . $room_check_id . ")";
    }  

    $vehicleMakes = explode(',',$vehicleMake); 
    if ($vehicleMakes) 
    {
      $make_values_count = 0;
      $make_checked_id = "";
      foreach ($vehicleMakes as $make_checked_values) {
        if ($make_checked_values != '') {
          $make_values_count = 1;
          $make_checked_id .= "'" . trim($make_checked_values) . "',";
        }
      }

      $make_checked_id .= "}";
      $make_check_id = str_replace(",}", "", $make_checked_id);
      if ($make_values_count == 1) $make_type_query = " and p.make_id IN (" . $make_check_id . ")";
    }  

    
    $listvalues = explode(',',$listvalue); 
    if (array_filter($listvalues)) 
    {
      $find_in_set_categories .= '(';
      foreach ($listvalues as $list) {
        if ($list != '') $find_in_set_categories .= ' FIND_IN_SET("' . $list . '", p.list_name) OR';
      }
    }

    if ($find_in_set_categories != '') {
      $find_in_set_categories = substr($find_in_set_categories, 0, -2);
      $category_query = ' ' . $find_in_set_categories . ') AND';
    }


    $search = '(p.vehicle_type = "'.$rental_type.'" AND p.status="publish" ' . $whereLat . $guest_query . $room_type_query . $make_type_query . $property_query . $restrict_product_id_query . $booking_type_qry . ') AND ' . $category_query; 
    $Start = /*ceil($page * $searchPerPage)*/0;

    $productList = $this->mobile_model->view_vehicle_details_sitemapview($search, $Start, $searchPerPage);
    /*echo $this->db->last_query(); exit();*/
    $vehicleListArr = array();

    if($productList->num_rows() == 0)
    {
      $json_encode = json_encode(array("status"=>0,"message"=>$this->no_veh_avail));
      echo $json_encode; 
      exit();
    }
    else
    {
      /* Loading wish-list */
      $wishlists = $this->mobile_model->get_all_details(LISTS_DETAILS, array('user_id' => $user_id));
      $wish_list_array = array();
      foreach ($wishlists->result() as $wish) {
        $wish_list_array = array_merge($wish_list_array, explode(',', $wish->vehicle_id));
      }
      /* close- wish-list */

      foreach ($productList->result() as $rentals) 
      {
        
        /*checking added in wislist*/
        $fav = false;
        $hr_price = "";
        $veh_iamge = "";
        if (in_array($rentals->id,$wish_list_array))
        {
          $fav = true;
        }
        /********/
        /*getting vehicle price*/
        $saved_booking_type=explode(",",$rentals->booking_type);
      
        if(in_array('1', $saved_booking_type) && !in_array('2', $saved_booking_type))
        { 
          $price = $rentals->day_price;
        }
        elseif (!in_array('1', $saved_booking_type) && in_array('2', $saved_booking_type)) 
        {
          $price = 0;
          $hr_price = $rentals->min_hour_price;
        }
        elseif (in_array('1', $saved_booking_type) && in_array('2', $saved_booking_type)) 
        {
          $price = $rentals->day_price;
          $hr_price = $rentals->min_hour_price;
        }
        else
        { 
          $price = $rentals->day_price;
        }

        if ($rentals->currency != $session_currency) 
        {
          $price = currency_conversion($rentals->currency, $session_currency, $price);
          if($hr_price != '')
          {
            $hr_price = currency_conversion($rentals->currency, $session_currency, $hr_price);
          }
        }

        $condition = array('currency_type'=>$session_currency);
        $currency_rate = $this->mobile_model->get_all_details ( CURRENCY, $condition );
        $property_currency_symbol = $currency_rate->row()->currency_symbols;

        if($language_code == 'en')
        {
           $field_list_value=$rentals->veh_title;
        }
        else
        {
           $list_value_Field='veh_title_ph';
           if($rentals->$list_value_Field == '') { 
               $field_list_value=$rentals->veh_title;
           }
           else{
               $field_list_value=$rentals->$list_value_Field;
           }
        }

        if($rentals->user_name != ''){
          $host_name = $rentals->user_name;
          
        } else {
          $host_name ="";
        }

        $veh_iamge = base_url().'images/vehicles/'.$rentals->image;

        if($rentals->userphoto != '') {
        $userphoto = base_url().'images/users/'.$rentals->userphoto;
        }else{
        $userphoto = base_url().'images/site/profile.jpg';
        }
        
        if($hr_price != "")
    		{
    			$sprice = $this->number_format($hr_price, 2);
    		}
    		else
    		{
    			$sprice = $this->number_format($price, 2);
    		}
		
    		if ($pricemin != "" && $pricemax != "") 
    		{
    		    if ($sprice >= $pricemin && $sprice <= $pricemax) 
    		    {
    		        $vehicle_lists[] = array("vehicle_id" => intval($rentals->id), "vehicle_title" => $field_list_value,"vehicle_image" =>$veh_iamge,"latitude" => $rentals->latitude,"longitude" => $rentals->longitude,"is_favourite"=>$fav,"vehicle_hour_price" =>floatval($hr_price),"vehicle_day_price" =>floatval($price),"hostname"=>$host_name,"host_id"=>intval($rentals->userid),"host_image" => $userphoto,"currency_symbol"=>$property_currency_symbol);
    		    }
    		}
    		else
    		{
    		    $vehicle_lists[] = array("vehicle_id" => intval($rentals->id), "vehicle_title" => $field_list_value,"vehicle_image" =>$veh_iamge,"latitude" => $rentals->latitude,"longitude" => $rentals->longitude,"is_favourite"=>$fav,"vehicle_hour_price" =>floatval($hr_price),"vehicle_day_price" =>floatval($price),"hostname"=>$host_name,"host_id"=>intval($rentals->userid),"host_image" => $userphoto,"currency_symbol"=>$property_currency_symbol);
    		}
       
        
      }    

      $json_encode = json_encode(array("status"=>1,"message"=>"vehicles available","vehicle_list"=>$vehicle_lists,"make" =>$car_make,"model" =>$car_model,"type"=>$car_type,"attribute"=>$attribute),JSON_PRETTY_PRINT);
      echo $json_encode; exit();   
    }
}

  public function GetDays($sStartDate, $sEndDate)
  {  
    
    $sStartDate = date("Y-m-d", strtotime($sStartDate)); 
    $sEndDate = date("Y-m-d", strtotime("-1 day", strtotime($sEndDate)));   
    
    $aDays[] = $sStartDate;  
      
    $sCurrentDate = $sStartDate;
    
    while($sCurrentDate < $sEndDate)
    {  
        $sCurrentDate = date("Y-m-d", strtotime("+1 day", strtotime($sCurrentDate)));       
        $aDays[] = $sCurrentDate; 
    }  
    return $aDays;  
    
  }

  public function vehicle_inbox_conversation()
  {
    $bookingNo = $_POST['bookingid'];
    $userId = $_POST['userid'];
    $rental_type = $_POST['base_id'];
    $language_code = $_POST['lang_code'];

    if($bookingNo =="" || $userId =="") {
      echo json_encode(array('status'=>0,'message'=>$this->parm_missing));
      exit;
    }

    $conversationDetails = $this->mobile_model->get_all_details(MED_MESSAGE, array ('bookingNo' => $bookingNo),array(array('field'=>'id', 'type'=>'desc')));
    $bookingDetails = $this->mobile_model->get_vehicle_booking_details($bookingNo);
    /*print_r($bookingDetails->result()); exit();*/
    if($bookingDetails->num_rows() >0) 
    {
      $product_details = $this->mobile_model->get_all_details(VEHICLE,array('id'=>$conversationDetails->row()->productId));
      if($product_details->row()->user_id == $userId){
        $this->db->where('bookingNo', $bookingNo);
        $this->db->update(MED_MESSAGE, array('msg_read' => 'Yes','host_msgread_status'=>'Yes')); 
      }else{
        $this->db->where('bookingNo', $bookingNo);
        $this->db->update(MED_MESSAGE, array('msg_read' => 'Yes','user_msgread_status'=>'Yes')); 
      }

      if($bookingDetails->row()->host_image != ''){
        if($bookingDetails->row()->host_login_type == 'google'){
          $host_img = $bookingDetails->row()->host_image;
        } else {
          $host_img = base_url().'images/users/'.$bookingDetails->row()->host_image;
        }
      }else{
        $host_img = base_url().'images/users/profile.png';
      }

      if($bookingDetails->row()->requester_image != ''){
        if($bookingDetails->row()->requester_login_type == 'google'){
          $req_img = $bookingDetails->row()->requester_image;
        } else {
          $req_img = base_url().'images/users/'.$bookingDetails->row()->requester_image;
        }
      }else{
        $req_img = base_url().'images/users/profile.png';
      }

      $message_detail = array();
      foreach($conversationDetails->result() as  $conversation) {
        $subject = false;
        if($conversation->point ==1) {
          $subject = true;
        }
        $is_receiver = false;
        if($conversation->user_msgread_status =='Yes') {
          $is_receiver = true;        
        }
        $is_host = false;
        if($conversation->host_msgread_status =='Yes') {
          $is_host = true;        
        }
        $parent_select_qry = "select image,loginUserType,id from fc_users where id=$conversation->senderId";
        $sender_img = $this->mobile_model->ExecuteQuery($parent_select_qry);
        if($sender_img->row()->image != ''){
          if($sender_img->row()->loginUserType == 'google'){
            $user_img = $sender_img->row()->image;
          } else {
            $user_img = base_url().'images/users/'.$sender_img->row()->image;
          }
        }else{
          $user_img = base_url().'images/users/profile.png';
        }
        
        $receiver_select_qry = "select image,loginUserType,id from fc_users where id=$conversation->receiverId";
        $receiver_img = $this->mobile_model->ExecuteQuery($receiver_select_qry);
        if($receiver_img->row()->image != ''){
          if($receiver_img->row()->loginUserType == 'google'){
            $rec_img = $receiver_img->row()->image;
          } else {
            $rec_img = base_url().'images/users/'.$receiver_img->row()->image;
          }
        }else{
          $rec_img = base_url().'images/users/profile.png';
        }
        $message_detail[] = array("Id"=>$conversation->id,"message"=>$conversation->message,"message_by"=>$conversation->senderId,"is_subject"=>$subject,"is_receiver_read"=> $is_receiver,"is_host_read"=> $is_host,"server_time"=>$conversation->dateAdded,"user_image"=>$user_img);
      }

      if($language_code == 'en')
      {
        $pdtNameField = $bookingDetails->row()->veh_title;
      }
      else
      {
        $title_field = 'veh_title_ph';
        if($r->$title_field=='') { 
            $pdtNameField = $bookingDetails->row()->veh_title;
        }
        else{
            $pdtNameField = $bookingDetails->row()->$title_field;
        }
      }

      $checkin_dt = $bookingDetails->row()->checkin_date;
      $checkout_dt = $bookingDetails->row()->checkout_date;

      echo json_encode(array('status'=>1,'message'=>$this->dta_found,"property_id"=>$bookingDetails->row()->veh_id,"property_name"=> $pdtNameField,"host_id"=>$bookingDetails->row()->renter_id,"host_name"=> $bookingDetails->row()->host_name,"host_image"=> $host_img,"requester_id"=>$bookingDetails->row()->user_id,"requester_name"=>$bookingDetails->row()->requester_name,"requester_image"=>$req_img,"booking_id"=>$bookingDetails->row()->Bookingno,"no_of_guest"=>$bookingDetails->row()->NoofGuest,"checkin"=>$checkin_dt,"checkout"=>$checkout_dt,"approval"=>$bookingDetails->row()->approval,"total_amount"=>floatval($bookingDetails->row()->totalAmt),"property_currency_code"=>$bookingDetails->row()->currencycode,"member_from"=>$bookingDetails->row()->created,'messages'=>$message_detail),JSON_PRETTY_PRINT);

    }
    else
    {
      echo json_encode(array('status'=>0,'message'=>$this->no_dta_found));
      exit();
    }
  }




}