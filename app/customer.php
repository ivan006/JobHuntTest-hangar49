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

  public static function read_hubspot_to_datatables($hubspot_data_raw){

    // foreach ($hubspot_data_array as $key => $value) {
    //   $hubspot_data[$key] = array();
    //
    //   foreach ($value as $key2 => $value2) {
    //     $hubspot_data[$key][$key2] = $value2['value'];
    //   }
    // }

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

  public static function write_localDb_to_hubspot($input, $apikey)
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
      array(
        'laravel_name' => "email",
        'hubspot_name' => "email",
      ),
      // array(
      //   'laravel_name' => "id",
      //   'hubspot_name' => "localdb_id",
      // ),
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
      // $result[$key]["email"] = $value["email"];
      $result[$key]["localdb_id"] = $value["id"];
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

    // echo "<pre>";
    // echo json_encode($result,JSON_PRETTY_PRINT);
    // exit;

    echo "<pre>";
    foreach ($result as $key => $value) {
      $post_json = json_encode($value);
      // $apikey = "c1eead18-5a35-4ada-b52f-dc6e1a084e5a";
      $endpoint = 'https://api.hubapi.com/contacts/v1/contact?hapikey=' . $apikey;
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
      echo "\nResponse: " . $response;
      echo "<br>";
    }

  }



}
