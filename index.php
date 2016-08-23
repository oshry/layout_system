<?php
require_once './env.php';
require DOCROOT.'./vendor/autoload.php';
$config = include 'config/db.php';
//singleton db
$db_instance = Repository\DataRepository::instance($config, 'default');
//config mustache foldegit rs
$m = new Mustache_Engine(array(
    'loader' => new Mustache_Loader_FilesystemLoader(dirname(__FILE__) . '/views'),
    'partials_loader' => new Mustache_Loader_FilesystemLoader(dirname(__FILE__). '/views/partials')
));
//check if admin
if($paths[0] === 'admin'){
    //load admin
    $admin = new Layout\Admin\AdminPanel($db_instance);
    $admin->load($method, $paths, $m);
    die();
}
//load page
$query = "SELECT * FROM `pages` p LEFT JOIN `layouts` l ON p.layout_id = l.id LEFT JOIN `areas` a ON a.page_id = p.id WHERE p.url = :page";
$statement = $db_instance->db->prepare($query);
$statement->bindValue(':page', $page);
$statement->execute();
$result = $statement->fetchAll();
//echo "<pre>",print_r($result),"</pre>";
$first = true;
$i = 1;
$output = "";
foreach($result as $row) {
    if($first){
        $first = false;
        //first time load page layout to the output
        $output = $row['layout_html'];
    }
    $current_block ="";
    //create area with the right module and configuration
    switch($row['module_type']){
        case 1:
            //external url
            $current_block = "external url<br>";
            $ex_url = new Layout\Modules\ExternalUrl($db_instance);
            $current_block .= $ex_url->init($row['module_configuration_id']);
            break;
        case 2:
            //news ticker
            $current_block = "news ticker<br>";
            $current_block .= "print ticker";
            break;
        case 3:
            //movie
            $current_block =  "movie block";
            break;
        case 4:
            //etc
        default:
            die('module doesn\'t exist');
    }
    //get current module namespace
    $current_module = sprintf($module_name, $i);
    //place area in output(layout)
    $output = str_replace($current_module, $current_block, $output);
    $i++;
}
echo $output;
die();