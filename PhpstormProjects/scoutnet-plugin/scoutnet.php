<?php

/*
Plugin Name: Scoutnet - we connect scouts
Plugin URI: http://www.scoutnet.be/
Description: Scoutnet API. Na activatie ga naar Settings/Instellingen -> Scoutnet API
Author: Scoutnet
Version: 0.1
Author URI: http://www.scoutnet.be
*/


session_start();

// calpress-event-calendar TO CHECK

// http://tgmpluginactivation.com/ -> om een plugin te installeren en activeren bij installatie van een theme

// http://wordpress.stackexchange.com/questions/14116/custom-nav-menu-is-created-with-default-pages-but-not-hooked-to-themes-custom

// Multi site: set default theme in wp-config.php WP_DEFAULT_THEME

// header voor de chiro http://www.chiropripo.be/contact.html


// generate menu
// http://wordpress.stackexchange.com/questions/12204/multisite-setting-theme-and-options-when-a-new-blog-is-created/39676#39676

// TODO javascript for googlemap http://code.tutsplus.com/articles/how-to-include-javascript-and-css-in-your-wordpress-themes-and-plugins--wp-24321

// Worst plugin ever

// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}

define('SN_API_VERSION', '1.0');
define('SN_API_PLUGIN_URL', plugin_dir_url( __FILE__ ));
define('SN_API_THEME_URL', get_template_directory_uri( __FILE__ ));

require_once dirname( __FILE__ ) . '/myfunctions.php';


function sn_common_scripts() {

wp_register_script('sn_public.js', SN_API_PLUGIN_URL . 'js/sn_public.js');
wp_enqueue_script('sn_public.js');

}

