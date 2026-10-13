<?php

namespace App\Jobs;

use App\Services\ChatAiResponder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateAiChatResponse implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 2;

    public int $backoff = 5;

    public function __construct(
        public readonly int $messageId
    ) {
    }

    public function handle(ChatAiResponder $aiResponder): void
    {
        $aiResponder->respondToMessageId($this->messageId);
    }
}
