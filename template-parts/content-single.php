<?php
/**
 * Template part for displaying single posts.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Fresh Coral
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class($class = 'col-xs-12'); ?>>
	<header class="entry-header">
		<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>

		<div class="entry-meta">
			<?php fresh_coral_posted_on(); ?>
		</div><!-- .entry-meta -->
	</header><!-- .entry-header -->

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

	<div class="entry-content">
		<?php the_content(); ?>
		<?php
			wp_link_pages( array(
				'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'fresh_coral' ),
				'after'  => '</div>',
			) );
		?>
	</div><!-- .entry-content -->
<hr />
	<footer class="entry-footer">
		<?php fresh_coral_entry_footer(); ?>
	</footer><!-- .entry-footer -->
</article><!-- #post-## -->
