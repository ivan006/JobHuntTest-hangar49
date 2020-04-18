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
      $test = array();
      // if (1==1) {
      //   $hubspot = \SevenShores\Hubspot\Factory::create('e5ee3461-4eda-46e7-969e-6d2d2e423b84');
      //   // $test = $hubspot->contacts()->all();
      //   $hubspot_data_raw = $hubspot->contacts()->all();
      //   $hubspot_data = json_decode(json_encode($hubspot_data_raw), true);
      //   $hubspot_data = json_encode($hubspot_data,JSON_PRETTY_PRINT);
      //   // $hubspot_data = customer::read_hubspot($hubspot_data_raw);
      //
      //   // echo "<pre>";
      //   // echo json_encode($hubspot_data,JSON_PRETTY_PRINT);
      //   // echo json_encode($hubspot_data_raw,JSON_PRETTY_PRINT);
      //
      // }

      $hubspot_data = customer::read_hubspot();


      $customers = customer::all();
      return view('customers', compact('customers','hubspot_data', 'test'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
    * Store a newly created resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
    public function SyncGoogleSheetsToLocalDB(Request $request)
    {


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

      customer::write_hubspot_to_localDb();

      return redirect('/');


    }

    /**
    * Update the specified resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function update(Request $request, $id)
    {

      customer::update();

      return redirect('/');

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
