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
      if (1==1) {
        $hubspot = \SevenShores\Hubspot\Factory::create('c1eead18-5a35-4ada-b52f-dc6e1a084e5a');
        
        // $contact = $hubspot->contacts()->getByEmail("bh@hubspot.com");
        // echo $contact->properties->email->value;

        // Get an array of 10 contacts
        // getting only the firstname and lastname properties
        // and set the offset to 123456
        $response = $hubspot->contacts()->all([
          'count'     => 10,
          'property'  => ['firstname', 'lastname'],
          'vidOffset' => 0,
        ]);

        ob_start();

        foreach ($response->contacts as $contact) {
          echo sprintf(
            "Contact name is %s %s." . PHP_EOL,
            $contact->properties->firstname->value,
            $contact->properties->lastname->value
          );
        }

        // Info for pagination
        echo $response->{'has-more'};
        echo $response->{'vid-offset'};

        $hubspot_data = ob_get_contents();

        ob_end_clean();
        // code...
      }


      $customers = customer::all();
      return view('customers', compact('customers','hubspot_data'));
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
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
