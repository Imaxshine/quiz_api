<?php
$url = "https://bongoclass.com/quiz/brain/jj.php";

$ch = curl_init();
curl_setopt($ch,CURLOPT_URL,$url);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
$responses = curl_exec($ch);
if(curl_error($ch))
{
    die("cURL Error: " . curl_errno($ch));
}
curl_close($ch);

//decode the Responses from the API 
$data = json_decode($responses,true);

if($_SERVER['REQUEST_METHOD'] === "POST")
{
    $user_answers = $_POST['answers'] ?? [];

$score = 0;
$results = [];

foreach($data['questions'] as $question){
    $question_id = $question['id'];
    $correct_answer = $question['answer'];
    $user_answer = $user_answers[$question_id] ?? "";

    $results[$question_id] = [
        'is_correct'=>($user_answer === $correct_answer),
        'correct_answer'=>$correct_answer,
        'user_answer'=>$user_answer
    ];
    if($user_answer === $correct_answer){
        $score++;
    }
}

}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz</title>
    <style>
        body{
            position: relative;
        }
        .title{
           text-align: center;
           background-color: blueviolet;
           color:#fff;
           padding: 10px 10px;
           position: fixed;
           top: -20px;
           left: 10px;
           border-radius: 10px;
           width: 100vw;
        }
        .container{
            padding: 20px 35px;
            background-color: #DDC;
        }
        .questions{
           /* background-color: #fff;
           border: 2px solid red; */
        }
        .eachSection{
            background-color: #fff;
            padding: 0 10px;
            height: auto;
            border-radius: 12px;
            box-shadow: 3px 6px 4px 3px rgba(0,0,0,0.3);
        }
        .eachSection p{
            padding: 10px;
        }
        .btnHolder{
            margin: 25px auto;
        }
        .btnHolder .btn{
            font-family: Cambria, Cochin, Georgia, Times, 'Times New Roman', serif;
            font-weight: bolder;
            padding: 7px 10px;
            border:none;
            border-radius: 10px;
            outline: none;
            background-color: blue;
            color: #fff;
            width: 30%;
            cursor: pointer;
            transition: 1s ease;
        }
        .btnHolder .btn:hover{
            transform: translateX(10px);
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="title">Maswali na majibu</h2>

        <div>
            <?php if(!empty($score)): ?>
                <h4>Umepata alama <?php echo $score; ?> / <?php echo count($data['questions']); ?> </h4>
            <?php endif; ?>
        </div>
        <form method="post">
            <div class="questions">
                <?php $qnum = 1; ?>
            <?php foreach($data['questions'] as $question): ?>
                
                <div class="eachSection">
                    <p> <?php echo $qnum++  . ": " . htmlspecialchars($question['text']);  ?></p>

                    <?php foreach($question['options'] as $opt): ?>
                        <input type="radio"
                        name="answers[<?php echo $question['id']; ?>]"
                        id="opt_<?php echo $opt['id']; ?>"
                        value="<?php echo $opt['id']; ?>"
                        <?php if(isset($user_answers[$question['id']]) && $user_answers[$question['id']] == $opt['id']){ echo "checked";} ?> required>
                        <label for="opt_<?php echo $opt['id']; ?>"><?php echo $opt['text']; ?></label><br>
                    <?php endforeach; ?>

                    <?php if(isset($results)): ?>
                        <?php if($results[$question['id']]['is_correct']): ?>
                            <p style="color: green;"> ✔ Correct answer</p>
                        <?php else: ?>
                            <p style="color: red;"> ❌ Wrong answer. The answer was: 
                                <?php foreach($question['options'] as $opt): ?>
                                    <?php if($opt['id'] == $results[$question['id']]['correct_answer'])
                                    {
                                        echo $opt['text'];
                                        break;
                                    }
                                    ?>
                                <?php endforeach; ?>
                            </p>
                        <?php endif; ?>
                    <?php endif; ?>
                    
                </div>
            <?php endforeach; ?>

            <div class="btnHolder">
            <button class="btn" type="submit">Submit</button>
            </div>
        </div>
        </form>
    </div>
</body>
</html>

