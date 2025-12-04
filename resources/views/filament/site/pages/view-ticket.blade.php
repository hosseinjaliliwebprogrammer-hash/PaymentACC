<x-filament::page>
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-[minmax(0,2.5fr)_minmax(0,1fr)]">

        {{-- LEFT SECTION --}}
        <div class="space-y-6">

            {{-- HEADER --}}
            <div class="rounded-2xl border border-gray-200 bg-white px-6 py-4 shadow-sm">
                <div class="flex flex-col gap-2">
                    <div class="space-y-2">
    <div class="flex items-center gap-3">
        

        @if($ticket->user)
            <span class="text-sm font-medium text-gray-900"
                  style="font-size: 20px; color:#ff0000; font-weight: bold;">
                {{ $ticket->user->name }}
            </span>
        @endif
    </div>

    {{-- خط جداکننده بسیار ملایم --}}
    <div style="
        border-bottom: 1px solid rgba(0,0,0,0.15);
        margin-top: 6px;
        margin-bottom: 4px;
    "></div>
</div>


                    <h1 class="text-lg font-semibold text-gray-900">{{ $ticket->subject }}</h1>

                    <div class="flex flex-wrap items-center gap-2 text-xs text-gray-600">
                        <span>
                            Department:
                            <span class="font-semibold text-gray-800">{{ ucfirst($ticket->department) }}</span>
                        </span>

                        <span class="text-gray-400">•</span>

                        <span>
                            Priority:
                            <span class="font-semibold text-gray-800">{{ ucfirst($ticket->priority) }}</span>
                        </span>

                        <span class="text-gray-400">•</span>

                        <span>
                            Status:
                            <span class="font-semibold text-gray-800">{{ ucfirst($ticket->status) }}</span>
                        </span>
                    </div>
                </div>
            </div>

            {{-- CONVERSATION --}}
            <div class="rounded-2xl border border-gray-200 bg-white px-6 py-5 shadow-sm">
                

                <div class="space-y-4">
                    @foreach ($this->messages as $msg)
                        @php
                            $isAdmin = $msg->sender?->is_admin ?? false;
                            $name = $msg->sender->name ?? 'User';
                        @endphp

                        <div class="flex {{ $isAdmin ? 'justify-end' : 'justify-start' }}">
    <div class="w-full lg:w-11/12">
        <div class="rounded-2xl border border-gray-200 bg-white px-5 py-4 shadow-sm">
            
            {{-- NAME + TIME --}}
            <div class="mb-2 flex items-center justify-between text-xs text-gray-500">
                <span class="font-semibold text-gray-800" style="color:#ff0000;font-size:18px;padding-left: 10px;">
                    {{ $name }}
                    <span class="text-gray-500" style="font-size:12px;">
                        {{ $isAdmin ? 'Support Agent' : 'Customer' }}
                    </span>
                </span>

                <span>{{ $msg->created_at->format('Y-m-d H:i') }}</span>
            </div>

            {{-- MESSAGE BODY --}}
            <div class="rounded-xl bg-gray-50 px-4 py-3 text-sm text-gray-900 leading-relaxed" style="font-size:18px">
                {!! nl2br(e($msg->message)) !!}
            </div>

        </div>
    </div>
</div>

                    @endforeach
                </div>
            </div>

            {{-- REPLY BOX --}}
            <div class="rounded-2xl px-6 py-5 shadow-sm"
                 style="background-color:#F7F7F7; border:1px solid #7c6f6f;">

                <form wire:submit.prevent="sendReply" class="space-y-4">

                    <label class="text-sm font-semibold" style="color:#ff0000;font-size:23px;">Your Reply</label>

                    <textarea
                        wire:model.defer="reply"
                        rows="4"
                        class="w-full rounded-xl p-3 text-sm shadow-sm"
                        style="
                            background:white;
                            color:black;
                            border:1px solid #6e6e6e;
                            resize:none;
                            font-size:18px;
                        "
                        placeholder="Write your reply..."
                    ></textarea>

                    <div class="flex justify-end">
                        <button type="submit"
                            class="px-5 py-2 rounded-xl text-sm font-semibold shadow"
                            style="background:#f59e0b; color:white;padding:18px;font-size:18px">
                            Send Reply
                        </button>
                    </div>

                </form>

            </div>
        </div>

        {{-- RIGHT SIDEBAR --}}
        <div class="space-y-4">

            <div class="rounded-2xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 px-4 py-3">
                    <h3 class="text-sm font-semibold text-gray-800">Ticket Details</h3>
                </div>

                <div class="divide-y divide-gray-100 text-sm">

                    <div class="px-4 py-3">
                        <div class="text-xs uppercase text-gray-500">Subject</div>
                        <div class="mt-1 text-gray-900">
                            #{{ $ticket->id }} — {{ $ticket->subject }}
                        </div>
                    </div>

                    <div class="px-4 py-3">
                        <div class="text-xs uppercase text-gray-500">Department</div>
                        <div class="mt-1 text-gray-900">{{ ucfirst($ticket->department) }}</div>
                    </div>

                    <div class="px-4 py-3">
                        <div class="text-xs uppercase text-gray-500">Priority</div>
                        <div class="mt-1 text-gray-900">{{ ucfirst($ticket->priority) }}</div>
                    </div>

                    <div class="px-4 py-3">
                        <div class="text-xs uppercase text-gray-500">Status</div>
                        <div class="mt-1">
                            <span class="inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-800">
                                {{ ucfirst($ticket->status) }}
                            </span>
                        </div>
                    </div>

                </div>
            </div>

        </div>

    </div>
</x-filament::page>
