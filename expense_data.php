<?php
// Login to mySQL
$link = mysqli_connect("127.0.0.1", "root", "dell14789632", "berkeleynails");
if (!$link) {
    echo "Error: Unable to connect to MySQL." . PHP_EOL;
    echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
    echo "Debugging error: " . mysqli_connect_error() . PHP_EOL;
    exit;
  }
// Pick Database
$mydb=mysqli_select_db($link,"berkeleynails");
// Prpcess ACTION form
$id = $_GET['id'];
$date = $_GET['date'];
$desc = $_GET['desc'];
$type = $_GET['type'];
$amount = $_GET['amount'];
$note = $_GET['note'];
$table = $_GET['table'];
$name = $_GET['name'];
if (isset($_GET['action'])) {
  if ($_GET['action'] == 'addRow') {
    $date = $_GET['date'];
    $desc = $_GET['desc'];
    $type = $_GET['type'];
    $amount = $_GET['amount'];
    $note = $_GET['note'];
    if (!$date || !$desc || !$amount)
      echo '<script>alert("Please Don\'t Leave\nDate / Expense / Amount\nblank.")</script>';
    else {
      exeQuery("INSERT INTO `$table` (`id`, `date`, `expense`, `type`, `amount`, `note`) VALUES (NULL, '$date', '$desc', '$type', '$amount', '$note')");
      header("Location: http://localhost/expense_data.php?action=viewTable&name=$table");
    }
  }
  elseif ($_GET['action'] == 'editRow') {
    $id = $_GET['id'];
    $date = $_GET['date'];
    $desc = $_GET['desc'];
    $type = $_GET['type'];
    $amount = $_GET['amount'];
    $note = $_GET['note'];
    $table = $_GET['table'];
    exeQuery("UPDATE `$table` SET `date` = '$date', `expense` = '$desc', `type` = '$type', `amount` = '$amount', `note` = '$note' WHERE `id` = $id");
    header("Location: http://localhost/expense_data.php?action=viewTable&name=$table");
  }
  elseif ($_GET['action'] == 'deleteRow') {
    $id = $_GET['id'];
    exeQuery("DELETE FROM `expense` WHERE `id` = $id");
  }
  elseif ($_GET['action'] == 'saveNnew') {
    $name = $_GET['desc'];
    exeQuery("RENAME TABLE `expense` TO `expense_$name`");
    exeQuery("CREATE TABLE expense(id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,date VARCHAR(30),expense VARCHAR(30),type VARCHAR(30),amount INT(11) NOT NULL,note LONGTEXT);");
    // Rename this table to expense_$name and add in new table
  }
  elseif ($_GET['action'] == 'viewTable') {
    $name = $_GET['name'];
    loadtable($name);
  }
}
?>
<html>
  <head>
    <title>Data</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link href="css/style.css" rel="stylesheet" type="text/css" media="all"/>
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <!-- dataTables -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.13/css/jquery.dataTables.min.css">
    <script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.10.13/js/jquery.dataTables.min.js"></script>
    <!-- dataTables Export -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/select/1.2.1/css/select.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.2.4/css/buttons.dataTables.min.css">
    <script type="text/javascript" language="javascript" src="https://cdn.datatables.net/select/1.2.1/js/dataTables.select.min.js">
    </script>
    <script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/1.2.4/js/dataTables.buttons.min.js">
    </script>
    <script type="text/javascript" language="javascript" src="//cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js">
    </script>
    <script type="text/javascript" language="javascript" src="//cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js">
    </script>
    <script type="text/javascript" language="javascript" src="//cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js">
    </script>
    <script type="text/javascript" language="javascript" src="//cdn.datatables.net/buttons/1.2.4/js/buttons.html5.min.js">
    </script>
    <script type="text/javascript" language="javascript" src="//cdn.datatables.net/buttons/1.2.4/js/buttons.print.min.js">
    </script>
    <script>
    $(document).ready(function() {
      $('#view_DataTable').DataTable( {
        "paging": false,
        "info": false,
        "scrollY": 500,
        "scrollX": true
      });
      $('#mainDataTable').DataTable( {
        "dom": 'Bfrtip',
        "buttons": [
          {
              extend: 'collection',
              text: 'Export',
              buttons: [
                  'copy',
                  'excel',
                  'csv',
                  'pdf',
                  'print'
              ]
          }
        ],
        "aoColumnDefs": [
          { "sClass": "Sale_Col", "aTargets": [3] }
        ],
        //"ordering": false,
        "responsive": true,
        "paging": false,
        "info": false,
        "scrollY": 485,
        "scrollX": true
      } );
      // Row Highlight
      $("table.display tbody tr").hover(function(){
          $(this).css("background-color", "#eed369");
          }, function(){
          $(this).css("background-color", "");
      });
    } );
    </script>
  </head>
  <!-- ------------------------------------------------  BODY  ------------------------------------------------ -->
  <body>
    <div id="Navigation">
      <ul class="menu_list">
        <a href="expense.php"><li><i class="fa fa-usd fa-2x" aria-hidden="true"></i><br>Expense</li></a>
        <a href="expense_data.php"><li class="menu_list_active" style="color: #394B58"><i class="fa fa-bar-chart fa-2x" aria-hidden="true"></i><br>Data</li></a>
      </ul>
    </div>
    <div class="middle_wrapper">
      <div class="left_Panel">
         <table id="view_DataTable" class="display nowrap cell-border" width="100%" cellspacing="0" style="border-left: 1px solid black; border-right: 1px solid black; font-size: 20px;">
           <thead>
             <tr>
               <th colspan="6" style="background-color: #eed369">File List</th>
             </tr>
              <tr>
                <th>#</th>
                <th>Name</th>
              </tr>
            </thead>
            <tbody style="text-align: center">
           <?php // Get table List that contain expense_
           $table_index = 1;
           $result = mysqli_query($link,"show tables like '%e\_%'");
           while($table = mysqli_fetch_array($result, MYSQLI_BOTH)) { // go through each row that was returned in $result
               echo '
               <tr>
                  <td>' . $table_index . '</td>
                  <td><a href="expense_data.php?action=viewTable&name=' . $table[0] . '">' . ltrim($table[0],"e_") . '</a></td>
                </tr>';
               $table_index++;
           }
            ?>
           </tbody>
         </table>
      </div> <!--
      <div class="right_Panel" style="border: solid 1px black">
        <span>Info Summary</span>
      </div> -->
    </div>
    <!-- PROMPT START -->
    <!-- ADD ROW -->
    <div class="addRow_prompt input_table">
      <button type="button" style="position: relative; float: right" onclick="closePrompt()">X</button>
      <form action="/expense_data.php" method="get" style="margin-bottom: 0;">
      <table>
        <thead>
            <tr style="color: #fff; font-size: 20px">
              <th><i class="fa fa-calendar fa-2x" aria-hidden="true"></i><br>Date</th>
              <th><i class="fa fa-money fa-2x" aria-hidden="true"></i><br>Expense</th>
              <th>Type</th>
              <th><i class="fa fa-usd fa-2x" aria-hidden="true"></i><br>Amount</th>
              <th><i class="fa fa-pencil fa-2x" aria-hidden="true"></i><br>Note</th>
            </tr>
        </thead>
        <tbody style="text-align: center">
          <tr>
            <input type="hidden" name="action" value="addRow"/>
            <input type="hidden" name="table" value="<?php echo $name ?>">
            <td><input class="datepicker" type="text" name="date" placeholder="mm/dd/yy"/></td>
            <td><input type="text" name="desc" placeholder="Chair"/></td>
            <td>
              <select name="type" style="width: auto; height: 105%; font-size: 20px;">
                <option value="Credit">Credit</option>
                <option value="Cash">Cash</option>
              </select>
            </td>
            <td><input type="text" name="amount" placeholder="150.00"/></td>
            <td><input type="text" name="note" placeholder="Chair at IKEA"/></td>
          </tr>
          <tr>
            <td colspan="5" style="padding-top: 10px;"><button type="submit" style="width: 100%; height: 50px; font-size: 20px"><b>ENTER<b></button></td></form>
          </tr>
        </tbody>
      </table>
    </div>
    <!-- EDIT ROW -->
    <div class="editRow_prompt input_table">
      <button type="button" style="position: relative; float: right" onclick="closePrompt()">X</button>
      <form action="/expense_data.php" method="get" style="margin-bottom: 0;">
      <table>
        <thead>
            <tr style="color: #fff; font-size: 20px">
              <th><i class="fa fa-calendar fa-2x" aria-hidden="true"></i><br>Date</th>
              <th><i class="fa fa-money fa-2x" aria-hidden="true"></i><br>Expense</th>
              <th>Type</th>
              <th><i class="fa fa-usd fa-2x" aria-hidden="true"></i><br>Amount</th>
              <th><i class="fa fa-pencil fa-2x" aria-hidden="true"></i><br>Note</th>
            </tr>
        </thead>
        <tbody style="text-align: center">
          <tr>
            <input type="hidden" name="action" value="editRow"/>
            <input type="hidden" name="id" value=""/>
            <input type="hidden" name="table" value="<?php echo $name ?>"/>
            <td><input class="datepicker" type="text" name="date" placeholder="mm/dd/yy"/></td>
            <td><input type="text" name="desc" placeholder="Chair"/></td>
            <td>
              <select name="type" style="width: auto; height: 105%; font-size: 20px;">
                <option value="Credit">Credit</option>
                <option value="Cash">Cash</option>
              </select>
            </td>
            <td><input type="text" name="amount" placeholder="150.00"/></td>
            <td><input type="text" name="note" placeholder="Chair at IKEA"/></td>
          </tr>
          <tr>
            <td colspan="5" style="padding-top: 10px;"><button type="submit" style="width: 100%; height: 50px; font-size: 20px"><b>ENTER<b></button></td>
          </tr>
          <!--<tr>
            <td colspan="5" style="padding-top: 10px;"><button onclick="confirmDel()" style="background-color: red; width: 100%; height: 50px; font-size: 20px"><b>DELETE<b></button></td>
          </tr>-->
        </tbody>
      </table></form>
    </div>
  </body>
  <!-- MAIN SCRIPTS -->
  <script>
    function confirmDel() {
      var x;
      if (confirm("Do you really want to delete this row ?") == true)
          $('.editRow_prompt input[name="action"]').val("deleteRow");
    }
    // Row Edit Click
    $( ".edit_Expense_row" ).click(function() {
      //var $rowIndex = $(this).closest("tr").index() + 1;    // Find the row
      $('.addRow_prompt').attr('style','visibility: invisible');
      var $row = $(this).closest("tr"),
          $tds = $row.find("td");    // Find the row
      $.each($tds, function(index) {               // Visits every single <td> element
        switch (index) {
          case 0:
            $('.editRow_prompt input[name="id"]').val($.trim($( this ).text()));
            break;
          case 1:
            $('.editRow_prompt input[name="date"]').val($( this ).text());
            break;
          case 2:
            $('.editRow_prompt input[name="desc"]').val($( this ).text());
            break;
          case 3:
            $('.editRow_prompt select[name="type"]').val($( this ).text());
            break;
          case 4:
            $('.editRow_prompt input[name="amount"]').val($( this ).text().replace('$ ', ''));
            break;
          case 5:
            $('.editRow_prompt input[name="note"]').val($( this ).text());
        }
      });
      $('.editRow_prompt').attr('style','visibility: visible');
      $(".editRow_prompt input[type='text'][name='desc']").focus();
    });
    // Row Edit Hover
    $(".edit").hover(function(){
        $('.edit_Expense_row').css("display", "");
        }, function(){
        $('.edit_Expense_row').css("display", "none");
    });
    $( "#addRow" ).click(function() {
      $('.editRow_prompt').attr('style','visibility: invisible');
      $('.addRow_prompt').attr('style','visibility: visible');
      $(".addRow_prompt input[type='text'][name='date']").focus();
    });
    $( function() {
      $( ".datepicker" ).datepicker();
    } );
    function closePrompt() {
      $('.addRow_prompt').attr('style','visibility: invisible');
      $('.editRow_prompt').attr('style','visibility: invisible');
    };
    /* When the user clicks on the button,
    toggle between hiding and showing the dropdown content */
    function myFunction() {
        document.getElementById("myDropdown").classList.toggle("show");
    }

    // Close the dropdown menu if the user clicks outside of it
    window.onclick = function(event) {
      if (!event.target.matches('.dropbtn')) {

        var dropdowns = document.getElementsByClassName("dropdown-content");
        var i;
        for (i = 0; i < dropdowns.length; i++) {
          var openDropdown = dropdowns[i];
          if (openDropdown.classList.contains('show')) {
            openDropdown.classList.remove('show');
          }
        }
      }
    }
    // SAVE & NEW
    $(".save_N_new").click(function(){
      var d = new Date();

      var month = d.getMonth()+1;
      var day = d.getDate();

      var output = ((''+month).length<2 ? '0' : '') + month + '/' +
          ((''+day).length<2 ? '0' : '') + day + '/' +  d.getFullYear();

      var x = prompt("Give this table a name.",output);
      if ( x != null) {
          $('.editRow_prompt input[name="action"]').val("saveNnew");
          $('.editRow_prompt input[name="desc"]').val(x);
          $( ".editRow_prompt form" ).submit();
      }
    });
    // ESC TO exit
    $(document).keydown(function(e){
      switch(e.which){
          case 27:
            $('.addRow_prompt').attr('style','visibility: invisible');
            $('.editRow_prompt').attr('style','visibility: invisible');
            break;
          default: return;
        }
      });
  </script>
