<?php

namespace App\Jobs;

use App\Models\ImportQueue;
use App\Models\Document;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class StoreFileData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public function __construct(
        private ImportQueue $importQueue, 
        private Document $document, 
        private $register = []
    ){}

    public function handle(): void
    {
        $createdDocument = $this->document->create($this->register);

        $update = [
            'status' => $createdDocument ? 'processed' : 'failed',
            'processed_at' => now()
        ];

        $this->importQueue
            ->find($this->register['id'])
            ->update($update);
    }
}
