<?php

function sn_getGrouplist___(){

$method = 'GET';
$endpoint = 'lists/';
$devkey = "jorisp@scoutnet.be";  //deprecate
$options = get_option('sn_scoutnet_api');
$secret = $options['apigroupkey'];

$appkey = substr($secret,0,6); // snxxxx
try{
$apicall = new Scoutnet_API_Call('group', $devkey, $appkey, $secret, false);
$call = $apicall->run($endpoint, $method, $args);

}catch(Exception $e){
throw($e->getMessage());
}

return $call;
}

function sn_getGroup(){

//lists/124?fields=sections

$args = null;
$method = 'GET';
$options = get_option('sn_scoutnet_api');
$secret = $options['apigroupkey'];
$endpoint = "lists/{$options['accountid']}/";
$devkey = "jorisp@scoutnet.be";  //deprecate
$appkey = substr($secret,0,6); // snxxxx
try{
$apicall = new Scoutnet_API_Call('group', $devkey, $appkey, $secret, false);
$call = $apicall->run($endpoint, $method, $args);

}catch(Exception $e)
{echo '<div class="error">' .$e->getMessage().'</div>';}

return $call;
}

function sn_getRents(){

$args = null;
$method = 'GET';
$endpoint = "rent/";
$devkey = 'jorisp@scoutnet.be'; // deprecate
$options = get_option('sn_scoutnet_api');
$secret = $options['apigroupkey'];
$appkey = substr($secret,0,6); // snxxxx

try{
$apicall = new Scoutnet_API_Call('group', $devkey, $appkey, $secret, false);
$call = $apicall->run($endpoint, $method, $args);

}catch(Exception $e)
{echo '<div class="error">' .$e->getMessage().'</div>';}

return $call;

}

function sn_getRent($rentid){

$args = null;
$rentid=intval($rentid);
$options = get_option('sn_scoutnet_api');
$accountid = intval($options['accountid']);
$method = 'GET';
$endpoint = "rent/$accountid/$rentid/";
$devkey = 'jorisp@scoutnet.be'; // deprecate
$secret = $options['apigroupkey'];
$appkey = substr($secret,0,6); // snxxxx

try{
$apicall = new Scoutnet_API_Call('group', $devkey, $appkey, $secret, false);
$call = $apicall->run($endpoint, $method, $args);

}catch(Exception $e)
{echo '<div class="error">' .$e->getMessage().'</div>';}

return $call;

}

function sn_updateRent($rentid,$args){

$rentid=intval($rentid);
$options = get_option('sn_scoutnet_api');
$accountid = intval($options['accountid']);
$method = 'PUT';
$endpoint = "rent/$accountid/$rentid/";
$devkey = 'jorisp@scoutnet.be'; // deprecate
$secret = $options['apigroupkey'];
$appkey = substr($secret,0,6); // snxxxx

try{
$apicall = new Scoutnet_API_Call('group', $devkey, $appkey, $secret, false);
$call = $apicall->run($endpoint, $method, $args);

}catch(Exception $e)
{echo '<div class="error">' .$e->getMessage().'</div>';}

return $call;

}


function sn_getSections(){

$args = null;
$method = 'GET';
$options = get_option('sn_scoutnet_api');
$secret = $options['apigroupkey'];
$endpoint = "sections/{$options['accountid']}/";
$devkey = 'jorisp@scoutnet.be'; // deprecate
$appkey = substr($secret,0,6); // snxxxx

try{
$apicall = new Scoutnet_API_Call('group', $devkey, $appkey, $secret, false);
$call = $apicall->run($endpoint, $method, $args);

}catch(Exception $e)
{echo '<div class="error">' .$e->getMessage().'</div>';}

return $call;

}

function sn_getSectionMembers($section,$type){

$args = null;
$method = 'GET';
$options = get_option('sn_scoutnet_api');
$secret = $options['apigroupkey'];
$endpoint = "sections/members/{$options['accountid']}/$section/$type/";
$devkey = 'jorisp@scoutnet.be'; // deprecate
$appkey = substr($secret,0,6); // snxxxx

try{
$apicall = new Scoutnet_API_Call('group', $devkey, $appkey, $secret, false);
$call = $apicall->run($endpoint, $method, $args);

}catch(Exception $e)
{echo '<div class="error">' .$e->getMessage().'</div>';}

return $call;

}


