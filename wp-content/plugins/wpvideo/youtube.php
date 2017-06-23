<?php

//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

if (!function_exists('strposX'))
{	
function strposX($haystack, $needle, $number) 
{
    // decode utf8 because of this behaviour: https://bugs.php.net/bug.php?id=37391
    preg_match_all("/$needle/", utf8_decode($haystack), $matches, PREG_OFFSET_CAPTURE);
    return $matches[0][$number-1][1];
}
}
function str_replace_nth($search, $replace, $subject, $nth)
{
    $found = preg_match_all('/'.preg_quote($search).'/', $subject, $matches, PREG_OFFSET_CAPTURE);
    if (false !== $found && $found > $nth) {
        return substr_replace($subject, $replace, $matches[0][$nth][1], strlen($search));
    }
    return $subject;
}
function replace_unicode_escape_sequence($match) {
    return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UCS-2BE');
}
function unicode_decode($str) {
    return preg_replace_callback('/\\\\u([0-9a-f]{4})/i', 'replace_unicode_escape_sequence', $str);
}

function file_get_contents_alternative($url,$a,$b) {
	
	
	$ch = curl_init();
	$timeout = 20;
	//curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
	$data = curl_exec($ch);
	curl_close($ch);
	return $data;
}
if (isset($_GET['description']))
{
	if (!function_exists('str_get_html')) 
{
require_once('simple_html_dom.php');
}
	$opts = array('http' => array('header' => 'Accept-Charset: UTF-8, *;q=0'));
$context = stream_context_create($opts);
	$homepage = '<pre>'.mb_convert_encoding(file_get_contents_alternative($_GET['description'],false, $context), 'HTML-ENTITIES', "UTF-8").'</pre>';
	$html_I= str_get_html(htmlspecialchars_decode($homepage));
	$html_II=$html_I->find('#eow-description',0);
	if (get_option( 'wp_video_show_end_text','1' )=="1") $sub_add_text = '<br><br><p>'.urldecode(get_option( 'wp_video_youtube_end_text','See%20more%20here%3A' )).' '.$_GET['description'].'</p>';

	if (get_option( 'wp_video_yt_description_strip_links','0' )=="1")
    {
		echo preg_replace('#<a.*?>.*?</a>#i', '', $html_II->innertext.$sub_add_text);
	}		
	else
	{
		echo $html_II->innertext.$sub_add_text;
	}
	
	
	exit();
}	

