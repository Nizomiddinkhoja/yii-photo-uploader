<?php

namespace app\controllers;

use app\models\ImageUploadForm;
use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\web\UploadedFile;

class SiteController extends Controller
{
    const A4_HEIGHT = 297;
    const A4_WIDTH = 210;

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('upload');
    }

    // Метод для обработки загрузки изображений
    public function actionUpload()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = new ImageUploadForm();
        $model->images = UploadedFile::getInstancesByName('images');

        if ($model->upload()) {
            return ['success' => true, 'message' => 'Изображения успешно загружены.'];
        } else {
            return ['success' => false, 'message' => 'Загрузка не удалась.'];
        }
    }

    // Метод для генерации PDF файла
    public function actionGeneratePdf()
    {
        ob_start();

        $images = glob('uploads/*.{jpg,jpeg,png}', GLOB_BRACE);
        shuffle($images); // Перемешиваем массив изображений
        if (count($images)) {
            $mpdf = new \Mpdf\Mpdf();
            foreach ($images as $key => $image) {
                if (($key === 0) || (($key) % 4 === 0)) {
                    $positionIndex = 0;
                    $mpdf->AddPage();
                }
                list($x, $y) = $this->getPdfImgPosition($positionIndex); // Расчитываем позиции изображений
                $mpdf->Image($image, $x, $y, 210 / 2, 297 / 2, 'jpg', '', true, false); // Вставка изображений в ПДФ
                unlink($image); // очистка папки uploads
                $positionIndex++;
            }
            ob_end_flush();

            $mpdf->Output('generated.pdf', 'D'); // Генерация и скачивание файла
        }
    }

// Расчитываем позиции изображений
    private function getPdfImgPosition($key)
    {
        $x = 0;
        $y = 0;
        switch ($key) {
            case 1:
                $x = self::A4_WIDTH / 2;
                break;
            case 2:
                $y = self::A4_HEIGHT / 2;
                break;
            case 3:
                $x = self::A4_WIDTH / 2;
                $y = self::A4_HEIGHT / 2;
                break;
        }
        return [$x, $y];

    }

}