function sn_getAllMembers($accountid,$sections=null,$types=null,$limit=0,$sortby='',$order=''){

$tmp_arr = array();
$allowed_sort_field = array('id','fname','lname','email','username','birthday','sections'); // used for sorting

$accountid = intval($accountid);


if (isset($sections)&&(!empty($sections))){ // optional 1:5:9:6
$tmp_arr['sections']=substr(trim($sections),0,50); // todo opschonen -> explode to array + intval op elk element + implode to string
}

if (isset($types)&&(!empty($types))){ // optional 1:2:3:8:9
$tmp_arr['types']=substr(trim($types),0,50);
}

if ($limit>0){ // optional 
$tmp_arr['limit']=intval($limit);
}

if ((in_array(strtolower($sortby),$allowed_sort_field))){
$tmp_arr['sortby'] = $sortby;
}

if ((in_array(strtolower($order),array('asc','desc')))){
$tmp_arr['order'] = $order;
}

$query_str = urldecode(http_build_query($tmp_arr,'','&'));
if ($query_str!=''){$query_str='?'.$query_str;} 


$args = null;
$method = 'GET';
$endpoint = "lists/$accountid/$query_str";
$options = get_option('sn_scoutnet_api');
$secret = $options['apimemberkey'];
$devkey = "jorisp@scoutnet.be"; // deprecate
$appkey = substr($secret,0,6); // snxxxx

try{
$apicall = new Scoutnet_API_Call('member', $devkey, $appkey, $secret, false);
$call = $apicall->run($endpoint, $method, $args);

}catch(Exception $e)
{echo '<div class="error">' .$e->getMessage().'</div>';}

return $call;

}


function sn_getAllSections($accountid){

$args = null;
$accountid = intval($accountid);
$method = 'GET';
$endpoint = "sections/$accountid/";
$options = get_option('sn_scoutnet_api');
$secret = $options['apimemberkey'];
$devkey = "jorisp@scoutnet.be"; // deprecate
$appkey = substr($secret,0,6); // snxxxx

try{
$apicall = new Scoutnet_API_Call('member', $devkey, $appkey, $secret, false);
$call = $apicall->run($endpoint, $method, $args);

}catch(Exception $e)
{echo '<div class="error">' .$e->getMessage().'</div>';}

return $call;

}

function sn_getAllTypes($accountid){

$args = null;
$accountid = intval($accountid);
$method = 'GET';
$endpoint = "types/$accountid/";
$options = get_option('sn_scoutnet_api');
$secret = $options['apimemberkey'];
$devkey = "jorisp@scoutnet.be"; // deprecate
$appkey = substr($secret,0,6); // snxxxx

try{
$apicall = new Scoutnet_API_Call('member', $devkey, $appkey, $secret, false);
$call = $apicall->run($endpoint, $method, $args);

}catch(Exception $e)
{echo '<div class="error">' .$e->getMessage().'</div>';}

return $call;

}

function sn_getMember($person,$accountid){

if (is_integer($person)){
$person = intval($person);
}else{
$person = substr(trim($person),0,80);
}

$args = null;
$accountid = intval($accountid);
$method = 'GET';
$endpoint = "lists/$accountid/$person/";
$options = get_option('sn_scoutnet_api');
$secret = $options['apimemberkey'];
$devkey = "jorisp@scoutnet.be"; // deprecate
$appkey = substr($secret,0,6); // snxxxx

try{
$apicall = new Scoutnet_API_Call('member', $devkey, $appkey, $secret, false);
$call = $apicall->run($endpoint, $method, $args);

}catch(Exception $e)
{echo '<div class="error">' .$e->getMessage().'</div>';}

return $call;

}


function sn_updateMember($personid,$accountid,$args){

$personid = intval($personid);
$accountid = intval($accountid);
if ($personid>0){$method='PUT';}
else{$method='POST';}
$endpoint = "lists/$accountid/$personid/";
$options = get_option('sn_scoutnet_api');
$secret = $options['apimemberkey'];
$devkey = "jorisp@scoutnet.be"; // deprecate
$appkey = substr($secret,0,6); // snxxxx

try{
$apicall = new Scoutnet_API_Call('member', $devkey, $appkey, $secret, false);
$call = $apicall->run($endpoint, $method, $args);

}catch(Exception $e)
{echo '<div class="error">' .$e->getMessage().'</div>';}

return $call;

}



