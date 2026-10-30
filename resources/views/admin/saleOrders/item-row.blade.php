<tr data-item-row>
    {{-- Item Name --}}
    <td data-field="item_name">
        @if (data_get($item, 'id'))
            <input type="hidden" name="items[{{ $index }}][id]" value="{{ data_get($item, 'id') }}">
        @endif
        <select class="form-select form-select-sm item-product" name="items[{{ $index }}][product_id]" data-inline-add="product" data-add-url="{{ route('products.add') }}" data-add-field="product_name" data-add-label="Product" required>
            <option value="">Select Item</option>
            {{-- <option value="__add__">+ Add Product</option> --}}
            @foreach ($products as $product)
                <option value="{{ $product->id }}" @selected((string) data_get($item, 'product_id', '') === (string) $product->id)>{{ $product->product_name }}</option>
            @endforeach
        </select>
        <input type="hidden" name="items[{{ $index }}][brand_id]" value="{{ data_get($item, 'brand_id') }}">
        <input type="hidden" name="items[{{ $index }}][size_extra]" value="{{ data_get($item, 'size_extra') }}">
        <input type="hidden" name="items[{{ $index }}][dispatch_qty]" value="{{ data_get($item, 'dispatch_qty') }}">
        <input type="hidden" name="items[{{ $index }}][dispatched_qty]" value="{{ data_get($item, 'dispatched_qty') }}">
        <input type="hidden" name="items[{{ $index }}][pending_qty]" value="{{ data_get($item, 'pending_qty') }}">
        <input type="hidden" name="items[{{ $index }}][s_marked_completed]" value="{{ data_get($item, 's_marked_completed') }}">
    </td>
    {{-- Size --}}
    <td data-field="size">
        <select class="form-select form-select-sm mb-1" name="items[{{ $index }}][size_id]" data-inline-add="size" data-add-url="{{ route('sizes.add') }}" data-add-field="size_name" data-add-label="Size">
            <option value="">Select Size</option>
            <option value="__add__">+ Add Size</option>
            @foreach ($sizes as $size)
                <option value="{{ $size->id }}" @selected((string) data_get($item, 'size_id', '') === (string) $size->id)>{{ $size->size_name }}</option>
            @endforeach
        </select>
    </td>
    {{-- Grade --}}
    <td data-field="grade">
        <select class="form-select form-select-sm" name="items[{{ $index }}][grade_id]" data-inline-add="grade" data-add-url="{{ route('grades.add') }}" data-add-field="grade_name" data-add-label="Grade" required>
            <option value="">Select Grade</option>
            <option value="__add__">+ Add Grade</option>
            @foreach ($grades as $grade)
                <option value="{{ $grade->id }}" @selected((string) data_get($item, 'grade_id', '') === (string) $grade->id)>{{ $grade->grade_name }}</option>
            @endforeach
        </select>
    </td>
    {{-- Qty --}}
    <td data-field="qty">
        <div class="d-flex border rounded overflow-hidden" style="gap: 0;">
            <input type="text" class="form-control number_only form-control-sm w-50 border-0 rounded-0 shadow-none" style="width: 50%;" name="items[{{ $index }}][item_qty]" value="{{ data_get($item, 'item_qty') }}" required>

            <select class="form-select form-select-sm w-50 border-0 rounded-0 shadow-none" style="width: 50%;" name="items[{{ $index }}][qty_type]" required>
                <option value="">Unit</option>
                <option value="1" @selected((string) data_get($item, 'qty_type', '') === '1')>Tons</option>
                <option value="2" @selected((string) data_get($item, 'qty_type', '') === '2')>KGs</option>
            </select>
        </div>
    </td>
    {{-- sale_rate --}}
    <td data-field="sale_rate">
        <input type="number" min="0" step="0.001" class="form-control number_only form-control-sm" name="items[{{ $index }}][sale_item_prices]" value="{{ data_get($item, 'sale_item_prices') }}" required>
    </td>

    @foreach (['straightening' => 'Straightening', 'point_discard' => 'Point Discard', 'phosphating' => 'Phosphating'] as $field => $label)
        <td data-field="{{ $field }}">
            <input type="hidden" name="items[{{ $index }}][{{ $field }}]" value="">
            <div class="mt-1">
                <label class="mr-2"><input type="radio" name="items[{{ $index }}][{{ $field }}]" value="1" @checked((string) data_get($item, $field, '') === '1')> Yes</label>
                <label><input type="radio" name="items[{{ $index }}][{{ $field }}]" value="2" @checked((string) data_get($item, $field, '') === '2')> No</label>
            </div>
        </td>
    @endforeach

        {{-- <input class="form-control form-control-sm" name="items[{{ $index }}][hardness_unit]" value="{{ data_get($item, 'hardness_unit') }}" placeholder="Unit (BHN, HRC)"> --}}
    {{-- <td data-field="hardness">
        <input class="form-control form-control-sm mb-1" name="items[{{ $index }}][hardness]" value="{{ data_get($item, 'hardness') }}" placeholder="Hardness">
        <div class="mt-1">
            <label class="d-block">
            <input type="radio" name="items[{{ $index }}][hardness_unit]" value="BHN" @checked(data_get($item, 'BHN') === 'hardness_unit')> Auto black</label>
            <label class="d-block"><input type="radio" name="items[{{ $index }}][hardness_unit]" value="HRC" @checked(data_get($item, 'hardness_unit') === 'HRC')> Galvanized</label>
        </div>
    </td> --}}
    <td data-field="hardness">
    <div class="d-flex border rounded overflow-hidden">
        <input type="text"
               class="form-control form-control-sm border-0 rounded-0 shadow-none"
               style="width: 50%;"
               name="items[{{ $index }}][hardness]"
               value="{{ data_get($item, 'hardness') }}"
               placeholder="Hardness">

        <select class="form-control form-control-sm border-0 rounded-0 shadow-none"
                style="width: 50%;"
                name="items[{{ $index }}][hardness_unit]">
            <option value="">Unit</option>
            <option value="BHN" @selected(data_get($item, 'hardness_unit') === 'BHN')>BHN</option>
            <option value="HRC" @selected(data_get($item, 'hardness_unit') === 'HRC')>HRC</option>
        </select>
    </div>