if (isset($_GET['subtitle']))
{
	if (!function_exists('str_get_html')) 
{
require_once('simple_html_dom.php');
}
for ($retry=1;$retry<3;$retry++)
{
	$opts = array('http' => array('header' => 'Accept-Charset: UTF-8, *;q=0'));
$context = stream_context_create($opts);
	$homepage = '<pre>'.mb_convert_encoding(file_get_contents_alternative($_GET['subtitle'],false, $context), 'HTML-ENTITIES', "UTF-8").'</pre>';
	$strtrt_I=strrpos($homepage,'u=https%3A%2F%2Fwww.youtube.com',0-strpos($homepage,'timedtext'));
	$strtrt_II=strpos($homepage,'","',$strtrt_I);
    
$finds_out=rawurldecode(unicode_decode(substr($homepage, $strtrt_I+2,$strtrt_II-$strtrt_I-2))).'&tlang='.get_option( 'wp_video_subtitle_language','en' );
$finds = explode('v=', rawurldecode(unicode_decode(substr($homepage, $strtrt_I+2,$strtrt_II-$strtrt_I-2))));
if (count($finds) == 3) {
  $finds_out=$finds[0].'v='.$finds[1].$finds[2].'&tlang='.get_option( 'wp_video_subtitle_language','en' );
  
 
}
if ($retry==1)
{
$youtube_p=explode('v=',$_GET['subtitle']);
 $youtube_p_I=explode('&',$youtube_p[1]);
  $finds_out='https://www.youtube.com/api/timedtext?lang='.get_option( 'wp_video_subtitle_language','en' ).'&v='.$youtube_p_I[0];
}
	$stringa= mb_convert_encoding(file_get_contents_alternative($finds_out,false, $context), 'HTML-ENTITIES', "UTF-8");
	//echo $stringa;
	$stringa=str_replace('</text><text','</text>&nbsp;<text',$stringa);
	$html_I= str_get_html(htmlspecialchars_decode($stringa));
	$sublitle_out = $html_I->plaintext;
	
	$sublitle_out_array=explode(" ",$sublitle_out);
$sub_add_text="";
 if (trim(get_option( 'wp_video_youtube_max_words','0' ))>0 and count($sublitle_out_array) > trim(get_option( 'wp_video_youtube_max_words','0' )))
 {
$sublitle_out=substr($sublitle_out,0,strposX($sublitle_out," ",trim(get_option( 'wp_video_youtube_max_words','0' ))));	 
$sublitle_out_array=array_slice($sublitle_out_array,0,trim(get_option( 'wp_video_youtube_max_words','0' )));
$sub_add_text = '...';
 }
	for ($i = 65; $i < count($sublitle_out_array); $i=$i+65) 
	{
	$sublitle_out = str_replace_nth(" ","<br><br>",$sublitle_out,$i);
    }
	if (trim($sublitle_out)!='') {if (get_option( 'wp_video_show_end_text','1' )=="1") $sub_add_text = $sub_add_text.'<br><br><p>'.urldecode(get_option( 'wp_video_youtube_end_text','See%20more%20here%3A' )).' '.$_GET['subtitle'].'</p>';}
$sublitle_out=str_replace('<?xml version="1.0" encoding="utf-8" ?>','',$sublitle_out);
	echo $sublitle_out.$sub_add_text;
	if (trim($sublitle_out)!='') break;
	//echo $html_I->find('text');
//	$test_dup="";
//	foreach($html_I->find('text') as $row_I) 
//{
	//if ($test_dup!=$row_I->plaintext) echo $row_I->plaintext.'<br>';
//	$test_dup=$row_I->plaintext;
//}
}

	//echo $homepage;
	exit();
}




