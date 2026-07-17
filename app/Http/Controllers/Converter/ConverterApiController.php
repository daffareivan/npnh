<?php

declare(strict_types=1);

namespace App\Http\Controllers\Converter;

use App\DTO\ConverterUploadData;
use App\Http\Controllers\Controller;
use App\Http\Requests\ConverterProcessRequest;
use App\Http\Requests\ConverterUploadRequest;
use App\Http\Resources\AudioFileResource;
use App\Models\AudioFile;
use App\Repositories\AudioFileRepository;
use App\Services\ConverterService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class ConverterApiController extends Controller
{
    public function __construct(
        private readonly ConverterService $converter,
        private readonly AudioFileRepository $audioFiles,
    ) {}

    public function upload(ConverterUploadRequest $request): AudioFileResource
    {
        $this->authorize('create', AudioFile::class);

        $audioFile = $this->converter->upload(new ConverterUploadData(
            file: $request->file('file'),
            presetId: (int) $request->integer('preset_id'),
            userId: $request->user()?->id,
        ));

        return AudioFileResource::make($audioFile);
    }

    public function process(ConverterProcessRequest $request): AudioFileResource
    {
        $audioFile = AudioFile::query()->findOrFail($request->integer('audio_file_id'));
        $this->authorize('view', $audioFile);

        return AudioFileResource::make($this->converter->dispatch($audioFile));
    }

    public function status(AudioFile $audioFile): AudioFileResource
    {
        $this->authorize('view', $audioFile);

        return AudioFileResource::make($audioFile->refresh());
    }

    public function download(AudioFile $audioFile, Request $request): Response|\Symfony\Component\HttpFoundation\StreamedResponse
    {
        $this->authorize('download', $audioFile);

        return $this->converter->download($audioFile, $request);
    }

    public function history(Request $request): AnonymousResourceCollection
    {
        return AudioFileResource::collection($this->audioFiles->paginate($request->user()?->id));
    }

    public function destroy(AudioFile $audioFile): Response
    {
        $this->authorize('delete', $audioFile);
        $this->converter->delete($audioFile);

        return response()->noContent();
    }
}
