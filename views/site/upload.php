<?php
use yii\helpers\Url;
?>

<div class="image-upload">
    <form id="image-upload-form" enctype="multipart/form-data">
        <div class="d-flex m-2">
            <input class="form-control me-1" type="file" name="images[]" multiple accept="image/jpeg, image/png">
            <button type="submit" class="btn btn-primary">Загрузить</button>
        </div>
    </form>
    <div class="m-2">
        <div id="upload-message"></div>
        <button id="generate-pdf" class="btn btn-success" style="display:none;">Генерировать PDF</button>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<script>
    $('#image-upload-form').on('submit', function (e) {
        e.preventDefault();
        var formData = new FormData(this);
        $.ajax({
            url: '<?= Url::to(['image/upload']) ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (data) {
                // вывод сообщения
                $('#upload-message').text(data.message);
                $('#upload-message').removeClass();
                // краска алерта в зависимости от ситуаций
                $('#upload-message').addClass('alert alert-' + (data.success ? 'success' : 'danger'));
                if (data.success) {
                    // вывод кнопки генерации ПДФ при загрузке изображений
                    $('#generate-pdf').show();
                }
            }
        });
    });

    $('#generate-pdf').on('click', function () {
        window.location.href = '<?= Url::to(['image/generate-pdf']) ?>';
    });
</script>
