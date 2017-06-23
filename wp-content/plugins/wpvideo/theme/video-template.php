<?php

/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * e.g., it puts together the home page when no home.php file exists.
 *
 * Learn more: {@link https://codex.wordpress.org/Template_Hierarchy}
 *
 * @package WordPress
 * @subpackage Video
 * @since Video 1.0
 */

if (!function_exists('strposX'))
{	
function strposX($haystack, $needle, $number) 
{
    // decode utf8 because of this behaviour: https://bugs.php.net/bug.php?id=37391
    preg_match_all("/$needle/", utf8_decode($haystack), $matches, PREG_OFFSET_CAPTURE);
    return $matches[0][$number-1][1];
}
}	
	
	
	
	
	
	$current_page=substr($_SERVER['REQUEST_URI'], (strlen(wp_make_link_relative(site_url('/')).urldecode(get_option( 'wp_video_relative_url','wp-video' )))-strlen($_SERVER['REQUEST_URI'])));
	
	if (urldecode(get_option( 'wp_video_relative_url','wp-video' ))=='') $current_page='/'.$current_page;
	if ($current_page==$_SERVER['REQUEST_URI']) { echo '<script>window.location = "'.$_SERVER['REQUEST_URI'].'/";</script>';}
	if ($current_page != '/' )
	{
  if (substr($current_page,0,2) != '/?' and '/'.wp_make_link_relative(site_url('/'))!=$current_page)
  {
	  
	global $wpdb;
	$table_name = $wpdb->prefix . "wp_video";
	//----------------clear front page
	$clear_time= strtotime(urldecode(get_option( 'wp_clear_frontpage',date('Y-m-d H:i:s'))));
	if ($clear_time< time() and $clear_time>0)
	{
		$wpdb->update(
		$table_name, 
		array( 
          	'featured' => ''
		),
		array( 'featured' => '1')
	);
	}
	
	
	
	$rezultat= $wpdb->get_results( "SELECT id,category FROM ".$table_name.' WHERE video_url=\''.substr($current_page,1).'\'');  
    $_GET['id']=$rezultat[0]->id;
	if ($current_page=='/page-about') {$about='true';}
	if (!isset($_GET['category']) and !isset($_GET['pg']) and  $_GET['id']=='' and $about!='true') { echo '<script>window.location = "'.site_url().'/'.urldecode(get_option( 'wp_video_relative_url','wp-video' )).'/";</script>';}
	$rezultat_left_t=$wpdb->get_results( "SELECT video_url FROM ".$table_name.' WHERE '.(isset($rezultat[0]->category) ? 'category=\''.$rezultat[0]->category.'\' and'  :'').' id > \''.trim($_GET['id']).'\' and date < '.time().' ORDER BY id  limit 1;' );
	$rezultat_left=$rezultat_left_t[0]->video_url;
	$rezultat_right_t=$wpdb->get_results( "SELECT video_url FROM ".$table_name.' WHERE '.(isset($rezultat[0]->category) ? 'category=\''.$rezultat[0]->category.'\' and'  :'').' id < \''.trim($_GET['id']).'\' and date < '.time().' ORDER BY id DESC limit 1;');
	$rezultat_right=$rezultat_right_t[0]->video_url;
	
  }
	}
	
function Get_Banners($position,$rezultat_banners_individual,$rezultat_banners_category,$rezultat_banners_general)
{
	
unset($neededObject);	
$neededObject=array();
$neededObject = array_filter(
    $rezultat_banners_individual,
    function ($e) use (&$position)
	{
        return $e->position == $position;
    }
);

if (count($neededObject)>0)
{
	shuffle($neededObject);
	return $neededObject[0]->html_text;
}
else
{

unset($neededObject);
$neededObject=array();
$neededObject = array_filter(
    $rezultat_banners_category,
    function ($e) use (&$position)
	{

        return $e->position == $position;
    }
);
if (count($neededObject)>0)
{
	shuffle($neededObject);
	return $neededObject[0]->html_text;
}
else
{
unset($neededObject);	
$neededObject=array();
$neededObject = array_filter(
    $rezultat_banners_general,
    function ($e) use (&$position)
	{
        return $e->position == $position;
    }
);
if (count($neededObject)>0)
{
	shuffle($neededObject);
	return $neededObject[0]->html_text;
}	
}	
	
}
}		
	
function get_string_between($string, $start, $end){
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) return '';
    
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}	

