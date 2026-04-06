<div
    style="max-width:800px; margin:30px auto; padding:30px; border:1px solid #e0e0e0; border-radius:12px; background:#ffffff; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05); font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">

    {{-- رسالة النجاح --}}
    @if (session()->has('message'))
        <div style="margin-bottom:20px; padding:15px; background:#e6ffed; color:#1f7a2d; border-radius:8px; border: 1px solid #b3dfc4; font-weight: 600;">
            {{ session('message') }}
        </div>
    @endif

    <form wire:submit.prevent="submit" class="php-email-form">

        {{-- الاسم --}}
        <div style="margin-bottom:20px;">
            <label style="display:block; margin-bottom: 5px; font-weight:600; color:#333;">{{ __('web.name') }}</label>
            <input type="text" wire:model="name" class="form-control"
                style="width:100%; padding:10px 15px; border:1px solid #ddd; border-radius:8px; transition: border-color 0.3s, box-shadow 0.3s;"
                onfocus="this.style.borderColor='#3b82f6'; this.style.boxShadow='0 0 0 3px rgba(59, 130, 246, 0.2)'; this.style.outline='none';"
                onblur="this.style.borderColor='#ddd'; this.style.boxShadow='none';">
            @error('name')
                <span style="color:#e3342f; font-size:12px; margin-top:5px; display:block;">{{ $message }}</span>
            @enderror
        </div>

        {{-- رقم الهاتف --}}
        <div style="margin-bottom:20px;">
            <label style="display:block; margin-bottom: 5px; font-weight:600; color:#333;">{{ __('web.phone') }}</label>
            <input type="number" wire:model.defer="phone" placeholder="{{__('web.phone_number')}}" class="form-control"
                style="width:100%; padding:10px 15px; border:1px solid #ddd; border-radius:8px; transition: border-color 0.3s, box-shadow 0.3s;"
                onfocus="this.style.borderColor='#3b82f6'; this.style.boxShadow='0 0 0 3px rgba(59, 130, 246, 0.2)'; this.style.outline='none';"
                onblur="this.style.borderColor='#ddd'; this.style.boxShadow='none';">
            @error('phone')
                <span style="color:#e3342f; font-size:12px; margin-top:5px; display:block;">{{ $message }}</span>
            @enderror
        </div>

        {{-- البريد --}}
        <div style="margin-bottom:20px;">
            <label style="display:block; margin-bottom: 5px; font-weight:600; color:#333;">{{ __('web.email') }}</label>
            <input type="email" wire:model="email" class="form-control"
                style="width:100%; padding:10px 15px; border:1px solid #ddd; border-radius:8px; transition: border-color 0.3s, box-shadow 0.3s;"
                onfocus="this.style.borderColor='#3b82f6'; this.style.boxShadow='0 0 0 3px rgba(59, 130, 246, 0.2)'; this.style.outline='none';"
                onblur="this.style.borderColor='#ddd'; this.style.boxShadow='none';">
            @error('email')
                <span style="color:#e3342f; font-size:12px; margin-top:5px; display:block;">{{ $message }}</span>
            @enderror
        </div>

        {{-- الحقل العام للسعر المتوقع (لو مفيش منتجات) --}}
        <div style="margin-bottom:20px;">
            @if (collect($items)->pluck('product_id')->filter()->isEmpty())
                <div style="margin:20px 0; padding:15px; border:1px dashed #b3d4ff; border-radius:8px; background:#f0f8ff;">
                    <label style="display:block; margin-bottom: 5px; font-weight:600; color:#333;">{{ __('web.expected_price') }}</label>
                    <input type="number" wire:model="rfq_expected_price" class="form-control"
                        style="width:100%; padding:10px 15px; border:1px solid #ddd; border-radius:8px; background:#fff; transition: border-color 0.3s, box-shadow 0.3s;"
                        onfocus="this.style.borderColor='#3b82f6'; this.style.boxShadow='0 0 0 3px rgba(59, 130, 246, 0.2)'; this.style.outline='none';"
                        onblur="this.style.borderColor='#ddd'; this.style.boxShadow='none';">

                    @error('rfq_expected_price')
                        <span style="color:#e3342f; font-size:12px; margin-top:5px; display:block;">{{ $message }}</span>
                    @enderror
                    <small style="color:#555; display:block; margin-top:5px;">يمكنك إدخال السعر المتوقع يدويًا إذا لم تختر منتجات.</small>
                </div>
            @endif
        </div>

        <h3 style="font-weight:700; margin-bottom:20px; padding-bottom:10px; border-bottom: 2px solid #eee; color:#1D4ED8; font-size: 18px;">{{ __('web.required_products') }}</h3>

        {{-- المنتجات --}}
        @foreach ($items as $index => $item)
            <div style="margin-bottom:20px; padding:20px; border:1px solid #e0e0e0; border-radius:10px; background:#fefefe; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.03);">

                {{-- المنتج --}}
                <div style="margin-bottom:15px;">
                    <label style="display:block; margin-bottom: 5px; font-weight:600; color:#333;">{{ __('web.product') }}</label>
                    <select wire:model.live="items.{{ $index }}.product_id" class="form-control"
                        style="width:100%; padding:10px 15px; border:1px solid #ddd; border-radius:8px; background-color: #fff; appearance: none; cursor: pointer; transition: border-color 0.3s;">
                        <option value="">{{ __('web.choose_product') }}</option>
                        @foreach ($products as $p)
                            <option value="{{ $p->id }}">{{ $p->name }}</option>
                        @endforeach
                    </select>
                    @error('items.' . $index . '.product_id')
                        <span style="color:#e3342f; font-size:12px; margin-top:5px; display:block;">{{ $message }}</span>
                    @enderror
                </div>

                {{-- المتغيرات - تم تعديلها لاستخدام الإخفاء بدلاً من الحذف --}}
                <div style="margin-bottom:15px; @if (count($item['variants']) == 0) display:none; @endif">
                    <label style="display:block; margin-bottom: 5px; font-weight:600; color:#333;">{{ __('web.product_variant') }}</label>
                    <select wire:model.live="items.{{ $index }}.product_variant_id" class="form-control"
                        style="width:100%; padding:10px 15px; border:1px solid #ddd; border-radius:8px; background-color: #fff; appearance: none; cursor: pointer; transition: border-color 0.3s;">
                        <option value="">{{ __('web.choose_product_variant') }}</option>
                        @foreach ($item['variants'] as $v)
                            <option value="{{ $v->id }}">
                                {{ $v->getTranslation('name', app()->getLocale()) }}
                            </option>
                        @endforeach
                    </select>
                    @error('items.' . $index . '.product_variant_id')
                        <span style="color:#e3342f; font-size:12px; margin-top:5px; display:block;">{{ $message }}</span>
                    @enderror
                </div>


                {{-- السعر المتوقع للمنتج --}}
                <div style="margin-bottom:15px;">
                    <label style="display:block; margin-bottom: 5px; font-weight:600; color:#333;">{{ __('web.expected_price') }}</label>
                    <input type="number" wire:model="items.{{ $index }}.expected_price" class="form-control"
                        @if (!empty($item['product_id']) || !empty($item['product_variant_id'])) readonly style="background:#f4f4f4; color:#666; width:100%; padding:10px 15px; border:1px solid #ddd; border-radius:8px;" @else style="width:100%; padding:10px 15px; border:1px solid #ddd; border-radius:8px; transition: border-color 0.3s, box-shadow 0.3s;" onfocus="this.style.borderColor='#3b82f6'; this.style.boxShadow='0 0 0 3px rgba(59, 130, 246, 0.2)'; this.style.outline='none';" onblur="this.style.borderColor='#ddd'; this.style.boxShadow='none';" @endif>

                    @error("items.$index.expected_price")
                        <span style="color:#e3342f; font-size:12px; margin-top:5px; display:block;">{{ $message }}</span>
                    @enderror
                </div>

                {{-- الكمية --}}
                <div style="margin-bottom:15px;">
                    <label style="display:block; margin-bottom: 5px; font-weight:600; color:#333;">{{ __('web.quantity') }}</label>
                    <input type="number" min="1" wire:model="items.{{ $index }}.quantity" class="form-control"
                        style="width:100%; padding:10px 15px; border:1px solid #ddd; border-radius:8px; transition: border-color 0.3s, box-shadow 0.3s;"
                        onfocus="this.style.borderColor='#3b82f6'; this.style.boxShadow='0 0 0 3px rgba(59, 130, 246, 0.2)'; this.style.outline='none';"
                        onblur="this.style.borderColor='#ddd'; this.style.boxShadow='none';">

                    @error("items.$index.quantity")
                        <span style="color:#e3342f; font-size:12px; margin-top:5px; display:block;">{{ $message }}</span>
                    @enderror
                </div>

                {{-- زر الحذف --}}
                @if (count($items) > 1)
                    <button type="button" wire:click="removeItem({{ $index }})"
                        style="margin-top:10px; background:#ef4444; color:white; padding:8px 15px; border:none; border-radius:6px; cursor:pointer; font-weight:600; transition: background 0.3s;">
                        <i class="bi bi-trash-fill"></i> {{ __('web.remove_product') }}
                    </button>
                @endif
            </div>
        @endforeach

        {{-- زر إضافة منتج --}}
        <button type="button" wire:click="addItem"
            style="background:#10b981; color:white; padding:10px 18px; border:none; border-radius:8px; cursor:pointer; font-weight:700; transition: background 0.3s; margin-top: 10px; box-shadow: 0 2px 4px rgba(16, 185, 129, 0.4);">
            + {{ __('web.add_another_product') }}
        </button>

        {{-- الملاحظات --}}
        <div style="margin-top:30px;">
            <label style="display:block; margin-bottom: 5px; font-weight:600; color:#333;">{{ __('web.extra_notes') }}</label>
            <textarea wire:model="description" rows="4" class="form-control"
                style="width:100%; padding:10px 15px; border:1px solid #ddd; border-radius:8px; resize:vertical; transition: border-color 0.3s, box-shadow 0.3s;"
                onfocus="this.style.borderColor='#3b82f6'; this.style.boxShadow='0 0 0 3px rgba(59, 130, 246, 0.2)'; this.style.outline='none';"
                onblur="this.style.borderColor='#ddd'; this.style.boxShadow='none';"></textarea>
                  <span>{{ __('web.check_desc_status') }}</span>
                @error('description')
                    <span style="color:red;">{{ $message }}</span>
                @enderror
        </div>

        {{-- زر الإرسال --}}
        <div style="text-align:center; margin-top:40px;">
            <button type="submit"
                style="background:#1D4ED8; color:white; padding:12px 30px; border:none; border-radius:8px; cursor:pointer; font-weight:bold; font-size:16px; transition: background 0.3s; box-shadow: 0 4px 6px rgba(29, 78, 216, 0.4);"
                onmouseover="this.style.backgroundColor='#2563EB';"
                onmouseout="this.style.backgroundColor='#1D4ED8';"
                wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="submit">{{ __('web.submit_rfq') }}</span>
                <span wire:loading wire:target="submit">جاري الإرسال...</span>
            </button>
        </div>
    </form>
</div>
