<?php
/**
 * Templating functions.
 */

namespace Ejo\Templating;


add_action( 'wp', function() {

	/*
	Ik interesseer me niet in het eerst registreren en daarna renderen. 
	Het is prima om tijdens het renderen alles op te bouwen. 
	Zolang het maar BEM proof is. En flexibel qua aanpasbaarheid.
	*/

	/*
	With the current setup the content can only be an array of components 
	or a string which needs to be outputted.
	*/

	/*
	Als ik removable filter functions gebruik, dan kan ik niet zo flexibel
	met template conditionals omgaan. Want die moet ik dan in de functions
	aanroepen, in plaats van dat ik alles tegelijkertijd tackle binnen die
	template. En dat is wel wat ik wil! Dus geen removable functions meer??

	Of ik moet de naam van het template meenemen in de functienaam. Dan kan 
	ik wel in één keer alle functies wrappen in een template conditional. 
	...
	Maar dit blijkt te omslachtig. Ik wil niet voor elke template contitional
	(plural, single post, search, 404, archive) aparte functies aanmaken.

	Waarom wil ik removable filters? Als er een plugin inhaakt op een component
	via een filter dan kan ik deze in het thema eventueel weghalen. 

	Het spanningsveld ligt dus tussen weghalen van component aanpassingen door
	een plugin en het kunnen samenvoegen van component-setups binnen een 
	template conditional. 

	---

	!! Geen removable functions meer. Ik kan met andere checks (filters/theme-support)
	eventueel bepaalde components uit laten zetten. Specifiek bepaalde component-
	setups uitschakelen is er niet meer bij.
	*/

	/*
	Ik kies voor het gebruik van een eigen API die het werk doet. Op deze manier
	heb ik meer controle over wat er achter de schermen van de API gebeurt. Zo
	lang de interface hetzelfde blijft hoef ik niet bang te zijn voor breakig 
	backwardscompatibility.
	*/

	/*
	On templates: Templates vs components vs composition.

	With template I stick with the WordPress definition. Singular, Archive, Search,
	404. Every template might have a different composition, which is a structure of 
	components. 

	The component called `page` is not tied to the wordpress post type `page`. 
	It is a layout component inside the `site` component. It is normally present
	on every template type. 

	*/

	/**
	 * Site and Page stuff
	 */
	// Composition::setup_component_defaults( 'site', function( $component ) { 
	// 	$component['element'] = [ 'tag' => 'div', 'inner_wrap' => true ];
	// 	$component['content'] = [ 
	// 		['name' => 'site-header-2', 'element' => [], 'content' => [['name' => 'site-footer', 'callback' => [__NAMESPACE__ . '\\render_site_info' ]]] ],
	// 		'site-main-2',
	// 		// ['name' => 'site-footer-2', 'callback' => __NAMESPACE__ . '\\render_site_info' ],
	// 		['name' => 'site-footer-3', 'callback' => ['get_the_title', 2]],
	// 	]; 

	// 	return $component;
	// });

	// Composition::setup_component_defaults( 'title', function( $component ) { 
	// 	$component['element'] = [ 'tag' => 'h1' ];
	// 	$component['content'] = ['get_the_title', null]; 

	// 	return $component;
	// });

	// Composition::setup_component_defaults( 'page-title', function( $component ) { 
	// 	$component['element'] = [ 'tag' => 'h1' ];
	// 	$component['content'] = ['get_the_title', null]; 

	// 	return $component;
	// });


	Composition::setup_component_defaults( 'site', function( $component ) { 
		$component['element'] = [ 'tag' => 'div', 'inner_wrap' => true ];
		$component['content'] = [ ['site-header'] ]; 

		return $component;
	});

	Composition::setup_component_defaults( 'site-header', function( $component ) { 
		$component['element'] = [ 'tag' => 'header', 'inner_wrap' => true, 'force_display' => true ];
		$component['content'] = [ ['site-branding'] ];
		
		return $component;
	});

	Composition::setup_component_defaults( 'site-main', function( $component ) { 
		$component['element'] = [ 'tag' => 'main', 'inner_wrap' => true, 'force_display' => true ];
		$component['content'] = [ 'page' ];
		
		return $component;
	});

	Composition::setup_component_defaults( 'site-footer', function( $component ) { 
		$component['element'] = [ 'tag' => 'footer', 'inner_wrap' => true, 'force_display' => true ];
		$component['content'] = [];
		
		return $component;
	});

	Composition::setup_component_defaults( 'page', function( $component ) { 
		$component['element'] = [ 'tag' => 'article', 'inner_wrap' => true, 'force_display' => true ];
		$component['content'] = [ 'page-header', 'page-main', 'page-footer' ];
		
		return $component;
	});

	Composition::setup_component_defaults( 'page-header', function( $component ) { 
		$component['element'] = [ 'tag' => 'header', 'inner_wrap' => true, 'force_display' => true ];
		$component['content'] = [ 'breadcrumbs', 'page-title' ];
		
		return $component;
	});

	Composition::setup_component_defaults( 'page-main', function( $component ) { 
		$component['element'] = [ 'tag' => 'div', 'inner_wrap' => true, 'force_display' => true ];
		$component['content'] = [ 'page-content' ];
		
		return $component;
	});

	Composition::setup_component_defaults( 'page-footer', function( $component ) { 
		$component['element'] = [ 'tag' => 'footer', 'inner_wrap' => true ];
		
		return $component;
	});

	Composition::setup_component_defaults( 'page-title', function( $component ) { 

		$component['element'] = [ 'tag' => 'h1', 'bem_element' => 'title' ];
		$component['content'] = single_post_title( '', false );
		// $component['content'] = [ 'fn:single_post_title' => ['', false] ];
		
		return $component;
	});

	Composition::setup_component_defaults( 'site-branding', function( $component ) {
		$component['element'] = false;
		$component['content'] = [ __NAMESPACE__ . '\\render_site_branding' ];
		
		return $component;
	});

	Composition::setup_component_defaults( 'site-nav-toggle', function( $component ) {
		$component['element'] = false;
		$component['content'] = render_site_nav_toggle();
		
		return $component;
	});

	Composition::setup_component_defaults( 'site-nav', function( $component ) { 
		$component['element'] = false;
		$component['content'] = render_site_nav();
		
		return $component;
	});

	Composition::setup_component_defaults( 'page-content', function( $component ) {
		$component['element'] = false;
		$component['content'] = apply_filters( 'the_content', get_the_content() );
		
		return $component;
	});

	Composition::setup_component_defaults( 'breadcrumbs', function( $component ) { 
		$component['element'] = false;
		$component['callback'] = [ __NAMESPACE__ . '\\render_breadcrumbs' ];
		
		return $component;
	});

	// Composition::setup_component_defaults( 'the-post', '__return_false' );
	Composition::after_setup_component( 'page', '\\the_post' );

	/**
	 * Post stuff
	 */

	Composition::setup_component_defaults( 'the-post-loop', function( $component ) {
		$component['element'] = false;
		$component['content'] = render_post_loop();
		
		return $component;
	});

	Composition::setup_component_defaults( 'plural-post', function( $component ) { 
		return [
			'element' => [ 'tag' => 'article' ],
			'content' => [ 'plural-post-header', 'plural-post-main', 'plural-post-footer' ]
		]; 
	});

	Composition::setup_component_defaults( 'plural-post-header', function( $component ) { 
		return [
			'element' => [ 'tag' => 'header', 'bem_block' => false, 'bem_element' => 'header' ],
			'content' => [ 'plural-post-title', 'post-meta' ]
		]; 
	});

	Composition::setup_component_defaults( 'plural-post-title', function( $component ) { 
		return [
			'element' => [ 'tag' => 'h3', 'bem_block' => false, 'bem_element' => 'title' ],
			'content' => '<a href="'. get_the_permalink() .'">'. get_the_title() .'</a>'
		]; 
	});

	Composition::setup_component_defaults( 'plural-post-main', function( $component ) { 
		return [
			'element' => [ 'tag' => 'div', 'bem_block' => false, 'bem_element' => 'main' ],
			'content' => render_plural_post_content() 
		]; 
	});

	Composition::setup_component_defaults( 'plural-post-footer', function( $component ) { 
		return [
			'element' => [ 'tag' => 'footer', 'bem_block' => false, 'bem_element' => 'footer' ],
			'content' => [ 'plural-post-read-more' ]
		]; 
	});

	Composition::setup_component_defaults( 'plural-post-read-more', function( $component ) { 
		return [
			'content' => '<a href="'. get_the_permalink() .'" class="'. Composition::get_parent() .'__read-more">'. __('Read more', 'ejo-base') .'</a>'
		]; 
	});

	Composition::setup_component_defaults( 'post-meta', function( $component ) {
		return [
			'element' => [ 'tag' => 'div' ],
			'content' => [ 'post-author', 'post-date', 'post-categories' ]
		]; 
	});

	Composition::setup_component_defaults( 'post-author', function( $component ) { 
		$bem_element = (Composition::get_parent() == 'post-meta') ? 'author' : true;

		return [
			'element' => [ 'tag' => 'div', 'bem_block' => false, 'bem_element' => $bem_element ],
			'content' => render_post_author()
		]; 
	});

	Composition::setup_component_defaults( 'post-date', function( $component ) { 
		$bem_element = (Composition::get_parent() == 'post-meta') ? 'date' : true;

		return [
			'element' => [ 'tag' => 'div', 'bem_block' => false, 'bem_element' => $bem_element ],
			'content' => render_post_date()
		]; 
	});

	Composition::setup_component_defaults( 'post-categories', function( $component ) { 
		$bem_element = (Composition::get_parent() == 'post-meta') ? 'categories' : true;

		return [
			'element' => [ 'tag' => 'div', 'bem_block' => false, 'bem_element' => $bem_element ],
			'content' => render_post_categories()
		]; 
	});

	Composition::setup_component_defaults( 'post-nav', function( $component ) { 
		return [
			'element' => [ 'tag' => 'nav', 'inner_wrap' => true ],
			'content' => [ 'post-nav-link-previous', 'post-nav-link-next' ]
		];
	});

	Composition::setup_component_defaults( 'post-nav-link-previous', function( $component ) { 
		return [
			'content' => render_post_nav_link('previous')
		];
	});

	Composition::setup_component_defaults( 'post-nav-link-next', function( $component ) { 
		return [
			'content' => render_post_nav_link('next')
		];
	});

	Composition::setup_component_defaults( 'recent-posts', function( $component ) {

		return [
			'element' => [ 'tag' => 'section' ],
			'content' => [ 'recent-posts-header', 'recent-posts-main' ]
		];
	});

	Composition::setup_component_defaults( 'recent-posts-header', function( $component ) {

		return [
			'element' => [ 'tag' => 'header', 'bem_block' => false, 'bem_element' => 'header' ],
			'content' => '<h2 class="'. Composition::get_parent() .'__title">Recente berichten</h2>'
		];
	});

	Composition::setup_component_defaults( 'recent-posts-main', function( $component ) {

		return [
			'element' => [ 'tag' => 'div', 'bem_block' => false, 'bem_element' => 'main' ],
			'content' => render_post_loop( 'posts_per_page=3' )
		];
	});

	/**
	 * Template Type: archive
	 */
	if ( is_template_type('archive') ) {

		Composition::setup_component_defaults( 'page', function( $component ) {

			// Composition::component_remove( $component['content'], 'the-post' );
			Composition::after_setup_component_remove( 'page', '\\the_post' );

			// global $wp_filters;
			// log($wp_filters);

			return $component;
		});

		// Composition::setup_component_defaults( 'page-main', function( $component ) {

		// 	Composition::component_insert_after( $component['content'], 'the-post-loop', 'page-content' );

		// 	return $component;
		// });
	}

	/**
	 * Template Type: Term
	 */
	if ( is_template_type('term') ) {
		
		Composition::setup_component_defaults( 'page-title', function( $component ) {
			$component['content'] = single_term_title( '', false );

			return $component;
		});

		Composition::setup_component_defaults( 'page-content', function( $component ) {
			$component['content'] = apply_filters( 'the_content', get_the_archive_description() );

			return $component;
		});
	}

	/**
	 * Template: blog page
	 */
	if ( is_template('blog') ) {
		
		Composition::setup_component_defaults( 'page-title', function( $component ) {
			$component['content'] = get_post_field( 'post_title', get_queried_object_id() );

			return $component;
		});

		Composition::setup_component_defaults( 'page-content', function( $component ) {
			$component['content'] = apply_filters( 'the_content', get_the_content( null, false, get_queried_object_id() ) );

			return $component;
		});
	}

	/**
	 * Template: Post
	 */
	if ( is_template('post') ) {

		Composition::setup_component_defaults( 'page-header', function( $component ) {

			Composition::component_insert_after( $component['content'], 'post-meta', 'page-title' );

			return $component;
		});	

		Composition::setup_component_defaults( 'page-footer', function( $component ) {

			Composition::component_insert_after( $component['content'], 'post-nav' );

			return $component;
		});	
	}


	/**
	 * Template: 404
	 */
	if ( is_template('404') ) {

		Composition::setup_component_defaults( 'page-title', function( $component ) {
			$component['content'] = render_404_title();

			return $component;
		});

		Composition::setup_component_defaults( 'page-content', function( $component ) {
			$component['content'] = render_404_content();

			return $component;
		});
	}

	/**
	 * Template: Search
	 */
	if ( is_template('search') ) {

		Composition::setup_component_defaults( 'page-title', function( $component ) {
			$component['content'] = render_search_title();

			return $component;
		});

		Composition::setup_component_defaults( 'page-content', function( $component ) {
			$component['content'] = render_search_content();

			return $component;
		});
	}

	/**
	 * Template: Blog no page
	 */
	if ( is_template('blog-no-page') ) {

		Composition::setup_component_defaults( 'page-title', function( $component ) {
			$component['content'] = 'Blog of Front';

			return $component;
		});
	}

	/**
	 * Template: Blog on Front
	 */
	if ( is_template('author') ) {

		Composition::setup_component_defaults( 'page-title', function( $component ) {
			$component['content'] = get_the_author_meta( 'display_name', absint( get_query_var( 'author' ) ) );

			return $component;
		});
	}
});

	
/** 
 * Test
 */ 
add_action( 'wp', function() {

	// Composition::setup_component_defaults( 'page-header', function( $component ) {

	// 	Composition::component_move_before( $component['content'], 'page-title', 'breadcrumbs' );

	// 	Composition::component_insert_after( $component['content'], 'page-title', 'breadcrumbs' );
	// 	Composition::component_insert_before( $component['content'], 'breadcrumbs', 'page-title' );
	// 	Composition::component_remove( $component['content'], 'page-title' );

	// 	return $component;
	// });	

}, 99);