function sn_updateMemberPassword($personid=0,$accountid=0,$args){

$personid = intval($personid);
$accountid = intval($accountid);
$method='PUT';
$endpoint = "lists/password/$accountid/$personid/";
$options = get_option('sn_scoutnet_api');
$secret = $options['apimemberkey'];
$devkey = "jorisp@scoutnet.be"; // deprecate
$appkey = substr($secret,0,6); // snxxxx

try{
$apicall = new Scoutnet_API_Call('member', $devkey, $appkey, $secret, false);
$call = $apicall->run($endpoint, $method, $args);

}catch(Exception $e)
{echo '<div class="error">' .$e->getMessage().'</div>';}

return $call;

}

function sn_delMember($personid=0,$accountid=0){

$args = array();
$personid = intval($personid);
$accountid = intval($accountid);
$method='DELETE';
$endpoint = "lists/$accountid/$personid/";
$options = get_option('sn_scoutnet_api');
$secret = $options['apimemberkey'];
$devkey = "jorisp@scoutnet.be"; // deprecate
$appkey = substr($secret,0,6); // snxxxx

try{
$apicall = new Scoutnet_API_Call('member', $devkey, $appkey, $secret, false);
$call = $apicall->run($endpoint, $method, $args);

}catch(Exception $e)
{echo '<div class="error">' .$e->getMessage().'</div>';}

return $call;

}

function sn_getAllLocations($type='residence',$limit=null){

switch ($type){
case 'residence':
	$endpoint = "locations";
	break;
/*case 'campplaces':
	$return = self::apiCall('getAllLocations',array('limit'=>$limit,'sessionID'=>$this->session),true);
	break;*/
}

$args = array();
$method='GET';
$options = get_option('sn_scoutnet_api');
$secret = $options['apimemberkey'];
$devkey = "jorisp@scoutnet.be"; // deprecate
$appkey = substr($secret,0,6); // snxxxx

try{
$apicall = new Scoutnet_API_Call('member', $devkey, $appkey, $secret, false);
$call = $apicall->run($endpoint, $method, $args);

}catch(Exception $e)
{echo '<div class="error">' .$e->getMessage().'</div>';}

return $call;

}


function sn_getLocation($locationid=0){

$args = array();
$locationid = intval($locationid);
$method='GET';
$endpoint = "locations/$locationid/";
$options = get_option('sn_scoutnet_api');
$secret = $options['apimemberkey'];
$devkey = "jorisp@scoutnet.be"; // deprecate
$appkey = substr($secret,0,6); // snxxxx

try{
$apicall = new Scoutnet_API_Call('member', $devkey, $appkey, $secret, false);
$call = $apicall->run($endpoint, $method, $args);

}catch(Exception $e)
{echo '<div class="error">' .$e->getMessage().'</div>';}

return $call;

}


function sn_updateLocation($locationid,$args){

$location = intval($locationid);
$method='PUT';
$endpoint = "locations/$locationid/";
$options = get_option('sn_scoutnet_api');
$secret = $options['apimemberkey'];
$devkey = "jorisp@scoutnet.be"; // deprecate
$appkey = substr($secret,0,6); // snxxxx

try{
$apicall = new Scoutnet_API_Call('member', $devkey, $appkey, $secret, false);
$call = $apicall->run($endpoint, $method, $args);

}catch(Exception $e)
{echo '<div class="error">' .$e->getMessage().'</div>';}

return $call;

}


//helper functions

/* Set locale to Dutch */
//setlocale(LC_ALL, 'nl_NL');

/* Output: vrijdag 22 december 1978 */
//echo strftime("%A %e %B %Y", mktime(0, 0, 0, 12, 22, 1978));

function calculateAge($dateString) {
$timestamp = strtotime($dateString);
if ($timestamp === false || $timestamp == -1) {return false;}
return date('Y') - date('Y', $timestamp) - (date('m') < date('m', $timestamp) ? 1 : (date('m') == date('m', $timestamp) && date('d') < date('d', $timestamp) ? 1 : 0));
}