global $wpdb;
	$table_name = $wpdb->prefix . "wp_video";
	
	
$total=$wpdb->get_var("SELECT COUNT(*) FROM ".$table_name." ".(isset($_GET['category']) ? ' WHERE category=\''.$_GET['category'].'\' and date < '.time() :' where date < '.time())." ".(isset($_GET['s']) ?"and title LIKE '%".$_GET['s']."%'":'').";");
if ($total==0) $total=1;
	$limit = get_option( 'wp_video_rows_at_home_page','3' )*3;
	$pages = ceil($total / $limit);
	// What page are we currently on?
    $page = min($pages, filter_input(INPUT_GET, 'pg', FILTER_VALIDATE_INT, array(
        'options' => array(
            'default'   => 1,
            'min_range' => 1,
        ),
    )));



	$rezultat= $wpdb->get_results( "SELECT id,keyword,video_url,title,meta_description,article_text,category,featured,date FROM ".$table_name.(isset($_GET['id']) ? ' WHERE id='.$_GET['id']:' '.(isset($_GET['category']) ? ' WHERE category=\''.$_GET['category'].'\' and date < '.time() :' where date < '.time()).' '.(isset($_GET['s']) ?"and title LIKE '%".$_GET['s']."%'":'').' ORDER BY featured DESC,id DESC LIMIT '.(get_option( 'wp_video_rows_at_home_page','3' )*3).' OFFSET '.(get_option( 'wp_video_rows_at_home_page','3' )*3*($page-1)) ) );  
	
    // Some information to display to the user
    $start = $offset + 1;
    $end = min(($offset + $limit), $total);

    // The "back" link
    $prevlink = ($page > 1) ? '<a href="'.site_url().'/'.urldecode(get_option( 'wp_video_relative_url','wp-video' )).'/'.(isset($_GET['category'])?'?category='.$_GET['category']:'').'" title="First page">&laquo;</a> <a href="'.site_url().'/'.urldecode(get_option( 'wp_video_relative_url','wp-video' )).'/'.(($page - 1)!=1?'?pg=' . ($page - 1):'').(isset($_GET['category'])?(($page - 1)!=1?'&':'?').'category='.$_GET['category']:''). '" title="Previous page">&lsaquo;</a>' : '<span class="disabled">&laquo;</span> <span class="disabled">&lsaquo;</span>';

    // The "forward" link
    $nextlink = ($page < $pages) ? '<a href="?pg=' . ($page + 1) .(isset($_GET['category'])?'&category='.$_GET['category']:''). '" title="Next page">&rsaquo;</a> <a href="?pg=' . $pages .(isset($_GET['category'])?'&category='.$_GET['category']:''). '" title="Last page">&raquo;</a>' : '<span class="disabled">&rsaquo;</span> <span class="disabled">&raquo;</span>';

	// Display the paging information
    
global $wpdb;
$table_name = $wpdb->prefix . "wp_video_banners";

$rezultat_banners_individual=array();
if (isset($_GET['id']))
{
$rezultat_banners_individual= $wpdb->get_results( "SELECT id,type,category,position,html_text FROM ".$table_name." WHERE type='individual' and category =".$_GET['id'] );
}
$rezultat_banners_category= $wpdb->get_results( "SELECT id,type,category,position,html_text FROM ".$table_name." WHERE type='global' and category ='".rawurlencode($rezultat[0]->category)."'" );
$rezultat_banners_general= $wpdb->get_results( "SELECT id,type,category,position,html_text FROM ".$table_name." WHERE type='global' and category = 'general'" );

