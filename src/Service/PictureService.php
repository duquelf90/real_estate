<?php

namespace App\Service;

use Exception;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class PictureService
{
    private $params;

    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
    }

    public function add(UploadedFile $picture, ?string $folder = '')
    {
        $name = md5(uniqid()) . '.' . $picture->guessExtension();
        $path = $this->params->get('images_directory') . $folder;
        if (!file_exists($path)) {
            mkdir($path, 0755, true);
        }
        $picture->move($path . '/', $name);
        return $name;
    }

    public function delete(string $fichier, ?string $folder = ''): bool
    {
        $path = $this->params->get('images_directory') . $folder;
        $mini = $path . "/" . $fichier;
        if (file_exists($mini)) {
            unlink($mini);
        }
        return true;
    }
}
