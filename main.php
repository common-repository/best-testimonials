<?php

/*
Plugin Name: Best Testimonials
Plugin URI: http://www.besos.dk/
Description: A nice testimonials listing plugin
Tags: Testimonials, post, list, grid
Version: 1.0.0
Author: Kjeld Hansen
Author URI: #
Requires at least: 4.0
Tested up to: 4.7
Text Domain: best-testimonials
*/

//function prefix btwp

if ( ! defined( 'ABSPATH' ) ) exit; 
 

add_action('init', 'btwp_testimonials_register');

function btwp_testimonials_register() {
	$labels = array(
		'name' => _x('Testimonials', 'post type general name', 'smts'),
		'singular_name' => _x('Testimonial', 'post type singular name', 'smts'),
		'add_new' => _x('Add New Testimonial', 'testimonial item', 'smts'),
		'add_new_item' => __('Add New Testimonial', 'smts'),
		'edit_item' => __('Edit Testimonial', 'smts'),
		'new_item' => __('New Testimonial', 'smts'),
		'view_item' => __('View Testimonial', 'smts'),
		'search_items' => __('Search Testimonials', 'smts'),
		'not_found' =>  __('Nothing found', 'smts'),
		'not_found_in_trash' => __('Nothing found in Trash', 'smts'),
		'parent_item_colon' => ''
	);
 
	$args = array(
		'labels' => $labels,
		'public' => true,
		'publicly_queryable' => true,
		'show_ui' => true,
		'query_var' => true,
 		'rewrite' => array(
 			'slug' => 'testimonial',
 			'with_front' => false
		 ),
		'capability_type' => 'post',
		'hierarchical' => false,
		'menu_position' => 20,
		//'menu_icon' => '/images/icons/testimonial.png', // 16px16
		'supports' => array('title','editor','thumbnail','excerpt' )
	  ); 
 
	register_post_type( 'testimonial' , $args );
}


// Testimonials Options
function btwp_testimonial_options() {
	global $post;
	?>
	<fieldset>
		<input type="hidden" name="saveTestimonial" id="saveTestimonial" value="1" />
		<div>
			<p>
				<label for="smts_testimonial_author">Testimonial Author:</label><br />
				<input type="text" style="width:90%;" name="smts_testimonial_author" id="smts_testimonial_author" value="<?php echo get_post_meta($post->ID, 'smts_testimonial_author', true); ?>"><br />
			</p>
			<p>
				<label for="smts_testimonial_author_position">Author Position:</label><br />
				<input type="text" style="width:90%;" name="smts_testimonial_author_position" id="smts_testimonial_author_position" value="<?php echo get_post_meta($post->ID, 'smts_testimonial_author_position', true); ?>"><br />
				<span class="description">Example: CEO &amp; Founder</span>
			</p>
			<p>
				<label for="smts_testimonial_author_company">Author Company:</label><br />
				<input type="text" style="width:90%;" name="smts_testimonial_author_company" id="smts_testimonial_author_company" value="<?php echo get_post_meta($post->ID, 'smts_testimonial_author_company', true); ?>"><br />
				<span class="description">Example: smts</span>
			</p>
			<p>
				<label for="smts_testimonial_author_company_url">Author Company Link:</label><br />
				<input type="text" style="width:90%;" name="smts_testimonial_author_company_url" id="smts_testimonial_author_company_url" value="<?php echo get_post_meta($post->ID, 'smts_testimonial_author_company_url', true); ?>"><br />
				<span class="description">Example: http://www.author.com</span>
			</p>
			
  		</div>
	</fieldset>
	<?php
	}
	
add_action('save_post', 'custom_add_save');

function btwp_custom_add_save($postID){

	if ($_POST['saveTestimonial']) {
		btwp_update_custom_meta($postID, sanitize_text_field($_POST['smts_testimonial_author']), 'smts_testimonial_author');
		btwp_update_custom_meta($postID, sanitize_text_field($_POST['smts_testimonial_author_position']), 'smts_testimonial_author_position');
		btwp_update_custom_meta($postID, sanitize_text_field($_POST['smts_testimonial_author_company']), 'smts_testimonial_author_company');
		btwp_update_custom_meta($postID, sanitize_text_field($_POST['smts_testimonial_author_company_url']), 'smts_testimonial_author_company_url');
	}
	
	
}


