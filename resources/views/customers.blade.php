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
          <a href="SyncLocalDBToWoodpecker" type="button" class="btn btn-primary">
            Push Records to Woodpecker
          </a>

        </div>
        <hr>
        <?php if (!empty($report)): ?>
          <div class="alert alert-info" role="alert">
            <strong>Diff report</strong>
            <?php foreach ($report as $key => $value): ?>
              <?php if (!empty($value)): ?>
                <!-- <br> -->

                <details>
                  <summary>
                    <strong><?php echo $value["name"] ?>: </strong>
                  </summary>
                  <?php foreach ($value["changes"] as $key2 => $value2): ?>
                    <?php echo $key2.": ".$value2."; "; ?>
                    <br>

                  <?php endforeach; ?>
                </details>

              <?php endif; ?>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
        <form class="" action="update" method="post">
          {{csrf_field()}}
          <table id="example" class="table table-striped  display nowrap dataTable dtr-inline collapsed" style="width:100%">
          </table>
        </form>
        <div class="">
          <pre>
            <table>
              <tr>
                <td>
                  <?php //echo $woodpecker_data; ?>
                  <?php //echo $hubspot_data; ?>
                </td>
                <td>
                  <?php //echo json_encode($reformat_localDb_to_woodpecker,JSON_PRETTY_PRINT); ?>
                </td>
              </tr>
            </table>

            Button 1 must
              - sync data from google sheets to localDB		(done)
              - and display data on screen			(done)
              - also data must add to and not replace the original dataset so that future data can be added		(done)
            Button 2 must
              - sync data from localDB to Hubspot		(processing)
            Button 3 must
              - sync data from Hubspot to localDb		(processing)
              - and give diff report				(done)
            Button 4 must
              - sync data from localDB to Woodpecker		(done)
            Update button must
              - update Woodpecker and localDB			(done)



          </pre>
        </div>
      </div>

      <script src="{{ asset('/js/app.js') }}"></script>
      <script type="text/javascript">

      $(document).ready( function () {

        var woodpecker_status_options = <?php echo json_encode($lookups_woodpecker_status); ?>;

        // woodpecker_status_options.unshift({label:"None",value:""});
        // alert(JSON.stringify(woodpecker_status_options));

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
              "data": "woodpecker_status",
              "render": function ( data, type, row, meta ) {

                // if (data == null) {
                //   alert(data+" "+row["first_name"]);
                // }
                var woodpecker_status_html = '<select class="form-control" id="" name="rows['+row['id']+'][woodpecker_status]" style="width: 200px;">';
                var option_html = '';

                var selectToggle = "";
                $.each(woodpecker_status_options, function(i, woodpecker_status_option){
                  if (woodpecker_status_option['value'] == data) {
                    selectToggle = "selected";
                  } else {
                    selectToggle = "";
                  }
                  var value = woodpecker_status_option['value'];
                  if (woodpecker_status_option['value'] === null) {
                    value = "";
                  }

                  option_html = '<option value="'+value+'" '+selectToggle+' '+woodpecker_status_option['disabled_toggle']+'>'+woodpecker_status_option['label']+'</option>';

                  woodpecker_status_html = woodpecker_status_html+option_html;
                })
                woodpecker_status_html = woodpecker_status_html+'</select>';

                var email_html = '<input type="text" name="rows['+row['id']+'][email]" value="'+row['email']+'" style="display:none;">';
                var inputs_html = woodpecker_status_html+email_html;
                // alert(woodpecker_status_html);
                return inputs_html;


                // return '<select class="form-control" id="sel1" name="sellist1">'
                // +'<option>1</option>'
                // +'<option>2</option>'
                // +'<option>3</option>'
                // +'<option>4</option>'
                // +'</select>';
              },
              "title": "woodpecker_status",
            },
            {
              "data": "id",
              "render": function ( data, type, row, meta ) {
                return '<button type="submit" class="btn btn-primary" name="submit" value="'+data+'">Update</button>';
              },
              "title": "woodpecker_status",

            },
            {
              "data": "created_at",
              "title": "created_at",
            },
            {
              "data": "phone_number",
              "title": "phone_number",
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
