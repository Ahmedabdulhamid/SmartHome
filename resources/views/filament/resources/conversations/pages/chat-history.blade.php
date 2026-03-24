<div class="flex flex-col space-y-4 p-4 bg-gray-50 dark:bg-gray-950 rounded-xl border border-gray-200 dark:border-gray-800 max-h-[600px] overflow-y-auto">
    @php
        // جلب الرسائل من العلاقة المعرفة في الموديل
        $messages = $getRecord()->messages()->orderBy('created_at', 'asc')->get();
    @endphp

    @forelse($messages as $message)
        <div class="flex {{ $message->direction === 'outbound' ? 'justify-end' : 'justify-start' }}">
            <div @class([
                'max-w-[80%] px-4 py-2 rounded-2xl shadow-sm text-sm',
                'bg-primary-600 text-white rounded-tr-none' => $message->direction === 'outbound',
                'bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 border border-gray-200 dark:border-gray-700 rounded-tl-none' => $message->direction === 'inbound',
            ])>
                {{-- عرض نص الرسالة --}}
                @if($message->type === 'text')
                    <p class="leading-relaxed">{{ $message->body }}</p>
                @endif

                {{-- عرض الصور إذا وجدت --}}
                @if($message->type === 'image' && $message->attachment_url)
                    <img src="{{ asset('storage/' . $message->attachment_url) }}" class="rounded-lg mb-2 max-w-full">
                @endif

                <div class="flex items-center justify-end gap-1 mt-1 opacity-70">
                    <span class="text-[10px]">{{ $message->created_at->format('g:i a') }}</span>
                    @if($message->direction === 'outbound')
                        {{-- أيقونات حالة الرسالة --}}
                        <x-filament::icon
                            icon="{{ $message->status === 'read' ? 'heroicon-m-check-badge' : 'heroicon-m-check' }}"
                            class="h-3 w-3"
                        />
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="text-center py-10 text-gray-500">لا توجد رسائل في هذه المحادثة بعد.</div>
    @endforelse
</div>
