<?php

namespace Inovector\Mixpost\Http\Base\Controllers\Admin;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Inertia\Response;
use Inovector\Mixpost\Http\Base\Requests\Admin\ClearSystemLog;
use Inovector\Mixpost\Http\Base\Requests\Admin\DownloadSystemLog;
use Inovector\Mixpost\Support\SystemLogs;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SystemLogsController extends Controller
{
    public function index(): Response
    {
        $systemLogs = new SystemLogs();

        return Inertia::render('Admin/System/Logs', [
            'logs' => $systemLogs->logs()
        ]);
    }

    public function download(DownloadSystemLog $systemLog): StreamedResponse
    {
        $filePath = $systemLog->handle();

        $headers = [
            'Content-Type' => 'text/plain',
            'Content-Disposition' => 'attachment; filename="' . $systemLog->input('filename') . '"',
        ];

        return new StreamedResponse(function () use ($filePath) {
            $handle = fopen($filePath, 'r');
            fpassthru($handle);
            fclose($handle);
        }, 200, $headers);
    }

    public function clear(ClearSystemLog $clearSystemLog): RedirectResponse
    {
        $clearSystemLog->handle();

        return redirect()->back()->with('success', __('mixpost::system.log_file_cleared'));
    }
}
