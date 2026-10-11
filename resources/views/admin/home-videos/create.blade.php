@extends('admin.admin_dashboard')
@section('title', 'Add Home Video')
@section('admin')

<div class="container-fluid px-4">

    <h1 class="mt-4">Add Home Page Video</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-body">

            <form action="{{ route('admin.home-videos.store') }}"
                  method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control"
                           value="{{ old('title') }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" rows="3"
                              class="form-control">{{ old('description') }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Source <span class="text-danger">*</span></label>
                    <select name="source_type" id="sourceType"
                            class="form-select" required>
                        <option value="youtube" @selected(old('source_type') === 'youtube')>
                            YouTube URL
                        </option>
                        <option value="file" @selected(old('source_type') === 'file')>
                            Upload File
                        </option>
                    </select>
                </div>

                {{-- YOUTUBE --}}
                <div class="mb-3" id="youtubeBlock">
                    <label class="form-label">YouTube URL <span class="text-danger">*</span></label>
                    <input type="text" name="youtube_url" class="form-control"
                           value="{{ old('youtube_url') }}"
                           placeholder="https://www.youtube.com/watch?v=XXXXXXXXXXX">
                    <small class="text-muted">
                        Paste the full YouTube link. Short URLs (youtu.be/...) also work.
                    </small>
                </div>

                {{-- FILE --}}
                <div class="mb-3" id="fileBlock">
                    <label class="form-label">Video File <span class="text-danger">*</span></label>
                    <input type="file" name="video_file" class="form-control"
                           accept="video/mp4,video/webm,video/quicktime,video/ogg">
                    <small class="text-muted">
                        MP4, WEBM, MOV or OGG. Max 100 MB.
                    </small>

                    <div class="mt-3">
                        <label class="form-label">Thumbnail (optional)</label>
                        <input type="file" name="thumbnail" class="form-control"
                               accept="image/jpeg,image/png,image/webp">
                        <small class="text-muted">
                            Shown as the video preview card. JPG, PNG or WEBP.
                        </small>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Video
                </button>

                <a href="{{ route('admin.home-videos.index') }}"
                   class="btn btn-secondary">Cancel</a>
            </form>

        </div>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const sourceType   = document.getElementById('sourceType');
    const youtubeBlock = document.getElementById('youtubeBlock');
    const fileBlock    = document.getElementById('fileBlock');

    function toggle() {
        if (sourceType.value === 'youtube') {
            youtubeBlock.style.display = 'block';
            fileBlock.style.display    = 'none';
        } else {
            youtubeBlock.style.display = 'none';
            fileBlock.style.display    = 'block';
        }
    }

    sourceType.addEventListener('change', toggle);
    toggle();
});
</script>

@endsection
