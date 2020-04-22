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
    "phone_number",
    "woodpecker_status",
  ];

  public static function apikey()
  {
    $result = array(
      "hubspot" => "e5ee3461-4eda-46e7-969e-6d2d2e423b84",
      "woodpecker" => "84565.4e74d807c5b32502fa3472b362fd4975325e2c22f095e36cb676c493f5500321",
    );
    return $result;
  }

  public static function write_localDb_to_hubspot()
  {
    $body = self::reformat_localDb_to_hubspot();
    $body = json_encode($body);
    $apikey = self::apikey()["hubspot"];
    $endpoint = 'https://api.hubapi.com/contacts/v1/contact/batch/?hapikey=' . $apikey;
    $userpwd = "";

    self::curl_post($body,$endpoint,$userpwd);

  }

  public static function reformat_localDb_to_hubspot()
  {
    $input = self::all();
    $input = json_decode(json_encode($input), true);

    $cols = array(
      array(
        'local_name' => "first_name",
        'remote_name' => "firstname",
      ),
      array(
        'local_name' => "company",
        'remote_name' => "company",
      ),
      array(
        'local_name' => "last_name",
        'remote_name' => "lastname",
      ),
      array(
        'local_name' => "id",
        'remote_name' => "localdb_id",
      ),
    );
    $result = array();
    foreach ($input as $key => $value) {
      $result[$key] = array();
      $result[$key]["email"] = $value["email"];
      // $result[$key]["localdb_id"] = $value["id"];
      $result[$key]["properties"] = array();
      $i = 0;

      foreach ($cols as $key2 => $value2) {
        $result[$key]["properties"][$i]["property"] = $value2["remote_name"];

        if (isset($value[$value2["local_name"]])) {
          $result[$key]["properties"][$i]["value"] = $value[$value2["local_name"]];
        } else {
          $result[$key]["properties"][$i]["value"] = "";
        }
        $i=$i+1;
      }

    }
    return $result;


  }

  public static function read_hubspot()
  {
    $apikey = customer::apikey()["hubspot"];
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

  public static function write_hubspot_to_localDb()
  {

    $customers = customer::read_hubspot();
    $customers = json_decode($customers, true);

    $customers_local = customer::all();
    $customers_local = json_decode(json_encode($customers_local), true);


    $cols = array(
      array(
        'local_name' => "first_name",
        'remote_name' => "firstname",
      ),
      array(
        'local_name' => "company",
        'remote_name' => "company",
      ),
      array(
        'local_name' => "last_name",
        'remote_name' => "lastname",
      ),
    );
    $changes = array();
    $customer_object = new customer;
    foreach ($customers as $key => $customer) {
      $key_value_pairs = array();
      foreach ($cols as $key2 => $value) {
        $key_value_pairs[$value["local_name"]] = $customer[$value["remote_name"]];
      }
      $customer_change = $customer_object->updateOrCreate(
        ['id' => $customer["localdb_id"]],
        $key_value_pairs
      );
      if ($customer_change->wasChanged()) {
        $changes[$key]["name"] = $customer_change->first_name;
        $changes[$key]["changes"] = $customer_change->getChanges();
      }

    }

    $changes = json_decode(json_encode($changes), true);
    return $changes;

  }

  public static function read_woodpecker_lookups()
  {



    // $apikey = customer::apikey()["hubspot"];
    // $endpoint = "https://api.hubapi.com/properties/v1/contacts/properties?hapikey="
    // .$apikey;
    // $userpwd = "";
    //
    // $result = self::curl_get($endpoint,$userpwd);
    // $result = json_decode($result, true);
    //
    //
    // if (isset($result[47]["options"])) {
    //   $result = $result[47]["options"];
    // } else {
    //   $result = "[]";
    // }
    //
    $result = array(
      array(
        "label" => "Active",
        "value" => "ACTIVE",
      ),
      array(
        "label" => "Blacklisted",
        "value" => "BLACKLISTED",
      ),
      array(
        "label" => "Responded",
        "value" => "RESPONDED",
      ),
      array(
        "label" => "Invalid",
        "value" => "INVALID",
      ),
      array(
        "label" => "Bounced",
        "value" => "BOUNCED",
      ),
      array(
        "label" => "Opt-out",
        "value" => "OPT-OUT",
      ),
    );

    return $result;
  }

  public static function read_woodpecker()
  {

    // code...
    $apikey = self::apikey()["woodpecker"];

    $userpwd = array(
      "username" => $apikey,
      "password" => "X",
    );
    $endpoint = 'https://api.woodpecker.co/rest/v1/prospects';

    $response = self::curl_get($endpoint,$userpwd);


    return $response;



  }

  public static function write_localDb_to_woodpecker()
  {

    $body = self::reformat_localDb_to_woodpecker();
    $body = json_encode($body);
    $endpoint = 'https://api.woodpecker.co/rest/v1/add_prospects_list';

    $userpwd = array(
      "username" => $apikey = self::apikey()["woodpecker"],
      "password" => "X",
    );

    self::curl_post($body,$endpoint,$userpwd);

  }

  public static function reformat_localDb_to_woodpecker()
  {


    $input = self::all();
    $input = json_decode(json_encode($input), true);


    $cols = array(
      array(
        'local_name' => "first_name",
        'remote_name' => "first_name",
      ),
      array(
        'local_name' => "company",
        'remote_name' => "company",
      ),
      array(
        'local_name' => "last_name",
        'remote_name' => "last_name",
      ),
    );


    $prospects = array();
    foreach ($input as $key => $value) {
      $prospects[$key] = array();
      $prospects[$key]["email"] = $value["email"];

      $i = 0;

      foreach ($cols as $key2 => $value2) {

        if (isset($value[$value2["local_name"]])) {
          $prospects[$key][$value2["remote_name"]] = $value[$value2["local_name"]];
        } else {
          $prospects[$key][$value2["remote_name"]] = "";
        }
        $i=$i+1;
      }

    }

    $result = array(
      "update" => "true",
      "prospects" => $prospects,
    );
    return $result;


  }

  public static function curl_post($body,$endpoint,$userpwd)
  {
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $endpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
    if (!empty($userpwd)) {
      curl_setopt($ch, CURLOPT_USERPWD, $userpwd['username'] . ":" . $userpwd['password']);
    }

    $headers = array();
    $headers[] = 'Content-Type: application/json';
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $result = curl_exec($ch);
    if (curl_errno($ch)) {
      echo 'Error:' . curl_error($ch);
    }
    curl_close($ch);
    var_dump($result);



  }

  public static function curl_get($endpoint,$userpwd)
  {


    $ch = @curl_init();
    if (!empty($userpwd)) {
      curl_setopt($ch, CURLOPT_USERPWD, $userpwd['username'] . ":" . $userpwd['password']);
    }
    @curl_setopt($ch, CURLOPT_URL, $endpoint);
    @curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      'Accept: application/json',
      'Content-Type: application/json'
    ));
    @curl_setopt($ch, CURLOPT_HEADER, 0);
    @curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = @curl_exec($ch);
    $status_code = @curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_errors = curl_error($ch);

    @curl_close($ch);


    $response = json_encode(json_decode($response, true),JSON_PRETTY_PRINT);
    return $response;


  }








}
