<?php
session_start();
function getQuizQuestions(){
    $url = "https://bongoclass.com/quiz/brain/jj.php";
    $data = file_get_contents($url);
    return json_decode($data,true);
}
//Baada ya kutuma POST Form (Ku submit)

$score = 0;
$results = [];

if($_SERVER['REQUEST_METHOD'] === "POST"){
    $questions = $_SESSION['questions'] ?? [];
    $user_answers = $_POST['answers'] ?? [];

    foreach($questions as $question){
        $questionId = $question['id'];
        $correctAnswer = $question['answer'];
        $userAnswer = $user_answers[$questionId] ?? null;

        $results[$questionId] = [
            'is_correct'=>($userAnswer === $correctAnswer), //Hapa tunaifadhi True / False
            'correct'=>$correctAnswer, //Jibu sahihi
            'user_answer'=>$userAnswer
        ];

        //Angalia kama jibu ni sahihi
        if($userAnswer === $correctAnswer)
        {
            $score++;
        }
    }
    
}else{
    //Kama Method sio Post Basi ukurasa unarun kwa mara ya kwanza na default ni GET method
    $data = getQuizQuestions();
    $_SESSION['questions'] = $data['questions'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <title>Chemsha bongo</title>
    <style>
        .correct{
            background: #d4edda;
        }
        .incorrect{
            background: #f8d7da;
        }
        .question{
            transition: 1s linear;
        }
        .question:hover{
            transform: translateX(25px);
        }
    </style>
</head>
<body>
    <h2>Chemsha Bongo</h2>

    <?php if($_SERVER['REQUEST_METHOD'] === "POST"): ?>
        <?php $num = 1; foreach($_SESSION['questions'] as $q):
        $qid = $q['id'];
        $res = $results[$qid];
        $class = $res['is_correct'] ? 'correct' : 'incorrect';
        ?>

        <div class="question shadow-lg p-2 rounded <?= $class; ?>">
            <p>
                <strong><?= $num++ . ": " . htmlspecialchars($q['text']); ?></strong>
            </p>

            <p> ✔ Jibu lako:
                <?php foreach($q['options'] as $opt): ?>
                    <?php if($opt['id'] == $res['user_answer']): ?>
                        <span> <?= htmlspecialchars($opt['text']) ?> </span>
                    <?php endif; ?>
                <?php endforeach; ?>
            </p>

            <?php if(!$res['is_correct']): ?>
                <p style="color: red;"> ❌ Sahihi ni:
                    <?php foreach($q['options'] as $opt): ?>
                        <?php if($opt['id'] == $res['correct']): ?>
                            <span><?= htmlspecialchars($opt['text']); ?></span>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </p>
            <?php else: ?>
                <p style="color: green;"> ✓ Sahihi</p>
            <?php endif; ?>
        </div>

    <?php endforeach; ?>

                <div class="ms-5">
                    <?= "Alama: " . $score . "/" . count($results) ?>
                </div>
    <!-- Reset Button -->
    <form method="get" class="my-4">
          <button class="btn btn-primary w-100" type="submit">Jaribu Maswali mengine</button>
    </form>

    <?php else: ?>
        <!-- Onesha maswali -->
         
            <form method="POST">
                <?php $qnum = 1; foreach($_SESSION['questions'] as $question) :?>
                    <div class="question shadow-lg p-2 rounded">
                        <p>
                            <strong> <?= $qnum++ . ": " . htmlspecialchars($question['text'])  ?> </strong>
                        </p>
                        <!-- Majibu (Multiple choise) -->
                         <?php foreach($question['options'] as $opt): ?>
                            <label>
                                <input type="radio"
                                name="answers[<?= $question['id']; ?>]"
                                value="<?= $opt['id']; ?>" required>
                                <?= htmlspecialchars($opt['text']); ?>
                            </label><br>
                        <?php endforeach; ?> 
                    </div>
                <?php endforeach; ?>
                <!-- Submit button -->
            <div class="my-4">
                <button class="btn btn-primary w-100">Wasilisha</button>
            </div>

            </form>
    <?php endif; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
</body>
</html>