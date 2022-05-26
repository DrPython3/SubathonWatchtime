<?php

// ====================================================================================================
// SUBATHON WATCHTIME for StreamElements by DrPython3 @ GitHub.com - 2022/05/26, v1
// ====================================================================================================

// ----------------------------------------------------------------------------------------------------
// ARGUMENTS:
// ----------------------------------------------------------------------------------------------------

if (!array_key_exists('channel', $_REQUEST)) {
    echo 'Error: Missing argument "channel"!';
    return;
}
if (!array_key_exists('action', $_REQUEST)) {
    echo 'Error: Missing argument "action" (get/update)!';
    return;
}

// ----------------------------------------------------------------------------------------------------
// DATA:
// ----------------------------------------------------------------------------------------------------

$channel = $_REQUEST['channel'];
$file = "{$channel}.subathon.json";

// Check for existing data and create a new data-file for the given channel, if none exists:

if (file_exists($file)) {
    $data = json_decode(file_get_contents($file), true);
} else {
    $data = [];
}

// Update the data for the given channel:

if ($_REQUEST['action'] == 'update') {
    $updatetime = time();
    if (array_key_exists('$', $data) && $updatetime - $data['$'] < 600) {
        $url = "http://tmi.twitch.tv/group/user/{$channel}/chatters";
        $users = json_decode(file_get_contents($url), true)['chatters'];
        // Only update the data if the channel is live (streamer is in chat):
        if (empty($users['broadcaster'])) {
            echo 'Error: Stream is offline!';
            return;
        }
        // Get viewer list and calculate data:
        $chatters = array_merge($users['vips'], $users['viewers'], $users['moderators']);
        $passed = $updatetime - $data['$'];
        foreach ($chatters as $viewer) {
            if (!array_key_exists($viewer, $data))
                $data[$viewer] = 0;
            $data[$viewer] += $passed;
        }
    }
    $data['$'] = $updatetime;
    file_put_contents($file, json_encode($data));
    echo 'Success: Viewer data updated!';
}

// Look for and return data for a given user:

elseif ($_REQUEST['action'] == 'get') {
    // Check for existing data first:
    if (empty($data)) {
        echo 'Error: Update viewer data first!';
        return;
    }
    // Return error if username is missing in request:
    if (!array_key_exists('user', $_REQUEST)) {
        echo 'Error: Missing argument "user"!';
        return;
    }
    // Calculate watchtime for given user:
    define("username", $_REQUEST['user']);
    if (array_key_exists(username, $data)) {
        $passed = time() - $data['$'];
        if ($passed > 600)
            $passed = 0;
        $s = $data[username] + $passed;
        $m = intdiv($s, 60);
        $s -= $m * 60;
        $h = intdiv($m, 60);
        $m -= $h * 60;
        $d = intdiv($h, 24);
        $h -= $d * 24;
        // Build message blocs:
        $args = [];
        if ($d > 0 && $d <= 1) array_push($args, "{$d} day");
        if ($d > 1) array_push($args, "{$d} days");
        if ($h > 0 && $h <= 1) array_push($args, "{$h} hour");
        if ($h > 1) array_push($args, "{$h} hours");
        if ($m > 0 && $m <= 1) array_push($args, "{$m} minute");
        if ($m > 1) array_push($args, "{$m} minutes");
        // Return chat message with userdata for StreamElements:
        echo username . ' is watching for ' . implode(', ', $args) . ' now!';
    } else echo 'Sorry! But ' . username . ' is not watching or has just arrived here ...';
}

?>
