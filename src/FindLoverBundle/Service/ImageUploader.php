<?php
/**
 * Created by PhpStorm.
 * User: zotakk
 * Date: 11/18/17
 * Time: 11:02 PM
 */

namespace FindLoverBundle\Service;


use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageUploader {

	private $targetDirectory;

	public function __construct($targetDirectory) {
		$this->setTargetDirectory($targetDirectory);
		//TODO: CREATE FOLDER FOR EACH USER
	}

	public function upload(UploadedFile $file)
	{
		$fileName = md5(uniqid()).'.'.$file->guessExtension();

		$file->move($this->getTargetDirectory(), $fileName);

		return $fileName;
	}

	public function getTargetDirectory()
	{
		return $this->targetDirectory;
	}

	public function setTargetDirectory($targetDirectory) {
		$this->targetDirectory = $targetDirectory;
	}

}