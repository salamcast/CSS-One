CSS One, unify your your style sheet and it's images into one file

@package css_one
@authour Karl Holz <newaeon|A|mac|d|com>

June 27, 2012
 

 CSS One
 -> bigger style sheet 
 -> less http requests 
 -> faster loading 



For an example of how to use this class in your php/jQuery project, look at index.php

CSS One will do
- print minifyed CSS with images embedded into the document output
- print HTML5 or xHTML output with a the ability to add custom feeds, js files/links and a 
custom HTML (<body /> only) for your widget markup.  


HowTo mix and print CSS, for the best performance, please keep your styles in different folders with their images.
like the jQuery UI example, use relative path in your CSS file for the best results

$css=new css_one();
$css->add_style('./book/style1.css');
$css->add_style('./css/style2.css');
$css->printCSS();
exit();

CSS One has been only tested with jQuery UI css release files - http://jqueryui.com/download 
You can create custom themes on their site - http://jqueryui.com/themeroller/

HTML5 / xHTML output

$css=new css_one();
// <title />
$css->title="CSS One ";
// <meta />
$css->description="This is a jQuery UI CSS theme testing tool";
// <meta />
$css->keywords="HTML5, css, base64 images, phpclasses";
// add ATOM feed
$css->add_atom('Test jQuery UI',$_SERVER['SCRIPT_NAME'].'/feed.atom');
// add jquery, will default to web link if file is not avaliable
$css->set_jquery('/js/jquery-1.7.2.min.js');
// add jquery-ui, will default to web link if file is not avaliable
$css->set_jquery_ui('/js/jquery-ui-1.8.21.custom.min.js');
// add custom javascript
$css->add_js('/js/ui-demo.js');
// this css is a link to the document or script
$css->add_style('style.php');
// load HTML5/xHTML markup
$css->load_body(__DIR__.'/jquery-ui/demo.html'); 
echo $css;

