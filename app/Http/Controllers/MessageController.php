<?php

namespace App\Http\Controllers;

use App\Jobs\SendEmailJob;
use App\Models\Messages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class MessageController extends Controller
{
    public function blastEmail(Request $request)
    {
        $requestData = $request->json()->all();
        $messages = [];
        $success = true;
        $failedEmails = [];

        foreach ($requestData as $data) {
            $messageText = $data['message'];
            $priority = $data['priority'];
            $emails = $data['email'];

            foreach ($emails as $email) {
                $message = new Messages();
                $message->message = $messageText;
                $message->email = $email;
                $message->priority = $priority;
                $message->save();
                // dd(Mail::to($email)->send(new BlastEmail($message)));
                // Send email
                try {
                    dispatch(new SendEmailJob($message->id));
                } catch (\Exception $e) {
                    $success = false;
                    $failedEmails[] = $email;
                }

                $messages[] = $message;
            }
        }

        if (!$success) {
            return response()->json(
                [
                    'message' => 'Some emails failed to send.',
                    'success' => $success,
                    'failed_emails' => $failedEmails,
                    'data' => [],
                ],
                404,
            );
        }

        return response()->json([
            'message' => 'Emails sent successfully.',
            'success' => $success,
            'data' => $messages,
        ]);
    }
}
