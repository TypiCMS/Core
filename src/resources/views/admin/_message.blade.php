@if ($message = Session::get('status') or $message = $errors->first())
    <div class="alert alert-info @if ($closable)alert-dismissable @endif">
        @if ($closable)
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        @endif
        <p>{{ $message }}</p>
    </div>
@endif