function sn_hello_scoutnet($content) {


//$content_by_user = $content;

//echo get_locale(); 

if (is_page('onze-groep')) {


//wp_enqueue_script('google-maps', 'https://maps.googleapis.com/maps/api/js?key=AIzaSyCwvKXGLUemghNxHVuYoiH8wEkoFfbVSgs&amp;sensor=false');
wp_register_script('map_public.js', SN_API_PLUGIN_URL . 'js/map_public.js' );
wp_enqueue_script('map_public.js');


	$call = sn_getGroup();

	 
	if ($call['decoded']['head']['status']==1){
		$group = $call['decoded']['body']['data'];

		$tmp = explode(':',$group['latlng']);
		$group['lat']=$tmp[0];
		$group['lng']=$tmp[1];
		unset($tmp);


$content = "
	<script type=\"text/javascript\">
		var templateDir = \"".SN_API_PLUGIN_URL."\";
	</script>
	<div class=\"leidingitem\">
	<div class=\"vereniging-titel\">
		<div class=\"left\">
			<h1>{$group['groupname']}</h1>
			<p class=\"ondertitel\">{$group['groupname2']}<br />{$group['groupID']} [{$group['section']}]</p>
";

if ($group['hasaddress']=='y'){
$content .= "<p>{$group['street']}<br />{$group['postcode']} {$group['city']}</p>";
}

if ($group['tel']!=''){
$content .= "<p>{$group['tel']}</p>";
}

if ($group['mail']!=''){
$content .= "<p>".sn_scramble($group['mail'])."</p>";
}

//TODO is de pagina "leiding" wel geactiveerd ? zoniet, geen link voorzien

if (count($group['contact']>0)){
$groepsleiding_str = '';
$content .= "<p><b>Groepsleiding:</b> ";
foreach ($group['contact'] as $key => $value){
$groepsleiding_str .= "<a href=\"../leiding/{$value['slug']}/\">{$value['name']}</a>, ";
}

$groepsleiding_str = substr($groepsleiding_str,0,-2);

$content .= "$groepsleiding_str</p>";
}


if ($group['extra'][1]['zee']=='y'){
$content .= "<p><img alt=\"zeescouts\" width=\"60\" height=\"80\" src=\"".SN_API_THEME_URL."/images/zeescouts.jpg\" title=\"zeescouts werking\" class=\"size-full alignleft\" /></p>";
}

if ($group['extra'][2]['akabe']=='y'){
$content .= "<p><img alt=\"akabe\" width=\"60\" height=\"80\" src=\"".SN_API_THEME_URL."/images/akabe.png\" title=\"akabe werking\" class=\"size-full alignleft\" /></p>";
}

if ($group['extra'][8]['das']!=''){
$das = json_decode($group['extra'][8]['das']);
$das_url = $das->{'url'};
$content .= "<p><img alt=\"groepsdas\" width=\"196\" height=\"88\" src=\"$das_url\" title=\"groepsdas\" class=\"size-full alignleft\" /></p>";
}else{
$content .= "<p><img alt=\"groepsdas\" width=\"196\" height=\"88\" src=\"http://images.scoutnet.be/dassen/blanco.png\" title=\"groepsdas - kleuren en formaat nog niet toegekend\" class=\"size-full alignleft\" /></p>";
}


$content .= "
		</div>
		<form>
		<input type=\"hidden\" id=\"lat\" value=\"{$group['lat']}\" />
		<input type=\"hidden\" id=\"lng\" value=\"{$group['lng']}\" />
		</form>
		
		<div class=\"map\" id=\"map\" style=\"background-image:url(http://maps.google.com/maps/api/staticmap?center={$group['lat']},{$group['lng']}&amp;zoom=13&amp;markers=icon:".SN_API_PLUGIN_URL."/img/m_red.png|{$group['lat']},{$group['lng']}&amp;size=400x300&amp;sensor=false);\">
		<br/><br/><br/><div style=\"text-align:center; padding: 20px; cursor:pointer; cursor:hand; filter: alpha(opacity=55); -moz-opacity: 0.55; opacity: 0.55; background-color:#eeeeee\" onclick=\"activateMap();\">Klik om de google map te activeren</div>
		</div>
		
		<!--<div class=\"map\" id=\"map\">
			Scoutnet Map
		</div>//-->
		</div>
";
if ($group['extra'][4]['promo']!=''){
$content .= "
		<h2>Wat ?</h2>
		<p>{$group['extra'][4]['promo']}</p>
";
}
if ($group['extra'][5]['waar']!=''){
$content .= "
		<h2>Waar ?</h2>
		<p>{$group['extra'][5]['waar']}</p>
";
}
if ($group['extra'][6]['wanneer']!=''){
$content .= "
		<h2>Wanneer ?</h2>
		<p>{$group['extra'][6]['wanneer']}</p>
";
}
if ($group['extra'][7]['lidgeld']!=''){
$content .= "
		<h2>Lidgeld ?</h2>
		<p>{$group['extra'][7]['lidgeld']}</p>
";
}

if ($group['extra'][9]['groepsfoto']!=''){
	$content .= "<p><img alt=\"groepsfoto\" width=\"700\" height=\"350\" src=\"{$group['extra'][9]['groepsfoto']}\" title=\"groepsfoto\" class=\"aligncenter\" /></p>";
}


$content .= "
		</div><br /><br />
";


	}else{
		$content = "<div class=\"error\">{$call['decoded']['head']['error_message']}<br /><br />Is de secret key correct? Contacteer info@scoutnet.be</div>";
	}
	
	
   
}else if (is_page('takken')) {
	$call = sn_getSections();
	if ($call['decoded']['head']['status']==1){
		$content = "";
		$sections = $call['decoded']['body']['data'];
			$content .= "<div id=\"sn_takken_info\">\n";
			foreach ($sections as $key => $section){
				//$content .= "<div class=\"section_logo_{$key}\"></div><div class=\"section_content\"><a href=\"{$section['code']}/\"><b>{$section['name']}</b></a><br /><br />{$section['des']}</div><br /><br />";
				$content .= "
		<div class=\"takkenitem\">
        <h2>{$section['name']}</h2>
        	<p><a href=\"{$section['code']}/\"><img alt=\"{$section['code']}\" width=\"130\" height=\"100\" src=\"".SN_API_THEME_URL."/images/sections/{$section['code']}.png\" title=\"{$section['code']}\" class=\"size-full alignleft\" /></a>{$section['des']}</p>
        	 <div class=\"clear\"></div>
        </div>
				
			
				";
			}
			$content .= "</div>\n";
		//$content .= print_r($sections,true);
	}else{
		$content = "<div class=\"error\">{$call['decoded']['head']['error_message']}<br /><br />Is de secret key correct? Contacteer info@scoutnet.be</div>";
	}
	
	

}else if (is_page('leiding')) {

	setlocale(LC_ALL, 'nl_BE');
	$today = mktime(0, 0, 0, date("m") , date("d"), date("Y"));
	$sn_scoutnet_api = get_option('sn_scoutnet_api');
	$emailalias = '';
	$sections = '';
	
/*
if ( is_user_logged_in() ) {
		$user_logged_in = true;
	} else {
		$user_logged_in = false;
	}
*/
	
	global $wp_query;
	//var_dump($wp_query);

	if(isset($wp_query->query_vars['name_slug'])) {
    
	$name_slug = urldecode($wp_query->query_vars['name_slug']);
	
	$content = "";
	// TODO get personid
	//$personid = 1;
	
	//$call = sn_getMember($name_slug,$sn_scoutnet_api['accountid']);
	//if ($call['decoded']['head']['status']==1){
	//$member = $call['decoded']['body']['data'];
	
	
/*

	if (isset($member['emailaliases'])){
	$emailalias = current($member['emailaliases']); 
	}

	if (isset($member['sections'])){
	foreach ($member['sections'] as $key => $section){
	$sections .= "{$section['name']}, ";
	}
	}
*/
	
	$content .= "<div id=\"sn_leiding_info\">\n";
	$content .= "\t<div class=\"leidingitem\">";
	$content .= "[scoutnet_members slug=\"$name_slug\"]";
	//$content .= "\t<h2>{$member['fname']} {$member['lname']}";
	//if ($member['nick']!=''){$content .= " - {$member['nick']}";}
	//if ($emailalias!=''){$content .= " - ".sn_scramble($emailalias,false);}
	//$content .= "</h2>";
/*
	if ($sections!=''){
		$sections = substr($sections,0,-2);
		$content .= "<br /><p>Dit jaar sta ik in leiding bij de tak: $sections</p>";
	}else{
		$content .= "<br /><p>Er is nog niet beslist bij welke tak ik dit jaar leiding geef.</p>";
	}
*/
	$content .= "<br /><p>Andere jaren heb ik reeds leiding gegevens aan: -</p>\n";
	$content .= "<br /><p>Hier kan je iets over jezelf schrijven</p>\n";
	$content .= "<br /><p>Mijn fotoalbums: -</p>\n";
	$content .= "<br /><p>Mijn kampen: -</p>\n";
	$content .= "<br /><p>Mijn opleidingen: -</p>\n";
	$content .= "\t</div>";
	$content .= "</div>";
		
	
	//$content .= print_r($member,true);
	
	//}else{$content = "<div class=\"error\">{$call['decoded']['head']['error_message']}<br /><br />Is de secret key correct? Contacteer info@scoutnet.be</div>";}
	
	}else{


	if (!isset($_SESSION['sections'][$sn_scoutnet_api['accountid']])){

	$call = sn_getAllSections($sn_scoutnet_api['accountid']);
	
	if (isset($call['decoded']['head']['status']) && ($call['decoded']['head']['status'] === "1")){
		if ($call['decoded']['body']['num']!=0){
			$all_sections = $call['decoded']['body']['data'];
		}else{
			$all_sections = array();
		}
	} else {
		echo "<div class=\"error settings-error\"><p><strong>Sections failure</strong></p></div>";
	}

	$_SESSION['sections'][$sn_scoutnet_api['accountid']]=$all_sections;
	
	}else{
	$all_sections = $_SESSION['sections'][$sn_scoutnet_api['accountid']];
	}


	
	$sections = null;
	$types = '2:3'; // leiding + groepsleiding HARDCODED


	$content = "[scoutnet_members type=\"leiding\" style=\"1\" groupby=\"section\" link_slug=\"1\"]";

/*	 
	$call = sn_getAllMembers($sn_scoutnet_api['accountid'],$sections,$types);
	if ($call['decoded']['head']['status']==1){
		$content = "";
		$members = $call['decoded']['body']['data'];
			$content .= "<div id=\"sn_leiding_info\">\n";
			if (count($members)>0){
			$m = array();
			foreach ($members as $key => $member){
			
				if ($member['sections']!=null){				
				$sections = explode(',',$member['sections']);

				foreach($sections as $section){
				$m[$section][]=$member;
				}
				}else{
				$m['Geen tak'][]=$member;
				}
			
			}

		if (isset($m['Geen tak'])){$all_sections[999999]['name']='Geen tak';}
			foreach ($all_sections as $k => $v){

				$mem = $m[$v['name']];
				if (count($mem)>0){
				$content .= "<h1>{$v['name']}</h1>";
				foreach ($mem as $member){
				$content .= "<div class=\"leidingitem\">";
				$content .= "<h2>{$member['fname']} {$member['lname']}</h2>";
				if ($member['avatar']=='y'){
				$content .= "<p><a href=\"{$member['name_slug']}/\"><img class=\"size-full alignleft\" width=\"132\" height=\"192\" title=\"{$member['fname']} {$member['lname']}\" src=\"http://images.scoutnet.be/avatar/{$member['name_slug']}-{$member['id']}.jpg\" alt=\"{$member['fname']} {$member['lname']}\" /></a></p>\n";				
				}else{
				if ($member['gender']=='M'){
				$content .= "<p><a href=\"{$member['name_slug']}/\"><img class=\"size-full alignleft\" width=\"108\" height=\"188\" title=\"{$member['fname']} {$member['lname']}\" src=\"".SN_API_THEME_URL."/images/male3.jpg\" alt=\"{$member['fname']} {$member['lname']}\" /></a></p>\n";
				}elseif ($member['gender']=='F'){
				$content .= "<p><a href=\"{$member['name_slug']}/\"><img class=\"size-full alignleft\" width=\"108\" height=\"188\" title=\"{$member['fname']} {$member['lname']}\" src=\"".SN_API_THEME_URL."/images/female3.jpg\" alt=\"{$member['fname']} {$member['lname']}\" /></a></p>\n";
				}else{
				$content .= "<p><a href=\"{$member['name_slug']}/\"><img class=\"size-full alignleft\" width=\"108\" height=\"176\" title=\"{$member['fname']} {$member['lname']}\" src=\"".SN_API_THEME_URL."/images/egg.jpg\" alt=\"{$member['fname']} {$member['lname']}\" /></a></p>\n";
				}
				}
				if ($member['nick']!=''){$content .= "<p>totem: {$member['nick']}</p>\n";}
				if (($member['birthday']!='0000-00-00')&&($member['birthday']!='')){$content .= "<p>{$member['fname']} " . generateBirthdayString($member['birthday'],$today) . "</p>\n";}
				$content .= "<p>";
				if ($member['mobile']!=''){$content .= "{$member['mobile']}<br />";}
				//if ($member['email']!=''){$content .= "".sn_scramble($member['email']);}
				$content .= "</p>\n";
				if ($member['street']!=''){$content .= "<p>{$member['street']}<br />{$member['pcode']}</p>\n";}
				if ($member['sections']!=''){$content .= "<p>In leiding bij : {$member['sections']}</p>\n";}
				
				$content .= "</div>";
				$content .= "<div class=\"clear\"></div>";
				}
				}
		}

			
			
			
			}else{
			$content .= "<p>Er is nog geen leidingsploeg samengesteld.</p>";
			}
			$content .= "</div>\n";
		//$content .= print_r($members,true);
	}else{
		$content = "<div class=\"error\">{$call['decoded']['head']['error_message']}<br /><br />Is de secret key correct? Contacteer info@scoutnet.be</div>";
	}
	
*/

	}
	
   
} else if (is_page('lokalenverhuur')){

//wp_register_script('rent_public.js', SN_API_PLUGIN_URL . 'js/rent_public.js', array( 'jquery', 'jquery-ui-core', 'jquery-ui-autocomplete' ) );
wp_register_script('map_public.js', SN_API_PLUGIN_URL . 'js/map_public.js' );
wp_enqueue_script('map_public.js');


	$call = sn_getRents();
	if ($call['decoded']['head']['status']==1){
	
		$content = "<script type=\"text/javascript\">var templateDir = \"".SN_API_PLUGIN_URL."\";</script>\n";
		
		$rents = $call['decoded']['body']['data'];
			$aantal_rents = count($rents);
			$content .= "<div id=\"sn_lokalenverhuur_info\">\n";
			$content .= "<div class=\"leidingitem\">\n";
			
			if ($aantal_rents==1){
			
				$rentid = intval($rents[0]['id']);
			
			}elseif($aantal_rents==0){
			
				$content .= "Volgens de groepinfo wordt jullie lokaal niet verhuurd. Pas dit aan of contacteer Scoutnet.<br /><br />";
			
			}else{
				$content .= "Wij verhuren lokalen op $aantal_rents verschillende locaties :<br /><br />";
				$tel = 0;
				foreach ($rents as $key => $rent){
				$tel += 1;
				$content .= "<a href=\"?rentid={$rent['id']}\">{$rent['name']}</a>";
				if ($tel < $aantal_rents){$content .= ' en ';}
				}
				$content .= "<br /><br /><br />";
				if (isset($_GET['rentid'])){
						$rentid = intval($_GET['rentid']);
				}
			
			
			}
			
			if (isset($rentid)){
			
				$call = sn_getRent($rentid);
				if ($call['decoded']['head']['status']==1){
					$lokaal = $call['decoded']['body']['data'];
					
					$tmp = explode(':',$lokaal['latlng']);
					$lokaal['lat']=$tmp[0];
					$lokaal['lng']=$tmp[1];
					unset($tmp);
					if ($lokaal['camp']==''){$lokaal['camp']='n';}
					if ($lokaal['weekend']==''){$lokaal['weekend']='n';}
					if ($lokaal['party']==''){$lokaal['party']='n';}
					if ($lokaal['attest']==''){$lokaal['attest']='n';}
					
$content .= "<div class=\"vereniging-titel\">
	<div class=\"left\">
		<h1>{$lokaal['name']}</h1>
		<p>{$lokaal['street']}<br />{$lokaal['postcode']} {$lokaal['city']}</p>";

if ($lokaal['tel']!=''){
$content .= "\t\t<p>{$lokaal['tel']}</p>\n";
}

if ($lokaal['mail']!=''){
$content .= "\t\t<p>".sn_scramble($lokaal['mail'])."</p>\n";
//$content .= "\t\t<p><a href=\"mailto:{$lokaal['mail']}\">{$lokaal['mail']}</a></p>\n";
}

if ($lokaal['nmax']>0){
$content .= "\t\t<p>Dit lokaal wordt verhuurd aan maximaal {$lokaal['nmax']} personen.</p>\n";
}
if (($lokaal['weekend']=='y')&&($lokaal['camp']=='n')){
$content .= "\t\t<p>En wordt enkel ter beschikking gesteld voor <b>weekends</b>.</p>\n";
}
if (($lokaal['weekend']=='n')&&($lokaal['camp']=='y')){
$content .= "\t\t<p>En wordt enkel ter beschikking gesteld voor <b>kampen</b>.</p>\n";
}
if (($lokaal['weekend']=='y')&&($lokaal['camp']=='y')){
$content .= "\t\t<p>En je kan er terecht voor weekends en kampen.</p>\n";
}

if ($lokaal['party']=='y'){
$content .= "\t\t<p>Ook is er de mogelijkheid om ons lokaal te huren voor een <b>feest</b>.</p>\n";
}

if ($lokaal['attest']=='y'){
$content .= "\t\t<p>Het lokaal heeft een brandveiligheidsattest.</p>\n";
}

$content .= "</div>
		<form>
		<input type=\"hidden\" id=\"lat\" value=\"{$lokaal['lat']}\" />
		<input type=\"hidden\" id=\"lng\" value=\"{$lokaal['lng']}\" />
		</form>
 			<div class=\"map\" id=\"map\" style=\"background-image:url(http://maps.google.com/maps/api/staticmap?center={$lokaal['lat']},{$lokaal['lng']}&amp;zoom=13&amp;markers=icon:".SN_API_PLUGIN_URL."/img/m_red.png|{$lokaal['lat']},{$lokaal['lng']}&amp;size=400x300&amp;sensor=false);\">
			<br/><br/><br/><div style=\"text-align:center; padding: 20px; cursor:pointer; cursor:hand; filter: alpha(opacity=55); -moz-opacity: 0.55; opacity: 0.55; background-color:#eeeeee\" onclick=\"activateMap();\">Klik om de google map te activeren</div>
			</div>
		</div>
		<h2>Onze troeven :</h2>
		<p>{$lokaal['promo']}</p>";

$content .= "<h2>Verhuurkalender:</h2>\n";
if ($lokaal['cal']=='y'){
require_once('Spinternet/Api.php');
try {
    $spinternet = Spinternet_Api::getInstance();
    $rentalCalendar = $spinternet->getRentalCalendar("{$lokaal['mykey']}");
    $content .= "<p>" .$rentalCalendar->getMonthView(). "</p>";
} catch (Exception $ex) {
    printf("<p class=\"pre\">%s</p>", $ex->__toString());
}
}else{$content .= "<p><img src=\"".SN_API_THEME_URL."/images/kalender_off.jpg\" width=\"176\" height=\"198\" alt=\"geen kalender\" title=\"De verhuurkalender is niet geactiveerd.\" /></p>";}



				
					$content .= "<br /><br /><br />";
					
					$content .= $content_by_user;
					//$content .= print_r($lokaal,true);
				}
			
			}
			
			$content .= "</div>\n";
			$content .= "</div>\n";
			
		//$content .= print_r($rents,true);
	}else{
		$content = "<div class=\"error\">{$call['decoded']['head']['error_message']}<br /><br />Is de secret key correct? Contacteer info@scoutnet.be</div>";
	}

}

// subpages /takken/kapoenen/ opvangen

if (is_page_template( 'takken-section-template.php' )){
	

	//$content = $content_by_user;
	/*
	if ( is_user_logged_in() ) {
		$user_logged_in = true;
	} else {
		$user_logged_in = false;
	}
*/


$post_7 = get_post();

//$title = $post_7->post_content;
$content = "<div class=\"leidingitem\">";
$content .= nl2br($post_7->post_content);
$content .= "</div>";

	setlocale(LC_ALL, 'nl_BE');
	$today = mktime(0, 0, 0, date("m") , date("d"), date("Y"));
	$slug = basename(get_permalink());
	
	$catid = get_cat_id($slug);
	$catid = get_category_by_slug($slug)->term_id;

	if ($catid>0){
	$posts = get_posts(array('category' => $catid, 'numberposts' => 5, 'order' => 'DESC', 'orderby' => 'date'));
	if (count($posts)>0){
	$content .= "<br /><br /><br /><h2>Laatste berichten:</h2>\n";
	$content .= "<div id=\"sn_post_info\">\n";
	foreach( (array) $posts as $post ) {
		$the_time = date('c',strtotime($post->post_modified)); // ISO 8601
		$content .= "<div class=\"leidingitem\">\n";
		//$content .= "<h2>{$post->post_title} - ".substr($post->post_modified,0,10)."</h2>";
		//$content .= "<p>{$post->post_content}</p>";
		
		//$content .=  "<a href=\"{get_permalink($post->ID)}\">{$post->post_title}</a><br />";
		
		$content .= "<h2 class=\"entry-cat\">{$post->post_title}</h2>\n";
		$content .= "<span class=\"entry-date\"><time datetime=\"{$the_time}\" class=\"entry-date\">{$post->post_modified}</time></span>";
		$content .= "<br /><br /><p>".nl2br($post->post_content)."</p>\n";
				
		//$content .= var_dump($post);
		
		$content .= "</div>\n";
	}
	
	$content .= "</div>\n";
	}

	if (class_exists('Event_List')) {

	//-3_months~+1_year  NOT WORKING WITH initial_date ??
	$past6months =  date('Y-m-d', strtotime('-6 months'));
	$next1year =  date('Y-m-d', strtotime('+1 year'));

	$content .= "<br /><br /><br /><h2>Activiteiten:</h2>\n";
	$content .= "<div id=\"sn_post_info\">\n";
	$content .= "<div class=\"leidingitem\">";
	$content .= do_shortcode("[event-list initial_date=\"{$past6months}~{$next1year}\" date_filter=\"-2_year~+2_years\" show_filterbar=\"true\" link_to_event=\"false\" cat_filter=\"{$slug}\"]");
	//$content .= do_shortcode("[event-list show_filterbar=\"false\" link_to_event=\"false\" cat_filter=\"{$slug}\"]");
	$content .= "</div>\n";
	$content .= "</div>\n";
	}

	}
	
	//TODO takleider
	$content .= "<br /><h2>De <b>leiding</b> van de $slug:</h2>";
	$content .= "[scoutnet_members type=\"leiding\" section=\"$slug\" groupby=\"none\" style=\"1\" link_slug=\"1\"]";

/*
	$call = sn_getSectionMembers($slug,'leiding');
	if ($call['decoded']['head']['status']==1){
		//$content = "";
		$members = $call['decoded']['body']['data'];
			$content .= "<br /><h2>De $slug leiding:</h2>";
			$content .= "<div id=\"sn_leiding_info\">\n";
			if (count($members)>0){
			
			foreach ($members as $key => $member){
			
				if ($member['types']!=null){				
				$types = explode(',',$member['types']);
				}
				
				if (in_array('Takleider',$types)){ // hardcoded
				$takleider = ' (takleider)';
				}else{
				$takleider = '';
				}
			
			
				$content .= "<div class=\"leidingitem\">";
				$content .= "<h2>{$member['fname']} {$member['lname']}$takleider</h2>";
				//if ($member['avatar']=='y'){
				$content .= "<p><a href=\"../../leiding/{$member['name_slug']}/\"><img class=\"size-full alignleft\" width=\"132\" height=\"192\" title=\"{$member['fname']} {$member['lname']}\" src=\"{$member['avatar']}\" alt=\"{$member['fname']} {$member['lname']}\" /></a></p>\n";				
				if ($member['nick']!=''){$content .= "<p>totem: {$member['nick']}</p>\n";}
				if (($member['birthday']!='0000-00-00')&&($member['birthday']!='')){$content .= "<p>{$member['fname']} " . generateBirthdayString($member['birthday'],$today) . "</p>\n";}
				$content .= "<p>";
				if ($member['mobile']!=''){$content .= "{$member['mobile']}<br />";}
				//if ($member['email']!=''){$content .= "".sn_scramble($member['email']);}
				$content .= "</p>\n";
				if ($member['street']!=''){$content .= "<p>{$member['street']}<br />{$member['pcode']}</p>\n";}
				$content .= "</div>";
				$content .= "<div class=\"clear\"></div>";
			
			
			
			
				//$content .= "{$member['fname']} {$member['lname']}<br /><br />";
			}
			}else{
				$content .= "Er is nog geen leiding aangeduid voor deze tak.<br /><br />";
			}
			$content .= "</div>\n";
		//$content .= print_r($members,true);
	}else{
		$content = "<div class=\"error\">{$call['decoded']['head']['error_message']}<br /><br />Is de secret key correct? Contacteer info@scoutnet.be</div>";
	}

*/

}
/*
if (is_page('kapoenen')){
$content .= "kapoenen";
}

$slug = basename(get_permalink());
$page_template = get_page_template();

$helper_vars = array('slug'=>$slug,'page_template'=>$page_template);;

$content .= "<br />".print_r($helper_vars,true)."<br />"; 
*/

return $content;

}



