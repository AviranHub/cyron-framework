<?php

namespace App\Http\Controllers;

use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Models\User;
use Cyron\Authentication\Auth;
use Cyron\Http\Controller;
use Cyron\Http\Request;
use Cyron\Http\Response;
use Cyron\Analytics\ActivityTracker;

class SupportController extends Controller
{
    public function messages()
    {
        if (!Auth::check()) return Response::unauthorized('برای مشاهده گفتگو وارد شوید.');
        $conversation = $this->userConversation();
        $messages = $conversation
            ? ChatMessage::where('conversation_id', $conversation->id)->orderBy('created_at')->limit(100)->get()
            : [];

        return Response::success([
            'conversation_id' => $conversation->id ?? null,
            'status' => $conversation->status ?? 'open',
            'messages' => $this->serializeMessages($messages),
        ]);
    }

    public function send(Request $request)
    {
        if (!Auth::check()) return Response::unauthorized('برای ارسال پیام وارد شوید.');
        $body = trim((string) $request->input('body'));
        if ($body === '' && !$request->hasFile('attachment')) {
            return Response::validationError(['body' => ['پیام یا فایل را وارد کنید.']]);
        }
        $errors = $request->validate(['body' => 'nullable|string|max:2000']);
        if ($errors && $errors->any()) return Response::validationError($errors->fieldErrors());

        $user = Auth::user();
        if (!is_object($user)) return Response::unauthorized('حساب کاربری پیدا نشد.');
        $conversation = $this->userConversation();
        if (!$conversation) {
            $conversation = ChatConversation::create([
                'user_id' => $user->id,
                'status' => 'open',
                'subject' => 'پشتیبانی سایت',
                'last_message_at' => date('Y-m-d H:i:s'),
            ]);
        } elseif (($conversation->status ?? 'open') === 'closed') {
            $conversation->update(['status' => 'open']);
        }

        $attachment = $this->storeAttachment($request);
        $message = ChatMessage::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $user->id,
            'sender_type' => 'user',
            'body' => $body,
            'attachment_path' => $attachment['path'] ?? null,
            'attachment_name' => $attachment['name'] ?? null,
            'attachment_mime' => $attachment['mime'] ?? null,
        ]);
        $conversation->update(['last_message_at' => date('Y-m-d H:i:s'), 'status' => 'open']);
        ActivityTracker::record('support.message_sent', ['subject_type' => ChatConversation::class, 'subject_id' => (int) $conversation->id], (int) $user->id);

        return Response::success($this->serializeMessages([$message]), 'پیام شما ثبت شد.');
    }

    public function attachment($filename)
    {
        if (!Auth::check()) return Response::unauthorized('برای مشاهده فایل وارد شوید.');
        $safeName = basename((string) $filename);
        $path = STORAGE_PATH . '/public/support/' . $safeName;
        if ($safeName === '' || !is_file($path)) abort(404);

        return Response::file($path, [
            'Cache-Control' => 'private, max-age=300',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function inbox()
    {
        if ($response = $this->requireAdmin()) return $response;
        $conversations = ChatConversation::query()->orderBy('last_message_at', 'desc')->limit(100)->get();
        $users = [];
        $conversationRows = [];
        foreach ($conversations as $conversation) {
            $user = User::find((int) $conversation->user_id);
            $users[(int) $conversation->user_id] = $user;
            $conversationRows[] = [
                'conversation' => $conversation,
                'user_name' => $user ? (string) ($user->name ?: $user->email) : 'کاربر ناشناس',
                'user_email' => $user ? (string) ($user->email ?? '') : 'شناسه کاربر: ' . (int) $conversation->user_id,
            ];
        }
        return view('admin.support.index', compact('conversationRows'));
    }

    public function show($id)
    {
        if ($response = $this->requireAdmin()) return $response;
        $conversation = ChatConversation::find($id);
        if (!$conversation) abort(404);
        $user = User::find($conversation->user_id);
        $messages = ChatMessage::where('conversation_id', $conversation->id)->orderBy('created_at')->limit(200)->get();
        return view('admin.support.show', compact('conversation', 'user', 'messages'));
    }

    public function reply(Request $request, $id)
    {
        if ($response = $this->requireAdmin()) return $response;
        $conversation = ChatConversation::find($id);
        if (!$conversation) abort(404);
        $body = trim((string) $request->input('body'));
        if ($body === '' && !$request->hasFile('attachment')) {
            return redirect()->back()->with('error', 'متن یا فایل پاسخ را وارد کنید.');
        }
        $errors = $request->validate(['body' => 'nullable|string|max:2000']);
        if ($errors && $errors->any()) return redirect()->back()->withErrors($errors)->withInput();

        $attachment = $this->storeAttachment($request);
        ChatMessage::create([
            'conversation_id' => $conversation->id,
            'sender_id' => Auth::id(),
            'sender_type' => 'admin',
            'body' => $body,
            'attachment_path' => $attachment['path'] ?? null,
            'attachment_name' => $attachment['name'] ?? null,
            'attachment_mime' => $attachment['mime'] ?? null,
        ]);
        $conversation->update(['last_message_at' => date('Y-m-d H:i:s'), 'status' => 'open']);
        ActivityTracker::record('support.reply_sent', ['subject_type' => ChatConversation::class, 'subject_id' => (int) $conversation->id], Auth::id());
        return redirect()->back()->with('success', 'پاسخ ارسال شد.');
    }

    public function close($id)
    {
        if ($response = $this->requireAdmin()) return $response;
        $conversation = ChatConversation::find($id);
        if (!$conversation) abort(404);
        $conversation->update(['status' => 'closed']);
        return redirect()->back()->with('success', 'گفتگو بسته شد.');
    }

    private function userConversation()
    {
        $conversation = ChatConversation::where('user_id', Auth::id())
            ->where('status', 'open')
            ->orderBy('last_message_at', 'desc')
            ->first();
        return $conversation;
    }

    private function requireAdmin()
    {
        $user = Auth::user();
        if (!$user) return redirect()->route('login');
        if (!in_array(strtolower((string) ($user->role ?? '')), ['admin', 'superadmin'], true)) {
            return redirect()->route('home');
        }
        return null;
    }

    private function serializeMessages($messages): array
    {
        $result = [];
        foreach ($messages as $message) {
            $result[] = [
                'id' => (int) $message->id,
                'body' => (string) $message->body,
                'sender_type' => (string) $message->sender_type,
                'created_at' => (string) $message->created_at,
                'attachment' => $message->attachment_path ? [
                    'url' => route('support.attachments.show', ['filename' => basename($message->attachment_path)]),
                    'name' => (string) $message->attachment_name,
                    'mime' => (string) $message->attachment_mime,
                ] : null,
            ];
        }
        return $result;
    }

    private function storeAttachment(Request $request): ?array
    {
        if (!$request->hasFile('attachment')) return null;
        $file = $request->fileData('attachment');
        if (!$file || ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            return null;
        }
        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
        $allowed = ['image/jpeg', 'image/png', 'image/webp', 'application/pdf'];
        if (!in_array($mime, $allowed, true) || (int) ($file['size'] ?? 0) > 10 * 1024 * 1024) {
            return null;
        }
        $path = storage()->upload($file, 'support');
        return ['path' => $path, 'name' => basename((string) ($file['name'] ?? 'attachment')), 'mime' => $mime];
    }
}