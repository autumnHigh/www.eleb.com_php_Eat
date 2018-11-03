@if(count($errors)>0)
<div class="alert alert-warning alert-dismissible" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <strong>Warning!</strong>
{{--
    <li>cuole</li>
    <li>cuole</li>
    <li>cuole</li>--}}

     @foreach($errors->all() as $err)
        <li>{{$err}}</li>
    @endforeach


</div>
@endif