<?php

namespace App\Http\Controllers;

use App\Services\ImportService;
use App\Utils\ResponseMessages;
use App\Http\Requests\FileUploadRequest;
use App\Http\Requests\ProcessFileRequest;
use App\Http\Requests\RunQueueRequest;
use App\Http\Responses\CustomResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DocumentsController extends Controller
{
    private $defaultRoute;

    public function __construct(
        private ImportService $importService, 
        private CustomResponse $customResponse, 
        private ResponseMessages $responseMessage
    ) {
        $this->defaultRoute = 'index';
        $this->importService = $importService;
        $this->redirectWithCustomMessage = $customResponse;
        $this->responseMessage = $responseMessage;
    }

    public function index(): View
    {
        return view('imports.form');
    }

    public function processFile(ProcessFileRequest $request): View
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

    public function upload(FileUploadRequest $request): RedirectResponse
    {
        try {
            $file = $request->uploadFile();

            $queueCreated = $this->importService->storeFile($file);

            session()->flash(
                'success-info', 
                $this->responseMessage::UPLOADED_FILE
            );

            return redirect()->route('process', [
                'file' => $queueCreated, 
                'slug' => $file['slug'] 
            ]);
        } catch (\Exception $e) {
            return $this->redirectWithCustomMessage
                ->errorRoute($e->getMessage());
        }
    }

    public function runQueue(RunQueueRequest $request): RedirectResponse
    {
        try {
            $file = $request->identifierFile();
            $processed = $this->importService->processFile($file);

            return $this->redirectWithCustomMessage
                ->successRoute(
                    $this->defaultRoute,
                    $this->responseMessage::FILE_PROCESSED_SUCCESS,
                    $processed
                );
        } catch (\Exception $e) {
            return $this->redirectWithCustomMessage
                ->errorRoute($e->getMessage());
        }
    }
}