@extends('layouts.wedding-invite')

@section('content')
    <section class="panel panel-gallery">
        <div class="gallery-card">
            <h2>Share Your Special Moments</h2>
            <p>Upload your favourite photo or video to share with everyone.</p>

            @if (session('success'))
                <p class="upload-message upload-success">{{ session('success') }}</p>
            @endif

            @if ($errors->has('media'))
                <p class="upload-message upload-error">{{ $errors->first('media') }}</p>
            @endif

            <div class="gallery-grid">
                @foreach ($media as $item)
                    @if ($item['type'] === 'video')
                        <video controls preload="metadata" aria-label="Shared wedding video">
                            <source src="{{ $item['url'] }}">
                        </video>
                    @else
                        <img src="{{ $item['url'] }}" alt="Shared wedding photo">
                    @endif
                @endforeach
            </div>

            <form class="upload-form" action="{{ route('invite.gallery.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <label class="upload-btn" for="media-upload">Add Photos &amp; Videos</label>
                <input id="media-upload" name="media" type="file" accept="image/*,video/*" hidden required data-auto-upload>
            </form>
        </div>

    </section>
@endsection