function calculateBirthday($dateString) {
$dateString = date("Y") . substr($dateString,4);
$timestamp = strtotime($dateString);
if ($timestamp === false || $timestamp == -1) {return false;}
return strftime("%A %e %B", $timestamp);
}

function generateBirthdayString($dateString,$today){
$birth = strtotime($dateString);
if ($birth === false || $birth == -1) {return "error calculating age";}
$dateString = date("Y") . substr($dateString,4);
$birthday = strtotime($dateString);
$verschil = $today-$birthday;
$daystring_long = strftime("%A %e %B", $birthday);
$daystring_short = strftime("%e %B %Y", $birthday);

if ($verschil<0){  // de verjaardag moet dit jaar nog komen
if (abs($verschil)>8640000){ // en is nog meer dan 100 dagen verwijderd
return "is jarig op $daystring_short.";
}else{
$yearstring = date('Y') - date('Y', $birth) - (date('m') < date('m', $birth) ? 1 : (date('m') == date('m', $birth) && date('d') < date('d', $birth) ? 1 : 0)) + 1;
return "gaat op $daystring_long $yearstring jaar worden.";
}

}elseif($verschil>0){ // de verjaardag is reeds geweest
if ($verschil<2592000){ // en nog niet langer geleden dan 30 dagen
$yearstring = date('Y') - date('Y', $birth) - (date('m') < date('m', $birth) ? 1 : (date('m') == date('m', $birth) && date('d') < date('d', $birth) ? 1 : 0));  
return "is op $daystring_long $yearstring jaar geworden.";
}else{
return "is jarig op " . strftime("%e %B %Y", strtotime('+1 year', $birthday)) . ".";
}

}else{
return "is vandaag jarig !";
}


}

function checkMyDate($str) {
	$timestamp=strtotime($str);
	$mydate=getdate($timestamp);
	if ((checkdate($mydate['mon'], $mydate['mday'], $mydate['year']))&&($timestamp<time())){return true;}else{return false;}
}

function flat_array($src,$str){
$result = array();
	foreach ($src as $v1 => $k1){
	if (is_array($k1)){
		foreach ($k1 as $v2 => $k2){
			if ($v2==$str){$result[]=$k2;}
		}
	}
	
	}
return $result;

}

function inte($str){return(intval($str));}

function safe_print() {
   print " -------- I think I'm getting a clue!";
}

function sn_scramble($address,$link=true){
	$address = strtolower($address);
	$address = str_replace("-", "hack2dirty", $address);
	$coded = "";
	if ($link){$link='true';}else{$link='false';}
	$unmixedkey = "_ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789.@";
	$inprogresskey = $unmixedkey;
	$mixedkey="";
	$unshuffled = strlen($unmixedkey);
	for ($i = 0; $i <= strlen($unmixedkey); $i++){
		$ranpos = rand(0,$unshuffled-1);
		$nextchar = $inprogresskey{$ranpos};
		$mixedkey .= $nextchar;
		$before = substr($inprogresskey,0,$ranpos);
		$after = substr($inprogresskey,$ranpos+1,$unshuffled-($ranpos+1));
		$inprogresskey = $before.''.$after;
		$unshuffled -= 1;
	}
    $cipher = $mixedkey;

	$shift = strlen($address);

    $txt = "\n<script type=\"text/javascript\">\n" .
           "<!-"."-\n";

	for ($j=0; $j<strlen($address); $j++){
	if (strpos($cipher,$address{$j}) == -1 ){
		$chr = $address{$j};
		$coded .= $address{$j};
	}else{
		$chr = (strpos($cipher,$address{$j}) + $shift) % strlen($cipher);
		$coded .= $cipher{$chr};
	}
    }

	$txt .= "sn_scramble(\"".$coded."\",\"".$cipher."\",$link);"."\n//-"."->" . "\n<" . "/script><noscript>Javascript disabled" . "<"."/noscript>\n";
	
	return $txt;
}


/*
'id' => 0,
'section' => 'null',
'type' => '',
'style' => '1' (default) | '2' | '3'
'groupby' => 'section' (default) | 'type' | 'none'

*/

