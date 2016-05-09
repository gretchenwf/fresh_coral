<?php
/**
 * Template part for displaying a truncated version of single posts.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Fresh Coral
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class($class = 'post-snippet col-xs-12 col-md-6'); ?>>


	<?php
		if ( has_post_thumbnail() ) { ?>
			<figure class="featured-image">
				<?php if ( $first_post == true ) { ?>
					<a href="<?php echo esc_url( get_permalink() ); ?>" rel="bookmark">
						<?php the_post_thumbnail(); ?>
					</a>
				<?php } else {
					the_post_thumbnail();
				}
				?>
			</figure>
		<?php }
		?>
    <header class="entry-header">
  		<h3><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h3>
  	</header><!-- .entry-header -->
    <div class="entry-content">
  		<?php the_excerpt(); ?>
  	</div><!-- .entry-content -->
</article><!-- #post-## -->