$banner_html=array();
//Header banner
$position="Header banner";
$banner_html[$position] = Get_Banners($position,$rezultat_banners_individual,$rezultat_banners_category,$rezultat_banners_general);



//Above title banner
//-------------------------------------------------
$position="Above title banner";
$banner_html[$position] = Get_Banners($position,$rezultat_banners_individual,$rezultat_banners_category,$rezultat_banners_general);

//-------------------------------------------------

//Video Left Banner
//-------------------------------------------------
$position="Video Left Banner";
$banner_html[$position] = Get_Banners($position,$rezultat_banners_individual,$rezultat_banners_category,$rezultat_banners_general);

//-------------------------------------------------

//Video Right Banner
//-------------------------------------------------
$position="Video Right Banner";
$banner_html[$position] = Get_Banners($position,$rezultat_banners_individual,$rezultat_banners_category,$rezultat_banners_general);

//-------------------------------------------------

//Above title banner
//-------------------------------------------------
$position="Above title banner";
$banner_html[$position] = Get_Banners($position,$rezultat_banners_individual,$rezultat_banners_category,$rezultat_banners_general);

//-------------------------------------------------

//Inline article banner
//-------------------------------------------------
$position="Inline article banner";
$banner_html[$position] = Get_Banners($position,$rezultat_banners_individual,$rezultat_banners_category,$rezultat_banners_general);

//-------------------------------------------------

//Side article banner
//-------------------------------------------------
$position="Side article banner";
$banner_html[$position] = Get_Banners($position,$rezultat_banners_individual,$rezultat_banners_category,$rezultat_banners_general);

//-------------------------------------------------

//Below article banner
//-------------------------------------------------
$position="Below article banner";
$banner_html[$position] = Get_Banners($position,$rezultat_banners_individual,$rezultat_banners_category,$rezultat_banners_general);

//-------------------------------------------------

//Mobile banner
//-------------------------------------------------
$position="Mobile banner";
$banner_html[$position] = Get_Banners($position,$rezultat_banners_individual,$rezultat_banners_category,$rezultat_banners_general);

//-------------------------------------------------

	//echo $_SERVER['REQUEST_URI'].'<br>';
	//echo wp_make_link_relative(site_url('/'));
?>
<!DOCTYPE html>
<html lang="en-US">
<head>
<base href="<?php echo site_url().'/'.urldecode(get_option( 'wp_video_relative_url','wp-video' )).(urldecode(get_option( 'wp_video_relative_url','wp-video' ))!=''?'/':''); ?>" />	
  <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php
	if (isset($_GET['id']))	
{
foreach ($rezultat as $value) 
{

$iframe_url_s = get_string_between(html_entity_decode($value->article_text),'<iframe','</iframe>');		
$iframe_url=get_string_between($iframe_url_s,'://www.youtube.com/embed/','"');
$iframe_url=str_replace('://www.youtube.com/embed/','',$iframe_url);
$iframe_url=str_replace('"','',$iframe_url);
	echo '<meta property="twitter:title" content="'.html_entity_decode($rezultat[0]->title).'" />
<meta property="twitter:description" content="'.($rezultat[0]->meta_description!='' ? html_entity_decode($rezultat[0]->title):html_entity_decode($rezultat[0]->title)).'" />
<meta name="twitter:image" content="https://i.ytimg.com/vi/'.$iframe_url.'/hqdefault.jpg" />
';
	echo '<meta name="twitter:card" content="summary_large_image" />';
}}
	?>
	
	<meta http-equiv="Cache-control" content="public">
	<?php if (!isset($_GET['id'])) {echo '<meta name="description" content="'.(isset($_GET['category']) ? 'Category: '.urldecode($_GET['category']) : (isset($_GET['pg'])?urldecode(get_option( 'wp_home_meta_title','' )):urldecode(get_option( 'wp_home_meta_description','' )))).($about=='true' ? ' - about page':'').(isset($_GET['pg']) ?' - part '.$_GET['pg']:'').'">'; } else { if ($rezultat[0]->meta_description!='') echo '<meta name="description" content="'.$rezultat[0]->meta_description.'">';} ?>
	<link rel="image_src" href="https://i.ytimg.com/vi/<?php echo $iframe_url;?>/hqdefault.jpg" />
	<link rel="shortcut icon" href="<?php echo urldecode(get_option( 'wp_video_favicon',urlencode(site_url()).'/wp-content%2Fplugins%2Fwpvideo%2Ftheme%2Finclude%2Fvideoicon.png' )); ?>" type="image/x-icon">