function display_member_style($member,$param){

$show = array();
$show['birthday'] = true;
$show['phone'] = true;
$leiding = false;
$lid = false;
$types_str = '';
$sections_str = '';

if (! is_user_logged_in() ) {
if (in_array('birthday',$param['hide'])){$show['birthday']=false;}
if (in_array('phone',$param['hide'])){$show['phone']=false;}
}


if ($member['nick']==''){$member['nick']='-';}

if (is_array($member['types'])){
//$types_str = implode(", ", $member['types']);

foreach($member['types'] as $type){
	if ($type['name']=='Leiding'){$leiding=true;}
	if ($type['name']=='Lid'){$lid=true;}
	$types_str .=  $type['name'] . ', ';
}
$types_str = substr($types_str, 0, -2);
}

if (is_array($member['sections'])){
foreach($member['sections'] as $section){
	$sections_str .=  $section['name'] . ', ';
}
$sections_str = substr($sections_str, 0, -2);
}



		$content = "<div class=\"leidingitem\">";
		if ($member['name_alias']==''){
		$content .= "<h2>{$member['fname']} {$member['lname']}</h2>";
		}else{
		$content .= "<h2>{$member['fname']} {$member['lname']} ({$member['name_alias']})</h2>";
		}
		
		if ($param['link_slug']==1){
		$site_url = get_home_url();
		$content .= "<p><a href=\"$site_url/leiding/{$member['name_slug']}/\"><img class=\"size-full alignleft\" width=\"132\" height=\"192\" title=\"{$member['fname']} {$member['lname']}\" src=\"{$member['avatar']}\" alt=\"{$member['fname']} {$member['lname']}\" /></a></p>\n";
		}else{
		$content .= "<p><img class=\"size-full alignleft\" width=\"132\" height=\"192\" title=\"{$member['fname']} {$member['lname']}\" src=\"{$member['avatar']}\" alt=\"{$member['fname']} {$member['lname']}\" /></p>\n";
		}

		//$content .= "<p><img class=\"size-full alignleft\" width=\"132\" height=\"192\" title=\"{$member['fname']} {$member['lname']}\" src=\"{$member['avatar']}\" alt=\"{$member['fname']} {$member['lname']}\" /></p>\n";
		$content .= "<p>totem: {$member['nick']}</p>\n";

		$content .= "<p>";
		if ($show['phone']){
		if ($member['mobile']!=''){$content .= "{$member['mobile']}<br />";}
		}
		if (is_array($member['emailaliases'])){$content .= "".sn_scramble(reset($member['emailaliases']));}

		if ($show['birthday']){
		//$ymd = DateTime::createFromFormat('Y-m-d', $member['birthday'])->format("j F Y");
		//$timestamp = DateTime::createFromFormat('Y-m-d', $member['birthday']);
		//$ymd = date_i18n("j F Y", $timestamp);

		if (($member['birthday']!='0000-00-00')&&($member['birthday']!='')){$content .= "<br />&deg; " . date_i18n("j F Y", strtotime($member['birthday'])) . "\n";}
		}
		if ($member['street']!=''){$content .= "<br />{$member['street']}<br />{$member['pcode']}\n";}
		$content .= "</p>\n";
		$content .= "<p>\n";
		if ($types_str!=''){$content .= "Functies : {$types_str}<br />\n";}
		if ($sections_str!=''){
			if($leiding){
			$content .= "In leiding bij de tak: {$sections_str}\n";
			}
			if($lid){
			$content .= "Tak : {$sections_str}\n";
			}
		}
		$content .= "</p>\n";
		$content .= "</div>\n";
		$content .= "<div class=\"clear\"></div>";

//$content .= print_r($param,true);
//$content .= print_r($member,true);


return $content;

}

