<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title></title>
        <style media="screen">

        </style>
        <link rel="stylesheet" href="{{ asset('/css/app.css') }}">

    </head>
    <body>
      <div class="container" style="margin-top:30px">
        <h2>Customer Data Syncer</h2>
        <hr>
        <div class="">
          <a href="SyncGoogleSheetsToLocalDB" type="button" class="btn btn-primary">
            Load Data from Sheet
          </a>
          <a href="SyncLocalDBToHubspot" type="button" class="btn btn-primary">
            Push to Hubspot
          </a>
          <a href="SyncHubspotToLocalDB" type="button" class="btn btn-primary">
            Pull Data from Hubspot
          </a>
          <button type="button" class="btn btn-primary">
            Push Records to Woodpecker
          </button>

        </div>
        <hr>
        <form class="" action="/update" method="post">
          <table id="example" class="table table-striped  display nowrap dataTable dtr-inline collapsed" style="width:100%">
          </table>
        </form>
        <div class="">
          <pre>
            <?php
            // function recursify($data){
            //   if (is_object($data) OR is_array($data)) {
            //     foreach ($data as $key => $value) {
            //       echo "<details style='margin-left: 15px;'>";
            //       echo "<summary>";
            //       echo $key;
            //       echo "</summary>";
            //
            //       recursify($value);
            //
            //       echo "</details> ";
            //     }
            //   } else {
            //     echo $data;
            //   }
            // }
            // recursify($test);

            echo $hubspot_data;
            ?>
          </pre>
        </div>
      </div>

      <script src="{{ asset('/js/app.js') }}"></script>
      <script type="text/javascript">

      $(document).ready( function () {

        // $('#table_id').DataTable();
        $('#example').dataTable( {
          "data": <?php echo json_encode($customers,JSON_PRETTY_PRINT); ?>,
          "columns": [
            {
              "data": "first_name",
              "title": "first_name",
            },
            {
              "data": "last_name",
              "title": "last_name",
            },
            {
              "data": "email",
              "title": "email",
            },
            {
              "data": "job_title_full",
              "title": "job_title_full",
            },
            {
              "data": "job_title",
              "title": "job_title",
            },
            {
              "data": "city",
              "title": "city",
            },
            {
              "data": "company_website",
              "render": function ( data, type, row, meta ) {
                return '<select class="form-control" id="sel1" name="sellist1">'
                +'<option>1</option>'
                +'<option>2</option>'
                +'<option>3</option>'
                +'<option>4</option>'
                +'</select>';
              },
              "title": "Action",
            },
            {
              "data": "company_website",
              "render": function ( data, type, row, meta ) {
                return '<button type="submit" class="btn btn-primary" name="xx['+data+']" value="Update">Update</button>';
              },
              "title": "Action",

            },
            {
              "data": "country",
              "title": "country",
            },
            {
              "data": "linkedin",
              "title": "linkedin",
            },
            {
              "data": "company",
              "title": "company",
            },
            {
              "data": "company_website",
              "title": "company_website",
            },
            {
              "data": "company_industry",
              "title": "company_industry",
            },
            {
              "data": "company_founded",
              "title": "company_founded",
            },
            {
              "data": "company_size",
              "title": "company_size",
            },
            {
              "data": "company_linkedin",
              "title": "company_linkedin",
            },
            {
              "data": "company_headquarters",
              "title": "company_headquarters",
            },
            {
              "data": "email_reliability_status",
              "title": "email_reliability_status",
            },
            {
              "data": "receiving_email_server",
              "title": "receiving_email_server",
            },
            {
              "data": "kind",
              "title": "kind",
            },
            {
              "data": "tag",
              "title": "tag",
            },
            {
              "data": "month",
              "title": "month",
            },

          ],
          rowReorder: {
            selector: 'td:nth-child(2)'
          },
          responsive: true

        } );
      } );



      </script>
    </body>
</html>
