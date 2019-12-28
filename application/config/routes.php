<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'auth/authentication';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

$route['attemptlogin'] = 'auth/authentication/attempt_login';
$route['logout'] = 'auth/authentication/logout';

$route['dashboard'] = 'dashboard';

$route['assessment'] = 'assessment';
$route['form/(:num)'] = 'assessment/form/$1';
$route['nik/(:num)/jobtitle/(:num)/competency/(:num)/assessment'] = 'assessment/get_competency/$1/$2/$3';
$route['assessment/(:num)/competency/(:any)/nik'] = 'assessment/see_poin/$1/$2';
$route['store_poin'] = 'assessment/insert_poin';
$route['submit_form/(:num)'] = 'assessment/submit_form/$1';
$route['export_to_excel/(:num)'] = 'assessment/export_assessment_to_excel/$1';

$route['dictionary'] = 'competency/dictionary';
$route['dictionary/(:num)/detail'] = 'competency/dictionary/get_dictionary/$1';
$route['dictionary/store'] = 'competency/dictionary/store_competency';
$route['dictionary/(:num)/edit'] = 'competency/dictionary/edit_competency/$1';
$route['dictionary/(:num)/remove'] = 'competency/dictionary/remove_competency/$1';
$route['dictionary/(:num)/print'] = 'competency/dictionary/print_dictionary/$1';

$route['skill_unit/(:num)/dictionary'] = 'competency/skill_unit/get_skill_unit/$1';
$route['skill_unit/store'] = 'competency/skill_unit/store';
$route['skill_unit/(:num)/print'] = 'competency/skill_unit/print_skill_unit/$1';
$route['skill_unit/(:num)/detail'] = 'competency/skill_unit/detail/$1';
$route['skill_unit/(:num)/remove/(:num)/dictionary'] = 'competency/skill_unit/remove/$1/$2';

$route['assessment_year'] = 'manage/assessment_year';
$route['assessment_year/store'] = 'manage/assessment_year/store';
$route['assessment_year/(:num)/edit'] = 'manage/assessment_year/edit/$1';
$route['assessment_year/(:num)/set_active'] = 'manage/assessment_year/set_active_year/$1';
$route['assessment_year/(:num)/remove'] = 'manage/assessment_year/remove/$1';
$route['assessment_year/set_period'] = 'manage/assessment_year/set_period';
$route['assessment_year/(:num)/period'] = 'manage/assessment_year/detail_period/$1';