function display_members_style($members,$param){

	$m = array();
	$show = array();	


	$content = "<div id=\"sn_leiding_info\">\n";

		if (count($members)>0){


$show['birthday'] = true;
$show['phone'] = true;

if (! is_user_logged_in() ) {
	//$content .= "niet ingelogged";
	//$content .= print_r($param['hide'],true);
if (in_array('birthday',$param['hide'])){$show['birthday']=false;}
if (in_array('phone',$param['hide'])){$show['phone']=false;}
}

//$content.= print_r($members,true);

/*opsplitsing per section*/

switch ($param['groupby']){


case 'section':
	foreach ($members as $key => $member){
		if ($member['sections']!=null){				
		$sections = explode(',',$member['sections']);
			foreach($sections as $section){
			$m[$section][]=$member;
			}
			}else{
			$m['geen tak'][]=$member;  // geen tak
		}
	}

	if ($param['type']!='null'){
	$mygroup = $param['type'];
	}else{
	$mygroup = $param['section'];
	}

break;


case 'type':

	foreach ($members as $key => $member){
		if ($member['types']!=null){				
		$types = explode(',',$member['types']);
			foreach($types as $type){
			$m[$type][]=$member;
			}
			}else{
			$m['no type'][]=$member; 
		}
	}

	$mygroup = $param['type'];
break;


case 'none':

	foreach ($members as $key => $member){
			$m['null'][]=$member;
	}


if ( (isset($param['type'])) &&  (isset($param['section'])) ){
	if ($param['section']!='null'){
		$mygroup = $param['section'] . ' ' . $param['type'];
	}else{
		$mygroup = $param['type'];
	}
} elseif (isset($param['type'])) {
	$mygroup = $param['type'];
} elseif (isset($param['section'])) {
	$mygroup = $param['section'];
} else {
$mygroup = '-';
}


break;


}


//$content.= print_r($m,true);


			
switch ($param['style']){

case '1':

	foreach ($m as $key => $mem){
//$content .= print_r($m,true);
//$content .= count($m);
		if (count($m)>1){
		if ($key!='null'){$content .= "<h1>$key</h1>";}
		}
			foreach ($mem as $member){

//$content .= print_r($member,true);

			if ($member['types']!=null){$types = explode(',',$member['types']);}
			
			if (in_array('Takleider',$types)){ // hardcoded
			$takleider = ' (takleider)';
			}else{
			$takleider = '';
			}


			$content .= "<div class=\"leidingitem\">";
			if($member['name_alias']==''){
			$content .= "<h2>{$member['fname']} {$member['lname']}$takleider</h2>";
			}else{
			$content .= "<h2>{$member['name_alias']}</h2>";
			}
			/*
			if (isset($member['avatar'])){
			$content .= "<p><img class=\"size-full alignleft\" width=\"132\" height=\"192\" title=\"{$member['fname']} {$member['lname']}\" src=\"http://images.scoutnet.be/avatar/{$member['name_slug']}-{$member['id']}.jpg\" alt=\"{$member['fname']} {$member['lname']}\" /></p>\n";
			}else{
			if ($member['gender']=='M'){
			$content .= "<p><img class=\"size-full alignleft\" width=\"108\" height=\"188\" title=\"{$member['fname']} {$member['lname']}\" src=\"http://images.scoutnet.be/avatar/male.jpg\" alt=\"{$member['fname']} {$member['lname']}\" /></p>\n";
			}elseif ($member['gender']=='F'){
			$content .= "<p><img class=\"size-full alignleft\" width=\"108\" height=\"188\" title=\"{$member['fname']} {$member['lname']}\" src=\"http://images.scoutnet.be/avatar/female.jpg\" alt=\"{$member['fname']} {$member['lname']}\" /></p>\n";
			}else{
			$content .= "<p><img class=\"size-full alignleft\" width=\"108\" height=\"176\" title=\"{$member['fname']} {$member['lname']}\" src=\"http://images.scoutnet.be/avatar/egg.jpg\" alt=\"{$member['fname']} {$member['lname']}\" /></p>\n";
			}
			}*/

			if ($param['link_slug']==1){
			$site_url = get_home_url();
			$content .= "<p><a href=\"$site_url/leiding/{$member['name_slug']}/\"><img class=\"size-full alignleft\" width=\"132\" height=\"192\" title=\"{$member['fname']} {$member['lname']}\" src=\"{$member['avatar']}\" alt=\"{$member['fname']} {$member['lname']}\" /></a></p>\n";
			}else{
			$content .= "<p><img class=\"size-full alignleft\" width=\"132\" height=\"192\" title=\"{$member['fname']} {$member['lname']}\" src=\"{$member['avatar']}\" alt=\"{$member['fname']} {$member['lname']}\" /></p>\n";
			}

			//$content .= "<p><img class=\"size-full alignleft\" width=\"132\" height=\"192\" title=\"{$member['fname']} {$member['lname']}\" src=\"{$member['avatar']}\" alt=\"{$member['fname']} {$member['lname']}\" /></p>\n";

			if ($member['nick']!=''){$content .= "<p>totem: {$member['nick']}</p>\n";}
			if ($show['birthday']){
			if (($member['birthday']!='0000-00-00')&&($member['birthday']!='')){$content .= "<p>{$member['fname']} " . generateBirthdayString($member['birthday'],$today) . "</p>\n";}
			}
			$content .= "<p>";
			if ($show['phone']){
			if ($member['mobile']!=''){$content .= "{$member['mobile']}<br />";}
			}
			//if ($member['email']!=''){$content .= "".sn_scramble($member['email']);}
			$content .= "</p>\n";
			if ($member['street']!=''){$content .= "<p>{$member['street']}<br />{$member['pcode']}</p>\n";}
			if ($member['sections']!=''){$content .= "<p>In leiding bij : {$member['sections']}</p>\n";}
		
			$content .= "</div>";
			$content .= "<div class=\"clear\"></div>";

			}
			 
		}

break;

case '2':

		foreach ($m as $key => $mem){

				if (count($m)>1){
				if ($key!='null'){$content .= "<h1>$key</h1>";}
				}

				$content .= "<div class=\"entry-content-full\">\n";
				$content .= "<p>";
				foreach ($mem as $member){

				$emailalias='';
				if (isset($member['emailaliases'])){$emailalias = current($member['emailaliases']);}
				if ($emailalias!=''){$emailalias = sn_scramble($emailalias,false);}
				if ($show['phone']){
				$title = "{$member['fname']} {$member['lname']}\n\n{$member['mobile']}\n\n{$emailalias}";
				}else{
				$title = "{$member['fname']} {$member['lname']}\n\n{$emailalias}";
				}
/*
				if (isset($member['avatar'])){
				$avatar_uri = "http://images.scoutnet.be/avatar/{$member['name_slug']}-{$member['id']}.jpg";
				}else{
				if ($member['gender']=='M'){
				$avatar_uri = "http://images.scoutnet.be/avatar/male.jpg";
				}elseif ($member['gender']=='F'){
				$avatar_uri = "http://images.scoutnet.be/avatar/female.jpg";
				}else{
				$avatar_uri = "http://images.scoutnet.be/avatar/egg.jpg";
				}
				}
*/
				if ($param['link_slug']==1){
				$site_url = get_home_url();
				$content .= "<a href=\"$site_url/leiding/{$member['name_slug']}/\"><img class=\"size-full alignleft\" width=\"132\" height=\"192\" title=\"{$title}\" src=\"{$member['avatar']}\" alt=\"{$member['fname']} {$member['lname']}\" /></a>";
				}else{
				$content .= "<img class=\"size-full alignleft\" width=\"132\" height=\"192\" title=\"{$title}\" src=\"{$member['avatar']}\" alt=\"{$member['fname']} {$member['lname']}\" />";
				}
	

				}
				$content .= "</p>";
				$content .= "<div class=\"clear\"></div>\n";
				$content .= "<p>{$mygroup}: ";
				$tmp_members_string = '';
				foreach ($mem as $member){

				if ($member['types']!=null){$types = explode(',',$member['types']);}

				if (in_array('Takleider',$types)){ // hardcoded
				$takleider = ' (takleider)';
				}else{
				$takleider = '';
				}

				if ($member['name_alias']==''){
				$tmp_members_string .= "{$member['fname']}$takleider, ";
				}else{
				$tmp_members_string .= "{$member['name_alias']}$takleider, ";
				}
				}
				$tmp_members_string = substr($tmp_members_string, 0, -2);
				$content .= $tmp_members_string;
				$content .= "</p>";
				$content .= "</div>\n";
				$content .= "<div class=\"clear\"></div>\n";
			 

	}



break;


case '3':

		foreach ($m as $key => $mem){

			if (count($m)>1){
			if ($key!='null'){$content .= "<h1>$key</h1>";}
			}
			
				$content .= "<div class=\"leidingitem\">\n";
				$content .= "<table>\n";
				foreach ($mem as $member){
					
				if ($member['types']!=null){$types = explode(',',$member['types']);}

				if (in_array('Takleider',$types)){ // hardcoded
				$takleider = ' (takleider)';
				}else{
				$takleider = '';
				}

				$emailalias='';
				if (isset($member['emailaliases'])){$emailalias = current($member['emailaliases']);}
								
				$content .= "<tr>\n";
				
				if ($emailalias!=''){
				$content .= "<td><a href='mailto:". sn_scramble($emailalias,false)."'>{$member['fname']} {$member['lname']}</a>$takleider</td>";
					}else{
				$content .= "<td>{$member['fname']} {$member['lname']}$takleider</td>";
				}
				
				//$content .= "<td>{$member['fname']} {$member['lname']}</td>";
				$content .= "<td>{$member['nick']}</td>";
				if ($show['phone']){
				//$content .= "<td>".sn_scramble($member['mobile'])."</td>";
				$content .= "<td>{$member['mobile']}</td>";	
				}
				if ($show['birthday']){
					//if (($member['birthday']!='0000-00-00')&&($member['birthday']!='')){$content .= "<td>" . generateBirthdayStringShort($member['birthday']) . "</td>";}else{$content.="<td>-</td>";}
					if (($member['birthday']!='0000-00-00')&&($member['birthday']!='')){$content .= "<td>" . calculateAge($member['birthday']) . " jaar</td>";}else{$content.="<td>-</td>";}
					
				}
				
				if (($member['street']!='')&&($member['pcode']!='')){$content .= "<td>{$member['street']}, {$member['pcode']}</td>\n";}


				$content .= "</tr>\n";
							
				}
				$content .= "</table>\n";
				$content .= "</div>\n";
				
		}
break;


case '13':

		foreach ($m as $key => $mem){

			if ($key!='null'){$content .= "<h1>$key</h1>";}
			
				$content .= "<div class=\"leidingitem\">\n";
				$content .= "<table>\n";
				foreach ($mem as $member){
					
				$emailalias='';
				if (isset($member['emailaliases'])){$emailalias = current($member['emailaliases']);}
								
				$content .= "<tr>\n";
				
				if ($member['name_alias']==''){
				$content .= "<td><strong>{$member['fname']} {$member['lname']}</strong></td>";
					}else{
				$content .= "<td><strong>{$member['fname']} \"{$member['name_alias']}\" {$member['lname']}</strong></td>";
				}
			
				$content .= "<td>{$member['nick']}</td>";
				$content .= "</tr>\n";
							
				}
				$content .= "</table>\n";
				$content .= "</div>\n";
				
		}
break;

default:

		$content .= "<div class=\"error\">Invalid or missing style parameter</div><div class=\"clear\"></div>";

}



//$content = print_r($members,true);


			
			
			}else{
			$content .= "<p>Geen contactpersonen gevonden.</p>";
			}
			$content .= "</div>\n";
		//$content .= print_r($members,true);
	
	return $content;
}

