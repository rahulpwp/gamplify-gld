<?php
require_once('../../../wp-load.php');
global $wpdb;
$table = $wpdb->prefix . 'learndash_user_activity';

echo "Table: $table\n";

// Show columns
$columns = $wpdb->get_results("DESCRIBE $table");
echo "\nColumns:\n";
foreach($columns as $col) {
    echo $col->Field . " (" . $col->Type . ")\n";
}

// Show some quiz data
$quiz_data = $wpdb->get_results("SELECT * FROM $table WHERE activity_type = 'quiz' LIMIT 5");
echo "\nQuiz Data samples:\n";
print_r($quiz_data);

// Try to calculate average manually for one quiz if data exists
if (!empty($quiz_data)) {
    $quiz_id = $quiz_data[0]->post_id;
    echo "\nTesting AVG for Quiz ID: $quiz_id\n";
    
    // Check possible percentage columns
    $cols = array_map(function($c) { return $c->Field; }, $columns);
    $p_col = '';
    if (in_array('activity_percentage', $cols)) $p_col = 'activity_percentage';
    elseif (in_array('activity_score', $cols)) $p_col = 'activity_score';
    
    if ($p_col) {
        $avg = $wpdb->get_var("SELECT AVG($p_col) FROM $table WHERE post_id = $quiz_id AND activity_type = 'quiz' AND activity_status = 1");
        echo "Avg with status=1: $avg\n";
        
        $avg_any = $wpdb->get_var("SELECT AVG($p_col) FROM $table WHERE post_id = $quiz_id AND activity_type = 'quiz'");
        echo "Avg with ANY status: $avg_any\n";
        
        $count = $wpdb->get_var("SELECT COUNT(*) FROM $table WHERE post_id = $quiz_id AND activity_type = 'quiz'");
        echo "Total attempts count: $count\n";
    } else {
        echo "No percentage/score column found in: " . implode(', ', $cols) . "\n";
    }
}
