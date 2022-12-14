<?php

/* 
 Plugin Name: AdminPress System
 Plugin URI: https://kingfunnel.co
 Description: Personalización del Admin en WordPress.
 Version: 1.4.5
 Author: KingFunnel
 Author URI: https://kingfunnel.co
 License: GPL 2+
 License URI: https://kingfunnel.co */ 



/* /////////////////////////////////////////////////// */

// TWP-0 PERSONALIZACION GENERAL

// TW-0.1  CSS para el Back-end y Front-End en un mismo archivo de estilos

	// CSS para el Back-end
	add_action('admin_enqueue_scripts', 'bs_custom_admin_styles');
		function bs_custom_admin_styles() {
	wp_enqueue_style('custom-admin-styles', plugins_url('/css/styles-admin.css', __FILE__ ));
	}

	// CSS para el Front-end
	add_action('wp_enqueue_scripts', 'bs_custom_theme_styles');
		function bs_custom_theme_styles() {
	wp_enqueue_style('custom-theme-styles', plugins_url('/css/styles-theme.css', __FILE__ ));
	}

/* /////////////////////////////////////////////////// */




// TWP-1 OCULTAR BRANDING DE WORDPRESS

// TW-1.1  Ocultar la pestaña de [Ayuda] en el Dashboard WP
	add_filter('contextual_help_list','asv_hide_help');
	function asv_hide_help(){
    global $current_screen;
    $current_screen->remove_help_tabs();
	}


// TW-1.2  Eliminar [Logo de WordPress] en el Login de WP
    function custom_login_logo() {
	echo '<style type ="text/css">.login h1 a {
		display:none!important; }</style>';
		}
	add_action('login_head', 'custom_login_logo');


// TW-1.3  Eliminar [Enlaces y Opciones] en el [AdminBar] de WorPress
	add_action('admin_bar_menu', 'bs_remove_nodes', 999 );
	function bs_remove_nodes() {
		global $wp_admin_bar;
		
		$wp_admin_bar->remove_node('comments');
		$wp_admin_bar->remove_node('wp-logo');
		$wp_admin_bar->remove_node('search');
		$wp_admin_bar->remove_node('updates');

		$wp_admin_bar->remove_node('themes');
		//$wp_admin_bar->remove_node('customize');
		$wp_admin_bar->remove_node('widgets');
		$wp_admin_bar->remove_node('hide-notifications');
		$wp_admin_bar->remove_node('admin-cleaner-main');
		$wp_admin_bar->remove_node('view-store');

		$wp_admin_bar->remove_node('new-post');
		$wp_admin_bar->remove_node('new-elementor_library');
		$wp_admin_bar->remove_node('new-media');
		$wp_admin_bar->remove_node('new-shop_order');
		$wp_admin_bar->remove_node('new-shop_coupon');
		$wp_admin_bar->remove_node('new-wc_membership_plan');
		$wp_admin_bar->remove_node('new-wc_user_membership');
		$wp_admin_bar->remove_node('new-astra-advanced-hook');
		$wp_admin_bar->remove_node('new-contenidos_cpt');
		$wp_admin_bar->remove_node('new-ibx_wpfomo');
		$wp_admin_bar->remove_node('archive');

		$wp_admin_bar->remove_node('edit-profile');
		
	}   

/* /////////////////////////////////////////////////// */




// TWP-2 MODIFICACIONES WORDPRESS

// TW-2.1  Desactivar [Actualizaciones Mayores] del Core WordPress 
	add_filter( 'allow_major_auto_core_updates', '__return_false' );

// TW-2.2  Desactivar [Enlaces a Adjuntos]
	function cleanup_attachment_link( $link ) {
	return; }
	add_filter( 'attachment_link', 'cleanup_attachment_link' );

// TW-2.3  Desactivar [Emails de actualizaciones automaticas de Plugins]
	add_filter('auto_plugin_update_send_email', '__return_false');

// TW-2.4  Desactivar [Emails de actualizaciones automaticas de Themes]
	add_filter('auto_theme_update_send_email', '__return_false');

// TW-2.5  Desactivar [Emails a Admins de Restablecimiento de contraseñas]
	if ( !function_exists( 'wp_password_change_notification' ) ) {
    function wp_password_change_notification() {}
	}

// TW-2.6  Desactiva el Modo [Pantalla completa] del editor de Gutenberg
function mg_desactivar_editor_gb_pantalla_completa_por_default() {
	$script = "window.onload = function() { const isFullscreenMode = wp.data.select( 'core/edit-post' ).isFeatureActive( 'fullscreenMode' ); if ( isFullscreenMode ) { wp.data.dispatch( 'core/edit-post' ).toggleFeature( 'fullscreenMode' ); } }";
	wp_add_inline_script( 'wp-blocks', $script );
}
add_action( 'enqueue_block_editor_assets', 'mg_desactivar_editor_gb_pantalla_completa_por_default' );


// TW-2.7  Ocultar widgets del escritorio
function wp_dashboard_hide_tw() {
	$screen = get_current_screen();
	if ( !$screen ) {
	return; }

	//remove_meta_box('dashboard_activity', 'dashboard', 'normal');  		// Actividad
	remove_meta_box('llar_stats_widget', 'dashboard', 'normal');			// Limit Login Attempts Reloaded
	remove_meta_box('dashboard_right_now', 'dashboard', 'normal'); 		    // De un vistazo
	remove_meta_box('wc_admin_dashboard_setup', 'dashboard', 'normal');  	// Primeros pasos Woocommerce
	remove_meta_box('dashboard_primary', 'dashboard', 'side');  		    // Eventos
	remove_action ('welcome_panel','wp_welcome_panel'); 				    // Bienvenida
	remove_meta_box('dashboard_site_health', 'dashboard', 'normal'); 	    // Salud del sitio
	remove_meta_box('e-dashboard-overview', 'dashboard', 'normal'); 	    // Conoce elementor
	remove_meta_box('nfw_dashboard_welcome', 'dashboard', 'normal'); 	    // NinjaFirewall
	remove_meta_box('fluentsmtp_reports_widget', 'dashboard', 'normal'); 	// Fluent-SMTP
	remove_meta_box('dashboard_quick_press', 'dashboard', 'side'); 			// Borrador rápido
	remove_meta_box('wpcomplete-course-statistics', 'dashboard', 'normal'); 	// WPComplete Course Statistics
	remove_meta_box('cartFlows_setup_dashboard_widget', 'dashboard', 'normal'); // CartFlows

}
add_action('wp_dashboard_setup', 'wp_dashboard_hide_tw', 20);