<link href="<?php echo site_url()?>/?feed=video-feed<?php if (isset($_GET['category'])) echo '&category='.$_GET['category']; ?>" rel="alternate" type="application/rss+xml" title="Video rss" />	
	<title><?php echo (isset($_GET['id']) ? html_entity_decode($rezultat[0]->title) : (isset($_GET['category']) ? 'Category: '.urldecode($_GET['category']) : urldecode(get_option( 'wp_home_meta_title','' )).($about=='true'?' - about page':''))); if (isset($_GET['pg'])) echo " - part ".$_GET['pg']; 	?></title>

<!--<link rel="stylesheet" id="dashicons-css" href="<?php echo plugin_dir_url(__FILE__) ; ?>include/dashicon.css" type="text/css" media="all">-->
<link rel="stylesheet" id="thickbox-css" href="<?php echo plugin_dir_url(__FILE__) ; ?>include/thickbox.css" type="text/css" media="all">
<link rel="stylesheet" id="carousel-css" href="<?php echo plugin_dir_url(__FILE__) ; ?>include/style000.css" type="text/css" media="all">
<link rel="stylesheet" id="fancybox-css" href="<?php echo plugin_dir_url(__FILE__) ; ?>include/style001.css" type="text/css" media="all">
<link rel="stylesheet" id="videoelements-css-css" href="<?php echo plugin_dir_url(__FILE__) ; ?>include/style002.css" type="text/css" media="all">
<script type="text/javascript" src="<?php echo plugin_dir_url(__FILE__) ; ?>include/jquery00.js"></script>
<script type="text/javascript" src="<?php echo plugin_dir_url(__FILE__) ; ?>include/jquery-m.js"></script>
<script type="text/javascript" src="<?php echo plugin_dir_url(__FILE__) ; ?>include/jquery01.js"></script>
<script type="text/javascript" src="<?php echo plugin_dir_url(__FILE__) ; ?>include/jquery02.js"></script>
<script type="text/javascript" src="<?php echo plugin_dir_url(__FILE__) ; ?>include/jquery03.js"></script>
<?php
echo stripslashes(urldecode(get_option( 'wp_header_script','%3Cscript%20type%3D%22text%2Fjavascript%22%3E%0D%0A%3C%2Fscript%3E' )));
?>
</head>
<body>
<div itemprop="video" itemscope itemtype="https://schema.org/VideoObject">
<div >
	<div id="header" style="background: <?php echo urldecode(get_option( 'wp_color_picker_header','%231f43d3' )); ?>  bottom left repeat-x;">
		<div id="header-inside">
			<div id="header-left">
        	<a href="<?php echo urldecode(get_option( 'wp_link_header',urlencode(site_url()).'/index.php%2Fwp-video%2F' )); ?>" title="Home"><img class="fade" src="<?php echo urldecode(get_option( 'wp_logo_header',urlencode(site_url()).'/wp-content%2Fplugins%2Fwpvideo%2Ftheme%2Finclude%2Fgallery_video.png' )); ?>" alt="<?php echo urldecode(get_option( 'wp_alt_header','')); ?>"></a>
			</div>
			<div id="header-right">
			<?php
			
			echo urldecode($banner_html["Header banner"]).'
