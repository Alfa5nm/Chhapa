<fieldset class="panel variant">
    <legend>Variant {{ $index + 1 }}</legend>
    <div class="form-grid">
        @foreach (['size' => 'Size', 'method' => 'Printing method', 'material' => 'Material', 'finish' => 'Finish'] as $key => $label)
            <label>{{ $label }}
                <select name="variants[{{ $index }}][{{ $key }}]" data-key="{{ $key }}" required>
                    <option value="{{ $displayValue($variant[$key] ?? '') }}">{{ $displayValue($variant[$key] ?? 'Choose an option') }}</option>
                </select>
            </label>
        @endforeach
        <label>Minimum quantity <input type="number" name="variants[{{ $index }}][min_quantity]" value="{{ $displayValue($variant['min_quantity'] ?? '') }}" data-key="min_quantity" required></label>
        <label>Maximum quantity <input type="number" name="variants[{{ $index }}][max_quantity]" value="{{ $displayValue($variant['max_quantity'] ?? '') }}" data-key="max_quantity" required></label>
        <label>Printable area <input name="variants[{{ $index }}][printable_area]" value="{{ $displayValue($variant['printable_area'] ?? '') }}" placeholder="e.g. 210 × 90 mm" maxlength="160" data-key="printable_area" required></label>
        <label>Indicative unit price (BDT)
            <input type="number" step="0.01" name="variants[{{ $index }}][unit_price]" value="{{ $displayValue($variant['unit_price'] ?? '') }}" placeholder="Leave blank for quotation" data-key="unit_price">
        </label>
    </div>
    <button type="button" class="text-button remove-variant">Remove variant</button>
</fieldset>
