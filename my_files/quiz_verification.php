<?php
$current_page = 'my_form';

///validating input data
$required = ['name', 'email', 'study', 'time', 'learner', 'deadline', 'reward'];

foreach($required as $field){
	if(!isset($_GET[$field]) || 
	(is_array($_GET[$field]) && count($_GET[$field])===0) ||
	(!is_array($_GET[$field]) && trim($_GET[$field]) === '')){
		header('Location: my_form.php'); 
		exit();
	}
}

$name     = $_GET['name'];
$email    = $_GET['email'];
$study    = $_GET['study'];
$time     = $_GET['time'];
$deadline = $_GET['deadline'];
$learners = (array) $_GET['learner'];
$rewards  = (array) $_GET['reward'];


///each personality type starts w/ score 0
$scores = [
    'solo' => 0,
    'social' => 0,
    'planner' => 0,
    'procrastinator' => 0
];

///q1 study preference
if ($study === 'alone') $scores['solo'] += 2;
if ($study === 'group') $scores['social'] += 2;
if ($study === 'tutor') $scores['solo']  += 1;

///q2 productivity
if ($time === 'morning')    $scores['planner']        += 2;
if ($time === 'afternoon')  $scores['social']         += 1;
if ($time === 'night')      $scores['procrastinator'] += 2;

///q3 learning style
if (in_array('visual', $learners))      $scores['solo']    += 1;
if (in_array('auditory', $learners))    $scores['social']  += 1;
if (in_array('kinesthetic', $learners)) $scores['planner'] += 1;

///q4 deadline
if ($deadline === 'early')       $scores['planner']        += 2;
if ($deadline === 'balanced')    $scores['planner']        += 1;
if ($deadline === 'lastminute')  $scores['procrastinator'] += 2;

///q5 rewards
if (in_array('nap', $rewards))      $scores['solo']   += 1;
if (in_array('food', $rewards))     $scores['solo']   += 1;
if (in_array('friends', $rewards))  $scores['social'] += 2;
if (in_array('shopping', $rewards)) $scores['social'] += 1;

///determining type
$max_score = max($scores);
$result_type = array_keys($scores, max($scores))[0];


$results_info = [
    'solo' => [
        'title' => 'The Solo Scholar',
        'emoji' => '🧐',
        'desc' => 'You do your best work alone, 
		focusing deeply without distractions. 
		You are self-motivated and disciplined.'
    ],
    'social' => [
        'title' => 'The Social Butterfly',
        'emoji' => '🦋',
        'desc' => 'You thrive in group settings! 
		Bouncing ideas off others and studying with friends 
		keeps you engaged and motivated.'
    ],
    'planner' => [
        'title' => 'The Balanced Planner',
        'emoji' => '🗓️',
        'desc' => 'You are organized and methodical. 
		You like to plan your work, start early, 
		and avoid last-minute stress.'
    ],
    'procrastinator' => [
        'title' => 'The Deadline Dynamo',
        'emoji' => '⚡',
        'desc' => 'You work best under pressure. 
		That last-minute rush is when you find your 
		greatest focus and creativity.'
    ]
];

?>
<!DOCTYPE html>
<html>
<head>
	
	<!--meta data-->
    <meta name="author" content="Tristan Geary">
    <meta name="Description" content="Quiz Results">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="my_style.css">
	
	<!--title-->
    <title>Quiz Results</title>
    
	
	<!--internal styling-->
    <style>
        .results-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            margin-top: 20px;
        }
        .result-card {
            border: 2px solid #ccc;
            border-radius: 8px;
            padding: 15px;
            width: 200px;
            text-align: center;
            background-color: #f9f9f9;
            transition: all 0.3s ease;
        }
        .result-card h3 {
            margin-top: 0;
            color: #333;
        }
        .result-card .emoji {
            font-size: 3rem;
        }
        /* This is the highlight class from the instructions [cite: 44, 46] */
        .highlight {
            border-color: #007bff;
            background-color: #e7f3ff;
            box-shadow: 0 4px 8px rgba(0,123,255,0.2);
            transform: scale(1.05);
        }
		
		
    </style>
	
	<!--linking the nav menu javascript file-->
	<script src="nav_menu.js"></script>
	
	
	
</head>


<body>
	
	<div class="body_wrapper">
	<!--linking the actual nav-->
	<?php include 'includes/nav.php'; ?>
	
    <main class="page-body" style="text-align: center;">
        <br>
		
		<!--displaying results-->
        <h1>Here Are Your Results, <?php echo htmlspecialchars($_GET['name']); ?>!</h1>
        
        <p>Based on your answers, your student type is: <strong><?php echo $results_info[$result_type]['title']; ?></strong></p>
        
        <hr>
        
        <div class="results-container">
        
            <?php
            
            foreach ($results_info as $type => $info) {
                $class = ($type === $result_type) ? 'result-card highlight' : 'result-card';
                
                echo '<div class="' . $class . '">';
                echo '<div class="emoji">' . $info['emoji'] . '</div>';
                echo '<h3>' . $info['title'] . '</h3>';
                echo '<p>' . $info['desc'] . '</p>';
                echo '</div>';
            }
            ?>
            
        </div>
		
        <br>
		
    </main>

	<?php include 'includes/footer.php' ?>
	</div>
</body>
</html>