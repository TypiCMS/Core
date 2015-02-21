@if (Session::has('message'))
    <div class="alert alert-info @if ($closable)alert-dismissable @endif">
        @if ($closable)
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        @endif
        <p>{{ Session::get('message') }}</p>
    </div>
@endif
