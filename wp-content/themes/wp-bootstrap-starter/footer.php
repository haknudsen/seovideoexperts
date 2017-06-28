<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package WP_Bootstrap_Starter
 */

?>
<?php if(!is_page_template( 'blank-page.php' ) && !is_page_template( 'blank-page-with-container.php' )): ?>
			</div><!-- .row -->
		</div><!-- .container -->
	</div><!-- #content -->
    <?php get_template_part( 'footer-widget' ); ?>
	<footer id="colophon" class="site-footer" role="contentinfo">
		<div class="container">
            <div class="site-info text-center">
            <div class="row">
                <div class="col-sm-6">
            <h4 class="footer-contact"><i class="fa fa-envelope-o"></i><br/><a href="mailto:info@websitetalkingheads.com">info@websitetalkingheads.com</a></span> </h4>
                </div>
                <div class="col-sm-6">
                <h4 class="footer-contact"><i class="fa fa-phone"></i> <br/>
            <span><a href="tel://801-748-2281" title="Give us a call." >801-748-2281</a></span></span></h4>
                </div>
            </div>
          
  <h3 class="copyright"><a href="http://seovideoexperts.com/"><sup class='tm-small'>&#169;</sup>SEO Video Experts <?php echo date("Y")?>. All rights reserved.</a></h3>
           <h3><a href="http://talkingheads.com/affiliate/">Powered by Talkingheads<sup class='tm-small'>&#169;</sup></a></h3>
          </div><!-- close .site-info -->
		</div>
	</footer><!-- #colophon -->
<?php endif; ?>
</div><!-- #page -->

<?php wp_footer(); ?>
</body>
</html>
