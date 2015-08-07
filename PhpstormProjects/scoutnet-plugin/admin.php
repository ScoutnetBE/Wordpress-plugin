<?php

/* Call the html code */
//add_action('admin_menu', 'sn_scoutnet_api_admin_menu');
/*
function sn_scoutnet_api_admin_menu() {
//global $sn_api_settings_page;
global $sn_api_group_settings_page;

//$sn_api_settings_page = add_options_page('Scoutnet API', 'Scoutnet API', 'administrator', 'scoutnet-api', 'sn_scoutnet_api_html_page');
$sn_api_group_settings_page = add_options_page('Scoutnet API group', 'Scoutnet API group', 'administrator', 'scoutnet-api-group', 'sn_scoutnet_api_group_html_page');

}


option sn_scoutnet_api -> info ivm. API + (?)enkele gegevens over de groep(?)
option sn_scoutnet_group -> beknopte info ivm. groep (info die niet dikwijls gaat wijzigen)

*/

function sn_api_load_js_and_css($hook) {

//global $sn_api_settings_page;
global $sn_api_group_settings_page;
global $sn_api_rent_settings_page;
global $sn_api_members_settings_page;
 
	//if( ( $hook == $sn_api_settings_page ) || ( $hook == $sn_api_group_settings_page ) || ( $hook == $sn_api_rent_settings_page )) {
	if( ( $hook == $sn_api_group_settings_page ) || ( $hook == $sn_api_rent_settings_page )) {

	//wp_enqueue_script('jquery');
	wp_enqueue_script('google-maps', 'https://maps.googleapis.com/maps/api/js?key=AIzaSyCwvKXGLUemghNxHVuYoiH8wEkoFfbVSgs&amp;sensor=false');
	wp_register_script('group.js', SN_API_PLUGIN_URL . 'js/group.js', array( 'jquery', 'jquery-ui-core', 'jquery-ui-autocomplete' ) );
	wp_enqueue_script('group.js');

	}else if ( $hook == $sn_api_members_settings_page ) {
	
	wp_enqueue_style('plugin_name-admin-ui-css', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/black-tie/jquery-ui.css');
	wp_register_script('members.js', SN_API_PLUGIN_URL . 'js/members.js', array( 'jquery', 'jquery-ui-core', 'jquery-ui-autocomplete' ) );
	wp_enqueue_script('members.js');
	
	wp_register_script('json.js', SN_API_PLUGIN_URL . 'js/jquery.json-2.4.min.js' );
	wp_enqueue_script('json.js');
	wp_register_script('tablesorter.js', SN_API_PLUGIN_URL . 'js/jquery.tablesorter.min.js' );
	wp_enqueue_script('tablesorter.js');
	wp_register_script('tablesorter.pager.js', SN_API_PLUGIN_URL . 'js/jquery.tablesorter.pager.js' );
	wp_enqueue_script('tablesorter.pager.js');
	
	wp_register_style('members', SN_API_PLUGIN_URL . 'css/members.css', array(), '20140104', 'all' );
	wp_enqueue_style('members');
	
/*
<link type="text/css" rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/black-tie/jquery-ui.css" />
<script type="text/javascript" src="js/jquery-1.7.2.js"></script>
<script type="text/javascript" src="js/jquery.json-2.4.min.js"></script>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/jquery-ui.min.js"></script>
<script type="text/javascript" src="js/jquery.tablesorter.min.js"></script>
<script type="text/javascript" src="js/jquery.tablesorter.pager.js"></script>
*/
	
	}else{
	
		return;
	}

}


function register_my_session() {
if( !session_id() ){ session_start();}
}

add_action('init', 'register_my_session');

add_action( 'admin_enqueue_scripts', 'sn_api_load_js_and_css' );

require_once dirname( __FILE__ ) . '/group.inc.php';
require_once dirname( __FILE__ ) . '/rent.inc.php';
require_once dirname( __FILE__ ) . '/members.inc.php';


$my_settings_page = new MySettingsPage();


class MySettingsPage {
	/**
	* Holds the values to be used in the fields callbacks
	*/
	private $options;
	private $pages;

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'page_init' ) );
    }

    /**
     * Add options page
     */
    public function add_plugin_page() {
    
    global $sn_api_group_settings_page;
    global $sn_api_rent_settings_page;
    global $sn_api_members_settings_page;
    
    //$sn_api_settings_page = add_options_page('Scoutnet API', 'Scoutnet API', 'administrator', 'scoutnet-api', 'sn_scoutnet_api_html_page');
        // This page will be under "Settings"
		add_options_page('Scoutnet API', 'Scoutnet API', 'administrator', 'scoutnet-api', array( $this, 'create_admin_page' ) ); // manage_options
		$sn_api_group_settings_page = add_options_page('Scoutnet groep', 'Scoutnet groep', 'administrator', 'scoutnet-api-group', 'sn_scoutnet_api_group_html_page');
		$sn_api_rent_settings_page = add_options_page('Scoutnet verhuur', 'Scoutnet verhuur', 'administrator', 'scoutnet-api-rent', 'sn_scoutnet_api_rent_html_page');
		$sn_api_members_settings_page = add_options_page('Scoutnet leden', 'Scoutnet leden', 'administrator', 'scoutnet-api-members', 'sn_scoutnet_api_members_html_page');
    }

	/**
	 * Options page callback
	 */
	public function create_admin_page() {

	
		// Set class property
		//$this->options = delete_option( 'my_option_name' );
		$this->options = get_option( 'sn_scoutnet_api' );
		$this->pages = get_option( 'sn_scoutnet_pages' );
		
		
		//print_r($this->options);
		
		// call get api
		
		// use caching NOT on admin site ONLY on frontend, take values from database or api
		
		// how to cron ?
		
		?>
		<div class="wrap">
			<?php //get_screen_icon(); ?>
			<h2>Scoutnet API Settings</h2>
			<?php //settings_errors(); ?>
			
			<?php
			$active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'settings';
			?>
         
	<h2 class="nav-tab-wrapper">
		<a href="?page=scoutnet-api&tab=settings" class="nav-tab <?php echo $active_tab == 'settings' ? 'nav-tab-active' : ''; ?>">Settings</a>
		<a href="?page=scoutnet-api&tab=shortcodes" class="nav-tab <?php echo $active_tab == 'shortcodes' ? 'nav-tab-active' : ''; ?>">Shortcodes</a>
		<a href="?page=scoutnet-api&tab=help" class="nav-tab <?php echo $active_tab == 'help' ? 'nav-tab-active' : ''; ?>">Help</a>
	</h2>
          
			<?php
			if( $active_tab == 'settings' ) {
			
			$site_url = get_site_url();
			
			?>
			<form method="post" action="options.php">
			<?php 
	            settings_fields( 'my_option_group' ); // This prints out all hidden setting fields
    	        do_settings_sections( 'scoutnet-api' );
    	        submit_button();
    	    ?>
			</form>
			De API keys vind je op het Scoutnet control panel https://my.scoutnet.be/?api<br /><br />
			<form method="post" action="">
			
			<?php if ($this->options['accountid']){?>
			
			<?php if ($this->options['apigroupkey']){?>
			
			<?php
			
			if (is_admin()){
			
			if (isset($_POST['group'])){
			
			   $page = get_page_by_title( 'Onze groep' );
			   
				if ( isset($page) ) {
    			
					echo "<div class=\"error settings-error\"><p>De pagina <a href=\"{$site_url}/onze-groep/\">Onze groep</a> bestaat reeds</p></div>";
        		
    			} else {
    				$_p = array();
    				$_p['post_name'] = 'onze-groep';
    				$_p['post_title'] = 'Onze groep';
					$_p['post_content'] = "De inhoud van deze pagina wordt automatisch aangemaakt. Je dient deze dus niet te editeren.<br /><br />De Scoutnet API secret key moet ingevuld zijn. Zie Instellingen -> Scoutnet API group";
					$_p['page_template'] = 'onze-groep-template.php';
    				
    				$this->make_my_page($_p);
					echo "<div class=\"updated settings-error\"><p><strong>De pagina <a href=\"{$site_url}/onze-groep/\">Onze groep</a> is aangemaakt.</strong></p></div>";
					
    			}
			}
			
			if (((isset($_POST['groupdel']))) && (current_user_can('delete_posts'))){
			
					
				$page_id = $this->pages['onze-groep']['id'];
		
					if( $page_id ) {
						wp_delete_post( $page_id, true ); // this will trash, not delete. To also empty the trash use : wp_delete_post ($page_id, true)
						//unset($this->pages['onze-groep']);
						update_option('sn_scoutnet_pages', $this->pages);
						echo "<div class=\"updated settings-error\"><p><strong>De pagina onze-groep is verwijderd.</strong></p></div>";
					}
			
			}
			
			if (isset($_POST['rent'])){
			
			   $page = get_page_by_title( 'Lokalenverhuur' );
			   
				if ( isset($page) ) {
    			
					echo "<div class=\"error settings-error\"><p><strong>De pagina <a href=\"{$site_url}/lokalenverhuur/\">Lokalenverhuur</a> bestaat reeds</strong></p></div>";
        		
    			} else {
    				$_p = array();
    				$_p['post_name'] = 'lokalenverhuur';
    				$_p['post_title'] = 'Lokalenverhuur';
					$_p['post_content'] = "De inhoud van deze pagina wordt automatisch aangemaakt. Je dient deze dus niet te editeren.<br /><br />De Scoutnet API secret key moet ingevuld zijn. Zie Instellingen -> Scoutnet API group";
					$_p['page_template'] = 'lokalenverhuur-template.php';
    				
    				$this->make_my_page($_p);
					echo "<div class=\"updated settings-error\"><p><strong>De pagina <a href=\"{$site_url}/lokalenverhuur/\">Lokalenverhuur</a> is aangemaakt.</strong></p></div>";
					
    			}
			}
			
			if (((isset($_POST['rentdel']))) && (current_user_can('delete_posts'))){
			
					
				$page_id = $this->pages['lokalenverhuur']['id'];
		
					if( $page_id ) {
						wp_delete_post( $page_id, true ); // this will trash, not delete. To also empty the trash use : wp_delete_post ($page_id, true)
						unset($this->pages['lokalenverhuur']);
						update_option('sn_scoutnet_pages', $this->pages);
						echo "<div class=\"updated settings-error\"><p><strong>De pagina lokalenverhuur is verwijderd.</strong></p></div>";
					}
			
			}
			

			
			
				if (isset($_POST['lidworden'])){
			
			   $page = get_page_by_title( 'Lid worden' );
			   
				if ( isset($page) ) {
    			
					echo "<div class=\"error settings-error\"><p><strong>De pagina <a href=\"{$site_url}/lidworden/\">Lid worden</a> bestaat reeds</strong></p></div>";
        		
    			} else {
    				$group = get_option('sn_scoutnet_group');
    				$_p = array();
    				$_p['post_name'] = 'lidworden';
    				$_p['post_title'] = 'Lid worden';
					$_p['post_content'] = "<iframe allowfullscreen=\"\" frameborder=\"0\" height=\"900\" src=\"https://groepsadmin.scoutsengidsenvlaanderen.be/groepsadmin/lidworden?groep={$group['groupID']}\" width=\"900\"></iframe> ";
					//$_p['page_template'] = 'lokalenverhuur-template.php';
    				
    				$this->make_my_page($_p);
					echo "<div class=\"updated settings-error\"><p><strong>De pagina <a href=\"{$site_url}/lidworden/\">Lid worden</a> is aangemaakt.</strong></p></div>";
					
    			}
			}
			
			if (((isset($_POST['lidwordendel']))) && (current_user_can('delete_posts'))){
			
					
				$page_id = $this->pages['lidworden']['id'];
		
					if( $page_id ) {
						wp_delete_post( $page_id, true ); // this will trash, not delete. To also empty the trash use : wp_delete_post ($page_id, true)
						unset($this->pages['lidworden']);
						update_option('sn_scoutnet_pages', $this->pages);
						echo "<div class=\"updated settings-error\"><p><strong>De pagina 'Lid worden' is verwijderd.</strong></p></div>";
					}
			
			}
			
			
			
			
			
			
			
			if (isset($_POST['sections'])){
			
			   $page = get_page_by_title( 'Takken' );
			   
				//var_dump($page);
				
				if ( isset($page) ) {
    			
					echo "<div class=\"error settings-error\"><p><strong>De pagina <a href=\"{$site_url}/takken/\">takken</a> bestaat reeds</strong></p></div>";
        		
    			} else {
    			
    				$_p = array();
    				$_p['post_name'] = 'takken';
    				$_p['post_title'] = 'Takken';
					$_p['post_content'] = "De inhoud van deze pagina wordt automatisch aangemaakt. Je dient deze dus niet te editeren.<br /><br />De Scoutnet API secret key moet ingevuld zijn. Zie Instellingen -> Scoutnet API group";
					$_p['page_template'] = 'takken-template.php';
    			
    				$this->make_my_page($_p);
    				
    				
    				
    				
    				
    				
    				
 try{
$devkey = "jorisp@scoutnet.be";
$secret = $this->options['apigroupkey'];
$appkey = substr($secret,0,6);

$apicall = new Scoutnet_API_Call('group', $devkey, $appkey, $secret, false);
$method = 'GET';
$endpoint = 'sections/'.$this->options['accountid'].'/?filter=name:name_url';

$call = $apicall->run($endpoint, $method, null);

//echo "ANSWER:<br />";
//var_dump($call);

if ($call['decoded']['head']['status']==1){
$sections = $call['decoded']['body']['data'];

/*
foreach ($sections as $section){
echo $section['name'];
}
*/

}else{
echo "<div class=\"error\">{$call['decoded']['head']['error_message']}<br /><br />Is de secret key correct? Contacteer info@scoutnet.be</div>";
}

}catch(Exception $e)
{echo '<div class="error">' .$e->getMessage().'</div>';}



// todo categoriën aanmaken

$parent_page = get_page_by_title( 'Takken' );


//var_dump($parent_page);

//$parent_ID = $parent_page->ID;


$new_page_template = 'takken-section-template.php';

foreach ($sections as $section){
$page_groep_title = $section['name'];
	

// Create the category

$term = term_exists($section['code'], 'category');
if ($term === 0 || $term === null) {
$my_cat = array('cat_name' => $section['name'], 'category_description' => $section['name'] . ' -- inhoud aangeleverd door Scouts en Gidsen Vlaanderen', 'category_nicename' => $section['code'], 'category_parent' => '');
$my_cat_id = wp_insert_category($my_cat);
}


    $page_groepen = get_page_by_title( $page_groep_title );
    
    if ( ! $page_groepen ) {

        // Create post (page) object
        $_p = array();
        $_p['post_title'] = $page_groep_title;
        $_p['post_content'] = "Geef hier wat algemene info over de tak. (info over de leiding en de berichten (van de categorie: $page_groep_title ) wordt automatisch toegevoegd)";
		$_p['post_parent'] = $parent_page->ID;
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
        
        
/*        
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
*/      
        

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
    				
   				
    				
    				
    				
    				
    				
    				
    				
    				
    				
    				
    				
    				
    				
					echo "<div class=\"updated settings-error\"><p><strong>De pagina <a href=\"{$site_url}/takken/\">takken</a> is aangemaakt.</strong></p></div>";
					
    			}
			}
			
			
			
			
			if (((isset($_POST['sectionsdel']))) && (current_user_can('delete_posts'))){
			
					
				$page_id = $this->pages['takken']['id'];
		
					if( $page_id ) {
				
					$mypages = get_pages( array( 'child_of' => $page_id, 'hierarchical' => 0 ) );

					foreach( $mypages as $page ) {	
					//echo $page->ID."<br />";
					wp_delete_post($page->ID, true);
					}
					
					
					
					
					
					
						wp_delete_post( $page_id, true ); // this will trash, not delete. To also empty the trash use : wp_delete_post ($page_id, true)
						unset($this->pages['takken']);
						update_option('sn_scoutnet_pages', $this->pages);
						echo "<div class=\"updated settings-error\"><p><strong>De pagina takken (en subpagina's) is verwijderd.</strong></p></div>";
						
					}
			
			}
			

			
			

			if (isset($_POST['members'])){
			
			   $page = get_page_by_title( 'Leiding' );
			   
				
				if ( isset($page) ) {
    				
					echo "<div class=\"error settings-error\"><p>De pagina <a href=\"{$site_url}/leiding/\">leiding</a> bestaat reeds</p></div>";
        		
    			} else {
    				$_p = array();
    				$_p['post_name'] = 'leiding';
    				$_p['post_title'] = 'Leiding';
					$_p['post_content'] = "De inhoud van deze pagina wordt automatisch aangemaakt. Je dient deze dus niet te editeren.<br /><br />De Scoutnet API secret key moet ingevuld zijn. Zie Instellingen -> Scoutnet API group";
					$_p['page_template'] = 'leiding-template.php';
    			
    				$this->make_my_page($_p);
					echo "<div class=\"updated settings-error\"><p>De pagina <a href=\"{$site_url}/leiding/\">leiding</a> is aangemaakt.</p></div>";
					
    			}
			}
			
			if (((isset($_POST['membersdel']))) && (current_user_can('delete_posts'))){
			
					
				$page_id = $this->pages['leiding']['id'];
		
					if( $page_id ) {
						wp_delete_post( $page_id, true ); // this will trash, not delete. To also empty the trash use : wp_delete_post ($page_id, true)
						unset($this->pages['leiding']);
						update_option('sn_scoutnet_pages', $this->pages);
						echo "<div class=\"updated settings-error\"><p>De pagina leiding is verwijderd.</p></div>";
			}
			
			}
			
			
			
			}
			
			?>
			<?php 
			$page = get_page_by_title( 'Onze groep' );
			if ( isset($page) ) {
			?>
			<input type="submit" name="groupdel" value="Verwijder de groepinfo pagina" class="button" /><br /><br />
			<?php
			}else{ 
			?>
			<input type="submit" name="group" value="Maak de groepinfo pagina" class="button button-primary" /><br /><br />
			<?php
			} 
			?>

			<?php 
			$page = get_page_by_title( 'Takken' );
			if ( isset($page) ) {
			?>
			<input type="submit" name="sectionsdel" value="Verwijder de takken pagina's" class="button" /> (opgelet! ook de subpagina's zullen verwijderd worden)<br /><br />
			<?php
			}else{ 
			?>
			<input type="submit" name="sections" value="Maak de takken pagina's" class="button button-primary" /><br /><br />
			<?php
			} 
			?>
			<?php
			$page = get_page_by_title( 'Lokalenverhuur' );
			if ( isset($page) ) {
			?>
			<input type="submit" name="rentdel" value="Verwijder de lokalenverhuur pagina" class="button" /><br /><br />
			<?php
			}else{
			?>
			<input type="submit" name="rent" value="Maak de lokalenverhuur pagina" class="button button-primary" /><br /><br />
			<?php
			} 
			?>
			
			<?php
			$page = get_page_by_title( 'Lid worden' );
			if ( isset($page) ) {
			//isset( $this->options['apigroupkey'] ) ? esc_attr( $this->options['apigroupkey']) : '')
			?>
			<input type="submit" name="lidwordendel" value="Verwijder de 'Lid worden' pagina" class="button" /><br /><br />
			
			<?php
			}else{
			$group = get_option('sn_scoutnet_group');
			if (isset($group['groupID'])){
			?>
			<input type="submit" name="lidworden" value="Maak de 'Lid worden' pagina" class="button button-primary" />&nbsp; [<?php echo $group['groupID'];?>]<br /><br />
			<?php
			}else{
			?>
			<input type="submit" name="lidworden" disabled="disabled" value="Maak de 'Lid worden' pagina" class="button" />&nbsp; (controleer SGV groep ID)<br /><br />
			<?php
			}
			}
			?>
			
			
			
			<?php }else{ ?>
			<input type="submit" name="group" value="Maak de groepinfo pagina" class="button" disabled="disabled" title="group API key missing" /><br /><br />
			<input type="submit" name="sections" value="Maak de takken pagina's" class="button" disabled="disabled" title="group API key missing" /><br /><br />
			<input type="submit" name="rent" value="Maak lokalenverhuur pagina" class="button" disabled="disabled" title="group API key missing" /><br /><br />
			<input type="submit" name="lidworden" value="Maak de 'Lid worden' pagina" class="button" disabled="disabled" title="group API key missing" /><br /><br />
			<?php }?> 
			<?php if ($this->options['apimemberkey']){?>
			
			<?php
			$page = get_page_by_title( 'Leiding' );
			if ( isset($page) ) {
			?>
			<input type="submit" name="membersdel" value="Verwijder de pagina met de leiding gegevens" class="button" /><br /><br />
			<?php
			}else{ 
			?>
			<input type="submit" name="members" value="Maak een pagina met de leiding gegevens" class="button button-primary" /><br /><br />
			<?php
			}
			?>
			
			<?php }else{ ?>
			<input type="submit" name="members" value="Maak een pagina met de leiding gegevens" class="button" disabled="disabled" title="member API key missing" /><br /><br />
			<?php }?>
			
			
	
			<?php }else{ ?>
			<input type="submit" name="accountid" value="accountid is missing" class="button" disabled="disabled" title="accountid missing" /><br /><br />
			<?php }?>
			
			
			</form>
    	    <?php 
    	        
		}elseif( $active_tab == 'shortcodes' ) {

			echo "<br /><br />Hier enkele voorbeelden van shortcodes die je kan gebruiken in je pagina's en berichten :";
			echo "<br /><br />[scoutnet_members type=\"leiding\" section=\"kapoenen\" style=\"1\"]";
			echo "<br /><br />[scoutnet_members type=\"groepsleiding\" groupby=\"none\" style=\"2\"]";
			echo "<br /><br />[scoutnet_members type=\"stam\" groupby=\"none\" style=\"3\"]";


		}elseif( $active_tab == 'help' ) {


				$mysections = get_mysections($this->options['accountid']);
				
				if (is_array($mysections)){
					$comma_separated = implode("| ", $mysections);
					echo "<br /><br /><b>section</b> = $comma_separated";
				}else{
					echo "<div class=\"error settings-error\"><p><strong>Sections failure</strong></p></div>";
				}
				
				$mytypes = get_mytypes($this->options['accountid']);
				
				if (is_array($mytypes)){
					$comma_separated = implode("| ", $mytypes);
					echo "<br /><br /><b>type</b> = $comma_separated";
				}else{
					echo "<div class=\"error settings-error\"><p><strong>Types failure</strong></p></div>";
				}
				
            			echo "<br /><br /><b>style</b> = 1 | 2 | 3 (default = 1)";
				echo "<br /><br /><b>groupby</b> = section | type | none (default = section)";
				echo "<br /><br /><br />Toevoegen/verwijderen van \"sections\" en \"types\" doe je via https://my.scoutnet.be/?config";

    	        
        	} else {
            	//settings_fields( 'my_option_group' );
            	//do_settings_sections( 'scoutnet-api' );
            	echo "<br /><br />page not found";
        	} 
			?>
				

		</div>
		<?php
    }

    /**
     * Register and add settings
     */
    public function page_init() {        
        register_setting(
            'my_option_group', // Option group
            'sn_scoutnet_api', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        add_settings_section(
            'setting_section_id', // ID
            'Keys',
            array( $this, 'print_section_info_keys' ), 
            'scoutnet-api' // Page
        );  

	add_settings_section(
		'options_section_shortcuts', // ID
		'Options',
		array( $this, 'print_section_info_options' ), 
		'scoutnet-api' // Page
	);

	add_settings_field(
		'apigroupkey', // ID
		'Group API key', // Title 
		array( $this, 'apigroupkey_callback' ), // Callback
		'scoutnet-api', // Page
		'setting_section_id' // Section           
	);

	add_settings_field(
		'apimemberkey', 
		'Member API key', 
		array( $this, 'apimemberkey_callback' ), 
		'scoutnet-api', 
		'setting_section_id'
	);

	add_settings_field(
		'accountid', 
		'Accountid', 
		array( $this, 'accountid_callback' ), 
		'scoutnet-api', 
		'setting_section_id'
	);
        
	add_settings_field(
		'option1', 
		'Hide birthday', 
		array( $this, 'option1_callback' ), 
		'scoutnet-api', 
		'options_section_shortcuts'
	);
        
	add_settings_field(
		'option2', 
		'Hide phone numbers', 
		array( $this, 'option2_callback' ), 
		'scoutnet-api', 
		'options_section_shortcuts'
	);
 
    }

	/**
	 * Sanitize each setting field as needed
	 *
	 * @param array $input Contains all settings fields as array keys
	*/
    
    
    // TODO accountid niet aanpassen 
    
	public function sanitize( $input ) {
		$new_input = array();
		$old = get_option('sn_scoutnet_api');
		$update_option = false;
		$new_input = $old;
		
		if( isset( $input['accountid'] ) ) {
			$new_input['accountid'] = absint( $input['accountid'] );
			
		}
		
		if( isset( $input['apigroupkey'] ) ) {

			$new_input['apigroupkey'] = sanitize_text_field( $input['apigroupkey'] );
			
			if ($old['apigroupkey']!=$new_input['apigroupkey']){
				$update_option = true;
			}
			
		}

		if( isset( $input['apimemberkey'] ) ) {
			$new_input['apimemberkey'] = sanitize_text_field( $input['apimemberkey'] );
			
			if ($old['apimemberkey']!=$new_input['apimemberkey']){
				$update_option = true;
				//TODO ping member service
				
			}
			
		}

		if( isset( $input['option1'] ) ) {$new_input['option1'] = 'y';}else{$new_input['option1'] = 'n';}
		if( isset( $input['option2'] ) ) {$new_input['option2'] = 'y';}else{$new_input['option2'] = 'n';}

		if ($update_option){
		
		try{

$devkey = "jorisp@scoutnet.be";
$secret = $new_input['apigroupkey'];
$appkey = substr($secret,0,6);
$apicall = new Scoutnet_API_Call('group', $devkey, $appkey, $secret, false);
$method = 'GET';
$endpoint = "lists/";
$call = $apicall->run($endpoint, $method, null);

if ($call['decoded']['head']['status']==1){
$group = $call['decoded']['body']['data'];
$new_input['accountid'] = $group[0]['accountid'];
$new_input['groupname'] = $group[0]['groupname'];
$new_input['orgname'] = $group[0]['orgname'];
$new_input['depname'] = $group[0]['depname'];
$new_input['depmark'] = $group[0]['depmark'];
$new_input['groupID'] = $group[0]['groupID'];
}else{
add_settings_error( 'myUniqueIdentifyer', esc_attr( 'settings_updated' ), 'Is de secret key correct? Contacteer info@scoutnet.be', 'error' );
$new_input['accountid'] = null;
}

}catch(Exception $e){
add_settings_error( 'myUniqueIdentifyer', esc_attr( 'settings_updated' ), $e->getMessage(), 'error' );
}
		
		}
		
		
		return $new_input;
	}

    /** 
     * Print the Section text
     */
	public function print_section_info_keys() {
	echo 'Om de shortcuts te kunnen gebruiken geef je hier de API keys:';
	}

	public function print_section_info_options() {
	//echo 'wat info';
	}


    /** 
     * Get the settings option array and print one of its values
     */
    public function apigroupkey_callback() {
    
    
        printf('<input type="text" id="apigroupkey" name="sn_scoutnet_api[apigroupkey]" value="%s" />', isset( $this->options['apigroupkey'] ) ? esc_attr( $this->options['apigroupkey']) : '');
    	//$test = get_option('sn_scoutnet_api');    
        //var_dump($test);
        
        echo " (omvat de groepgegevens en de lokaalverhuur data)";
        
        
    }

	/** 
	 * Get the settings option array and print one of its values
	 */
	public function apimemberkey_callback() {
		printf('<input type="text" id="apimemberkey" name="sn_scoutnet_api[apimemberkey]" value="%s" />',isset( $this->options['apimemberkey'] ) ? esc_attr( $this->options['apimemberkey']) : '');
		
		echo " (contacten en ledenbeheer)";
	}


	/** 
	 * Get the settings option array and print one of its values
	 */
	public function accountid_callback() {
		printf('<input type="text" id="accountid" name="sn_scoutnet_api[accountid]" value="%s" />',isset( $this->options['accountid'] ) ? esc_attr( $this->options['accountid']) : '');
		//var_dump($this->options);
		//echo " (TODO dit kan waarschijnlijk weg. Enkel voor demo SGV.)";
	}
	
	public function option1_callback() {
		if ($this->options['option1']=='y'){
        echo '<input type="checkbox" id="option1" name="sn_scoutnet_api[option1]" checked="checked "/>';
		}else{
		echo '<input type="checkbox" id="option1" name="sn_scoutnet_api[option1]" />';	
		}
		echo 'Show birthday only to logged-in users';
	}
	
	public function option2_callback() {
		if ($this->options['option2']=='y'){
        echo '<input type="checkbox" id="option2" name="sn_scoutnet_api[option2]" checked="checked "/>';
		}else{
		echo '<input type="checkbox" id="option2" name="sn_scoutnet_api[option2]" />';	
		}
		echo 'Show phone numbers only to logged-in users';
	}

	
	
	private function make_my_page($_p) {

	//global $wpdb;
	
	if (!isset($_p['post_name'])){$_p['post_name'] = 'error-page';}
	if (!isset($_p['post_title'])){$_p['post_title'] = 'Error page';}
	if (!isset($_p['post_content'])){$_p['post_content'] = 'Please contact Scoutnet';}
	


	// the menu entry...
	//delete_option("sn_page_{$key}_title");
	//delete_option($this->pages[$_p['post_name']]);
	//add_option("sn_page_{$key}_title", $page_title, '', 'yes');
	//add_option($this->pages[$_p['post_name']]['title'], $_p['post_title'], '', 'yes');
	
	unset($this->pages[$_p['post_name']]);
	update_option('sn_scoutnet_pages', $this->pages);
		
	// the slug...
	//delete_option("sn_page_{$key}_name");
	//add_option("sn_page_{$key}_name", $key, '', 'yes');
		
	// the id...
	//delete_option("sn_page_{$key}_id");
	//delete_option($this->pages[$_p['post_name']]['id']);
	$this->pages[$_p['post_name']]['id']=0;
	$this->pages[$_p['post_name']]['title']=$_p['post_title'];
	update_option('sn_scoutnet_pages', $this->pages);
	//add_option($this->pages[$_p['post_name']]['id'], '0', '', 'yes');
		
	$mypage = get_page_by_title( $_p['post_title'] );

	if ( ! $mypage ) { 

		// Default values
		$_p['post_status'] = 'publish';
		$_p['post_type'] = 'page';
		$_p['comment_status'] = 'closed';
		$_p['ping_status'] = 'closed';
		$_p['post_category'] = array(1); // the default 'Uncategorised'
		
		$new_page_template = $_p['page_template']; 
		unset($_p['page_template']);
		
		//$_p['page_template'] = 'Takken';  
		/* indien template niet bestaat gaat er blijkbaar iets fout
		 * De pagina wordt aangemaakt, maar we krijgen geen ID -> wp_insert_post() geeft geen ID
		 * De pagina koppelen aan de template doen in de theme functions.php (?)
		 */
		
		
		//Insert the post into the database
		$page_id = wp_insert_post( $_p );
		
		if(!empty($new_page_template)){
			update_post_meta($page_id, '_wp_page_template', $new_page_template);
		}

function scotts_set_nav_menu($menu_id,$location){
    $locations = get_theme_mod('nav_menu_locations');
    $locations[$location] = $menu_id;
    set_theme_mod( 'nav_menu_locations', $locations );
}
		
//echo "building menu";
$menu_name= 'footer_menu';

$locations = get_nav_menu_locations();
//var_dump($locations);

//if ( !is_nav_menu( 'footer_menu' )) {
//$run_once = get_option('menu_check');
//if (!$run_once){
    $name = 'footer_menu';
    if ( !is_nav_menu( $name )) {
    $menu_id = wp_create_nav_menu($name);
    }
    $menu = get_term_by( 'name', $name, 'nav_menu' );
 /*   wp_update_nav_menu_item($menu->term_id, 0, array(
        'menu-item-title' => 'First Menu Item', 
        'menu-item-url' => 'http://mysite.com', 
        'menu-item-status' => 'publish'));*/
    
    $itemData =  array(
    'menu-item-object-id' => $page_id,
    'menu-item-parent-id' => 0,
    'menu-item-position'  => 2,
    'menu-item-object' => 'page',
    'menu-item-type'      => 'post_type',
    'menu-item-status'    => 'publish'
  );

  
  
wp_update_nav_menu_item($menu->term_id, 0, $itemData);
scotts_set_nav_menu($menu->term_id,'footer_menu');

/*
 * TODO hoofdmenu creeer. Nu gebruiken we de primary 
 * 
    $name = 'main-nav-menu';
    if ( !is_nav_menu( $name )) {
    $menu_id = wp_create_nav_menu($name);
    }
    $menu = get_term_by( 'name', $name, 'nav_menu' );


wp_update_nav_menu_item($menu->term_id, 0, $itemData);
scotts_set_nav_menu($menu->term_id,'main-nav-menu');
    */
    
    //update_option('menu_check', true);
//}
    
//}



/*
if ( ( $locations = get_nav_menu_locations() ) && isset( $locations[ $menu_name ] ) ) {
	$mymenu = wp_get_nav_menu_object( $locations[ $menu_name ] );

	echo "eee";
	$menuID = (int) $mymenu->term_id;
//$page_id = get_page_by_title('About Us');
 
$itemData =  array(
    'menu-item-object-id' => $page_id,
    'menu-item-parent-id' => 0,
    'menu-item-position'  => 2,
    'menu-item-object' => 'page',
    'menu-item-type'      => 'post_type',
    'menu-item-status'    => 'publish'
  );
 
wp_update_nav_menu_item($menuID, 0, $itemData);
	
	
	
	$menu_items = wp_get_nav_menu_items($mymenu->term_id);
	
	$menu_list = 'hallo<ul id="menu-' . $menu_name . '">';

	foreach ( (array) $menu_items as $key => $menu_item ) {
	    $title = $menu_item->title;
	    $url = $menu_item->url;
	    $menu_list .= '<li><a href="' . $url . '">' . $title . '</a></li>';
	}
	$menu_list .= '</ul>';
    } else {
	$menu_list = '<ul><li>Menu "' . $menu_name . '" not defined.</li></ul>';
    }
*/
		
/*
$mymenu = wp_get_nav_menu_object('Hoofdmenu');
$menuID = (int) $mymenu->term_id;
//$page_id = get_page_by_title('About Us');
 
$itemData =  array(
    'menu-item-object-id' => $page_id,
    'menu-item-parent-id' => 0,
    'menu-item-position'  => 2,
    'menu-item-object' => 'page',
    'menu-item-type'      => 'post_type',
    'menu-item-status'    => 'publish'
  );
 
wp_update_nav_menu_item($menuID, 0, $itemData);
		
		
$mymenu = wp_get_nav_menu_object('Hoofdmenu');

$menu_items = wp_get_nav_menu_items( $mymenu, $args );

var_dump($menu_items);

	$menu_list = '<ul id="menu-' . $menu_name . '">';

	foreach ( (array) $menu_items as $key => $menu_item ) {
		
	    $title = $menu_item->title;
	    $url = $menu_item->url;
	    $menu_list .= '<li><a href="' . $url . '">' . $title . '</a></li>';
	    unset($menu_item);
	}
	$menu_list .= '</ul>';

	
	echo $menu_list;	
*/		

	} else {
	
	// the plugin may have been previously active and the page may just be trashed...

		$page_id = $mypage->ID;

		//make sure the page is not trashed...
		$mypage->post_content = "Recovered from trash.<br /><br />De inhoud van deze pagina wordt automatisch aangemaakt. Je dient deze dus niet te editeren.<br /><br />De Scoutnet API secret key moet ingevuld zijn. Zie Instellingen -> Scoutnet API group";
		$mypage->post_status = 'publish';
		$page_id = wp_update_post( $mypage );

	}
		
	//delete_option( $this->pages[$_p['post_name']]['id'] );
	//add_option( $this->pages[$_p['post_name']]['id'], $page_id );
	
	$this->pages[$_p['post_name']]['id']=$page_id;
	update_option('sn_scoutnet_pages', $this->pages);



	

	

}	
	
	
	

}






