<?php

namespace App\Http\Requests;

use App\Utils\FileManager;
use App\Utils\ResponseMessages;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class RunQueueRequest extends FormRequest
{
    public function __construct(
        private FileManager $fileManager, 
        private ResponseMessages $responseMessage
    ) {}

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file' => 'required'
        ];
    }

    public function identifierFile(): string
    {
        if (!$this->has('file'))
            throw new Exception(
                $this->responseMessage::UPLOAD_FILE_NOT_FOUND, 
                Response::HTTP_BAD_REQUEST
            );

        $fileId = $this->get('file');

        if ($this->fileManager->exists($fileId))
            return $fileId;

        throw new Exception(
            $this->responseMessage::UPLOAD_FILE_NOT_FOUND_TO_PROCESS,
            Response::HTTP_BAD_REQUEST
        );
    }
}
