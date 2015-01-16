<?php
/*
 *  Tämä ohjelmakoodi on muokattu versio alkuperäisesti David Consuegran veriosta, joka löytyy täältä: 
 *  http://advancedkittenry.github.io/koodaaminen/testisovellus.html
 */

/***************************************************************/
/* Koodia. Tästä tiedostosta ei kannata ottaa mallia mihinkään */
/***************************************************************/

$connection_config = DatabaseConfig::connection_config();

function getDatabase() {
  return DB::connection();
}
function getObjects() {
  $args = func_get_args();
  $sql = array_shift($args);
  $query = getDatabase()->prepare($sql);
  $query->execute($args);
  return $query->fetchAll(PDO::FETCH_OBJ);
}
function getValue() {
  $args = func_get_args();
  $sql = array_shift($args);
  $query = getDatabase()->prepare($sql);
  $query->execute($args);
  return $query->fetchColumn();
}

class TableLister {
  protected $name;

  public function __construct($name) {$this->name = $name;}
  public function getName() { return $this->name; }
  public function quoteName() { return static::TABLE_QUOTE.$this->name.static::TABLE_QUOTE; }

  public function getRowCount() {
    return getValue('SELECT count(*) FROM '.$this->quoteName());
  }
  public function getRows($limit = 100) {
    return getObjects("SELECT * FROM ".$this->quoteName()." LIMIT ".(int)$limit);
  }
}
class MySqlTableLister extends TableLister {
  const TABLE_QUOTE = '`';

  public static function getTables() {
    $objs = getObjects("SHOW TABLES");
    $ret = array();
    foreach($objs as $obj) {
      foreach($obj as $v) {
        $ret[] = new self($v);
        break;
      }
    }
    return $ret;
  }
  public function getColumns() {
    return getObjects("DESCRIBE ".$this->quoteName());
  }
}
class PgSqlTableLister extends TableLister {
  const TABLE_QUOTE = '"';

  public static function getTables() {
    $objs = getObjects("select tablename from pg_tables where tableowner != 'postgres' order by tablename");
    $ret = array();
    foreach($objs as $obj) {
      $ret[] = new self($obj->tablename);
    }
    return $ret;
  }
  public function getColumns() {
    $sql = <<<SQL
SELECT
    f.attname AS name,
    f.attnotnull AS notnull,
    pg_catalog.format_type(f.atttypid,f.atttypmod) AS type,
    CASE
        WHEN p.contype = 'p' THEN 't'
        ELSE 'f'
    END AS primarykey,
    CASE
        WHEN p.contype = 'u' THEN 't'
        ELSE 'f'
    END AS uniquekey,
    CASE
        WHEN p.contype = 'f' THEN g.relname
    END AS foreignkey,
    CASE
        WHEN p.contype = 'f' THEN p.confkey
    END AS foreignkey_fieldnum,
    CASE
        WHEN p.contype = 'f' THEN g.relname
    END AS foreignkey,
    CASE
        WHEN p.contype = 'f' THEN p.conkey
    END AS foreignkey_connnum,
    CASE
        WHEN f.atthasdef = 't' THEN d.adsrc
    END AS default
FROM pg_attribute f
    JOIN pg_class c ON c.oid = f.attrelid
    JOIN pg_type t ON t.oid = f.atttypid
    LEFT JOIN pg_attrdef d ON d.adrelid = c.oid AND d.adnum = f.attnum
    LEFT JOIN pg_namespace n ON n.oid = c.relnamespace
    LEFT JOIN pg_constraint p ON p.conrelid = c.oid AND f.attnum = ANY (p.conkey)
    LEFT JOIN pg_class AS g ON p.confrelid = g.oid
WHERE c.relkind = 'r'::char
    AND n.nspname = 'public'
    AND c.relname = ?  -- Replace with Schema name
    AND f.attnum > 0 ORDER BY f.attnum;
SQL;
    return getObjects($sql, $this->name);
  }
}
function prettyprint($data) {
  if (count($data) == 0) {
    echo '<p>Taulussa ei ole vielä dataa</p>';
    return;
  }
  echo '<table class="table table-striped table-bordered">';
  echo '<tr>',implode('', array_map(function($t) {return "<th>$t</th>";}, array_keys((array)array_shift(array_values($data))))),'</tr>';
  foreach($data as $datum) {
    echo '<tr>',implode('', array_map(function($t) {return "<td>$t</td>";}, (array)$datum)),'</tr>';
  }
  echo '</table>';
}

try {
  $dbtype = $connection_config['db'];
  switch($dbtype) {
    case 'mysql':
      $tables = MySqlTableLister::getTables();
       break;
    case 'psql':
      $tables = PgSqlTableLister::getTables();
      break;
    default:
      die("Unknown database");
  }
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Tietokantayhteystesti</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css">
  <script src="vendor/jquery.min.js"></script>
  <script src="vendor/bootstrap/js/bootstrap.min.js"></script>
  <style>.panel h2 {text-transform: capitalize; } th {text-transform: capitalize;}</style>
</head>
<body style="padding: 25px;">
<div class="container" id="content">
  <div class="page-header">
    <h1>Projektin tietokantataulut</h1>
  </div>
<div class="alert alert-success">Tietokantayhteyden muodostaminen onnistui!</div>
<?php
foreach($tables as $table): ?>
  <h2><?= $table->getName(); ?></h2>
  <?php prettyprint($table->getColumns()); ?>
  <button type='button' class='btn-link expandable collapsed' data-toggle='collapse' data-target='#expandable_<?= $table->getName(); ?>'>
  Tietokantataulussa on yhteensä <?= $table->getRowCount(); ?> riviä:
  </button>
  <div id='expandable_<?= $table->getName(); ?>' class='collapse'>
  <?php prettyprint($table->getRows()); ?>
  </div>
<?php endforeach; ?>
</div>
</body>
</html><?php } catch (Exception $e) {
  echo '<pre>', $e->getMessage();
}
?>
