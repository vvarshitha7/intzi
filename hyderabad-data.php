<?php
// Hyderabad Areas and Pincodes
$hyderabad_areas = [
    // Central Hyderabad
    ['area' => 'Abids', 'pincode' => '500001'],
    ['area' => 'Koti', 'pincode' => '500027'],
    ['area' => 'Sultan Bazaar', 'pincode' => '500095'],
    ['area' => 'Nampally', 'pincode' => '500001'],
    ['area' => 'Musheerabad', 'pincode' => '500020'],

    // North Hyderabad
    ['area' => 'Secunderabad', 'pincode' => '500003'],
    ['area' => 'Trimulgherry', 'pincode' => '500015'],
    ['area' => 'Alwal', 'pincode' => '500010'],
    ['area' => 'Bowenpally', 'pincode' => '500011'],
    ['area' => 'Kompally', 'pincode' => '500014'],
    ['area' => 'Malkajgiri', 'pincode' => '500047'],
    ['area' => 'Mallampet', 'pincode' => '500090'],
    
    // West Hyderabad
    ['area' => 'Kukatpally', 'pincode' => '500072'],
    ['area' => 'KPHB Colony', 'pincode' => '500085'],
    ['area' => 'Miyapur', 'pincode' => '500049'],
    ['area' => 'Nizampet', 'pincode' => '500090'],
    ['area' => 'Bachupally', 'pincode' => '500090'],
    ['area' => 'Madhapur', 'pincode' => '500081'],
    ['area' => 'Gachibowli', 'pincode' => '500032'],
    ['area' => 'Kondapur', 'pincode' => '500084'],
    ['area' => 'Hitec City', 'pincode' => '500081'],
    
    // East Hyderabad
    ['area' => 'Uppal', 'pincode' => '500039'],
    ['area' => 'LB Nagar', 'pincode' => '500074'],
    ['area' => 'Dilsukhnagar', 'pincode' => '500060'],
    ['area' => 'Malakpet', 'pincode' => '500036'],
    ['area' => 'Habsiguda', 'pincode' => '500007'],
    ['area' => 'ECIL', 'pincode' => '500062'],
    ['area' => 'Nacharam', 'pincode' => '500076'],
    
    // South Hyderabad
    ['area' => 'Banjara Hills', 'pincode' => '500034'],
    ['area' => 'Jubilee Hills', 'pincode' => '500033'],
    ['area' => 'Mehdipatnam', 'pincode' => '500028'],
    ['area' => 'Tolichowki', 'pincode' => '500008'],
    ['area' => 'Attapur', 'pincode' => '500048'],
    ['area' => 'Rajendranagar', 'pincode' => '500030'],
    ['area' => 'Shamshabad', 'pincode' => '500409'],
    
    // Additional Areas
    ['area' => 'Ameerpet', 'pincode' => '500016'],
    ['area' => 'Begumpet', 'pincode' => '500016'],
    ['area' => 'Somajiguda', 'pincode' => '500082'],
    ['area' => 'Punjagutta', 'pincode' => '500082'],
    ['area' => 'Lakdikapul', 'pincode' => '500004'],
    ['area' => 'Masab Tank', 'pincode' => '500028'],
    ['area' => 'Charminar', 'pincode' => '500002'],
    ['area' => 'Moosapet', 'pincode' => '500018'],
    ['area' => 'SR Nagar', 'pincode' => '500038'],
    ['area' => 'Erragadda', 'pincode' => '500018'],
];

// Sort by area name
usort($hyderabad_areas, function($a, $b) {
    return strcmp($a['area'], $b['area']);
});

// Create area to pincode mapping
$area_pincode_map = [];
foreach($hyderabad_areas as $location) {
    $area_pincode_map[$location['area']] = $location['pincode'];
}
?>