function get_mysections($accountid){

	$mysections = array();
	
	if (!isset($_SESSION['sections'][$accountid])){

	$call = sn_getAllSections($accountid);
	
	if (isset($call['decoded']['head']['status']) && ($call['decoded']['head']['status'] === "1")){
		if ($call['decoded']['body']['num']!=0){
			$all_sections = $call['decoded']['body']['data'];
		}else{
			$all_sections = array();
		}
	}else{
		return false;
	}

	$_SESSION['sections'][$accountid]=$all_sections;
	
	}else{
	$all_sections = $_SESSION['sections'][$accountid];
	}
	
	foreach($all_sections as $section){$mysections[]=$section['code'];}

	return $mysections;
}

function get_mytypes($accountid){
	
	$mytypes = array();
	
	if (!isset($_SESSION['types'][$accountid])){

	$call = sn_getAllTypes($accountid);
	
	if (isset($call['decoded']['head']['status']) && ($call['decoded']['head']['status'] === "1")){
		if ($call['decoded']['body']['num']!=0){
			$all_types = $call['decoded']['body']['data'];
		}else{
			$all_types = array();
		}
	} else {
		return false;
	}

	$_SESSION['types'][$accountid]=$all_types;
	
	}else{
	$all_types = $_SESSION['types'][$accountid];
	}

	foreach($all_types as $type){$mytypes[]=strtolower($type['name']);}
	
	return $mytypes;
	
}



?>
