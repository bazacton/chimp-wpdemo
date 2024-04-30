<?php

$root_url = '#';
$thumbnails_uri = 'http://chimpgroup.com/wp-demo/webservice/addons/jobcareer/thumbnails/';

// Specify when to check for available addons in days.
$fetch_addons_after = 1;

$other_links = array(
	array(
		'text' => 'Browse All Addons',
		'link' => $root_url . 'jobcareer/addons'
	),
	array(
		'text' => 'Need a theme? Try Storefront',
		'link' => $root_url . 'themes'
	),
);

$addons = array(
	'job-alerts' => array(
		'display'		 => true,
		'category'		 => 'job',
		'name'			 => 'Job Notifications',
		'slug'			 => 'jobhunt-notifications',
		'main_file_path' => 'jobhunt-notifications/jobhunt-job-alerts.php',
		'description'	 => 'This addon will allow to set alerts/notifications for different activities on website.',
		'thumbnail'		 => $thumbnails_uri . 'jobhunt-job-notifications.jpg',
		'url'			 => $root_url . 'jobhunt-job-notifications',
		'version'		 => '1.0',
		'standalone'	 => true,
	),
	'job-deadline' => array(
		'display'		 => true,
		'category'		 => 'job',
		'name'			 => 'Job Application Deadline',
		'slug'			 => 'jobhunt-application-deadline',
		'main_file_path' => 'jobhunt-application-deadline/jobhunt-application-deadline.php',
		'description'	 => 'This addon will allow you add Job Deadline field for each job.',
		'thumbnail'		 => $thumbnails_uri . 'jobhunt-application-deadline.jpg',
		'url'			 => $root_url . 'jobhunt-job-application-deadline',
		'version'		 => '1.0',
		'standalone'	 => true,
	),
	'jobhunt-apply-with-facebook' => array(
		'display'		 => true,
		'category'		 => 'job',
		'name'			 => 'Apply With Facebook',
		'slug'			 => 'jobhunt-apply-with-facebook',
		'main_file_path' => 'jobhunt-apply-with-facebook/jobhunt-apply-with-facebook.php',
		'description'	 => 'This addon will allow you a user to apply to a job with facebook.',
		'thumbnail'		 => $thumbnails_uri . 'jobhunt-apply-with-facebook.jpg',
		'url'			 => $root_url . 'jobhunt-apply-with-facebook',
		'version'		 => '1.0',
		'standalone'	 => true,
	),
	'jobhunt-email-templates' => array(
		'display'		 => true,
		'category'		 => 'email',
		'name'			 => 'Job Email Templates',
		'slug'			 => 'jobhunt-email-templates',
		'main_file_path' => 'jobhunt-email-templates/jobhunt-email-templates.php',
		'description'	 => 'This addon will allow user to add custom email template for each job alert.',
		'thumbnail'		 => $thumbnails_uri . 'jobhunt-job-email-templates.jpg',
		'url'			 => $root_url . 'jobhunt-job-email-templates',
		'version'		 => '1.0',
		'standalone'	 => true,
	),
	'jobhunt-indeed-jobs' => array(
		'display'		 => true,
		'category'		 => 'job',
		'name'			 => 'Jobhunt Indeex Jobs',
		'slug'			 => 'jobhunt-indeed-jobs',
		'main_file_path' => 'jobhunt-indeed-jobs/jobhunt-indeed-jobs.php',
		'description'	 => 'This addon will allow user to import jobs from indeed right into their website.',
		'thumbnail'		 => $thumbnails_uri . 'jobhunt-indeed-jobs.jpg',
		'url'			 => $root_url . 'jobhunt-indeed-jobs',
		'version'		 => '1.0',
		'standalone'	 => true,
	),
);