// TW-2.8  Cerrar Sesión sin confirmación
add_action('check_admin_referer', 'logout_without_confirm', 10, 2);
function logout_without_confirm($action, $result)	{

	if ($action == "log-out" && !isset($_GET['_wpnonce'])) {
		$redirect_to = isset($_REQUEST['redirect_to']) ? $_REQUEST['redirect_to'] : '/mi-cuenta/';
		$location = str_replace('&amp;', '&', wp_logout_url($redirect_to));
		header("Location: $location");
		die;
	}
}


// TW-2.9 Desactivar ADMIN-BAR para usuarios no Administradores
function bbloomer_hide_admin_bar_if_non_admin( $show ) {
	if ( ! current_user_can( 'administrator' ) ) $show = false;
		return $show;
	}
add_filter( 'show_admin_bar', 'bbloomer_hide_admin_bar_if_non_admin', 20, 1 );


// TW-2.10 Restringir la Verificación del email de Administración WordPress al usuario [KitFunnel-Admin]
add_filter( 'admin_email_check_interval', function( $interval ) {
   if ( in_array( $_POST['log'], array( '{{KitFunnel-Admin}}' ) ) ) {
      return false;
   } else {
      return $interval;
   }
} );



// Ocultar [Muplugins] en el listado de Plugins
add_filter( 'show_advanced_plugins', 'f711_hide_advanced_plugins', 10, 2 );
	function f711_hide_advanced_plugins( $default, $type ) {
    if ( $type == 'mustuse' ) return false;
    return $default;
}

/* /////////////////////////////////////////////////// */




// TWP-3 ELIMINAR ITEMS EN EL MENÚ DEL ESCRITORIO DE WORDPRESS

// TW-3.1  Eliminar items del menú en el Back End
add_action('admin_menu', 'bs_remove_menu_items');
function bs_remove_menu_items() {
remove_menu_page( 'edit-comments.php' ); }
function remove_menus(){

	// Quitar menús principales del admin de WordPress
    remove_menu_page( 'jet-dashboard' );							//Jet Dasboard
    remove_menu_page( 'jet-engine' );								//Jet Engine
    remove_menu_page( 'edit.php?post_type=jet-smart-filters' );		//Jet Smart Filters
    remove_menu_page( 'wpcomplete-courses' );						//WP Complete
    remove_menu_page( 'limit-login-attempts' );						//Limit Login Attempts Reloaded
    remove_menu_page( 'plugin_load_filter_admin_manage_page' );		//Plugin Load Filter
    remove_menu_page( 'loco' );										//Loco Translate
    remove_menu_page( 'wpcode' );									//WP Code Snippet
    remove_menu_page( 'pixelyoursite' );							//Pixel Your Site
    remove_menu_page( 'ai1wm_export' );								//All-in-One WP Migration
    remove_menu_page( 'wppusher' );									//WP Pusher

	// Quitar menús hijo o secundarios del admin de WordPress
  	remove_submenu_page( 'NinjaFirewall','NinjaFirewall' );						//NinjaFirewall/Dasboard
  	remove_submenu_page( 'NinjaFirewall','nfsubmalwarescan' );					//NinjaFirewall/Anti-Malware
  	remove_submenu_page( 'NinjaFirewall','nfsubwplus' );						//NinjaFirewall/WP+Edition

  	remove_submenu_page( 'themes.php','astra' );					//Apariencia/Astra
  	remove_submenu_page( 'themes.php','widgets.php' );				//Apariencia/Widgets

  	remove_submenu_page( 'tools.php','tools.php' );					//Herramientas/Herramientas
  	remove_submenu_page( 'tools.php','import.php' );				//Herramientas/Importar
  	remove_submenu_page( 'tools.php','export.php' );				//Herramientas/Exportar
  	remove_submenu_page( 'tools.php','export-personal-data.php' );	//Herramientas/Exportar los datos personales
  	remove_submenu_page( 'tools.php','erase-personal-data.php' );	//Herramientas/Borrar los datos personales

  	remove_submenu_page( 'options-general.php','ithemes-licensing' );	//Ajustes/iThemes Licensing
  	remove_submenu_page( 'options-general.php','options-privacy.php' );	//Ajustes/Privacidad
  	remove_submenu_page( 'options-general.php','options-writing.php' );	//Ajustes/Ajustes de escritura
  	remove_submenu_page( 'options-general.php','options-media.php' );	//Ajustes/Ajustes de medios
  	remove_submenu_page( 'options-general.php','perfmatters' );			//Ajustes/Perfmatters
  	remove_submenu_page( 'options-general.php','wcipi.php' );			//Ajustes/WooCommerce international phone
  	remove_submenu_page( 'options-general.php','cpto-options' );		//Ajustes/Post Types Order

  	remove_submenu_page( 'themes.php','edit.php?post_type=astra-advanced-hook' );	//Apariencia/Diseños Personalizados

}
add_action( 'admin_menu', 'remove_menus', 999 );

/* /////////////////////////////////////////////////// */




// TWP-4 MODIFICACIONES WOOCOMMERCE

// TW-4.1  Eliminar Sub-Menus del Item Woocommerce (My Tienda)
	add_action( 'admin_menu', 'wcbloat_remove_admin_addon_submenu', 999 );
	function wcbloat_remove_admin_addon_submenu() {
		//remove_submenu_page( 'woocommerce', 'wc-admin');		//Inicio	
		//remove_submenu_page( 'woocommerce', 'wc-status');		//Estado
		//remove_submenu_page( 'woocommerce', 'wc-addons');		//Extenciones
	}

// TW-4.2  Eliminar Item [Marketing] del Back-end WP
	add_filter( 'woocommerce_admin_features', 'disable_features' );
	function disable_features( $features ) {
	  $marketing = array_search('marketing', $features);
	  unset( $features[$marketing] );
	return $features;
	}


// TW-4.3  Esconder aviso "Conecta tu tienda a WooCommerce.com"
	add_filter ('woocommerce_helper_suppress_admin_notices', '__return_true');

