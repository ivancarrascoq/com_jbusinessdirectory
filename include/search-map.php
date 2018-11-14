<?php
/**
 * @package    JBusinessDirectory
 *
 * @author     CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2018 CMS Junkie. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
defined('_JEXEC') or die('Restricted access');

if (!defined('COMPONENT_IMAGE_PATH')) {
	define("COMPONENT_IMAGE_PATH", JURI::base() . "components/com_jbusinessdirectory/assets/images/");
}

$appSettings = JBusinessUtil::getInstance()->getApplicationSettings();
$lang        = JFactory::getLanguage()->getTag();
$key         = JBusinessUtil::loadMapScripts();

$map_latitude  = $appSettings->map_latitude;
$map_longitude = $appSettings->map_longitude;
$map_zoom      = (int) $appSettings->map_zoom;

if ((empty($map_latitude)) || (!is_numeric($map_latitude)))
	$map_latitude = 37.4419;

if ((empty($map_longitude)) || (!is_numeric($map_longitude)))
	$map_longitude = -122.1419;

if ((empty($map_zoom)) || (!is_numeric($map_zoom)))
	$map_zoom = 3;

if ($appSettings->map_apply_search != '1') {
	$map_latitude  = 37.4419;
	$map_longitude = -122.1419;
	$map_zoom      = 3;
}

$map_enable_auto_locate = "";
if ($appSettings->map_enable_auto_locate) {
	$map_enable_auto_locate = "map.fitBounds(bounds);";
}

// if selected the Style 5 layout from General settings
$layout_style_5 = false;
if ($appSettings->search_result_view == 5 && empty($param)) {
	$layout_style_5 = true;
}

$mapId = rand(1000, 10000);
if ($appSettings->enable_map_clustering) {
	JHtml::_('script', 'components/com_jbusinessdirectory/assets/js/markercluster.js');
}

$width  = "100%";
$height = "450px";

if ($layout_style_5) {
	$height = "100%";
}
else {
	if (isset($mapHeight))
		$height = $mapHeight;
	if (isset($mapWidth))
		$width = $mapWidth;
}

$session = JFactory::getSession();
if (empty($radius)) {
	$radius = $session->get("radius");
}

if ($appSettings->metric == 0) {
	$radius = $radius * 0.621371;
}

$company_locations = array();
if (!isset($companies)) {
	$companies = $this->companies;
}

$index = 1;
foreach ($companies as $company) {
	$tmp    = array();
	$marker = 0;

	if ($company->featured) {
		$marker = JURI::root() . PICTURES_PATH . "/default_featured_marker.png";
	}
    elseif (!empty($company->categoryMaker)) {
		$marker = JURI::root() . PICTURES_PATH . $company->categoryMaker;
	}

	$contentPhone  = (!empty($company->phone) && (isset($company->packageFeatures) && in_array(PHONE, $company->packageFeatures) || !$appSettings->enable_packages)) ?
		'<div class="info-phone"><i class="dir-icon-phone"></i> ' . htmlspecialchars($company->phone, ENT_QUOTES) . '</div>' : "";
	$contentString = '<div class="info-box">' .
		'<div class="title">' . htmlspecialchars($company->name) . '</div>' .
		'<div class="info-box-content">' .
		'<div class="address" itemtype="http://schema.org/PostalAddress" itemscope="" itemprop="address">' . htmlspecialchars(JBusinessUtil::getAddressText($company), ENT_QUOTES) . '</div>' .
		$contentPhone .
		'<a href="' . htmlspecialchars(JBusinessUtil::getCompanyLink($company), ENT_QUOTES) . '"><i class="dir-icon-external-link"></i> ' . htmlspecialchars(JText::_("LNG_MORE_INFO"), ENT_QUOTES) . '</a>' .
		'</div>' .
		'<div class="info-box-image">' .
		(!empty($company->logoLocation) ? '<img src="' . JURI::root() . PICTURES_PATH . htmlspecialchars($company->logoLocation, ENT_QUOTES) . '" alt="' . htmlspecialchars($company->name) . '">' : "") .
		'</div>' .
		'</div>';

	if ($layout_style_5) {
		$contentString = intval($company->id);
	}

	$searchSecondaries = false;
	if (!empty($company->latitude) && !empty($company->longitude) && (isset($company->packageFeatures) && in_array(GOOGLE_MAP, $company->packageFeatures) || !$appSettings->enable_packages)) {
		$tmp['title']        = htmlspecialchars($company->name);
		$tmp['latitude']     = $company->latitude;
		$tmp['latitude']     = $company->latitude;
		$tmp['longitude']    = $company->longitude;
		$tmp['zIndex']       = (int) $company->id;
		$tmp['content']      = $contentString;
		$tmp[]               = $index;
		$tmp['marker']       = $marker;

		if (isset($company->distance)) {
			$distance = $company->distance;
			$secondaryDistances = !empty($company->secondaryDistances) ? explode(',', $company->secondaryDistances) : array(0);
			$secDistance = min($secondaryDistances);

			if (!empty($secDistance)) {
				$distance = $distance < $secDistance ? $distance : $secDistance;
			}

			$searchSecondaries = true;
			if ($distance == $company->distance) {
				$tmp['in_range'] = 1;
				$searchSecondaries = false;
			}
        }

		$company_locations[] = $tmp;
	}

	if (!empty($company->locations) && (isset($company->packageFeatures) && in_array(GOOGLE_MAP, $company->packageFeatures) && in_array(SECONDARY_LOCATIONS, $company->packageFeatures) || !$appSettings->enable_packages)) {
		$locations = explode("#", $company->locations);
		$distances = isset($company->secondaryDistances) ? explode(',', $company->secondaryDistances) : array();

		foreach ($locations as $k => $location) {
			$tmp = array();
			$loc = explode("|", $location);

			$address = JBusinessUtil::getLocationAddressText($loc[2], $loc[3], $loc[9], $loc[4], $loc[5], $loc[8], $loc[6]);

			$contentPhoneLocation = (isset($company->packageFeatures) && in_array(PHONE, $company->packageFeatures) || !$appSettings->enable_packages) ?
				'<div class="info-phone"><i class="dir-icon-phone"></i> ' . htmlspecialchars($loc[7], ENT_QUOTES) . '</div>' : "";

			$contentStringLocation = '<div class="info-box">' .
				'<div class="title">' . htmlspecialchars($company->name) . '</div>' .
				'<div class="info-box-content">' .
				'<div class="address" itemtype="http://schema.org/PostalAddress" itemscope="" itemprop="address">' . htmlspecialchars($address, ENT_QUOTES) . '</div>' .
				$contentPhoneLocation .
				'<a href="' . htmlspecialchars(JBusinessUtil::getCompanyLink($company), ENT_QUOTES) . '"><i class="dir-icon-external-link"></i> ' . htmlspecialchars(JText::_("LNG_MORE_INFO"), ENT_QUOTES) . '</a>' .
				'</div>' .
				'<div class="info-box-image">' .
				(!empty($company->logoLocation) ? '<img src="' . JURI::root() . PICTURES_PATH . htmlspecialchars($company->logoLocation, ENT_QUOTES) . '" alt="' . htmlspecialchars($company->name) . '">' : "") .
				'</div>' .
				'</div>';

			if ($layout_style_5) {
				$contentStringLocation = intval($company->id);
			}

			$tmp['title']     = htmlspecialchars($company->name);
			$tmp['latitude']  = $loc[0];
			$tmp['longitude'] = $loc[1];
			$tmp['zIndex']    = 4;
			$tmp['content']   = $contentStringLocation;
			$tmp[]            = $index;
			$tmp['marker']    = $marker;

			if ($searchSecondaries) {
			    if ($distances[$k] == $secDistance) {
			        $tmp['in_range'] = 1;
                }
            }

			$company_locations[] = $tmp;
		}
	}

	$index++;
}

// the params array that will be used to initialize the map
$initparams                   = array();
$initparams["tmapId"]         = $mapId;
$initparams["default_marker"] = $mapId;
$initparams["map_div"]        = 'companies-map-';
$initparams["map_style"]      = 'search';
$initparams["map_latitude"]   = $map_latitude;
$initparams["map_longitude"]  = $map_longitude;
$initparams["map_width"]      = $width;
$initparams["map_height"]     = $height;
$initparams["map_zoom"]       = $map_zoom;
$initparams["isLayout"]       = $layout_style_5 ? 1 : 0;
$initparams["map_clustering"] = $appSettings->enable_map_clustering;
$initparams["imagePath"]      = COMPONENT_IMAGE_PATH;
$initparams["has_location"]   = (isset($this) && !empty($this->location["latitude"])) ? 1 : 0;
$initparams["radius"]         = !empty($radius) ? $radius : 0;
$initparams["autolocate"]     = $appSettings->map_enable_auto_locate;
$initparams["longitude"]      = '';
$initparams["latitude"]       = '';

if (isset($this) && !empty($this->location["latitude"])) {
	$initparams["longitude"] = $this->location["longitude"];
	$initparams["latitude"]  = $this->location["latitude"];
}

if ($appSettings->map_type == MAP_TYPE_BING) {
    $initparams["key"] = $key;
}
?>

<div id="companies-map-<?php echo $mapId ?>" style="position: relative;" class="search-map-container"></div>

<script>
    jbdMap.construct(<?php echo json_encode($company_locations) ?>, <?php echo json_encode($initparams) ?>, <?php echo $appSettings->map_type ?>);
    var mapInstance = jbdMap.loadMapScript();

    function initializeSearchAutocomplete(token, preventSubmit) {
        var element = document.getElementsByClassName('zipcode-'+token)[0];
        mapInstance.initAutocomplete(element, function(place) {
            jQuery("#geo-latitude").val(place['latitude']);
            jQuery("#geo-longitude").val(place['longitude']);
            jQuery("#location-detected").val("1");
        }, preventSubmit, false);
    }
</script>