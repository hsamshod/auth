<?php

class Attachment
{
    const FOLDER_PATH = 'store';

    const DEFAULT_PHOTO = 'default.png';

    public static function create($uploaded_file, $key)
    {
        if (Validator::validateUploadedPhoto($uploaded_file, $key)) {
            $path_info = pathinfo($uploaded_file['name'][$key]);
            $extension = $path_info["extension"];
            $user_photo = uniqid() . '.' . $extension;
            $destination = static::FOLDER_PATH . DIRECTORY_SEPARATOR . $user_photo;
            if (move_uploaded_file($uploaded_file['tmp_name'][$key], $destination)) {
                return app()->user()->setPhoto($user_photo);
            }
        }

        return false;
    }
}
