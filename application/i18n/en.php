<?php defined('SYSPATH') or die('No direct access allowed.');

return array
(
		
		//main menu
		'Sign Up'=>'Sign Up',
		'Log In'=>'Log In',
		'My Maps'=>'My Maps',
		'Create Map'=>'Create Map',
		'View Map'=>'View Map',
		'Share'=>'Share',
		'Statistics'=>'Statistics',
		'Templates'=>'Templates',
		'Public Maps'=>'Public Maps',
		
		//sub menus
		'Create New Map'=>'Create New Map',
		'Share'=>'Share',
		'Message Center'=>'Message Center',
		'Basic Set-up'=>'Basic Set-up',
		'Data Structure'=>'Data Structure',
		'Validation'=>'Validation',
		'Geo Set-up'=>'Geo Set-up',
		'Geo Matching'=>'Geo Matching',
		'View Map'=>'View Map',
		'Create Template'=>'Create Template',
		'All Templates'=>'All Templates',
		'My Templates'=>'My Templates',
		
		
		//User Menu
		'About KoboMap'=>'About KoboMap',
		'Support & Feedback'=>'Support & Feedback',
		'Help'=>'Help',
		'profile'=>'profile',
		'Log Out'=>'Log Out',
		'Login, Signup'=>'Login, Signup',
		
		
		//main page
		'Welcome to Kobo Maps'=>'Welcome to Kobo Maps',
		
		
		//log out page
		'log out explanation'=>'You have successfully been logged out<br/><br/>Come on back now',
		
		//log in page
		'Enter your password and username below to log in'=>'Enter your password and username below to log in',
		'password'=>'password',
		'user name'=>'user name',
		'Forgot password?'=>'Forgot password?',
		'Forgot Password'=>'Forgot Password',
		'Enter your email address and instructions to reset your password will be emailed to you'=>'Enter your email address and instructions to reset your password will be emailed to you',
		'email address'=>'email address',
		'Submit'=>'Submit',
		
		//sign up page
		'Fill out this form to sign up for Kobo Maps'=>'Fill out this form to sign up for Kobo Maps',
		'first name'=>'first name',
		'last name'=>'last name',
		'password again'=>'password again',
		'Agree to terms of use'=>'Agree to terms of use',
		
		//my maps main page
		'Select a map to edit or create a new one'=>'Select a map to edit or create a new one',
		'Select'=>'Select',
		'Map'=>'Map',
		'Tasks'=>'Tasks',
		'Public'=>'Public',
		'Edit'=>'Edit',
		'Copy'=>'Copy',
		'View'=>'View',
		'Share'=>'Share',
		'Delete'=>'Delete',
		'You have no maps'=>'You have no maps',
		'Delete Selected'=>'Delete Selected',
		
		
		//add 1 page
		'Add Map - Basic Setup'=>'Add Map - Basic Setup',
		'First we will need some basic information for your map'=>'First we will need some basic information for your map',
		'Map Title'=>'Map Title',
		'Map Description'=>'Map Description',
		'Should this map be private'=>'Should this map be private',
		'Password (if private)'=>'Password (if private)',
		'Is the data source'=>'Is the data source',
		'Excel File'=>'Excel File',
		'Google Spreadsheet'=>'Google Spreadsheet',
		'Spreadsheet (.xls, .xlsx)'=>'Spreadsheet (.xls, .xlsx)',
		'Google Spreadsheet'=>'Google Spreadsheet',
		'Name'=>'Name',
		'Owner'=>'Owner',
		'Date'=>'Date',
		'Show advanced options'=>'Show advanced options',
		'Advanced'=>'Advanced',
		'Map CSS'=>'Map CSS',
		'<br/>CSS can be used to edit the style of the map menubar and legend. <br /> To learn more about the use of CSS see: '=>'<br/>CSS can be used to edit the style of the map menubar and legend. <br /> To learn more about the use of CSS see: ',
		'Map Style'=>'Map Style',
		'<br/>The map styles can be used to edit the style of the background map. <br /> To learn more about the use of map styles see: '=>'<br/>The map styles can be used to edit the style of the background map. <br /> To learn more about the use of map styles see: ',
		'Revert to default map style'=>'Revert to default map style',
		'Default'=>'Default',
		'Continue'=>'Continue',
		'<br/>Show All Labels' => '<br/>Show All Labels',
		'Maps will show region names with no data.' => 'Maps will show region names with no data.',
		'  Level at which labels will begin to appear.' => '  Level at which labels will begin to appear.',
		'<br/>Zoom level to show labels' => '<br/>Zoom level to show labels',
		
		//add 2 page
		'Add Map - Data Structure'=>'Add Map - Data Structure',
		'Now tell us about the structure of you data'=>'Now tell us about the structure of you data',
		'Header Rows'=>'Header Rows',
		'Tell us which row is used as the header. This is the row that denotes the geographic areas. You can only specify one row as a header.'=>'Tell us which row is used as the header. This is the row that denotes the geographic areas. You can only specify one row as a header.',
		'Data Rows'=>'Data Rows',
		'Tell us which rows are used to store data for an indicator. You can specify as many data rows as needed, but there must be at least one.'=>'Tell us which rows are used to store data for an indicator. You can specify as many data rows as needed, but there must be at least one.',
		'Indicator Columns'=>'Indicator Columns',
		'Tell us which columns are used to specify the indicators or questions that the data in the rest of the spreadsheet shows. You can have mutliple columns that represent mulitple levels. You can specify multiple indicator columns'=>'Tell us which columns are used to specify the indicators or questions that the data in the rest of the spreadsheet shows. You can have mutliple columns that represent mulitple levels. You can specify multiple indicator columns',
		'Region Columns'=>'Region Columns',
		'Tell us which columns are used to hold the information that pertains to a specific geographic region. You can speicfy mulitple region columns.'=>'Tell us which columns are used to hold the information that pertains to a specific geographic region. You can speicfy mulitple region columns.',
		'Total Column - Optional'=>'Total Column - Optional',
		'Which column represents the total for all geographic regions. You can only specify one total column'=>'Which column represents the total for all geographic regions. You can only specify one total column',
		'Total Label Column - Optional'=>'Total Label Column - Optional',
		'Which column presents the lable for the total column for each indicator (for example, this would not have to be labeled "total"). '=>'Which column presents the lable for the total column for each indicator (for example, this would not have to be labeled "total"). ',
		'Unit Column - Optional'=>'Unit Column - Optional',
		'Which column stores the units for each indicator. You can only specify one unit column.'=>'Which column stores the units for each indicator. You can only specify one unit column.',
		'Source Column - Optional'=>'Source Column - Optional',
		'Which column stores the name of the source for each indicator. You can only specify one source column.'=>'Which column stores the name of the source for each indicator. You can only specify one source column.',
		'Source Link Column - Optional'=>'Source Link Column - Optional',
		'Which column stores the link to the source for each indicator. You can only specify one source link column.'=>'Which column stores the link to the source for each indicator. You can only specify one source link column.',
		'Ignore - Optional'=>'Ignore - Optional',
		'Set any column or row to ignore if it should not be taken into consideration when rendering the map. You can set multiple columns and rows to ignore.'=>'Set any column or row to ignore if it should not be taken into consideration when rendering the map. You can set multiple columns and rows to ignore.',
		'Sheet'=>'Sheet',
		'Ignore this sheet?:'=>'Ignore this sheet?:',
		'All following sheets have the same data structure?'=>'All following sheets have the same data structure?',
		'Region'=>'Region',
		'Indicator'=>'Indicator',
		'Total'=>'Total',
		'Total Label'=>'Total Label',
		'Unit'=>'Unit',
		'Source'=>'Source',
		'Source Link'=>'Source Link',
		'Ignore'=>'Ignore',
		'Data'=>'Data',
		'Header'=>'Header',
		
		//add 3 page
		'Add Map - Validation'=>'Add Map - Validation',
		'Confirm that the below is true. If it is continue on, otherwise go back to the Data Structure page(s) and make corrections.'=>'Confirm that the below is true. If it is continue on, otherwise go back to the Data Structure page(s) and make corrections.',
		'Regions in sheet'=>'Regions in sheet',
		'Indicators in sheet'=>'Indicators in sheet',
		'If the indicators above are not correct please check the rows that you set as data and the columns you set as denoting indicators.'=>'If the indicators above are not correct please check the rows that you set as data and the columns you set as denoting indicators.',
		
		//add 4 page
		'Add Map - Geo Set-up'=>'Add Map - Geo Set-up',
		'Select which map template you want to use for your map'=>'Select which map template you want to use for your map',
		'Title'=>'Title',
		'Description'=>'Description',
		'Admin Level'=>'Admin Level',
		'Decimals'=>'Decimals',
		'There are no templates'=>'There are no templates',
		'No Rounding'=>'No Rounding',
		'Pan and zoom the map to adjust what the default view will be for the map'=>'Pan and zoom the map to adjust what the default view will be for the map',
		'You have to choose a template.'=>'You have to choose a template.',
		'Official'=>'Official',
		'Choose from official KoBoMap Templates, in bold, or use your own templates.'=>'Choose from official KoBoMap Templates, in bold, or use your own templates.',
		'If you want to add your own template click'=>'If you want to add your own template click',
		'here'=>'here',
		'here.'=>'here.',
		'You do not have access to this template'=>'You do not have access to this template',
		'If you want to copy someone else\'s template click'=>'If you want to copy someone else\'s template click',
		
		//add 5 page
		'Add Map - Geo Matching'=>'Add Map - Geo Matching',
		'Select how the regions specified in your data match up to the regions in the template you have chosen. A drop down box surrouned in <strong><span style="color:#ff9900;">orange</span></strong> need to be set since no match was deteced for them.'=>'Select how the regions specified in your data match up to the regions in the template you have chosen. A drop down box surrouned in <strong><span style="color:#ff9900;">orange</span></strong> need to be set since no match was deteced for them.',
		'Matches template region'=>'Matches template region',
		'All following sheets have the same region settings?'=>'All following sheets have the same region settings?',
		'Number of regions that couldn\'t be automatically matched:'=>'Number of regions that couldn\'t be automatically matched:',
		
		
		//map view page
		'Click on a section name to display the questions, then click on the questions to show the indicator(s). Click on the indicator to display its data on the map.'=>'Click on a section name to display the questions, then click on the questions to show the indicator(s). Click on the indicator to display its data on the map.',
		'Please be patient while the map is loading.'=>'Please be patient while the map is loading.',
		'powered by KoboToolbox'=>'powered by KoboToolbox',
		'Please select an indicator to display its data.'=>'Please select an indicator to display its data.',
		'Toggle Labels' => 'Toggle Labels',
		'Toggle Values' => 'Toggle Values',
		'We\'re sorry, but the template for this map is missing. We have alerted the map\'s owner. Please check back soon.'=>'We\'re sorry, but the template for this map is missing. We have alerted the map\'s owner. <br/><br/>Please check back soon.',
		
		
		//footer
		'Copyright KoBo'=>'Copyright KoBo',
		

		//misc
		'yes' => 'Yes',
		'no' => 'No',
		'n/a' => 'Not Applicable',
		'n/a explain' => 'Does not appy, and thus intentionally left blank',
		'his'=>'his',
		'her'=>'her',
		'error'=>'Error:',
		
		
	
		//Header
		'site name'=>'KoBo Maps',
		'tagline'=>'Visualize your data spatially',
		'Should this map be private'=>'Should this map be hidden from public view?',
			
			
		//sharing
		'Sharing Settings:'=>'Sharing Settings:',
		'Link to share map'=>'Link to share map',
		'I want to share this map with you:'=>'I want to share this map with you:',
		'Sharing'=>'Sharing',
		'map'=>'map',
		'Link to share this indicator'=>'Link to share this indicator',
		'Code to embed map'=>'Code to embed map',
		'By KoboMaps'=>'By KoboMaps',
		
		//statistics
		'Select Maps' => 'Select Maps',
		'Please choose a map' => 'Please choose a map',
		'End Date' => 'End Date',
		'Start Date' => 'Start Date',
		'Dates on the graph that are shaded grey correspond to weekends.' => 'Dates on the graph that are shaded grey correspond to weekends.',
		
		
		//public map page
		'Search Maps'=>'Search Maps',
		'Publicly viewable maps'=>'Publicly viewable maps',
		'is now missing its template.'=>'is now missing its template.',
		
		
		//templates main page
		'Templates are the base maps from which custom maps are made.'=>'Templates are the base maps from which custom maps are made.',
		'User'=>'User',
		"Are you sure you want to delete this template? \r\n\r\n You will break any maps that use this template."=>"Are you sure you want to delete this template? \\r\\n\\r\\n You will break any maps that use this template.",
		
		
		//templates edit page
		'maps use this template.'=>'maps use this template.',
		'Template Saved'=>'Template Saved',
		'is now missing the region'=>'is now missing the region',
		'Is an official template'=>'Is an official template',
		'Visibility'=>'Visibility',
		'Private'=>'Private',
		'Copy Template'=>'Copy Template',
		
		
		//templates view page
		'View Template'=>'View Template',
		'Decimal places rounded to'=>'Decimal places rounded to',
		'Center point latitude'=>'Center point latitude',
		'Center point longitude'=>'Center point longitude',
		'Default zoom level'=>'Default zoom level',
		
		//limits exceeded
		'Map Limit Exceeded'=>'Map Limit Exceeded',
		'Template Limit Exceeded'=>'Template Limit Exceeded',
		'Back to my maps'=>'Back to my maps',
		'We\'re sorry, but you have'=>'We\'re sorry, but you have',
		'maps and your account only allows you to have'=>'maps,<br/>and your account only allows you to have',
		'If you want to add a new map you must remove'=>'If you want to add a new map you must remove',
		'templates and your account only allows you to have'=>'templates,<br/>and your account only allows you to have',
		'If you want to add a new template you must remove'=>'If you want to add a new template you must remove',
		'maps.'=>'maps.',
		'templates.'=>'templates.',
		'SENTANCE_END'=>'.',
);


