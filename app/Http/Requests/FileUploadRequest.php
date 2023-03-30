<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Utils\FileManager;
use Symfony\Component\HttpFoundation\Response;
use Exception;

class FileUploadRequest extends FormRequest
{
    private $maxFileSize = 10240;
    private $fileManager;

    public function __construct() {
        $this->fileManager = new FileManager;
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
            throw new Exception("Nenhum arquivo foi enviado", Response::HTTP_BAD_REQUEST);

        $uploadFile = $this->file('file');

        if ($formatted = $this->inCorrectFormat($uploadFile)){
            $hash = hash_file('sha256', $uploadFile->getPathname());

            if ($this->fileManager->exists($hash))
                throw new Exception("Arquivo já importado", Response::HTTP_BAD_REQUEST);

            return [ 
                'slug' => $uploadFile->getClientOriginalName(), 
                'filename' => $hash, 
                'data' => $formatted, 
                'file' => $uploadFile 
            ];
        }

        throw new Exception("Arquivo no formato incorreto", Response::HTTP_BAD_REQUEST);
    }

    private function inCorrectFormat($fileContent)
    {
        $exception = new Exception("Arquivo no formato incorreto.", Response::HTTP_BAD_REQUEST);

        $isCorrectFormat = true;
        if (!$fileContent || empty($fileContent)) {
            throw new Exception("Arquivo vazio.", Response::HTTP_BAD_REQUEST);
        }
    
        $fileContentArray = (array) json_decode($fileContent->get(), true);
        $requiredProperties = ['exercicio', 'documentos'];
        $requiredDocumentProperties = ['categoria', 'conteúdo', 'titulo'];
    
        if (
            array_intersect(
                $requiredProperties, array_keys($fileContentArray)
            ) !== $requiredProperties
        ) 
        {
            throw $exception;
        }
    
        if (empty($fileContentArray['documentos']))
        {
            throw $exception;
        }

        foreach ($fileContentArray['documentos'] as $document) 
        {
            if (
                array_intersect(
                    $requiredDocumentProperties, 
                    array_keys($document)
                ) !== $requiredDocumentProperties
            ) 
            {
                throw $exception;
            }
        }

        return $fileContentArray;
    }
}
