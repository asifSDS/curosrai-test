<?php
class Shortcodes {
	public function __construct() {
		add_filter( 'wp', [ $this, 'has_shortcode' ] );
		add_shortcode( 'carleader_listings_listing', [ $this, 'listing' ] );
		add_shortcode( 'carleader_listings_listings', [ $this, 'listings' ] );
		add_shortcode('caleader_listing_submit', [$this, 'listing_submit'] );
	}
	/**
	 * Check if we have the shortcode displayed
	 */
	public function has_shortcode() {
		global $post;
		if ( ! is_a( $post, 'WP_Post' ) ) {
			return;
		}
		if ( has_shortcode( $post->post_content, 'carleader_listings_listings' ) ||
		     has_shortcode( $post->post_content, 'carleader_listings_listings' ) ||
		     has_shortcode( $post->post_content, 'carleader_listings_listings' )
		) {
			add_filter( 'is_auto_listings', '__return_true' );
		}
		if ( has_shortcode( $post->post_content, 'carleader_listings_listings' ) ) {
			add_filter( 'is_listing', '__return_true' );
		}
	}
	/**
	 * Display a single listing.
	 *
	 * @param array $atts
	 *
	 * @return string
	 */
	public function listing( $atts ) {
		$atts = shortcode_atts( [
			'id' => 0,
		], $atts );
		$args  = [
			'post_type'      => 'carleader-listing',
			'posts_per_page' => 1,
			'no_found_rows'  => 1,
			'post_status'    => 'publish',
			'p'              => $atts['id'],
		];
		$query = new \WP_Query( apply_filters( 'carleader_listings_shortcode_listing_query', $args, $atts ) );
		if ( ! $query->have_posts() ) {
			return '';
		}
		ob_start();
		?>
		<div id="listing-<?php the_ID(); ?>" class="carleader-listings-single">
			<?php while ( $query->have_posts() ) : $query->the_post(); ?>
				// <?php auto_listings_get_part( 'content-single-listing.php' ); ?>
			<?php endwhile; // end of the loop. ?>
		</div>
		<?php
		wp_reset_postdata();
		return ob_get_clean();
	}
	/**
	 * List multiple listings shortcode.
	 *
	 * @param array $atts
	 *
	 * @return string
	 */
	public function listings( $atts ) {
		$atts = shortcode_atts( [
			'orderby' => 'date',
			'order'   => 'asc',
			'number'  => '20',
			'seller'  => '', // id of the seller
			'ids'     => '',
			'compact' => '',
		], $atts );
		$query_args = [
			'post_type'           => 'carleader-listing',
			'post_status'         => 'publish',
			'ignore_sticky_posts' => 1,
			'orderby'             => $atts['orderby'],
			'order'               => $atts['order'],
			'posts_per_page'      => $atts['number'],
		];
		if ( ! empty( $atts['ids'] ) ) {
			$query_args['post__in'] = array_map( 'trim', explode( ',', $atts['ids'] ) );
		}
		// if we are in compact mode
		if ( ! empty( $atts['compact'] ) && $atts['compact'] == 'true' ) {
			remove_action( 'auto_listings_listings_loop_item', 'auto_listings_template_loop_at_a_glance', 40 );
			remove_action( 'auto_listings_listings_loop_item', 'auto_listings_template_loop_description', 50 );
			add_filter( 'post_class', [ $this, 'listings_compact_mode' ] );
		}
		return $this->listing_loop( $query_args, $atts, 'listings' );
	}

	// ---------------************Submit from**************---------------

