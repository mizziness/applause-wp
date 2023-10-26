<?php

	function getCraftImage( $id ) {
		if ( $id == "" ) { return false; }

		$id = str_replace(array("[", "]"), "", $id);
		if ( strlen($id) <= 0 ) {
			return "";
		}

		$url = "https://prod-website.cloud.applause.com/api/images/" . $id . ".json?site=english";
		$json = file_get_contents($url);
		$output = json_decode( $json );
		return $output->url;
	}

	function getLocation( $location, $other = "" ) {
		$display = "";
		
		switch ($location) {
			case "bostonMassachusetts":
				$display = "Boston, Massachusetts";
				break;
			case "berlinGermany":
				$display = "Berlin, Germany";
				break;
			case "sanMateoCalifornia":
				$display = "San Mateo, California";
				break;
			case "other":
				$display = $other;
				break;
		}
		return $display;
	}

	function unserializeMe( $data, $value = "" ) {
		$expanded = json_decode($data);
		
		if ( !empty($expanded->metaGlobalVars) ) {
			$seo = $expanded->metaGlobalVars;
			$findIt = $seo->{$value} ?? null;
		} else {
			$findIt = $expanded->{$value} ?? null;
		}
		
		if ( $findIt != null ) {
			return $findIt;
		} else {
			return;
		}
	}

	function getAuthor( $craftID ) {
		if ( $craftID == "" ) { return false; }
		$id = intval(trim(str_replace(array("[", "]"), "", $craftID)));

		$args = array(
			'post_type' => 'blog-author',
			'post_status' => 'publish',
			'posts_per_page' => -1,
			'suppress_filters' => false,
			'meta_query' => array(
				'relation' => 'AND',
				array(
					'key' => 'craft_id',
					'value' => $id,
					'type' => 'numeric',
					'compare' => '=',
				)
			),
		);
		$query = new WP_Query($args);
		$author = $query->posts[0];

		return $author->ID;
	}


	function getPage( $craftID ) {
		if ( $craftID == "" ) { return false; }
		$id = intval(trim(str_replace(array("[", "]"), "", $craftID)));

		$args = array(
			'post_type' => array('post', 'page'),
			'post_status' => 'publish',
			'posts_per_page' => -1,
			'suppress_filters' => true,
			'meta_query' => array(
				'relation' => 'AND',
				array(
					'key' => 'craft_id',
					'value' => $id,
					'type' => 'numeric',
					'compare' => '=',
				)
			),
		);
		$query = new WP_Query($args);
		$result = $query->posts[0];

		return $result->ID;
	}

	function getCategory( $craftID ) {
		if ( $craftID == "" ) { return false; }

		$ids = array_filter(explode(",", $craftID));
		$result = array();

		foreach ($ids as $id) {
			$id = intval(trim(str_replace(array("[", "]"), "", $id)));

			$args = array(
				'hide_empty' => false,
				'meta_key' => 'craft_id',
				'meta_value' => $id,
				'meta_type' => 'numeric',
				'meta_compare' => '='
			);
			$results = get_categories($args);
			if (count($results) > 0) {
				$result[] = $results[0]->slug;
			}
		}
		if (count($ids) > 0 ) {
			$temp = implode( ", ", array_filter($result));
			return $temp;
		}
	}

	function getTag( $craftID ) {
		if ( $craftID == "" ) { return false; }

		$ids = array_filter(explode(",", $craftID));
		$result = array();

		foreach ($ids as $id) {
			$id = intval(trim(str_replace(array("[", "]"), "", $id)));
			$args = array(
				'hide_empty' => false,
				'meta_key' => 'craft_id',
				'meta_value' => $id,
				'meta_type' => 'numeric',
				'meta_compare' => '='
			);
			$results = get_tags($args);

			if (count($results) > 0) {
				$result[] = $results[0]->slug;
			}
		}

		if (count($ids) > 0 ) {
			return implode( ", ", array_filter($result));
		}
	}

	function site2Lang( $sideID ) {
		$display = "";
		
		switch ($sideID) {
			case 1:
				$display = "en";
				break;
			case 2:
				$display = "de";
				break;
			case 3:
				$display = "fr";
				break;
			case 5:
				$display = "ja";
				break;
		}
		return $display;
	}

	function boolConvert( $value ) {
		if ( $value == true || $value == "true" ) {
			return 1;
		} else {
			return 0;
		}
	}
		

?>