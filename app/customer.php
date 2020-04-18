<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class customer extends Model
{
  protected $fillable = [
    "first_name",
    "last_name",
    "email",
    "job_title_full",
    "job_title",
    "city",
    "country",
    "linkedin",
    "company",
    "company_website",
    "company_industry",
    "company_founded",
    "company_size",
    "company_linkedin",
    "company_headquarters",
    "email_reliability_status",
    "receiving_email_server",
    "kind",
    "tag",
    "month",
  ];

  public static function write_localDb_to_hubspot($input)
  {
    // $hubspot_data_array = array_column($input, 'properties');
    $input = json_decode(json_encode($input), true);
    //
    $cols = array(
      array(
        'laravel_name' => "first_name",
        'hubspot_name' => "firstname",
      ),
      // array(
      //   'laravel_name' => "lastmodifieddate",
      //   'hubspot_name' => "lastmodifieddate",
      // ),
      array(
        'laravel_name' => "company",
        'hubspot_name' => "company",
      ),
      array(
        'laravel_name' => "last_name",
        'hubspot_name' => "lastname",
      ),
      // array(
      //   'laravel_name' => "email",
      //   'hubspot_name' => "email",
      // ),
      array(
        'laravel_name' => "id",
        'hubspot_name' => "localdb_id",
      ),
    );
    // foreach ($input as $key => $value) {
    //   $result[$key] = array();
    //   $result[$key]["properties"] = array();
    //   $i = 0;
    //   foreach ($value as $key2 => $value2) {
    //     $result[$key]["properties"][$i]["property"] = $key2;
    //     $result[$key]["properties"][$i]["value"] = $value2;
    //     $i=$i+1;
    //   }
    // }


    foreach ($input as $key => $value) {
      $result[$key] = array();
      $result[$key]["email"] = $value["email"];
      // $result[$key]["localdb_id"] = $value["id"];
      $result[$key]["properties"] = array();
      $i = 0;

      foreach ($cols as $key2 => $value2) {
        $result[$key]["properties"][$i]["property"] = $value2["hubspot_name"];

        if (isset($value[$value2["laravel_name"]])) {
          $result[$key]["properties"][$i]["value"] = $value[$value2["laravel_name"]];
        } else {
          $result[$key]["properties"][$i]["value"] = "";
        }
        $i=$i+1;
      }

    }

    if (1==1) {
      // code...
      $apikey = self::apikey();
      $post_json = json_encode($result);
      // $apikey = "c1eead18-5a35-4ada-b52f-dc6e1a084e5a";
      $endpoint = 'https://api.hubapi.com/contacts/v1/contact/batch/?hapikey=' . $apikey;


      $ch = @curl_init();
      @curl_setopt($ch, CURLOPT_POST, true);
      @curl_setopt($ch, CURLOPT_POSTFIELDS, $post_json);
      @curl_setopt($ch, CURLOPT_URL, $endpoint);
      @curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
      @curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

      $response = @curl_exec($ch);
      $status_code = @curl_getinfo($ch, CURLINFO_HTTP_CODE);
      $curl_errors = curl_error($ch);

      @curl_close($ch);





      echo "curl Errors: " . $curl_errors;
      echo "\nStatus code: " . $status_code;
      $response = json_decode($response);
      $response = json_encode($response,JSON_PRETTY_PRINT);
      echo "<details><pre>";
      echo "<summary>";
      echo "\nResponse: ";
      echo "</summary>";
      echo $response;
      echo "</pre></details>";
      echo "<br>";
    }


    // echo "<pre>";
    // echo json_encode($result,JSON_PRETTY_PRINT);
    // exit;




  }

  public static function write_hubspot_to_localDb()
  {


    $customers = customer::write_hubspot_to_localDb_helper();

    $cols = array(
      array(
        'laravel_name' => "first_name",
        'hubspot_name' => "firstname",
      ),
      // array(
      //   'laravel_name' => "lastmodifieddate",
      //   'hubspot_name' => "lastmodifieddate",
      // ),
      array(
        'laravel_name' => "company",
        'hubspot_name' => "company",
      ),
      array(
        'laravel_name' => "last_name",
        'hubspot_name' => "lastname",
      ),
      // array(
      //   'laravel_name' => "email",
      //   'hubspot_name' => "email",
      // ),
      array(
        'laravel_name' => "id",
        'hubspot_name' => "localdb_id",
      ),
    );

    foreach ($customers as $customer) {

      // $key_value_pairs = array();
      // foreach ($cols as $key => $value) {
      //   $key_value_pairs[$value["laravel_name"]] = $customer[$value["hubspot_name"]];
      // }
      $key_value_pairs = array(

          "first_name" =>       "test",
          "last_name" =>        "test",

      );
      customer::create($key_value_pairs);


      // customer::create([
      //   "first_name" =>       $customer[0],
      //   "last_name" =>        $customer[1],
      //   "email" =>            $customer[2],
      //   "job_title_full" =>   $customer[3],
      //   "job_title" =>        $customer[4],
      //   "city" =>             $customer[5],
      //   "country" =>          $customer[6],
      //   "linkedin" =>         $customer[7],
      //   "company" =>          $customer[8],
      //   "company_website" =>  $customer[9],
      //   "company_industry" => $customer[10],
      //   "company_founded" =>  $customer[11],
      //   "company_size" =>     $customer[12],
      //   "company_linkedin" => $customer[13],
      //   "company_headquarters" => $customer[14],
      //   "email_reliability_status" => $customer[15],
      //   "receiving_email_server" => $customer[16],
      //   "kind" =>             $customer[17],
      //   "tag" =>              $customer[18],
      //   "month" =>            $customer[19],
      // ]);
    }

    return redirect('/');


  }
  public static function write_hubspot_to_localDb_helper(){
    $apikey = customer::apikey();
    $cols = array(
      "firstname",
      "lastmodifieddate",
      "company",
      "lastname",
      "localdb_id",
    );
    $get_req_properties = "";
    foreach ($cols as $key => $value) {
      $get_req_properties = $get_req_properties."&property=".$value;
    }
    $hubspot_data_raw =     file_get_contents(
      "https://api.hubapi.com/contacts/v1/lists/all/contacts/all?hapikey="
      .$apikey
      // ."&count=2"
      .$get_req_properties
    );
    $hubspot_data_raw = json_decode($hubspot_data_raw, true);
    $hubspot_data_raw = $hubspot_data_raw["contacts"];




    $cols = array(
      "localdb_id",
      "firstname",
      "lastmodifieddate",
      "company",
      "lastname",
      "localdb_id",
    );
    $hubspot_data = array();
    foreach ($hubspot_data_raw as $key => $value) {
      $hubspot_data[$key] = array();
      // $hubspot_data[$key]["id"] = $value["vid"];
      foreach ($cols as $key2 => $value2) {
        if (isset($value["properties"][$value2]['value'])) {
          $hubspot_data[$key][$value2] = $value["properties"][$value2]['value'];
        } else {
          $hubspot_data[$key][$value2] = "";
        }
      }
    }
    $result = json_encode($hubspot_data,JSON_PRETTY_PRINT);
    return $result;
  }

  public static function apikey()
  {
    $result = "e5ee3461-4eda-46e7-969e-6d2d2e423b84";
    return $result;
  }



}
