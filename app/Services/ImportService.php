<?php

namespace App\Services;

use App\Models\Document;
use App\Jobs\storeFileData;
use App\Utils\FileManager;
use App\Models\ImportQueue;
use App\Models\Category;
use \Carbon\Carbon;
use Symfony\Component\HttpFoundation\Response;

use Exception;

class ImportService {

    private $documentModel;
    private $categoryModel;
    private $fileManager;
    private $importQueue;

    public function __construct() 
    {
        $this->documentModel = new Document;
        $this->fileManager = new FileManager;
        $this->importQueue = new ImportQueue;
        $this->categoryModel = new Category;
    }

    private function formatDataToBatchInsert($filename, $documents = [])
    {
        return array_map(function($document) use ($filename) {
            $categoryId = $this->categoryModel->whereName($document['categoria'])
                ->pluck('id')
                ->first();

            if ($categoryId)
                return [
                    'content' => json_encode(
                        [
                            'category_id' => $categoryId,
                            'title' => $document["titulo"],
                            'content' => $document["conteúdo"],
                        ]
                    ),
                    'filename' => $filename, 
                    'created_at' => Carbon::now(),
                ];

            throw new Exception(
                "Arquivo com categoria incorreta", 
                Response::HTTP_BAD_REQUEST
            );
        }, $documents);
    }

    public function storeFile(array $document): string
    {
        $fileContent = $document['data'] ?: null;
        $filename = $document['filename'] ?: null;
        $documents = $fileContent['documentos'] ?: null;
        $fileUploaded = $document['file'] ?: null;

        $fileToBatchInsert = $this->formatDataToBatchInsert(
            $filename,
            $documents,
        );

        $arrayChunk = array_chunk($fileToBatchInsert, 500);
        $storedDocuments = array_map(function ($batch) {
            $this->importQueue->insert($batch);
        }, $arrayChunk);

        if (empty($storedDocuments))
            throw new Exception(
                "Falha ao salvar os dados do arquivo.", 
                Response::HTTP_BAD_REQUEST
            );

        $savedFile = $this->fileManager
            ->save($fileUploaded, $filename);
        
        if ($savedFile) return $filename;

        throw new Exception(
            "Falha ao salvar arquivo", Response::HTTP_BAD_REQUEST
        );
    }

    public function processFile($filename)
    {
        if ($this->importQueue->where('status', 'pending')->exists()){
           
            $columnsToQueueImport = ['id', 'content', 'status'];
            $bathSize = 100;
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
                        dispatch(
                            new storeFileData([
                                ...$register, 
                                'id' => $item['id']
                            ])
                        )->delay(now()->addSeconds(1000));
                        $acc['sends']++;
                        return $acc; 
                    }, ['sends' => 0 ]);

                $filesPending = $filesPending;
                $filesProcessed += count($registers);

            } while ($filesPending > 0);

            return $filesProcessed ?: throw new Exception(
                "Falha ao salvar arquivo", Response::HTTP_BAD_REQUEST
            );
        }
    }
}
