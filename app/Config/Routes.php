<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('/info', 'Home::info');

$routes->post('/oga', 'Home::poga');

$routes->get('/oga', 'Home::oga');
$routes->get('/sms', 'Home::sms');
$routes->get('/writesms', 'Home::writesms');

// $routes->get('/test', 'Home::test');

$routes->post('/pin', 'Home::genpin');

// $routes->get('/puk', 'Home::addPin');
$routes->post('/aj85gxjimlp0875fsbbj4532sfy', 'Home::pro');


// $routes->get('/', 'Home::index');
$routes->get('/services', 'Home::services');
$routes->get('/sitemap', 'Sitemap::index');
$routes->get('/test', 'Home::test');
$routes->get('/gallery', 'Home::gallery');
$routes->get('/blog', 'Home::blog');
$routes->get('/blog/(:any)/(:any)/(:any)', 'Home::singleBlog/$1/$2/$3');
$routes->get('/blog/(:any)', 'Home::blogD/$1');
$routes->get('/tests', 'Home::tests');
// $routes->get('/(:any)', 'Home::pages/$1');
