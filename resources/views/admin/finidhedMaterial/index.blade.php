{{-- resources/views/admin/finidhedMaterial/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Finished Material')

@section('content')
<div class="container-fluid">

    {{-- Header + Filters --}}
    <div class="card mb-3">
        <div class="card-body">
            <div class="mb-3">
                <h4 class="mb-0">Finished Material</h4>
                <div class="text-muted small">Completed production (<span id="fmCount">{{ $listing->total() }}</span> records)</div>
            </div>

            <form id="fmFilters" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small mb-1" for="fmSearch">Search</label>
                    <input type="text" class="form-control" id="fmSearch" name="search" value="{{ request('search') }}"
                        placeholder="WO / SO / customer / product / heat no">
                </div>
                <div class="col-md-2">
                    <label class="form-label small mb-1" for="fmProductType">Product</label>
                    <select class="form-select" id="fmProductType" name="product_type">
                        <option value="">All</option>
                        @foreach ($productTypes as $id => $name)
                            <option value="{{ $id }}" @selected((string) request('product_type') === (string) $id)>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small mb-1" for="fmFrom">From</label>
                    <input type="date" class="form-control" id="fmFrom" name="created_on[0]" value="{{ request('created_on.0') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small mb-1" for="fmTo">To</label>
                    <input type="date" class="form-control" id="fmTo" name="created_on[1]" value="{{ request('created_on.1') }}">
                </div>
                <div class="col-md-1">
                    <label class="form-label small mb-1" for="fmPerPage">Rows</label>
                    <select class="form-select" id="fmPerPage" name="per_page">
                        @foreach ([10, 25, 50, 100] as $size)
                            <option value="{{ $size }}" @selected((int) request('per_page', 10) === $size)>{{ $size }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Search</button>
                    <button type="button" class="btn btn-outline-secondary" id="fmReset">Reset</button>
                </div>
            </form>
            <div class="text-danger small mt-2" id="fmError" role="alert"></div>
        </div>
    </div>

    {{-- Listing --}}
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-sm align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>S No.</th>
                            <th role="button" data-sort="id">WO. No./Date <span class="sort-icon"></span></th>
                            <th>Party Name</th>
                            <th>SO No - Product - Size</th>
                            <th>Grade</th>
                            <th>Heat No</th>
                            <th role="button" data-sort="material_send_for_work">Sent (Kg) <span class="sort-icon"></span></th>
                            <th>Scrap (Kg)</th>
                            <th>Finished (Kg)</th>
                            <th>Packing</th>
                            <th class="no-print">Action</th>
                        </tr>
                    </thead>
                    <tbody id="fmRows">
                        @include('admin.finidhedMaterial.listingLoop')
                    </tbody>
                </table>
            </div>

            <div class="mt-3" id="fmPagination">{{ $listing->links('pagination::bootstrap-5') }}</div>
        </div>
    </div>
</div>

<script>
    // Finished Material listing: AJAX search / filter / sort / pagination
    (function () {
        const url = @json(route('finishedMaterial'));
        const form = document.getElementById('fmFilters');
        const rows = document.getElementById('fmRows');
        const pager = document.getElementById('fmPagination');
        const count = document.getElementById('fmCount');
        const errorBox = document.getElementById('fmError');

        let sortBy = @json(request('sort_by', 'id'));
        let sortOrder = @json(request('sort_order', 'desc'));
        let page = 1;
        let timer = null;

        function updateSortIcons() {
            document.querySelectorAll('th[data-sort]').forEach((th) => {
                th.querySelector('.sort-icon').textContent =
                    th.dataset.sort === sortBy ? (sortOrder === 'asc' ? '▲' : '▼') : '';
            });
        }

        async function load() {
            const params = new URLSearchParams(new FormData(form));
            params.set('sort_by', sortBy);
            params.set('sort_order', sortOrder);
            params.set('page', page);
            [...params.keys()].forEach((key) => { if (params.get(key) === '') params.delete(key); });

            errorBox.textContent = '';
            rows.style.opacity = '0.5';

            try {
                const response = await fetch(url + '?' + params.toString(), {
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                });
                const data = await response.json();

                if (!response.ok) {
                    const first = data.errors ? Object.values(data.errors)[0][0] : (data.message || 'Something went wrong.');
                    errorBox.textContent = first;
                    return;
                }

                rows.innerHTML = data.html;
                pager.innerHTML = data.pagination;
                count.textContent = data.count;
                history.replaceState(null, '', '?' + params.toString());
            } catch (error) {
                errorBox.textContent = 'Unable to load the listing. Please try again.';
            } finally {
                rows.style.opacity = '1';
                updateSortIcons();
            }
        }

        function reload() { page = 1; load(); }

        form.addEventListener('submit', (event) => { event.preventDefault(); reload(); });
        form.querySelectorAll('select, input[type="date"]').forEach((el) => el.addEventListener('change', reload));
        document.getElementById('fmSearch').addEventListener('input', () => {
            clearTimeout(timer);
            timer = setTimeout(reload, 400);
        });
        document.getElementById('fmReset').addEventListener('click', () => {
            form.reset();
            form.querySelectorAll('input[type="text"], input[type="date"]').forEach((el) => { el.value = ''; });
            document.getElementById('fmProductType').value = '';
            document.getElementById('fmPerPage').value = '10';
            sortBy = 'id'; sortOrder = 'desc';
            reload();
        });

        document.querySelectorAll('th[data-sort]').forEach((th) => {
            th.addEventListener('click', () => {
                sortOrder = (sortBy === th.dataset.sort && sortOrder === 'desc') ? 'asc' : 'desc';
                sortBy = th.dataset.sort;
                reload();
            });
        });

        pager.addEventListener('click', (event) => {
            const link = event.target.closest('a');
            if (!link) return;
            event.preventDefault();
            page = new URL(link.href).searchParams.get('page') || 1;
            load();
        });

        updateSortIcons();
    })();
</script>
@endsection