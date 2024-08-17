<?php

namespace app\controllers;

use app\models\ImageUploadForm;
use app\services\PdfService;
use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\web\UploadedFile;

class SiteController extends Controller
{
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
            return ['success' => true, 'message' => 'Загрузка не удалась.'];
        }
    }

    // Метод для генерации PDF файла
    public function actionGeneratePdf()
    {
        $generatePdf = PdfService::actionGeneratePdf();
        if (!$generatePdf) {
            Yii::$app->session->setFlash('error', "Ошибка при генерации PDF файла!");
            return $this->goBack(Yii::$app->request->referrer);
        }
    }

}
