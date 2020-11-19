<?php
session_start();

require 'config.php';

$errors = [];
$messages = [];

$imageFileName = $_GET['name'];
$commentFilePath = COMMENT_DIR . '/' . $imageFileName . '.txt';

// Если коммент был отправлен
if (!empty($_POST['comment'])) {

    $comment = trim($_POST['comment']);

    // Валидация коммента
    if ($comment === '') {
        $errors[] = 'Вы не ввели текст комментария';
    }

    //Ограничение на время между комментариями от одного пользователя
    $pause = time() - $_SESSION[$imageFileName];
    if ($pause < COMMENT_PAUSE){
        $errors[] = 'Подождите еще ' . (COMMENT_PAUSE - $pause) . ' секунд, прежде чем добавить еще комментарий';
    }

    // Если нет ошибок записываем коммент
    if (empty($errors)) {

        // Чистим текст, земеняем переносы строк на <br/>, дописываем дату
        $comment = strip_tags($comment);
        $comment = str_replace(array("\r\n", "\r", "\n"), "<br>", $comment);
        $comment = '<b>' . date('d.m.y H:i') . ': </b>' . $comment;

        // Дописываем текст в файл (будет создан, если еще не существует)
        file_put_contents($commentFilePath,  $comment . "\n", FILE_APPEND);

        $messages[] = 'Комментарий был добавлен';

        $_SESSION[$imageFileName] = time();

    }
}

// Получаем список комментов
$comments = file_exists($commentFilePath)
    ? file($commentFilePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES)
    : [];
?>


<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Просмотр изображения</title>
    <link rel="stylesheet" href="/css/file.css">
</head>

<body>

<div class="messages">
        <?php foreach ($messages as $message) : ?>
            <p><?php echo $message; ?></p>
        <?php endforeach; ?>
    </div>
    <div class="errors">
        <?php if (!empty($errors)) {
            foreach ($errors as $error) : ?>
                <p><?php echo $error; ?></p>
        <?php endforeach;
        } ?>
    </div>

    <div class="container">
        <h1><a href="<?php echo URL; ?>">Галерея изображений</a></h1>
        <h2 class="image-name"><?php echo $imageFileName ?></h2>
        <img src=<?php echo UPLOAD_DIR . '/' . $imageFileName ?> alt=<?php echo $imageFileName ?>>

        <div class="comments">
            <h3 class="comments__h3">Комментарии:</h3><br>
            <?php if (!empty($comments)) : ?>
                <?php foreach ($comments as $key => $comment) : ?>
                    <p class="comments__comment"><?php echo $comment; ?></p>
                <?php endforeach; ?>
            <?php else : ?>
                <p>Пока ни одного коммантария, будте первым!</p>
            <?php endif; ?>
        </div>
        <br>
        <form method="POST" class="new-comment">
            <label for="comment">Ваш комментарий:</label>
            <textarea class="new-comment__area" name="comment" rows="3" required></textarea>
            <button type="submit" class="new-comment__submit">Отправить</button>
        </form>
    </div>

    <script>
        const messages = document.querySelector('.messages');
        const errors = document.querySelector('.errors');

        if (messages.innerText != "") {
            messages.classList.add('show');
        }

        if (errors.innerText != "") {
            errors.classList.add('show');
        }
    </script>
</body>

</html>