';
?>
			
		 			</div>
		</div> <!-- header-inside -->
	</div> <!-- header -->

	<div id="navigation" style="background: <?php echo urldecode(get_option( 'wp_color_picker_menu','%23ed9136' )); ?>  top left repeat-x;">
		<div id="navigation-inside">
						<ul class="menu">
				<li class><a href="." title="Home">Home</a></li>

<li class="cat-item cat-item-3"><a onclick="return false;" href="#">Category</a>
<ul class="children" style="background: <?php echo urldecode(get_option( 'wp_color_picker_menu','%232A2A2A' )); ?> repeat;">
<?php
global $wpdb;
$table_name = $wpdb->prefix . "wp_video";
$rezultat_category= $wpdb->get_results( "SELECT DISTINCT(category) FROM ".$table_name." ORDER BY category DESC" );  
foreach ($rezultat_category as $value_category) 
{
echo '<li class="cat-item cat-item-7"><a href="?category='.$value_category->category.'">'.$value_category->category.'</a></li>';
}
?>


	
</ul>
</li>

<?php
$wp_video_menu_text=explode("\r\n",htmlentities(stripslashes(urldecode(get_option( 'wp_video_menu_text','About||page-about' ) ))));
foreach($wp_video_menu_text as $menu_item)
{ $menu_item_array=explode("||",$menu_item);
	echo '<li class="page_item page-item-184"><a href="'.$menu_item_array[1].'">'.$menu_item_array[0].'</a></li>';
}
?>


			</ul>
					</div> <!-- navigation-inside -->
	</div>  <!-- navigation -->
	
	<!-- include the featured content carousel for the home page -->
