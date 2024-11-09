<?php

namespace Inovector\Mixpost\Support;

use Illuminate\Support\Str;

class SystemLogs
{
    public function logs(): array
    {
        $files = $this->getFilePaths();

        $logs = [];

        foreach ($files as $file) {
            $filename = basename($file);

            $size = filesize($file);

            $error = '';

            if ($size >= 3145728) {
                $suffix = [
                    'B',
                    'KB',
                    'MB',
                    'GB',
                    'TB',
                    'PB',
                    'EB',
                    'ZB',
                    'YB'
                ];

                $i = 0;

                while (($size / 1024) > 1) {
                    $size = $size / 1024;
                    $i++;
                }

                $error = sprintf('Warning: Error log file %s is %s!', $filename, round(substr($size, 0, strpos($size, '.') + 4), 2) . $suffix[$i]);
            }

            $handle = fopen($file, 'r+');

            $logs[] = [
                'name' => $filename,
                'contents' => fread($handle, 3145728),
                'error' => $error
            ];

            fclose($handle);
        }

        return $logs;
    }

    public function basePathForLogs(): string
    {
        return Str::finish(realpath(storage_path('logs')), DIRECTORY_SEPARATOR);
    }

    public function getFilePath(string $name): string
    {
        return $this->basePathForLogs() . $name;
    }

    protected function getFilePaths(): bool|array
    {
        $files = glob($this->basePathForLogs() . '*.log');

        $files = array_map('realpath', $files);

        return array_filter($files, 'is_file');
    }
}
