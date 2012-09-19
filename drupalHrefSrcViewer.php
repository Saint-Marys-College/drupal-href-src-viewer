<?php
  // header("Content-Type: text/plain");
?>
<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title></title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width">
        <link rel="stylesheet" href="style.css" type="text/css" />
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js"></script>
    </head>
    <body>
        <!--[if lt IE 7]>
            <p class="chromeframe">You are using an outdated browser. <a href="http://browsehappy.com/">Upgrade your browser today</a> or <a href="http://www.google.com/chromeframe/?redirect=true">install Google Chrome Frame</a> to better experience this site.</p>
        <![endif]-->

        <!-- Add your site or application content here -->
<?php
function printFilesAndImagesFromDB(array $databaseName)
{
  $hostnamePath = current($databaseName);
  $databaseName = key($databaseName);
  $rows = fetchFromDB($databaseName);
  printTableHeader($databaseName);
  processRows($rows, $hostnamePath, $databaseName);
  printTableEnd();
}

function fetchFromDB($databaseName)
{
  $mysqli = new mysqli('localhost', '', '', $databaseName);
  if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit;
  }
  $result = $mysqli->query("SELECT nid, body FROM node_revisions WHERE body REGEXP '<img.*\.jpg.*>' OR body REGEXP '<a.*/files/.*>' OR body REGEXP '<img.*\.png.*>' OR body REGEXP '<img.*\.gif.*>' ORDER BY nid");
  if (!$result) {
    printf("Query failed: %s\n", $mysqli->error);
    exit;
  }
  while ($row = $result->fetch_assoc()) {
    $rows[]=$row;
  }
  $mysqli->close();
  return $rows;
}

function processRows($rows, $hostnamePath, $databaseName)
{
  foreach ($rows as $row) {
    processRow($row, $databaseName, $hostnamePath);
  }
}

function printTableHeader($databaseName)
{
  echo "<table border=\"1\"><caption>$databaseName</caption><thead><tr><td>NID</td><td>Files</td></tr></thead><tbody>";
}

function printTableEnd()
{
  echo '</tbody></table>';
}

function printRowStart($hostnamePath, $nid)
{
  echo "<tr><td><a href=\"http://$hostnamePath/node/$nid\">$nid</a></td><td>";
}

function printRowEnd()
{
  echo '</td></tr>';
}

function printMatches($string, $context)
{
  $classes = array($context);
  if (strpos($string, 'www.saintmarys.edu') !== false) {
    $classes[] = 'www';
  }
  if (strpos($string, 'www3.saintmarys.edu') !== false) {
    $classes[] = 'absolute';
  }
  if (strpos($string, '/') === 0) {
    $classes[] = 'relative';
  } else {
    if (strpos($string, 'saintmarys.edu') === false) {
      $classes[] = 'offsite';
    }
  }
  if (strpos($string, 'mailto') !== false) {
    $classes[] = 'mailto';
  }
  if (strpos($string, 'centerforwomeninleadership') !== false) {
    $classes[] = 'cwil';
  }
  echo '<li><p class="';
  $flag = false;
  foreach ($classes as $class) {
    if ($flag) {
      echo ' ';
    } else {
      $flag = true;
    }
    echo "$class";
  }
  echo "\">$string</p></li>";
}

function processRow($row, $databaseName, $hostnamePath)
{
  printRowStart($hostnamePath, $row['nid']);
  $hrefMatches = array();
  $srcMatches  = array();
  preg_match_all('/href=[\"\']((?:https?:\/\/)?[^\"\']*?\.[^\"\']{3,4})?\/?[\"\']/', $row['body'], $hrefMatches);
  preg_match_all('/src=[\"\']((?:https?:\/\/)?[^\"\']*?\.[^\"\']{3,4})?\/?[\"\']/', $row['body'], $srcMatches);
  if (!empty($hrefMatches[1])) {
    echo '<h2>href</h2><ul>';
    foreach ($hrefMatches[1] as $string) {
      printMatches($string, 'href');
    }
    echo '</ul>';
  }
  if (!empty($srcMatches[1])) {
    echo '<h2>src</h2><ul>';
    foreach ($srcMatches[1] as $string) {
      printMatches($string, 'src');
    }
    echo '</ul>';
  }
  printRowEnd();
}
?>
<table border="1">
  <thead>
    <tr>
      <th>Color Key</th>
    </tr>
  </thead>
  <tbody class="noStripe">
    <tr>
      <td class="key">None of the below</td>
    </tr>
    <tr>
      <td class="key www">On www</td>
    </tr>
    <tr>
      <td class="key absolute">Absolute</td>
    </tr>
    <tr>
      <td class="key relative">Relative</td>
    </tr>
    <tr>
      <td class="key offsite">Offsite Link</td>
    </tr>
    <tr>
      <td class="key mailto">Mail To</td>
    </tr>
    <tr>
      <td class="key cwil">BAD CWIL</td>
    </tr>
  </tbody>
</table>

<?php
printFilesAndImagesFromDB(array('www3drupal01'=>'www3.saintmarys.edu',));
printFilesAndImagesFromDB(array('campaign'=>'www3.saintmarys.edu/campaign-steering',));
printFilesAndImagesFromDB(array('commencement'=>'www3.saintmarys.edu/commencement-experience',));
printFilesAndImagesFromDB(array('library'=>'www3.saintmarys.edu/library',));
printFilesAndImagesFromDB(array('quest'=>'www3.saintmarys.edu/quest',));
printFilesAndImagesFromDB(array('tickets'=>'www3.saintmarys.edu/tickets',));
printFilesAndImagesFromDB(array('trustees'=>'www3.saintmarys.edu/trustees',));
?>
<script type="text/javascript">
  $(function() {
    $("tr:odd").addClass("odd");
  });
</script>
  </body>
</html>