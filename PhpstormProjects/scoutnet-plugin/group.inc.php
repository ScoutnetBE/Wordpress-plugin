<?php

function sn_scoutnet_api_group_html_page() {

$zeestr='';$zee='n';
$akabestr='';$akabe='n';
$verhuurstr='';$verhuurstrY='';$verhuurstrN='';$verhuur='-';
$hasaddressstr='';$hasaddress='n';
$streetstr='';$latstr='';$lngstr='';$verhuurstr2='';
$str_urllist='';$str_oldurllist='';

$devkey = "jorisp@scoutnet.be";
$options = get_option('sn_scoutnet_api');
$secret = $options['apigroupkey'];
$accountid = $options['accountid'];
$appkey = substr($secret,0,6);

$dir = plugin_dir_path( __FILE__ );
//print_r($_POST);
?>

<script type="text/javascript">
var templateDir = "<?php echo SN_API_PLUGIN_URL; ?>";
</script>

<?php 
if (isset($_POST['sn3_submit_old'])){


try{
$apicall = new Scoutnet_API_Call('group',$devkey, $appkey, $secret, false);
$method = 'GET';
$endpoint = 'sections/'.get_option('sn_accountid').'/?filter=name:name_url';

$call = $apicall->run($endpoint, $method, null);

//echo "ANSWER:<br />";
//var_dump($call);

if ($call['decoded']['head']['status']==1){
$sections = $call['decoded']['body']['data'];

foreach ($sections as $section){
echo $section['name'];
}

}else{
echo "<div class=\"error\">{$call['decoded']['head']['error_message']}<br /><br />Is de secret key correct? Contacteer info@scoutnet.be</div>";
}

}catch(Exception $e)
{echo '<div class="error">' .$e->getMessage().'</div>';}



function my_register_navs() {
    register_nav_menus(
        array( 'takken-menu' => __( 'Takken Menu' )
        
        )
    );
}
add_action( 'init', 'my_register_navs' );

/*
$locations = get_nav_menu_locations();
var_dump($locations);

    $menu_name = 'Hoofdmenu';

    if ( ( $locations = get_nav_menu_locations() ) && isset( $locations[ $menu_name ] ) ) {
    
    
	$menu = wp_get_nav_menu_object( $locations[ $menu_name ] );

	$menu_items = wp_get_nav_menu_items($menu->term_id);

	$menu_list = '<ul id="menu-' . $menu_name . '">';

	foreach ( (array) $menu_items as $key => $menu_item ) {
	    $title = $menu_item->title;
	    $url = $menu_item->url;
	    $menu_list .= '<li><a href="' . $url . '">' . $title . '</a></li>';
	}
	$menu_list .= '</ul>';
    } else {
	$menu_list = '<ul><li>Menu "' . $menu_name . '" not defined.</li></ul>';
    }
    // $menu_list now ready to output


if ( !is_nav_menu( 'Takken Menu' )) {
    $menu_id = wp_create_nav_menu( 'Takken Menu' );
    $menu = array( 'menu-item-type' => 'custom', 'menu-item-url' => get_home_url('/'),'menu-item-title' => 'Home', 'menu-item-status' => 'publish' );
    wp_update_nav_menu_item( $menu_id, 0, $menu );
}
*/



// Create the menus
$tak_menu = array(
    'menu-name'     => 'Takken Menu'
    , 'description' => 'A navigation menu for this website'
);
$takken_menu = wp_update_nav_menu_object( $menu_id, $tak_menu );

//var_dump($takken_menu);

// Set the menus to appear in the proper theme locations  ???
$locations = get_theme_mod('nav_menu_locations');

//var_dump($locations);

$locations['header-menu'] = $takken_menu;
set_theme_mod('nav_menu_locations', $locations);

//var_dump($locations);

// todo categoriën aanmaken


// todo pages /takken/welpen/ aanmaken

$parent_page = get_page_by_title( 'Takken' );

//if ( is_page($parent_page->ID) ){
$parent_ID = $parent_page->ID;

foreach ($sections as $section){
$page_groep_title = $section['name'];
$new_page_template = 'takken-section-template.php';	

    $page_groepen = get_page_by_title( $page_groep_title );
    
    //wp_delete_post($page_groepen->ID);

    if ( ! $page_groepen ) {

        // Create post object
        $_p = array();
        $_p['post_title'] = $page_groep_title;
        $_p['post_content'] = "Vul hier aan. (info over de leiding en activiteiten wordt automatisch toegevoegd)";
		$_p['post_parent'] = $parent_ID;
        $_p['post_status'] = 'publish';
        $_p['post_type'] = 'page';
        $_p['comment_status'] = 'closed';
        $_p['ping_status'] = 'closed';
        $_p['post_category'] = array(1); // the default 'Uncategorised'
        //$_p['page_template'] = 'Takken'; //?

        // Insert the post into the database
        $page_groep_id = wp_insert_post( $_p );
        
        
		if(!empty($new_page_template)){
			update_post_meta($page_groep_id, '_wp_page_template', $new_page_template);
		}
        
        
        
        // Build menu item
		$menu_item = array(
    		'menu-item-object-id' => $page_groep_id
    		, 'menu-item-parent-id' => 0
    		, 'menu-item-position' => $menu_order
    		, 'menu-item-object' => 'page'
    		, 'menu-item-url' => 'sdfsfs'
    		, 'menu-item-type' => 'post_type'
    		, 'menu-item-status' => 'publish'
    		, 'menu-item-title' => $page_groep_title
		);

		// Add to nav menu
		wp_update_nav_menu_item( $takken_menu, 0, $menu_item );
        
        

    }
    else {
        // the plugin may have been previously active and the page may just be trashed...

        $page_groep_id = $page_groepen->ID;

        //make sure the page is not trashed...
        $page_groepen->post_content = 'nieuwe content';
        $page_groepen->post_status = 'publish';
        //$page_groepen->page_template = 'Takken'; //?
        $page_groep_id = wp_update_post( $page_groepen );

    }

}
//}




}



//print_r(get_registered_nav_menus());

?>

<div>

<h2>Onze groep</h2>

<?php
try{
$apicall = new Scoutnet_API_Call('group',$devkey, $appkey, $secret, false);

if (isset($_POST['sn_submit'])){
//echo "POST:<br />";
//print_r($_POST);

$method = "PUT";
$endpoint = "lists/".$options['accountid'];

$lat="";$lng="";
$args = array();

if (isset($_POST['groupname']))$args['groupname']=trim($_POST['groupname']);
if (isset($_POST['groupname2']))$args['groupname2']=trim($_POST['groupname2']);
if (isset($_POST['promo']))$args['promo'] = trim($_POST['promo']);
if (isset($_POST['street']))$args['street'] = trim($_POST['street']);
if (isset($_POST['gem']))$args['gem'] = trim($_POST['gem']);
if (isset($_POST['mail']))$args['mail'] = trim($_POST['mail']);
if (isset($_POST['tel']))$args['tel'] = trim($_POST['tel']);
if (isset($_POST['urllist']))$args['urllist'] = trim($_POST['urllist']);
if (isset($_POST['lat']))$lat = trim($_POST['lat']);
if (isset($_POST['lng']))$lng = trim($_POST['lng']);
if ($lat!="" && $lng!=""){$args['latlng']=$lat.':'.$lng;}

if (isset($_POST['hasaddress'])){$args['hasaddress']='y';}else{$args['hasaddress']='n';}
if (isset($_POST['zee'])){$args['zee']='y';}else{$args['zee']='n';}
if (isset($_POST['akabe'])){$args['akabe']='y';}else{$args['akabe']='n';}
if (isset($_POST['verhuur'])){$args['verhuur']=$_POST['verhuur'];}
if (isset($_POST['waar'])){$args['waar']=trim($_POST['waar']);}
if (isset($_POST['wanneer'])){$args['wanneer']=trim($_POST['wanneer']);}
if (isset($_POST['lidgeld'])){$args['lidgeld']=trim($_POST['lidgeld']);}
if (isset($_POST['groupID2'])){$args['groupID']=substr(trim($_POST['groupID1']),0,3).substr(trim($_POST['groupID2']),0,3);}

$args['alert']="mailto:jorisp@scoutnet.be";
$args['http_host']=$_POST['http_host'];
$args['auth_user']=$_POST['auth_user'];
 
try{
$call = $apicall->run($endpoint, $method, $args);
}catch(Exception $e)
{echo '<div class="error">' .$e->getMessage().'</div>';}

}

$args = array();
$method = 'GET';
$endpoint = 'lists/'.$options['accountid'];

if (intval($options['accountid'])>0){

$call = $apicall->run($endpoint, $method, $args);
//var_dump($call);

// TODO: telkens deze pagina opgevraagd wordt dan gaan we de wordpress option variabele sn_scoutnet_group aanpassen.
 // update_option( $option, $new_value ); 

if ($call['decoded']['head']['status']==1){

$group = $call['decoded']['body']['data'];

foreach ($group['extra'] as $v){

switch (key($v)){
case 'zee':
	if ($v[key($v)]=='y'){$zeestr=" checked=\"checked\"";$zee='y';}
	break;
case 'akabe':
	if ($v[key($v)]=='y'){$akabestr=" checked=\"checked\"";$akabe='y';}
	break;
case 'verhuur':
	if ($v[key($v)]=='y'){$verhuurstrY=" checked=\"checked\"";$verhuur='y';}
	if ($v[key($v)]=='n'){$verhuurstrN=" checked=\"checked\"";$verhuur='n';}
	if ($v[key($v)]=='-'){$verhuur='-';}
	break;
case 'promo':
	$group['promo']=$v[key($v)];
}
}
if ($group['hasaddress']=='y'){$hasaddressstr=" checked=\"checked\"";$hasaddress='y';}else{$streetstr=" disabled=\"disabled\"";$latstr=" disabled=\"disabled\"";$lngstr=" disabled=\"disabled\"";$verhuurstr2=" disabled=\"disabled\"";}

$group['gem'] = $group['postcode'].' '. $group['city'];
$groupID1=substr($group['groupID'],0,3);
$groupID2=substr($group['groupID'],3,3);
$tmp = explode(':',$group['latlng']);
$group['lat']=$tmp[0];
$group['lng']=$tmp[1];
unset($tmp);


if ($verhuur=='y'){
// TODO GET rent request om lijst op te halen van al de te huren lokalen

$lokalen = array();
$method = 'GET';
$endpoint = 'rent/';

try{
$rent = $apicall->run($endpoint, $method, null);

if ($rent['decoded']['head']['status']==1){
$lokalen = $rent['decoded']['body']['data'];
//var_dump($lokalen);
}else{
echo "<div class=\"error\">{$call['decoded']['head']['error_message']}<br /><br />Is de secret key correct? Contacteer info@scoutnet.be</div>";
}

}catch(Exception $e)
{echo '<div class="error">' .$e->getMessage().'</div>';}

}
//print_r($_SERVER);
?>

<form method="post" action="">
<?php wp_nonce_field('update-options'); ?>
<input type="hidden" name="http_host" value='<?php echo $_SERVER['HTTP_HOST']?>' />
<input type="hidden" name="auth_user" value='<?php bloginfo('admin_email');?>' />
<input type="hidden" name="orgid" value="1" />
<input type="hidden" name="groupID1" value='<?php echo $groupID1;?>' />
<input type="text" name="eenheid" value="Scouts en Gidsen Vlaanderen" maxlength="50" style="width:400px;" readonly="readonly" /><br />
<input type="text" name="groupname" value="<?php echo $group['groupname'];?>" maxlength="50" style="width:400px;" /> <small>[officieel]</small><br />
<input type="text" name="groupname2" value="<?php echo $group['groupname2'];?>" maxlength="50" style="width:400px;" /> <small>[alternatieve benaming]</small><br />
<textarea name='promo' style='width:400px;height:80px;'><?php echo $group['promo'];?></textarea> <small>[promo tekstje]</small><br /><br />
<b><?php echo $group['section'];?></b> <?php echo $groupID1;?> <input type='text' name='groupID2' value='<?php echo $groupID2;?>' maxlength='3' size='5' />&nbsp;<small>[zeescouts <input type='checkbox' name='zee'<?php echo $zeestr;?> />]</small> <small>[akabe <input type='checkbox' name='akabe'<?php echo $akabestr;?> />]</small><br /><br />

<table><tr><td><input type='checkbox' name='hasaddress' value='<?php echo $hasaddress;?>' title='uncheck if no address'<?php echo $hasaddressstr;?> onclick="if(this.checked==true){this.form.street.disabled=false;this.form.lat.disabled=false;this.form.lng.disabled=false;this.form.verhuur[0].disabled=false;this.form.verhuur[1].disabled=false;}else{this.form.street.disabled=true;this.form.lat.disabled=true;this.form.lng.disabled=true;this.form.verhuur[0].disabled=true;this.form.verhuur[1].disabled=true;}"> address: <img src='<?php echo SN_API_PLUGIN_URL;?>img/arrow_right.gif' width='6' height='6' onclick='showAddress(); return false' style='cursor: pointer; cursor: hand;' title='geocode: put address on map'><br />
<label title="Klik op het zwarte pijlje om de locatie op te zoeken aan de hand van adres en postcode." for="street"><textarea name='street' id='street' rows='2' style='width:300px;height=50px;' ><?php echo $group['street'];?></textarea></label><br />
<input type='text' id='gem' name='gem' value="<?php echo $group['gem'];?>" maxlength='100' autocomplete='off' size='42' style='width:300px'  title='geef de postcode en gemeente' /><br /><br />
public email: <br /><input type='text' name='mail' value='<?php echo $group['mail'];?>' maxlength='50' /><br />
public tel: <br /><input type='text' name='tel' value='<?php echo $group['tel'];?>' maxlength='25' /><br />
public urls: <br /><label title="Geef alle urls die je wilt opnemen op je groepsfiche. Gebruik ';' als scheidingsteken." for="urllist"><input type="text" name="urllist" value="<?php echo $group['urllist'];?>" maxlength="510" /></label><br /><br />
coordinaten: <small>[scroll to zoom and left mouse click]</small><br /><label title="De geo-coordinaten kan je ingeven door op de kaart te klikken." for="lat"><input type='text' id='lat' name='lat' value='<?php echo $group['lat'];?>' maxlength='18'<?php echo $latstr;?> /></label> latitude<br />
<label title="De geo-coordinaten kan je ingeven door op de kaart te klikken." for="lng"><input type='text' id='lng' name='lng' value='<?php echo $group['lng'];?>' maxlength='18'<?php echo $lngstr;?> /></label> longitude<br />
<br />
</td><td><div id="map" style="width: 500px; height: 350px"></div></td><tr/></table>


<fieldset style="width: 250px; padding: 15px; border: solid 1px black; "><legend style="color: black; font-weight: bold;">
Wij verhuren onze lokalen: <a href="http://www.lokalenverhuur.be" target="_blank"><img src='<?php echo SN_API_PLUGIN_URL;?>img/arrow_right.gif' width='6' height='6' border='0' style='cursor: pointer; cursor: hand;' title='www.lokalenverhuur.be' /></a></legend>
&nbsp;&nbsp;YES <input type='radio' name='verhuur' value='y'<?php echo $verhuurstrY;?><?php echo $verhuurstr2;?> />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;NO <input type='radio' name='verhuur' value='n'<?php echo $verhuurstrN;?><?php echo $verhuurstr2;?> /><br />
<?php 
if (count($lokalen)>0){
foreach($lokalen as $lokaal){
//echo " <a href=\"http://www.lokalenverhuur.be/lokalen/fiche/scouts-en-gidsen-vlaanderen/{$group['accountname']}/{$lokaal['name_url']}/\" target=\"_blank\">{$lokaal['name']}</a><br />\n";
echo " <a href=\"?page=scoutnet-api-rent&rentid={$lokaal['id']}\">{$lokaal['name']}</a><br />\n";
}
}
?>
</fieldset>

<br /><br /><b>Meer info over de groep:</b> <br /><br />
<?php

foreach ($group['extra'] as $v){
switch (key($v)){
case 'waar':
	echo "waar ? <br /><textarea name='waar' style='width:500px;height:100px;'>{$v[key($v)]}</textarea> <small>[extra info over waar de activiteiten doorgaan]</small><br /><br />\n";
	break;
case 'wanneer':
	echo "wanneer ? <br /><textarea name='wanneer' style='width:500px;height:100px;'>{$v[key($v)]}</textarea> <small>[geef aan wanneer de werking plaats heeft]</small><br /><br />\n";
	break;
case 'lidgeld':
	echo "lidgeld ? <br /><textarea name='lidgeld' style='width:500px;height:100px;'>{$v[key($v)]}</textarea> <small>[verduidelijk waarvoor er lidgeld gevraagd wordt]</small><br /><br />\n";
	break;
}
}

if (isset($group['sections'])){

	echo "takwerking ? <img id=\"state9003\" width=\"9\" height=\"9\" onclick=\"exp(9003)\" name=\"state9003\" src=\"".SN_API_PLUGIN_URL."img/plus.gif\"><br />";
	echo "<span id=\"item9003\" style=\"display:none;\">";
	foreach ($group['sections'] as $s){
	echo ' '.$s['section_name'].'<br />';
	}
	echo "<br />Beheer van de takwerking (toevoegen, verwijderen van de actieve takken) doe je voorlopig via <a href=\"https://my.scoutnet.be/?config\" target=\"_blank\">https://my.scoutnet.be/?config</a> (my-sections) </span><br /><br />\n";
}

?>

<input type="submit" name="sn_submit" class="button button-primary" value="Verzenden" />
</form>

<br /><br />Deze gegevens worden getoond op <a href="http://www.scoutnet.be/adressen/scouts-en-gidsen-vlaanderen/<?php echo $group['accountname'];?>/?map" target="_blank">www.scoutnet.be/adressen</a> en <a href="http://www.spinternet.be/adressen/jeugd/scouts-en-gidsen-vlaanderen/<?php echo $group['accountname'];?>/?map" target="_blank">www.spinternet.be/adressen</a>

<?php

update_option( 'sn_scoutnet_group', $group ); 
 
}else{
echo "<div class=\"error\">{$call['decoded']['head']['error_message']}<br /><br />Is de secret key correct? Contacteer info@scoutnet.be</div>";
}

}else{
echo "<div class=\"error\">Invalid accountid ! Contacteer info@scoutnet.be</div>";
}

}catch(Exception $e)
{echo '<div class="error">' .$e->getMessage().'</div>';}

?>

</div>


<?php
}