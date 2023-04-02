<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Utils\FileManager;
use App\Utils\ResponseMessages;
use Exception;
use Symfony\Component\HttpFoundation\Response;

class FileUploadRequest extends FormRequest
{
    private $maxFileSize;

    public function __construct(
        private FileManager $fileManager, 
        private ResponseMessages $responseMessage
    ) {
        $this->maxFileSize = config('configurations.max_file_size');
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file' => "required|file|max:$this->maxFileSize|mimes:json"
        ];
    }

    public function messages()
    {
        return [
            'file.required' => 'Por favor selecione um arquivo para fazer o upload.',
            'file.file' => 'O arquivo selecionado não é válido.',
            'file.mimes' => 'Formato de arquivo invalido. Só são permitidos arquivos .json',
            'file.max' => 'O arquivo selecionado é muito grande. O tamanho máximo permitido é 10MB.'
        ];
    }

    public function uploadFile(): array
    {
        if (!$this->hasFile('file'))
            throw new Exception(
                $this->responseMessage::UPLOAD_FILE_NOT_FOUND, 
                Response::HTTP_NOT_FOUND
            );

        $uploadFile = $this->file('file');
        if ($formatted = $this->inCorrectFormat($uploadFile)){
            $hash = hash_file('sha256', $uploadFile->getPathname());

            if ($this->fileManager->exists($hash))
                throw new Exception(
                    $this->responseMessage::PREVIOUSLY_IMPORTED_FILE, 
                    Response::HTTP_CONFLICT
                );

            return [ 
                'slug' => $uploadFile->getClientOriginalName(), 
                'filename' => $hash, 
                'data' => $formatted, 
                'file' => $uploadFile 
            ];
        }

        throw new Exception(
            $this->responseMessage::WRONG_FORMAT_FILE, 
            Response::HTTP_CONFLICT
        );
    }

    private function inCorrectFormat($fileContent)
    {
        $isCorrectFormat = true;
        if (!$fileContent || empty($fileContent)) {
            throw new Exception(
                $this->responseMessage::EMPTY_FILE, 
                Response::HTTP_BAD_REQUEST
            );
        }
    
        $fileContentArray = (array) json_decode($fileContent->get(), true);
        $requiredProperties = ['exercicio', 'documentos'];
        $requiredDocumentProperties = ['categoria', 'conteúdo', 'titulo'];
    
        $keysOfFileContent = array_keys($fileContentArray);
        if (array_intersect($requiredProperties, $keysOfFileContent) !== $requiredProperties) 
        {
            throw new Exception(
                $this->responseMessage::NON_STANDARD_FILE_FORMATTING, 
                Response::HTTP_BAD_REQUEST
            ); 
        }
    
        if (empty($fileContentArray['documentos']))
        {
            throw new Exception(
                $this->responseMessage::FILE_DOCUMENT_IMPORT_EMPTY, 
                Response::HTTP_BAD_REQUEST
            ); 
        }

        foreach ($fileContentArray['documentos'] as $document) 
        {
            $documentKeys = array_keys($document);
            if (array_intersect($requiredDocumentProperties, $documentKeys) !== $requiredDocumentProperties) 
            {
                throw new Exception(
                    $this->responseMessage::MISSING_DOCUMENTS_PROPERTIES, 
                    Response::HTTP_BAD_REQUEST
                ); 
            }
        }

        return $fileContentArray;
    }
}