function btwp_update_custom_meta($postID, $newvalue, $field_name) {
	// To create new meta
	if (!get_post_meta($postID, $field_name)) {
 		add_post_meta($postID, $field_name, $newvalue);
	} else {
		// or to update existing meta
		update_post_meta($postID, $field_name, $newvalue);
	}
}

add_action('admin_menu', 'btwp_options_box');

function btwp_options_box() {
   	add_meta_box('smts_testimonial_options', 'Testimonial Options', 'btwp_testimonial_options', 'testimonial', 'side', 'high');
}


add_shortcode('best-testimonials', 'btwp_best_testimonials_fun');
function btwp_best_testimonials_fun($args){
	$number = $agrs[num];
	$get_featured_posts = new WP_Query( array(
            'posts_per_page'        => $number,
            'post_type'             => 'testimonial',
            'ignore_sticky_posts'   => true
         ) );
		 
	 $i=1;
	 echo '<div class="following-post-wrap"  >';
         while( $get_featured_posts->have_posts() ):$get_featured_posts->the_post();
            ?>
            <?php if( $i == 0 ) { $featured = 'postGrid-featured-post-medium'; } else { $featured = 'postGrid-featured-post-small'; } ?>
            <?php if( $i == 0 ) { echo '<div class="first-post">'; } elseif ( $i == 1 ) { echo '<div class="following-post style1">'; } ?>
               <div class="single-article clearfix">
                  <?php
                  if( has_post_thumbnail() ) {
                     $image = '';
                     $title_attribute = get_the_title( $post->ID );
                     $image .= '<figure>';
                     $image .= '<a href="' . get_permalink() . '" title="'.the_title( '', '', false ).'">';
                     $image .= get_the_post_thumbnail( $post->ID, $featured, array( 'title' => esc_attr( $title_attribute ), 'alt' => esc_attr( $title_attribute ) ) ).'</a>';
                     $image .= '</figure>';
                     echo $image;
                  }
                  ?>
                  <div class="article-content">
                     <h3 class="entry-title">
                        <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute();?>"><?php the_title(); ?></a>
                     </h3>
                     <div class="below-entry-meta">
                        <?php
                           $time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time>';
                           $time_string = sprintf( $time_string,
                              esc_attr( get_the_date( 'c' ) ),
                              esc_html( get_the_date() )
                           );
                           printf( __( '<span class="posted-on_"><a href="%1$s" title="%2$s" rel="bookmark"><i class="fa fa-calendar-o"></i> %3$s</a></span>', 'postGrid' ),
                              esc_url( get_permalink() ),
                              esc_attr( get_the_time() ),
                              $time_string
                           );
                        
						$pmeta = get_post_meta(get_the_id());
						//print_r($pmeta);
						?>
                        <b>|</b>
                        <span class="author vcard"><i class="fa fa-user"></i><a class="url fn n" href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>" title="<?php echo get_the_author(); ?>"><?php echo $pmeta[smts_testimonial_author][0]; ?></a></span>
                        <b>|</b>
                        <span>
                        	Position : <?php echo $pmeta[smts_testimonial_author_position][0]; ?>
                        </span>
                        <b>|</b>
                        
                        <span>
                        	Company : <?php echo $pmeta[smts_testimonial_author_company][0]; ?>
                        </span>
                        <b>|</b>
                        
                        <span>
                        	URL : <?php echo $pmeta[smts_testimonial_author_company_url][0]; ?>
                        </span>
                        
                     </div>
                     <?php if( $i == 1 ) { ?>
                     <div class="entry-content">
                        <?php the_excerpt(); ?>
                        
                     </div>
                     <?php } ?>
                  </div>

               </div>
            <?php if( $i == 0 ) { echo '</div>'; } ?>
         <?php
            $i++;
         endwhile;
         if ( $i > 1 ) { echo '</div>'; }
         // Reset Post Data
         wp_reset_query();
         ?>
         </div>
         <?php
}


function btwp_wpdocs_excerpt_more( $more ) {
    return sprintf( '<a class="read-more" href="%1$s">%2$s</a>',
        get_permalink( get_the_ID() ),
        __( 'Read More', 'textdomain' )
    );
}
//add_filter( 'excerpt_more', 'smts_wpdocs_excerpt_more' );



