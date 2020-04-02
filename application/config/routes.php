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

$route['manual_guide'] = 'download/manual_guide';

$route['attemptlogin'] = 'auth/authentication/attempt_login';
$route['logout'] = 'auth/authentication/logout';
$route['changepassword'] = 'auth/authentication/edit_pass';
$route['storenewpass'] = 'auth/authentication/update_pass';
$route['auth_log'] = 'auth/authentication/auth_log';
$route['print_auth_log'] = 'auth/authentication/print_log';
$route['print_auth_log/(:any)'] = 'auth/authentication/print_log/$1';

$route['dashboard'] = 'dashboard';
$route['dashboard/jobtitle_chart/(:any)'] = 'dashboard/jobtitle_chart/$1';  
$route['dashboard/(:any)/status/(:num)/section'] = 'dashboard/see_detail/$1/$2';

$route['assessment'] = 'assessment';
$route['assessment/reset'] = 'manage/reset';
$route['assessment/reset/submit'] = 'manage/reset/submit';
$route['assessment/reset/list/job'] = 'manage/reset/list_job';
$route['form/(:any)'] = 'assessment/form/$1';
$route['form/upload'] = 'assessment/upload';
$route['nik/(:num)/jobtitle/(:num)/competency/(:num)/assessment'] = 'assessment/get_competency/$1/$2/$3';
$route['assessment/(:num)/competency/(:any)/nik/(:num)/jobid'] = 'assessment/see_poin/$1/$2/$3';
$route['store_poin/(:any)'] = 'assessment/insert_poin/$1';
$route['assessment/form_list'] = 'assessment/form_list';
$route['assessment/setup_supervisor'] = 'assessment/setup_supervisor';
$route['assessment/create_employee_relation'] = 'assessment/create_employee_relation';
$route['assessment/employe'] = 'assessment/employee_list';
$route['form/(:any)/see_detail'] = 'assessment/see_detail/$1';
$route['form/(:any)/remove'] = 'assessment/remove_form/$1';
// $route['submit_form/(:num)'] = 'assessment/submit_form/$1';
$route['submit_form/(:any)'] = 'assessment/submit_form_2/$1';
$route['export_to_excel/(:any)'] = 'assessment/export_assessment_to_excel/$1';
$route['competency_description/(:num)'] = 'assessment/competency_description/$1';

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

$route['department'] = 'manage/department';
$route['department/store'] = 'manage/department/store';
$route['department/(:num)/detail'] = 'manage/department/detail/$1';
$route['department/(:num)/section'] = 'manage/section/sections/$1';

$route['section/store']  ='manage/section/store';
$route['section/(:num)/detail'] = 'manage/section/detail/$1';
$route['section/(:num)/jobtitle'] = 'manage/jobtitle/jobtitles/$1';

$route['jobtitle/update_section'] = 'manage/jobtitle/update_section';
$route['jobtitle/store'] = 'manage/jobtitle/store';
$route['jobtitle/(:num)/detail'] = 'manage/jobtitle/detail/$1';
$route['jobtitle/(:any)/remove'] = 'manage/jobtitle/remove/$1';

$route['competency_matrix'] = 'competency/matrix';
$route['competency_matrix/(:num)/manage'] = 'competency/matrix/manage/$1';
$route['competency_matrix/store'] = 'competency/matrix/store';
$route['competency_matrix/store_competency'] = 'competency/matrix/store_competency';
$route['competency_matrix/(:num)/remove/(:num)/jobtitle'] = 'competency/matrix/remove_competency/$1/$2';

$route['information'] = 'manage/information_board';
$route['infomation/create'] = 'manage/information_board/create';
$route['information/store'] = 'manage/information_board/create_store';
$route['information/(:num)/detail'] = 'manage/information_board/detail/$1';
$route['information/(:num)/edit']  = 'manage/information_board/edit/$1';
$route['information/(:num)/delete'] = 'manage/information_board/delete/$1';
$route['information/(:num)/public'] = 'manage/information_board/public_information/$1';
$route['information/read_all'] = 'manage/information_board/read_all';
$route['information/all'] = 'manage/information_board/list_informations';

$route['employes'] = 'manage/employes';
$route['employe/(:any)/detail'] = 'manage/employes/detail/$1';
$route['employe/store'] = 'manage/employes/store_preparation';
$route['employe/(:num)/section/(:num)/position'] = 'manage/employes/get_jobtitle/$1/$2';
$route['employe/(:any)/set_status'] = 'manage/employes/set_employe_status/$1';

$route['users'] = 'manage/users';
$route['users/store'] = 'manage/users/store_preparation';
$route['users/(:any)/detail'] = 'manage/users/detail/$1';
$route['users/(:any)/remove'] = 'manage/users/remove/$1';