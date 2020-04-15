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
          <button type="button" class="btn btn-primary">
            Push to Hubspot
          </button>
          <button type="button" class="btn btn-primary">
            Pull Data from Hubspot
          </button>
          <button type="button" class="btn btn-primary">
            Push Records to Woodpecker
          </button>

        </div>
        <hr>
        <table id="example" class="display  table table-striped">
        </table>
        <!-- <div class="">
          <pre>
            <?php echo json_encode($customers,JSON_PRETTY_PRINT); ?>
          </pre>
        </div> -->
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
            // {
            //   "data": "company_industry",
            //   "title": "company_industry",
            // },
            // {
            //   "data": "company_founded",
            //   "title": "company_founded",
            // },
            // {
            //   "data": "company_size",
            //   "title": "company_size",
            // },
            // {
            //   "data": "company_linkedin",
            //   "title": "company_linkedin",
            // },
            // {
            //   "data": "company_headquarters",
            //   "title": "company_headquarters",
            // },
            // {
            //   "data": "email_reliability_status",
            //   "title": "email_reliability_status",
            // },
            // {
            //   "data": "receiving_email_server",
            //   "title": "receiving_email_server",
            // },
            // {
            //   "data": "kind",
            //   "title": "kind",
            // },
            // {
            //   "data": "tag",
            //   "title": "tag",
            // },
            // {
            //   "data": "month",
            //   "title": "month",
            // },
          ]
        } );
      } );

      </script>
    </body>
</html>
