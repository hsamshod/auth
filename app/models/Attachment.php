<?php

/**
 * Class Attachment.
 */
class Attachment
{
    const FOLDER_PATH = 'store';

    const DEFAULT_PHOTO = 'default.png';

    /**
     * Creates attachment record and stores file.
     *
     * @param array $uploaded_file      Uploaded file info.
     * @param string $key               Uploaded file key in $_FILES array.
     *
     * @return bool             Whether the file is successfully stored.
     */
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