<?php 
if (isset($_GET['id']))	
{
foreach ($rezultat as $value) 
{

$iframe_url_s = get_string_between(html_entity_decode($value->article_text),'<iframe','</iframe>');		
$iframe_url=get_string_between($iframe_url_s,'://www.youtube.com/embed/','"');
$iframe_url=str_replace('://www.youtube.com/embed/','',$iframe_url);
$iframe_url=str_replace('"','',$iframe_url);
$iframe_url_s_origin=$iframe_url_s;
$iframe_url_s=str_replace($iframe_url,$iframe_url.'?showinfo=0&autohide=1&rel=0'.(get_option( 'wp_video_auto_play','0' )=="1"?'&autoplay=1':''),$iframe_url_s);
}	
?>	
<style>
ul.share-buttons{
  list-style: none;
  padding: 0 !important;
  margin: 0 !important;
  max-width: 960px !important;
 
}

ul.share-buttons li{
  display: inline;
  margin: 0 !important;
}


</style>
<div id="video">
<div id="video-inside">
<div class="videoparts" id="v1">
<?php 
echo urldecode($banner_html["Video Left Banner"]).'
';
echo "</div><div class=\"videoparts\" id=\"v3\">";
echo urldecode($banner_html["Video Right Banner"]).'
';
echo "</div><div class=\"videoparts\" id=\"v2\">
";
?>
<meta itemprop="thumbnailURL" content="https://i.ytimg.com/vi/<?php echo $iframe_url;?>/hqdefault.jpg" />
<meta itemprop="embedURL" content="https://www.youtube.com/embed/<?php echo $iframe_url;?>?showinfo=0&rel=0&autohide=1" />
<meta itemprop="uploadDate" content="<?php echo date(DATE_RFC2822, $value->date);?>" />
<?php
echo $iframe_url_s.'</iframe>';


?>
</div>

<?php
$title_for_share =$value->title;
$url_m=site_url().'/'.urldecode(get_option( 'wp_video_relative_url','wp-video' )).$current_page;
echo '
<ul class="share-buttons">

'.($rezultat_left==''?'':'<li><a title="Previous video: '.$rezultat_left.'" href="'.$rezultat_left.'"><img src="'.plugin_dir_url(__FILE__).'include/left.png"></a></li>').' 
<li><a href="https://www.facebook.com/sharer/sharer.php?u='.$url_m.'&t='.urlencode($title_for_share).'" title="Share on Facebook" target="_blank"><img src="'.plugin_dir_url(__FILE__).'img/sharer/Facebook.png"></a></li>
<li><a onclick="javascript:window.open(\'https://twitter.com/share?text='.urlencode($title_for_share).'&url='.urlencode($url_m).'\', \'twitwin\', \'left=20,top=20,width=500,height=500,toolbar=1,resizable=1\');"  href="javascript:void(0)" title="Tweet"><img src="'.plugin_dir_url(__FILE__).'img/sharer/Twitter.png"></a></li>
<li><a href="https://plus.google.com/share?url='.$url_m.'" target="_blank" title="Share on Google+"><img src="'.plugin_dir_url(__FILE__).'img/sharer/Google.png"></a></li>
<li><a href="https://pinterest.com/pin/create/button/?url='.$url_m.'&media=https://i.ytimg.com/vi/'.$iframe_url.'/hqdefault.jpg&description='.urlencode($title_for_share).'" target="_blank" title="Pin it"><img src="'.plugin_dir_url(__FILE__).'img/sharer/Pinterest.png"></a></li>
<li><a href="https://www.linkedin.com/shareArticle?mini=true&url='.$url_m.'&title='.urlencode($title_for_share).'&summary=&source=" target="_blank" title="Share on LinkedIn"><img src="'.plugin_dir_url(__FILE__).'img/sharer/LinkedIn.png"></a></li>
'.($rezultat_right==''?'':'<li><a title="Next video: '.$rezultat_right.'" href="'.$rezultat_right.'"><img src="'.plugin_dir_url(__FILE__).'include/right.png"></a></li>').' 

</ul>
';

?>

</div>
</div>
<?php
}
else
{
?>	
	
	<div id="carousel" style="background: <?php echo urldecode(get_option( 'wp_color_picker_video_back','%23333333' )); ?> top left repeat-x;">
	<div id="carousel-inside">
		<div class="infinite">
			<div class="carousel">
				<ul> 
					    
<?php
global $wpdb;
$table_name = $wpdb->prefix . "wp_video";
$rezultat_f= $wpdb->get_results( "SELECT id,keyword,video_url,title,article_text,category,featured,date FROM ".$table_name.' where date < '.time().' ORDER BY id DESC' ) ;  
shuffle($rezultat_f);
$i=1;
foreach ($rezultat_f as $value_f) 
{
if 	($i==9) break;
$i++;
$iframe_url_s = get_string_between(html_entity_decode($value_f->article_text),'<iframe','</iframe>');		
$iframe_url=get_string_between($iframe_url_s,'://www.youtube.com/embed/','"');
$iframe_url=str_replace('://www.youtube.com/embed/','',$iframe_url);
$iframe_url=str_replace('"','',$iframe_url);
$img='https://i.ytimg.com/vi/'.$iframe_url.'/hqdefault.jpg?custom=true&w=230&h=170';
$iframe_url_s=str_replace($iframe_url,$iframe_url.'?showinfo=0&rel=0&autohide=1',$iframe_url_s);

?>
                                        
<li>
<a class="post-frame-carousel-video <?php echo $i; ?> inline" href="<?php echo site_url();?>/wp-admin/admin-ajax.php?action=youtube_iframe_out&url=<?php echo $iframe_url;?>" title="<?php echo $value_f->title; ?>"></a>
<img width="230" height="170" src="<?php echo $img ;?>" class="attachment-featured size-featured wp-post-image" alt="Car" srcset="" sizes="(max-width: 230px) 100vw, 230px">
<?php if (get_option( 'wp_video_show_title_of_featured_videos','0' )=="1") echo '<h2 class="carousel-title"><a href="'.$value_f->video_url.'" title="'.$value_f->title.'">'.(strlen($value_f->title)>30 ? substr(html_entity_decode($value_f->title),0, 30).'...':$value_f->title).'</a></h2>';?>

</li>
<?php
}
?>					

						                				</ul>        
			</div> <!-- carousel -->
		</div> <!-- infinite -->
	</div> <!-- carousel-inside -->
</div> <!-- carousel -->
<?php
}
?>
	<div id="content">
		<div id="content-inside">
		
	<?php	
