<?php
/*
Plugin Name: Wordpress Minecraft Dynmap
Plugin URI: http://craft.micoli.org
Description: Dynmap
Author: o.michaud
Version: 0.1
Author URI: http://craft.micoli.org
*/
include_once dirname(__FILE__)."/../morg_wp_plugin/morg_wp_plugin.php";

class minecraft_dynmap extends morg_wp_plugin{
	var $prefix = "morgdm";
	var $pluginName="";
	var $jsonapi = null;

	var $adminMenu = array(
		"MinecraftDynmap"=>array(
			"page_title"=>"Minecraft Dynmap",
			"menu_title"=>"Minecraft Dynmap",
			"capability"=>'manage_options',
			"function"	=>"admin__main"
		)
	);
	
	function __construct(){
		parent::__construct();
		$this->jsonapi = new JSONAPI(
			get_option('morg_ah_jsonapi_host'		),
			get_option('morg_ah_jsonapi_port'		),
			get_option('morg_ah_jsonapi_user'		),
			get_option('morg_ah_jsonapi_password'	),
			get_option('morg_ah_jsonapi_salt'		)
		);
	}

	function wp_enqueue_script__jquerydatatables() {
		wp_enqueue_script( 'jquerynotmin','http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js');
		
		wp_enqueue_style ( 'dynmapleaflet'		, get_site_url().'/wp-content/plugins/minecraft-wp-Dynmap/web/css/leaflet.css');
		wp_enqueue_script( 'dynmapleaflet'		, get_site_url().'/wp-content/plugins/minecraft-wp-Dynmap/web/js/leaflet.js');
		wp_enqueue_script( 'dynmapcmarker'		, get_site_url().'/wp-content/plugins/minecraft-wp-Dynmap/web/js/custommarker.js');
		wp_enqueue_script( 'dynmapdynmaputils'	, get_site_url().'/wp-content/plugins/minecraft-wp-Dynmap/web/js/dynmaputils.js');
		
		//wp_enqueue_style ( 'dynmapstandalone'	, get_site_url().'/wp-content/plugins/minecraft-wp-Dynmap/web/css/standalone.css');
		wp_enqueue_style ( 'dynmapdmstyle'		, get_site_url().'/wp-content/plugins/minecraft-wp-Dynmap/web/css/dynmap_style.css');
		wp_enqueue_style ( 'dynmapoverride'		, get_site_url().'/wp-content/plugins/minecraft-wp-Dynmap/override.css');
		//wp_enqueue_style ( 'dynmapembeded'		, get_site_url().'/wp-content/plugins/minecraft-wp-Dynmap/embedded.css');
		
		wp_enqueue_script( 'dynmapversion'		, get_site_url().'/wp-content/plugins/minecraft-wp-Dynmap/web/version.js');
		wp_enqueue_script( 'dynmapjquery'		, get_site_url().'/wp-content/plugins/minecraft-wp-Dynmap/web/js/jquery.json.js');
		wp_enqueue_script( 'dynmapmouse'		, get_site_url().'/wp-content/plugins/minecraft-wp-Dynmap/web/js/jquery.mousewheel.js');
		wp_enqueue_script( 'dynmapminecraft'	, get_site_url().'/wp-content/plugins/minecraft-wp-Dynmap/web/js/minecraft.js');
		wp_enqueue_script( 'dynmapmap'			, get_site_url().'/wp-content/plugins/minecraft-wp-Dynmap/web/js/map.js');
		wp_enqueue_script( 'dynmapwpmap'		, get_site_url().'/wp-content/plugins/minecraft-wp-Dynmap/minecraftDynmap.js');
		wp_enqueue_script( 'dynmaphdmap'		, get_site_url().'/wp-content/plugins/minecraft-wp-Dynmap/web/js/hdmap.js');
		wp_enqueue_script( 'dynmapkzemap'		, get_site_url().'/wp-content/plugins/minecraft-wp-Dynmap/web/js/kzedmaps.js');
		wp_enqueue_script( 'dynmapflatmap'		, get_site_url().'/wp-content/plugins/minecraft-wp-Dynmap/web/js/flatmap.js');
		//wp_enqueue_script( 'dynmapconfig'		, get_site_url().'/wp-content/plugins/minecraft-wp-Dynmap/web/config.js');
	}
	function wp_add_action__init__dynmap(){
		global $wp,$wp_rewrite;
		//$wp_rewrite->add_rule(
		//		'images/blank.png',
		//		'/wp/wp-content/plugins/minecraft-wp-Dynmap/images/blank.png', 
		//		'top'
		//);
		//$wp_rewrite->flush_rules(false);
	}
	
