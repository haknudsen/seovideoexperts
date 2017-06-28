<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package WP_Bootstrap_Starter
 */

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="http://gmpg.org/xfn/11">
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
<script src="https://use.typekit.net/pln8pyk.js"></script>
<script>try{Typekit.load({ async: true });}catch(e){}</script>
<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'wp-bootstrap-starter' ); ?></a>
    <?php if(!is_page_template( 'blank-page.php' )): ?>
	<header id="masthead" class="site-header navbar navbar-static-top" role="banner">
    <div id="page-sub-header" style="background-image: url('<?php if(has_header_image()) { header_image(); } ?>');">
        <div class="container">
            <h1><?php esc_url(bloginfo('name')); ?></h1>
            <p><?php bloginfo( 'description'); ?></p>
        </div>
		    <!-- Brand and toggle get grouped for better mobile display -->
		    <div class="navbar-header">
		      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
		        <span class="sr-only"><?php echo esc_html__('Toggle navigation', 'wp-bootstrap-starter'); ?></span>
		        <span class="icon-bar"></span>
		        <span class="icon-bar"></span>
		        <span class="icon-bar"></span>
		      </button>
            </div>

		    <nav class="collapse navbar-collapse navbar-right" role="navigation">

		        <?php
		            wp_nav_menu( array(
		                'theme_location'    => 'primary',
		                'depth'             => 3,
                        'link_before' => '', //Add wrapper in the text inside the anchor tag
                        'link_after' => '',
		                'container'         => '',
		                'container_class'   => '',
		        		'container_id'      => 'navbar-collapsed',
		                'menu_class'        => 'nav navbar-nav',
		                'fallback_cb'       => 'wp_bootstrap_navwalker::fallback',
		                'walker'            => new wp_bootstrap_navwalker())
		            );
		        ?>

			</nav>
    </div>
		
	</header><!-- #masthead -->
	<div id="content" class="site-content">
		<div class="container">
			<div class="row">
                <?php endif; ?>