</html>
<?php //============================ FUNCTION ============================
function loadTable($table_name) {
  global $link;?>
  <table id="mainDataTable" class="display nowrap cell-border" cellspacing="0" width="100%" style="border-left: 1px solid black; border-right: 1px solid black; font-size: 20px;">
    <thead>
        <tr>
          <th colspan="6" style="background-color: #eed369"><?php echo ltrim($table_name,'e_') ?><span style="float: right">
            <!-- <div class="dropdown" onclick="myFunction()">
              <button class="dropbtn">New</button>
              <div id="myDropdown" class="dropdown-content">
                <a href="#" class="save_N_new"><i class="fa fa-save" aria-hidden="true"></i> | Save & New</a>
              </div>
            </div></span>
          </th>
        </tr>-->
        <tr>
          <th colspan="6"><a href="#" id="addRow"><i class="fa fa-plus-square" aria-hidden="true"> New Row</a></th>
        </tr>
        <tr>
          <th>#</th>
          <th><i class="fa fa-calendar fa-2x" aria-hidden="true" style="color: #000080"></i><br>Date</th>
          <th><i class="fa fa-money fa-2x" aria-hidden="true" style="color: green"></i><br>Expense</th>
          <th>Type</th>
          <th><i class="fa fa-usd fa-2x" aria-hidden="true"></i><br>Amount</th>
          <th><i class="fa fa-pencil fa-2x" aria-hidden="true"></i><br>Note</th>
        </tr>
    </thead>
    <tbody style="text-align: center">
        <?php
        $result = mysqli_query($link,"SELECT * FROM `$table_name`");
        if(!$result) {
           echo "<p>Invalid form...</p>";
           return;
        }
        while($row=mysqli_fetch_array($result, MYSQLI_BOTH)) {
          if ($row['type'] == 'Cash')
            echo '<tr id="cash_pay">';
          else
            echo '<tr>';
          echo '
              <td class="edit"><span class="edit_Expense_row" style="display: none; float: left"><a href="#"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a></span> ' . $row['id'] . '</td>
              <td>' . $row['date'] . '</td>
              <td>' . $row['expense'] . '</td>
              <td>' . $row['type'] . '</td>
              <td>$ ' . $row['amount'] . '</td>
              <td>' . $row['note'] . '</td>
            </tr>
            ';
        }
        ?>
    </tbody>
    <tfoot>
    <tr>
        <th colspan="6" style="text-align:right; background-color: #fff">Total:
          <?php
          $result = mysqli_query($link,"SELECT * FROM `$table_name`");
          if(!$result) {
             echo "<p>Invalid form...</p>";
             return;
          }
          $total = 0;
          while($row=mysqli_fetch_array($result, MYSQLI_BOTH)) {
            $amount = $row['amount'];
            $total += $amount;
          }
          echo '$ ' . $total;
          ?>
        </th>
    </tr>
    </tfoot>
  </table>
<?php } // End of loadtable()
function exeQuery($query) {
    global $link;
    $result = mysqli_query($link,$query); //  execute mySQL query
    if(!$result) {
       echo "<p>Invalid form...</p>";
       return;
    }
  }
function getColFromQuery($query,$colname) {
    global $link;
    $result = mysqli_query($link,$query); //  execute mySQL query
    if(!$result) {
       echo "<p>Invalid form...</p>";
       return;
    }
    while($row=mysqli_fetch_array($result, MYSQLI_BOTH))
      $temp = $row[$colname];
      return $temp;
  }
?>
