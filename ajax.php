<?php
session_start();
header('Content-type: application/json');

require_once('google-calendar-api.php');

try {
	// Get event details
	$event = $_POST['event_details'];
	$capi = new GoogleCalendarApi();
	
	switch($event['operation']) {
		case 'create':
			// Get user calendar timezone
			if(!isset($_SESSION['user_timezone']))
				$_SESSION['user_timezone'] = $capi->GetUserCalendarTimezone($_SESSION['access_token']);

			// Create event on primary calendar
			$event_id = $capi->CreateCalendarEvent('primary', $event['title'], $event['all_day'],  $event['recurrence'], $event['recurrence_end'], $event['event_time'], $_SESSION['user_timezone'], $_SESSION['access_token']);

			echo json_encode([ 'event_id' => $event_id ]);
			break;

		case 'update':
			// Update event on primary calendar
			$capi->UpdateCalendarEvent($event['event_id'], 'primary', $event['title'], $event['all_day'], $event['event_time'], $_SESSION['user_timezone'], $_SESSION['access_token']);

			echo json_encode([ 'updated' => 1 ]);
			break;

		case 'delete':
			// Delete event on primary calendar
			$capi->DeleteCalendarEvent($event['event_id'], 'primary', $_SESSION['access_token']);

			echo json_encode([ 'deleted' => 1 ]);
			break;
	}
}
catch(Exception $e) {
	header('Bad Request', true, 400);
    echo json_encode(array( 'error' => 1, 'message' => $e->getMessage() ));
}

?>