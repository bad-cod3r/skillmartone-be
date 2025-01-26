<?php
function uploadImage($file, $directory) {
    try {
        if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
            return [
                'success' => false,
                'message' => 'No file uploaded'
            ];
        }

        $imageExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $allowedTypes = ['jpg', 'jpeg', 'png'];

        if (!in_array(strtolower($imageExtension), $allowedTypes)) {
            return [
                'success' => false,
                'message' => 'Invalid image type. Allowed types: jpg, jpeg, png'
            ];
        }

        $imageName = uniqid() . '.' . $imageExtension;
        $uploadDir = __DIR__ . '/../../public/uploads/' . $directory . '/';
        $uploadPath = $uploadDir . $imageName;

        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            return [
                'success' => true,
                'path' => '/uploads/' . $directory . '/' . $imageName
            ];
        } 

        return [
            'success' => false,
            'message' => 'Failed to upload image'
        ];

    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => 'Upload error: ' . $e->getMessage()
        ];
    }
}