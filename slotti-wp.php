<?php
	/*
	Plugin Name: Slotti Ajanvaraus
	Plugin URI: https://slotti.fi
	Description: Slotti Ajanvaraus -lisäosa helpottaa ajanvaruspainikkeen tai upotetun varaussivun lisäämistä WordPress-sivuille.
	Author: Teonos Oy
	Version: 1.3.0
	License: GPLv2 or later
	License URI: https://www.gnu.org/licenses/gpl-2.0.html
	*/

//[slotti url="https://slotti.fi/booking/EXAMPLE"]
function slotti_shortcode($atts){

	  $a = shortcode_atts( array(
	      'url' => NULL,
				'text' => 'Varaa aika'
	  ), $atts );

		// filter only data- prefixed parameters and create data-attributes out of
		$data_attrs = '';
		foreach ($atts as $key => $value) {
			if (strpos($key, 'data-') === 0) {
				$data_attrs = $data_attrs . $key . '=' . $value . ' ';
			}
		}

    $url = $a['url'];
    $text = $a['text'];
		$link = '<a href="'.$url.'" '.$data_attrs.' class="slotti-book-now">'.$text.'</a>';

		return $link;
}

//[slotti-embed-ga tracking-id="UA-12312313-X"]
function slotti_embed_ga_shortcode($atts){

	$a = shortcode_atts( array(
			'id' => NULL
	), $atts );

	add_action('wp_footer', function() use ($a) {

		$id = $a['id'];

		$ga_script = <<<EOT

			console.warn('slotti-embed-ga shortcode is deprecated. Please migrate to another method of adding Google Analytics to your site.');

			(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
									(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
							m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
			})(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

      ga('create', '$id', 'auto');
			ga('require', 'linker');
			ga('linker:autoLink', ['slotti.fi'], true, false);

			ga(function(tracker){
					Slotti.gaLinker(tracker);
			});

			ga('send', 'pageview');
EOT;

		wp_add_inline_script('slotti_embed_js', $ga_script);
	});

}

add_shortcode( 'slotti', 'slotti_shortcode' );
add_shortcode( 'slotti-embed-ga', 'slotti_embed_ga_shortcode' );

function register_embed_script(){
	wp_register_script('slotti_embed_js', 'https://slotti.fi/static/js/embed.js');
	wp_enqueue_script('slotti_embed_js');

}

// add embed script to footer, so the slotti-book-now -tags exists before
// scanning.
add_action('wp_footer', 'register_embed_script');

?>