	function wp_ajax_nopriv__minecraft_dynmap_fwd(){
	
		// Change these configuration options if needed, see above descriptions for info.
		$enable_jsonp    = false;
		$enable_native   = false;
		$valid_url_regex = '/.*/';
	
		// ############################################################################
	
		$path = $_GET['path'];
		$url = get_option('morg_dm_dynmapurl').$path;
	
	
		$ch = curl_init( $url );
	
		$client_headers = array();
		$client_headers[] = 'X-Forwarded-For: '.$_SERVER['REMOTE_ADDR'];
		curl_setopt($ch, CURLOPT_HTTPHEADER, $client_headers);
	
		if ( strtolower($_SERVER['REQUEST_METHOD']) == 'post' ) {
			$postText = trim(file_get_contents('php://input'));
			curl_setopt( $ch, CURLOPT_POST, true );
			curl_setopt( $ch, CURLOPT_POSTFIELDS, $postText );
		}
	
	
		$cookie = array();
		foreach ( $_COOKIE as $key => $value ) {
			$cookie[] = $key . '=' . $value;
		}
		$cookie[] = SID;
		$cookie = implode( '; ', $cookie );
		curl_setopt( $ch, CURLOPT_COOKIE, $cookie );
	
		//curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
		curl_setopt( $ch, CURLOPT_HEADER, true );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
	
		curl_setopt( $ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT'] );
	
		list( $header, $contents ) = preg_split( '/([\r\n][\r\n])\\1/', curl_exec( $ch ), 2 );
	
		$status = curl_getinfo( $ch );
	
		curl_close( $ch );
	
		// Split header text into an array.
		$header_text = preg_split( '/[\r\n]+/', $header );
	
		// Propagate headers to response.
		foreach ( $header_text as $header ) {
			if ( preg_match( '/^(?:Content-Type|Content-Language|Set-Cookie):/i', $header ) ) {
				header( $header );
			}
		}
	
		print $contents;
		die();
	}
	
	function admin__main() {
		if($_POST['morg_dm_hidden'] == 'Y') {
			update_option('morg_dm_dynmapurl', $_POST['morg_dm_dynmapurl']);
			update_option('morg_dm_url_root', $_POST['morg_dm_url_root']);
			update_option('morg_dm_iframemode', $_POST['morg_dm_iframemode']);
			print sprintf('<div class="updated"><p><strong>%s</strong></p></div>', __('Options saved.' ));
		}
		$morg_dm_dynmapurl		= get_option('morg_dm_dynmapurl'		,"http://127.0.0.1:8123");
		$morg_dm_url_root		= get_option('morg_dm_url_root'			,"http://127.0.0.1:8123");
		$morg_dm_iframemode		= get_option('morg_dm_iframemode'		,"1");
		
		$form = "
				<div class=\"wrap\">
				<h2>" . __( 'MineCraft Dynmap', 'morg_dm_trdom' ) . "</h2>
				<form name=\"morg_dm_form\" method=\"post\" action=\"".str_replace( '%7E', '~', $_SERVER['REQUEST_URI']) ."\">
				<input type=\"hidden\" name=\"morg_dm_hidden\" value=\"Y\">
				<h4>" . __( 'Settings', 'morg_wi_trdom' ) . "</h4>
				<p>". __("Dynmap Url: "		)."<input type=\"text\" name=\"morg_dm_dynmapurl\" 	value=\""	.$morg_dm_dynmapurl		."\" size=\"90\">". __(" ex: http://127.0.0.1:8123") ."</p>
				<p>". __("Dynmap url root: ")."<input type=\"text\" name=\"morg_dm_url_root\" 	value=\""	.$morg_dm_url_root		."\" size=\"90\">". __(" ex: http://127.0.0.1:8123") ."</p>
				<p>". __("iframemode: "		)."<input type=\"text\" name=\"morg_dm_iframemode\" value=\""	.$morg_dm_iframemode	."\" size=\"10\">". __(" ex: 1|0") ."</p>
				<hr />
				<p class=\"submit\">
					<input type=\"submit\" name=\"Submit\" value=\"". __('Update Options', 'morg_dm_trdom' ) ."\" />
				</p>
				</form>
			</div>";
		print $form;
	}

	function shortcode__map($atts, $content = null) {
		global $wp;
		$prms =shortcode_atts(array(
			'uid'				=>'mcmap'.date('dHisu'),
			'mode'				=>get_option('morg_dm_iframemode')?'iframe':'div',
			'dynmapurl'			=>get_option('morg_dm_dynmapurl'),
			'width'				=>'840px',
			'height'			=>'600px',
			'world'				=>'world',
			'map'				=>'surface',
			'nopanel'			=>'true',
			'type'				=>'true',
			'showlayercontrol'	=>'false',
			'chat'				=>'false',
			'largeclock'		=>'false',
			'link'				=>'false',
			'zoomonfirstlink'	=> 1,
		), $atts);
		
		if(array_key_exists('dm_mode',$wp->query_vars)){
			$dm_mode = $wp->query_vars['dm_mode'];
		}else{
			$dm_mode = 'main';
		}
		$smarty	= self::getSmarty(__FILE__);
		$data	= array(
			'morg_dm_url_root'		=> get_option('morg_dm_url_root'),
			'morg_dm_localplan_page'=> get_option('morg_dm_localplan_page'),
			'prms'					=> $prms
		);
		switch ($dm_mode){
			case 'main';
				$data['scripturlRoot']='/wp-content/plugins/minecraft-wp-Dynmap/web';
				$data['siteUrl']=site_url();
				$template='templates/dynmap_map.tpl';
			break;
		}
		$smarty->assign('data',$data);
		print $smarty->fetch($template);
	}
}
$morg_minecraft_dynmap = new minecraft_dynmap();
?>