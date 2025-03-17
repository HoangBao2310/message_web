<div class="pt-2 ms-2 d-flex align-items-center">
    <span
        class="me-2 bg-coins-refund rounded-circle d-inline-flex align-items-center justify-content-center text-white"
        style="width: 40px; height: 40px;">
        {!! $svg !!}
    </span>
    <p class="m-0 fw-bold">
        {{ $title }}
        @if (isset($span))
            <span class="bg-coins-refund text-white fw-semibold badge">{{ $span }}</span>
        @endif
    </p>
</div>