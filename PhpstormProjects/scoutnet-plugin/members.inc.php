<?php
function sn_scoutnet_api_members_html_page() {

$options = get_option('sn_scoutnet_api');

$sendpasswordmail=false;

//$_SESSION = array();

// Finally, destroy the session.
//session_destroy();
//print_r($_SESSION);

?>
<script type="text/javascript">
var templateDir = "<?php echo SN_API_PLUGIN_URL; ?>";
</script>



<div class="wrap">
	<h2>Contacten beheer</h2>
		<?php
		$active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'per-persoon';
		?>
         
	<h2 class="nav-tab-wrapper">
		<a href="?page=scoutnet-api-members&tab=per-persoon" class="nav-tab <?php echo $active_tab == 'per-persoon' ? 'nav-tab-active' : ''; ?>">Per persoon</a>
		<a href="?page=scoutnet-api-members&tab=per-adres" class="nav-tab <?php echo $active_tab == 'per-adres' ? 'nav-tab-active' : ''; ?>">Per adres</a>
	</h2>
	<?php

	
// HARDCODED
//Array ( [21] => Array ( [natid] => 21 [natcode] => BE [nation] => België [nat] => Belg ) [56] => Array ( [natid] => 56 [natcode] => DE [nation] => Duitsland [nat] => Duitser ) [69] => Array ( [natid] => 69 [natcode] => FR [nation] => Frankrijk [nat] => Fransman ) [152] => Array ( [natid] => 152 [natcode] => NL [nation] => Nederland [nat] => Nederlander ) )
$nations = array('21'=> array('natid' => 21,'natcode' => 'BE', 'nation' => 'België', 'nat' => 'Belg'), '56'=> array('natid' => 56,'natcode' => 'DE', 'nation' => 'Duitsland', 'nat' => 'Duitser'), '69'=> array('natid' => 69,'natcode' => 'FR', 'nation' => 'Frankrijk', 'nat' => 'Fransman'), '152'=> array('natid' => 152,'natcode' => 'NL', 'nation' => 'Nederland', 'nat' => 'Nederlander'));
	
$auth_user = get_bloginfo('admin_email');

	
		if( $active_tab == 'per-persoon' ) {
		
		
/*
 // TODO waarschijnlijk nodig voor dep
if (isset($_GET['accountid'])){
$accountid = intval($_GET['accountid']);
}else{
$accountid=$account;
}
*/
		
//unset($_SESSION['sections'][$options['accountid']]);

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




if ((isset($_POST['filters']))&&(!isset($_POST['sections']))){
unset($_SESSION['sections']['filter']);
}


if (!(isset($_SESSION['sections']['filter']))){

$_SESSION['sections']['filter']['id']="";
$_SESSION['sections']['filter']['name']="";
$_SESSION['sections']['filter']['arr'] = array();

}

if (isset($_POST['sections'])){


unset($_SESSION['sections']['filter']);

$_SESSION['sections']['filter']['id']=implode(":",$_POST['sections']);
$_SESSION['sections']['filter']['arr']=$_POST['sections'];

foreach ($all_sections as $k => $v){
if (in_array($k,$_POST['sections'])){$_SESSION['sections']['filter']['name'] .= "{$v['name']}, ";}
}
$_SESSION['sections']['filter']['name'] = substr($_SESSION['sections']['filter']['name'],0,-2);
}


if ((isset($_POST['filters']))&&(!isset($_POST['types']))){
unset($_SESSION['types']['filter']);
}


if (!(isset($_SESSION['types']['filter']))){

$_SESSION['types']['filter']['id']="";
$_SESSION['types']['filter']['name']="";
$_SESSION['types']['filter']['arr'] = array();

}

if (isset($_POST['types'])){

unset($_SESSION['types']['filter']);

$_SESSION['types']['filter']['id']=implode(":",$_POST['types']);
$_SESSION['types']['filter']['arr']=$_POST['types'];

foreach ($all_types as $k => $v){
if (in_array($k,$_POST['types'])){$_SESSION['types']['filter']['name'] .= "{$v['name']}, ";}
}
$_SESSION['types']['filter']['name'] = substr($_SESSION['types']['filter']['name'],0,-2);
}





if (isset($_POST['btnVerzenden'])){

$args = array();
$personid = intval($_POST['personid']);
$accountid = intval($_POST['accountid']);

if (!isset($_POST['section'])){$_POST['section']=array();}
if (!isset($_POST['type'])){$_POST['type']=array();}

$args['personid'] = $personid;

$string_fields = array('fname'=>40,'lname'=>40,'name_alias'=>50,'nick'=>50,'birthday'=>10,'tel'=>15,'mobile'=>20,'email'=>50,'street'=>50,'bus'=>5,'nlcode'=>2,'gem'=>70,'natid'=>3,'orgnum'=>20,'gender'=>1,'countrycode'=>3,'http_host'=>40,'auth_user'=>40);

foreach ($string_fields as $key => $value){
	if (isset($_POST[$key])){
	$args[$key]=substr(trim($_POST[$key]),0,$value);
}
}

if (isset($_POST['section'])){$args['sections']=implode(':',$_POST['section']);} // optional string 1:2:3:6 
if (isset($_POST['type'])){$args['types']=implode(':',$_POST['type']);} // optional string 1:2:3:6

$args['alert']="mailto:jorisp@scoutnet.be";


try {

$call = sn_updateMember($personid,$accountid,$args);

	$warnings = $call['decoded']['head']['warning'];

	if (count($warnings)>0){
		echo "<div class=\"update-nag settings-error\">";
		foreach ($warnings as $key => $value){
		echo "<p>$value [$key]</p>";
		}
		echo "</div>";
	}

	if ($call['decoded']['head']['status']==1){
	echo "<div class=\"updated settings-error\"><p><strong>Persoongegevens zijn aangepast.</strong></p></div>";  // update-nag
		
	}else{
	echo "<div class=\"error settings-error\"><p><strong>Aanpassing mislukt</strong></p></div>";
	}
	
}catch (Exception $ex) {
    printf('<br /><span class="error">%s</span><br />', $ex->getMessage());
}


if ((isset($_POST['sn_password']))&&($_POST['sn_password']!='')){

$args = array();

$args['personid']=intval($_POST['personid']);
$args['username']=substr(trim($_POST['sn_username']),0,50);
$args['password']=substr(trim($_POST['sn_password']),0,50);


try {

$call = sn_updateMemberPassword($personid,$accountid,$args);


	$warnings = $call['decoded']['head']['warning'];

	if (count($warnings)>0){
		echo "<div class=\"update-nag settings-error\">";
		foreach ($warnings as $key => $value){
		echo "<p>$value [$key]</p>";
		}
		echo "</div>";
	}

	if ($call['decoded']['head']['status']==1){
	echo "<div class=\"updated settings-error\"><p><strong>Password updated.</strong></p></div>";  // update-nag
	$sendpasswordmail=true;
	}else{
	echo "<div class=\"error settings-error\"><p><strong>Aanpassing mislukt</strong></p></div>";
	}



} catch (Exception $ex) {
    printf('<br /><span class="error">%s</span><br />', $ex->getMessage());
}

}

}

if (isset($_POST['del'])){
if ($_POST['del']=='y'){

try {

$args = array();
$personid = intval($_POST['personid']);

$call = sn_delMember($personid);

	$warnings = $call['decoded']['head']['warning'];
	
	if (count($warnings)>0){
		echo "<div class=\"update-nag settings-error\">";
		foreach ($warnings as $key => $value){
		echo "<p>$value [$key]</p>";
		}
		echo "</div>";
	}

	if ($call['decoded']['head']['status']==1){
	echo "<div class=\"updated settings-error\"><p><strong>Person deleted.</strong></p></div>";  // update-nag
	
	}else{
	echo "<div class=\"error settings-error\"><p><strong>Aanpassing mislukt</strong></p></div>";
	}


unset($_GET['personid']);  //??


} catch (Exception $ex) {
    printf('<br /><span class="error">%s</span><br />', $ex->getMessage());
}

}
}






if (isset($_GET['personid'])){

$personid = intval($_GET['personid']);
$accountid = intval($_GET['accountid']);
//$auth_user = get_bloginfo('admin_email');


try {
if ($personid>0){

	$call = sn_getMember($personid,$accountid);
	
	if (isset($call['decoded']['head']['status']) && ($call['decoded']['head']['status'] === "1")){
		if ($call['decoded']['body']['num']!=0){
			$person = $call['decoded']['body']['data'];
		}else{
			$person = array();
		}
	} else {
		echo "<div class=\"error settings-error\"><p><strong>Types failure</strong></p></div>";
	}



}else{
$person['id']=0;
$person['accountid']=$options['accountid'];
$person['natid']=21;
$person['countrycode']="BE";
$person['birthday']='0000-00-00';

}

//var_dump($person);

if ($person){


if($sendpasswordmail){

global $current_user;
$current_user = wp_get_current_user(); 

$message = "Beste {$person['fname']},\n\nop aanvraag van {$current_user->user_email} ontvang je hier je Scoutnet ID:\n\ngebruikersnaam = {$person['username']}\npaswoord = {$args['password']}\n\n";
$message .= "Met deze login kan je aanmelden op al onze websites, mogelijks ook jullie eigen website.\n\nHeb je hierover vragen? contacteer dan {$current_user->user_email} of Scoutnet.\n\nHet Scoutnet Team";
$headers = "From: info@scoutnet.be\r\n" .
"Reply-To: {$current_user->user_email}\r\n" .
"Bcc: jorisp@scoutnet.be\r\n" .
'X-Mailer: PHP/' . phpversion();

mail($person['email'], "Scoutnet ID", $message, $headers);

}



if ($person['gender']=='F'){$person['gender']='V';} // Geslacht (wordt in de database webgeschreven als 'F')

// Onbekend en een nieuw persoon -> Belg
if (($person['natid']==247)&&($personid==0)){$person['natid']=21;} // Nationaliteit

// Default Country Belgium
//if (($person['countrycode']=='')&&($personid==0)){$person['countrycode']="BE";} // Land default BE
if ($person['countrycode']==''){$person['countrycode']="BE";} // Land default BE

// Birthday required
if (is_null($person['birthday'])){$person['birthday']='0000-00-00';}

//$person['fname'] = utf8_decode($person['fname']);
//$person['lname'] = utf8_decode($person['lname']);
//$person['street'] = utf8_decode($person['street']);
//$person['city'] = utf8_decode($person['city']);

$sections=array();
$types=array();

//var_dump($person['sections']);
if (isset($person['sections'])){
$sections = flat_array($person['sections'],'id');
}
if (isset($person['types'])){
$types = flat_array($person['types'],'id');
}

//print_r($person);
//var_dump($sections);
//var_dump($types);

if (isset($_GET['locationid'])){

	$locationid = intval($_GET['locationid']);
	//$location = $mm->getLocation($locationid);

	$call = sn_getLocation($locationid);
	
	if (isset($call['decoded']['head']['status']) && ($call['decoded']['head']['status'] === "1")){
		if ($call['decoded']['body']['num']!=0){
			$location = $call['decoded']['body']['data'];
		}else{
			$location = array();
		}
	} else {
		echo "<div class=\"error settings-error\"><p><strong>Locations failure</strong></p></div>";
	}


//$person['street'] = utf8_decode($location['street']);
$person['street'] = $location['street'];
$person['bus'] = $location['bus'];
$person['nlcode'] = $location['nlcode'];
$person['postcode'] = $location['postcode'];
//$person['city'] = utf8_encode($location['city']);
$person['city'] = $location['city'];
$person['contrycode'] = $location['countrycode'];
}

if ($personid>0){
?>
<form action="?page=scoutnet-api-members&personid=<?php echo $personid;?>" method="post" name="frmlidwijzigen" id="frmlidwijzigen">
<?php 
}else{
?>
<form action="?page=scoutnet-api-members" method="post" name="frmlidwijzigen" id="frmlidwijzigen">
<?php
}
?>
<br /><h3>Vul alle gegevens aan en klik op "Verzenden"</h3><br />
<input type="hidden" name="accountid" value="<?php echo $person['accountid'];?>" />
<input type="hidden" name="del" value="n" />
<input type="hidden" name="http_host" value="<?php echo $_SERVER['HTTP_HOST'];?>" />
<input type="hidden" name="auth_user" value="<?php echo $auth_user;?>" />
<label for="orgnum">Koepel ID</label>
<input type="text" name="orgnum" id="orgnum" value="<?php echo $person['orgnum']?>" size="10" maxlength="20" title="lidnummer bij koepel" /><span class="notatie"><?php echo $options['orgname'];?> lidnummer</span><br />
<label for="personid"><?php echo $options['depmark'];?> ID</label>
<input type="text" name="personid" id="personid" value="<?php echo $person['id'];?>" size="10" title="<?php echo $options['depmark'];?> ID" readonly="readonly" /> <input type="text" name="sn_username" id="sn_username" value="<?php echo $person['username'];?>" size="30" title="Username" readonly="readonly" /> <img width="9" height="9" title="change password" src="<?php echo SN_API_PLUGIN_URL?>img/plus.gif" name="state14" onclick="exp(14)" alt="password" id="state14" />
<div id="item14" style="display: none;" class="sourcecode">
<br />
&nbsp;<input type="button" class="button button-primary" value="generate new password" onclick="generate_password()" /><input name="sn_password" id="sn_password" type="text" size="24" maxlength="20" value="" autocomplete="off" class="password1" /> &nbsp;<span class="notatie">Geef enkel het nieuwe paswoord als je dit wilt wijzigen</span>
</div>
<br />
<input type="hidden" name="score" id="score" value="" />
<label for="fname">Voornaam</label>
<input type="text" name="fname" class="required" id="fname" maxlength="50" size="40" title="geef de voornaam" value="<?php echo $person['fname'];?>" /><br />
<label for="lname">Familienaam</label>
<input type="text" name="lname" class="required" id="lname" maxlength="50" size="40" title="geef de familienaam" value="<?php echo $person['lname'];?>" /> <img width="9" height="9" title="eventueel bijnaam of roepnaam" src="<?php echo SN_API_PLUGIN_URL?>img/plus.gif" name="state15" onclick="exp(15)" alt="eventueel bijnaam of roepnaam" id="state15" />
<div id="item15" style="display: none;" class="sourcecode">
<label for="name_alias">Bijnaam</label>
<input type="text" name="name_alias" id="name_alias" maxlength="50" size="40" title="geef eventueel bijnaam of roepnaam" value="<?php echo $person['name_alias'];?>" placeholder="geef de bijnaam of roepnaam" />
</div>
<br />
<label for="nick">Totem</label>
<input type="text" name="nick" id="nick" maxlength="50" size="40" title="geef de <?php echo $nick;?>" value="<?php echo $person['nick'];?>" /><br />
<label for="birthday">Geboortedatum</label>
<input type="text" name="birthday" class="required" id="birthday" maxlength="10" title="geef de geboortedatum" value="<?php echo $person['birthday'];?>" /><span class="notatie">jjjj-mm-dd</span><br />
<label for="tel">Telefoon</label>
<input type="text" name="tel" id="tel" maxlength="15" size="40" title="geef telefoonnnumer" value="<?php echo $person['tel'];?>" /><span class="notatie">+32.12345678</span><br />
<label for="mobile">GSM</label>
<input type="text" name="mobile" id="mobile" maxlength="20" size="40" title="geef mobiel nummer" value="<?php echo $person['mobile'];?>" /><br />
<label for="email">Email</label>
<input type="text" name="email" class="email" id="email" maxlength="50" size="40" title="geef emailadres" value="<?php echo $person['email'];?>" /><br />
<label for="gender">Geslacht</label>
<input type="text" name="gender" class="required" id="gender" size="1" maxlength="1" title="geef het geslacht M|V" value="<?php echo $person['gender'];?>" /><span class="notatie">M/V</span><br />
<label for="natid">Nationaliteit</label>
<select name="natid" id="natid" title="kies de nationaliteit">
<?php
	foreach ($nations as $l_landen){
		if ($person['natid'] != $l_landen['natid']) {
        	echo "<option value='{$l_landen['natid']}'>{$l_landen['nat']}</option>";
		} else {
			echo "<option value='{$l_landen['natid']}' selected='selected'>{$l_landen['nat']}</option>";
		}
    }
?>
</select><br /><br />

<label for="street">Straat + nummer</label>
<input type="text" name="street" id="street" value="<?php echo $person['street'];?>" title="geef de straatnaam" size="30" maxlength="50" /> bus <input type="text" name="bus" id="bus" value="<?php echo $person['bus'];?>" title="geef het busnummer indien van toepassing" size="4" maxlength="5" /> <span class="notatie">Kerkwegel 11 + 1</span><br />
<label for="gem">Gemeente</label>
<input type="text" name="gem" id="gem" value="<?php echo "{$person['postcode']} {$person['city']}";?>" size="40" maxlength="150" autocomplete="off" title="geef de postcode en gemeente" />
<input type="text" name="nlcode" id="nlcode" value="<?php echo "{$person['nlcode']}";?>" size="2" maxlength="2" title="Geef de 2 letter code (enkel voor Nederland)" style="<?php if ($person['countrycode']=="BE"){echo "display:none;visibility:hidden;";}else{echo "display:inline;visibility:visible;";};?>" />

<select name="countrycode" id="countrycode" onchange="toonNLcode(this.value);" title="geef de 2 letterige landcode (BE|NL)">
<?php 
	foreach ($nations as $l_landen){
		if ($person['countrycode'] != $l_landen["natcode"]) {
        	echo "<option value='{$l_landen["natcode"]}'>{$l_landen["natcode"]}</option>";
		} else {
			echo "<option value='{$l_landen["natcode"]}' selected='selected'>{$l_landen["natcode"]}</option>";
			
		}
    }
?>
</select>
<?php //if($person->getLocationID()>1){
if (($personid>0)&&($person['locationid']>0)){
echo "<a href=\"?page=scoutnet-api-members&tab=per-adres&locationid={$person['locationid']}\">Wil je meteen het <b>adres</b> van ALLE gezinsleden wijzigen? klik dan op deze link</a>";
}
//}

echo "<br />";

echo "<div id=\"container_st\">\n";
echo "<div>\n";
echo "<fieldset>\n";
echo "<legend title=\"Select one or more sections\">Sections:</legend>\n";

if (count($all_sections)>0){
foreach ($all_sections as $k => $v){
if (!in_array($k,$sections)){
	echo "<input type=\"checkbox\" value=\"$k\" name=\"section[]\" /> {$v['name']}<br />\n";
}else{
	echo "<input type=\"checkbox\" value=\"$k\" name=\"section[]\" checked=\"checked\" /> <b>{$v['name']}</b><br />\n";
}
}
}else{
echo "Aanmaak van sections doe je momenteel nog via https://my.scoutnet.be";
}

echo "</fieldset>\n</div>\n<div>\n<fieldset>\n";

echo "<legend title=\"Select one or more types\">Types:</legend>\n";

if (count($all_types)>0){
foreach ($all_types as $k => $v){
if (!in_array($k,$types)){
	echo "<input type=\"checkbox\" value=\"$k\" name=\"type[]\" /> {$v['name']}<br />\n";
}else{
	echo "<input type=\"checkbox\" value=\"$k\" name=\"type[]\" checked=\"checked\" /> <b>{$v['name']}</b><br />\n";
}
}
}else{
echo "Aanmaak van types doe je momenteel nog via https://my.scoutnet.be";
}
echo "</fieldset>\n";
echo "</div>\n";
echo "</div>\n";
echo "<div class=\"snspacer\"></div>";


?>
<br />
<br />
<input name="btnVerzenden" id="btnVerzenden" class="button button-primary" type="submit" value="Verzenden" />
<?php
if ($personid>0){
?>
<img src="<?php echo SN_API_PLUGIN_URL;?>img/delete.gif" style="cursor:pointer" onclick="dele('<?php echo $personid;?>');" width="20" height="16" alt="delete" title="delete person" />
<?php
}
?> 
</form>

<?php

}else{
echo "<br /><span class=\"warning\">Person error</span><br />";
}


} catch (Exception $ex) {
echo "<br /><br /><span class=\"error\">".$ex->getMessage()."</span><br />";
}

}











	//$members = $mm->getAllMembers($_SESSION['sections']['filter']['id'],$_SESSION['types']['filter']['id']);

	$call = sn_getAllMembers($options['accountid'],$_SESSION['sections']['filter']['id'],$_SESSION['types']['filter']['id']); // section en type filter
	
	if ($call['decoded']['head']['status']==1){
	
	$members = $call['decoded']['body']['data'];
	
	//var_dump($members);
	

	
	
	
	
	
	if (count($members)>=0){



$all=array('orgnum'=>'Koepel ID','id'=>'Spin ID','groupname'=>'Groepsnaam','username'=>'Username','fname'=>'Voornaam','lname'=>'Familienaam','since'=>'Aansluitdatum (todo)','svd'=>'Aantal jaren lid (todo)','birthday'=>'Geboortedatum','age'=>'Leeftijd','gender'=>'Geslacht','nat'=>'Nationaliteit','tel'=>'Telefoon','mobile'=>'GSM','email'=>'Email','adres'=>'Adres','street'=>'Straat','bus'=>'Bus','nlcode'=>'NLcode','postcode'=>'Postcode','city'=>'Plaats','pcode'=>'Postcode Plaats','gem'=>'Gemeente','regio'=>'Regio','prov'=>'Provincie','country'=>'Land');

if (isset($_POST['kolom'])) {
$_SESSION[$active_tab]['kolom']=$_POST['kolom'];
}

if (!isset($_SESSION[$active_tab]['kolom'])) {
$kolom=array('id','fname','lname','birthday','email');
}else{
$kolom=$_SESSION[$active_tab]['kolom'];
}

if (isset($_POST['filter'])){
$_SESSION[$active_tab]['filter']=array_filter($_POST['filter']);
}

if (!isset($_SESSION[$active_tab]['filter'])) {
$_POST['filter']=array();
}else{
$_POST['filter']=$_SESSION[$active_tab]['filter'];
}

if (!isset($_POST['andor'])) {
	$_POST['andor']='and';
}

if (!isset($_POST['check'])) {
	$check=array();
}else{
	$check=$_POST['check'];
}


?>
<br />Filter op inhoud: <img height="9" width="9" alt="expand" title="column filter" onclick="exp(901)" name="state901" src="<?php echo SN_API_PLUGIN_URL;?>/img/plus.gif" id="state901" border="0" /><?php if (count($_POST['filter'])>0){echo " <img src=\"".SN_API_PLUGIN_URL."img/filter.png\" width=\"24\" height=\"23\" />";}?><br />
<div id="item901" style="font-size: 12px; margin-left: 20px; display: none;">
<form action="" method="post">
<table>
<tr><td><input type="radio" name="andor" value="and" <?php if($_POST['andor']=='and'){echo " checked=\"checked\"";}?> /> EN <input type="radio" name="andor" value="or" <?php if($_POST['andor']=='or'){echo " checked=\"checked\"";}?> />OF </td><td>&nbsp;</td><td>&nbsp;</td></tr>
<?php 
foreach ($all as $key => $value){
?>
<tr><td><input type="text" id="f<?php echo $key;?>" name="filter[<?php echo $key;?>]" size="10" value="<?php echo @$_POST['filter'][$key];?>"<?php if (!in_array($key,$kolom)){echo " disabled=\"disabled\"";}?> /></td><td><input type="checkbox" value="<?php echo $key;?>" name="kolom[]"<?php if (in_array($key,$kolom)){echo " checked=\"checked\"";}?> onclick="setCheck(this,'f<?php echo $key;?>');" /></td><td><?php echo $value;?></td></tr>	
<?php 
}
?>

<tr><td colspan="2"><input type="submit" name="" value="Filter on content" class="button button-primary" title="Selecteer de kolomkoppen" /></td></tr>
</table>
<br />
</form>
</div>
<br />
<?php

// TODO filter op soort ook in een session variabele bewaren
?>

Filter op soort: <img height="9" width="9" alt="expand" title="column filter" onclick="exp(902)" name="state902" src="<?php echo SN_API_PLUGIN_URL;?>/img/plus.gif" id="state902" border="0" /><?php if ((count($_SESSION['sections']['filter']['arr'])>0)||(count($_SESSION['types']['filter']['arr'])>0)){echo " <img src=\"".SN_API_PLUGIN_URL."img/filter.png\" width=\"24\" height=\"23\" />";}?> <?php echo "<span title=\"sections\"><b>".$_SESSION['sections']['filter']['name']."</b></span>&nbsp; &nbsp;<span title=\"types\"><b>".$_SESSION['types']['filter']['name']."</b></span>";?><br />
<div id="item902" style="font-size: 12px; margin-left: 20px; display: none;">
<br />
<form action="" method="post">
<input type="submit" value="Filter sections/types" name="filters" class="button button-primary" title="Maak selectie" /> <small>[beheer (toevoegen/verwijderen) voorlopig enkel mogelijk via my-site]</small><br /><br />
<div id="container_st">
<div>
	<fieldset>
	<legend>Sections:</legend>
<?php 
foreach ($all_sections as $k => $v){
if (!in_array($k,$_SESSION['sections']['filter']['arr'])){
	echo "<input type=\"checkbox\" value=\"$k\" name=\"sections[]\" /> {$v['name']}<br />\n";
}else{
	echo "<input type=\"checkbox\" value=\"$k\" name=\"sections[]\" checked=\"checked\" /> {$v['name']}<br />\n";
}
}

?>
	<br />
	</fieldset>
 
</div>
<div>

	<fieldset>
	<legend>Types:</legend>
<?php 
foreach ($all_types as $k => $v){
if (!in_array($k,$_SESSION['types']['filter']['arr'])){
	echo "<input type=\"checkbox\" value=\"$k\" name=\"types[]\" /> {$v['name']}<br />\n";
}else{
	echo "<input type=\"checkbox\" value=\"$k\" name=\"types[]\" checked=\"checked\" /> {$v['name']}<br />\n";
}
}

?>
	<br />
	</fieldset>
</div>
</div>
</form>
</div>
<div class="snspacer"></div>
<?php

echo "<table id=\"persons\" style=\"clear:both;\" class=\"tablesorter\">"; 
echo "<thead>"; 
echo "<tr>";
 

foreach ($all as $key => $value){
	if (in_array($key,$kolom)){echo "<th>{$value}</th>";}
}



echo "</tr>"; 
echo "</thead>"; 
echo "<tbody>";


//var_dump($members);

$filter_num=count($_POST['filter']);

	foreach($members as $member){
	

	
	
	
		if ($filter_num>0){
		$show_line=false;
		$elem_num=0;

			foreach ($kolom as $f){
				if (@$_POST['filter'][$f]!=''){
					$match="1"; 
					$pos=false;

					// aantal jaren lid
					if (($_POST['filter'][$f]{0}=='>')&&(($f=="svd")||($f=="age"))){$match=">";}
					if (($_POST['filter'][$f]{0}=='<')&&(($f=="svd")||($f=="age"))){$match="<";}
					/*
					if (($_POST['filter'][$f]{0}=='>')&&($f=="afstand")){
									$pieces = explode("-", $member['afstand']);
									$pieces = array_map("inte", $pieces);
									$fafstand = intval(substr($_POST['filter'][$f],1,9));
						
									foreach ($pieces as $piece){
									if ($piece>$fafstand){$show_line=true;}
									}
						
					}
					*/
					if ($_POST['filter'][$f]{0}=='%'){$match="0";}

					switch ($_POST['andor']){
						case 'or':
								switch ($match){
								case "1":
								if (strtolower($member[$f])==strtolower($_POST['filter'][$f])){$show_line=true;}
								break;
								case "0":
									$pos = stripos($member[$f], substr($_POST['filter'][$f],1));
									if ($pos !== false) {$show_line=true;}
								break;
								case ">":
									$fafstand = intval(substr($_POST['filter'][$f],1,9));
									
									switch ($f){
										case "svd":
											$my_num=calculateAge($member['since']);
										break;
										case "age":
											$my_num=calculateAge($member['birthday']);
										break;
										default:
									}
									
									if ($my_num>$fafstand){$show_line=true;}
									
								break;
								
								case "<":
									$fafstand = intval(substr($_POST['filter'][$f],1,9));
									
									switch ($f){
										case "svd":
											$my_num=calculateAge($member['since']);
										break;
										case "age":
											$my_num=calculateAge($member['birthday']);
										break;
										default:
									}
									
									if ($my_num<$fafstand){$show_line=true;}
									
								break;
								
								}
							break;
						case 'and':
								switch ($match){
								case "1":
								if (strtolower($member[$f])==strtolower($_POST['filter'][$f])){$elem_num+=1;}
								if ($filter_num==$elem_num){$show_line=true;}
								break;
								case "0":
								$pos = stripos($member[$f], substr($_POST['filter'][$f],1));
								if ($pos !== false){$elem_num+=1;}
								if ($filter_num==$elem_num){$show_line=true;}
								break;
								case ">":
								
									$fafstand = intval(substr($_POST['filter'][$f],1,9));
									$aantal_afstanden_groter_dan_gevraagd=0;
									
									switch ($f){
										case "svd":
											$my_num=calculateAge($member['since']);
											
										break;
										case "age":
											$my_num=calculateAge($member['birthday']);
										break;
										default:
									}
									
									if ($my_num>$fafstand){$aantal_afstanden_groter_dan_gevraagd+=1;}
									
									if ($aantal_afstanden_groter_dan_gevraagd>0){$elem_num+=1;}
									
									if ($filter_num==$elem_num){$show_line=true;}
									
								break;
								
								case "<":
								
									$fafstand = intval(substr($_POST['filter'][$f],1,9));
									$aantal_afstanden_groter_dan_gevraagd=0;
									
									switch ($f){
										case "svd":
											$my_num=calculateAge($member['since']);
											
										break;
										case "age":
											$my_num=calculateAge($member['birthday']);
										break;
										default:
									}
									
									if ($my_num<$fafstand){$aantal_afstanden_groter_dan_gevraagd+=1;}
									
									if ($aantal_afstanden_groter_dan_gevraagd>0){$elem_num+=1;}
									
									if ($filter_num==$elem_num){$show_line=true;}
									
								break;
								
								
								}								
				
							break;
							
					}
				}
				
			}
			}else{
				$show_line=true;
			}
	
	
	
	
	
	foreach ($all as $key => $value){if (in_array($key,$kolom)){$member['class'][$key]='normal';}}
			
			
			if ($show_line){
			$span = array();

			if (in_array('id',$kolom)){
				$member['id']="<a href=\"?page=scoutnet-api-members&amp;personid={$member['id']}&amp;accountid={$member['accountid']}\" class=\"{$member['class']['id']}\">{$member['id']}</a>";
				array_push($span, "id");
			}
		
			
			if (in_array('adres',$kolom)){
				if ($member['bus']==''){$member['adres']="{$member['street']} {$member['pcode']}";}
				else{$member['adres']="{$member['street']} bus {$member['bus']} {$member['pcode']}";}
			}
			
			if (in_array('age',$kolom)){
				$member['age']=calculateAge($member['birthday'])." jaar";
			}
			
	
			echo "<tr>";
			foreach ($all as $key => $value){
				if (in_array($key,$kolom)){
					if (!in_array($key,$span)){$member[$key]="<span class=\"{$member['class'][$key]}\">{$member[$key]}</span>";}
					echo "<td>{$member[$key]}</td>";

				}
			}
			echo "</tr>\n";
	
			}
	
	
	}


echo "</tbody>";
echo "</table>";

}else{
	echo "<span class=\"info\">Er zijn nog geen leden.</span>";
}
	
	
	
echo "<br /><img src=\"".SN_API_PLUGIN_URL."img/add_contact.gif\" alt=\"nieuw persoon toevoegen\" title=\"nieuw persoon toevoegen\" width=\"25\" height=\"21\" /> <a href=\"?page=scoutnet-api-members&personid=0\">Persoon toevoegen.</a><br />";
	
	
	
	

	}else{
		echo "<div class=\"error\">{$call['decoded']['head']['error_message']}<br /><br />Is de secret key correct? Contacteer info@scoutnet.be</div>";
	}
	

		
		
		
		}else{  // per-adres
		

		


if (isset($_POST['btnVerzenden'])){

$args = array();

$locationid = intval($_POST['locationid']);
$args['locationid'] = $locationid;
$string_fields = array('name'=>50,'street'=>50,'bus'=>5,'nlcode'=>2,'gem'=>70,'countrycode'=>2,'http_host'=>40,'auth_user'=>40);

foreach ($string_fields as $key => $value){
	if (isset($_POST[$key])){
	$args[$key]=substr(trim($_POST[$key]),0,$value);
}
}

try {

$call = sn_updateLocation($locationid, $args);

	$warnings = $call['decoded']['head']['warning'];

	if (count($warnings)>0){
		echo "<div class=\"update-nag settings-error\">";
		foreach ($warnings as $key => $value){
		echo "<p>$value [$key]</p>";
		}
		echo "</div>";
	}

	if ($call['decoded']['head']['status']==1){
	echo "<div class=\"updated settings-error\"><p><strong>Adresgegevens zijn aangepast.</strong></p></div>";  // update-nag
		
	}else{
	echo "<div class=\"error settings-error\"><p><strong>Aanpassing mislukt</strong></p></div>";
	}
	
}catch (Exception $ex) {
    printf('<br /><span class="error">%s</span><br />', $ex->getMessage());
}



}
		
		
		
		
		
		
if (isset($_GET['locationid'])){

$locationid = intval($_GET['locationid']);

try {

	//$location = $mm->getLocation($locationid);
	$call = sn_getLocation($locationid);
	
	if (isset($call['decoded']['head']['status']) && ($call['decoded']['head']['status'] === "1")){
		if ($call['decoded']['body']['num']!=0){
			$location = $call['decoded']['body']['data'];
		}else{
			$location = array();
		}
	} else {
		echo "<div class=\"error settings-error\"><p><strong>Locations failure</strong></p></div>";
	}



//var_dump($location);

if ($location){

// Default Country Belgium
if (($location['countrycode']=='')&&($locationid==0)){$location['countrycode']="BE";} // Land default BE

$location['name'] = $location['name']; // TODO check utf8_decode ???
$location['street'] = $location['street'];
$location['city'] = $location['city'];

?>

<form action="" method="post" name="frmlidwijzigen" id="frmlidwijzigen">
<br /><h3>Vul alle gegevens aan en klik op "Verzenden"</h3><br />
<input type="hidden" name="accountid" value="<?php echo $options['accountid'];?>" />
<input type="hidden" name="locationid" id="locationid" value="<?php echo $location['id'];?>" />
<input type="hidden" name="http_host" value="<?php echo $_SERVER['HTTP_HOST'];?>" />
<input type="hidden" name="auth_user" value="<?php echo $auth_user;?>" />
<label for="name">Aanspreking</label>
<input type="text" name="name" id="name" value="<?php echo $location['name'];?>" title="geef de aanspreektitel" size="40" maxlength="100" /> <span class="notatie">De familie ..., De Heer en Mevrouw Peeters - Vandamme ...</span><br />
<label for="street">Straat + nummer</label>
<input type="text" name="street" id="street" value="<?php echo $location['street'];?>" title="geef de straatnaam en huisnummer" size="30" maxlength="50" /> bus <input type="text" name="bus" id="bus" value="<?php echo $location['bus'];?>" title="geef het busnummer indien van toepassing" size="4" maxlength="10" /> <span class="notatie">Kerkwegel 25 [+ busnummer]</span><br />
<label for="gem">Gemeente</label>
<input type="text" name="gem" id="gem" value="<?php echo "{$location['postcode']} {$location['city']}";?>" size="40" maxlength="150" autocomplete="off" title="geef de postcode en gemeente" />
<input type="text" name="nlcode" id="nlcode" value="<?php echo "{$location['nlcode']}";?>" size="2" maxlength="2" title="Geef de letter code (enkel voor Nederland)" style="<?php if ($location['countrycode']!="NL"){echo "display:none;visibility:hidden;";}else{echo "display:inline;visibility:visible;";};?>" />
<select name="countrycode" id="countrycode" title="geef de 2 letterige landcode" onchange="toonNLcode(this.value);">
<?php 
	foreach ($nations as $l_landen){
		if ($location['countrycode'] != $l_landen["natcode"]) {
        	echo "<option value='{$l_landen["natcode"]}'>{$l_landen["natcode"]}</option>";
		} else {
			echo "<option value='{$l_landen["natcode"]}' selected='selected'>{$l_landen["natcode"]}</option>";
			
		}
    }
?>
</select><br /> 


<?php 

foreach ($location['persons'] as $key => $row) {
	$birthday[$key] = $row['birthday'];
	$gender[$key] = $row['gender'];
}

//array_multisort($birthday, SORT_ASC, $l_members);
array_multisort($gender, SORT_ASC, $birthday, SORT_ASC, $location['persons']);

if (count($location['persons']>0)){
echo "<table cellspacing=\"10\">";
foreach ($location['persons'] as $member){
$member['fname']=$member['fname'];
$member['lname']=$member['lname'];
echo "<tr><td>";
$l_memberprofilepath = "https://my.scoutnet.be/members/profile/{$member['personid']}.jpg";
//if (file_exists($_SERVER['DOCUMENT_ROOT'].$l_memberprofilepath)){
if (file_exists($l_memberprofilepath)){
echo "<br /><a href=\"?page=scoutnet-api-members&amp;personid={$member['personid']}\"><img src='{$l_memberprofilepath}' alt='{$member['fname']}' title='{$member['fname']}' /></a>";
}else{echo "<br /><a href=\"?page=scoutnet-api-members&amp;personid={$member['personid']}\"><img src='https://my.scoutnet.be/members/profile/0.jpg' alt='{$member['fname']}' title='{$member['fname']}' /></a>";}
echo "</td>";
echo "<td>";
if ($member['email']){echo "<a href=\"mailto:{$member['email']}\">{$member['fname']} {$member['lname']}</a><br />";}
else{echo "{$member['fname']} {$member['lname']}<br />";}
if ($member['mobile']){echo "{$member['mobile']}<br />";}
echo "{$member['gender']} {$member['birthday']}<br />";
/*
if ($member['paid']=='1'){$paidchecked=" checked=\"checked\" disabled=\"disabled\"";$paidstr="betaald";}else{$paidchecked="";$paidstr="";}
echo "Lidgeld 2012 ? <input type=\"checkbox\" value=\"{$member['personid']}\"$paidchecked onclick=\"setPaid('{$member['personid']}',this.checked,'{$l_sessie->getSessionUIDencrypted()}');\" /><span id=\"gs{$member['personid']}\">{$paidstr}</span>";
*/

echo "</td>";
echo "</tr>";
}
echo "<tr><td><a href=\"?page=scoutnet-api-members&amp;personid=0&amp;locationid={$location['id']}\"><img src='https://my.scoutnet.be/members/profile/0.jpg' alt='nieuw contact' title='nieuw contact' /></a></td><td>Nieuw contact toevoegen <b>op dit adres</b>.</td></tr>";
echo "</table>";
}
?>
<br />
<input name="btnVerzenden" id="btnVerzenden" class="button button-primary" type="submit" value="Verzenden" />
</form>
	
<?php 
}else{
//echo "<span class=\"warning\">Location error</span>";
}

} catch (Exception $ex) {
echo "<span class=\"error\">".$ex->getMessage()."</span>";
}



}
		
		

		
		

	$call = sn_getAllLocations('residence');
	
	if (isset($call['decoded']['head']['status']) && ($call['decoded']['head']['status'] === "1")){
		if ($call['decoded']['body']['num']!=0){
			$locations = $call['decoded']['body']['data'];
		}else{
			$locations = array();
		}
	} else {
		echo "<div class=\"error settings-error\"><p><strong>Locations failure</strong></p></div>";
	}



//var_dump($locations);

//if ($locations===false){var_dump($snapi->showError());}

if (count($locations)>0){


$all=array('locationid'=>'Adres ID','name'=>'Aanspreking','adres'=>'Adres','street'=>'Straat','bus'=>'Bus','postcode'=>'Postcode','pcode'=>'Postcode Plaats','nlcode'=>'NL code','city'=>'Plaats','gem'=>'Gemeente','regio'=>'Regio','prov'=>'Provincie','country'=>'Land');


if (isset($_POST['kolom'])) {
$_SESSION[$active_tab]['kolom']=$_POST['kolom'];
}

if (!isset($_SESSION[$active_tab]['kolom'])) {
$kolom=array('name','adres','pcode','regio');
}else{
$kolom=$_SESSION[$active_tab]['kolom'];
}

if (isset($_POST['filter'])){
$_SESSION[$active_tab]['filter']=array_filter($_POST['filter']);
}

if (!isset($_SESSION[$active_tab]['filter'])) {
$_POST['filter']=array();
}else{
$_POST['filter']=$_SESSION[$active_tab]['filter'];
}

if (!isset($_POST['andor'])) {
	$_POST['andor']='and';
}

if (!isset($_POST['check'])) {
	$check=array();
}else{
	$check=$_POST['check'];
}


?>

<br />Filter op inhoud: <img height="9" width="9" alt="expand" title="filter + selecteer kolommen" onclick="exp(901)" name="state901" src="<?php echo SN_API_PLUGIN_URL;?>img/plus.gif" id="state901" /><?php if (count($_POST['filter'])>0){echo " <img src=\"".SN_API_PLUGIN_URL."img/filter.png\" width=\"24\" height=\"23\" />";}?><br />
<div id="item901" class="sourcecode" style="font-size: 12px; margin-left: 20px; display: none;">
<form action="" method="post">
<table>
<tr><td><input type="radio" name="andor" value="and" <?php if($_POST['andor']=='and'){echo " checked=\"checked\"";}?> /> EN <input type="radio" name="andor" value="or" <?php if($_POST['andor']=='or'){echo " checked=\"checked\"";}?> />OF </td><td>&nbsp;</td><td>&nbsp;</td></tr>
<?php 
foreach ($all as $key => $value){
?>
<tr><td><input type="text" id="f<?php echo $key;?>" name="filter[<?php echo $key;?>]" size="10" value="<?php echo @$_POST['filter'][$key];?>"<?php if (!in_array($key,$kolom)){echo " disabled=\"disabled\"";}?> /></td><td><input type="checkbox" value="<?php echo $key;?>" name="kolom[]"<?php if (in_array($key,$kolom)){echo " checked=\"checked\"";}?> onclick="setCheck(this,'f<?php echo $key;?>');" /></td><td><?php echo $value;?></td></tr>	
<?php 
}
?>
<tr><td colspan="2"><input type="submit" name="" value="Filter on content" class="button button-primary" title="Selecteer de kolomkoppen" /></td></tr>
</table>
</form>
</div>


<?php 


echo "<table id=\"addresses\" class=\"tablesorter\">"; 
echo "<thead>"; 
echo "<tr>";
 

foreach ($all as $key => $value){
	if (in_array($key,$kolom)){echo "<th>{$value}</th>";}
}


echo "</tr>"; 
echo "</thead>"; 
echo "<tbody>";


	//var_dump($members);

	foreach($locations as $location){
	
			$l_class = "normal"; // kleurtjes gebruiken
			$span=array();
			$l_locationid = $location['locationid'];
		
			if (in_array('name',$kolom)){
				//$location['name']=utf8_decode($location['name']); //?? TODO check
				if ($location['name']==''){$location['name']="EMPTY";}
				if ($location['bad']==0){
				$location['name']="<a href=\"?page=scoutnet-api-members&amp;tab=per-adres&amp;locationid={$location['locationid']}\">{$location['name']}</a>";
				}else{
				$location['name']="<a href=\"?page=scoutnet-api-members&amp;tab=per-adres&amp;locationid={$location['locationid']}\" style=\"color:red;\" title=\"Dit adres is vermoedelijk fout.\">{$location['name']}</a>";
				}
				array_push($span, "name");
			}
			/*
			if ((in_array('street',$kolom))||((in_array('adres',$kolom)))){
				$location['street']=utf8_decode($location['street']);
			}
			
			if (in_array('pcode',$kolom)){
				$location['pcode']=utf8_decode($location['pcode']);
			}
			*/
			if (in_array('adres',$kolom)){
				if ($location['bus']==''){$location['adres']="{$location['street']} {$location['num']}";}
				else{$location['adres']="{$location['street']} {$location['num']} bus {$location['bus']}";}
			}
			/*
			if (in_array('country',$kolom)){
				$location['country']=utf8_decode($location['country']);
			}
			
			if (in_array('regio',$kolom)){
				$location['regio']=utf8_decode($location['regio']);
			}
			
			if (in_array('gem',$kolom)){
				$location['gem']=utf8_decode($location['gem']);
			}
			*/
			if (in_array('locationid',$kolom)){
				$location['locationid']="<a href=\"?page=scoutnet-api-members&amp;tab=per-adres&amp;locationid={$location['locationid']}\">{$location['locationid']}</a>";
				array_push($span, "locationid");
			}			
			
			
		if ($filter_num>0){
		$show_line=false;
		$elem_num=0;

			foreach ($kolom as $f){
				if (@$_POST['filter'][$f]!=''){
					$match="1"; 
					$pos=false;

					// afstanden
					if (($_POST['filter'][$f]{0}=='>')&&($f=="afstand")){$match=">";}
					/*
					if (($_POST['filter'][$f]{0}=='>')&&($f=="afstand")){
									$pieces = explode("-", $location['afstand']);
									$pieces = array_map("inte", $pieces);
									$fafstand = intval(substr($_POST['filter'][$f],1,9));
						
									foreach ($pieces as $piece){
									if ($piece>$fafstand){$show_line=true;}
									}
						
					}
					*/
					if ($_POST['filter'][$f]{0}=='%'){$match="0";}

					switch ($_POST['andor']){
						case 'or':
								switch ($match){
								case "1":
								if (strtolower($location[$f])==strtolower($_POST['filter'][$f])){$show_line=true;}
								break;
								case "0":
									$pos = stripos($location[$f], substr($_POST['filter'][$f],1));
									if ($pos !== false) {$show_line=true;}
								break;
								case ">":
								
									$pieces = explode("-", $location['afstand']);
									$pieces = array_map("inte", $pieces);
									$fafstand = intval(substr($_POST['filter'][$f],1,9));
							
									foreach ($pieces as $piece){
									if ($piece>$fafstand){$show_line=true;}
									}
									
								break;
								
								}
							break;
						case 'and':
								switch ($match){
								case "1":
								if (strtolower($location[$f])==strtolower($_POST['filter'][$f])){$elem_num+=1;}
								if ($filter_num==$elem_num){$show_line=true;}
								break;
								case "0":
								$pos = stripos($location[$f], substr($_POST['filter'][$f],1));
								if ($pos !== false){$elem_num+=1;}
								if ($filter_num==$elem_num){$show_line=true;}
								break;
								case ">":
								
									$pieces = explode("-", $location['afstand']);
									$pieces = array_map("inte", $pieces);
									$fafstand = intval(substr($_POST['filter'][$f],1,9));
						
									// geef het aantal afstanden die groter zijn dan de gevraagde afstand.
									//als dat getal groter is dan 1 dan $elem_num+=1
									$aantal_afstanden_groter_dan_gevraagd=0;
									foreach ($pieces as $piece){
									if ($piece>$fafstand){$aantal_afstanden_groter_dan_gevraagd+=1;}
									}
									
									if ($aantal_afstanden_groter_dan_gevraagd>0){$elem_num+=1;}
									
									if ($filter_num==$elem_num){$show_line=true;}
									
								break;
								
								}								
				
							break;
							
					}
				}
				
			}
			}else{
				$show_line=true;
			}
			
			//$show_line=true;
			if ($l_locationid==787){$show_line=false;}

			if ($show_line){
			$l_teller +=1;
		
			echo "<tr>";
			/*
			foreach ($all as $key => $value){
				if (in_array($key,$kolom)){echo "<td>{$location[$key]}</td>";}
			}
			*/
			foreach ($all as $key => $value){
				if (in_array($key,$kolom)){
					if (!in_array($key,$span)){$location[$key]="<span>{$location[$key]}</span>";}
					//echo "<td><span class=\"{$location['class'][$key]}\">{$location[$key]}</span></td>";
					echo "<td>{$location[$key]}</td>";
				}
			}
			
			
			
			echo "</tr>\n";
			
			}
	
	
	
	
	}


echo "</tbody>";
echo "</table>";

}else{

	echo "Er zijn nog geen locations.";
}



//}

echo "<br /><img src=\"".SN_API_PLUGIN_URL."img/add_contact.gif\" alt=\"nieuw persoon toevoegen\" title=\"nieuw persoon toevoegen\" width=\"25\" height=\"21\" /> <a href=\"?page=scoutnet-api-members&amp;personid=0\">Persoon toevoegen.</a><br />";

if ($l_teller != 1) {echo "<p>In totaal werden <strong>{$l_teller} adressen</strong> gevonden.</p>"; }
else {echo "<p>In totaal werd <strong>1 adres</strong> gevonden.</p>";}

$filter_str='';
if ($filter_num>0){
	$filter_str = "Filter: "; 
foreach ($_POST['filter'] as $key => $value){
	$filter_str.= "$key:<b>$value</b> {$_POST['andor']} ";
}
$filter_str=substr($filter_str,0,-4);
}
echo $filter_str;
?>

<br />
(*) Het veld "Aanspreking" kan je vrij invullen. Dit in functie van de samenstelling van het gezin.<br />
Dit kan je gebruiken voor het versturen van brieven.<br />
Er verschijnt "EMPTY" indien dit veld leeg is.<br />
Indien de aanspreking in het rood staat, dan is het adres vermoedelijk niet juist.<br />

		
		
		
		
		
		
		
<?php 		
		}
	?>

</div>

<?php
}
?>