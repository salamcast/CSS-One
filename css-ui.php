<?php
/**
 * @authour karl holz
 * @package css_one
 * 
 * 
 * 
 */

class css_one {
    private $match='/[A-Za-z0-9%.,_-]+?\.css/';
    private $img=array();
    private $type=array('jpg', 'gif', 'png');
    public  $style=array();
    public  $js=array();
    public  $atom=array();
    /**
     * __set img array
     * @param type $name
     * @param type $value 
     */
    function __set($name, $value) {
        $this->img[$name]=$value;
    }
    
    /**
     * __get item if the key exists
     * @param type $name
     * @return type 
     */
    function __get($name) {
        if (array_key_exists($name, $this->img)) {
            return $this->img[$name];
        } else {
            return;
        }
    }
    
    
    /**
     * __construct boot strap the class
     * @param type $feed
     * @return boolean 
     */
    function __construct() {
        $this->dir=__DIR__;
        $this->id=__CLASS__;
        $this->HTML5=TRUE;
        $this->css='';


//        $this->style[]=$this->make_rest_link($this->get_css());
    }
    
    function set_jquery($j='') {
        if ($j != '' && is_file(__DIR__.'/'.$j)) { 
            $this->js[]=$j; 
        } else { 
            $this->js[]='https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js'; 
        }        
    }
    
    function set_jquery_ui($ui='') {
        if ($ui != '' && is_file(__DIR__.'/'.$ui)) { 
            $this->js[]=$ui; 
        } else { 
            $this->js[]='https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/jquery-ui.min.js'; 
        }   
    }
    /**
     * make http rest link
     * @param type $css
     * @return type 
     */
    function make_rest_link($css) {
        return 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'].'?'.$this->rest_fix($css);
    }
    
    /**
     * strip off root dir to form rest link to style sheet
     * @param type $css
     * @return type 
     */
    function rest_fix($css) {
        return str_replace(array($this->dir), array(''), $css);
    }
    
    function add_atom($l){
        $this->atom[]=$l;
    }
    

    /**
     * checks CSS file is a file
     * @return boolean 
     */
    function rest_check() {
        $css=$this->get_css();
        if (is_file($css)) return $css;
        header("HTTP/1.1 404 Not Found");
        ?><h2>HTTP/1.1 404 Not Found</h2>
        <?php echo $this->rest; ?><br/>File is not avaliable on this system<?php
    }
    
    /**
     * generate a glob string for all img types
     */ 
    function get_img_glob($d) {
        $c='{';        
        foreach ($this->type as $t) {
            $c.=$this->get_css_dir($d).'*.'.$t.','.$this->get_css_dir($d).'*/*.'.$t.',';
        }
        $c=rtrim($c, ',');
        $c.='}';
        return $c;
    }
    
    /**
     * get CSS file
     * @return string
     */
    function get_css() {
        return $this->dir.$this->rest;
    }
    /**
     * get the CSS directory for image searching
     * @return string 
     */
    function get_css_dir($d) {
        
        return $this->dir.preg_replace($this->match, '', trim($d, '.'));
    }
    /**
     * encode image list for faster stlye loading
     * @param type $imagefile
     * @return type 
     */
    function base64_encode_image ($imagefile) {
        $filetype = strtolower(pathinfo($imagefile, PATHINFO_EXTENSION));
        if (in_array($filetype, $this->type)){
            $imgbinary = fread(fopen($imagefile, "r"), filesize($imagefile));
        } else {
            return $imagefile;
        }
        return 'data:image/'.$filetype.';base64,'.base64_encode($imgbinary);
    }
    
    /**
     * encode all images found in the css directory 
     */
    function encode_img($c) {
        $imgs=array(); 
        $dir=glob($this->get_img_glob($c), GLOB_BRACE );
        foreach ($dir as $d) {
            $imgs['file'][]=str_replace($this->get_css_dir($c), '', $d);
            $imgs['encode'][]=$this->base64_encode_image($d)  ;
        }
        $this->imgs=$imgs;
        return $imgs;
    }


    /**
     * Print CSS output with images encoded into the style 
     */
    function printCSS() {
        header("Content-type: text/css");
        $this->makeOneCSS();
//        if ($this->css=='') {
//            print_r($this);
//        } else {
            echo $this->css;
//        }
        exit();
    }
    
