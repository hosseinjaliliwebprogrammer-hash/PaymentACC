<div>
    <x-filament::page>
         <style>
        .filament-breadcrumbs,
        .fi-header {
            justify-content: center !important;
            text-align: center !important;
        }

        .fi-header-heading {
            width: 100%;
            text-align: center !important;
            font-weight: 600;
        }
    </style>
        <style>
            /* اسکرول‌بار ظریف */
            .thin-scrollbar::-webkit-scrollbar { height: 8px; width: 8px; }
            .thin-scrollbar::-webkit-scrollbar-thumb { background:#374151; border-radius: 8px; }
            .thin-scrollbar::-webkit-scrollbar-track { background:#111827; }
        </style>

        <div class="mx-auto w-full max-w-5xl px-3 sm:px-6 lg:px-8 space-y-6">

            <!-- Header -->
            <div class="rounded-2xl border border-gray-800 bg-gray-900/60 backdrop-blur p-4 sm:p-5">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 class="text-xl sm:text-2xl font-semibold text-gray-100">{{ $record->subject }}</h1>
                        <div class="mt-2 flex flex-wrap items-center gap-2 text-xs">
                            <span class="inline-flex items-center gap-1 rounded-full bg-gray-800 px-2.5 py-1 text-gray-300">
                                <span class="h-2 w-2 rounded-full bg-sky-400"></span>
                                Department: <span class="capitalize">{{ $record->department }}</span>
                            </span>
                            <span class="inline-flex items-center gap-1 rounded-full bg-gray-800 px-2.5 py-1 text-gray-300">
                                <span class="h-2 w-2 rounded-full bg-amber-400"></span>
                                Priority: <span class="capitalize">{{ $record->priority }}</span>
                            </span>
                            <span class="inline-flex items-center gap-1 rounded-full bg-gray-800 px-2.5 py-1 text-gray-300">
                                <span class="h-2 w-2 rounded-full {{ $record->status === 'closed' ? 'bg-red-400' : ($record->status === 'in_progress' ? 'bg-yellow-400' : 'bg-green-400') }}"></span>
                                Status: <span class="capitalize">{{ $record->status }}</span>
                            </span>
                            <span class="inline-flex items-center gap-1 rounded-full bg-gray-800 px-2.5 py-1 text-gray-300">
                                #{{ $record->id }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Conversation -->
            <div class="rounded-2xl border border-gray-800 bg-gray-900/40 p-3 sm:p-5">
                <div class="thin-scrollbar max-h-[65vh] overflow-y-auto space-y-4">

                    {{-- original ticket message --}}
                    @if($record->message)
    <div class="p-4 rounded-lg border border-gray-700 bg-gray-800">
        <div class="mb-2 flex items-center gap-2 text-xs text-gray-400">
            <strong class="text-gray-300">{{ $record->user->name }}</strong>
            <span class="text-gray-500">•</span>
            <span class="text-gray-400">{{ $record->created_at->format('Y-m-d H:i') }}</span>
        </div>
        <div class="mt-2 text-gray-200 prose max-w-none">
            {!! $record->message !!}
        </div>
    </div>
@endif

                    {{-- replies --}}
                    @foreach ($messages as $msg)
                        @php $isAdmin = (int)$msg->sender_id === (int)auth()->id(); @endphp

                        <div class="flex items-start gap-3 {{ $isAdmin ? 'justify-end' : '' }}">
                            @unless($isAdmin)
                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-gray-700 text-gray-200">
                                    {{ mb_substr($msg->sender->name ?? 'U',0,1) }}
                                </div>
                            @endunless

                            <div class="max-w-[85%] sm:max-w-[70%] rounded-2xl p-3 sm:p-4 shadow border
                                        {{ $isAdmin
                                            ? 'bg-amber-500/15 border-amber-500/40'
                                            : 'bg-gray-800/80 border-gray-800' }}">
                                <div class="mb-2 flex items-center justify-between text-[10px] sm:text-xs text-gray-400">
    <div class="flex items-center gap-3">
        <span class="font-medium {{ $isAdmin ? 'text-amber-300' : 'text-gray-300' }}">
            {{ $msg->sender->name ?? 'Unknown' }}
        </span>
        <span class="text-gray-500">•</span>
        <span class="text-gray-400">{{ $msg->created_at->format('Y-m-d H:i') }}</span>
    </div>
</div>

                                <div class="prose prose-invert prose-sm max-w-none text-gray-100 whitespace-pre-line">
                                    {!! nl2br(e($msg->message)) !!}
                                </div>
                            </div>

                            @if($isAdmin)
                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-amber-600 text-white">
                                    A
                                </div>
                            @endif
                        </div>
                    @endforeach

                    @if(!$record->message && $messages->isEmpty())
                        <div class="text-center text-sm text-gray-400 py-8">
                            No messages yet.
                        </div>
                    @endif
                </div>
            </div>

            <!-- Composer -->
            <div class="rounded-2xl border border-gray-800 bg-gray-900/60 p-3 sm:p-4 sticky bottom-2 backdrop-blur">
                <form wire:submit.prevent="sendReply" class="flex flex-col gap-3">
                    <label class="text-sm text-gray-300">Your reply</label>
                    <textarea
    wire:model.defer="reply"
    rows="4"
    class="w-full rounded-xl border border-gray-700 bg-gray-950/70 p-3 text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none resize-y"
    style="color: #000;"
    placeholder="Type your reply…"
></textarea>

                    <div class="flex items-center justify-end">
                        <button
    type="submit"
    class="inline-flex items-center gap-2 rounded-xl px-6 py-2.5 font-semibold text-white shadow-md transition-all duration-300"
    style="background-color: rgb(245, 158, 11); box-shadow: 0 4px 10px rgba(245, 158, 11, 0.3);"
    onmouseover="this.style.backgroundColor='rgb(251, 191, 36)'; this.style.boxShadow='0 6px 14px rgba(245,158,11,0.45)';"
    onmouseout="this.style.backgroundColor='rgb(245,158,11)'; this.style.boxShadow='0 4px 10px rgba(245,158,11,0.3)';"
>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M12 5l7 7-7 7"/>
                            </svg>
                            Send Reply
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </x-filament::page>
</div>
