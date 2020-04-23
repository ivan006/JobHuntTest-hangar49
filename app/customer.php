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

  public function apikey()
  {
    $result = array(
      // "hubspot" => "e5ee3461-4eda-46e7-969e-6d2d2e423b84",
      "hubspot" => "c06a4d74-dc05-484a-8158-300ea67ed18a",
      "woodpecker" => "84565.4e74d807c5b32502fa3472b362fd4975325e2c22f095e36cb676c493f5500321",
      // "woodpecker" => "78173.9ed5e81922bf4a5ed1f8c42bbb534822f3904671a7af2d797834284dd53b9680",
      // "googlesheets" => "1itH8PruSyObaztP4hhHfavx8UEwBIII_gNAKPSSUib8",
      "googlesheets" => "1fn3QpOndShigo1fxMPHYGyWXPr8688sOaZvneZCvPsw",
    );
    return $result;
  }

  public function write_localDb_to_hubspot()
  {
    $customer_object = new customer;
    $body = $customer_object->reformat_localDb_to_hubspot();
    $body = json_encode($body);
    $apikey = $customer_object->apikey()["hubspot"];
    $endpoint = 'https://api.hubapi.com/contacts/v1/contact/batch/?hapikey=' . $apikey;
    $userpwd = "";

    $customer_object->curl_post($body,$endpoint,$userpwd);
    // exit;
  }

  public function reformat_localDb_to_hubspot()
  {
    $input = self::all();
    $input = json_decode(json_encode($input), true);

    // $cols = array(
    //   array(
    //     'local_name' => "first_name",
    //     'remote_name' => "firstname",
    //   ),
    //   array(
    //     'local_name' => "company",
    //     'remote_name' => "company",
    //   ),
    //   array(
    //     'local_name' => "last_name",
    //     'remote_name' => "lastname",
    //   ),
    //   array(
    //     'local_name' => "id",
    //     'remote_name' => "localdb_id",
    //   ),
    // );
    $customer_object = new customer;
    $cross_db_mapping = $customer_object->cross_db_mapping();

    $result = array();
    foreach ($input as $key => $value) {
      $result[$key] = array();
      $result[$key]["email"] = $value["email"];
      // $result[$key]["localdb_id"] = $value["id"];
      $result[$key]["properties"] = array();
      $i = 0;

      foreach ($cross_db_mapping as $key2 => $value2) {
        if ($value2["hubspot"] !== "") {
          $result[$key]["properties"][$i]["property"] = $value2["hubspot"];

          if (isset($value[$value2["localdb"]])) {
            $result[$key]["properties"][$i]["value"] = $value[$value2["localdb"]];
          } else {
            $result[$key]["properties"][$i]["value"] = "";
          }
          $i=$i+1;
        }
      }

    }
    return $result;


  }

  public function read_hubspot()
  {
    $customer_object = new customer;
    $apikey = $customer_object->apikey()["hubspot"];
    $customer_object = new customer;
    $cross_db_mapping = $customer_object->cross_db_mapping();
    // $cols = array(
    //   // "localdb_id",
    //   "firstname",
    //   "lastmodifieddate",
    //   "company",
    //   "email",
    //   "lastname",
    // );
    $get_req_properties = "";
    foreach ($cross_db_mapping as $key => $value) {
      if ($value["hubspot"] !== "") {
        $get_req_properties = $get_req_properties."&property=".$value["hubspot"];
      }
    }
    // =demo
    $hubspot_data_raw =     file_get_contents(
      "https://api.hubapi.com/contacts/v1/lists/all/contacts/all?hapikey="
      .$apikey
      // ."&count=2"
      .$get_req_properties
    );

    $hubspot_data_raw = json_decode($hubspot_data_raw, true);
    $hubspot_data_raw = $hubspot_data_raw["contacts"];
    // dd($hubspot_data_raw);





    $hubspot_data = array();
    foreach ($hubspot_data_raw as $key => $value) {
      $hubspot_data[$key] = array();
      // $hubspot_data[$key]["id"] = $value["vid"];
      foreach ($cross_db_mapping as $key2 => $value2) {
        if ($value2["hubspot"] !== "") {
          if (isset($value["properties"][$value2["hubspot"]]['value'])) {
            $hubspot_data[$key][$value2["hubspot"]] = $value["properties"][$value2["hubspot"]]['value'];
          } else {
            $hubspot_data[$key][$value2["hubspot"]] = "";
          }
        }
      }
    }
    $result = json_encode($hubspot_data,JSON_PRETTY_PRINT);
    return $result;
  }

  public function write_hubspot_to_localDb()
  {
    $customer_object = new customer;
    $customers = $customer_object->read_hubspot();
    $customers = json_decode($customers, true);
    // dd($customers);
    $customers_local = customer::all();
    $customers_local = json_decode(json_encode($customers_local), true);


    // $cols = array(
    //   array(
    //     'local_name' => "first_name",
    //     'remote_name' => "firstname",
    //   ),
    //   array(
    //     'local_name' => "company",
    //     'remote_name' => "company",
    //   ),
    //   array(
    //     'local_name' => "email",
    //     'remote_name' => "email",
    //   ),
    //   array(
    //     'local_name' => "last_name",
    //     'remote_name' => "lastname",
    //   ),
    // );
    $cross_db_mapping = $customer_object->cross_db_mapping();
    $changes = array();
    foreach ($customers as $key => $customer) {
      $key_value_pairs = array();
      if (isset($customer["email"])) {
        foreach ($cross_db_mapping as $key2 => $value) {
          if ($value["hubspot"] !== "") {
            $key_value_pairs[$value["localdb"]] = $customer[$value["hubspot"]];
          }

        }
        $customer_change = $customer_object->updateOrCreate(
          // ['id' => $customer["localdb_id"]],
          ['email' => $customer["email"]],
          $key_value_pairs
        );
        if ($customer_change->wasChanged()) {
          $changes[$key]["name"] = $customer_change->first_name." (update)";
          $changes[$key]["changes"] = $customer_change->getChanges();
        } elseif ($customer_change->wasRecentlyCreated) {
          $changes[$key]["name"] = $customer_change->first_name." (create)";
          $changes[$key]["changes"] = $customer_change->attributes;
        }
        // $changes_object[$key] = $customer_change;
      }

    }
    // dd($changes_object);
    $changes = json_decode(json_encode($changes), true);
    return $changes;

  }

  public function read_woodpecker_lookups()
  {


    // $customer_object = new customer;
    // $apikey = $customer_object->apikey()["hubspot"];
    // $endpoint = "https://api.hubapi.com/properties/v1/contacts/properties?hapikey="
    // .$apikey;
    // $userpwd = "";
    //
    // $result = $customer_object->curl_get($endpoint,$userpwd);
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
        "disabled_toggle" => "",
      ),
      array(
        "label" => "Blacklisted",
        "value" => "BLACKLIST",
        "disabled_toggle" => "",
      ),
      array(
        "label" => "Responded",
        "value" => "REPLIED",
        "disabled_toggle" => "",
      ),
      array(
        "label" => "Invalid",
        "value" => "INVALID",
        "disabled_toggle" => "",
      ),
      array(
        "label" => "Bounced",
        "value" => "BOUNCED",
        "disabled_toggle" => "",
      ),
      array(
        "label" => "Opt-out",
        "value" => "OPT-OUT",
        "disabled_toggle" => "disabled",
      ),
    );

    return $result;
  }

  public function read_woodpecker()
  {

    // code...
    $customer_object = new customer;
    $apikey = $customer_object->apikey()["woodpecker"];

    $userpwd = array(
      "username" => $apikey,
      "password" => "X",
    );
    $endpoint = 'https://api.woodpecker.co/rest/v1/prospects';

    $response = $customer_object->curl_get($endpoint,$userpwd);


    return $response;



  }

  public function write_localDb_to_woodpecker()
  {
    $customer_object = new customer;
    $body = $customer_object->reformat_localDb_to_woodpecker();
    $body = json_encode($body);
    $endpoint = 'https://api.woodpecker.co/rest/v1/add_prospects_list';

    $userpwd = array(
      "username" => $apikey = $customer_object->apikey()["woodpecker"],
      "password" => "X",
    );

    $customer_object->curl_post($body,$endpoint,$userpwd);

  }

  public function reformat_localDb_to_woodpecker()
  {
    $input = self::all();
    $input = json_decode(json_encode($input), true);

    $customer_object = new customer;
    $cross_db_mapping = $customer_object->cross_db_mapping();
    // $cols = array(
    //   array(
    //     'local_name' => "first_name",
    //     'remote_name' => "first_name",
    //   ),
    //   array(
    //     'local_name' => "company",
    //     'remote_name' => "company",
    //   ),
    //   array(
    //     'local_name' => "last_name",
    //     'remote_name' => "last_name",
    //   ),
    // );


    $prospects = array();
    foreach ($input as $key => $value) {
      $prospects[$key] = array();
      $prospects[$key]["email"] = $value["email"];

      $i = 0;

      foreach ($cross_db_mapping as $key2 => $value2) {
        if ($value2["woodpecker"] !== "") {
          if (isset($value[$value2["localdb"]])) {
            $prospects[$key][$value2["woodpecker"]] = $value[$value2["localdb"]];
          } else {
            $prospects[$key][$value2["woodpecker"]] = "";
          }
          $i=$i+1;
        }
      }

    }

    $result = array(
      "update" => "true",
      "prospects" => $prospects,
    );
    return $result;


  }

  public function curl_post($body,$endpoint,$userpwd)
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

  public function curl_get($endpoint,$userpwd)
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

  public function cross_db_mapping()
  {


    $result = array(
      // array(
      //   'google_sheets' => "",
      //   'localdb' => "id",
      //   'woodpecker' => "id",
      //   'hubspot' => "localdb_id",
      // ),
        array(
          'google_sheets' => "",
          'localdb' => "created_at",
          'woodpecker' => "",
          'hubspot' => "",
        ),
        array(
          'google_sheets' => "",
          'localdb' => "updated_at",
          'woodpecker' => "",
          'hubspot' => "",
        ),
        array(
          'google_sheets' => "first_name",
          'localdb' => "first_name",
          'woodpecker' => "first_name",
          'hubspot' => "firstname",
        ),
        array(
          'google_sheets' => "last_name",
          'localdb' => "last_name",
          'woodpecker' => "last_name",
          'hubspot' => "lastname",
        ),
        array(
          'google_sheets' => "email",
          'localdb' => "email",
          'woodpecker' => "email",
          'hubspot' => "email",
        ),
        array(
          'google_sheets' => "job_title_full",
          'localdb' => "job_title_full",
          'woodpecker' => "",
          'hubspot' => "",
        ),
        array(
          'google_sheets' => "job_title",
          'localdb' => "job_title",
          'woodpecker' => "title",
          'hubspot' => "jobtitle",
        ),
        array(
          'google_sheets' => "city",
          'localdb' => "city",
          'woodpecker' => "city",
          'hubspot' => "city",
        ),
        array(
          'google_sheets' => "country",
          'localdb' => "country",
          'woodpecker' => "country",
          'hubspot' => "country",
        ),
        array(
          'google_sheets' => "linkedin",
          'localdb' => "linkedin",
          'woodpecker' => "",
          'hubspot' => "linkedin",
        ),
        array(
          'google_sheets' => "company",
          'localdb' => "company",
          'woodpecker' => "company",
          'hubspot' => "company",
        ),
        array(
          'google_sheets' => "company_website",
          'localdb' => "company_website",
          'woodpecker' => "website",
          'hubspot' => "website",
        ),
        array(
          'google_sheets' => "company_industry",
          'localdb' => "company_industry",
          'woodpecker' => "industry",
          'hubspot' => "industry",
        ),
        array(
          'google_sheets' => "company_founded",
          'localdb' => "company_founded",
          'woodpecker' => "",
          'hubspot' => "company_founded",
        ),
        array(
          'google_sheets' => "company_size",
          'localdb' => "company_size",
          'woodpecker' => "",
          'hubspot' => "company_size",
        ),
        array(
          'google_sheets' => "company_linkedin",
          'localdb' => "company_linkedin",
          'woodpecker' => "",
          'hubspot' => "linkedin_company",
        ),
        array(
          'google_sheets' => "company_headquarters",
          'localdb' => "company_headquarters",
          'woodpecker' => "",
          'hubspot' => "company_headquarters",
        ),
        array(
          'google_sheets' => "email_reliability_status",
          'localdb' => "email_reliability_status",
          'woodpecker' => "",
          'hubspot' => "email_reliability_status",
        ),
        array(
          'google_sheets' => "receiving_email_server",
          'localdb' => "receiving_email_server",
          'woodpecker' => "",
          'hubspot' => "receiving_email_server",
        ),
        array(
          'google_sheets' => "kind",
          'localdb' => "kind",
          'woodpecker' => "",
          'hubspot' => "kind",
        ),
        array(
          'google_sheets' => "tag",
          'localdb' => "tag",
          'woodpecker' => "tags",
          'hubspot' => "",
        ),
        array(
          'google_sheets' => "month",
          'localdb' => "month",
          'woodpecker' => "",
          'hubspot' => "month",
        ),
        array(
          'google_sheets' => "",
          'localdb' => "phone_number",
          'woodpecker' => "phone",
          'hubspot' => "phone",
        ),
        array(
          'google_sheets' => "",
          'localdb' => "woodpecker_status",
          'woodpecker' => "status",
          'hubspot' => "",
        ),
    );
    return $result;


  }








}
