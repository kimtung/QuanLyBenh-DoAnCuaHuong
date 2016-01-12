<?php
$router['']                 = 'benhvatnuoi/home';
$router['search']           = 'benhvatnuoi/search';
$router['contact']          = 'benhvatnuoi/contact';
$router['disease']          = 'benhvatnuoi/disease/index';
$router['disease/(:num)']   = 'benhvatnuoi/disease/index/$1';
$router['species']          = 'benhvatnuoi/species/index';
$router['species/(:num)']   = 'benhvatnuoi/species/index/$1';
$router['group/(:num)']     = 'benhvatnuoi/group/index/$1';
$router['manager']          = 'benhvatnuoi/manager/index';

$router['manager/accounts']                 = 'benhvatnuoi/manager/accounts';
$router['manager/accounts/(:any)']          = 'benhvatnuoi/manager/accounts/$1';
$router['manager/accounts/(:any)/(:num)']   = 'benhvatnuoi/manager/accounts/$1/$2';

$router['manager/species']                  = 'benhvatnuoi/manager/species';
$router['manager/species/(:any)']           = 'benhvatnuoi/manager/species/$1';
$router['manager/species/(:any)/(:num)']    = 'benhvatnuoi/manager/species/$1/$2';

$router['manager/breeds']                   = 'benhvatnuoi/manager/breeds';
$router['manager/breeds/(:any)']            = 'benhvatnuoi/manager/breeds/$1';
$router['manager/breeds/(:any)/(:num)']     = 'benhvatnuoi/manager/breeds/$1/$2';

$router['manager/diseases_group']                   = 'benhvatnuoi/manager/diseases_group';
$router['manager/diseases_group/(:any)']            = 'benhvatnuoi/manager/diseases_group/$1';
$router['manager/diseases_group/(:any)/(:num)']     = 'benhvatnuoi/manager/diseases_group/$1/$2';

$router['manager/diseases']                 = 'benhvatnuoi/manager/diseases';
$router['manager/diseases/(:any)']          = 'benhvatnuoi/manager/diseases/$1';
$router['manager/diseases/(:any)/(:num)']   = 'benhvatnuoi/manager/diseases/$1/$2';

$router['manager/login']    = 'benhvatnuoi/manager/login';
$router['manager/logout']   = 'benhvatnuoi/manager/logout';