// TW-4.4  Completar los pedidos digitales
	add_filter( 'woocommerce_payment_complete_order_status', 'virtual_order_payment_complete_order_status', 10, 2 );
	function virtual_order_payment_complete_order_status( $order_status, $order_id ) {
	$order = new WC_Order( $order_id );
		if ( 'processing' == $order_status &&
		   ( 'on-hold' == $order->status || 'pending' == $order->status || 'failed' == $order->status ) ) {
		   $virtual_order = null;
		if ( count( $order->get_items() ) > 0 ) {
		   foreach( $order->get_items() as $item ) {
        if ( 'line_item' == $item['type'] ) {
           $_product = $order->get_product_from_item( $item );
        if ( ! $_product->is_virtual() ) {
				$virtual_order = false;
				break;
				} else {
				$virtual_order = true;
				}
			  }
			}
		  }
    if ( $virtual_order ) {
      return 'completed';
		}
	}
	return $order_status;
	}


// TW-4.5  Sincronizar Nombre y Apellidos del Cliente con la Data de WordPress
add_filter( 'pre_user_first_name', 'ayudawp_sincronizar_nombre_usuario_wp_woo' );
function ayudawp_sincronizar_nombre_usuario_wp_woo( $first_name ) {
	if ( isset( $_POST['billing_first_name'] ) ) {
	$first_name = $_POST['billing_first_name'];
	}
return $first_name;
}
add_filter( 'pre_user_last_name', 'ayudawp_sincronizar_apellidos_usuario_wp_woo' );
function ayudawp_sincronizar_apellidos_usuario_wp_woo( $last_name ) {
	if ( isset( $_POST['billing_last_name'] ) ) {
	$last_name = $_POST['billing_last_name'];
	}
return $last_name;
}


// TW-4.6  Cambiar la Limpieza predeterminada del Programador de acciones a #1 Dia
add_filter( 'action_scheduler_retention_period', 'wpb_action_scheduler_purge' );
function wpb_action_scheduler_purge() {
	return DAY_IN_SECONDS * 3;
}



// TWP-5 OCULTAR PLUGINS DEL LISTADO DE PLUGINS

add_filter( 'all_plugins', 'hide_plugins');
function hide_plugins($plugins) {
        
	// Ocultar AdminPress
	if(is_plugin_active('adminpress-wp/adminpress-system.php')) {
	unset( $plugins['adminpress-wp/adminpress-system.php'] ); }

	// Ocultar Add-ons Ninja Firewall Security
	if(is_plugin_active('add-ons-ninja-firewall-security/add-ons-ninja-firewall-security.php')) {
	unset( $plugins['add-ons-ninja-firewall-security/add-ons-ninja-firewall-security.php'] ); }

	// Ocultar WP Pusher
	if(is_plugin_active('wppusher/wppusher.php')) {
	unset( $plugins['wppusher/wppusher.php'] ); }

return $plugins; }





// TWP-6 DESACTIVAR ACTUALIZACIONES DE PLUGINS PERSONALIZADOS
function disable_plugin_updates( $value ) {


	// Deactivar actualizaciones de [Elementor]
	unset( $value->response['elementor/elementor.php'] );

	// Deactivar actualizaciones de [Elementor Pro]
	unset( $value->response['elementor-pro/elementor-pro.php'] );

	// Deactivar actualizaciones de [CartFlows]
	unset( $value->response['cartflows/cartflows.php'] );

	// Deactivar actualizaciones de [CartFlows Pro]
	unset( $value->response['cartflows-pro/cartflows-pro.php'] );

	// Deactivar actualizaciones de [Perfmatters]
	unset( $value->response['perfmatters/perfmatters.php'] );

	// Deactivar actualizaciones de [Woocommerce Memberships]
	unset( $value->response['woocommerce-memberships/woocommerce-memberships.php'] );

	// Deactivar actualizaciones de [WP Rocket]
	unset( $value->response['wp-rocket/wp-rocket.php'] );

	// Deactivar actualizaciones de [WP Fusion]
	unset( $value->response['wp-fusion/wp-fusion.php'] );


return $value; }
add_filter( 'site_transient_update_plugins', 'disable_plugin_updates' );

/* /////////////////////////////////////////////////// */