//apply_filters('the_content', $post->post_content);
add_action('wp_enqueue_scripts', 'sn_common_scripts');
add_filter('the_content', 'sn_hello_scoutnet');


function my_plugin_install() {

	//global $wpdb;
	
	delete_option("sn_scoutnet_pages");
	add_option("sn_scoutnet_pages", array(), '', 'yes');


}


require_once dirname( __FILE__ ) . '/api_call.php';

if ( is_admin() ){
require_once dirname( __FILE__ ) . '/admin.php';
}




function my_plugin_remove() {

	//global $wpdb;
	
	// verplaats de pagina's naar de trash. Of meteen volledig verwijderen ??
	
	$pages = get_option( 'sn_scoutnet_pages' );
	foreach($pages as $key => $page){
		if( $page['id'] ) {
			wp_delete_post( $page['id'] ); // this will trash, not delete. To also empty the trash use : wp_delete_post ($page_id, true)
		}
	}
	
	delete_option( 'sn_scoutnet_api' );
	delete_option( 'sn_scoutnet_pages' );
	delete_option( 'sn_scoutnet_group' );
	
}


function display_members_callback($atts,$content,$tag){
     
	$values = shortcode_atts(array(
		'id' => 0,
		'slug' => '',
		'section' => 'null',
		'type' => '',
		'style' => '1',
		'link_slug' => '0',
		'groupby' => 'section'
	),$atts);  
	
	$options = get_option('sn_scoutnet_api');
	
	$mysections = array();
	$mytypes = array();
	
	$styles = array(1,2,3,13);
	$groupby = array('section','type','none');
	$values['hide'] = array();
	
	if (isset($options['option1'])){if ($options['option1']=='y'){$values['hide'][] = 'birthday';}}
	if (isset($options['option2'])){if ($options['option2']=='y'){$values['hide'][] = 'phone';}}
	
	if (!in_array($values['style'],$styles)){$values['style']=1;}
	if (!in_array($values['groupby'],$groupby)){$values['groupby']='section';}
	
//return print_r($values,true);

    $output = '';
	
	
	
     
	if($values['id'] > 0){
 	$call = sn_getMember($values['id'],$options['accountid']);
	
		if ($call['decoded']['head']['status']==1){
		$member = $call['decoded']['body']['data'];
	
		//$output = print_r($member,true);
		return display_member_style($member,$values);
	
		}else{
		$output = "<div class=\"error\">error</div>";
		}
	
	//return $output;
	
	}

	if($values['slug'] != ''){
 	$call = sn_getMember($values['slug'],$options['accountid']);
	
		if ($call['decoded']['head']['status']==1){
		$member = $call['decoded']['body']['data'];
	
		//$output = print_r($member,true);
		return display_member_style($member,$values);
	
		}else{
		$output = "<div class=\"error\">error</div>";
		}
	
	//return $output;
	
	}
	
	
	
	
	if (!isset($_SESSION['sections'][$options['accountid']])){

	$call = sn_getAllSections($options['accountid']);
	
	if (isset($call['decoded']['head']['status']) && ($call['decoded']['head']['status'] === "1")){
		if ($call['decoded']['body']['num']!=0){
			$all_sections = $call['decoded']['body']['data'];
		}else{
			$all_sections = array();
		}
	} else {
		echo "<div class=\"error settings-error\"><p><strong>Sections failure</strong></p></div>";
	}

	$_SESSION['sections'][$options['accountid']]=$all_sections;
	
	}else{
	$all_sections = $_SESSION['sections'][$options['accountid']];
	}
	
	foreach($all_sections as $section){$mysections[]=$section['code'];}
	
	if (!isset($_SESSION['types'][$options['accountid']])){

	$call = sn_getAllTypes($options['accountid']);
	
	if (isset($call['decoded']['head']['status']) && ($call['decoded']['head']['status'] === "1")){
		if ($call['decoded']['body']['num']!=0){
			$all_types = $call['decoded']['body']['data'];
		}else{
			$all_types = array();
		}
	} else {
		echo "<div class=\"error settings-error\"><p><strong>Types failure</strong></p></div>";
	}

	$_SESSION['types'][$options['accountid']]=$all_types;
	
	}else{
	$all_types = $_SESSION['types'][$options['accountid']];
	}

	foreach($all_types as $type){$mytypes[]=strtolower($type['name']);}
	
	
	
	
	
	if((in_array($values['section'],$mysections)) || (in_array($values['type'],$mytypes))){

		$call = sn_getSectionMembers($values['section'],$values['type']);
		
		if ($call['decoded']['head']['status']==1){
		$members = $call['decoded']['body']['data'];
	
	
		//$output = print_r($members,true);
	
		}else{
		$output = "<div class=\"error settings-error\">error</div>";
		}
	
	 
	//return $output;
	return display_members_style($members,$values);
		
	}
	
//return print_r($_SESSION['sections'],true);
//return print_r($mytypes,true);
//return $values['type']."ee";

     
}

add_shortcode('scoutnet_members','display_members_callback');





/* Runs when plugin is activated */
register_activation_hook(__FILE__,'my_plugin_install'); 

/* Runs on plugin deactivation*/
register_deactivation_hook( __FILE__, 'my_plugin_remove' );


/* End of File */