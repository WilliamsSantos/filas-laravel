
@extends('layouts.app')
@section('content')
<form method="post" action="{{ route('documents.upload') }}" enctype="multipart/form-data">
    @csrf

<div class="file-upload">
  <button class="file-upload-btn" type="button" onclick="$('.file-upload-input').trigger( 'click' )">Add Image</button>

  <div class="image-upload-wrap">
    <input class="file-upload-input" name="file" type='file' onchange="readURL(this);" accept="json/*" />
    <div class="drag-text">
      <h3>Drag and drop a file or select add Image</h3>
    </div>
  </div>
  <div class="file-upload-content">
    <img class="file-upload-image" src="#" alt="" />
    <div class="image-title-wrap">
      <button type="button" onclick="removeUpload()" class="remove-image">Remove <span class="image-title">Uploaded Image</span></button>
    </div>
    <button type="submit" class="btn btn-info" id="btn-submit">ENVIAR</button>
  </div>
</div>

</form>
@stop
