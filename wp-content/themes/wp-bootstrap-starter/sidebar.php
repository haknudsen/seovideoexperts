<?php
/**
 * The sidebar containing the main widget area
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package WP_Bootstrap_Starter
 */

if ( ! is_active_sidebar( 'sidebar-1' ) ) {
	return;
}
?>

<aside id="secondary" class="widget-area col-sm-12 col-md-12 col-lg-4" role="complementary">
	<?php dynamic_sidebar( 'sidebar-1' ); ?>
	<h3>RSS Feeds</h3>
  <ul class="nav flex-column">
	    <li class="cat-item cat-item-2 nav-item"><a href="https://www.youtube.com/feeds/videos.xml?channel_id=UC4QvzMZNTDEnNoRC51rXWrA%20" title="YouTube Channel" target="_blank">SEO Video Experts Channel</a></li>
	    <li class="cat-item cat-item-2 nav-item"><a href="http://seovideoexperts.com?feed=video-feed" title="Site Videos" target="_blank">Site Videos</a></li>
	    <li class="cat-item cat-item-2 nav-item"><a href="https://www.youtube.com/feeds/videos.xml?playlist_id=PLX8HdwgJpLfQbmTcnUsg41InF2UV6fwmO" title="Teaser Playlist" target="_blank">Teaser Playlist</a></li>
	    <li class="cat-item cat-item-2 nav-item"><a href="https://www.youtube.com/feeds/videos.xml?playlist_id=PLX8HdwgJpLfRFDR6cgQNVjwhUSo2Si3GI" title="Video Spokespeople Playlist" target="_blank">Video Spokespeople Playlist</a></li>
	    <li class="cat-item cat-item-2 nav-item"><a href="https://www.youtube.com/feeds/videos.xml?playlist_id=PLX8HdwgJpLfQ2wdE7lLD0RSfekYRW4iP-" title="Videos We Like" target="_blank">Videos We Like</a></li>
	</ul>
</aside><!-- #secondary -->
