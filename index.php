<?php
 
$served_by = '';
if ($_GET['method'] == 'flickr.photos.search') {
	if (strpos($_GET['text'], 'group:') === 0) {
		$group_name = trim(substr($_GET['text'], 6));
		$served_by = 'group.' . str_replace(' ', '_', $group_name) . '.';
		$xml = file_get_contents('http://api.flickr.com/services/rest/?method=flickr.groups.search&api_key=' . $_GET['api_key'] . '&text=' . urlencode($group_name));
		$xml = simplexml_load_string($xml);
		$attr = $xml->groups->group->attributes();
		$group_id = (string) $attr['nsid'];
		
		unset($_GET['text']);
		$_GET['method'] = 'flickr.groups.pools.getPhotos';
		$_GET['group_id'] = $group_id;
	}
	else if ($_GET['text'] == 'explore:' || $_GET['text'] == 'interesting:') {
		$served_by = 'explore.';
		unset($_GET['text']);
		$_GET['method'] = 'flickr.interestingness.getList';
	}
}
 
if (isset($_GET['per_page'])) {
	$_GET['per_page'] = '500';
}
 
$query = array();
foreach ($_GET as $k => $v) {
	$query[] = "$k=" . urlencode($v);
}
 
header('Content-Type: text/xml; charset=utf-8');
header('P3P: policyref="http://info.yahoo.com/w3c/p3p.xml", CP="CAO DSP COR CUR ADM DEV TAI PSA PSD IVAi IVDi CONi TELo OTPi OUR DELi SAMi OTRi UNRi PUBi IND PHY ONL UNI PUR FIN COM NAV INT DEM CNT STA POL HEA PRE LOC GOV"');
header('X-Served-By: ' . $served_by . 'dataproxy.pommepause.com');
echo file_get_contents('http://api.flickr.com/services/rest/?' . implode('&', $query));
