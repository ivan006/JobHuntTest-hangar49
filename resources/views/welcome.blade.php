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
      <table id="table_id" class="display">
        <thead>
          <tr>
            <th>Column 1</th>
            <th>Column 2</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>Row 1 Data 1</td>
            <td>Row 1 Data 2</td>
          </tr>
          <tr>
            <td>Row 2 Data 1</td>
            <td>Row 2 Data 2</td>
          </tr>
        </tbody>
      </table>

      <script src="{{ asset('/js/app.js') }}"></script>
      <script type="text/javascript">
      $(document).ready( function () {
        $('#table_id').DataTable();
      } );
      </script>
    </body>
</html>
