<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\FileUploadRequest;
use App\Http\Requests\ProcessFileRequest;
use App\Http\Requests\RunQueueRequest;
use App\Services\ImportService;
use App\Http\Responses\CustomResponse;
use DB;
use Illuminate\Routing\Redirector;

class DocumentsController extends Controller
{
    private $importService;
    private $response;
    private $redirectWithCustomMessage; 

    public function __construct($defaultRoute = 'index') {
        $this->defaultRouter = $defaultRoute;
        $this->importService = new ImportService;
        $this->redirectWithCustomMessage = new CustomResponse;
    }

    public function index()
    {
        return view('imports.form');
    }

    public function processFile(ProcessFileRequest $request)
    {
        try {
            return view('imports.process', [
                'filename' => $request->input('file'),
                'slug' => $request->input('slug', 'arquivo.json')
            ]);
        } catch (\Exception $e) {
            return $this->redirectWithCustomMessage
                ->errorRoute($e->getMessage());
        }
    }

    public function upload(FileUploadRequest $request)
    {
        try {
            $file = $request->uploadFile();
            $queueCreated = $this->importService->storeFile($file);

            session()->flash('success-info', 'Arquivo enviado com sucesso!');

            return redirect()->route('process', [
                'file' => $queueCreated, 
                'slug' => $file['slug'] 
            ]);
        } catch (\Exception $e) {
            return $this->redirectWithCustomMessage
                ->errorRoute($e->getMessage());
        }
    }

    public function runQueue(RunQueueRequest $request)
    {
        try {
            $file = $request->identifierFile();
            $processed = $this->importService->processFile($file);

            return $this->redirectWithCustomMessage
                ->successRoute($this->defaultRouter, 
                    'processamento iniciado, dependendo do tamanho do arquivo isso pode levar alguns minutos',
                    [ 'processed' => $processed ]
                );
        } catch (\Exception $e) {
            return $this->redirectWithCustomMessage
                ->errorRoute($e->getMessage());
        }
    }
}