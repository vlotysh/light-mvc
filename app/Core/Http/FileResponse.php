<?php

namespace LightMVC\Core\Http;

class FileResponse extends Response
{
    protected $file;
    protected $fileName;
    protected $disposition = 'attachment';

    public function __construct(string $filePath, string $fileName = null, int $statusCode = 200, array $headers = [])
    {
        if (!file_exists($filePath)) {
            throw new \RuntimeException("File not found at path: $filePath");
        }

        $this->file = $filePath;
        $this->fileName = $fileName ?? basename($filePath);

        parent::__construct('', $statusCode, $headers);
    }

    public function inline(): self
    {
        $this->disposition = 'inline';
        return $this;
    }

    public function download(): self
    {
        $this->disposition = 'attachment';
        return $this;
    }

    public function send(bool $flush = true): static
    {
        $fileSize = filesize($this->file);
        $mimeType = $this->getMimeType();

        $this->headers = array_merge($this->headers, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => "$this->disposition; filename=\"$this->fileName\"",
            'Content-Length' => $fileSize,
            'Cache-Control' => 'private, no-transform, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);

        // Поддержка загрузки частями (resume download)
        if (isset($_SERVER['HTTP_RANGE'])) {
            $this->handlePartialDownload($fileSize);
        }

        // Отправка заголовков
        http_response_code($this->statusCode);
        foreach ($this->headers as $name => $value) {
            header("$name: $value");
        }

        // Отправка файла
        $handle = fopen($this->file, 'rb');
        if ($handle === false) {
            throw new \RuntimeException("Cannot open file: {$this->file}");
        }

        // Отправка содержимого файла частями для экономии памяти
        while (!feof($handle)) {
            echo fread($handle, 8192);
            flush();
        }

        fclose($handle);
        exit;
    }

    protected function handlePartialDownload($fileSize): void
    {
        $ranges = $this->parseRangeHeader($fileSize);

        if ($ranges === false) {
            header('HTTP/1.1 416 Requested Range Not Satisfiable');
            header("Content-Range: bytes */$fileSize");
            exit;
        }

        $start = $ranges[0];
        $end = $ranges[1];

        $this->statusCode = 206;
        $this->headers['Content-Length'] = $end - $start + 1;
        $this->headers['Content-Range'] = "bytes $start-$end/$fileSize";

        fseek($this->handle, $start);
    }

    protected function parseRangeHeader($fileSize): array|false
    {
        if (!preg_match('/bytes=(\d*)-(\d*)/', $_SERVER['HTTP_RANGE'], $matches)) {
            return false;
        }

        $start = empty($matches[1]) ? 0 : intval($matches[1]);
        $end = empty($matches[2]) ? $fileSize - 1 : intval($matches[2]);

        if ($start >= $fileSize || $end >= $fileSize) {
            return false;
        }

        return [$start, $end];
    }

    protected function getMimeType(): string
    {
        $mimeTypes = [
            'txt' => 'text/plain',
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
        ];

        $extension = strtolower(pathinfo($this->file, PATHINFO_EXTENSION));

        if (function_exists('mime_content_type')) {
            return mime_content_type($this->file);
        }

        return $mimeTypes[$extension] ?? 'application/octet-stream';
    }
}
