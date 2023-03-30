<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\ImportQueue;
use App\Models\Document;

use \Carbon\Carbon;
use DB;

class storeFileData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $importQueue;
    private $register;

    /**
     * Create a new job instance.
     */
    public function __construct(array $register)
    {
        $this->register = $register;
        $this->document = new Document;
        $this->importQueue = new ImportQueue;
    }

    public function handle()
    {
        Log::info("Tarefa iniciada!");
        
        var_dump('>>>>', $this->register);
        $this->document->create($this->register);

        $this->importQueue->whereId($this->register['id'])
            ->update(['status' => 'processed']);

        Log::info("item processado!");
    }
}
