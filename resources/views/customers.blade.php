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
          <button type="button" class="btn btn-primary">
            Load Data from Sheet
          </button>
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
      </div>

      <script src="{{ asset('/js/app.js') }}"></script>
      <script type="text/javascript">
      $(document).ready( function () {
        // $('#table_id').DataTable();
        $('#example').dataTable( {
          "data": [
            {
              "name":       "Tiger Nixon",
              "position":   "System Architect",
              "salary":     "$3,120",
              "start_date": "2011/04/25",
              "office":     "Edinburgh",
              "extn":       5421
            },
            {
              "name": "Garrett Winters",
              "position": "Director",
              "salary": "5300",
              "start_date": "2011/07/25",
              "office": "Edinburgh",
              "extn": "8422"
            },
            // ...
          ],
          "columns": [
            {
              "data": "name",
              "title": "name"
            },
            {
              "data": "position",
              "title": "position",
            },
            {
              "data": "office",
              "title": "office",
            },
            {
              "data": "extn",
              "title": "extn",
            },
            {
              "data": "start_date",
              "title": "start_date",
            },
            {
              "data": "salary",
              "title": "salary",
            }
          ]
        } );
      } );

      </script>
    </body>
</html>