    function makeOneCSS () {
        foreach ($this->style as $s) {
         $c=@file_get_contents($s);  
         $this->encode_img($s);
         $css=$this->compress($c);
         if (array_key_exists('file', $this->imgs) && array_key_exists('encode', $this->imgs)) {
            $this->css.=str_replace($this->imgs['file'], $this->imgs['encode'], $css);
         } else { $this->css.=$css; } 
        }

        return ;
    }
    
    
    /**
     * prints an ATOM feed of CSS files that have been found in the curent folder 
     */
    function CSSfeed() {
     $this->id=md5($this->dir);
     $date=gmdate(DATE_ATOM,filectime($this->dir));
     header('Content-type: application/atom+xml');
     echo <<<H
<?xml version="1.0" encoding="utf-8"?>
<feed xmlns="http://www.w3.org/2005/Atom">
  <id>$this->id</id>
  <title type="text">Found CSS files</title>
  <updated>$date</updated>
  <subtitle type="text">These css files will have all found images from current directory and one bellow </subtitle>
  
H
     ;
     $styles=  glob('./css/*/*.custom.css');
     foreach ($styles as $c) {
      $link=$this->make_rest_link($c);
      $tag=$this->rest_fix($c);
      $size=  filesize($this->dir.'/'.$tag);
      $d=$date=gmdate(DATE_ATOM,filectime($this->dir.'/'.$tag));
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
    }
    
  /**
   * I found this online, looked cool so I borrowed it, check the link bellow
   * The Reinhold Weber method
   * -http://www.catswhocode.com/blog/3-ways-to-compress-css-files-using-php
   * 
   * this will strip out all comments and newlines,tabs,plus uother un-needed space wasters.
   * 
   * @param type $buffer
   * @return type 
   */
  function compress($buffer) {
    /* remove comments */
    $buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);
    /* remove tabs, spaces, newlines, etc. */
    $buffer = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $buffer);
    return $buffer;
  }

  /**
   * add style sheet
   * @param type $css 
   */
  function add_style($css) {
      $this->style[]=$css;
  }
  
  /**
   * add javascript
   * @param type $js 
   */
  function add_js($js) {
      $this->js[]='http://'.$_SERVER['HTTP_HOST'].$js;
  }
  
  /**
   * load html widget template
   * @param type $h 
   */
  function load_body($h) {
      if (is_file($h)) $this->body=file_get_contents($h);
      
  }
 /**
  * xHTML/HTML5 __toString output
  * set to false for xHTML/css/jquery 
  */
 function __toString() {
  $css=$this->makeOneCSS();  
  if (stristr($_SERVER['HTTP_ACCEPT'], "application/xhtml+xml") && $this->HTML5 !== TRUE) {
   $this->mime="application/xhtml+xml";
   header("Content-Type: ".  $this->mime);
   print '<?xml version="1.0" encoding="utf-8"?>';
   $ns='xmlns="http://www.w3.org/1999/xhtml"';
  } else {
   $this->mime="text/html";
   header("Content-Type: ".  $this->mime);
   print '<!DOCTYPE HTML>';
   $ns='';
  }
  $js='';

  if (count($this->style) > 0) {
   foreach ($this->style as $j) { //append css style
  $js.='<link type="text/css" href="'.$j.'" rel="stylesheet" />'."\n";   }   
  }
  if (count($this->js) > 0) {
   foreach ($this->js as $j) { //append css style
    $js.='<script type="text/javascript" src="'.$j.'"></script>'."\n";
   }   
  }
    if (count($this->atom) > 0) {
   foreach ($this->atom as $k => $j) { //append css style
    $js.='<link type="application/atom+xml" href="'.$j.'" rel="alternate" title="'.$k.'" />'."\n";
   }   
  }

  $div='';
  if ($this->feed) {
    $styles=  glob('{'.__DIR__.'/*/*.css, '.__DIR__.'/*.css }', GLOB_BRACE );
    foreach ($styles as $c) {
     $link=str_replace(array(__DIR__), array(''), $c);
     $div.='<a href="http://'.$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'].'?'.$link.'" >'.$link.'</a><br />';
    }
  }
  return <<<H
<html $ns >
 <head>
  <title>$this->title</title>
  <meta http-equiv="Content-Type" content="$this->mime; charset=utf-8" />
  <meta http-equiv="Content-Language" content="en-us" />
  <style type="text/css">
   $css
  </style>
  <!-- Load Javascript and CSS files to this basic HTML skel layout. -->
  <!-- let you're JavaScript and CSS build and manage the UI -->
  $js
 
 </head>
 <body>
  <div id='$this->id' >
   $div
  </div>
  $this->body
 </body>
</html>
H
;

 }

}

?>
