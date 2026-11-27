@if ($workOrder->status === 'completed')
    <section class="card mt-0">
        {{-- <div class="card-header fw-semibold">Product Bundle Stickers</div> --}}
        <div class="card-body">
            <iframe
                title="Bundle stickers for work order {{ $workOrder->id }}"
                src="{{ route('productions.bundle-stickers', ['workOrder' => $workOrder->id, 'embedded' => 1]) }}"
                loading="lazy"
                style="display: block; width: 100%; height: 85vh; min-height: 640px; border: 0;"
            ></iframe>
        </div>
    </section>
@endif
