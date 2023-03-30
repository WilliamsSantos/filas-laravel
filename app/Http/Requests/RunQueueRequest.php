<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Utils\FileManager;

class RunQueueRequest extends FormRequest
{
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
            'file' => 'required'
        ];
    }

    public function identifierFile(): string
    {
        if (!$this->has('file'))
            throw new Exception("Nenhum arquivo foi enviado", Response::HTTP_BAD_REQUEST);

        $fileId = $this->get('file');

        if ($this->fileManager->exists($fileId))
            return $fileId;

        throw new Exception("Arquivo não encontrado.", Response::HTTP_BAD_REQUEST);
    }
}
