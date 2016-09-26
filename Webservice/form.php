<html>
  <head>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>

    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

    <!-- Latest compiled and minified JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css" rel="stylesheet">

    <title>Bill commit - HealtCare</title>
  </head>

  <body>

    <div class="container">

      <?php
        $fieldsValidate = false;
        if(isset($_POST['submitBill'])){
          if( $fieldsValidate ){
      ?>
        <div class="alert alert-success" role="alert" style="margin-top: 30px;">
          Bill for user <strong><?php echo $_POST['userId'] ?></strong> submitted succesfully!
        </div>
      <?php
          }
        }

        if( !isset($_POST['submitBill']) || !$fieldsValidate ){
          if( !$fieldsValidate ){
      ?>
            <div class="alert alert-danger" role="alert" style="margin-top: 30px;">
              Check input fields
            </div>
      <?php
          }
      ?>
      <form class="form-horizontal" method="post">

        <h2>Order information</h2>
        <div class="form-group">
          <label class="control-label col-sm-2" for="healthCare">Organisation:</label>
          <div class="col-sm-10">
            <input type="email" class="form-control" id="healthCare" placeholder="Enter Health Care name">
          </div>
        </div>

        <div class="form-group">
          <label class="control-label col-sm-2" for="userId">User:</label>
          <div class="col-sm-10">
            <input type="text" name="userId" class="form-control" id="userId" placeholder="Enter user id">
          </div>
        </div>


        <h2>Order line(s)</h2>

        <div id="order-lines">

        </div>


        <div class="form-group">
          <div class="col-sm-12" style="text-align: right;">
            <span class="fa fa-plus" style="color: green; cursor: pointer; font-size: 1.6rem;" onclick="addOrderLine()"> Add another order line</span>
          </div>
        </div>


        <div class="form-group">
          <div class="col-sm-offset-2 col-sm-10">
            <button type="submit" name="submitBill" class="btn btn-default">Submit</button>
          </div>
        </div>
      </form>
      <?php
        }
      ?>
    </div>
  </body>

  <script type="text/javascript">
    addOrderLine();

    function addOrderLine()
    {
        $('#order-lines').append(getOrderLineHtml());
    }

    function getOrderLineHtml()
    {
      var newDiv = $(
        "<div class=\"form-group\" style=\"padding-top: 30px;\">"+
          "<label class=\"control-label col-sm-2\" for=\"description\">"+
            "Description:"+
          "</label>"+
          "<div class=\"col-sm-10\">"+
            "<textarea class=\"form-control\" id=\"description\" placeholder=\"Enter description\" style=></textarea>"+
          "</div>"+
        "</div>"+
        "<div class=\"form-group\">"+
          "<label class=\"control-label col-sm-2\" for=\"code\">Code:</label>"+
          "<div class=\"col-sm-10\"><input type=\"text\" class=\"form-control\" id=\"code\" placeholder=\"Enter order line code\"></div>"+
        "</div>"+
        "<div class=\"form-group\">"+
          "<label class=\"control-label col-sm-2\" for=\"price\">Price:</label>"+
          "<div class=\"col-sm-10\"><input type=\"text\" class=\"form-control\" id=\"price\" placeholder=\"Enter price\"></div>"+
        "</div>");
      return newDiv;
    }

  </script>

</html>