</td>

    <td data-field="length">
        <input class="form-control form-control-sm" name="items[{{ $index }}][pcs_length_unit]" value="{{ data_get($item, 'pcs_length_unit') }}" placeholder="Length">
    </td>

    <td data-field="coating">
        <input type="hidden" name="items[{{ $index }}][coating]" value="">
        <div class="mt-1">
            <label class="d-block"><input type="radio" name="items[{{ $index }}][coating]" value="autoblack" @checked(data_get($item, 'coating') === 'autoblack')> Auto black</label>
            <label class="d-block"><input type="radio" name="items[{{ $index }}][coating]" value="galvanized" @checked(data_get($item, 'coating') === 'galvanized')> Galvanized</label>
        </div>
    </td>

    <td data-field="fs_bundle_weight">
        <div class="d-flex align-items-center gap-1">
            <input type="number" min="0" step="0.001" class="form-control form-control-sm number_only w-50" name="items[{{ $index }}][fs_bundle_weight]" value="{{ data_get($item, 'fs_bundle_weight') }}" placeholder="Weight" required>

            <select class="form-select form-select-sm w-50" name="items[{{ $index }}][fs_bundle_unit]" required>
                <option value="">Unit</option>
                <option value="1" @selected((string) data_get($item, 'fs_bundle_unit', '') === '1')>KG</option>
                <option value="2" @selected((string) data_get($item, 'fs_bundle_unit', '') === '2')>Tons</option>
            </select>
        </div>
    </td>


    <td data-field="remarks">
        <input class="form-control form-control-sm" name="items[{{ $index }}][item_remarks]" value="{{ data_get($item, 'item_remarks') }}" required>
    </td>

    <td>
        <button type="button" class="btn btn-sm btn-outline-danger remove-sale-order-item"> Remove </button>
    </td>
</tr>