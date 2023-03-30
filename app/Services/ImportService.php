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
                        'created_at' => Carbon::now(),
                    ];
                    return $result;
                }

                throw new Exception(
                    "O Arquivo contem categorias não cadastradas.",
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
                "Nenhum registro salvo. Registros duplicados não são re-processados.", 
                Response::HTTP_INTERNAL_SERVER_ERROR
            );

        $savedFile = $this->fileManager
            ->save($fileUploaded, $filename);
        
        if ($savedFile) return $filename;

        throw new Exception(
            "Falha ao armazenar o arquivo.", Response::HTTP_INTERNAL_SERVER_ERROR
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
                        );
                        
                        $acc['sends']++;
                        
                        return $acc; 
                    }, ['sends' => 0 ]);

                $filesPending = $filesPending;
                $filesProcessed += count($registers);

            } while ($filesPending > 0);

            return $filesProcessed ?: throw new Exception(
                "Falha ao tentar processar o arquivo.", Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