	public function listing_submit( $atts ) { ?>
		
					<form action="/localWp/caleader/#wpcf7-f801-o2" method="post" class="wpcf7-form init" novalidate="novalidate" data-status="init">
					<h5 class="modal-title">Add your Item</h5>
					<p class="tt-default-color02">Trading in your current car can help serve as a springboard into your new one. One of our team members will be in touch with a quote for your trade in right away.</p>
					<div class="tt-form-default02">
					<div class="form-group"><span class="your-name"><input type="text" name="your-name" value="" size="40" class="form-control" aria-required="true" aria-invalid="false" placeholder="Your name*"></span></div>
					<div class="row">
					<div class="col-12 col-sm-6">
					<div class="form-group"><span class="your-email"><input type="email" name="your-email" value="" size="40" class="form-control" aria-required="true" aria-invalid="false" placeholder="E-mail*"></span></div>
					</div>
					<div class="col-12 col-sm-6">
					<div class="form-group"><span class="your-phone"><input type="text" name="your-phone" value="" size="40" class="form-control" aria-required="true" aria-invalid="false" placeholder="Phone*"></span></div>
					</div>
					</div>
					</div>
					<h6 class="tt-title">Vehicle Info</h6>
					<div class="form-group tt-row-select tt-skinSelect-01">
					<span class="Year"><div class="SumoSelect sumo_Year" tabindex="0" role="button" aria-expanded="false"><select name="Year" class="SumoUnder" aria-invalid="false" tabindex="-1"><option value="Year">Year</option><option value="2014">2014</option><option value="2015">2015</option><option value="2016">2016</option><option value="2017">2017</option><option value="2018">2018</option></select></div></span><br>
					<span class=select-make"><div class="SumoSelect sumo_select-make" tabindex="0" role="button" aria-expanded="false"><select name="select-make" class="tt-select SumoUnder" aria-invalid="false" tabindex="-1"><option value="Select a make">Select a make</option><option value="KIA">KIA</option><option value="CHEVROLET">CHEVROLET</option><option value="VOLKSWAGEN">VOLKSWAGEN</option><option value="AUDI">AUDI</option><option value="NISSAN">NISSAN</option></select></div></span><br>
					<span class="select-model"><div class="SumoSelect sumo_select-model" tabindex="0" role="button" aria-expanded="false"><select name="select-model" class="tt-select SumoUnder" aria-invalid="false" tabindex="-1"><option value="SELECT a model">SELECT a model</option><option value="SEDAN">SEDAN</option><option value="HATCHBACK">HATCHBACK</option><option value="SPORT CARS">SPORT CARS</option><option value="SUV">SUV</option><option value="PICKUPS">PICKUPS</option></select></div></span><br>
					<span class="milage"><div class="SumoSelect sumo_milage" tabindex="0" role="button" aria-expanded="false"><select name="milage" class="wpcf7-form-control wpcf7-select tt-select SumoUnder" aria-invalid="false" tabindex="-1"><option value="Milage">Milage</option><option value="10000 miles">10000 miles</option><option value="50000 miles">50000 miles</option><option value="100000 miles">100000 miles</option></select></div></span>
					</div>
					<div class="tt-row-radio">
					<div class="tt-title">Exterior Condition*</div>
					<div><span class="wpcf7-form-control-wrap radio-814"><span class="wpcf7-form-control wpcf7-radio" id="radio-814"><span class="wpcf7-list-item first last"><label><input type="radio" name="radio-814" value="Clean" checked="checked"><span class="wpcf7-list-item-label">Clean</span></label></span></span></span></div>
					<div><span class="wpcf7-form-control-wrap radio-814"><span class="wpcf7-form-control wpcf7-radio" id="radio-815"><span class="wpcf7-list-item first last"><label><input type="radio" name="radio-814" value="Average" checked="checked"><span class="wpcf7-list-item-label">Average</span></label></span></span></span></div>
					<div><span class="wpcf7-form-control-wrap radio-814"><span class="wpcf7-form-control wpcf7-radio" id="radio-816"><span class="wpcf7-list-item first last"><label><input type="radio" name="radio-814" value="Rough" checked="checked"><span class="wpcf7-list-item-label">Rough</span></label></span></span></span></div>
					</div>
					<div class="tt-row-radio">
					<div class="tt-title">Interior Condition*</div>
					<div><span class="wpcf7-form-control-wrap radio-815"><span class="wpcf7-form-control wpcf7-radio" id="radio-817"><span class="wpcf7-list-item first last"><label><input type="radio" name="radio-815" value="Clean" checked="checked"><span class="wpcf7-list-item-label">Clean</span></label></span></span></span></div>
					<div><span class="wpcf7-form-control-wrap radio-815"><span class="wpcf7-form-control wpcf7-radio" id="radio-818"><span class="wpcf7-list-item first last"><label><input type="radio" name="radio-815" value="Average" checked="checked"><span class="wpcf7-list-item-label">Average</span></label></span></span></span></div>
					<div><span class="wpcf7-form-control-wrap radio-815"><span class="wpcf7-form-control wpcf7-radio" id="radio-819"><span class="wpcf7-list-item first last"><label><input type="radio" name="radio-815" value="Rough" checked="checked"><span class="wpcf7-list-item-label">Rough</span></label></span></span></span></div>
					</div>
					<div class="tt-row-radio">
					<div class="tt-title">Been in Accident?*</div>
					<div><span class="wpcf7-form-control-wrap radio-816"><span class="wpcf7-form-control wpcf7-radio" id="radio-820"><span class="wpcf7-list-item first last"><label><input type="radio" name="radio-816" value="Yes" checked="checked"><span class="wpcf7-list-item-label">Yes</span></label></span></span></span></div>
					<div><span class="wpcf7-form-control-wrap radio-816"><span class="wpcf7-form-control wpcf7-radio" id="radio-821"><span class="wpcf7-list-item first last"><label><input type="radio" name="radio-816" value="No" checked="checked"><span class="wpcf7-list-item-label">No</span></label></span></span></span></div>
					</div>
					<h6 class="tt-title">Upload your car Photos</h6>
					<div class="input-group tt-input-file"><label class="input-group-btn"><span class="tt-btn-icon">choose file... <input type="file" style="display: none;" multiple=""></span></label><input type="text" readonly=""></div>
					<div>
					<span class="wpcf7-form-control-wrap checkbox-additem"><span class="wpcf7-form-control wpcf7-checkbox wpcf7-validates-as-required"><span class="wpcf7-list-item first last"><label><input type="checkbox" name="checkbox-additem[]" value="I agree to receive emails from caleader"><span class="wpcf7-list-item-label">I agree to receive emails from caleader</span></label></span></span></span>
					</div>
					<div><input type="submit" value="make an offer" class="wpcf7-form-control has-spinner wpcf7-submit btn"><span class="wpcf7-spinner"></span></div>
					<div class="wpcf7-response-output" aria-hidden="true"></div></form>
<?php } 



	
	/**
	 * Add the compact class to the listings
	 *
	 * @param array $classes Post classes.
	 *
	 * @return array
	 */
	public function listings_compact_mode( $classes ) {
		$classes[] = 'compact';
		return $classes;
	}
	/**
	 * Loop over found listings.
	 *
	 * @param  array  $query_args
	 * @param  array  $atts
	 * @param  string $loop_name
	 *
	 * @return string
	 */
	protected function listing_loop( $query_args, $atts, $loop_name ) {
		$query = new \WP_Query( apply_filters( 'carleader_listings_shortcode_listings_query', $query_args, $atts, $loop_name ) );
		if ( ! $query->have_posts() ) {
			ob_start();
			do_action( "carleader_listings_shortcode_{$loop_name}_loop_no_results" );
			return ob_get_clean();
		}
		ob_start();
		?>
		<?php do_action( "carleader_listings_shortcode_before_{$loop_name}_loop" ); ?>
		<ul class="carleader-listings-items">
			<?php while ( $query->have_posts() ) : $query->the_post(); ?>
				<?php auto_listings_get_part( 'content-listing.php' ); ?>
			<?php endwhile; ?>
		</ul>
		<?php do_action( "carleader_listings_shortcode_after_{$loop_name}_loop" ); ?>
		<?php
		wp_reset_postdata();
		return ob_get_clean();
	}
}
new Shortcodes();