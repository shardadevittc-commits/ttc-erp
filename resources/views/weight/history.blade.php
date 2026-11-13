@extends('layouts.app')

@section('title', 'History Report Unload Material | TTC Robotronics - Steel Industry ERP')

@section('content')

<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h4>History Report - Unload Material</h4>
        </div>

        <div class="card-body">

            {{-- Filters --}}
            <div class="row mb-3">

                <div class="col-md-4 mb-2">
                    <label for="customerFilter">Customer Name</label>
                    <input type="text"
                           id="customerFilter"
                           class="form-control"
                           placeholder="Type customer name...">
                </div>

                <div class="col-md-4 mb-2">
                    <label for="formlistFilter">Formlist</label>
                    <select id="formlistFilter" class="form-control">
                        <option value="">All Formlists</option>
                        @foreach($wt->pluck('formlist')->unique()->filter() as $form)
                            <option value="{{ $form }}">{{ $form }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4 mb-2 d-flex align-items-end">
                    <button type="button"
                            id="resetFilter"
                            class="btn btn-secondary">
                        Reset Filter
                    </button>
                </div>

            </div>

            {{-- Table --}}
            <div class="table-responsive">
                <table class="table table-bordered table-striped"
                       id="historyTable">

                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Customer Name</th>
                            <th>Formlist</th>
                            <th>Truck No</th>
                            <th>Loaded Weight</th>
                            <th>Tare Weight</th>
                            <th>Material Weight</th>
                            <th>Unload Tare Weight</th>
                            <th>Unload Loaded Weight</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($wt as $key => $weight)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $weight->customer_name ?? 'N/A' }}</td>
                                <td>{{ $weight->formlist ?? 'N/A' }}</td>
                                <td>{{ $weight->truckno ?? 'N/A' }}</td>
                                <td>{{ number_format((float) $weight->loadedweight, 2) }}</td>
                                <td>{{ number_format((float) $weight->tareweight, 2) }}</td>
                                <td>{{ number_format((float) $weight->material_weight, 2) }}</td>
                                <td>{{ number_format((float) $weight->unload_tare_weight, 2) }}</td>
                                <td>{{ number_format((float) $weight->unload_loaded_weight, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">
                                    No unload material history found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

            <div class="mt-2">
                <strong>Showing records: </strong>
                <span id="recordCount">{{ $wt->count() }}</span>
            </div>

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const customerFilter = document.getElementById('customerFilter');
    const formlistFilter = document.getElementById('formlistFilter');
    const resetFilter = document.getElementById('resetFilter');
    const table = document.getElementById('historyTable');
    const recordCount = document.getElementById('recordCount');

    function filterTable() {
        const customerValue = customerFilter.value.toLowerCase().trim();
        const formlistValue = formlistFilter.value.toLowerCase().trim();

        const rows = table.querySelectorAll('tbody tr');
        let visibleCount = 0;

        rows.forEach(function (row) {
            const cells = row.querySelectorAll('td');

            // Skip empty-state row
            if (cells.length < 9) {
                return;
            }

            const customerName = cells[1].textContent.toLowerCase().trim();
            const formlist = cells[2].textContent.toLowerCase().trim();

            const customerMatch = customerName.includes(customerValue);
            const formlistMatch = formlistValue === '' ||
                                  formlist === formlistValue;

            if (customerMatch && formlistMatch) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        recordCount.textContent = visibleCount;
    }

    customerFilter.addEventListener('input', filterTable);
    formlistFilter.addEventListener('change', filterTable);

    resetFilter.addEventListener('click', function () {
        customerFilter.value = '';
        formlistFilter.value = '';
        filterTable();
    });

});
</script>

@endsection