// one article		
if (isset($_GET['id']))	
{
echo '<div class="above_title" style=" max-width: 100%;  height: auto;">'.urldecode($banner_html["Above title banner"]).'</div>
';
}
?>		
		
			<div id="breadcrumbs">
				<p>You are here: <strong><?php if (isset($_GET['s'])){echo "Search '".$_GET['s']."'";} else {echo ($about=='true' ? 'About':'Home');}		
if (isset($_GET['id']))	
{
	echo ' - > '.html_entity_decode($value->title);
}

if (isset($_GET['category']))	
{
	echo ' - > '.$_GET['category'];
}
?></strong></p>				<script type="text/javascript">
	function doClear(theText) {
		if (theText.value == theText.defaultValue) {
			theText.value = ""
		}
	}
</script>
<?php if (get_option( 'wp_video_post_show_search_box','1' )=="1")
{
?>	
<div id="search">
	<form method="get" id="search-form" action=".">
		<input type="text" name="s" id="s" value="Find Something" onfocus="doClear(this)">
		<input type="submit" id="search-submit" value="Search">
	</form>
</div>
<?php
}
?>		
</div>
		
<div id="main">
<?php	
// one article		
if (isset($_GET['id']))	
{
?>	
<div id="post-121" class="single post-121 post type-post status-publish format-standard has-post-thumbnail hentry category-category category-sample-category category-sample-child-category category-sample-videos category-sub-category category-vimeo-videos">
<h1 itemprop="name" style="padding-top: 20px;padding-bottom: 30px; line-height: 30px;"><?php
echo html_entity_decode($value->title);

?></h1>
<div class="entry">
<?php
echo '<div class="above_title" style="max-width: 100%;  height: auto;">'.urldecode($banner_html["Inline article banner"]).'</div>
';
echo '<div style="clear: both;"><div class="side_title" style="clear: left;float:right; max-width: 100%;  height: auto;">'.urldecode($banner_html["Side article banner"]).'</div>
';
$iframe_text=str_replace($iframe_url_s_origin.'</iframe>','',html_entity_decode($value->article_text));
echo ''.$iframe_text.'';
echo '</div><div class="above_title" style="max-width: 100%;  height: auto;">'.urldecode($banner_html["Below article banner"]).'</div>
<div class="mobile_banner" style="max-width: 100%;  height: auto;">'.urldecode($banner_html["Mobile banner"]).'</div>
<span style="display: none;" itemprop="description">'.substr(strip_tags($iframe_text),0,strposX($iframe_text,' ',50)).'...</span>
';
?>

</div>
</div>

<?php

}
else
{
if ($about=='true')
{
	echo stripslashes(urldecode(get_option( 'wp_video_about_text','This is about page !' ) ));
}	
else	
{	
$i=1;
foreach ($rezultat as $value) 
{
$i++;
$iframe_url_s = get_string_between(html_entity_decode($value->article_text),'<iframe','</iframe>');		
$iframe_url=get_string_between($iframe_url_s,'://www.youtube.com/embed/','"');
$iframe_url=str_replace('://www.youtube.com/embed/','',$iframe_url);
$iframe_url=str_replace('"','',$iframe_url);
$img='https://i.ytimg.com/vi/'.$iframe_url.'/hqdefault.jpg';
$iframe_url_s=str_replace($iframe_url,$iframe_url.'?showinfo=0&rel=0&autohide=1',$iframe_url_s);
?> 

<div id="post-<?php echo $i; ?>" class="multiple post-<?php echo $i; ?> post type-post status-publish format-standard has-post-thumbnail hentry category-category category-sample-category category-sample-child-category category-sample-videos category-sub-category category-vimeo-videos">			
<div class="post-image">
<a class="post-frame-video <?php echo $i; ?> inline" href="<?php echo site_url();?>/wp-admin/admin-ajax.php?action=youtube_iframe_out&url=<?php echo $iframe_url;?>" title="<?php echo $value->title; ?>"></a>
												
						<img width="230" height="170" src="<?php echo $img ;?>" class="attachment-post-thumbnail size-post-thumbnail wp-post-image" alt="Car" srcset="" sizes="(max-width: 180px) 100vw, 180px">					</div>
					
					
				
					<h2><a href="<?php echo $value->video_url ;?>" rel="bookmark" title="<?php echo $value->title; ?>"><?php echo (strlen($value->title)>55 ? substr(html_entity_decode($value->title),0, 55).'...':htmlspecialchars_decode($value->title))?></a></h2>
			<!--
							<li>Posted on <?php echo date("F j, Y",$value->date); ?></li>
							-->
				</div> <!-- post -->
				
	<?php
}
?>								
				
								
								
	<?php
if ($pages>1) echo '<br><div style="float: left;" id="paging"><br><p style="font-size: 18px;">', $prevlink, ' Page ', $page, ' of ', $pages, ' pages ', $nextlink, ' </p></div>';
}	
}

