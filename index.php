<?php

/*
CSS One Tester page
 * This tester uses modifyd parts of the jquery ui demo, 
 * but i split them up into 3 files: html, css, js
 * it's so i can give you a better idea of how this class works and how it's can 
 * help you solve your 
 */          
//      file selecter
$webdir=  str_replace(array($_SERVER['DOCUMENT_ROOT']), array(''), dirname(__FILE__));
if (array_key_exists('style', $_GET)) { 
   $style=$_GET['style']; 
} else { $style= ''; }
// view switcher
if  (array_key_exists('ORIG_PATH_INFO', $_SERVER)) { 
   $rest=$_SERVER['ORIG_PATH_INFO']; 
} elseif  (array_key_exists('PATH_INFO', $_SERVER)) { 
   $rest=$_SERVER['PATH_INFO']; 
} else { $rest=''; }
        
require_once 'css-ui.php';
switch ($rest) {
    /**
     * Style CSS
     * this will return CSS output with images encoded into the style, minifyed
     * and combined with other style sheet 
     */
    case '/style.css':
        $css=new css_one();
        // if no style pass with query string,
        // use first custom jquery ui css file
        if ($style == '') {
            $d=glob('./css/*/*.custom.css');
            $style=$d[0];
        }
        $css->add_style($style);
        // add custom css
        $css->add_style('./css/ui-demo.css');
        $css->printCSS();
        exit();
    break;
    /**
     * ATOM Feed of different jquery ui CSS styles view
     */
    case '/feed.atom':
     $id=md5(dirname(__FILE__));
     $date=gmdate(DATE_ATOM,filectime(dirname(__FILE__)));
     header('Content-type: application/atom+xml');
     echo <<<H
<?xml version="1.0" encoding="utf-8"?>
<feed xmlns="http://www.w3.org/2005/Atom">
  <id>$id</id>
  <title type="text">CSS One</title>
  <updated>$date</updated>
  <subtitle type="text">jQuery UI theme tester</subtitle>
  
H
     ;
     $styles=  glob('./css/*/*.custom.css');
     foreach ($styles as $c) {
      $link='http://'.$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'].'?style='.$c;
      $tag=str_replace(array('/'.basename($c), './css/'), array('', ''), $c);
      $size=  filesize($c);
      $d=$date=gmdate(DATE_ATOM,filectime($c));
      echo <<<H
  <entry>
    <title>$tag</title>
    <id>$tag</id>
    <updated>$d</updated>
    <link rel="enclosure" type="text/css" length="$size" href="$link"/>
    <summary>$tag</summary>
  </entry>
  
H
      ;
     }
     echo <<<H
</feed>
H
     ;
     exit();
    break;
    /**
     * xHTML/HTML5 output shell, HTML5 is default
     * -> set $css->HTML5=FALSE; // for xHTML output 
     * use this to:
     * - load your custom javascript
     * - add atom feeds
     * - add style
     * - load body markup
     */
    default :
        $css=new css_one();
        $css->title="CSS One - jQuery UI demo tester";
        $css->description="This is a jQuery UI CSS theme testing tool";
        $css->keywords="HTML5, css, base64 images, phpclasses";
        // add ATOM feed
        $css->add_atom('Test jQuery UI',$_SERVER['SCRIPT_NAME'].'/feed.atom');
        // add jquery, will default to web link if file is not avaliable
        $css->set_jquery($webdir.'/js/jquery-1.7.2.min.js');
        // add jquery-ui, will default to web link if file is not avaliable
        $css->set_jquery_ui($webdir.'/js/jquery-ui-1.8.21.custom.min.js');
        // add custom javascript
        $css->add_js($webdir.'/js/ui-demo.js');
        // set dynamic css changer for jquery-ui
        // this script will have it's images embedded and css minifyed
        if ($style == '') {
            $style=$_SERVER['SCRIPT_NAME'].'/style.css';
        } else {
            $style=$_SERVER['SCRIPT_NAME'].'/style.css?style='.$style;
        }
        $css->add_style($style);
        // load HTML5/xHTML markup
        $css->load_body(dirname(__FILE__).'/jquery-ui/demo.html'); 
        echo $css;

    break;
}
?>
