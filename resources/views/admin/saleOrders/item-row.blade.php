<div class="card mb-3" data-item-row>
    <div class="card-header d-flex justify-content-between align-items-center py-2">
        <strong>Item</strong>
        <button type="button" class="btn btn-sm btn-outline-danger remove-sale-order-item" aria-label="Remove item" title="Remove item">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>
    <div class="card-body">
        @if(isset($item['id']))
            <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item['id'] }}">
        @endif
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Product Category</label>
                <select class="form-select item-category" name="items[{{ $index }}][category]">
                    <option value="">All Categories</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category }}" @selected(data_get($item, 'category', data_get($item, 'product.product_category')) === $category)>{{ ucfirst(str_replace('_', ' ', $category)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Product <span class="text-danger">*</span></label>
                <select class="form-select item-product" name="items[{{ $index }}][product_id]" required>
                    <option value="">Select Product</option>
                    @foreach ($products as $product)
                        <option value="{{ $product->id }}" data-category="{{ $product->product_category }}" @selected((string) data_get($item, 'product_id', '') === (string) $product->id)>
                            {{ $product->product_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Grade</label>
                <select class="form-select" name="items[{{ $index }}][grade_id]">
                    <option value="">Select Grade</option>
                    @foreach ($grades as $grade)
                        <option value="{{ $grade->id }}" @selected((string) data_get($item, 'grade_id', '') === (string) $grade->id)>{{ $grade->grade_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Brand</label>
                <select class="form-select" name="items[{{ $index }}][brand_id]">
                    <option value="">Select Brand</option>
                    @foreach ($brands as $brand)
                        <option value="{{ $brand->id }}" @selected((string) data_get($item, 'brand_id', '') === (string) $brand->id)>{{ $brand->brand_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Size</label>
                <select class="form-select" name="items[{{ $index }}][size_id]">
                    <option value="">Select Size</option>
                    @foreach ($sizes as $size)
                        <option value="{{ $size->id }}" @selected((string) data_get($item, 'size_id', '') === (string) $size->id)>{{ $size->size_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Size / Unit</label>
                <input class="form-control" name="items[{{ $index }}][size_unit]" value="{{ data_get($item, 'size_unit') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Sale Rate</label>
                <input type="number" min="0" step="0.001" class="form-control" name="items[{{ $index }}][sale_item_prices]" value="{{ data_get($item, 'sale_item_prices') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Quantity</label>
                <input type="number" min="0" step="0.001" class="form-control" name="items[{{ $index }}][item_qty]" value="{{ data_get($item, 'item_qty') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Quantity Unit</label>
                <select class="form-select" name="items[{{ $index }}][qty_type]">
                    <option value="">Select Unit</option>
                    <option value="1" @selected((string) data_get($item, 'qty_type', '') === '1')>Tons</option>
                    <option value="2" @selected((string) data_get($item, 'qty_type', '') === '2')>KGs</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Size Extra / Ton</label>
                <input type="number" min="0" step="0.001" class="form-control" name="items[{{ $index }}][size_extra]" value="{{ data_get($item, 'size_extra') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Dispatch Qty</label>
                <input type="number" min="0" step="0.001" class="form-control" name="items[{{ $index }}][dispatch_qty]" value="{{ data_get($item, 'dispatch_qty') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Dispatched Qty</label>
                <input type="number" min="0" step="0.001" class="form-control" name="items[{{ $index }}][dispatched_qty]" value="{{ data_get($item, 'dispatched_qty') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Pending Qty</label>
                <input type="number" min="0" step="0.001" class="form-control" name="items[{{ $index }}][pending_qty]" value="{{ data_get($item, 'pending_qty') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Completion</label>
                <select class="form-select" name="items[{{ $index }}][s_marked_completed]">
                    <option value="">Select</option>
                    <option value="1" @selected((string) data_get($item, 's_marked_completed', '') === '1')>Not Completed</option>
                    <option value="2" @selected((string) data_get($item, 's_marked_completed', '') === '2')>Completed</option>
                </select>
            </div>
            @foreach (['straightening' => 'Straightening', 'point_discard' => 'Point Discard', 'phosphating' => 'Phosphating'] as $field => $label)
                <div class="col-md-3">
                    <label class="form-label">{{ $label }}</label>
                    <select class="form-select" name="items[{{ $index }}][{{ $field }}]">
                        <option value="">Select</option>
                        <option value="1" @selected((string) data_get($item, $field, '') === '1')>Yes</option>
                        <option value="2" @selected((string) data_get($item, $field, '') === '2')>No</option>
                    </select>
                </div>
            @endforeach
            <div class="col-md-3">
                <label class="form-label">Hardness</label>
                <input class="form-control" name="items[{{ $index }}][hardness]" value="{{ data_get($item, 'hardness') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Hardness Unit</label>
                <input class="form-control" name="items[{{ $index }}][hardness_unit]" value="{{ data_get($item, 'hardness_unit') }}" placeholder="BHN, HRC">
            </div>
            <div class="col-md-3">
                <label class="form-label">Length Unit</label>
                <input class="form-control" name="items[{{ $index }}][pcs_length_unit]" value="{{ data_get($item, 'pcs_length_unit') }}" placeholder="MM, Inches">
            </div>
            <div class="col-md-3">
                <label class="form-label">Coating</label>
                <input class="form-control" name="items[{{ $index }}][coating]" value="{{ data_get($item, 'coating') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">FS Bundle Weight</label>
                <input type="number" min="0" step="0.001" class="form-control" name="items[{{ $index }}][fs_bundle_weight]" value="{{ data_get($item, 'fs_bundle_weight') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">FS Bundle Unit</label>
                <select class="form-select" name="items[{{ $index }}][fs_bundle_unit]">
                    <option value="">Select Unit</option>
                    <option value="1" @selected((string) data_get($item, 'fs_bundle_unit', '') === '1')>KG</option>
                    <option value="2" @selected((string) data_get($item, 'fs_bundle_unit', '') === '2')>Tons</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Product Type ID</label>
                <input type="number" min="0" class="form-control" name="items[{{ $index }}][product_type]" value="{{ data_get($item, 'product_type') }}">
            </div>
            <div class="col-md-12">
                <label class="form-label">Item Remarks</label>
                <textarea class="form-control" name="items[{{ $index }}][item_remarks]" rows="2">{{ data_get($item, 'item_remarks') }}</textarea>
            </div>
        </div>
    </div>
</div>
