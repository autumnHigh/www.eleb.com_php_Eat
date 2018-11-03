@include('layOut._header')


<div class="container">
    @include('layOut._navi')
</div>

<div class="container">
    @include('layOut._notice')
    @yield('contents')


    @yield('javascript')
</div>

@include('layOut._footer')