if (isset($_GET['search']))
{

if (!function_exists('str_get_html')) 
{
require_once('simple_html_dom.php');
}

if ($_GET['type_of_search']=='1') echo '<br><br>
<input onclick="add_links_stack();return false;" class="button" type="button" value="ADD "><br><br>
<input onclick="var delete_check = document.getElementsByName(\'box[]\');for (i = 0; i < delete_check.length ; i++) {delete_check[i].checked=true;}" class="button" type="button" value="Sellect all"><br><br>
';
echo '<table style="border: 1px solid #e3e3e0;" >
';
//if (isset($_POST['keyword'])) {$correctString = str_replace(" ","+",$_POST['keyword']);} else {$correctString = str_replace(" ","+",$_GET['search_query']);};
$correctString=str_replace(" ","+",$_GET['search']);
$opts = array('http' => array('header' => 'Accept-Charset: UTF-8, *;q=0'));
$context = stream_context_create($opts);
if ((strpos($_GET['youtube_url'],'/user/')) or (strpos($_GET['youtube_url'],'/channel/')))
{
	$filename=rtrim($_GET['youtube_url'], '/') . '/'.($_GET['search']!='' ?'search?query='.$correctString:'');
}
else if (strpos($_GET['youtube_url'],'playlist?list='))
{
	$filename=$_GET['youtube_url'];
}
else

{	
$filename='https://www.youtube.com/results?search_query='.$correctString.(isset($_GET['page'])?'&page='.$_GET['page']:'');
}
$homepage = '<pre>'.mb_convert_encoding(file_get_contents_alternative($filename.($_GET['captions']=='true' ? '&sp=EgIoAQ%253D%253D':'' ),false, $context), 'HTML-ENTITIES', "UTF-8").'</pre>';
$html_I= str_get_html(htmlspecialchars_decode($homepage));
//echo $homepage;

if (strpos($_GET['youtube_url'],'playlist?list=') )
{
	$html_II=$html_I->find('#pl-video-table');
	foreach($html_I->find('tr') as $row_I) 
{
	$link_e= $row_I->getAttribute('data-video-id');
	$link="/watch?v=".$link_e;
	$selector_table='<input onclick="YouTubeInsertVideo(\''.$link_e.'\');return false;" class="button" type="button" value="Add">';
if ($_GET['type_of_search']=='1') $selector_table='<input data-title="'.$row_I->getAttribute('data-title').'" name="box[]" type="checkbox" value="https://www.youtube.com/embed/'.$link_e.'">';
if (!(strpos($link,'&list=')!== FALSE)) echo '<tr ><td>'.$selector_table.'</td><td><a target="_blank" href="https://www.youtube.com'.$link.'"><img width="196" height="110" src="//i.ytimg.com/vi/'.$link_e.'/mqdefault.jpg"></td><td><a target="_blank" href="https://www.youtube.com'.$link.'">'.$row_I->getAttribute('data-title').'</a></td></tr>';


}
	
}
else
{
foreach($html_I->find('.yt-lockup-dismissable') as $row_I) 
{
$link=$row_I->find('a',0)->href;
$link_e=str_replace("/watch?v=","",$link);

//ddati checkbox
//<input name="box[]" type="checkbox" value="http://www.youtube.com'.$link.'">
//javascript za ubacivanje
//<script language="JavaScript">
//function toggle(source) {
//  checkboxes = document.getElementsByName(\'box[]\');
//  for(var i=0, n=checkboxes.length;i<n;i++) {
//    checkboxes[i].checked = source.checked;
 // }
//}
//</script>
$selector_table='<input onclick="YouTubeInsertVideo(\''.$link_e.'\');return false;" class="button" type="button" value="Add">';
if ($_GET['type_of_search']=='1') $selector_table='<input data-title="'.$row_I->find('.yt-lockup-content',0)->find('a',0)->plaintext.'" name="box[]" type="checkbox" value="https://www.youtube.com/embed/'.$link_e.'">';
if (!(strpos($link,'&list=')!== FALSE)) echo '<tr ><td>'.$selector_table.'</td><td><a target="_blank" href="https://www.youtube.com'.$link.'"><img width="196"  src="https://i.ytimg.com/vi/'.$link_e.'/hqdefault.jpg?custom=true&w=246&h=138"></td><td><a target="_blank" href="https://www.youtube.com'.$link.'">'.$row_I->find('.yt-lockup-content',0)->find('a',0)->plaintext.'</a></td></tr>';
}
}
//-----------------------------
if ($_GET['youtube_url']=='')
{
$opts = array('http' => array('header' => 'Accept-Charset: UTF-8, *;q=0'));
$context = stream_context_create($opts);
$filename='https://www.youtube.com/results?search_query='.$correctString.'&page=2';
$homepage = '<pre>'.mb_convert_encoding(file_get_contents_alternative($filename.($_GET['captions']=='true' ? '&sp=EgIoAQ%253D%253D':'' ),false, $context), 'HTML-ENTITIES', "UTF-8").'</pre>';
$html_I= str_get_html(htmlspecialchars_decode($homepage));
//echo $homepage;
foreach($html_I->find('.yt-lockup-dismissable') as $row_I) 
{
$link=$row_I->find('a',0)->href;
$link_e=str_replace("/watch?v=","",$link);
$selector_table='<input onclick="YouTubeInsertVideo(\''.$link_e.'\');return false;" class="button" type="button" value="Add">';
if ($_GET['type_of_search']=='1') $selector_table='<input data-title="'.$row_I->find('.yt-lockup-content',0)->find('a',0)->plaintext.'" name="box[]" type="checkbox" value="https://www.youtube.com/embed/'.$link_e.'">';
if (!(strpos($link,'&list=')!== FALSE)) echo '<tr ><td>'.$selector_table.'</td><td><a target="_blank" href="http'.(is_ssl()? 's':'').'://www.youtube.com'.$link.'"><img width="196" src="https://i.ytimg.com/vi/'.$link_e.'/hqdefault.jpg?custom=true&w=246&h=138"></td><td><a target="_blank" href="http'.(is_ssl()? 's':'').'://www.youtube.com'.$link.'">'.$row_I->find('.yt-lockup-content',0)->find('a',0)->plaintext.'</a></td></tr>';

}
}
echo '</table>';
if ($_GET['type_of_search']=='1') echo '<br><br>
<input onclick="add_links_stack();return false;" class="button" type="button" value="ADD "><br><br>
';
}
?>