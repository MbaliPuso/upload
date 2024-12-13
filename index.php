<?php
// Include Composer's autoloader
require 'vendor/autoload.php';

use Intervention\Image\ImageManagerStatic as Image;

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Credentials: true');

// Check if file is uploaded
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file'];
    $uploadDir = 'uploads/'; // Directory to save the uploaded file
    $uniqueName = uniqid() . '-' . time() . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);

    try {
        // Check for file upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('File upload failed with error code ' . $file['error']);
        }

        // Move the uploaded file to the 'uploads' folder
        if (!is_dir($uploadDir)) {
            throw new Exception('Upload directory does not exist');
        }

        if (!is_writable($uploadDir)) {
            throw new Exception('Upload directory is not writable');
        }

        if (!move_uploaded_file($file['tmp_name'], $uploadDir . $uniqueName)) {
            throw new Exception('Error moving uploaded file');
        }

        // Open the uploaded image
        $image = Image::make($uploadDir . $uniqueName);

        // Resize the image
        $image->resize(150, 150);

        // Save the image (overwrite the original image)
        $image->save();

        echo json_encode(["message" => "File uploaded successfully!", "filePath" => "https://demos.kickass.co.za/jimnycrewnew/upload/uploads/" . $uniqueName]);
    } catch (Exception $e) {
        echo json_encode(["message" => "Error uploading file: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["message" => "Wrong method"]);
}
?>