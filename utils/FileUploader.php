<?php

namespace file_upload;

class FileUploader {

    private $file;

    public function upload_img($file, $target_dir, $filename = null) {
        //********FILE UPLOAD**********
        $this->file = $file;
        $uploadOk = true;

//        $target_dir = "uploads/profile_pictures";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }


        $orig_file_name = basename($this->file["name"]);
        $imageFileType = strtolower(pathinfo($orig_file_name, PATHINFO_EXTENSION));

        if ($filename == null) {
            $target_file = $target_dir . basename($this->file["name"]);
        } else {
            $target_file = $target_dir . $filename . "." . $imageFileType;
        }

        // Check if image file is a actual image or fake image

        $check = getimagesize($this->file["tmp_name"]);
        if ($check !== false) {
            echo "File is an image - " . $check["mime"] . ". ";
            $uploadOk = true;
        } else {
            echo "File is not an image. ";
            $uploadOk = false;
        }

        if ($this->existence($target_file) == false) $uploadOk = false;
        if ($this->limit_size() == false) $uploadOk = false;
        if ($this->check_format($imageFileType) == false) $uploadOk = false;

        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == false) {
            echo "Sorry, your file was not uploaded. ";

            // if everything is ok, try to upload file
        } else {
            if (move_uploaded_file($file["tmp_name"], $target_file)) {
                echo "The file " . basename($this->file["name"]) . " has been uploaded. ";
            } else {
                echo "Sorry, there was an error uploading your file. ";
            }
        }

    }

    private function existence($target_file) {
        // Check if file already exists
        if (file_exists($target_file)) {
            echo "Sorry, file already exists. ";
            return false;
        } else {
            return true;
        }
    }

    private function limit_size() {
        // Check file size
        if ($this->file["size"] > 500000) {
            echo "Sorry, your file is too large. ";
            return false;
        } else {
            return true;
        }
    }

    private function check_format($imageFileType) {
        // Allow certain file formats
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
            && $imageFileType != "gif") {
            echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed. ";
            return false;
        } else {
            return true;
        }
    }

}