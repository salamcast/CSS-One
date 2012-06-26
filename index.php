<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
        if (array_key_exists('QUERY_STRING', $_SERVER)) { 
            $style=$_SERVER['QUERY_STRING']; 
        } else { $style= ''; }
        if  (array_key_exists('ORIG_PATH_INFO', $_SERVER)) { 
            $rest=$_SERVER['ORIG_PATH_INFO']; 
        } elseif  (array_key_exists('PATH_INFO', $_SERVER)) { 
            $rest=$_SERVER['PATH_INFO']; 
        } else { $rest=''; }
require_once 'css-ui.php';
switch ($rest) {
    case '/style.css':
        $css=new css_one();
        if ($style == '') {
            $d=glob('./css/*/*.custom.css');
            $style=$d[0];
        }
//        print_r($d); exit();
        
        $css->add_style($style);
        $css->add_style('css/ui-demo.css');
        $css->printCSS();
    break;
    case '/feed.atom':
        $css=new css_one();
        $css->CSSfeed();
        exit();
    break;
default :

$css=new css_one();
$css->js=array(); // clear out the web based jquery and use local
// add js, use absolute web path
$css->add_atom($_SERVER['SCRIPT_NAME'].'/feed.atom');
$css->set_jquery('/jQuery-UI/js/jquery-1.7.2.min.js');
$css->set_jquery_ui('/jQuery-UI/js/jquery-ui-1.8.21.custom.min.js');
$css->add_js('/jQuery-UI/js/ui-demo.js');
if ($style == '') {
    $style=$_SERVER['SCRIPT_NAME'].'/style.css';
} else {
    $style=$_SERVER['SCRIPT_NAME'].'/style.css?'.$style;
}
$css->add_style($style);
//add css, use web path

// use real path
$css->load_body(__DIR__.'/jquery-ui/demo.html'); 
echo $css;

    break;
}




?>
