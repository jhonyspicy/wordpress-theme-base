<?php
namespace ShortCode;
use \Jhonyspicy\Wordpress\Theme\Base\Lib\ShortCode as ShortCode;

class GoogleMaps extends ShortCode {
	static $count = 0;

	public function do_shortcode($atts, $content) {
		$atts = shortcode_atts(array('lat'     => '-25.363882',
									 'lng'     => '131.044922',
									 'zoom'    => '4',
									 'comment' => 'ここです。'), $atts);

		$count = self::$count++;

		wp_enqueue_script('googlemap', 'https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false');
		$script = <<<EOF
			<script>
				(function () {
					var myLatlng = new google.maps.LatLng({$atts['lat']},{$atts['lng']});
					var mapOptions = {
						zoom: {$atts['zoom']},
						center: myLatlng
					}
					var map = new google.maps.Map(document.getElementById('map-canvas{$count}'), mapOptions);

					var marker = new google.maps.Marker({
						position: myLatlng,
						map: map,
						title: '{$atts['comment']}'
					});

				})();
			</script>
EOF;

		ShortCode::register_script($script);

		return <<<EOF
<p id="map-canvas{$count}" class="map-canvas" itemprop="geo" itemscope itemtype="http://www.data-vocabulary.org/Geo/">
	<meta itemprop="latitude" content="{$atts['lat']}" />
	<meta itemprop="longitude" content="{$atts['lng']}" />
</p>
EOF;
	}
}