?>								
			 
			</div> <!-- main -->
				
			<div id="sidebar">
			<?php
			if (get_option( 'wp_video_post_general_sidebar','%3Ch2%20align%3D%22center%22%3EThis%20Is%20The%20Sidebar%3C%2Fh2%3E%3Cbr%3E%0A%3Cimg%20src%3D%22'.urlencode(plugin_dir_url(__FILE__)).'img%2F220600.png%22%3E%09%09' )!='')	
{
echo stripslashes(urldecode(get_option( 'wp_video_post_general_sidebar','%3Ch2%20align%3D%22center%22%3EThis%20Is%20The%20Sidebar%3C%2Fh2%3E%3Cbr%3E%0A%3Cimg%20src%3D%22'.urlencode(plugin_dir_url(__FILE__)).'img%2F220600.png%22%3E%09%09' ) ));
}
else
{
echo urldecode($banner_html["Side article banner"]).'
';
}
			?>
		</div> <!-- sidebar -->		</div> <!-- content-inside -->
	</div> <!-- content -->

	<div id="footer">
		<div id="footer-inside">
						
							<p><?php echo urldecode(get_option( 'wp_site_desing_text','Site Design by: wpvideosites' )); ?></p>
						<!-- 50 queries. 0.334 seconds. -->
		</div> <!-- footer-inside -->
	</div> <!-- footer -->
	
<!--	<script type="text/javascript">
/* <![CDATA[ */
//var thickboxL10n = {"next":"Next >","prev":"< Prev","image":"Image","of":"of","close":"Close","noiframes":"This feature requires inline frames. You have iframes disabled or your browser does not support them.","loadingAnimation":"loadingAnimation.gif"};
/* ]]> */
</script>

<script type="text/javascript" src="<?php echo plugin_dir_url(__FILE__) ; ?>include/thickbox.js"></script>
<script type="text/javascript" src="<?php echo plugin_dir_url(__FILE__) ; ?>include/wp-embed.js"></script>
-->	
		<!--[if IE 6]>
	<script type="text/javascript"> 
		/*Load jQuery if not already loaded*/ if(typeof jQuery == 'undefined'){ document.write("<script type=\"text/javascript\"   src=\"http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js\"></"+"script>"); var __noconflict = true; } 
		var IE6UPDATE_OPTIONS = {
			icons_path: "http://static.ie6update.com/hosted/ie6update/images/"
		}
	</script>
	<script type="text/javascript" src="http://static.ie6update.com/hosted/ie6update/ie6update.js"></script>
	<![endif]-->
	</div>
	</div>
	<?php
echo stripslashes(urldecode(get_option( 'wp_footer_script','%3Cscript%20type%3D%22text%2Fjavascript%22%3E%0D%0A%3C%2Fscript%3E' )));
?>
	</body>
</html>

