<div id="carouselExampleIndicators" class="img-zoom-container" data-ride="carousel">

  <img id="myimage" src="{{ asset('uploads/') }}/{{ $images[0]->url }}" style="width: 100%;">
  <div id="myresult" class="img-zoom-result" style="opacity: 0;"></div>

  <div class="xzoom-thumbs" style="text-align: left;">
    <br>
    @foreach($images as $image)
      <a id="{{ asset('uploads/') }}/{{ $image->url }}" class="other_images">
        <img class="xzoom-gallery" width="80" src="{{ asset('uploads/') }}/{{ $image->url }}" xpreview="{{ asset('uploads/') }}/{{ $image->url }}">
      </a>
    @endforeach
  </div>
</div>