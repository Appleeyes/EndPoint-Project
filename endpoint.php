<?php
// Function to get the current day of the week
function getCurrentDay() {
    $daysOfWeek = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    return $daysOfWeek[date('w')];
}

// Function to get the current UTC time with validation of +/-2 minutes
function getCurrentUTC() {
    $currentTime = time();
    $validWindow = 120; // 2 minutes in seconds
    $currentUTC = gmdate('Y-m-d\TH:i:s\Z', $currentTime);

    // Check if the time is within the valid window
    $serverTime = strtotime($currentUTC);
    if (abs($serverTime - $currentTime) <= $validWindow) {
        return $currentUTC;
    } else {
        return false;
    }
}

// Check if slack_name and track parameters are provided in the URL
if (isset($_GET['slack_name']) && isset($_GET['track'])) {
    $slackName = $_GET['slack_name'];
    $track = $_GET['track'];

    // Validate track
    if ($track === '') {
        http_response_code(400); // Bad Request
        die('Invalid track value.');
    }

    // Get current day and UTC time
    $currentDay = getCurrentDay();
    $currentUTC = getCurrentUTC();

    if ($currentUTC === false) {
        http_response_code(500); // Internal Server Error
        die('Unable to retrieve UTC time.');
    }

    // Construct the JSON response
    $response = [
        "slack_name" => $slackName,
        "current_day" => $currentDay,
        "utc_time" => $currentUTC,
        "track" => $track,
        "github_file_url" => "https://github.com/username/repo/blob/main/file_name.ext",
        "github_repo_url" => "https://github.com/username/repo",
        "status_code" => 200
    ];

    // Set response headers and send JSON
    header('Content-Type: application/json');
    echo json_encode($response);
} else {
    http_response_code(400); // Bad Request
    echo 'Missing required parameters.';
}
