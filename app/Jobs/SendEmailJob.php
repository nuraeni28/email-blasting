<?php

namespace App\Jobs;

use App\Mail\BlastEmail;
use App\Models\Messages;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $message;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($message)
    {
        $this->message = $message;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info('Processing queue job: ' . $this->message);

        // sent message priority high
        $highPriorityMessages = Messages::where('priority', 'high')->whereNull('status')->orderBy('created_at', 'asc')->get();
        foreach ($highPriorityMessages as $message) {
            try {
                foreach ($message->email as $email) {
                    Mail::to($email)->send(new BlastEmail($message));
                }
                $this->updateMessageStatus($message);
                Log::info('High priority message sent: ' . $message->id);
            } catch (\Exception $e) {
                Log::error('Failed to send high priority message: ' . $message->id . '. Error: ' . $e->getMessage());
            }
        }

        // Send low priority messages
        $lowPriorityMessages = Messages::where('priority', 'low')->whereNull('status')->orderBy('created_at', 'asc')->get();

        foreach ($lowPriorityMessages as $message) {
            try {
                foreach ($message->email as $email) {
                    Mail::to($email)->send(new BlastEmail($message));
                }
                $this->updateMessageStatus($message);
                Log::info('Low priority message sent: ' . $message->id);
            } catch (\Exception $e) {
                Log::error('Failed to send low priority message: ' . $message->id . '. Error: ' . $e->getMessage());
            }
        }
    }

    private function updateMessageStatus($message)
    {
        // Perbarui status pesan menjadi "done"
        $message->status = 'done';
        $message->save();
    }
}
