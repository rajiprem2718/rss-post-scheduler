<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$route['default_controller'] = 'rss';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

$route['rss']              = 'rss/index';
$route['rss/posts']        = 'rss/posts';
$route['rss/posts/(:num)'] = 'rss/posts/$1';
$route['rss/dashboard']    = 'rss/dashboard';
$route['rss/social-dashboard'] = 'rss/social_dashboard';

