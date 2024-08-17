<?php

namespace app\services;

use yii\base\ErrorException;

class PdfService
{
    // Метод для генерации PDF файла
    public static function actionGeneratePdf()
    {
        try {
            $countImgPerPage = 4;
            $images = glob('uploads/*.{jpg,jpeg,png}', GLOB_BRACE);
            shuffle($images); // Перемешиваем массив изображений
            if (count($images)) {
                ob_start();
                $mpdf = new \Mpdf\Mpdf();
                $imgPositions = self::generatePdfImgPosition($countImgPerPage);

                $positionIndex = 0;
                foreach ($images as $key => $image) {
                    if (($key === 0) || (($key) % $countImgPerPage === 0)) {
                        $positionIndex = 0;
                        $mpdf->AddPage();
                    }
                    list($x, $y, $imageWidth, $imageHeight) = $imgPositions[$positionIndex]; // Расчитываем позиции изображений
                    $mpdf->Image($image, $x, $y, $imageWidth, $imageHeight, 'jpg', '', true, false); // Вставка изображений в ПДФ
                    unlink($image); // очистка папки uploads
                    $positionIndex++;
                }
                ob_end_flush();
                $mpdf->Output('generated.pdf', 'D'); // Генерация и скачивание файла
            }
            return false;
        } catch (ErrorException $e) {
            return false;
        }
    }

    // Генерируем позиции изображений изходя из количества изображений на странице
    public static function generatePdfImgPosition($countImgPerPage)
    {
        $a4Height = 297;
        $a4Width = 210;
        list($imagesPerRow, $imagesPerColumn) = self::findTwoProductNumbers($countImgPerPage);
        $imageWidth = floor($a4Width / $imagesPerRow);
        $imageHeight = floor($a4Height / $imagesPerColumn);

        // Сгенерировать координаты
        $images = [];
        for ($i = 0; $i < $countImgPerPage; $i++) {
            $row = floor($i / $imagesPerRow);
            $col = floor($i % $imagesPerRow);

            $x = $col * $imageWidth; // X координаты
            $y = $row * $imageHeight; // Y координаты

            // Проверка, не превышено ли количество изображений, которые можно разместить в формате А4.
            if ($y + $imageHeight > $a4Height) {
                break;
            }

            $images[$i] = [
                $x,
                $y,
                $imageWidth,
                $imageHeight
            ];
        }
        return $images;
    }

    // Поиск наибольших множителей N
    static function findTwoProductNumbers($n)
    {
        $pairs = [];
        for ($i = 1; $i <= sqrt($n); $i++) {
            if ($n % $i === 0) {
                $j = $n / $i;
                $pairs[] = [$i, $j];
            }
        }
        return count($pairs) ? $pairs[count($pairs) - 1] : [1, 1];
    }
}