// TWP-6 CSS BACK-END [PANEL DE CONTROL]
add_action( 'admin_head', 'css_kitfunnel_dash' );
function css_kitfunnel_dash() {	?> <style>

		/* CSS Personalizado aqui */
		.wp-submenu.wp-submenu-wrap {border-radius: 6px !important;}.ab-sub-wrapper {border-radius: 0px 0px 6px 6px !important;}#wp-admin-bar-my-account img:not(#wp-admin-bar-user-info img) {border: 3px solid #8c8f94bf !important;border-radius: 6px !important;background: #aaaaaa00 !important;}#wp-admin-bar-user-info img {border-radius: 6px !important;}#wp-admin-bar-wp-rocket #wp-admin-bar-support, #wp-admin-bar-wp-rocket #wp-admin-bar-faq, #wp-admin-bar-wp-rocket #wp-admin-bar-docs {display: none !important;}.simple301redirects__documentation {margin-top: 0px !important;}.simple301redirects__topbar, .simple301redirects__panel__divider, .s3r-tooltip {display: none !important;}.wpcode-code-textarea .CodeMirror {height: 700px !important;}img.menu_pto {display: none !important;}.components-panel button.ast-custom-button-with-padding {display: none !important;}#setting-error-wc_am_client_error {display: none !important;}.plugin-description p {height: 41px;display: block;display: -webkit-box;max-width: 100%;-webkit-line-clamp: 2;-webkit-box-orient: vertical;overflow: hidden;text-overflow: ellipsis;margin-bottom: 11px;}.plugin-title .row-actions span:not(.deactivate, .activate) {display: none !important;}.plugin-title .row-actions span:not(a) {color: #fff0 !important;}.column-auto-updates a {display: none !important;}.components-panel button.ast-custom-button-with-padding {display: none !important;}.plugin-update .update-message {display: none !important;}.ss-content .ss-upgrade {display: none !important;}#toplevel_page_real-time-auto-find-and-replace-real-time-auto-find-and-replace {display: none !important;}#product_binder .panel-body .form-group.pro-version {display: none;}#product_binder .panel-body .bypass-rule {display: none;}#product_binder .panel-body .st2-wrapper {display: none;}#product_binder .panel .panel-footer, #product_binder .panel .panel-heading {display: none;}#product_binder .panel .panel-body {margin: 0px 0;padding: 0px 15px;}#product_binder input[type=search] {border: 2px solid #cacaca;border-radius: 4px;}#product_binder .panel .panel-body .button, #product_binder .btn-custom-submit {border-radius: 4px;}.toplevel_page_NinjaFirewall .wp-menu-image img {display: none !important;}.toplevel_page_NinjaFirewall .wp-menu-image:before {content: '\f332';}#toplevel_page_NinjaFirewall .wp-menu-name {visibility: hidden;font-size: 0px;}#toplevel_page_NinjaFirewall .wp-menu-name::before {content: 'Seguridad' !important;visibility:visible;font-size: 14px;}.toplevel_page_cartflows > div.wp-menu-image.svg {background: none !important;}.toplevel_page_cartflows > div.wp-menu-image.svg:before {font-family: dashicons;content: '\f536';font-size: 21px;display: inline-block;line-height: 0.9;}#toplevel_page_cartflows .wp-menu-name {visibility: hidden;font-size: 0px;}#toplevel_page_cartflows .wp-menu-name::before {content: 'Embudos' !important;visibility:visible;font-size: 14px;}div#wcf-settings-app {margin-bottom: 30em !important;}.toplevel_page_automatewoo .wp-menu-image {visibility: hidden;}.toplevel_page_automatewoo .wp-menu-image::before {visibility: visible;font-family: dashicons;content: '\f12a';font-size: 21px;display: inline-block;line-height: 1.0;}.toplevel_page_woocommerce > div.wp-menu-image.svg {background: none !important;}.toplevel_page_woocommerce > div.wp-menu-image.svg:before {font-family: dashicons;content: '\f174';font-size: 21px;display: inline-block;line-height: 0.9;}#toplevel_page_woocommerce .wp-first-item {display: none !important;}table.wp-list-table.widefat.plugins img {width: 0px !important;margin-left: 0px !important;}.wp-pusher_page_wppusher-plugins .widefat {width: 100%;max-width: 550px;}.wp-pusher_page_wppusher-themes .widefat {width: 100%;max-width: 550px;}.wp-pusher_page_wppusher-plugins .button-secondary {display: none !important;}.wp-pusher_page_wppusher-themes .button-secondary {display: none !important;}.wp-pusher_page_wppusher-plugins .plugin-version-author-uri {display: none !important;}.wp-pusher_page_wppusher-themes .plugin-version-author-uri {display: none !important;}.wp-pusher_page_wppusher-plugins .wppusher-ptd-show {display: none !important;}.wp-pusher_page_wppusher-themes .wppusher-ptd-show {display: none !important;}.wp-pusher_page_wppusher-plugins td {padding-bottom: 15px !important;}.wp-pusher_page_wppusher-themes td {padding-bottom: 15px !important;}.wp-pusher_page_wppusher-plugins .fa.fa-github {margin-left: -18px !important;visibility: hidden !important;}.wp-pusher_page_wppusher-themes .fa.fa-github {margin-left: -18px !important;visibility: hidden !important;}.wp-pusher_page_wppusher-plugins h2 {font-size: 10px !important;visibility: hidden !important;}.wp-pusher_page_wppusher-themes h2 {font-size: 10px !important;visibility: hidden !important;}.wp-pusher_page_wppusher-plugins hr {display: none !important;}.wp-pusher_page_wppusher-themes hr {display: none !important;}.wp-pusher_page_wppusher-plugins img {display: none !important;}.wp-pusher_page_wppusher-themes img {display: none !important;}.wp-pusher_page_wppusher-plugins p {visibility: hidden;}.wp-pusher_page_wppusher-themes p {visibility: hidden;}.wp-pusher_page_wppusher-plugins .updated p {visibility: visible !important;}.wp-pusher_page_wppusher-themes .updated p {visibility: visible !important;}.wp-pusher_page_wppusher-plugins thead {display: none !important;}.wp-pusher_page_wppusher-themes thead {display: none !important;}.wp-pusher_page_wppusher-plugins td {padding-top: 15px !important;}.wp-pusher_page_wppusher-themes td {padding-top: 15px !important;}

</style> <?php }




