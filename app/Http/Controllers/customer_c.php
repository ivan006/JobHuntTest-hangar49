<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Datatables;
use App\customer;

class customer_c extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

      if (isset($_GET["report"])) {
        $report = json_decode($_GET["report"],true);

      } else {
        $report = array();
      }
      $hubspot_data = customer::read_hubspot();
      $woodpecker_data = customer::read_woodpecker();
      $reformat_localDb_to_woodpecker = customer::reformat_localDb_to_woodpecker();



      $customers = customer::all();
      $lookups_woodpecker_status = customer::read_woodpecker_lookups();
      return view('customers', compact('customers','hubspot_data', 'lookups_woodpecker_status', 'woodpecker_data', 'reformat_localDb_to_woodpecker', 'report'));


    }

    /**
    * Store a newly created resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
    public function SyncGoogleSheetsToLocalDB(Request $request)
    {
      if (1==1) {
        $client = new \Google_Client();
        $client->setApplicationName("JobHuntTest_hangar49");
        $client->setScopes(\Google_Service_Sheets::SPREADSHEETS);
        $client->setAccessType("offline");
        $client->setAuthConfig("../credentials.json");
        $service = new \Google_Service_Sheets($client);
        $spreadsheetId = "1itH8PruSyObaztP4hhHfavx8UEwBIII_gNAKPSSUib8";
        $range = "A2:T7";
        $response = $service->spreadsheets_values->get($spreadsheetId, $range);
        $sheet_data = $response->getValues();
      }


      if (empty($sheet_data)) {
        print "No data found.\n";
      } else {
        // $mask = "%10s %-10s %s\n";
        // foreach ($sheet_data as $row) {
        //   echo sprintf($mask, $row[2], $row[1], $row[0]);
        // }
        foreach ($sheet_data as $row) {
          customer::create([
            "first_name" =>       $row[0],
            "last_name" =>        $row[1],
            "email" =>            $row[2],
            "job_title_full" =>   $row[3],
            "job_title" =>        $row[4],
            "city" =>             $row[5],
            "country" =>          $row[6],
            "linkedin" =>         $row[7],
            "company" =>          $row[8],
            "company_website" =>  $row[9],
            "company_industry" => $row[10],
            "company_founded" =>  $row[11],
            "company_size" =>     $row[12],
            "company_linkedin" => $row[13],
            "company_headquarters" => $row[14],
            "email_reliability_status" => $row[15],
            "receiving_email_server" => $row[16],
            "kind" =>             $row[17],
            "tag" =>              $row[18],
            "month" =>            $row[19],
          ]);
        }

      }

      return redirect('/');

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function SyncLocalDBToHubspot(Request $request)
    {

      customer::write_localDb_to_hubspot();

      return redirect('/');


    }

    /**
    * Store a newly created resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
    public function SyncHubspotToLocalDB(Request $request)
    {

      $result = customer::write_hubspot_to_localDb();
      $result = urlencode(json_encode($result));

      return redirect('/?report='.$result);


    }

    /**
    * Store a newly created resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
    public function SyncLocalDBToWoodpecker(Request $request)
    {

      customer::write_localDb_to_woodpecker();

      return redirect('/');


    }

    /**
    * Update the specified resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function update(Request $request)
    {
      $id = $request->all()['submit'];
      // $id = ;
      $email = $request->all()["rows"][$id]["email"];
      $woodpecker_status = $request->all()["rows"][$id]["woodpecker_status"];
      // $result = array($id => $woodpecker_status);

      $var = customer::find($id);
      $var->woodpecker_status = $woodpecker_status;
      $var->save();

      // dd($request->all());
      // customer::custom_update();


      $endpoint = 'https://api.woodpecker.co/rest/v1/add_prospects_list';
      $userpwd = array(
        "username" => $apikey = customer::apikey()["woodpecker"],
        "password" => "X",
      );
      $body = array(
        "update" => "true",
        "prospects" => array(
          array(
            "email" => $email,
            "status" => $woodpecker_status,

          )
        )
      );
      $body = json_encode($body);
      customer::curl_post($body,$endpoint,$userpwd);
      // exit;
      return redirect('/');

    }


}
