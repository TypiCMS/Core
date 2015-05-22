@if (isset($status) or $status = $errors->first())
    <div class="alert alert-info @if ($closable)alert-dismissable @endif">
        @if ($closable)
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        @endif
        <p>{{ $status }}</p>
    </div>
@endif
