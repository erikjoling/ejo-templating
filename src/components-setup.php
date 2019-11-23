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

	Composition::setup_component( 'site', function( $component ) { 
		return [
			'element' => [ 'tag' => 'div', 'inner_wrap' => true ],
			'content' => [ 'site-header', 'site-main', 'site-footer' ]
		]; 
	});

	Composition::setup_component( 'site-header', function( $component ) { 
		return [ 
			'element' => [ 'tag' => 'header', 'inner_wrap' => true, 'force_display' => true ],
			'content' => [ 'site-branding', 'site-nav-toggle', 'site-nav' ]
		]; 
	});

	Composition::setup_component( 'site-main', function( $component ) { 
		return [ 
			'element' => [ 'tag' => 'main', 'inner_wrap' => true, 'force_display' => true ],
			'content' => [ 'page' ]
		]; 
	});

	Composition::setup_component( 'site-footer', function( $component ) { 
		return [ 
			'element' => [ 'tag' => 'footer', 'inner_wrap' => true, 'force_display' => true ],
			'content' => []
		]; 
	});

	Composition::setup_component( 'page', function( $component ) { 
		return [ 
			'element' => [ 'tag' => 'article', 'inner_wrap' => true, 'force_display' => true ],
			'content' => [ 'the-post', 'page-header', 'page-main', 'page-footer' ]
		]; 
	});

	Composition::setup_component( 'page-header', function( $component ) { 
		return [ 
			'element' => [ 'tag' => 'header', 'inner_wrap' => true, 'force_display' => true ],
			'content' => [ 'breadcrumbs', 'page-title' ]
		]; 
	});

	Composition::setup_component( 'page-main', function( $component ) { 
		return [ 
			'element' => [ 'tag' => 'div', 'inner_wrap' => true, 'force_display' => true ],
			'content' => [ 'page-content' ]
		]; 
	});

	Composition::setup_component( 'page-footer', function( $component ) { 
		return [ 
			'element' => [ 'tag' => 'footer', 'inner_wrap' => true ],
			'content' => []
		]; 
	});

	Composition::setup_component( 'page-title', function( $component ) { 

		$tag = (Composition::get_parent() == 'page-header') ? 'h1' : 'h2';

		return [ 
			'element' => [ 'tag' => $tag, 'bem_element' => 'title' ],
			'content' => get_page_title()
		]; 
	});

	Composition::setup_component( 'site-branding', function( $component ) { 
		return [
			'content' => render_site_branding() 
		]; 
	});

	Composition::setup_component( 'site-nav-toggle', function( $component ) { 
		return [
			'content' => render_site_nav_toggle() 
		]; 
	});

	Composition::setup_component( 'site-nav', function( $component ) { 
		return [
			'content' => render_site_nav() 
		]; 
	});

	Composition::setup_component( 'page-content', function( $component ) { 
		return [
			'content' => render_page_content() 
		]; 
	});

	Composition::setup_component( 'breadcrumbs', function( $component ) { 
		return [
			'content' => render_breadcrumbs() 
		]; 
	});

	Composition::setup_component( 'the-post', '\\the_post' );

	/**
	 * Post stuff
	 */

	Composition::setup_component( 'the-post-loop', function( $component ) { 
		return [
			'content' => render_post_archive_loop() 
		]; 
	});

	Composition::setup_component( 'plural-post', function( $component ) { 
		return [
			'element' => [ 'tag' => 'article' ],
			'content' => [ 'plural-post-header', 'plural-post-main', 'plural-post-footer' ]
		]; 
	});

	Composition::setup_component( 'plural-post-header', function( $component ) { 
		return [
			'element' => [ 'tag' => 'header', 'bem_block' => false, 'bem_element' => 'header' ],
			'content' => [ 'plural-post-title' ]
		]; 
	});

	Composition::setup_component( 'plural-post-title', function( $component ) { 
		return [
			'element' => [ 'tag' => 'h3', 'bem_block' => false, 'bem_element' => 'title' ],
			'content' => '<a href="'. get_the_permalink() .'">'. get_the_title() .'</a>'
		]; 
	});

	Composition::setup_component( 'plural-post-main', function( $component ) { 
		return [
			'element' => [ 'tag' => 'div', 'bem_block' => false, 'bem_element' => 'main' ],
			'content' => render_plural_post_content() 
		]; 
	});

	Composition::setup_component( 'plural-post-footer', function( $component ) { 
		return [
			'element' => [ 'tag' => 'footer', 'bem_block' => false, 'bem_element' => 'footer' ],
			'content' => [ 'post-meta' ]
		]; 
	});

	Composition::setup_component( 'post-meta', function( $component ) { 
		return [
			'element' => [ 'tag' => 'div' ],
			'content' => [ 'post-author', 'post-date', 'post-categories' ]
		]; 
	});

	Composition::setup_component( 'post-author', function( $component ) { 
		$bem_element = (Composition::get_parent() == 'post-meta') ? 'author' : true;

		return [
			'element' => [ 'tag' => 'div', 'bem_block' => false, 'bem_element' => $bem_element ],
			'content' => render_post_author()
		]; 
	});

	Composition::setup_component( 'post-date', function( $component ) { 
		$bem_element = (Composition::get_parent() == 'post-meta') ? 'date' : true;

		return [
			'element' => [ 'tag' => 'div', 'bem_block' => false, 'bem_element' => $bem_element ],
			'content' => render_post_date()
		]; 
	});

	Composition::setup_component( 'post-categories', function( $component ) { 
		$bem_element = (Composition::get_parent() == 'post-meta') ? 'categories' : true;

		return [
			'element' => [ 'tag' => 'div', 'bem_block' => false, 'bem_element' => $bem_element ],
			'content' => render_post_categories()
		]; 
	});


	/**
	 * Template: plural pages
	 */
	if (is_plural_page()) {

		Composition::setup_component( 'page', function( $component ) {

			Composition::component_remove( $component['content'], 'the-post' );

			return $component;
		});

		Composition::setup_component( 'page-main', function( $component ) {


			Composition::component_add_after( $component['content'], 'the-post-loop' );

			return $component;
		});
	}

	/**
	 * Template: Single post
	 */
	if ( is_singular_page('post') ) {

		Composition::setup_component( 'page-header', function( $component ) {

			Composition::component_add_after( $component['content'], 'post-meta', 'page-title' );

			return $component;
		});	
	}
});

	
/** 
 * Test
 */ 
add_action( 'wp', function() {

	Composition::setup_component( 'plural-post', function( $component ) {

		Composition::component_move_before( $component['content'], 'plural-post-footer' );

		return $component;
	});	

}, 99);

