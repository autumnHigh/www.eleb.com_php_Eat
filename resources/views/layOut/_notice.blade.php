

@foreach(['success','warning','info','danger'] as $not)
@if(session()->has($not))
<div class="alert alert-{{$not}} alert-dismissible" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <strong>{{session()->get($not)}}</strong>
</div>
@endif
@endforeach