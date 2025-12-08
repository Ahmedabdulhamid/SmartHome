<form wire:submit.prevent="saveInvoice">


    @if ($successMessage)
        <div class="alert alert-success">{{ $successMessage }}</div>
    @endif

    {{-- جزء الفاتورة الرئيسية --}}
    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="client_id" class="form-label">العميل</label>
            <select class="form-control @error('client_id') is-invalid @enderror" wire:model="client_id">
                <option value="">-- اختر العميل --</option>
                @foreach ($clients as $client)
                    <option value="{{ $client->id }}">{{ $client->name }}</option>
                @endforeach
            </select>
            @error('client_id')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-md-3 mb-3">
            <label for="invoice_date" class="form-label">تاريخ الفاتورة</label>
            <input type="date" class="form-control @error('invoice_date') is-invalid @enderror"
                wire:model="invoice_date">
            @error('invoice_date')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-md-3 mb-3">
            <label for="due_date" class="form-label">تاريخ الاستحقاق</label>
            <input type="date" class="form-control @error('due_date') is-invalid @enderror" wire:model="due_date">
            @error('due_date')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <hr>

    {{-- 🚀 جزء بنود الفاتورة (Invoice Items) 🚀 --}}
    <h4>بنود الفاتورة</h4>
    <div class="table-responsive">
        <table class="table table-sm table-bordered">
            <thead>
                <tr>
                    {{-- ⬅️ إزالة تحديد العرض لترك المساحة المتبقية لهذا العمود --}}
                    <th>الوصف</th>

                    {{-- ⬅️ تحديد عرض ثابت للأعمدة الصغيرة --}}
                    <th style="width: 100px;">الكمية</th>
                    <th style="width: 120px;">السعر/الوحدة</th>
                    <th style="width: 140px;">الإجمالي الفرعي</th>
                    <th style="width: 50px;"></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $index => $item)
                    <tr>
                        <td>
                            {{-- يمكن تحويله إلى textarea إذا كنت تتوقع وصفاً طويلاً جداً --}}
                            <textarea type="text"
                                class="form-control form-control-sm @error('items.' . $index . '.description') is-invalid @enderror"
                                wire:model.blur="items.{{ $index }}.description" placeholder="وصف المنتج/الخدمة" rows="4">
                            </textarea>
                            @error('items.' . $index . '.description')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </td>
                        {{-- باقي حقول الجدول هنا... --}}
                        <td>
                            <input type="number" step="1"
                                class="form-control form-control-sm @error('items.' . $index . '.quantity') is-invalid @enderror"
                                wire:model.live.debounce.300ms="items.{{ $index }}.quantity" min="1">
                            @error('items.' . $index . '.quantity')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </td>
                        <td>
                            <input type="number" step="0.01"
                                class="form-control form-control-sm @error('items.' . $index . '.price') is-invalid @enderror"
                                wire:model.live.debounce.300ms="items.{{ $index }}.price" min="0.01">
                            @error('items.' . $index . '.price')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </td>
                        <td>
                            <input type="text" class="form-control form-control-sm" value="{{ $item['total'] }}"
                                readonly>
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-danger btn-sm"
                                wire:click="removeItem({{ $index }})">X</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-between align-items-center mt-3">
        <button type="button" class="btn btn-sm btn-success" wire:click="addItem">
            + إضافة بند
        </button>

        <h4>الإجمالي الكلي: {{ $this->totalAmount }} EGP</h4>
    </div>

    {{-- زر الإرسال --}}
    <div class="modal-footer px-0 pb-0 mt-4">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
        <button type="submit" class="btn btn-primary">
            <span wire:loading.remove wire:target="saveInvoice">حفظ الفاتورة</span>
            <span wire:loading wire:target="saveInvoice">جاري الحفظ...</span>
        </button>
    </div>
</form>
