<?php
require 'config.php';

$errors = [];
$messages = [];


if (!empty($_FILES)) {


    $file = $_FILES['files'];

    function inspectionFile($file)
    {
        $fileName = $file['name'][0];

        // Проверяем размер
        if ($file['size'][0] > UPLOAD_MAX_SIZE) {
            $errors[] = 'Недопустимый размер файла ' . $fileName;
        }

        // Проверяем формат
        if (!in_array($file['type'][0], ALLOWED_TYPES)) {
            $errors[] = 'Недопустимый формат файла ' . $fileName;
        }

        if (file_exists(UPLOAD_DIR . '/' . basename($fileName))){
            $errors[] = 'Файл ' . $fileName . ' уже существует';
        }

        $filePath = UPLOAD_DIR . '/' . basename($fileName);

        // Пытаемся загрузить файл
        if (!move_uploaded_file($file['tmp_name'][0], $filePath)) {
            $errors[] = 'Ошибка загрузки файла ' . $fileName;
        }

        return $errors;
    }
    $errors = inspectionFile($file);

    if (empty($errors)) {
        $messages[] = 'Файл был загружен';
    }
}

if (!empty($_POST['name'])) {

    $filePath = UPLOAD_DIR . '/' . $_POST['name'];
    $commentPath = COMMENT_DIR . '/' . $_POST['name'] . '.txt';

    if (file_exists($filePath)){
         unlink($filePath);
    }
    if (file_exists($commentPath)) {
        unlink($commentPath);
    }

    $messages[] = 'Файл был удален';
}

$files = scandir(UPLOAD_DIR);
$files = array_filter($files, function ($file) {
    return !in_array($file, ['.', '..', '.gitkeep']);
});

?>


<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Галерея изображений</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="/css/index.css">
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
        <div class="files-block">
            <h2 class="files-block__h2">Список файлов:</h2>
            <div class="break"></div>
            <div class="files-block__files-list">
                <?php if (!empty($files)) {
                    foreach ($files as $file) :
                ?>
                        <div class="files-list__frame">
                            <form method="post">
                                <input type="hidden" name="name" value="<?php echo $file; ?>">
                                <button type="submit" class="files-list__delete" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </form>
                            <a href="<?php echo URL . '/file.php?name=' . $file; ?>" title="Просмотр полного изображения">
                                <img src="<?php echo URL . '/' . UPLOAD_DIR . '/' . $file ?>">
                            </a>
                        </div>
                <?php endforeach;
                } ?>
            </div>
        </div>

        <br>

        <form method="POST" class="upload-field" enctype="multipart/form-data">
            <h2 class="upload-field__h2">Добавить изображение:</h2>
            <div class="break"></div>
            <div class="upload-field__select-file">
                <label class="label">
                    <i class="material-icons">attach_file</i>
                    <span class="title">Добавить файл</span>
                    <input type="file" name="files[]" id="selectFile">
                </label>
            </div>
            <canvas id="preview" width="200" height="170" class="upload-field__preview"></canvas>
            <ul class="upload-field__preview-info">
                <li>Имя файла: <span id="infoName"></span></li>
                <li>Тип файла: <span id="infoType"></span></li>
                <li>Размер файла: <span id="infoSize"></span></li>
            </ul>
            <div class="break"></div>
            <input type="submit" value="Отправить" class="upload-field__upload" disabled>
            <div class="break"></div>
            <div class="upload-field__limits">Максимальный размер файла: <?php echo round(UPLOAD_MAX_SIZE / 1000000) . ' Мб.' ?><br>
                Допустимые форматы: <?php echo implode(', ', ALLOWED_TYPES) . '.' ?>
            </div>
        </form>
    </div>

    <script src="script.js"></script>
</body>

</html>