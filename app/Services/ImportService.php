<?php

namespace App\Services;

use App\Models\Document;
use App\Utils\FileManager;
use App\Models\ImportQueue;
use App\Models\Category;
use App\Jobs\StoreFileData;
use App\Utils\ResponseMessages;
use \Carbon\Carbon;
use Illuminate\Contracts\Bus\Dispatcher;
use Exception;
use Symfony\Component\HttpFoundation\Response;

class ImportService
{
    public function __construct(
        private Document $document, 
        private FileManager $fileManager, 
        private ImportQueue $importQueue, 
        private Category $category,
        private ResponseMessages $responseMessage,
        private Dispatcher $dispatcher, 
        private Carbon $carbon,
        private StoreFileData $storeFileData
    ) 
    {
        $this->documentModel = $document;
        $this->fileManager = $fileManager;
        $this->importQueue = $importQueue;
        $this->categoryModel = $category;
        $this->responseMessages = $responseMessage;
        $this->carbon = $carbon;
    }

    private function formatDataToBatchInsert($slug, $filename, $documents = [])
    {
        $exercice = $documents['exercicio'];
        return array_reduce($documents['documentos'], 
            function ($result, $document) use ($filename, $exercice, $slug) 
            {
                $isDuplicatedRegister = $this->documentModel
                    ->where('exercice_year', $exercice)
                    ->whereTitle($document["titulo"])
                    ->exists();
            
                if ($isDuplicatedRegister) return $result;

                $categoryId = $this->categoryModel
                    ->whereName($document['categoria'])
                    ->pluck('id')
                    ->first();

                if ($categoryId) {
                    $result[] = [
                        'content' => json_encode(
                            [
                                'category_id' => $categoryId,
                                'title' => $document["titulo"],
                                'content' => $document["conteúdo"],
                                'exercice_year' => $exercice
                            ]
                        ),
                        'slug' => $slug,
                        'filename' => $filename,
                        'created_at' => $this->carbon->now(),
                    ];
                    return $result;
                }

                throw new Exception(
                    $this->responseMessage::CATEGORY_NOT_FOUND,
                    Response::HTTP_BAD_REQUEST
                );
            }, []);
    }

    public function storeFile(array $document): string
    {
        $fileContent = $document['data'] ?: null;
        $filename = $document['filename'] ?: null;
        $fileUploaded = $document['file'] ?: null;
        $slug = $document['slug'] ?: null;

        $fileToBatchInsert = $this->formatDataToBatchInsert(
            $slug,
            $filename,
            $fileContent,
        );

        $arrayChunk = array_chunk($fileToBatchInsert ?: [], 500);
        $storedDocuments = array_map(function ($batch) {
            $this->importQueue->insert($batch);
        }, $arrayChunk);

        if (empty($storedDocuments))
            throw new Exception(
                $this->responseMessage::NOT_FOUND_OR_DUPLICATED_DATA, 
                Response::HTTP_INTERNAL_SERVER_ERROR
            );

        $savedFile = $this->fileManager
            ->save($fileUploaded, $filename);
        
        if ($savedFile) return $filename;

        throw new Exception(
           $this->responseMessage::STORE_FILE_FAIL, 
           Response::HTTP_INTERNAL_SERVER_ERROR
        );
    }

    private function dispatchRegisterToJob($register)
    {
        $this->storeFileData->dispatch($register)
            ->delay(
                now()->addMinutes(
                    config('configurations.queue_dispatch_delay_minutes')
                )
            );
    }

    public function processFile($filename)
    {
        if ($this->importQueue->where('status', 'pending')->exists())
        {
            $bathSize = config('configurations.bath_size');
            $columnsToQueueImport = ['id', 'content', 'status'];

            $filesPending = $filesProcessed = 0;
            do {
                $registers = $this->importQueue
                    ->whereFilename($filename)
                    ->whereStatus('pending')
                    ->take($bathSize)
                    ->get($columnsToQueueImport)
                    ->toArray();

                $sendsToQueye = 
                    array_reduce($registers, function($acc, $item) {
                        $register = json_decode($item['content'], true);

                        $this->dispatchRegisterToJob([
                            ...$register, 
                            'id' => $item['id']
                        ]);
          
                        $acc['sends']++;
                        
                        return $acc; 
                    }, ['sends' => 0 ]);

                $filesPending = $filesPending;
                $filesProcessed += count($registers);

            } while ($filesPending > 0);

            if ($filesProcessed)
                return [ 'processed' => $filesProcessed ];

            throw new Exception(
                $this->responseMessage::PROCESS_QUEUE_FAIL, 
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
