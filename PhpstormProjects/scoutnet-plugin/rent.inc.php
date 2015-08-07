<?php

function my_admin_notice() {
    ?>
    <div class="updated">
        <p><?php _e( 'Updated!', 'my-text-domain' ); ?></p>
    </div>
    <?php
}


function sn_scoutnet_api_rent_html_page() {

?>

<script type="text/javascript">
var templateDir = "<?php echo SN_API_PLUGIN_URL; ?>";
</script>

<div>
<h2>Lokalenverhuur</h2>

<?php 

	$call = sn_getRents();
	if ($call['decoded']['head']['status']==1){

		$rents = $call['decoded']['body']['data'];
			echo "<div id=\"sn_lokalenverhuur_overview\">\n";
			
			if (count($rents)==1){
			
				$rentid = $rents[0]['id'];
			
			}elseif(count($rents)==0){
			
				echo "<div class=\"error\">Volgens de groepinfo wordt jullie lokaal niet verhuurd.</div>";
			
			}else{
			
				foreach ($rents as $key => $rent){
				echo "<a href=\"?page=scoutnet-api-rent&rentid={$rent['id']}\">{$rent['name']}</a> ";
				}
				if (isset($_GET['rentid'])){
						$rentid = intval($_GET['rentid']);
				}
			
			
			}
			
			if (isset($rentid)){
			

if (isset($_POST['sn_submit'])){

$lat="";$lng="";
$args = array();

if (isset($_POST['name']))$args['name']=trim($_POST['name']);
if (isset($_POST['cjtid']))$args['cjtid']=trim($_POST['cjtid']);
if (isset($_POST['street']))$args['street'] = trim($_POST['street']);
if (isset($_POST['gem']))$args['gem'] = trim($_POST['gem']);
if (isset($_POST['mail']))$args['mail'] = trim($_POST['mail']);
if (isset($_POST['tel']))$args['tel'] = trim($_POST['tel']);
if (isset($_POST['link']))$args['link'] = trim($_POST['link']);
if (isset($_POST['nmax']))$args['nmax'] = trim($_POST['nmax']);
if (isset($_POST['promo']))$args['promo'] = trim($_POST['promo']);

if (isset($_POST['lat'])){$lat = trim($_POST['lat']);}
if (isset($_POST['lng'])){$lng = trim($_POST['lng']);}
if ($lat!="" && $lng!=""){$args['latlng']=$lat.':'.$lng;}

if (isset($_POST['geocode'])){$args['geocode']='y';}else{$args['geocode']='n';}
if (isset($_POST['cal'])){$args['cal']='y';}else{$args['cal']='n';}
if (isset($_POST['attest'])){$args['attest']='y';}else{$args['attest']='n';}
if (isset($_POST['camp'])){$args['camp']='y';}else{$args['camp']='n';}
if (isset($_POST['weekend'])){$args['weekend']='y';}else{$args['weekend']='n';}
if (isset($_POST['party'])){$args['party']='y';}else{$args['party']='n';}

$args['alert']="mailto:jorisp@scoutnet.be";
$args['http_host']=$_POST['http_host'];
$args['auth_user']=$_POST['auth_user'];


$call = sn_updateRent($rentid,$args);

$warnings = $call['decoded']['head']['warning'];

	if ($call['decoded']['head']['status']==1){
	echo "<div class=\"updated settings-error\"><p><strong>Lokaalgegevens zijn aangepast.</strong></p></div>";  // update-nag
	
	if (count($warnings)>0){
		echo "<div class=\"update-nag settings-error\">";
		foreach ($warnings as $key => $value){
		echo "<p>$value [$key]</p>";
		}
		echo "</div>";
		}
	
	
	}else{
	echo "<div class=\"error settings-error\"><p><strong>Aanpassing mislukt</strong></p></div>";
	}
	

}
			
			
$call = sn_getRent($rentid);


	if ($call['decoded']['head']['status']==1){
		$lokaal = $call['decoded']['body']['data'];
			//$content .= "{$lokaal['name']} {$lokaal['promo']}<br /><br /><br />";
			//$content .= print_r($lokaal,true);
					

$tmp = explode(':',$lokaal['latlng']);
$lokaal['lat']=$tmp[0];
$lokaal['lng']=$tmp[1];
unset($tmp);
$auth_user = get_bloginfo('admin_email');
					
if ($lokaal['weekend']=='y'){$weekends_check=" checked='checked'";}else{$weekends_check='';}
if ($lokaal['camp']=='y'){$kampen_check=" checked='checked'";}else{$kampen_check='';}
if ($lokaal['party']=='y'){$feestjes_check=" checked='checked'";}else{$feestjes_check='';}
if ($lokaal['cal']=='y'){$cal_check=" checked='checked'";}else{$cal_check='';}
if ($lokaal['attest']=='y'){$attest_check=" checked='checked'";}else{$attest_check='';}

echo "<form method=\"post\" action=\"\">";
wp_nonce_field('update-options');
echo "<input type=\"hidden\" name=\"http_host\" value=\"{$_SERVER['HTTP_HOST']}\" />";
echo "<input type=\"hidden\" name=\"auth_user\" value=\"{$auth_user}\" />";

echo "<table border='0'>";

echo "<tr><td>lokaal <img src='".SN_API_PLUGIN_URL."/img/warning.gif' width='18' height='16' title='Changing the name also changes the url already indexed by google and other search engines. [{$lokaal['url']}]' alt='warning' /></td><td>";
echo "<label title='Geef een unieke naam voor elk van de huurbare lokalen.'><input type='text' name='name' value='{$lokaal['name']}' maxlength='30' size='25' /></label>";
echo "</td></tr>\n";

echo "<tr><td>CJT ID</td><td>";
echo "<label title='CJT ID' for='cjtid'><input type='text' id='cjtid' size='5' name='cjtid' value='{$lokaal['cjtid']}' /></label>";
if ($lokaal['cjtid']>0){ 
echo " <a href='http://www.cjt.be/boekingscentrale/fiche.aspx?nr={$lokaal['cjtid']}' target='_blank'><img src='".SN_API_PLUGIN_URL."/img/arrow_right.gif' width='6' height='6' border='0' title='CJT verblijfsfiche' alt='' /></a>";
}
echo "</td><td rowspan='10'>";
echo "<div id=\"map\" style=\"width: 280px; height: 300px\"></div>\n";
echo "</td></tr>\n";

echo "<tr><td>weekends</td><td>";
echo "<label title='lokaal wordt verhuurd voor weekends' for='weekend'><input type='checkbox' id='weekend' name='weekend' value=''$weekends_check /></label>";
echo "</td></tr>\n";

echo "<tr><td>kampen</td><td>";
echo "<label title='lokal wordt verhuurd voor kampen' for='camp'><input type='checkbox' id='camp' name='camp' value=''$kampen_check /></label>";
echo "</td></tr>\n";

echo "<tr><td>feestjes</td><td>";
echo "<label title='lokaal wordt verhuurd voor feestjes / geen overnachting' for='party'><input type='checkbox' id='party' name='party' value=''$feestjes_check /></label>";
echo "</td></tr>\n";

echo "<tr><td>Straat</td><td>";
echo "<label title='straatnaam en huisnummer' for='street'><input type='text' id='street' size='25' maxlength='100' name='street' value=\"{$lokaal['street']}\" /></label> <a href=''><img src='".SN_API_PLUGIN_URL."/img/arrow_right.gif' width='6' height='6' onclick='showAddress(); return false' style='cursor: pointer; cursor: hand;' title='lookup googlemap' alt='lookup' /></a>";
echo "</td></tr>\n";

echo "<tr><td>Gemeente</td><td>";
echo "<label title='postcode en gemeente' for='gem'><input type='text' id='gem' size='25' maxlength='70' name='gem' value=\"{$lokaal['postcode']} {$lokaal['city']}\" /></label>";
echo "</td></tr>\n";

echo "<tr><td>email</td><td>";
echo "<label title='emailadres van de verhuurverantwoordelijke' for='mail'><input type='text' id='mail' size='25' maxlength='50' name='mail' value='{$lokaal['mail']}' /></label>";
echo "</td></tr>\n";

echo "<tr><td>tel</td><td>";
echo "<label title='telefoonnumer: +32.xxxxxxxx' for='tel'><input type='text' id='tel' name='tel' size='25' maxlength='25' value='{$lokaal['tel']}' /></label>\n";
echo "</td></tr>\n";

echo "<tr><td>lat</td><td>";
echo "<label title='Deze geo-coordinaten kan je invullen door op het kaartje te klikken' for='lat'><input type='text' id='lat' size='25' name='lat' value='{$lokaal['lat']}' maxlength='17' readonly='readonly' /></label>\n";
echo "</td></tr>\n";

echo "<tr><td>lng</td><td>";
echo "<label title='Deze geo-coordinaten kan je invullen door op het kaartje te klikken' for='lng'><input type='text' id='lng' size='25' name='lng' value='{$lokaal['lng']}' maxlength='17' readonly='readonly' /></label>\n";
echo "</td></tr>\n";

echo "<tr><td>link</td><td colspan='2'>";
echo "<label title='Link naar de lokalen info pagina op je website.' for='link'><input type='text' id='link' name='link' size='42' maxlength='254' value='{$lokaal['link']}' /></label>\n <small>[url lokalen verhuur]</small>";
echo "</td></tr>\n";

echo "<tr><td>Kalender</td><td colspan='2'>";
echo "<label title='Toon de verhuurkalender op de publieke website.' for='cal'><input type='checkbox' id='cal' name='cal'$cal_check /></label> <small>[Toon de verhuurkalender op de <a href='http://www.lokalenverhuur.be/lokalen/fiche/{$mygroup['orgurl']}/{$mygroup['accountname']}/{$lokaal['url']}/' target='_blank'>publieke website</a>]</small>";
echo "</td></tr>\n";

echo "<tr><td>Attest</td><td colspan='2'>";
echo "<label title='Het lokaal beschikt over een brandveiligheids attest.' for='attest'><input type='checkbox' id='attest' name='attest'$attest_check /></label> <small>[Het lokaal beschikt over een brandveiligheids attest]</small>";
echo "</td></tr>\n";

echo "<tr><td>Max aantal</td><td colspan='2'>";
echo "<label title='Het maximaal aantal personen' for='nmax'><input type='text' id='nmax' name='nmax' size='3' maxlength='3' value='{$lokaal['nmax']}' /></label> <small>[Capaciteit van het lokaal]</small>";
echo "</td></tr>\n";

echo "<tr><td>Opmerking</td><td colspan='2'>";
echo "<label title='Promoot je lokaal in enkele zinnen' for='promo'><textarea id='promo' name='promo' rows='5' cols='58'>".$lokaal['promo']."</textarea></label><br /><small>[Promoot je lokaal in enkele zinnen]</small>";
echo "</td></tr>\n";

echo "</table>\n";

echo "<br /><br /><input type='submit' name='sn_submit' class='button button-primary' value='Save Changes' /> <small>[opgelet: wijzigingen zijn omwille van \"caching\" niet steeds meteen zichtbaar op de publieke sites]</small><br /><br />\n";
echo "</form>\n";			

echo "<br /><br /><h1>Verhuurkalender :</h1>";

require_once('Spinternet/Api.php');
try {
	$spinternet = Spinternet_Api::getInstance();
	$rentalCalendar = $spinternet->getRentalCalendar($lokaal['mykey']);
	echo $rentalCalendar->getMonthUpdater();
} catch (Exception $ex) {
	//printf('<div class="error">%s</div>', $ex->__toString());
	echo "<div class=\"error\">De verhuurkalender heeft een eigen API. Contacteer ons en we helpen je deze te installeren.</div>";
}
					
			
				}else{
				echo "<div class=\"error\">{$call['decoded']['head']['error_message']}<br /><br />Contacteer info@scoutnet.be</div>";
				}
			
			}
			
			echo "</div>\n";
			
		//$content .= print_r($rents,true);
	}else{
		echo "<div class=\"error\">{$call['decoded']['head']['error_message']}<br /><br />Is de secret key correct? Contacteer info@scoutnet.be</div>";
	}
	
	

?>




</div>


<?php 
}
?>