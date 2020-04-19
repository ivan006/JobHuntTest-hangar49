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

  public static function write_localDb_to_hubspot()
  {
    // $hubspot_data_array = array_column($input, 'properties');
    $input = self::all();
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

  public static function read_hubspot(){
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

  public static function write_hubspot_to_localDb()
  {

    $customers = customer::read_hubspot();
    $customers = json_decode($customers, true);
    // echo "<pre>";
    // var_dump($customers);
    // exit;
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
      // array(
      //   'laravel_name' => "id",
      //   'hubspot_name' => "localdb_id",
      // ),
    );

    foreach ($customers as $customer) {

      $key_value_pairs = array();
      foreach ($cols as $key => $value) {
        $key_value_pairs[$value["laravel_name"]] = $customer[$value["hubspot_name"]];
      }

      // $key_value_pairs = array(
      //
      //
      //       "first_name" =>       "test",
      //       "last_name" =>        "test",
      //       "email" =>            "test",
      //
      //       // "job_title_full" =>   "test",
      //       // "job_title" =>        "test",
      //       // "city" =>             "test",
      //       // "country" =>          "test",
      //       // "linkedin" =>         "test",
      //       // "company" =>          "test",
      //       // "company_website" =>  "test",
      //       // "company_industry" => "test",
      //       // "company_founded" =>  "test",
      //       // "company_size" =>     "test",
      //       // "company_linkedin" => "test",
      //       // "company_headquarters" => "test",
      //       // "email_reliability_status" => "test",
      //       // "receiving_email_server" => "test",
      //       // "kind" =>             "test",
      //       // "tag" =>              "test",
      //       // "month" =>            "test",
      //
      // );
      // customer::create($key_value_pairs);

      // If there's a flight from Oakland to San Diego, set the price to $99.
      // If no matching model exists, create one.
      self::updateOrCreate(
        ['id' => $customer["localdb_id"]],
        $key_value_pairs
      );
      // up till here
      // $flight = self::updateOrCreate(
      //   ['first_name' => 'alon', "last_name" => "Lich"],
      //   ['email' => "updateorcreate"]
      // );
      // dd($customer);

      // echo "<pre>";
      // var_dump($key_value_pairs);
    }
    // exit;




  }

  public static function custom_update()
  {




  }



}
