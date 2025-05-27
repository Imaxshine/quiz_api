<?php
session_start(); // Lazima

function getQuestions() {
    $url = "https://bongoclass.com/quiz/brain/jj.php";
    $data = file_get_contents($url);
    return json_decode($data, true);
}

// Ukibofya SUBMIT (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $questions = $_SESSION['quiz_questions'] ?? [];
    $user_answers = $_POST['answers'] ?? [];

    $score = 0;
    $results = [];

    foreach ($questions as $question) {
        $qid = $question['id'];
        $correct = $question['answer'];
        $user = $user_answers[$qid] ?? null;

        $results[$qid] = [
            'is_correct' => ($user === $correct),
            'user_answer' => $user,
            'correct_answer' => $correct
        ];

        if ($user === $correct) $score++;
    }
} else {
    // Mara ya kwanza kufungua page: fetch maswali
    $data = getQuestions();
    $_SESSION['quiz_questions'] = $data['questions']; // Hifadhi kwenye session
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Quiz ya Ubongo</title>
    <style>
        .question { margin: 20px 0; border: 1px solid #ccc; padding: 10px; }
        .correct { background: #d4edda; }
        .incorrect { background: #f8d7da; }
    </style>
</head>
<body>
    <h1>Jaribio la Ubongo</h1>

    <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
        <h2>Matokeo:</h2>
        
        <?php foreach ($_SESSION['quiz_questions'] as $q): 
            $qid = $q['id'];
            $res = $results[$qid];
            $class = $res['is_correct'] ? 'correct' : 'incorrect';
        ?>
            <div class="question <?= $class ?>">
                <p><strong><?= htmlspecialchars($q['text']) ?></strong></p>
                <p>✅ Jibu lako:
                <?php foreach ($q['options'] as $opt) {
                    if ($opt['id'] == $res['user_answer']) {
                        echo htmlspecialchars($opt['text']);
                    }
                } ?></p>

                <?php if (!$res['is_correct']): ?>
                    <p style="color:red;">✗ Sahihi ni:
                    <?php foreach ($q['options'] as $opt) {
                        if ($opt['id'] == $res['correct_answer']) {
                            echo htmlspecialchars($opt['text']);
                        }
                    } ?>
                    </p>
                <?php else: ?>
                    <p style="color:green;">✓ Sahihi!</p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
        <h3>Alama zako: <?= $score ?>/<?= count($results) ?></h3>

        <!-- Reset button -->
        <form method="get">
            <button type="submit">Jaribu Maswali Mengine</button>
        </form>

    <?php else: ?>
        <!-- Onyesha maswali -->
        <form method="post">
            <?php $no = 1; ?>
            <?php foreach ($_SESSION['quiz_questions'] as $index => $q): ?>
                <div class="question">
                    <p><strong><?= ($no++) . '. ' . htmlspecialchars($q['text']) ?></strong></p>
                    <?php foreach ($q['options'] as $opt): ?>
                        <label>
                            <input type="radio" name="answers[<?= $q['id'] ?>]" value="<?= $opt['id'] ?>">
                            <?= htmlspecialchars($opt['text']) ?>
                        </label><br>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
            <button type="submit">Tuma Majibu</button>
        </form>
    <?php endif; ?>
</body>
</html>