// TWP-7 CSS FRONT-END [PARTE PUBLICA]
add_action( 'wp_head', function () { ?> <style>

		/* 00 CSS */
		#wp-admin-bar-wp-rocket #wp-admin-bar-support, #wp-admin-bar-wp-rocket #wp-admin-bar-faq, #wp-admin-bar-wp-rocket #wp-admin-bar-docs {display: none !important;}.wp-submenu.wp-submenu-wrap {border-radius: 6px !important;}.ab-sub-wrapper {border-radius: 0px 0px 6px 6px !important;}#wp-admin-bar-my-account img:not(#wp-admin-bar-user-info img) {border: 3px solid #8c8f94bf !important;border-radius: 6px !important;background: #aaaaaa00 !important;}#wp-admin-bar-user-info img {border-radius: 6px !important;}

		/* 01 CSS */
		ul.sub-menu {padding: 6px;margin-top: -20px !important }span.ast-svg-iconset {color: var( --e-global-color-3f9bb60 ) !important;background-color: var( --e-global-color-b2b5419 ) !important;padding: 2px 5.5px 3.1px 5.5px !important;border-radius: 50px !important;margin-right: 12px !important;margin-top: 12px !important;transform: scale(1.3) !important;z-index: 99 !important;}.ast-mobile-popup-content ul.sub-menu {background-color: var( --e-global-color-7b03dbe ) !important;margin: 10px 15px 15px 18px !important;padding: 8px 0px !important;border-radius: 4px !important;}@media screen and (max-width: 920px) {.ast-theme-transparent-header .sub-menu .menu-link {background-color: #7c737300 !important;}.ast-mobile-popup-content ul.sub-menu li {margin: 18px 0px !important;}}.site-title a {margin-left: -5px !important;}.by-kitfunnel {border-radius: 5px !important;background-color: #FFFFFF1C !important;padding: 7px 10px !important;margin: 10px !important;}::selection {background-color: var(--ast-global-color-1) !important;color: #fff !important;}a {text-decoration: none !important;}.titulo-tw, .titulo-tw h1, .titulo-tw h2, .titulo-tw h3, .titulo-tw h4, .titulo-tw h5, .titulo-tw h6, .titulo-tw p, .titulo-tw span, .titulo-tw .elementor-icon-list-text, .titulo-tw .elementor-heading-title {font-family: var( --e-global-typography-primary-font-family ) !important;}.texto-tw, .texto-tw h1, .texto-tw h2, .texto-tw h3, .texto-tw h4, .texto-tw h5, .texto-tw h6, .texto-tw p, .texto-tw span, .texto-tw .elementor-icon-list-text, .texto-tw .elementor-heading-title {font-family: var( --e-global-typography-text-font-family ) !important;}.bton-mkt .elementor-button-text:after {color: #fff !important;margin-top: 08px !important;display: block !important;font-family: var( --e-global-typography-3dfba5d-font-family );font-size: var( --e-global-typography-3dfba5d-font-size );font-weight: var( --e-global-typography-3dfba5d-font-weight );line-height: var( --e-global-typography-3dfba5d-line-height );letter-spacing: var( --e-global-typography-3dfba5d-letter-spacing );word-spacing: var( --e-global-typography-3dfba5d-word-spacing );}p {margin-bottom: 1.3em !important;margin-top: 0px !important;}p:last-child {margin: 0px !important;}.elementor-headline.e-animated {margin: 0px !important;}.elementor-image-box-wrapper .elementor-image-box-title {margin-top: 0px !important;}.pag-legales h2, .pag-legales h3, .pag-legales h4, .pag-legales h5, .pag-legales h6 {margin-bottom: 10px;}.pag-legales p {margin-bottom: 35px;}

		/* 02 CSS */
		.woocommerce-error {background-color: #d86767 !important;}.woocommerce-info {background-color: #6e81dc !important;}.woocommerce-message {background-color: #4495ff !important;}.woocommerce-error, .woocommerce-info, .woocommerce-message {color: #fff !important;border-radius: 5px !important;border-top: 0px solid #ffffff00 !important;line-height: 1.4 !important;}.woocommerce-error .button, .woocommerce-info .button, .woocommerce-message .button {background-color: #ffffff42 !important;border-radius: 10px !important;font-weight: 600 !important;padding: 10px 20px !important;}.woocommerce-error .button:hover, .woocommerce-info .button:hover, .woocommerce-message .button:hover {background-color: #ffffff62 !important;}.woocommerce-error::before, .woocommerce-info::before, .woocommerce-message::before {color: #fff !important;scale: 1.3 !important;top: 1.1em !important;left: 1.5em !important;}.woocommerce-error a:not(.button), .woocommerce-info a:not(.button), .woocommerce-message a:not(.button) {width: auto !important;display: initial !important;color: #fff !important;font-style: italic !important;padding: 0px 1px !important;border-width: 0px 0px 2px 0px !important;border-style: solid !important;border-color: #fff !important;}a.woocommerce-Button.button, .woocommerce-button, .woocommerce .button {font-weight: 500 !important;}

		/* 03 CSS */
		.woocommerce-account .woocommerce-MyAccount-content {width: 100% !important;}.woocommerce-MyAccount-navigation {display:none !important;}.woocommerce-MyAccount-content h2 {font-family: var( --e-global-typography-f599a67-font-family ) !important;font-size: var( --e-global-typography-f599a67-font-size ) !important;font-weight: var( --e-global-typography-f599a67-font-weight ) !important;line-height: var( --e-global-typography-f599a67-line-height ) !important;letter-spacing: var( --e-global-typography-f599a67-letter-spacing ) !important;}.hero-intranet-tw {padding: 3em 1.2em 3.5em 1.2em !important;}@media screen and (max-width: 1024px) {.hero-intranet-tw {padding: 2.5em 1.2em 3em 1.2em !important;}}body:not(.logged-in) .hero-intranet-tw {padding: 3em 1.2em 8em 1.2em !important;}body:not(.logged-in) .seccion-nav-content {display: none !important;}.nav-mycuenta .elementor-icon-list-text {font-family: var( --e-global-typography-87aa81c-font-family ) !important;font-size: var( --e-global-typography-87aa81c-font-size ) !important;font-weight: var( --e-global-typography-87aa81c-font-weight ) !important;letter-spacing: var( --e-global-typography-87aa81c-letter-spacing ) !important;}.logged-in .sec-login-woo {display: none !important;}body:not(.logged-in) .fila-login-woo {margin-top: -6em !important;}.fila-login-woo .woocommerce h2 {display: none !important;}.woocommerce-form-row #password, .woocommerce-form-row #username {width: 100% !important;}.fila-login-woo .form-row {margin-bottom: 20px !important;}label.woocommerce-form__label.woocommerce-form__label-for-checkbox.woocommerce-form-login__rememberme {display: none !important;}.fila-login-woo .woocommerce-button {margin-top: 0px !important;margin-bottom: 0px !important;font-weight: 500 !important;font-size: 16px !important;width: 100% !important;padding: 15px 20px !important;color: #fff !important;}.fila-login-woo .lost_password {margin-top: -8px !important;margin-bottom: 0px !important;font-weight: 500 !important;font-size: 13px !important;text-align: center !important;}.fila-login-woo .lost_password a {color: #8e8e8e !important;}.fila-login-woo .lost_password a:hover {color: var(--ast-global-color-0) !important;}.lost_reset_password button.woocommerce-Button.button {font-weight: 500 !important;padding: 15px 20px !important;background-color: var(--ast-global-color-0) !important;color: #fff !important;}.lost_reset_password button.woocommerce-Button.button:hover {background-color: var(--ast-global-color-1) !important;color: #fff !important;}td.woocommerce-orders-table__cell.woocommerce-orders-table__cell-order-actions .woocommerce-button {padding: 7px 10px !important;border-radius: 3px !important;font-weight: 400 !important;}.tw-post-content a.woocommerce-button.button.pay {display: none !important;}.woocommerce-table__product-name a {pointer-events: none !important;color: var(--ast-global-color-2) !important;}.woocommerce-table__product-name .product-quantity {display: none !important;}.woocommerce-MyAccount-downloads-file.button.alt {padding: 10px !important;font-size: 12px !important;}.woocommerce-customer-details {display: none !important;}.order-again {display: none !important;}.subscription_details .button.cancel {padding: 7px 10px !important;border-radius: 3px !important;font-weight: 400 !important;font-size: 13px !important;}.woocommerce-orders-table--subscriptions {margin-top: 12px !important;}.shop_table th, .shop_table td {font-size: 13px !important;}@media screen and (min-width: 922px) {.my_account_orders.shop_table td {border-top: 1px solid var(--ast-border-color) !important;border-bottom: 0px solid #fff !important;border-left: 0px solid #fff !important;border-right: 0px solid #fff !important;}}@media screen and (max-width: 921px) {.my_account_orders.shop_table {border: 0px solid #fff !important;}.my_account_orders.shop_table td {border-top: 0px solid #fff !important;border-bottom: 1px solid var(--ast-border-color) !important;border-left: 0px solid #fff !important;border-right: 0px solid #fff !important;}.woocommerce-orders-table tr {border-style: solid !important;border-width: 1px 1px 0px 1px !important;border-color: var(--ast-border-color) !important;margin: 0px 0px 30px 0px !important;padding: 0px !important;border-radius: 0px !important;}}ol.woocommerce-OrderUpdates.commentlist.notes {margin: 0px;}.woocommerce-OrderUpdate.comment.note {border-width: 0px 0px 1px 0px;border-style: solid;border-color: #e8e8e8;padding-bottom: 20px;margin-bottom: 30px;padding-top: 15px;}li.woocommerce-OrderUpdate.comment.note::marker {font-size: 0px;}.woocommerce-OrderUpdate-meta.meta {color: var(--ast-global-color-2);text-align: left;font-family: var( --e-global-typography-4387e6c-font-family ) !important;font-size: var( --e-global-typography-4387e6c-font-size ) !important;font-weight: var( --e-global-typography-4387e6c-font-weight ) !important;line-height: var( --e-global-typography-4387e6c-line-height ) !important;margin-bottom: 10px !important;}p.woocommerce-OrderUpdate-meta.meta::first-letter {text-transform: uppercase !important;}.woocommerce-OrderUpdate-description.description {font-style: italic !important;font-size: 14px !important;}.edit-account em {font-size: 11px !important;font-style: normal !important;}.woocommerce-account fieldset {border: 1px solid var(--ast-border-color) !important;border-radius: 4px !important;padding: 20px 30px !important;margin-top: 40px !important;background-color: #f4f4f450 !important;}@media screen and (max-width: 1024px) {.woocommerce-account fieldset {padding: 16px 30px !important;}}.woocommerce-account fieldset legend {width: auto !important;background-color: #f4f4f460 !important;border: 1px solid var(--ast-border-color) !important;border-radius: 4px !important;padding: 02px 10px !important;}@media screen and (min-width: 1025px) {.box-izquierda-tw, .box-derecha-tw {max-width: 50% !important;}}@media screen and (min-width: 1025px) {.box-izquierda-tw {padding: 8px 15px 8px 0px !important;}.box-derecha-tw {padding: 8px 0px 8px 15px !important;}}@media screen and (max-width: 1024px) {.box-izquierda-tw, .box-derecha-tw {max-width: 100% !important;padding: 0px 0px 18px 0px !important;}}.box-izquierda-tw .elementor-cta__bg-wrapper, .box-derecha-tw .elementor-cta__bg-wrapper {border-radius: 5px !important;}.box-izquierda-tw .elementor-cta__title, .box-derecha-tw .elementor-cta__title {max-height: 100%;display: block;display: -webkit-box;max-width: 100%;-webkit-line-clamp: 2;-webkit-box-orient: vertical;overflow: hidden;text-overflow: ellipsis;}.box-izquierda-tw .elementor-button, .box-derecha-tw .elementor-button {padding: 07px 12px 06px 12px !important;margin-left: -03px !important;border-width: 0px 0px 2px 0px !important;}.form-mycuenta button {margin-top: 18px !important;}

		/* 04 CSS */
		.wcf-el-checkout-form .woocommerce-error::before, .wcf-el-checkout-form .woocommerce-info::before, .wcf-el-checkout-form .woocommerce-message::before {display: none !important;}.wcf-el-checkout-form .woocommerce-error, .wcf-el-checkout-form .woocommerce-info, .wcf-el-checkout-form .woocommerce-message {padding-bottom: 20px !important;}@media screen and (max-width: 768px) {.checkout-kitfunnel .wcf-customer-info-main-wrapper {padding: 0px 12px 10px 12px !important;}}.checkout-kitfunnel .dashicons-arrow-down-alt2:before, .checkout-kitfunnel .dashicons-arrow-up-alt2:before {color: var( --e-global-color-primary ) !important;font-weight: 800 !important;}@media screen and (max-width: 769px) {.checkout-kitfunnel .wcf-product-thumbnail {width: 30% !important;max-width: 50px !important;}}.select2-container--default .select2-results__option--highlighted[aria-selected], .select2-container--default .select2-results__option--highlighted[data-selected] {background-color: var( --e-global-color-primary ) !important;color: #fff !important;font-weight: 500 !important;}.select2-results__option, .select2-dropdown.select2-dropdown--above {font-family: var( --e-global-typography-text-font-family ) !important;}.wcipi-label.error-msg, .wcipi-label.valid-msg {display: none !important;}.iti.iti--allow-dropdown {width: 100% !important;}#billing_phone {max-height: 48px !important;}#billing_country_field label {z-index: 2 !important;}#billing_phone {padding-left: 50px !important;}#billing_phone_field label {padding-left: 50px !important;}.iti-mobile .iti__country-list {max-height: 70% !important;width: 100% !important;}.iti__country-list {position: absolute !important;z-index: 999 !important;padding: 0 !important;margin: 0 0 0 -1px !important;box-shadow: 2px 2px 5px rgb(0 0 0 / 18%) !important;background-color: #fff !important;border: 1px solid #ccc !important;border-radius: 2px !important;}.iti__country-list::-webkit-scrollbar {width: 10px;}.iti__country-list::-webkit-scrollbar-track {background: #f6f6f6;}.iti__country-list::-webkit-scrollbar-thumb {background: var(--ast-global-color-0);border-radius: 2px;}.iti__country-list::-webkit-scrollbar-thumb:hover {background: var(--ast-global-color-1);}.checkout-kitfunnel a#wcf_optimized_wcf_custom_coupon_field {font-weight: 600 !important;color: var( --e-global-color-primary ) !important;}.checkout-kitfunnel .wcf-coupon-col-1 .input-text {font-family: var( --e-global-typography-primary-font-family ) !important;font-weight: 300 !important;font-style: italic !important;}.checkout-kitfunnel .button.wcf-submit-coupon {font-weight: 600 !important;font-family: var( --e-global-typography-primary-font-family ) !important;}.wcf-customer-login-lost-password-url {font-weight: 600 !important;font-size: 14px !important;font-family: var( --e-global-typography-text-font-family ) !important;}@media screen and (max-width: 768px) {.wcf-customer-login-lost-password-url {font-size: 12px !important;padding-left: 10px !important;}}.checkout-kitfunnel .product-quantity {display: none !important;}.wcf-embed-checkout-form-modern-checkout table.shop_table td, .wcf-embed-checkout-form-modern-checkout table.shop_table th {padding: 1em 1em !important;}.wcf-order-wrap .recurring-totals {display: none !important;}.wcf-order-wrap .cart-subtotal.recurring-total {display: none !important;}.wcf-order-wrap .order-total.recurring-total {font-size: 13px !important;}.wcf-order-wrap .order-total.recurring-total {border-top: 1px solid #e5e7eb;}.checkout-kitfunnel .wcf-bump-order-wrap {margin: 10px 0px 35px 0px !important;}.checkout-kitfunnel .wcf-bump-order-style-2 .wcf-bump-order-bump-highlight {font-size: 16px !important;font-weight: 500 !important;font-family: var( --e-global-typography-primary-font-family ) !important;}.checkout-kitfunnel .wcf-bump-order-style-2 .wcf-bump-order-desc {font-size: 14px !important;}.checkout-kitfunnel .wcf-bump-order-field-wrap {font-weight: 600 !important;font-family: var( --e-global-typography-primary-font-family ) !important;}@media screen and (max-width: 520px) {.checkout-kitfunnel .wcf-bump-order-label {font-size: 14px;}}.checkout-kitfunnel .wcf-bump-order-offer-content-left.wcf-bump-order-image {align-self: auto !important;}.checkout-kitfunnel .wcf-bump-order-offer-content-left.wcf-bump-order-image {padding: 3px 0px 20px 24px !important;}@media screen and (max-width: 520px) {.checkout-kitfunnel .wcf-bump-order-offer-content-left.wcf-bump-order-image {max-width: 100px !important;padding: 3px 0px 10px 25px !important;}}.checkout-kitfunnel .wcf-bump-order-style-2 .wcf-bump-order-offer-content-left img {padding: 0px !important;border-radius: 04px !important;}

		/* 05 CSS */
		.detalles-pedido-thankyou p {display: none !important;}.detalles-pedido-thankyou .woocommerce-MyAccount-subscriptions {display: none !important;}.detalles-pedido-thankyou h2 {display: none !important;}.wcf-thankyou-wrap .woocommerce-table th {color: var( --e-global-color-secondary ) !important;font-weight: 500 !important;font-family: var( --e-global-typography-primary-font-family ) !important;}.wcf-thankyou-wrap .woocommerce-order-overview, .wcf-thankyou-wrap .woocommerce-order-downloads, .wcf-thankyou-wrap .woocommerce-order-details, .wcf-thankyou-wrap .woocommerce-customer-details {border-radius: 06px !important;padding-bottom: 30px !important;}.wcf-thankyou-wrap .download-product a {pointer-events: none !important;color: var( --e-global-color-text ) !important;}.wcf-thankyou-wrap strong.product-quantity {display: none !important;}.wcf-thankyou-wrap .product-purchase-note td {display: none !important;}.wcf-thankyou-wrap .order-again {display: none !important;}@media screen and (min-width: 769px) {.wcf-thankyou-wrap .shop_table_responsive td {padding: 5px 0px !important;}}@media screen and (max-width: 768px) {.wcf-thankyou-wrap .shop_table_responsive tr:nth-child(2n) {background-color: #efefef !important;border-radius: 05px !important;}.wcf-thankyou-wrap .shop_table_responsive tr:nth-child(2n) td {background-color: #efefef !important;}.wcf-thankyou-wrap .shop_table_responsive tr {padding: 6px 8px !important;border-radius: 05px !important;margin: 0px -14px !important;}}

		/* 06 CSS */
		.sec-kitfunnel-cont {padding: 0em 1.5em !important;max-width: 750px !important;margin: auto !important;}.col-kitfunnel-cont, .col-kitfunnel-cont .elementor-widget-wrap.elementor-element-populated {margin: 0px !important;padding: 0px !important;}.col-kitfunnel-cont .tw-temario-kitfunnel {margin-bottom: 06em !important;}@media screen and (max-width: 600px) {.col-kitfunnel-cont .tw-temario-kitfunnel {margin-bottom: 3.5em !important;}}.tw-desc-content strong {color: var( --e-global-color-astglobalcolor2 ) !important;font-weight: 600;}.tw-desc-content p a {color: var(--ast-global-color-0);border-width: 0px 0px 2px 0px !important;border-style: solid !important;border-color: var(--ast-global-color-0) !important;}#tw-nav-cont .elementor-post-navigation .elementor-post-navigation__link {width: auto !important;z-index: 2 !important;}.tw_mostrar_siguiente .post-navigation__prev--label, .tw_mostrar_anterior .post-navigation__next--label {display: none !important;}.tw_mostrar_anterior .post-navigation__prev--label, .tw_mostrar_siguiente .post-navigation__next--label {font-family: var( --e-global-typography-f9cd581-font-family ) !important;font-weight: var( --e-global-typography-f9cd581-font-weight ) !important;letter-spacing: var( --e-global-typography-f9cd581-letter-spacing ) !important;text-transform: none !important;font-size: 17px !important;background-color: var( --e-global-color-710c118 ) !important;color: var( --e-global-color-primary ) !important;padding: 12px 25px !important;border-radius: 5px !important;}.tw_mostrar_anterior .post-navigation__prev--label:hover, .tw_mostrar_siguiente .post-navigation__next--label:hover {background-color: var( --e-global-color-1753576 ) !important;color: var( --e-global-color-3be920f ) !important;transition-duration: 0.7s !important;}@media screen and (max-width: 600px) {.tw_mostrar_anterior .post-navigation__prev--label, .tw_mostrar_siguiente .post-navigation__next--label {font-size: 15px !important;padding: 6px 12px !important;max-width: 70px !important;}.tw_mostrar_anterior, .tw_mostrar_siguiente {bottom: 9px !important;}}.wp-complete-content {margin-top: 0px !important;text-align: center !important;}@media screen and (max-width: 600px) {.wp-complete-content {margin-top: 0px !important;}}a.wpc-complete, a.wpc-completed {font-family: var( --e-global-typography-f9cd581-font-family ) !important;font-weight: var( --e-global-typography-f9cd581-font-weight ) !important;letter-spacing: var( --e-global-typography-f9cd581-letter-spacing ) !important;font-size: 16px !important;border-radius: 4px !important;padding: 14px 25px !important;}@media screen and (max-width: 600px) {a.wpc-complete, a.wpc-completed {padding: 10px 25px !important;}}.wp-complete-barra .wpc-bar-progress .wpc-numbers {display: none !important;}.wp-complete-barra .wpc-bar-progress .wpc-progress-fill {height: 08px !important;}.wp-complete-barra .wpc-bar-progress.wpc-rounded .wpc-progress-fill {border-radius: 3px !important;}.tw-num-complet {font-size: 16px !important;font-weight: 400 !important;color: var( --e-global-color-text ) !important;letter-spacing: 0.2px !important;}.tw-temario-kitfunnel .elementor-accordion-icon-left {font-size: 19px !important;float: none !important;display: block !important;margin-bottom: -23px !important;margin-left: -30px !important;}.tw-temario-kitfunnel p em:before {content: url(/wp-content/plugins/adminpress-wp/img/file-video-min.svg) !important;}.tw-temario-kitfunnel p strong:before {content: url(/wp-content/plugins/adminpress-wp/img/file-lines-min.svg) !important;}.tw-temario-kitfunnel p del:before {content: url(/wp-content/plugins/adminpress-wp/img/file-zipper-min.svg) !important;}.tw-temario-kitfunnel p em:before, .tw-temario-kitfunnel p strong:before, .tw-temario-kitfunnel p del:before {display: block !important;width: 15px !important;margin-bottom: -28px !important;margin-left: -29px !important;opacity: 40% !important;}.tw-temario-kitfunnel p em, .tw-temario-kitfunnel p strong, .tw-temario-kitfunnel p del {text-decoration: none !important;font-weight: 400 !important;font-style: normal !important;}.tw-temario-kitfunnel p a {padding: 6px !important;color: var( --e-global-color-4999739 ) !important;font-family: var( --e-global-typography-144ee0d-font-family ) !important;font-weight: var( --e-global-typography-144ee0d-font-weight ) !important;text-transform: var( --e-global-typography-144ee0d-text-transform ) !important;font-style: var( --e-global-typography-144ee0d-font-style ) !important;text-decoration: var( --e-global-typography-144ee0d-text-decoration ) !important;}.tw-temario-kitfunnel .elementor-accordion-item {background-color: #fff !important;margin: 0px 0px 15px 0px !important;border-radius: 6px !important;border-width: 2px !important;border-style: solid !important;border-color: var( --e-global-color-5377d93 ) !important;}.tw-temario-kitfunnel .elementor-tab-title {background-color: var( --e-global-color-57a0d3e ) !important;}.tw-temario-kitfunnel .elementor-tab-title.elementor-active {border-width: 0px 0px 2px 0px !important;border-style: solid !important;border-color: var( --e-global-color-5377d93 ) !important;background-color: var( --e-global-color-57a0d3e ) !important;border-radius: 6px 06px 0px 0px !important;}.tw-temario-kitfunnel .elementor-tab-content {border-radius: 0px 0px 6px 6px !important;border-width: 0px !important;border-style: solid !important;border-color: var( --e-global-color-5377d93 ) !important;background-color: #fff !important;}ul.wpc-list {list-style: none !important;}ul.wpc-list a::before {content: "Clase ";}ul.wpc-list {margin: -47px 0px 0px 0px;}.tw-temario-kitfunnel .wpc-list em, .tw-temario-kitfunnel .wpc-list strong, .tw-temario-kitfunnel .wpc-list del {text-decoration: none !important;font-weight: 400 !important;font-style: normal !important;}.tw-temario-kitfunnel .wpc-list a {color: #4b4f58 !important;font-family: var( --e-global-typography-primary-font-family ) !important;font-weight: 300 !important;}.tw-temario-kitfunnel .wpc-list a:hover {color: var( --e-global-color-4999739 ) !important;}

		/* 08 CSS */
		.img-destacada-tw img {max-height: 370px !important;object-fit: cover !important;}.post-tw {padding: 2.5em 3em 3em 3em !important;}@media screen and (max-width: 1024px) {.post-tw {padding: 3em 3em 3em 3em !important;}}@media screen and (max-width: 768px) {.post-tw {padding: 2.2em 2.2em 2.5em 2.2em !important;}}@media screen and (max-width: 560px) {.post-tw {padding: 1.5em 1.4em 1.8em 1.4em !important;}}.post-tw h2, .post-tw h3, .post-tw h4, .post-tw h5, .post-tw h6 {padding: 25px 0px 15px 0px !important;font-weight: 600 !important;}.post-tw h2 {font-size: 22px !important;}.post-tw h3 {font-size: 20px !important;}.post-tw h4 {font-size: 19px !important;}.post-tw h5, .post-tw h6 {font-size: 18px !important;}.post-tw img {border-radius: 4px !important;margin: 10px 0px 15px 0px !important;}.post-tw p strong {font-weight: 500 !important;color: #000 !important;}.post-tw p a {color: var(--ast-global-color-0);border-width: 0 0 2px!important;border-style: solid!important;border-color: var(--ast-global-color-0)!important;}.post-blog-tw .elementor-post__avatar img {border-radius: 6px !important;}.post-blog-tw .elementor-pagination a {text-decoration: none !important;}


</style> <?php } );

/* /////////////////////////////////////////////////// */



?>